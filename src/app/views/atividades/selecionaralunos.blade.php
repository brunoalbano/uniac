	<section>
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						Alunos
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-5">
								{{ BForm::select('turma', 'Turma', array('' => 'Carregando...'), null, array('tabindex' => 2, 'required')) }}
							</div>
							<div class="col-md-7">
								{{  BForm::formGroup('matriculas', 'Aluno',
										'<div class="input-group">' .
											Form::select('selectaluno', array('' => '(Selecione para adicionar)'), null, array('class' => 'form-control', 'id' => 'selectaluno', 'tabindex' => 2)) .
										'	<span class="input-group-btn">' .
										'    <button type="button" class="btn btn-default dropdown-toggle un-aluno-s-menu" data-toggle="dropdown" tabindex="2">' .
										'    	<span class="caret"></span>' .
										'    </button>' .
										'    <ul class="dropdown-menu pull-right" role="menu">' .
										'	    <li><a id="adicionartodos">Adicionar Todos</a></li>' .
										'	</ul>' .
										'	</span>' .
										'</div>'
									);
								}}
							</div>
						</div>
						<div class="row un-alunos">
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

@section('script')

@parent

<script>

	var Atividade = Atividade || {};

	Atividade.SelecaoAlunos = function() {
		var oPublic = {};

		var carregarAlunos = function() {
			var turma = $('#turma').val(),
				$select = $('select[name=selectaluno]');
			
			$select.uniacLoadSelect({ 
				url: '{{ url("atividades/alunos") }}', 
				valueField: 'codigo', 
				textField: 'nome',
				data: { turma: turma },
				placeholder: '(Selecione para adicionar)'
			});
		}

		var carregarTurmas = function() {
			$('#turma').uniacLoadSelect({ 
				url: '{{ url("atividades/turmas") }}', 
				valueField: 'turma_codigo', 
				textField: 'turma_nome', 
				groupField: 'curso_nome',
				selected: '{{ Input::old("turma") }}',
				done: function() {
					carregarAlunos();
					controlarStatusInputPorTurma();
				}
			});
		}

		var adicionarAluno = function(codigo, nome) {
			if (!codigo)
				return;

			if ($('.un-alunos :input[value=' + codigo + ']').length)
				return;

			var id = generateId();

			var $alunoRow = $(
				'<div class="col-xs-12 col-md-6 un-aluno-container" style="display: none">' +
				'	<div class="un-aluno">' +
				'		<input type="hidden" name="matriculas[' + id + '][codigo]" value="' + codigo + '"/>' +
				'		<input type="hidden" name="matriculas[' + id + '][nome]" value="' + nome + '"/>' +
				'		<div class="row">' +
				'			<div class="un-aluno-nome col-xs-10 col-md-10">' +
				'           	' + nome + 
				'			</div>' +
				'			<div class="col-xs-2 col-md-2">' +
				'		        <button class="btn btn-danger btn-xs pull-right excluiraluno" type="button">' +
				'		            ×' +
				'		        </button>' +
				'		    </div>' +
				'		</div>' +
				'	</div>' +
				'</div>'
			);

			$('.un-alunos').append($alunoRow);
			$alunoRow.fadeIn('fast');
		}

		var controlarStatusInputPorTurma = function() {
			var $turma = $('#turma');

			if (!$turma.val()) {
				$('.un-aluno-s-menu, #selectaluno, #selecionarTipoAtividade').attr('disabled', true);
			}
			else {
				$('.un-aluno-s-menu, #selectaluno, #selecionarTipoAtividade').attr('disabled', false);
			}
		}

		var onChangeTurma = function() {
			$('.un-alunos').html('');

			$('input[name=tipo_atividade_codigo]').val('');
			$('input[name=tipo_atividade_descricao]').val('');

			carregarAlunos();

			controlarStatusInputPorTurma();
		}

		var onChangeAluno = function() {
			var $select = $('#selectaluno'),
				$selected = $('#selectaluno :selected');
			adicionarAluno($selected.val(), $selected.text());
			$select.val('');
		}

		var onClickAdicionarTodos = function() {
			var $select = $('#selectaluno'),
				options = $('#selectaluno option');

			$.each(options, function(index, option) {
				var $option = $(option);

				adicionarAluno($option.val(), $option.text());
			});

			$select.val('');
		}

		var onClickExcluirAluno = function() {
			var $item = $(this).closest('.un-aluno-container');

			$item.fadeOut('fast', function() {
				$item.remove();
			});
		}

		var initMatriculas = function(matriculas) {
			if (matriculas)
				$.each(matriculas, function(index, item) {
					adicionarAluno(item.codigo, item.nome);
				});
		}

		oPublic.init = function(matriculas) {
			controlarStatusInputPorTurma();

			carregarTurmas();

			initMatriculas(matriculas);

			$('#turma').on('change', onChangeTurma);

			$('#selectaluno').on('change', onChangeAluno);

			$('#adicionartodos').on('click', onClickAdicionarTodos);

			$('.un-alunos').on('click', '.excluiraluno', onClickExcluirAluno);
		}		

		return oPublic;
	}();

	$(function(){
		var matriculas = {{ json_encode(Input::old('matriculas', array())) }};

		Atividade.SelecaoAlunos.init(matriculas);
	});

</script>
@stop