@extends('layout/autenticar')

@section('title')
  Redefinir senha
@stop

@section('content')      

    {{ BForm::open() }}    

		<input type="hidden" name="token" value="{{ $token }}">

		{{ BForm::email('email', 'E-mail', null, array('placeholder' => 'endereco@email.com', 'autofocus')) }}

		{{ BForm::password('password', 'Nova senha') }}

		{{ BForm::password('password_confirmation', 'Confirmar nova senha') }}

		<button class="btn btn-large btn-primary btn-block" type="submit">Confirmar</button>

    {{ BForm::close() }}
@stop        