@extends('layout/principal')

@section('style')

@stop

@section('content')

<section>
	<h3>Ajuda</h3>
	<hr/>

	@if(Auth::user()->aluno)
		<p><a href="{{ url('manuais/manual_aluno.pdf') }}">Manual do Aluno</a></p>

		@foreach ($documentos as $documento)
			<p><a href="{{ url('ajuda/documento/' . $documento->codigo) }}">{{ $documento->nome }}</a></p>			
		@endforeach
	@else
		<p><a href="{{ url('manuais/manual_supervisor.pdf') }}">Manual do Supervisor</a></p>
	@endif

	@if(Auth::user()->coordenador || Auth::user()->administrador)
		<p><a href="{{ url('manuais/manual_administracao.pdf') }}">Manual de Administração</a></p>
	@endif
</section>

@stop