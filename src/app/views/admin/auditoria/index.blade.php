@extends('layout/layout')

@section('content')

    <div id='jqxWidget'>
        <div id="jqxgrid"></div>
    </div>

    {{ Form::token() }}

@stop

@section('script')

	<script type="text/javascript">
	(function(){

		var urlListar = "{{ url('auditoria/listar') }}";	 


		var registrorenderer = function (row, column, value, cell, cellopts, data) {
			debugger;
			if (data.tabela_codigo_campo)
            	return data.tabela_codigo_campo + ' = ' + data.tabela_codigo_valor;
        };

		// grid principal
		var gridSource =
		{	
			datafields: [
				{ name: 'usuario.login', type: 'string', map: 'usuario.login' },
				{ name: 'acao', type: 'string' },
				{ name: 'criado_em', type: 'date' },
				{ name: 'url', type: 'string' },
				{ name: 'tabela', type: 'string' },
				{ name: 'tabela_codigo_campo', type: 'string' },
				{ name: 'tabela_codigo_valor', type: 'string' },
				{ name: 'navegador', type: 'string' },
				{ name: 'ip', type: 'string' }
			],
			sortcolumn: 'criado_em',
	        sortdirection: 'desc',
			url: urlListar,
			beforeLoadComplete: function(data) {
				var newData = [];

				$.each(data, function(index, value) {
					if (value)
					{
						if (value.tabela_codigo_campo)
	            			value.tabela_codigo_valor = value.tabela_codigo_campo + ' = ' + value.tabela_codigo_valor;
	            		else
	            			value.tabela_codigo_valor = '';

						newData[index] = value
					}
				});

				return newData;
			}
		};
		
		// initialize jqxGrid
		var gridConfig = 
		{		
			columns: [
				{ text: 'Login', datafield: 'usuario.login', width: 200 },
				{ text: 'Ação', datafield: 'acao', width: 200 },
				{ text: 'Tabela', datafield: 'tabela', width: 150 },
				{ text: 'Registro', datafield: 'tabela_codigo_valor', width: 150 },
				{ text: 'Data', datafield: 'criado_em', filterable: false, cellsformat: 'dd/MM/yyyy HH:mm:ss', width: 150 },
				{ text: 'URL', datafield: 'url', width: 350 },
				{ text: 'Navegador', datafield: 'navegador', width: 500 },
				{ text: 'IP', datafield: 'ip', width: 200 },
			]
		};

		$(document).ready(function () { 
			inicializarGridPrincipal("#jqxgrid", gridSource, gridConfig);
		});
	})();
	</script>

@stop