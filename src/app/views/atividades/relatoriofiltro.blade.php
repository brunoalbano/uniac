<div class="well">
	<div class="row">
		@if (isset($aluno))
		<div class="col-sm-6"><strong>Aluno:</strong> {{ $aluno->nome_completo }}, {{ $aluno->login }}</div>
		@endif
		@if (isset($turma))
		<div class="col-sm-6"><strong>Turma:</strong> {{ $turma->nome . ', ' . $turma->curso->nome . ', ' . $turma->curso->campus->nome }}</div>
		@endif
		@if (isset($status) && !empty($status))
		<div class="col-sm-6"><strong>Status da matrícula:</strong> {{ $status}}</div>
		@endif
		@if (isset($data_inicial) && !empty($data_inicial))
		<div class="col-sm-6"><strong>Data inicial:</strong> {{ Helpers::dateDescription($data_inicial) }}</div>
		@endif
		@if (isset($data_final) && !empty($data_final))
		<div class="col-sm-6"><strong>Data final:</strong> {{ Helpers::dateDescription($data_final) }}</div>
		@endif
	</div>
</div>