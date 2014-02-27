// retorna um id único
function generateId() {
    generateId._lastId = generateId._lastId || new Date() * 1;

    generateId._lastId += 1;
    return generateId._lastId;
}

(function() {

    $.fn.uniacLoadSelect = function(options) {
        var $this = $(this);

        var url = options.url, 
            valueField = options.valueField, 
            textField = options.textField, 
            groupField = options.groupField,
            dataAjax = options.data,
            ajaxDone = options.done,
            selected = options.selected,
            placeholder = options.placeholder || '';
    
        var done = function(data) {
            $this.html('<option value="">' + placeholder + '</option>');

            var groups = {};

            $.each(data, function(index, item) {
                var $option = $('<option />').val(item[valueField]).text(item[textField]);

                if (groupField) {
                    var $group = groups[item[groupField]];

                    if (!$group) {
                        $group = $('<optgroup />').attr('label', item[groupField])
                        groups[item[groupField]] = $group;
                        $this.append($group);
                    }

                    $group.append($option);
                }
                else
                    $this.append($option);
            });

            if (selected)
                $this.val(selected);

            if ($.isFunction(ajaxDone))
                ajaxDone(data);
        }

        var fail = function() {
            $this.html('');
        }

        $.ajax({
                url: url,
                method: 'GET',
                cache: false,
                dataType: 'json',
                data: dataAjax  
            })
            .done(done)
            .fail(fail);
    }

    var uniac_fileupload_default_options = {};

    // Adiciona ao jquery a inicialização do componente de 
    // upload de arquivos com a configuração padrão do UNIAC
    // de tamanho, tipo e quantidade de arquivos aceitos
    $.fn.uniacFileupload = function(options) {
        var upload = this;

        var fileuploadOptions =  $.extend({}, uniac_fileupload_default_options, options);

        upload.fileupload(fileuploadOptions);
        
        // Load existing files:
        upload.addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: upload.fileupload('option', 'url'),
            dataType: 'json',
            context: upload[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });
    };

    // Define a configuração padrão de tamanho, tipo
    // e quantidade de arquivos aceitos para upload
    $.uniacFileuploadDefaultOptions = function(maxFileSize, acceptFileTypes, maxNumberOfFiles) {
        var acceptFileTypesRegEx = acceptFileTypes.join('|'),
            acceptFileTypesMessage = acceptFileTypes.join(', '),
            maxFileSizeBytes = maxFileSize * 1024;

        uniac_fileupload_default_options = {
            autoUpload : true,
            maxFileSize: maxFileSizeBytes,
            acceptFileTypes: new RegExp('(\\.|\\/)(' + acceptFileTypesRegEx  + ')$'),
            maxNumberOfFiles: maxNumberOfFiles,
            sequentialUploads: true, // Evitar conflitos de upload: alguns arquivos eram perdidos
            messages: {
                maxNumberOfFiles: 'Número máximo de anexos excedido.',
                acceptFileTypes: 'O anexo deve ser um arquivo dos tipos: ' + acceptFileTypesMessage + '.',
                maxFileSize:  'O anexo deve possuir menos que ' + maxFileSize + ' kb.'
            },
            /*formData: {
                _token: $('[name=_token]').val()
            }*/
        };
    };

})();



function inicializarGridPrincipal(gridSelector, gridSource, gridConfig, urlVisualizar)
{       
    var grid = $(gridSelector);

    // source da grid
    var defaultGridSource =
    {
        datatype: "json",
        root: 'data',
        cache: false,
        filter: function()
        {
            // update the grid and send a request to the server.
            grid.jqxGrid('updatebounddata', 'filter');
        },
        sort: function()
        {
            // update the grid and send a request to the server.
            grid.jqxGrid('updatebounddata', 'sort');
        },
        beforeprocessing: function(data)
        {       
            if (data != null)
                _gridSource.totalrecords = data.total;
        }
    };      

    // opcoes da grid
    var defaultGridConfig = 
    {       
        width: "100%",
        source: dataAdapter,
        rowsheight: 37,
        showfilterrow: true,
        filterable: true,
        sortable: true,
        columnsresize: true,
        //autoheight: true,
        height: 480,
        pageable: true,
        virtualmode: true,
        localization: getGridLocalization(),
        rendergridrows: function(obj)
        {
            return obj.data;    
        }
    };

    var _gridSource = $.extend({}, defaultGridSource, gridSource);

    var dataAdapter = new $.jqx.dataAdapter(_gridSource, _gridSource);

    var _gridConfig = $.extend({}, defaultGridConfig, gridConfig);

    _gridConfig.source = dataAdapter;

    // initialize jqxGrid
    grid.jqxGrid(_gridConfig);

    grid.on('click', '.btn-excluir', function(e){
        e.preventDefault();

        var $this = $(this);

        if (confirm('Deseja realmente excluir?'))
        {
    
            grid.jqxGrid('showloadelement');

            var url = $this.attr('href');
            $.ajax(url, {
                type: 'POST',
                data: {
                    _token: $('input[name=_token]').val()
                }
            })
            .always(function(){
                dataAdapter.dataBind();
            })
            .fail(function(){
                alert('Falha ao excluir! Verifique se o registro não está em uso.');
            });
        }

        return false;
    });

    if (urlVisualizar)
    {
        if (urlVisualizar[urlVisualizar.length - 1] != "/")
            urlVisualizar += "/";

        grid.on('rowdoubleclick', function (event) 
        { 
            var $this = $(this);

            var id = grid.jqxGrid('source')._options.id || 'codigo';

            var data = $this.jqxGrid('getrowdata', event.args.rowindex);
            window.location.href = urlVisualizar + data[id];
        });
    }
}

function isRightClick(event) {
    // Indica se o evento foi disparado pelo botão direito do mouse
    var rightclick;
    if (!event) var event = window.event;
    if (event.which) rightclick = (event.which == 3);
    else if (event.button) rightclick = (event.button == 2);
    return rightclick;
}
