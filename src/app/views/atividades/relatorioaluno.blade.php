@extends('layout/principal')

@section('style')
	<style>
		section .table, 
		section .alert {
			font-size: .9em;
		}

		section .well {
			font-size: .85em;	
		}
	</style>
@stop

@section('content')

	<?php
		$turma_codigo = Input::get('turma', Input::get('turma_codigo'));
		$status = Input::get('status');
		$data_inicial = Input::get('data_inicial');
		$data_final = Input::get('data_final');

		$opcoesUrl = "?turma_codigo=$turma_codigo&status=$status&data_inicial=$data_inicial&data_final=$data_final";

		$exibirhorasfaltando = empty($data_inicial) && empty($data_final);		

		$filtro = array(
			'aluno' => $matricula->usuario,
			'turma' => $matricula->turma,
			'data_inicial' => $data_inicial,
			'data_final' => $data_final,
			'status' => null);
	?>

<section>
	<h3>Relatório de Atividades Complementares do Aluno</h3>
	<hr/>

	@if(empty($turma_codigo) === false)
	<div class="hidden-print">
		<a href="{{ url('atividades/relatorio/turma') . $opcoesUrl }}" class="btn btn-default">Voltar</a>
		<br/><br/>
	</div>
	@endif

	@if(Auth::user()->aluno == false)

		@include('atividades/relatoriofiltro', $filtro)

	@endif

	<div class="row hidden-print">
		<div class="col-sm-4">
			<div class="alert alert-info">
				Necessárias: <strong>{{ $model['horas_necessarias'] }}</strong>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="alert alert-success">
				Aceitas: <strong>{{ $model['horas_aceitas'] }}</strong>
			</div>
		</div>

		@if ($exibirhorasfaltando)		
		<div class="col-sm-4">
			<div class="alert alert-warning">
				Faltando: <strong>{{ $model['horas_faltando'] }}</strong>
			</div>
		</div>
		@endif
	</div>

	<table class="table table-striped">
		<colgroup>
			<col>
			<col style="width: 140px">
			<col style="width: 90px">
			<col style="width: 90px">
		</colgroup>
		<thead>
			<tr>
				<th></th>
				<th>Necessário</th>
				<th>Aceitas</th>
				@if ($exibirhorasfaltando)
				<th>Faltando</th>
				@endif
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Atividades Complementares</td>
				<td>{{ $model['horas_necessarias_normais'] }}</td>
				<td>{{ $model['horas_aceitas_normais'] }}</td>
				@if ($exibirhorasfaltando)
				<td>{{ $model['horas_faltando_normais'] }}</td>
				@endif
			</tr>
			
			@foreach($model['tipos_obrigatorios'] as $value)
				@if($value->obrigatorio)
				<tr>
					<td>{{ $value->descricao }}</td>
					<td>{{ $value->horas_necessarias }}</td>
					<td>{{ $value->horas_aceitas }}</td>
					@if ($exibirhorasfaltando)
					<td>{{ $value->horas_faltando }}</td>
					@endif
				</tr>
				@endif
			@endforeach
		</tbody>

		<tfoot>
			<tr>
				<th>Total</th>
				<th>{{ $model['horas_necessarias'] }}</th>
				<th>{{ $model['horas_aceitas'] }}</th>
				@if ($exibirhorasfaltando)
				<th>{{ $model['horas_faltando'] }}</th>
				@endif
			</tr>
		</tfoot>
	</table>


	<table class="table table-striped">
		<caption>Distribuição das Atividades Complementares</caption>
		<colgroup>
			<col>
			<col style="width: 140px">
			<col style="width: 90px">
			@if ($exibirhorasfaltando)
			<col style="width: 90px">
			@endif
		</colgroup>
		<thead>
			<tr>
				<th>Tipos de atividade</th>
				<th>Limite de horas</th>
				<th>Aceitas</th>
				@if ($exibirhorasfaltando)
				<th>Disponíveis</th>
				@endif
			</tr>
		</thead>
		<tbody>

			@if ($model['saldo_anterior'] > 0)
			<tr>
				<td>Saldo anterior</td>
				<td>-</td>
				<td>{{ $model['saldo_anterior'] }}</td>
				@if ($exibirhorasfaltando)
				<td>-</td>
				@endif
			</tr>
			@endif
			
			@foreach($model['tipos_normais'] as $value)
				@if(!$value->obrigatorio)
				<tr>
					<td>
						<div style="margin-left: {{ substr_count($value->nivel, '.') * 10 }}px;">
							{{ $value->descricao }}
						</div>
					</td>
					@if($value->possui_itens)
						<td>{{ $value->horas }} no total</td>
						<td>{{ $value->horas_aceitas }}</td>
						@if ($exibirhorasfaltando)
						<td>{{ $value->horas_disponiveis }}</td>
						@endif
					@else
						<td>{{ $value->horas }} por atividade</td>
						<td>{{ $value->horas_aceitas }}</td>
						@if ($exibirhorasfaltando)
						<td title="sem limite">&infin;</td>
						@endif
					@endif
				</tr>
				@endif
			@endforeach
		</tbody>
	</table>

</section>

@stop