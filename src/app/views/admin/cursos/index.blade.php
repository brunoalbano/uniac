@extends('layout/layout')

@section('content')
	
	@if(Auth::user()->administrador)
	<div>
		<a href="{{ url('cursos/inserir') }}" class="btn btn-default">Inserir</a>
	</div>
	@endif

    <div id='jqxWidget'>
        <div id="jqxgrid"></div>
    </div>

    {{ Form::token() }}

@stop

@section('script')

	<script type="text/javascript">
	(function(){

		var urlListar = "{{ url('cursos/listar') }}";	 

		var urlVisualizar = "{{ url('cursos') }}/";

		var naoPossuiPermissao = {{ Auth::user()->convidado ? 'true' : 'false' }};

		var possuirPermissaoParaExcluir = {{ Auth::user()->administrador ? 'true' : 'false' }};

		// grid principal
		var gridSource =
		{	
			datafields: [
				{ name: 'curso.codigo', type: 'int' },
				{ name: 'curso.nome', type: 'string' },
				{ name: 'campus.nome', type: 'string' }
			],
			sortcolumn: 'curso.nome',
			id: 'curso.codigo',
	        sortdirection: 'asc',
			url: urlListar
		};
		
		// initialize jqxGrid
		var gridConfig = 
		{		
			columns: [
				{ text: '', datafield: 'curso.codigo', width: possuirPermissaoParaExcluir ? 82 : 43, filterable: false, sortable: false, cellsrenderer: renderButtons, hidden: naoPossuiPermissao, pinned: true },
				{ text: 'Nome', datafield: 'curso.nome', width: 500 },
				{ text: 'Campus', datafield: 'campus.nome', type: 'string', width: 300 },
			]
		};

		function renderButtons(row, columnfield, value, defaulthtml, columnproperties){
			return '<a href="cursos/' + value + '/editar"  class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil"></span></a> ' + 
					(possuirPermissaoParaExcluir ?
				   		'<a href="cursos/' + value + '/excluir" class="btn btn-danger btn-sm btn-excluir"><span class="glyphicon glyphicon-trash"></span></a>' : 
				   		'');
		}	

		$(document).ready(function () { 
			inicializarGridPrincipal("#jqxgrid", gridSource, gridConfig, urlVisualizar);
		});
	})();
	</script>


@stop