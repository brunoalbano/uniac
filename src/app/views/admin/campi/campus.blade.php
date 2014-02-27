@extends('layout/layout')

@section('content')

	@if(isset($model))		
		{{ BForm::model($model, array('url' => "campi/$model->codigo/editar"), $readonly) }}		
	@else
		{{ BForm::open(array('url' => 'campi/inserir')) }}
	@endif    
    	<div class="row">
    		<div class="col-lg-4">
				{{ BForm::errorsAlert() }}

				{{ BForm::text('codigo', 'Código', null, array('disabled' => '')) }}
				{{ BForm::hidden('codigo') }}

				{{ BForm::text('nome', 'Nome', null, array('autofocus')) }}

		        <footer class="un-form-footer">
		            {{ BForm::submit('Salvar') }}

		            <a href="{{ url('campi') }}">Voltar</a>
		        </footer>
			</div>
		</div>

	{{ BForm::close() }}

@stop