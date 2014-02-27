<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2 style="text-align: center">UNIAC - Atividades Complementares do UNISAL</h2>

		<hr/>
		
		<h2>Redefinir senha</h2>

		<div>
			Para redefinir a sua senha, complete esse formulário: {{ URL::to('password/reset', array($token)) }}.
		</div>

		<br/>

		<div>
			Se você não deseja alterar a sua senha, apenas ignore essa mensagem.
		</div>

	</body>
</html>