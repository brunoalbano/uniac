<?php 
/* 
    Template de anexos para download de anexos.
    Usado em telas de visualização.
*/ 
?>

@section('content')

<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <div class="col-md-6 fade template-download">
    	<div class="un-anexo">
    		<div class="row">
	    		<div class="un-anexo-nome col-xs-8">
		            <a href="{%=file.url%}" title="{%=file.name%}" target="_blank" >{%=file.name%}</a>
	            </div>

	            <div class="un-anexo-tamanho col-xs-4">
	            	({%=o.formatFileSize(file.size)%})
	            </div>
	        </div>
	    </div>
    </div>
{% } %}
</script>

<script id="template-upload" type="text/x-tmpl">
</script>

@stop