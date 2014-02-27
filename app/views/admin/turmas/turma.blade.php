@extends('layout/layout')

<?php 
/* 
    Formulário de um turma 
    Recebe:
        bool    $readonly               Indica se o formulário é somente leitura
        array   $cursos                 Lista de cursos para selecionar
        object  $model                  Curso para editar/visualizar (opcional)
        array   $matriculas             Lista de matrículas quando aberto para edição/visualização (opcional)
*/ 
?>

@section('content')

	<?php
		$cursosOptions = array('' => '');

        // Mapeia a lista de cursos agrupado por Campus para o select
		if ($readonly === false) {
			array_walk($cursos, function($value) use (&$cursosOptions) {
                $nome = $value['campus']['nome'];

                if (isset($cursosOptions[$nome]) === false)
                    $cursosOptions[$nome] = array();

				$cursosOptions[$nome][$value['codigo']] = $value['nome'];
			});
		}
	?>
  
	@if(isset($model))
		{{ BForm::model($model, array('url' => 'turmas/' . $model->codigo . '/editar', 'id' => 'formCurso', 'files' => true), $readonly) }}		
	@else
		{{ BForm::open(array('url' => 'turmas/inserir', 'id' => 'formCurso', 'files' => true)) }}
	@endif
		{{-- Utilizado para enviar para o servidor a lista de tipos de matriculas --}}
		{{ BForm::hidden('matriculas', isset($matriculas) ? $matriculas : null) }}

    	<div class="row">
    		<div class="col-lg-4">
    			<div class="row">
    			<div class="col-md-9 col-lg-12">
				{{ BForm::errorsAlert() }}

				{{ BForm::text('codigo', 'Código', null, array('disabled' => '')) }}
                {{ BForm::hidden('codigo', isset($codigo) ? $codigo : null) }}

				{{ BForm::text('nome', 'Nome', null, array('maxlength' => 50, 'autofocus')) }}

			@if($readonly === false)
				{{ BForm::select('curso_codigo', 'Curso', $cursosOptions) }}
			@else
				{{ BForm::text('curso', 'Curso', $model->curso->nome) }}
			@endif

            	{{ BForm::checkbox('ativa', 'Ativa', 1, true) }}
                </div>				
                </div>
			</div>

			<div class="col-lg-8" id="divMatriculas" style="display: none">				
				<div class="form-group">
					<label class="{{ $errors->has('matriculas') ? 'text-danger' : ''}}">Matrículas</label>
					{{ $errors->first('matriculas', '<p class="text-danger">:message</p>') }}

					<div id="jqxgridmatriz"></div>
				</div>
			</div>
		</div>

        <footer class="un-form-footer">
            {{ BForm::submit('Salvar') }}

            <a href="{{ url('turmas') }}">Voltar</a>
        </footer>

	{{ BForm::close() }}

@stop

@section('script')
<?php
	$curso_codigo = Input::old('curso_codigo');
?>
	@if(isset($model) || empty($curso_codigo) === false)
		<script type="text/javascript">
	    	$(function(){
				initializeGridMatrizCurricular({{ $readonly ? 'false' : true }}, "{{ url('turmas/matrizescurriculares') }}", "{{ url('turmas/alunos') }}");
	    	});
		</script>
	@else
		<script type="text/javascript">	
	    	$(function(){
	    		$('#curso_codigo').one('change', function() {
	    			initializeGridMatrizCurricular({{ $readonly ? 'false' : true }}, "{{ url('turmas/matrizescurriculares') }}", "{{ url('turmas/alunos') }}");
	    		});
	    	});
		</script>
	@endif

    
    {{ HTML::script('assets/js/turma.js') }}
@stop