@extends('layout/autenticar')

@section('title')
  Primeiro acesso
@stop

@section('content')
    {{ BForm::open(array('autocomplete' => 'on')) }} 
    	<br>   
    	<h4>R. A.: {{ $model->login }}</h4>

    	<p>Será enviado um e-mail com a próxima etapa para definir sua senha para o endereço:</p>

    	<p><strong>{{ $model->email }}</strong></p>

    	<p>O endereço de e-mail confere?</p>

    	{{ BForm::hidden('email', $model->email) }}

        {{ BForm::hidden('confirma', 1) }}

        <button class="btn btn-large btn-primary" type="submit">Sim</button>

		<a class="btn btn-large btn-danger" href="{{ url('autenticar?emailinvalido=' . $model->email) }}">Não</a>

    {{ BForm::close() }}
    <br>
    <p><a href="{{ url('autenticar') }}">Voltar</a></p>
@stop      