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
		$turma_codigo = Input::old('turma', Input::old('turma_codigo'));
		$data_inicial = Input::old('data_inicial');
		$data_final = Input::old('data_final');		
		
		$opcoesUrl = "?turma_codigo=$turma_codigo&status=$status&data_inicial=$data_inicial&data_final=$data_final";

		$exibirhorasfaltando = empty($data_inicial) && empty($data_final);

		$lista_status = array(
			'' => '(Todos)', 
			Matricula::ATIVO => 'Ativo', 
			99 => 'Ativo (Completo)', 
			Matricula::INATIVO => 'Inativo', 
			Matricula::BLOQUEADO => 'Bloqueado',
			Matricula::HOMOLOGADO => 'Homologado'
		);

		$filtro = array(
			'turma' => $turma,
			'status' => empty($status) ? '' : $lista_status[$status],
			'data_inicial' => $data_inicial,
			'data_final' => $data_final);
	?>

		<div class="visible-print">
			@include('atividades/relatoriofiltro', $filtro)
		</div>

		<div class='row hidden-print'>
			<div class='col-sm-8'>
				{{ BForm::select('turma', 'Turma', array('Carregando...'), $turma_codigo) }}
			</div>
			<div class='col-sm-4'>
				{{ BForm::select('status', 'Status da matrícula', $lista_status, '') }}
			</div>
		</div>

		<div class='row  hidden-print' id='outrosfiltros'>
			<div class='col-sm-4 col-md-3'>
				{{ BForm::date('data_inicial', 'Data inicial')}}
			</div>
			<div class='col-sm-4 col-md-3'>
				{{ BForm::date('data_final', 'Data final')}}
			</div>
			<div class='col-sm-4 col-md-1 col-md-offset-5 hidden-print'>
				<label>&nbsp;</label>
				<button class='btn btn-block btn-primary'><span class='glyphicon glyphicon-filter'></span></button>
			</div>
		</div>

		@if (isset($model))	

			@if (count($model) == 0)
				<table class='table table-striped'>
					<tr>
						<td style='text-align: center;'>Sem dados para exibir para o filtro selecionado.</td>
					</tr>
				</table>
			@else
			<table class='table table-striped' id='lista'>
				<colgroup>
					<col>
					<col style='width: 130px'>
					<col style='width: 50px'>
					<col style='width: 50px'>
					@if ($exibirhorasfaltando)
					<col style='width: 50px'>
					@endif
				<thead>
					<tr>
						<th>Aluno</th>
						<th>R.A.</th>
						<th>Horas aceitas</th>
						@if ($exibirhorasfaltando)
						<th>Horas faltando</th>
						@endif
					</tr>
				</thead>
				<tbody>
					@foreach($model as $value)
						<tr>
							<td>
								<a href='{{ url('atividades/relatorio/matricula') . '/' . $value->codigo . $opcoesUrl }}'>
								{{ $value->usuario->nome_completo }}
								</a>
							</td>
							<td>
								<a href='{{ url('atividades/relatorio/matricula') . '/' . $value->codigo . $opcoesUrl }}'>
								{{ $value->usuario->login }}
								</a>
							</td>
							<td>
								<a href='{{ url('atividades/relatorio/matricula') . '/' . $value->codigo . $opcoesUrl }}'>
								{{ $value->horas_aceitas }}
								</a>
							</td>
							@if ($exibirhorasfaltando)
							<td>
								<a href='{{ url('atividades/relatorio/matricula') . '/' . $value->codigo . $opcoesUrl }}'>
								{{ $value->horas_faltando }}
								</a>
							</td>
							@endif
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

<script type='text/javascript'>
	ImportarAlunos = function(){
		var oPublic = {};

		var onChangeDate = function() {
			if ($('#data_inicial').val() || $('#data_final').val())
				$('#outrosfiltros').removeClass('hidden-print');
			else
				$('#outrosfiltros').addClass('hidden-print');
		};

		oPublic.init = function(){
			$('#turma').uniacLoadSelect({ 
				url: '{{ url('atividades/turmascoordenador') . '?ativa=false' }}', 
				valueField: 'turma_codigo', 
				textField: 'turma_nome', 
				groupField: 'curso_nome',
				selected: '{{ Input::old('turma', $turma_codigo) }}',
				placeholder: '(Selecione uma turma...)',
			});

			$('#data_inicial,#data_final').on('change', onChangeDate);

			onChangeDate();
		}

		return oPublic;
	}();

	$(function(){
		ImportarAlunos.init();
	});
</script>

@stop