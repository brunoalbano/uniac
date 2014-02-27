@extends('layout/layout')

@section('content')
	
	@if(Auth::user()->convidado === false)
	<div>
		<a href="{{ url('turmas/inserir') }}" class="btn btn-default">Inserir</a>
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

		var urlListar = "{{ url('turmas/listar') }}";	 

		var urlVisualizar = "{{ url('turmas') }}/";

		var naoPossuiPermissao = {{ Auth::user()->convidado ? 'true' : 'false' }};

		// grid principal
		var gridSource =
		{	
			datafields: [
				{ name: 'turma.codigo', type: 'int' },
				{ name: 'turma.nome', type: 'string' },
				{ name: 'turma.ativa', type: 'bool' },
				{ name: 'curso.nome', type: 'string' },
				{ name: 'campus.nome', type: 'string' }
			],
			sortcolumn: 'turma.nome',
	        sortdirection: 'asc',
	        id: 'turma.codigo',
			url: urlListar
		};
		
		// initialize jqxGrid
		var gridConfig = 
		{		
			columns: [
				{ text: '', datafield: 'turma.codigo', width: 82, filterable: false, sortable: false, cellsrenderer: renderButtons, hidden: naoPossuiPermissao, pinned: true },
				{ text: 'Nome', datafield: 'turma.nome', width: 300 },
				{ text: 'Ativa', datafield: 'turma.ativa', columntype: 'checkbox', filtertype: 'bool', width: 80 },
				{ text: 'Curso', datafield: 'curso.nome', type: 'string', width: 300 },
				{ text: 'Campus', datafield: 'campus.nome', type: 'string', width: 300 },
			]
		};

		function renderButtons(row, columnfield, value, defaulthtml, columnproperties){
			return '<a href="turmas/' + value + '/editar"  class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil"></span></a> ' +
				   '<a href="turmas/' + value + '/excluir" class="btn btn-danger btn-sm btn-excluir"><span class="glyphicon glyphicon-trash"></span></a>';
		}	

		$(document).ready(function () { 
			inicializarGridPrincipal("#jqxgrid", gridSource, gridConfig, urlVisualizar);
		});
	})();
	</script>


@stop