@extends('layout/autenticar')

@section('title')
  Selecione uma matrícula
@stop

@section('style')

  <style type="text/css">

  </style>

@stop

@section('content')  
  <table class="table">

  @foreach($matriculas as $matricula)  
    <tr>
      <td>
        @if($matricula->status == Matricula::BLOQUEADO)
          <div class="alert alert-warning">
            <p><strong>Matrícula bloqueada.</strong></p> 
            <p>Verifique o acesso com o coordenador do curso.</p>
          </div>
        @endif
        <p><strong>Turma:</strong> {{ $matricula->turma->nome }}</p>
        <p><strong>Curso:</strong> {{ $matricula->turma->curso->nome }}</p>
        <p><strong>Campus:</strong> {{ $matricula->turma->curso->campus->nome }}</p>
      </td>
      @if($matricula->status == Matricula::BLOQUEADO)
        <td></td>
      @else
        <td><a class="btn btn-primary btn-sm pull-right" href="{{ url('selecionarmatricula/' . $matricula->codigo) }}"><span class="glyphicon glyphicon-ok"></span></a></td>
      @endif
    </tr>
  @endforeach

  </table>
@stop