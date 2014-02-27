@extends('layout/layout')

@section('content')
		<div class="alert alert-info">
			<ul>
			<li>Para importar alunos selecione um arquivo do tipo CSV. </li>
			<li>Se uma turma for informada os alunos serão matriculados para ela. </li>
			<li>Se o aluno já estiver cadastrado o seu "Acesso Liberado" será atualizado, caso contrário o usuário será inserido.</li>
			<li>Colunas do arquivo: Nome, RA (somente números e letras), E-mail, Acesso Liberado (1 para sim e 0 para não), Saldo anterior de horas (inteiro). </li>
			<li>Utilize ; (ponto-e-vírgula) como separador de colunas.</li>
			</ul>
		</div>

		{{ BForm::open(array('files' => true)) }}

    	<div class="row">
    		<div class="col-lg-4">

    			@if (Session::has('errorsMessages'))
					<div class="alert alert-danger"><strong>Atenção!</strong> Corrija os erros para importar o arquivo.</div>
				@endif

				@if (Session::has('errorsMessages'))
					<table class="table">
						<thead>
							<tr>
								<th>Linha</th><th>Erros</th>
							</tr>
						</thead>
						<tbody>
							@foreach(Session::get('errorsMessages') as $key => $errors)
								<tr>
									<td>{{ $key }}</td>
									<td>
										@foreach ($errors as $error) 
											<div class="text-danger">
												{{ $error }}
											</div>
										@endforeach
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				@endif

				{{ BForm::file('arquivo', 'Arquivo CSV', array('accept' => '.csv')) }}

				<div class="panel panel-default">
					<div class="panel-heading">Matricular alunos em</div>
					<div class="panel-body">
						{{ BForm::select('turma', 'Turma', array('Carregando...')) }}

						{{ BForm::select('matriz_curricular', 'Matriz curricular') }}
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
			    <footer class="un-form-footer">
		            {{ BForm::submit('Importar') }}

		            <a href="{{ url('usuarios') }}">Voltar</a>
		        </footer>
			</div>
		</div>

	{{ BForm::close() }}

@stop


@section('script')

<script type="text/javascript">
	ImportarAlunos = function(){
		var oPublic = {};

		var carregarMatrizesCurriculares = function(turma) {
            $.ajax({
                url: '{{ url("usuarios/matrizescurriculares") }}',
                method: 'GET',
                cache: false,
                dataType: 'json',
                data: { turma: turma }  
            })
            .done(function(data) {
                var $matriz_curricular = $('#matriz_curricular');

                $matriz_curricular.html('');
                $matriz_curricular.show();

                for(var i = 0; i < data.length; i++) {
                    var item = data[i];

                    $matriz_curricular.append('<option value="' + item.codigo + '">' + item.nome + ' (' + item.horas + ' horas) </option>');
                }
            });
        }

		var onChangeTurma = function (e) {
			var turma = $('#turma').val();

			if (turma)
			{
				$('#matriz_curricular').uniacLoadSelect({ 
					url: '{{ url("usuarios/matrizescurriculares") }}', 
					valueField: 'codigo', 
					textField: 'nome', 
					data: { turma: turma },
					selected: '{{ Input::old("matriz_curricular") }}',
				});
			}
		}

		oPublic.init = function(){
			$('#turma').uniacLoadSelect({ 
				url: '{{ url("usuarios/turmas") }}', 
				valueField: 'turma_codigo', 
				textField: 'turma_nome', 
				groupField: 'curso_nome',
				selected: '{{ Input::old("turma") }}',
				done: onChangeTurma
			});

			$('#turma').on('change', onChangeTurma);
		}

		return oPublic;
	}();

	$(function(){
		ImportarAlunos.init();
	});
</script>

@stop