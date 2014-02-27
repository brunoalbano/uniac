@extends('layout/autenticar')

@section('title')
  Por favor efetue login
@stop

@section('style')

  <style type="text/css">
      .form-signin input[type="text"] {
        margin-bottom: -1px;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
      }
      .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
      }
  </style>

@stop

@section('content')  
  {{ BForm::open(array('autocomplete' => 'on')) }}            
      <input type="text" name="login" class="form-control" placeholder="R.A. ou login" autofocus required>
      <input type="password" name="senha" class="form-control" placeholder="Senha" required>
      <label class="checkbox">
          <input type="checkbox" name="lembrar" value="true"> Mantenha-me conectado
      </label>
      <button class="btn btn-large btn-primary btn-block" type="submit">Entrar</button>
      <br/>

      <p><a href="{{ url('redefinirsenha') }}">Esqueci minha senha</a></p>

      <p><a href="{{ url('primeiroacesso') }}">Primeiro acesso</a></p>
  {{ BForm::close() }}
@stop