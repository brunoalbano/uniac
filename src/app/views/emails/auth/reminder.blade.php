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
			Para redefinir a sua senha clique sobre o endereço abaixo ou copie e cole na barra de endereço do seu navegador:
		</div>

		<br/>

		<div style="text-align: center">
			<a href="{{ URL::to('password/reset', array($token)) }}">{{ URL::to('password/reset', array($token)) }}</a>
		</div>

		<br/>

		<div>
			Se você não deseja alterar a sua senha, apenas ignore essa mensagem.
		</div>

	</body>
</html>