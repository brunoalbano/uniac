@extends('layout/layout')

@section('content')

	@if(Auth::user()->convidado === false)
	<div class="row">
		<div class="col-xs-12">
			<a href="{{ url('usuarios/inserir') }}" class="btn btn-default">Inserir</a>

			<div class="pull-right">
				<a href="{{ url('usuarios/exportar') }}" class="btn btn-default">Exportar</a>

				<a href="{{ url('usuarios/importar') }}" class="btn btn-default">Importar Alunos</a>
			</div>
		</div>
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
		var listaDePerfil = [
			{ codigo: 0, descricao: '(Escolha...)'},
			{ codigo: 1, descricao: 'Administrador'},
			{ codigo: 2, descricao: 'Aluno'},
			{ codigo: 3, descricao: 'Convidado'},
			{ codigo: 4, descricao: 'Supervisor'}
		];

		var urlListarUsuarios = "{{ url('usuarios/listar') }}";	 

		var urlVisualizar = "{{ url('usuarios') }}/";

		var naoPossuiPermissao = {{ Auth::user()->convidado ? 'true' : 'false' }};

		// grid principal
		var gridSource =
		{	
			datafields: [
				{ name: 'codigo', type: 'int' },
				{ name: 'primeiro_nome', type: 'string' },
	            { name: 'sobrenome', type: 'string'},
	            { name: 'perfil', type: 'string', values: { source: listaDePerfil, value: 'codigo', name: 'descricao' }},
	            { name: 'login', type: 'string' },
	            { name: 'email', type: 'email'},
	            { name: 'acesso_liberado', type: 'bool'}
			],
			sortcolumn: 'primeiro_nome',
	        sortdirection: 'asc',
			url: urlListarUsuarios
		};
		
		// initialize jqxGrid
		var gridConfig = 
		{		
			columns: [			
				{ text: '', datafield: 'codigo', width: 82, filterable: false, sortable: false, cellsrenderer: renderButtons, pinned: true, hidden: naoPossuiPermissao },
				{ text: 'Nome', datafield: 'primeiro_nome', width: 150 },
				{ text: 'Sobrenome', datafield: 'sobrenome', width: 250 },
				{ text: 'Perfil', datafield: 'perfil', filtertype: 'list', width: 130, createfilterwidget: createfilterwidget },
				{ text: 'Login', datafield: 'login', width: 100 },
				{ text: 'E-mail', datafield: 'email', width: 250 },
				{ text: 'Acesso', datafield: 'acesso_liberado', columntype: 'checkbox', filtertype: 'bool', width: 80 },
			]
		};

		function renderButtons(row, columnfield, value, defaulthtml, columnproperties){
			return '<a href="usuarios/' + value + '/editar"  class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil"></span></a> ' +
				   '<a href="usuarios/' + value + '/excluir" class="btn btn-danger btn-sm btn-excluir"><span class="glyphicon glyphicon-trash"></span></a>';
		}		

		function createfilterwidget(column, columnElement, widget) {
			// lista de cursos para dropdown da grid
		    var perfilSource =
		    {
		        datatype: "json",
		        datafields: [
		            { name: 'codigo', type: 'int' },
		            { name: 'descricao', type: 'string' }
		        ],
		        id: 'codigo',
		        localdata: listaDePerfil
		    };
		    var perfilAdapter = new $.jqx.dataAdapter(perfilSource, {
		        autoBind: true
		    });

			widget.jqxDropDownList({ source: perfilAdapter.records, displayMember: 'descricao', valueMember: 'codigo', placeHolder: '(Escolha...)' });
		}

		$(document).ready(function () { 
			inicializarGridPrincipal("#jqxgrid", gridSource, gridConfig, urlVisualizar);
		});
	})();
	</script>












<!--
<script type="text/javascript">
        $(document).ready(function () {
            
			var url = "http://localhost/uniac/index.php/tiposatividades/listararvorealuno/";

			function obterItems(pai, callBack)
			{
				var urlParam = url;

				if (pai)
					urlParam += pai;

				$.ajax({
					url: urlParam,
					cache: false,
					dataType: 'json'	
				})
				.done(function(data) {
					var result = $.map(data, function(item){
						if (item.possuiItens)
							item.items = [{ value: -1, label: 'Carregando...' }];
						return item;
					});

					callBack(result);
				});
			}

            // Create jqxTree
            var tree = $('#jqxTree');

            function inicializarTree(source) {
            	tree.jqxTree({ source: source, height: 300, width: 800 });

	            tree.bind('expand', function (event) {
	            	var item = tree.jqxTree('getItem', event.args.element);
	                
	                if (item.hasItems && item.nextItem.value < 0) {

                        obterItems(item.value, function (data) {
                            var items = data;
                            tree.jqxTree('addTo', items, item.element);
                            tree.jqxTree('removeItem', item.nextItem);
	                    });
	                }
	            });
			}

			obterItems(null, inicializarTree);
	    });
		
    </script>

    <div id="jqxTree">
    </div>
-->
@stop