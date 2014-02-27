@extends('layout/principal')

@section('style')
	<style>
		section .table, 
		section .alert {
			font-size: .9em;
		}

		/* Oculta a url do link quando imprimindo */
		@media print {
			a[href]:after {
			content: none !important;
			}
		}
	</style>
@stop

@section('content')

<section>
	<h3>Relatório de Atividades Complementares por Turma</h3>
	<hr/>
	{{ BForm::open(array('method' => 'GET')) }}

	<?php
		$status = Input::old('status');

		$opcoesUrl = '?turma_codigo=' . $turma_codigo . '&status=' . $status;
	?>

		<div class="row">
			<div class="col-md-7 visible-print">
				<label>Curso</label>
				<div id="cursonome"></div>
				<br/>
			</div>
			<div class="col-md-7">
				{{ BForm::select('turma', 'Turma', array('Carregando...'), $turma_codigo) }}
			</div>
			<div class="col-sm-4">
				{{ BForm::select('status', 'Status da matrícula', array(
					'' => '(Todos)', 
					Matricula::ATIVO => 'Ativo', 
					99 => 'Ativo (Completo)', 
					Matricula::INATIVO => 'Inativo', 
					Matricula::BLOQUEADO => 'Bloqueado',
					Matricula::HOMOLOGADO => 'Homologado'
				), '') }}
			</div>
			<div class="col-sm-2 col-md-1 hidden-print">
				<label>&nbsp;</label>
				<button class="btn btn-block btn-primary"><span class="glyphicon glyphicon-filter"></span></button>
			</div>
		</div>

		@if (isset($model))	

			@if (count($model) == 0)
				<table class="table table-striped">
					<tr>
						<td style="text-align: center;">Sem dados para exibir para o filtro selecionado.</td>
					</tr>
				</table>
			@else
			<table class="table table-striped">
				<colgroup>
					<col>
					<col style="width: 130px">
					<col style="width: 50px">
				<thead>
					<tr>
						<th>Aluno</th>
						<th>R.A.</th>
					</tr>
				</thead>
				<tbody>
					@foreach($model as $value)
						<tr>
							<td>
								<a href="{{ url('atividades/relatorio/matricula') . '/' . $value->codigo . $opcoesUrl }}">
								{{ $value->usuario->nome_completo }}
								</a>
							</td>
							<td>
								<a href="{{ url('atividades/relatorio/matricula') . '/' . $value->codigo . $opcoesUrl }}">
								{{ $value->usuario->login }}
								</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			@endif
		@endif

	{{ BForm::close() }}
</section>

@stop


@section('script')

<script type="text/javascript">
	ImportarAlunos = function(){
		var oPublic = {};

		var onChangeTurma = function() {
			var cursonome = $('#cursonome');

			var nome = $('#turma').find('option:selected').closest('optgroup').attr('label');
			
			cursonome.text(nome);
		};

		oPublic.init = function(){
			$('#turma').uniacLoadSelect({ 
				url: '{{ url("atividades/turmascoordenador") . "?ativa=false" }}', 
				valueField: 'turma_codigo', 
				textField: 'turma_nome', 
				groupField: 'curso_nome',
				selected: '{{ Input::old("turma", $turma_codigo) }}',
				placeholder: '(Selecione uma turma...)',
				done: onChangeTurma
			});

			$('#turma').on('change', onChangeTurma);
		}

		return oPublic;
	}();

	$(function(){
		ImportarAlunos.init();
	});
</script>

@stop