@extends('layout/layout')

@section('content')

	@if(isset($model))		
		{{ BForm::model($model, array('url' => "motivosrecusa/$model->codigo/editar"), $readonly) }}		
	@else
		{{ BForm::open(array('url' => 'motivosrecusa/inserir')) }}
	@endif    

    	<div class="row">
    		<div class="col-sm-7 col-md-6">
				{{ BForm::errorsAlert() }}

				{{ BForm::text('codigo', 'Código', null, array('disabled' => '')) }}
				{{ BForm::hidden('codigo') }}

				{{ BForm::text('nome', 'Nome', null, array('autofocus')) }}

				<footer class="un-form-footer">
		            {{ BForm::submit('Salvar') }}

	            	<a href="{{ url('motivosrecusa') }}">Voltar</a>
	        	</footer>
        	</div>
		</div>

	{{ BForm::close() }}

@stop