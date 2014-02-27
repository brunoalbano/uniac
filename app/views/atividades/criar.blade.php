@extends('layout/principal')

@section('content')

@parent


<?php

    if (isset($status) === false)
        $status = 0;

    if (isset($pesquisa) === false)
        $pesquisa = '';

    if (isset($page) === false)
        $page = '';
    
	$camposNavegacao =  "<input type=\"hidden\" name=\"status\" value=\"$status\" \>" .
						"<input type=\"hidden\" name=\"pesquisa\" value=\"$pesquisa\" \>" .
						"<input type=\"hidden\" name=\"page\" value=\"$page\" \>";
?>

<article>

	@if(isset($model))		
		{{ BForm::model($model, array('files' => true)) }}		
	@else
		{{ BForm::open(array('files' => true)) }}
	@endif

	{{ BForm::hidden('codigo') }}

	{{ $camposNavegacao }}

	<header>
		<div class="row visible-xs" style="margin-bottom: 15px">
			<div class="col-xs-4">
				<a type="button" class="btn btn-default btn-block" href="{{ url('atividades?status=' . $status . '&pesquisa=' . $pesquisa . '&page=' . $page) }}" tabindex="1">Cancelar</a>
			</div>

			<div class="col-xs-4 pull-right">
				<input type="submit" class="btn btn-success btn-block" value="Enviar" tabindex="7"/>
			</div>		
		</div>
		<div class="row">
			<div class="hidden-xs col-xs-3 col-sm-2 col-md-2">
				<a type="button" class="btn btn-default btn-block" href="{{ url('atividades?status=' . $status . '&pesquisa=' . $pesquisa . '&page=' . $page) }}" tabindex="1">Cancelar</a>
			</div>

			<div class="col-xs-12 col-md-8 col-sm-8">
				<label class="visible-xs">Título</label>
				{{ BForm::text('titulo', null, null, array('placeholder' => 'Título resumido da atividade', "maxlength" => 100, "autofocus" => "", "tabindex" => 1)) }}
			</div>

			<div class="hidden-xs col-sm-2 col-md-2">
				<input type="submit" class="btn btn-success btn-block" value="Enviar" tabindex="7"/>
			</div>
		</div>
	</header>

	<section>
		@if(Auth::user()->supervisor || Auth::user()->administrador)

			@include('atividades/selecionaralunos')

		@endif

		<div class="row">
			<div class="col-md-9 col-lg-9">

			<?php 
				$tipo_atividade_descricao = null;

				if (isset($model) && $model->tipo_atividade !== null)
					$tipo_atividade_descricao = $model->tipo_atividade->descricao_completa;
			?>

				{{
					BForm::formGroup('tipo_atividade_codigo', 'Tipo de atividade',
						'<div class="input-group">' .
							Form::text('tipo_atividade_descricao', $tipo_atividade_descricao, array('disabled' => 'disabled', 'class' => 'form-control')) .
						'	<span class="input-group-btn">' .
						'		<button type="button" id="selecionarTipoAtividade" class="btn btn-info" tabindex="2">Selecionar</button>' .
						'	</span>' .
						'</div>'
					);
				}}

				{{ BForm::hidden('tipo_atividade_descricao') }}
				{{ BForm::hidden('tipo_atividade_codigo') }}
			</div>

			<div class="col-md-3 col-lg-3">
			<?php 
				$horas = null;

				if (isset($model) )
					$horas = $model->horas_requisitadas;
			?>
				{{ BForm::integer('horas', 'Horas', $horas, array("tabindex" => "3")) }}
			</div>
		</div>

		@if(Auth::user()->aluno == false)
		<div class="row extra-link">
			<div class="col-xs-12">
				<label><a href="#">(Mais...)</a></label>
			</div>
		</div>
		@endif

		@if (Auth::user()->aluno === false)
		<?php $hj = (new DateTime('NOW')) ?>
		<div class="row extra" style="display: none">
			<div class="col-sm-4">
				{{ BForm::select('status', 'Status', array(Atividade::ACEITA => 'Aceita', Atividade::RECUSADA => 'Recusada'), null, array("tabindex" => "3")) }}
			</div>
		</div>
		@endif

		<div class="row" id="fileupload">
			<div class="col-xs-12">
				<div class="panel panel-default {{ $errors->has('files') ? 'panel-danger' : '' }}">
					<div class="panel-heading">
		                <a class="btn btn-success btn-sm fileinput-button" tabindex="4">
		                    <i class="glyphicon glyphicon-plus"></i>
		                    <span>Adicionar Anexos</span>
		                    <input type="file" name="files[]" multiple>
		                </a>
                	</div>
					<div class="panel-body">
						{{ $errors->first('files', '<p class="text-danger">:message</p>') }}
						<div class="row files un-anexos" role="presentation">
				        	
    					</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6">
				{{ BForm::textarea('descricao', 'Descrição', null, array('placeholder' => 'Descrição detalhada da atividade', "tabindex" => 5)) }}
			</div>

			<div class="col-lg-6">
				{{ BForm::textarea('justificativa', 'Justificativa', null, array('placeholder' => 'Justificativa da importância da atividade para o curso', "tabindex" => 6)) }}
			</div>

		</div>
	</section>

	{{ BForm::close() }}

</article>

@include('atividades/selecionartipoatividade')

@stop

@section('script')

<script type="text/javascript">

	var SUPERVISOR = {{ Auth::user()->aluno ? 'false' : 'true' }};

	var Atividade = Atividade || {};

	Atividade.Criar = function() {
		var oPublic = {};

		var initExtra = function() {
			var extraLink = $('.extra-link');

			extraLink.on('click', function(e) {
				e.preventDefault();
				var extra = $('.extra');

				extraLink.hide();
				extra.show();
			});

			if ($('.extra .has-error').length)
				extraLink.click();
		}

		var initTipoAtividade = function() {
			var callback = function(data) {
				$('input[name=tipo_atividade_descricao]').val(data.titulo);
				$('#tipo_atividade_codigo').val(data.codigo);
				$('input[name=horas]').val(data.horas);
				$('input[name=horas]').data('max', data.horas);

				if (SUPERVISOR)
					$('input[name=horas]').attr('max', data.horas);
			};

			$('#selecionarTipoAtividade').on('click', function(e){
				TipoAtividade.open(callback);
			});

			$('form').on('submit', function(e) {
				var inputHoras = $('input[name=horas]');

				var horas = inputHoras.val() * 1,
					max = inputHoras.data('max') * 1;

				if (max && horas > max && SUPERVISOR == false)
				{
					var result = confirm('As horas informadas excedem o limite do tipo de atividade (' + max + ' horas). Se aceitas, as horas serão  alteradas pelo avaliador. Deseja continuar?');

					if (!result)
					{
						e.preventDefault();
						return false;
					}
				}
			});
		}

		oPublic.init = function() {
			initTipoAtividade();

			initExtra();
		}

		return oPublic;
	}();

	$(document).ready(function(){
		Atividade.Criar.init();

		TipoAtividade.init();

		var urlFileupload = '{{ url("atividades/" . $model->codigo . "/anexos") }}';

        $('#fileupload').uniacFileupload({
            url : urlFileupload
        });
	});
</script>

@stop

@section('style')

	<style type="text/css">
		.panel,
		.panel-group,
		.panel-body {
			transition: height 1s;
			-webkit-transition: height 1s; /* Safari */
		}

		#modalTipoAtividade .panel-heading[data-toggle] {
			cursor: pointer;
		}

		@media (min-width: 992px) {
			.modal-body {
				overflow-y: auto;
				max-height: 450px;
			}
		}
	</style>

@stop

{{-- Template de anexos --}}
@include('anexos/edit')