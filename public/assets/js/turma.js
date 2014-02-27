
    	var matricula_status = {
    		ATIVO: 1,
    		INATIVO: 2,
    		BLOQUEADO: 3,
    		HOMOLOGADO:4
    	};

		function initializeGridMatrizCurricular(EDITABLE, urlMatriz, urlAlunos)
		{
			$('#divMatriculas').show();

		    var grid = $("#jqxgridmatriz");

            var dataAdapterMatrizCurricular = new $.jqx.dataAdapter({
	                datatype: "json",
	                datafields: [
	                    { name: 'codigo' },
	                    { name: 'nome' },
	                    { name: 'horas' }
	                ],
	                url: urlMatriz
            	},
                {
            		autoBind: true,
            		selectedIndex: 0,
                    formatData: function (data) {
                        data.curso = $('#curso_codigo').val()
                    },
                    beforeLoadComplete: function(data) {
                    	for(var i = 0; i < data.length; i++) {
                    		data[i].nome = data[i].nome + " (" + data[i].horas + " horas)"; 
                    	}

                    	return data;
                    }
                }
            );

	    	var statusSource = [
            	{ nome: 'Ativo', codigo: 1 },
            	{ nome: 'Inativo', codigo: 2 },
            	{ nome: 'Bloqueado', codigo: 3 }
            ];

	    	var statusSourceComHomologado = [
            	{ nome: 'Ativo', codigo: 1 },
            	{ nome: 'Inativo', codigo: 2 },
            	{ nome: 'Bloqueado', codigo: 3 },
            	{ nome: 'Homologado', codigo: 4 }
            ];

	    	var initializeComboBoxAlunos = function(editor) 
	    	{
	            // prepare the data
	            var source =
	            {
	                datatype: "json",
	                datafields: [
	                    { name: 'primeiro_nome' },
	                    { name: 'sobrenome' },
	                    { name: 'login' },
	                    { name: 'codigo' },
						{ name: 'label' }
	                ],
	                url: urlAlunos,
	                data: {
	                    maxRows: 20
	                }
	            };
	            var dataAdapter = new $.jqx.dataAdapter(source,
	                {
	                    formatData: function (data) {
	                        if (editor.jqxComboBox('searchString') != undefined) {
	                            data.search = editor.jqxComboBox('searchString');
	                            return data;
	                        }
	                    },
	                    beforeLoadComplete: function(data) {
	                    	var result = [];

	                    	for(var i = 0; i < data.length; i++) {
	                    		var item = data[i];
	                    		item.label = $.trim(item.primeiro_nome + " " + item.sobrenome) + ", " + item.login;
	                    		result.push(item);
	                    	}

	                    	return result;
	                    }
	                }
	            );
	            var render = function (index, label, value) {
	            	// monta a descrição do nome + RA
	                var item = dataAdapter.records[index];
	                if (item != null) {
	                    var label = $.trim(item.primeiro_nome + " " + item.sobrenome) + ", " + item.login;
	                    return label;
	                }
	                return "";
	            };
	            editor.jqxComboBox(
	            {
	                width: 300,
	                height: 36,
	                source: dataAdapter,
	                remoteAutoComplete: true,
	                autoDropDownHeight: true,
	                displayMember: 'label',
	                valueMember: "codigo",
	                placeHolder: "Digite o nome ou R.A. do aluno",
	                //renderer: render,
	                //renderSelectedItem: render,
	                search: function (searchString) {
	                    dataAdapter.dataBind();
	                }
	            });
	    	};

	    	var initializarDropDownListMatrizCurricular = function(editor)
	    	{
	            editor.jqxDropDownList(
	            {
	                width: 200,
	                height: 36,
	                autoDropDownHeight: true,
	                source: dataAdapterMatrizCurricular.records,
	                displayMember: 'nome',
	                valueMember: "codigo",
	                placeHolder: '...'
	            });
	    	};

	    	var criarDropDownListStatus = function(editor, row)
	    	{
	            editor.jqxDropDownList(
	            {
	                width: 150,
	                height: 36,
	                autoDropDownHeight: true,
	                source: statusSourceComHomologado,
	                displayMember: 'nome',
	                valueMember: 'codigo'
	            });
			};

	    	var initializarDropDownListStatus = function(editor, row)
	    	{
	    		var data = grid.jqxGrid('getrowdata', row),
	    			source;

	    		// Só possívle homologar matrículas sem horas pendentes e com status ATIVO
	    		if (data.permite_homologar == 1 && data.status == matricula_status.ATIVO)
	    			source = statusSourceComHomologado;
	    		else
	    			source = statusSource;

	            editor.jqxDropDownList('source', source);
	    	};

	    	var validateEdit = function (row) {
            	var data = grid.jqxGrid('getrowdata', row);
		    	if (!data.inserido)
		    		return false;
		    };

		    // dados da grid
		    var source =
		    {
		        datatype: "json",
		        datafields: [
		            { name: 'codigo', type: 'int' },
		            { name: 'status_nome', value: 'status', values: { source: statusSourceComHomologado, value: 'codigo', name: 'nome' } },
		            { name: 'permite_homologar', type: 'int'},
		            { name: 'status', type: 'int' },
		            { name: 'saldo_anterior', type: 'int' },
		            { name: 'usuario_codigo', type: 'int' },
		            { name: 'usuario_nome', type: 'string' },
		            { name: 'matriz_curricular_codigo', type: 'int' },
		            { name: 'matriz_curricular_nome', type: 'string' },
		            { name: 'inserido', type: 'bool' }
		        ],
		        id: 'codigo', // código do curso
		        localdata: JSON.parse($('#matriculas').val() || '[]')
		    };

		    var dataAdapter = new $.jqx.dataAdapter(source);

		    // inicializa o componente de grid
		    grid.jqxGrid(
		    {
		        width: '100%',
		        height: 350,
		        source: dataAdapter,
		        columnsresize: true,
		        showtoolbar: EDITABLE,
		        editable: EDITABLE,
		        rowsheight: 37,
		        localization: getGridLocalization(),
		        columns: [
		            { text: 'Aluno', datafield: 'usuario_codigo', displayfield : 'usuario_nome', width: 300,
		                validation: function (cell, value) {
		                    if (!$.trim(value.label))
		                        return { result: false, message: "Campo obrigatório" };

		                    return true;
		                },
		                columntype: 'combobox',
                        createeditor: function (row, column, editor) {
                            initializeComboBoxAlunos(editor);
                        },
                        // update the editor's value before saving it.
                        cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
                            // return the old value, if the new value is empty.
                            if (newvalue == "") return oldvalue;
                        },
                        cellbeginedit: validateEdit
		            },
		            { text: 'Matriz curricular', datafield: 'matriz_curricular_codigo', displayfield: 'matriz_curricular_nome', width: 200,
		                validation: function (cell, value) {
		                    if (!$.trim(value))
		                        return { result: false, message: "Campo obrigatório" };

		                    return true;
		                },
		                columntype: 'dropdownlist',
                        createeditor: function (row, column, editor) {
                            initializarDropDownListMatrizCurricular(editor);
                        },
                        // update the editor's value before saving it.
                        cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
                            // return the old value, if the new value is empty.
                            if (newvalue == "") return oldvalue;
                        },
                        cellbeginedit: validateEdit
		            },
		            { text: 'Saldo anterior', datafield: 'saldo_anterior', columntype: 'int', width: 100 },
		            { text: 'Status', datafield: 'status', displayfield: 'status_nome', columntype: 'dropdownlist', width: 110,
		            	createeditor: function (row, column, editor) {
                            criarDropDownListStatus(editor, row);
                        },
                        initeditor: function (row, column, editor) {                        	
                            initializarDropDownListStatus(editor, row);
                        },
                        cellbeginedit: function (row) {
			            	var data = grid.jqxGrid('getrowdata', row);
					    	if (data.status == matricula_status.HOMOLOGADO && !data.inserido)
					    		return false;
					    }
                    },
		            { text: '', datafield: 'Excluir', columntype: 'button', width: 80, hidden: !EDITABLE,
		                cellsrenderer: function () {
		                    return "Excluir";
		                }, 
		                buttonclick: function (row) {
		                    var data = grid.jqxGrid('getrowdata', row);
		                    grid.jqxGrid('deleterow', data.uid);
		                }
		            }
		        ],
		        rendertoolbar: function (toolbar) {
		            // Botão de inserir
		            var button = $(
		                '<a class="btn btn-success btn-sm fileinput-button" style="margin: 2px;">' +
		                '    <i class="glyphicon glyphicon-plus"></i>' +
		                '    Inserir' +
		                '</a>');

		            toolbar.append(button);

		            button.on('click', function(){

		            	var matriz = dataAdapterMatrizCurricular.records.length ? dataAdapterMatrizCurricular.records[0] : {};

		                grid.jqxGrid('addrow', generateId(), { status: 1, status_nome: 'Ativo', matriz_curricular_codigo: matriz.codigo, matriz_curricular_nome: matriz.nome, inserido: true });
		                var rows = grid.jqxGrid('getrows');
		                var index = rows.length - 1;
		                var editable = grid.jqxGrid('begincelledit', index, "usuario_codigo");
		            });
		        }
		    });

		    $('#formCurso').on('submit', function(){
		        // Salva a lista de cursos do usuário em um input[hidden]
		        // para que seja enviada para o servidor junto com o formulário
		        var rows = grid.jqxGrid('getrows');
		        $('#matriculas').val(JSON.stringify(rows));
		    });

			$('#curso_codigo').on('change', function(){
				grid.jqxGrid('clear');
				dataAdapterMatrizCurricular.dataBind();
			});
		}