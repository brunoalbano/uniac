@extends('layout/autenticar')

@section('title')
  Redefinir senha
@stop

@section('content')
	@if (Session::has('success'))
		<div class="alert alert-success">
	    	Um e-mail foi enviado com o próximo passo para redifinir a sua senha.
	    </div>
	@endif  

    {{ BForm::open(array('autocomplete' => 'on')) }}    
    
		{{ BForm::text('email', 'E-mail', null, array('placeholder' => 'endereco@email.com', 'autofocus')) }}

		<button class="btn btn-large btn-primary btn-block" type="submit">Confirmar</button>

    {{ BForm::close() }}

    <br>
    <p><a href="{{ url('autenticar') }}">Voltar</a></p>
@stop      