<?php 
/* 
    Template de anexos para upload, download e remover anexos.
    Usado em telas de edição.
*/ 
?>

@section('content')

<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">

{% for (var i=0, file; file=o.files[i]; i++) { %}
    <div class="col-md-6 fade template-download">
    	<div class="un-anexo">
	    	<div class="row">
	    		<div class="un-anexo-nome col-xs-8 col-md-6">
		            {% if (file.url) { %}
		                <a href="{%=file.url%}" title="{%=file.name%}" target="_blank">{%=file.name%}</a>
		            {% } else { %}
		                <span title="{%=file.name%}">{%=file.name%}</span>
		            {% } %}
	            </div>

	            <div class="un-anexo-tamanho col-xs-3 col-md-4">
	            	({%=o.formatFileSize(file.size)%})
	            </div>

		        <div class="col-xs-1 col-md-2">
		            {% if (file.deleteUrl) { %}
		                <button class="btn btn-danger btn-xs delete pull-right" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}" data-data='{"_token":"{%=$("[name=_token]").val()%}"}'  {% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
		                    &times;
		                </button>
		            {% } else { %}
		                <button class="btn btn-danger btn-xs delete pull-right">
		                    &times;
		                </button>
		            {% } %}
		        </div>
		    </div>  
	        {% if (file.error) { %}
	        <div class="row">
		        <div class="col-xs-12">
		                <span class="label label-danger">Erro</span> {%=file.error%}
		        </div>
		    </div>
	        {% } %}
	        
	    </div>
    </div>
{% } %}

</script>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <div class="col-md-6 fade template-upload">
        <div class="un-anexo">
        	<div class="row">
	        	<div class="un-anexo-nome col-xs-8 col-md-6">
		            <span title="{%=file.name%}">{%=file.name%}</span>
	            </div>

	            <div class="un-anexo-tamanho col-xs-3 col-md-4">
		            {% if (!o.files.error) { %}
		                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
		            {% } else { %}
		            	({%=o.formatFileSize(file.size)%})
		            {% } %}
	            </div>

		        <div class="col-xs-1 col-md-2">
		            {% if (!i) { %}
		                <button class="btn btn-danger btn-xs cancel pull-right">
		                    &times;
		                </button>
		            {% } %}
		        </div>
	        </div>
	            
	        {% if (file.error) { %}
	        <div class="row">
		        <div class="col-xs-12">
		        	<span class="label label-danger">Erro</span> {%=file.error%}
		        </div>
	        </div>
	        {% } %}

        </div>
    </div>
{% } %}
</script>

@stop