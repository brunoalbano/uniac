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
		$opcoesUrl = '?turma_codigo=' . $turma_codigo . '&status=' . $status;
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
		<div class="well">
			<div class="row">
			<div class="col-sm-6"><strong>Aluno:</strong> {{ $matricula->usuario->nome_completo }}, {{ $matricula->usuario->login }}</div>
			<div class="col-sm-6"><strong>Turma:</strong> {{ $matricula->turma->nome . ', ' . $matricula->turma->curso->nome . ', ' . $matricula->turma->curso->campus->nome }}</div>
			</div>
		</div>
	@endif
<!--
	<div class="visible-print">
		
			Necessárias: <strong>{{ $matricula->horas_necessarias }}</strong>;
		
			Aceitas: <strong>{{ $matricula->horas_aceitas }}</strong>;		
		
			Faltando: <strong>{{ $matricula->horas_faltando }}</strong>
		
	</div>
-->
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
		<div class="col-sm-4">
			<div class="alert alert-warning">
				Faltando: <strong>{{ $model['horas_faltando'] }}</strong>
			</div>
		</div>
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
				<th>Faltando</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Atividades Complementares</td>
				<td>{{ $model['horas_necessarias_normais'] }}</td>
				<td>{{ $model['horas_aceitas_normais'] }}</td>
				<td>{{ $model['horas_faltando_normais'] }}</td>
			</tr>
			
			@foreach($model['tipos_obrigatorios'] as $value)
				@if($value->obrigatorio)
				<tr>
					<td>{{ $value->descricao }}</td>
					<td>{{ $value->horas_necessarias }}</td>
					<td>{{ $value->horas_aceitas }}</td>
					<td>{{ $value->horas_faltando }}</td>
				</tr>
				@endif
			@endforeach
		</tbody>

		<tfoot>
			<tr>
				<th>Total</th>
				<th>{{ $model['horas_necessarias'] }}</th>
				<th>{{ $model['horas_aceitas'] }}</th>
				<th>{{ $model['horas_faltando'] }}</th>
			</tr>
		</tfoot>
	</table>


	<table class="table table-striped">
		<caption>Distribuição das Atividades Complementares</caption>
		<colgroup>
			<col>
			<col style="width: 140px">
			<col style="width: 90px">
			<col style="width: 90px">
		</colgroup>
		<thead>
			<tr>
				<th>Tipos de atividade</th>
				<th>Limite de horas</th>
				<th>Aceitas</th>
				<th>Disponíveis</th>
			</tr>
		</thead>
		<tbody>

			@if ($model['saldo_anterior'] > 0)
			<tr>
				<td>Saldo anterior</td>
				<td>-</td>
				<td>{{ $model['saldo_anterior'] }}</td>
				<td>-</td>
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
						<td>{{ $value->horas_disponiveis }}</td>
					@else
						<td>{{ $value->horas }} por atividade</td>
						<td>{{ $value->horas_aceitas }}</td>
						<td title="sem limite">&infin;</td>
					@endif
				</tr>
				@endif
			@endforeach
		</tbody>
	</table>

</section>

@stop