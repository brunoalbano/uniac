@extends('layout/principal')

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

	$placeholderComentario = 'Digite uma resposta';
	$responderDescricao = 'Responder';
	$responderLabel = 'Resposta';
	$responderClass = 'btn-primary';

	if(Auth::user()->aluno && $model->aguardando_correcao)
		$placeholderComentario = 'Digite um comentário para responder e para enviar a atividade para avaliar';
	
	if ((Auth::user()->supervisor || Auth::user()->administrador) && $model->aguardando_avaliacao) {
		$placeholderComentario = 'Descreva o que deve ser corrigido pelo aluno';

		$responderDescricao = 'Responder e Devolver';
		$responderClass = 'btn-warning';
		$responderLabel = 'Resposta da avaliação';
	}
?>

@section('style')

<style type="text/css">
	blockquote {
		background-color: #f8f8f8;
		width: 100%;
	}

	.un-atividade-det {
		margin-bottom: 5px;
		font-size: 0.9em;
		border-bottom: 1px silver dotted;
		padding-bottom: 15px;
	}

	.un-atividade-det .label{
		font-size: 0.8em;
	}

	.un-atividade-header {
		margin-top: 15px;
		margin-bottom: 15px;
		border-bottom: 1px silver solid;
	}

	.un-atividade-tipo-descricao{
		white-space: nowrap;
		overflow:hidden;
		text-overflow:ellipsis;
	}

	.un-atividade-resposta {
		padding-top: 15px;
		margin-bottom: 15px;
		border-bottom: 1px silver dotted;		
		border-top: 1px silver dotted;
	}

	.un-atividade-resposta button {
		margin-bottom: 15px;
	}

	.un-atividade-comentario {
		white-space: pre-wrap;
	}
</style>

@stop

@section('content')

@parent

    @if(Session::has('error') || Session::has('errors'))            
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Atenção!</strong> Corrija os erros para continuar:
            
            @if(Session::has('reason')) 
                {{ trans(Session::get('reason')) }} 
            @else 
                @if (Session::has('errors'))
                    <ul>
                    @foreach (Session::get('errors')->all('<li>:message</li>') as $erro) 
                        {{ $erro }}
                    @endforeach
                    </ul>
                @else
                    {{ Session::get('error') }} 
                @endif
            @endif
        </div>
    @endif

	<header class="row hidden-print">
		
		<div class="col-xs-3 col-md-2">
			<a href="{{ url('atividades?status=' . $status . '&pesquisa=' . $pesquisa . '&page=' . $page) }}" class="btn btn-default btn-block">Voltar</a>
		</div>

		@if($model->aguardando_correcao || $model->aguardando_avaliacao)
			@if(Auth::user()->aluno)

				<div class="col-xs-3 col-sm-2 col-md-offset-1 col-lg-1 col-lg-offset-5" onclick="return confirm('Deseja realmente excluir?')">
					<a class="btn btn-danger btn-block" href="{{ url('atividades/' . $model->codigo . '/excluir') }}" title="Excluir atividade"><span class="glyphicon glyphicon-trash"></span></a>
				</div>

				@if((int)$model->aguardando_correcao)
					<div class="col-xs-3 col-sm-2 col-lg-1">
						<a class="btn btn-primary btn-block" href="#" id="btnEnviar" title="Responder e enviar para avaliação"><span class="glyphicon glyphicon-comment"></span></a>
					</div>

					<div class="col-xs-3 col-sm-2 col-lg-1">
						<a class="btn btn-info btn-block" href="{{ url('atividades/' . $model->codigo . '/editar?status=' . $status . '&pesquisa=' . $pesquisa . '&page=' . $page) }}" title="Editar atividade e enviar para avaliação"><span class="glyphicon glyphicon-pencil"></span></a>
					</div>
				@else
					<div class="col-xs-6 col-sm-4 col-lg-2">
					</div>
				@endif
			@elseif(Auth::user()->supervisor || Auth::user()->administrador)

				@if($model->aguardando_avaliacao)
					<div class="col-xs-3 col-sm-2 col-md-offset-1 col-lg-1 col-lg-offset-5">
						<button class="btn btn-danger btn-block"  data-toggle="modal" data-target="#modalRecusar" id="btnRecusar" title="Recusar atividade"><span class="glyphicon glyphicon-remove"></span></button>
					</div>

					<div class="col-xs-3 col-sm-2 col-lg-1">
						<a class="btn btn-warning btn-block" href="#" id="btnEnviar" title="Devolver para correção"><span class="glyphicon glyphicon-arrow-left"></span></a>
					</div>

					<div class="col-xs-3 col-sm-2 col-lg-1">
						<button class="btn btn-success btn-block" type="button" id="btnAceitar"><span class="glyphicon glyphicon-ok"></span></button>
					</div>
				@else
					<div class="col-xs-6 col-md-7 col-lg-8">
					</div>
				@endif
			@endif
		@elseif(Auth::user()->supervisor || Auth::user()->administrador)
			<div class="col-xs-3 col-sm-2 col-md-offset-1 col-lg-1 col-lg-offset-5" onclick="return confirm('Deseja realmente reabrir a atividade?')">
				<a class="btn btn-warning btn-block" href="{{ url('atividades/' . $model->codigo . '/reabrir') }}" title="Reabrir atividade"><span class="glyphicon glyphicon-repeat"></span></a>
			</div>
		@else
			<div class="col-xs-6 col-md-7 col-lg-8">
			</div>
		@endif

		<div class="hidden-xs col-sm-3 col-md-3 col-lg-2">
			<div class="btn-group btn-block" style="display: none">
		  		<button type="button" class="btn btn-default col-xs-6" title="Atividade anterior"><span class="glyphicon glyphicon-chevron-up"></span></button>
		  		<button type="button" class="btn btn-default col-xs-6" title="Próxima atividade"><span class="glyphicon glyphicon-chevron-down"></span></button>
			</div>
		</div>

	</header>

	<article>	

		<div class="row">
			<div class="col-sm-12">
				<div class="un-atividade-header">
					<h4>
						{{ $model->titulo }}
					</h4>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<div class="un-atividade-det">
					<div class="row">
						<div class="col-xs-4 col-sm-3 col-md-2">
							<span class="label {{ Helpers::getStatusClass($model->status) }}">
								{{ $model->status_descricao }}
							</span>
						</div>
						<div class="col-xs-3 col-sm-2">
							@if($model->aceita)
								<div>Horas: {{ (int)$model->horas_aceitas }}</div>
							@else
								<div>Horas: {{ (int)$model->horas_requisitadas }}</div>
							@endif
						</div>
						
						<div class="col-md-5 col-lg-6 visible-md visible-lg" >
							<div title="{{ $model->tipo_atividade->descricao }}" class="un-atividade-tipo-descricao">Tipo: {{ $model->tipo_atividade->descricao }}</div>
						</div>

						<div class="col-xs-5 col-sm-7 col-md-3 col-lg-2">
							<div class="pull-right">
								<span title="{{ date_format($model->atualizado_em, 'd/m/Y H:m:s') }}">{{ Helpers::dateDescription($model->atualizado_em) }}</span>
							</div>
						</div>
					</div>

					@if (Auth::user()->aluno === false)
						<div class="row" style="margin-top: 10px">
							<div class="col-xs-12 col-md-6">
								Aluno: {{ $model->matricula->usuario->nome_completo . ', ' . $model->matricula->usuario->login }}
							</div>
							<div class="col-xs-12 col-md-6">
								Turma: {{ $model->matricula->turma->nome . ', ' . $model->matricula->turma->curso->nome . ', '. $model->matricula->turma->curso->campus->nome }}
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>

		<div class="row" id="fileupload">
			<div class="col-lg-12">
				<div class="row files un-anexos" role="presentation">
		        	
				</div>
			</div>
		</div>

		<div class="row justify" style="margin-top: 5px">
			<div class="col-md-6">
				{{ BForm::formGroup('descricao', 'Descrição', '<div>' . str_replace("\n", "<br/>", $model->descricao) . '</div>') }}
			</div>
			<div class="col-md-6">
				{{ BForm::formGroup('justificativa', 'Justificativa', '<div>'. str_replace("\n", "<br/>", $model->justificativa) . '</div>') }}
			</div>
		</div>

		<div class="row un-bloco-ocultar un-reponder" style="display: none">
			<div class="col-xs-12">
				{{ BForm::open(array('url' => 'atividades/' . $model->codigo . '/responder', 'class' => 'un-atividade-resposta')) }}
					{{ $camposNavegacao }}

					<div class="row">
						<div class="col-xs-12">
							<button type="button" class="btn btn-default pull-left un-atividade-resposta-cancelar" tabindex="100">Cancelar</button>

							<button type="submit" class="btn {{ $responderClass }} pull-right" tabindex="102">{{ $responderDescricao }}</button>
						</div>
					</div>

					{{ BForm::textarea('comentario', $responderLabel, null, array('required', 'placeholder' => $placeholderComentario, 'rows' => 4, 'tabindex' => 101)) }}
				{{ BForm::close() }}
			</div>
		</div>

		@if((Auth::user()->supervisor || Auth::user()->administrador) && $model->aguardando_avaliacao)

		{{ BForm::hidden('turma', $model->matricula->turma_codigo) }}

		<div class="row un-aceitar un-bloco-ocultar" style="display: none">
			<div class="col-xs-12">
				{{ BForm::open(array('url' => 'atividades/' . $model->codigo . '/aceitar', 'class' => 'un-atividade-resposta')) }}
					{{ $camposNavegacao }}

					<div class="row">
						<div class="col-xs-12">
							<button type="button" class="btn btn-default pull-left un-atividade-resposta-cancelar" tabindex="100">Cancelar</button>

							<button type="submit" class="btn btn-success pull-right" tabindex="104" id="btnAceitarConfirmar">Aceitar</button>
						</div>
					</div>

					<div class="row">
						<div class="col-md-9">

						<?php 
							$tipo_atividade_descricao = $model->tipo_atividade->descricao_completa;
							$horasMaximo = $model->tipo_atividade->horas;
						?>
							<label for="selecionarTipoAtividade" class="control-label">Tipo de atividade</label>
								
							<div class="input-group">
								{{ Form::text('tipo_atividade_descricao', $tipo_atividade_descricao, array('disabled' => 'disabled', 'class' => 'form-control', "required")) }}
								<span class="input-group-btn">
									<button type="button" id="selecionarTipoAtividade" class="btn btn-info" tabindex="101">Selecionar</button>
								</span>
							</div>
								
							{{ BForm::hidden('tipo_atividade_codigo', $model->tipo_atividade_codigo) }}
						</div>

						<div class="col-md-3">
							{{ BForm::integer('horas', 'Horas', $model->horas_requisitadas, array("tabindex" => "102", "required", "max" => $horasMaximo)) }}
						</div>
					</div>
					
					{{ BForm::textarea('comentario', 'Comentário', null, array('placeholder' => 'Comentário', 'rows' => 4, 'tabindex' => 103)) }}

				{{ BForm::close() }}
			</div>
		</div>

		<div class="row un-recusar un-bloco-ocultar" style="display: none">
			<div class="col-xs-12">
				{{ BForm::open(array('url' => 'atividades/' . $model->codigo . '/recusar', 'class' => 'un-atividade-resposta')) }}
					{{ $camposNavegacao }}

					<div class="row">
						<div class="col-xs-12">
							<button type="button" class="btn btn-default pull-left un-atividade-resposta-cancelar" tabindex="100">Cancelar</button>

							<button type="submit" class="btn btn-danger pull-right" tabindex="104" id="btnAceitarConfirmar">Recusar</button>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							{{ BForm::select('motivo_recusa_codigo', 'Motivo da recusa', array('' => '(Selecione...)'), null, array('required', "tabindex" => 101)) }}
						</div>
					</div>
					
					{{ BForm::textarea('comentario', 'Comentário', null, array('placeholder' => 'Detalhes do motivo pelo qual a atividade foi recusada', 'rows' => 4, 'tabindex' => 103)) }}

				{{ BForm::close() }}
			</div>
		</div>

		@endif

		<div class="row">
			<div class="col-xs-12">
				@foreach($model->comentarios()->with('usuario')->orderBy('atualizado_em', 'desc')->get() as $comentario)
					<div class="row">
						<div class="col-xs-12">
							<blockquote class="{{ (int)$model->matricula->usuario_codigo !== (int)$comentario->usuario->codigo ? 'pull-right' : '' }}">
							  <p class="un-atividade-comentario">{{ $comentario->comentario }}</p>
							  @if(empty($comentario->interno) == false)
							  	<small>{{ $comentario->interno }}</small>
							  @endif
							  <small>
							  	<span title="Responsável pela alteração">{{ ($comentario->usuario->codigo === Auth::user()->codigo ? 'Eu' : $comentario->usuario->nome_completo) }}</span> <em title="{{ date_format($comentario->atualizado_em, 'd/m/Y h:m:s') }}">({{ Helpers::dateDescription($comentario->atualizado_em) }})</em>
							  </small>

							  	@if(Auth::user()->aluno || Auth::user()->supervisor || Auth::user()->administrador)
									<p>
										<button type="button" class=" btn btn-default btn-xs un-atividade-responder hidden-print"><span class="glyphicon glyphicon-comment"></span>&nbsp;Responder</button>
									</p>
								@endif
							</blockquote>
						</div>
					</div>
				@endforeach
			</div>
		</div>

	</article>

	@if((Auth::user()->supervisor || Auth::user()->administrador) && $model->aguardando_avaliacao)

		@include('atividades/selecionartipoatividade')

	@endif

@stop


@section('script')

@if((Auth::user()->supervisor || Auth::user()->administrador) && $model->aguardando_avaliacao)
	<script>
		var initTipoAtividade = function() {
			var callback = function(data) {
				$('input[name=tipo_atividade_descricao]').val(data.titulo);
				$('#tipo_atividade_codigo').val(data.codigo);
				$('input[name=horas_aceitas]').val(data.horas);
				$('input[name=horas_aceitas]').attr('max', data.horas);
			};

			$('#selecionarTipoAtividade').on('click', function(e){
				TipoAtividade.open(callback);
			});
		}

		$(function () {

			initTipoAtividade();

			TipoAtividade.init();
		});
	</script>
@endif

<script>
	$(function () {

        $('#btnEnviar').on('click', function()
        {
        	$('.un-atividade-responder:first').click();

        	$('.comentario-confirmar').text('Devolver');

        	return false;
        });

        $("#btnAceitar").on('click', function() {
        	$('.un-bloco-ocultar').hide();
        	$('.un-aceitar').show();

        	$('#btnAceitarConfirmar').focus();

        	$('.un-aceitar').appear();
        })

        $('#btnRecusar').one('click', function() {
        	$('.un-bloco-ocultar').hide();
        	$.get("{{ url('atividades/motivosrecusa') }}", function(data){        		
        		var motivos = $('#motivo_recusa_codigo');

        		$.each(data, function(index, value){
					$(motivos).append('<option value="' + value.codigo + '">' + value.nome + '</option>');
        		});
        	});

        	$('.un-recusar').show();

        	$('#motivo_recusa_codigo').focus();

        	$('.un-recusar').appear();
        });

        $('.un-atividade-responder').on('click', function()
        {
        	$('.un-bloco-ocultar').hide();
        	$('.comentario-confirmar').text('Responder');

        	var comentario = $('.un-reponder');

    		comentario.show(0, function() {
	    		comentario.find('textarea')
	        			.focus(); 
   			});  

    		//comentario.appear();

			/*
        	$('html, body').animate({
                scrollTop: $('.un-reponder').offset().top - 90
            }, 200);
			*/
        		//.find('textarea')
        			//.focus();        	
        });

        $(document).on('click', '.un-atividade-resposta-cancelar', function(){
        	$('.un-bloco-ocultar').hide();
        });
	});

	$(document).ready(function(){

		var urlFileupload = '{{ url("atividades/" . $model->codigo . "/anexos") }}';

        $('#fileupload').uniacFileupload({
            url : urlFileupload
        });
	});
</script>

@stop

{{-- Template de anexos --}}
@include('anexos/show')