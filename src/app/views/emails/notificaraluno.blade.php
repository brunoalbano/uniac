<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2 style="text-align: center">UNIAC - Atividades Complementares do UNISAL</h2>
		
		<hr/>
		
		<h2>Atualização de atividades</h2>

		<div>
			<p>Suas atividades complementares foram atualizadas.</p>

			<br>

			@if (empty($tituloAtividade) === false)
				<p>Atividade: <strong>{{ $tituloAtividade }}</strong></p>
			@endif

			<br>

			<p>Para visualizar os detalhes acesse: <a href="{{ URL::to('atividades') }}">{{ URL::to('atividades') }}</a>.</p>
		</div>

		<br/>

		<div>
			<small>
				Se você não deseja mais receber notificações por e-mail altere a configuração da sua conta <a href="{{ URL::to('configuracao') }}">clicando aqui</a>.
			</small>
		</div>

	</body>
</html>