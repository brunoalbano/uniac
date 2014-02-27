@extends('layout/layout')

@section('content')

	@if(Auth::user()->administrador)
	<div>
		<a href="{{ url('motivosrecusa/inserir') }}" class="btn btn-default">Inserir</a>
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

		var urlListar = "{{ url('motivosrecusa/listar') }}";	 

		var urlVisualizar = "{{ url('motivosrecusa') }}/";

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
				{ text: '', datafield: 'codigo', width: 82, filterable: false, sortable: false, cellsrenderer: renderButtons, pinned: true, hidden: naoPossuiPermissao },
				{ text: 'Nome', datafield: 'nome', width: 700 },
			]
		};

		function renderButtons(row, columnfield, value, defaulthtml, columnproperties){
			return '<a href="motivosrecusa/' + value + '/editar"  class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil"></span></a> ' +
				   '<a href="motivosrecusa/' + value + '/excluir" class="btn btn-danger btn-sm btn-excluir"><span class="glyphicon glyphicon-trash"></span></a>';
		}	

		$(document).ready(function () { 
			inicializarGridPrincipal("#jqxgrid", gridSource, gridConfig, urlVisualizar);
		});
	})();
	</script>

@stop