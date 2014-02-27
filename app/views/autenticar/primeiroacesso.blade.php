@extends('layout/autenticar')

@section('title')
  Primeiro acesso
@stop

@section('content')
    {{ BForm::open(array('autocomplete' => 'on')) }}    
    
		{{ BForm::text('login', 'R. A.', null, array('placeholder' => 'Informe o seu R.A.', 'autofocus', 'required')) }}

		<button class="btn btn-large btn-primary btn-block" type="submit">Confirmar</button>

    {{ BForm::close() }}
    <br>
    <p><a href="{{ url('autenticar') }}">Voltar</a></p>
@stop      