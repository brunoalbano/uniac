@extends('layout/layout')

@section('content')

	@if(Auth::user()->administrador)
	<div>
		<a href="{{ url('campi/inserir') }}" class="btn btn-default">Inserir</a>
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

		var urlListar = "{{ url('campi/listar') }}";	 

		var urlVisualizar = "{{ url('campi') }}/";

		var naoPossuiPermissao = {{ Auth::user()->administrador ? 'false' : 'true' }};

		// grid principal
		var gridSource =
		{	
			datafields: [
				{ name: 'codigo', type: 'int' },
				{ name: 'nome', type: 'string' }
			],
			sortcolumn: 'nome',
	        sortdirection: 'asc',
			url: urlListar
		};
		
		// initialize jqxGrid
		var gridConfig = 
		{		
			columns: [
				{ text: '', datafield: 'codigo', width: 82, filterable: false, sortable: false, cellsrenderer: renderButtons, hidden: naoPossuiPermissao, pinned: true },
				{ text: 'Nome', datafield: 'nome', width: 500 },
			]
		};

		function renderButtons(row, columnfield, value, defaulthtml, columnproperties){
			return '<a href="campi/' + value + '/editar"  class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil"></span></a> ' +
				   '<a href="campi/' + value + '/excluir" class="btn btn-danger btn-sm btn-excluir"><span class="glyphicon glyphicon-trash"></span></a>';
		}	

		$(document).ready(function () { 
			inicializarGridPrincipal("#jqxgrid", gridSource, gridConfig, urlVisualizar);
		});
	})();
	</script>

@stop