@extends('layout/layout')

@section('content')

	<?php
		$inserindo = isset($model) === false;
	?>

	@if(isset($model))		
		{{ BForm::model($model, array('url' => "usuarios/$model->codigo/editar"), $readonly) }}		
	@else
		{{ BForm::open(array('url' => 'usuarios/inserir')) }}
	@endif    
		{{-- Utilizado para enviar para o servidor um json com os cursos que o usuário tem acesso --}}
		<input type="hidden" name="cursos" id="cursos" value="[]"/>

    	<div class="row">
    		<div class="col-lg-4">
				{{ BForm::errorsAlert() }}

				{{ BForm::text('codigo', 'Código', null, array('disabled' => '')) }}

				@if(Auth::user()->coordenador)
				{{ BForm::hidden('perfil', Usuario::ALUNO) }}
				@else
				{{ BForm::select('perfil', 'Perfil', array(
				    Usuario::ADMINISTRADOR => 'Administrador',
				    Usuario::ALUNO => 'Aluno',
				    Usuario::CONVIDADO => 'Convidado',
				    Usuario::SUPERVISOR => 'Supervisor'
				), $inserindo ? Usuario::ALUNO : null) }}
				@endif

				{{ BForm::text('primeiro_nome', 'Primeiro nome', null, array('autofocus')) }}
		    
				{{ BForm::text('sobrenome', 'Sobrenome') }}

				{{ BForm::text('login', 'Login', null, array('placeholder' => 'R.A. ou login')) }}

				{{ BForm::email('email', 'E-mail') }}

				@if ($inserindo)
				
				{{ BForm::password('senha', 'Senha') }}

				{{ BForm::password('confirmar_senha', 'Confirmar senha') }}
				
				@endif

				{{ BForm::checkbox('acesso_liberado', 'Acesso liberado', '1') }}

				{{ BForm::checkbox('notificar', 'Enviar e-mails de notificações ao atualizar atividades', '1', true) }}

				<div>
		            @if (isset($model) && (Auth::user()->coordenado || Auth::user()->administrador))
		            <a href="{{ url('usuarios/' . $model->codigo . '/resetarsenha') }}">Enviar e-mail para redefinir senha</a>
		            @endif
		        </div>
			</div>
			<div>
				{{-- Grid de cursos do supervisor --}}
				<div id="jqxgrid"></div>
				
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
			    <footer class="un-form-footer">
		            {{ BForm::submit('Salvar') }}

		            <a href="{{ url('usuarios') }}">Voltar</a>
		        </footer>
			</div>
		</div>

	{{ BForm::close() }}

@stop

@section('script')

	@if ($readonly === FALSE || (int)$model->perfil === Usuario::SUPERVISOR)

	<?php
		function obterCursos()
		{
			$cursos = Input::old('cursos', '');

			if (empty($cursos))
				$cursos = Session::get('cursos', '[]');

			return $cursos;
		}
	?>

	<script>
	(function(){
		var EDITABLE = {{ $readonly === TRUE ? 'false' : 'true' }},
			SUPERVISOR = {{ Usuario::SUPERVISOR }};

		var cursosDoUsuario = {{ obterCursos() }};

		var urlListaDeCursosParaDropdown = "{{ url('cursos/listar') }}";

		var gridCursosInicializada = false;

		function inicializarGridCursos()
		{
			// lista de cursos para dropdown da grid
	        var cursosSource =
            {
                datatype: "json",
                datafields: [
                    { name: 'codigo', type: 'int', map: 'curso.codigo' },
                    { name: 'nome', type: 'string', map: 'curso.nome' },
                    { name: 'group', type: 'string', map: 'campus.nome' },
                ],
                id: 'codigo',
                root: 'data',
                url: urlListaDeCursosParaDropdown,
                async: false
            };
            var cursosAdapter = new $.jqx.dataAdapter(cursosSource, {
                autoBind: true
            });

            // dados da grid
	        var source =
	        {
	            datatype: "json",
	            datafields: [
	            	{ name: 'curso_nome', value: 'codigo', values: { source: cursosAdapter.records, value: 'codigo', name: 'nome' } },
	                { name: 'codigo', type: 'int' }, // código do curso
	                { name: 'coordenador', type: 'bool' }
	            ],
	            id: 'codigo', // código do curso
	            localdata: cursosDoUsuario
	        };

	        var dataAdapter = new $.jqx.dataAdapter(source);

	        $("#jqxgrid").jqxGrid(
	        {
	            width: 600,
	            source: dataAdapter,
	            columnsresize: true,
                showtoolbar: EDITABLE,
                editable: EDITABLE,
	        	rowsheight: 37,
	        	localization: getGridLocalization(),
	            columns: [
	            	{ text: 'Curso', datafield: 'codigo', displayfield: 'curso_nome', columntype: 'dropdownlist', width: 300,
                        createeditor: function (row, value, editor) {
                            editor.jqxDropDownList({ source: cursosAdapter.records, displayMember: 'nome', valueMember: 'codigo', placeHolder: '(Escolha)' });
                        },
                        validation: function (cell, value) {
			                if (!value) {
			                    return { result: false, message: "Campo obrigatório" };
			                }
			                return true;
			            },
                    },
	            	{ text: 'Coordenador', datafield: 'coordenador', columntype: 'checkbox', width: 100 },
	            	{ text: '', datafield: 'Excluir', columntype: 'button', width: 80, hidden: !EDITABLE,
	            		cellsrenderer: function () {
	                    	return "Excluir";
	                	}, 
	                	buttonclick: function (row) {
	                		var data = $('#jqxgrid').jqxGrid('getrowdata', row);
	                		$('#jqxgrid').jqxGrid('deleterow', data.uid);
	                	}
	            	}
	          	],
	          	rendertoolbar: function (toolbar) {   
	          		var button = $('<a class="btn btn-success btn-sm fileinput-button" style="margin: 2px;">' +
					                '    <i class="glyphicon glyphicon-plus"></i>' +
					                '    Inserir' +
					                '</a>');

                    toolbar.append(button);

                    button.on('click', function(){                    	
                    	$("#jqxgrid").jqxGrid('addrow', generateId(), { coordenador: false });
                    	var rows = $('#jqxgrid').jqxGrid('getrows');
                    	var index = rows.length - 1;
                    	var editable = $("#jqxgrid").jqxGrid('begincelledit', index, "codigo");
                    });
                }
	        });

			$('form').on('submit', function(){
				// Salva a lista de cursos do usuário em um input[hidden]
				// para que seja enviada para o servidor junto com o formulário
				var rows = $('#jqxgrid').jqxGrid('getrows');
				$('#cursos').val(JSON.stringify(rows));
			});
		}

		function onPerfilChange()
		{
			var perfil = $('select[name=perfil]').val(),
				grid = $("#jqxgrid");
				
			if (perfil == SUPERVISOR)
			{
				if (!gridCursosInicializada)
				{
					inicializarGridCursos();
					gridCursosInicializada = true;
				}

				grid.show();
			}
			else
				grid.hide();
		}

		$(document).ready(function() { 
			$('select[name=perfil]').on('change', onPerfilChange);

			onPerfilChange();
	    });
	})();
	</script>

	@endif

@stop