@extends('layout/layout')

<?php 
/* 
    Formulário de um curso 
    Recebe:
        bool    $readonly               Indica se o formulário é somente leitura
        array   $campi                  Lista de campi para selecionar
        string  $tipos_de_atividade     Lista de tipos de atividade para a árvore em formato JSON
        string  $matrizes_curriculares  Lista de matrizes curriculares para a grade em formato JSON
        object  $model                  Curso para editar/visualizar (opcional)
        mixed   $codigo                 Código do curso (opcional)
*/ 
?>

@section('content')

    @parent

	<?php
		$campiOptions = array('' => '');

        // Mapeia a lista de campi no formato correto para o select
		if ($readonly === false) {
			array_walk($campi, function($value) use (&$campiOptions) {
				$campiOptions[$value['codigo']] = $value['nome'];
			});
		}
	?>
  
	@if(isset($model))
		{{ BForm::model($model, array('url' => 'cursos/' . $model->codigo . '/editar', 'id' => 'formCurso', 'files' => true), $readonly) }}		
	@else
		{{ BForm::open(array('url' => 'cursos/inserir', 'id' => 'formCurso', 'files' => true)) }}
	@endif

		{{-- Utilizado para enviar para o servidor a lista de tipos de atividade --}}
		{{ BForm::hidden('tipos_de_atividade', isset($tipos_de_atividade) ? $tipos_de_atividade : null) }}

        {{-- Utilizado para enviar para o servidor a lista de tipos de atividade --}}
        {{ BForm::hidden('matrizes_curriculares', isset($matrizes_curriculares) ? $matrizes_curriculares : null) }}

    	<div class="row">
    		<div class="col-md-4">
				{{ BForm::errorsAlert() }}

				{{ BForm::text('codigo', 'Código', null, array('disabled' => '')) }}
                {{ BForm::hidden('codigo', isset($codigo) ? $codigo : null) }}

				{{ BForm::text('nome', 'Nome', null, array('maxlength' => 50, 'autofocus')) }}

			@if($readonly === false)
				{{ BForm::select('campus_codigo', 'Campus', $campiOptions) }}
			@else
				{{ BForm::text('campus', 'Campus', $model->campus->nome) }}
			@endif
                				
			</div>

			<div class="col-md-8">

                <ul class="nav nav-tabs" id="myTab">
                    <li class="active">
                        <a href="#tab-tipos" data-toggle="tab">
                            <span class="{{ $errors->has('tipos_de_atividade') ? 'text-danger' : '' }}">Tipos de atividades</span>
                        </a>
                    </li>

                    <li><a href="#tab-documentos" data-toggle="tab">Documentos</a></li>

                    <li>
                        <a href="#tab-matriz" data-toggle="tab">                            
                            <span class="{{ $errors->has('matrizes_curriculares') ? 'text-danger' : '' }}">Matrizes curriculares</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="tab-tipos">
                        <div style="margin-bottom: 5px">
                            {{ $errors->first('tipos_de_atividade', '<p class="text-danger">:message</p>') }}

                            @if($readonly === false)
                            <em>(Utilize o botão direito do mouse para editar)</em>
                            @endif
                        </div>
                        {{-- Árvore de tipos de atividade --}}
                        <div id='jqxTipos'>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab-documentos">
                        <div class="row" id="fileupload" style="margin-top: 5px">
                            <div class="col-lg-12">
                                {{-- Anexos --}}
                                <div class="panel panel-default">
                                    @if($readonly === false)
                                    <div class="panel-heading">
                                        <a class="btn btn-success btn-sm fileinput-button" tabindex="4">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            <span>Adicionar Anexos</span>
                                            <input type="file" name="files[]" multiple>
                                        </a>
                                    </div>
                                    @endif
                                    <div class="panel-body">
                                        <div class="row files un-anexos" role="presentation">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab-matriz">
                        {{ $errors->first('matrizes_curriculares', '<p class="text-danger">:message</p>') }}

                        <div style="margin-top: 5px; overflow-x: auto">
                            {{-- Grid de cursos de matriz curricular --}}
                            <div id="jqxgridmatriz"></div>
                        </div>

                    </div>
                </div>


			</div>
		</div>

        <footer class="un-form-footer">
            {{ BForm::submit('Salvar') }}

            <a href="{{ url('cursos') }}">Voltar</a>
        </footer>

	{{ BForm::close() }}

    @if($readonly === false)
    
    {{-- Menu de edição da árvore de tipos de atividade --}}
    <div id='jqxMenu' style="display: none">
        <ul>
            <li><span class="glyphicon glyphicon-plus"></span>&nbsp;Inserir</li>
            <li><span class="glyphicon glyphicon-pencil"></span>&nbsp;Editar</li>
            <li><span class="glyphicon glyphicon-remove"></span>&nbsp;Excluir</li>
        </ul>
    </div>

    {{-- Janela de edição de tipos de atividade --}}
	<div class="modal fade" id="modalTipoAtividade" tabindex="-1" role="dialog" aria-labelledby="labelTipoAtividade" aria-hidden="true">
	    <div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 id="labelTipoAtividade" class="modal-title">Tipo de atividade</h4>
				</div>

                {{ BForm::open(array('id' => 'formTipoAtividade')) }}

                {{ BForm::hidden('inserido') }}
                {{ BForm::hidden('editado') }}
                {{ BForm::hidden('codigo') }}

				<div class="modal-body">
					<div id="modal-content">
                        <div class="alert alert-info">
                            <p>- Se o tipo de atividade <strong>possuir</strong> subtipos ou <strong>for obrigatório</strong> o campo "Horas" é o total máximo de horas de atividades aceitas.</p>
                            <p>- Se o tipo de atividade <strong>não possuir</strong> subtipos o campo "Horas" é o limite de horas por atividade.</p>
                        </div>
						<div class="row">                            
							<div class="col-sm-4">
								{{ BForm::integer('horas', 'Horas', null, array('min' => 1, 'required')) }}
							</div>
                            <div class="col-sm-4">                                
                                <br/>
                                {{ BForm::checkbox('obrigatorio', 'Obrigatório', null, 1, array('title' => 'Indica se o aluno deve cumprir com todo o limite de horas')) }}
                            </div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								{{ BForm::textarea('descricao', 'Descrição', null, array('rows' => 3, 'required', 'placeholder'=> 'Exemplo: 2.4.1. Atividade de natureza acadêmica', 'maxlength' => 200)) }}
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">                                
								<label style="margin-top: 10px">Utilizado por:</label>
							</div>
							<div class="col-sm-5">
								{{ BForm::checkbox('visivel_para_aluno', 'Aluno', null, 1, array('title' => 'Indica se o tipo de atividade pode ser utilizado pelo aluno ao criar atividades')) }}
							</div>
							<div class="col-sm-3">
								{{ BForm::checkbox('ativo', 'Supervisor', null, 1, array('title' => 'Indica se o tipo de atividade pode ser utilizado pelo supervisor ao criar atividades')) }}
							</div>
						</div>
					</div>				
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" id="btnSalvarTipo">Salvar</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
				</div>
                {{ BForm::close() }}    
			</div>
	    </div>
	</div>

    @endif

@stop


@section('script')

    {{ HTML::script('assets/js/curso.js') }}

    <script type="text/javascript">
        $(document).ready(function () {
            var editable = {{ $readonly ? 'false' : 'true' }};

            var urlFileupload = '{{ url("cursos/" . (isset($codigo) ? $codigo : $model->codigo) . "/anexos") }}';

            initializeGridMatrizCurricular(editable);
                
            initializeTreeTiposDeAtividade(editable);

            $('#fileupload').uniacFileupload({
                url : urlFileupload
            });
        });
    </script>

@stop

{{-- Adiciona os templates de download/upload de anexos --}}
@if($readonly === false)

    @include('anexos/edit')

@else

    @include('anexos/show')

@endif