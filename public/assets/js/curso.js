/**
* Scripts de controle e inicialização dos componentes 
* do cadastro de cursos
*/

function initializeGridMatrizCurricular(EDITABLE)
{
    var grid = $("#jqxgridmatriz");

    // dados da grid
    var source =
    {
        datatype: "json",
        datafields: [
            { name: 'codigo', type: 'int' },
            { name: 'nome', type: 'string' },
            { name: 'horas', type: 'int' },
            { name: 'inserido', type: 'bool' }
        ],
        id: 'codigo', // código do curso
        localdata: JSON.parse($('#matrizes_curriculares').val() || '[]')
    };

    var dataAdapter = new $.jqx.dataAdapter(source);

    // retorna um id único
    function generateId() {
        generateId._lastId = generateId._lastId || 0;

        generateId._lastId += 1;
        return Math.random() * generateId._lastId;
    }

    // inicializa o componente de grid
    grid.jqxGrid(
    {
        width: 505,
        height: 300,
        source: dataAdapter,
        columnsresize: true,
        showtoolbar: EDITABLE,
        editable: EDITABLE,
        rowsheight: 37,
        localization: getGridLocalization(),
        columns: [
            { text: 'Nome', datafield: 'nome', columntype: 'textbox', width: 300,
                validation: function (cell, value) {
                    if (!$.trim(value))
                        return { result: false, message: "Campo obrigatório" };

                    return true;
                },
            },
            { text: 'Horas', datafield: 'horas', align: 'right', cellsalign: 'right', columntype: 'textbox', width: 100,
                validation: function (cell, value) {
                    if (!value || value <= 0)
                        return { result: false, message: "Campo obrigatório" };
                    else
                        if (Math.floor(value) != value || $.isNumeric(value) == false) // Verifica se é um inteiro
                            return { result: false, message: "Formato inválido" };

                    return true;
                }, 
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
                grid.jqxGrid('addrow', generateId(), { nome: '', horas: 0, inserido: true });
                var rows = grid.jqxGrid('getrows');
                var index = rows.length - 1;
                var editable = grid.jqxGrid('begincelledit', index, "nome");
            });
        }
    });

    $('#formCurso').on('submit', function(){
        // Salva a lista de cursos do usuário em um input[hidden]
        // para que seja enviada para o servidor junto com o formulário
        var rows = grid.jqxGrid('getrows');
        $('#matrizes_curriculares').val(JSON.stringify(rows));
    });
}

function initializeTreeTiposDeAtividade(EDITABLE)
{
    var tree = $('#jqxTipos'),
        contextMenu =  $('#jqxMenu'),
        modal = $('#modalTipoAtividade');

    // Primeiro item
    var rootData = { id: -1, label: '(Tipos de atividade)' };

    var prepareTreeData = function (data) {
        var initialData = JSON.parse($('#tipos_de_atividade').val() || '[]'),
            data = [rootData];

        for(var i = 0; i < initialData.length; i++) {
            var item = {
                id: initialData[i].codigo,
                label: initialData[i].descricao,
                parentId: initialData[i].tipo_atividade_codigo || rootData.id,
                value: initialData[i]
            };

            data.push(item);
        }

        var dataAdapter = new $.jqx.dataAdapter({
            datatype: "json",
            datafields: [
                { name: 'id' },
                { name: 'label' },
                { name: 'parentId' },
                { name: 'value' }
            ],
            id: 'id',
            localdata: data
        });

        dataAdapter.dataBind();

        return dataAdapter.getRecordsHierarchy('id', 'parentId', 'items');
    }

    var initializeTree = function() {
        var treeData = prepareTreeData();

        tree.jqxTree({ 
            height: 280,
            source: treeData, 
            allowDrag: EDITABLE, 
            allowDrop: EDITABLE,
            dragStart: function (dragItem) {
                // Não permite reordenar o primeiro item
                if (dragItem.id == rootData.id)
                    return false;
            },
            dragEnd: function (dragItem, dropItem, args, dropPosition, tree) {
                // Não permite reordenar para fora do primeiro item
                if (dropItem.id == -rootData.id)
                    return false;

                dragItem.value.editado = true;
            }
        });
        
        tree.jqxTree('selectItem', tree.find('li:first')[0]);
        tree.jqxTree('expandItem', tree.jqxTree('selectedItem'));

        $('#formCurso').on('submit', function() {
            var json = JSON.stringify(getTreeData());
            $('#tipos_de_atividade').val(json);
        }); 
    }

    var initializeContextMenu = function() {
        if (contextMenu.length == 0)
            return;

        contextMenu.jqxMenu({ width: '110px',  height: '85px', autoOpenPopup: false, mode: 'popup' });

        contextMenu.on('itemclick', function (event) {
            var item = $.trim($(event.args).text());
            switch (item) {
                case "Inserir":
                    add();
                    break;
                case "Editar":
                    edit();
                    break;
                case "Excluir":
                    remove();
                    break;
            }
        });

        // disable the default browser's context menu.
        $(document).on('contextmenu', function (e) {
            if ($(e.target).parents('.jqx-tree').length > 0) {
                return false;
            }
            return true;
        });

        attachContextMenuToTree(); 
    }

    var initializeModalValidation = function() {
        $('#formTipoAtividade').validate({ 
            submitHandler: save, 
            onfocusout: false,
            onkeyup: false,
            onclick: false,
            showErrors: function(errorMap, errorList) {
                var validation = this;

                $(errorList).each(function()
                {
                    var error = this;
                    validation.settings.highlight(error.element, error.message);
                });

                for (var i = 0, elements = this.validElements(); elements[i]; i++)
                {
                    validation.settings.unhighlight(elements[i], validation.settings.errorClass, validation.settings.validClass);
                }
            },
            highlight: function(element, message) {
                var validation = this;

                $(element).parents('.form-group').addClass("has-error");
                $(element).tooltip({ title: message, placement: 'bottom', html: true });
                $(element).tooltip('show');

                $(element).one('blur', function(){
                   $(element).tooltip('destroy');
                });
            },
            unhighlight: function(element) {
                $(element).parents('.form-group').removeClass("has-error");
                $(element).tooltip('destroy');
            },
        }); 
    }

    var attachContextMenuToTree = function () {
        if (EDITABLE == false)
            return;

        // open the context menu when the user presses the mouse right button.
        tree.on('mousedown', 'li', function (event) {
            var target = $(event.target).parents('li:first')[0];
            var rightClick = isRightClick(event);
            if (rightClick && target != null) {
                tree.jqxTree('selectItem', target);

                var selected = tree.jqxTree('selectedItem');

                contextMenu.find('li').show();

                // Não permite editar ou excluir o primeiro item
                if (selected.id == rootData.id)
                {
                    contextMenu.find('li:not(:first)').hide();
                    contextMenu.jqxMenu('height', '30px');
                }
                else
                    // Não permite subitens para tipos obrigatórios
                    if (selected.value.obrigatorio)
                    {
                        contextMenu.find('li:first').hide();
                        contextMenu.jqxMenu('height', '60px');
                    }
                    else
                    {
                        contextMenu.jqxMenu('height', '86px'); 
                    }
                
                var scrollTop = $(window).scrollTop();
                var scrollLeft = $(window).scrollLeft();
                contextMenu.jqxMenu('open', parseInt(event.clientX) + 5 + scrollLeft, parseInt(event.clientY) + 5 + scrollTop);
                return false;
            }
        });
    }

    var setDataToModal = function(data, firstLevel) {
        data = data || {};

        modal.find('#codigo').val(data.codigo || 0);
        modal.find('#horas').val(data.horas || 0);
        modal.find('#descricao').val(data.descricao || '');
        modal.find('#obrigatorio').prop('checked', data.obrigatorio || false);
        modal.find('#visivel_para_aluno').prop('checked', data.visivel_para_aluno || false);
        modal.find('#ativo').prop('checked', data.ativo || false);
        modal.find('#inserido').val(data.inserido || false);
        modal.find('#editado').val(data.editado || false);

        modal.find('#obrigatorio').attr('disabled', !data.inserido || !firstLevel);

        clearModalValidationErrors();
    }

    var getDataFromModal = function() {
        var data = {
            codigo: modal.find('#codigo').val(),
            horas: modal.find('#horas').val(),
            descricao: modal.find('#descricao').val(),
            obrigatorio: modal.find('#obrigatorio').is(':checked'),
            visivel_para_aluno: modal.find('#visivel_para_aluno').is(':checked'),
            ativo: modal.find('#ativo').is(':checked'),
            inserido: modal.find('#inserido').val() == 'true',
            editado: modal.find('#editado').val() == 'true',
        };

        return data;
    }

    var getTreeData = function() {
        var data = tree.jqxTree('getItems');

        var result = [];

        for(var i = 0; i < data.length; i++) {
            var item = data[i].value || {};
            item.codigo = data[i].id;
            item.tipo_atividade_codigo = data[i].parentId;                    

            //if (item.codigo != rootData.id)
                result.push(item);
        }

        return result;
    }

    var add = function() {
        var selected = tree.jqxTree('selectedItem');
        var firstLevel = selected.id == rootData.id;

        var data = { inserido: true, visivel_para_aluno: true, ativo: true };
        setDataToModal(data, firstLevel);
        modal.modal('show');
    }

    var edit = function() {
        var selected = tree.jqxTree('getSelectedItem');
        var firstLevel = selected.id == rootData.id;

        if (selected != null) {
            var data = $.extend({}, selected.value, { editado: true });
            setDataToModal(data, firstLevel);
            modal.modal('show');
        }
    }

    var remove = function() {

        var selected = tree.jqxTree('selectedItem');
        if (selected != null) {

            if (!selected.value.inserido)
            {
                var callback = function()
                {
                    debugger;

                    var prev = selected.prevItem;

                    prev.value = prev.value ||  {};
                    prev.value.removidos = prev.value.removidos || [];

                    prev.value.removidos.push(selected.id);

                    tree.jqxTree('removeItem', selected.element);
                    attachContextMenuToTree();
                };

                $.get('http://127.0.0.1/uniac/cursos/tipoatividadeusado/' + selected.id, function(data){
                    if (data.usado)
                    {
                        alert('Não é possível excluir este tipo, pois ele foi utilizado em uma atividade.');
                    }
                    else
                        callback();
                });
            }
            else
            {
                tree.jqxTree('removeItem', selected.element);
                attachContextMenuToTree();
            }
        }
    }

    var save = function() {
        var selected = tree.jqxTree('getSelectedItem');

        var data = getDataFromModal();

        var item = { label: data.descricao, value: data };

        if (!data.editado)
        {
            tree.jqxTree('addTo', item, selected.element);
            attachContextMenuToTree();
            tree.jqxTree('expandItem', selected.element);
        }
        else
        {
            tree.jqxTree('updateItem', item, selected.element);
        }

        modal.modal('hide');
    }

    var clearModalValidationErrors = function() {
        var groups = $('#formTipoAtividade .has-error');

        groups.removeClass('has-error');

        groups.find(':input').each(function(index, element) {
            $(element).tooltip('destroy');
        });
    }

    initializeTree();

    if (EDITABLE)
    {
        initializeContextMenu();

        initializeModalValidation();
    }
}