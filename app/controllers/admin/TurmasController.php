<?php namespace Controllers\Admin;

use BaseController;
use View;
use Response;
use Redirect;
use Input;
use Hash;
use Validator;
use Turma;
use DB;
use Session;
use Curso;
use TipoAtividade;
use AnexoControlador;
use Auth;
use MatrizCurricular;
use Usuario;
use Matricula;

class TurmasController extends BaseController {

	public function getIndex()
	{
    	return View::make('admin/turmas/index');
	}

	public function getListar()
	{
		$input = Input::all();

		$query = Turma::selectToGrid('turma.codigo', 'turma.nome', 'turma.ativa', 'curso.nome', 'campus.nome')
						  ->join('curso', 'curso.codigo', '=', 'turma.curso_codigo')
						  ->join('campus', 'campus.codigo', '=', 'curso.campus_codigo');

		if (Auth::user()->coordenador)
			$query->whereIn('curso.codigo', Auth::user()->cursos()->lists('curso_codigo'));

		$resultado = $query->toGrid($input);

		return $resultado;
	}

	public function getInserir()
	{
		$cursos = $this->obterCursosComCampus();

		return View::make('admin/turmas/turma')
					->with('titulo', 'Inserindo Turma')
					->with('readonly', FALSE)
					->with('cursos', $cursos);
	}

	public function postInserir()
	{
		$validator = Validator::make(Input::all(), Turma::obterRegrasDeValidacao());

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);
		
		Auth::user()->testarPermissaoParaCurso(Input::get('curso_codigo'));
				
		DB::transaction(function()
		{
			$turma = new Turma;
			$turma->ativa = Input::get('ativa', 0);
			$turma->nome = Input::get('nome');
			$turma->curso_codigo = Input::get('curso_codigo');			
			$turma->save();

			TurmasController::salvarMatriculas($turma);
		});

		return Redirect::to('turmas')->with('success', 'Turma inserida com sucesso.');
	}

	public function getEditar(Turma $turma)
	{
		Auth::user()->testarPermissaoParaCurso($turma->curso_codigo);

		$cursos = $this->obterCursosComCampus();

		return View::make('admin/turmas/turma')
					->with('titulo', 'Editando Turma')
					->with('model', $turma)
					->with('readonly', FALSE)
					->with('cursos', $cursos)
					->with('matriculas', $this->obterMatriculasParaGrid($turma));
	}

	public function postEditar(Turma $turma)
	{
		Auth::user()->testarPermissaoParaCurso($turma->curso_codigo);

		$validator = Validator::make(Input::all(), Turma::obterRegrasDeValidacao($turma->codigo));

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);
		
		DB::transaction(function() use(&$turma)
		{
			$turma->ativa = Input::get('ativa', 0);
			$turma->nome = Input::get('nome');
			$turma->curso_codigo = Input::get('curso_codigo');
			
			Auth::user()->testarPermissaoParaCurso($turma->curso_codigo);

			$turma->save();

			TurmasController::salvarMatriculas($turma);
		});

		return Redirect::to('turmas')->with('success', 'Turma editada com sucesso.');
	}

	public function getVisualizar(Turma $turma)
	{
		Auth::user()->testarPermissaoParaCurso($turma->curso_codigo);

		return View::make('admin/turmas/turma')
					->with('titulo', 'Visualizando Turma')
					->with('model', $turma)
					->with('readonly', TRUE)
					->with('matriculas', $this->obterMatriculasParaGrid($turma));
	}

	public function postExcluir(Turma $turma)
	{
		Auth::user()->testarPermissaoParaCurso($turma->curso_codigo);

		$turma->delete();
	}

	/**
	* Retorna alunos para o combobox na lista de matrículas. 
	* Pesquisa por nome (primeiro_nome + sobrenome) e por RA (login)
	*
	* Recebe:
	*		int $maxRows Quantidade máxima de registros retornados
	*		string $search Texto pesquisado
	*/
	public function getAlunos()
	{
		$pesquisa = Input::get('search', '');
		$maxRows = Input::get('maxRows', 100);

		$query = Usuario::select('codigo', 'primeiro_nome', 'sobrenome', 'login')
						->where('perfil', Usuario::ALUNO)
						->take($maxRows);

		if (empty($pesquisa) === false)
		{
			$query->where(function($query) use($pesquisa)
					{
						$query->whereRaw('CONCAT(primeiro_nome, \' \', sobrenome) LIKE ?' , array($pesquisa . '%'))
							  ->orWhereRaw('login = ?' , array($pesquisa));
					});
		}

		return $query->get()->toJson();
	}
	
	/**
	* Retorna lista de matrizes curriculares de um curso para a lista de matrículas.
	*
	* Recebe: 
	* 		int $curso Código do curso da turma
	*/
	public function getMatrizesCurriculares()
	{
		$curso = Input::get('curso', 0);

		return MatrizCurricular::select('codigo', 'nome', 'horas')
							  ->where('curso_codigo', $curso)
							  ->orderBy('codigo', 'desc')
							  ->get()
							  ->toJson();
	}

	/**
	* Salva as matrículas.
	* 
	* Cada matrículana na lista possui propriedades de controle
	* que não são envidadas para o banco de dados:
	* 		inserido: indica que a matrícula foi inserida
	* @param Curso $curso
	*/
	public static function salvarMatriculas(Turma $turma)
	{
		$matriculas = json_decode(Input::get('matriculas', '[]'));

		$codigos = array();

		foreach ($matriculas as $matricula) {
			$model;

			if (isset($matricula->inserido) && $matricula->inserido) {
				$model = new Matricula();		
				// Não permite editar usuário e matriz curricular		
				$model->usuario_codigo = $matricula->usuario_codigo;
				$model->matriz_curricular_codigo = $matricula->matriz_curricular_codigo;
				$model->saldo_anterior = isset($matricula->saldo_anterior) ? $matricula->saldo_anterior : 0;
				$model->horas_aceitas = $model->saldo_anterior;
			}
			else {
				$model = Matricula::find($matricula->codigo);
				$model->saldo_anterior = isset($matricula->saldo_anterior) ? $matricula->saldo_anterior : 0;
				$model->horas_aceitas = $model->processarHoras();

				if ((int)$model->status !== (int)$matricula->status) {
					// Matrículas Homologadas não podem ter o status alterado
					if ((int)$model->status === Matricula::HOMOLOGADO)
						throw new Exception("Não é possível alterar o status de uma matrícula já homologada", 1);

					if ((int)$matricula->status === Matricula::HOMOLOGADO && (int)$model->horas_faltando != 0)
						throw new Exception("A matrícula possui horas pendente, não é possível homologar", 1);

					if ((int)$matricula->status === Matricula::HOMOLOGADO && (int)$model->status != Matricula::ATIVO)
						throw new Exception("Apenas é possível homologar matrículas com status Ativo", 1);
				}
			}	
			
			$model->status = $matricula->status;

			$turma->matriculas()->save($model);
			$codigos[] = $model->codigo;
		}

		if (count($codigos))
			$turma->matriculas()->whereNotIn('codigo', $codigos)->delete();
	}

	/**
	* Retorna as matrículas no formato da grid.
	*/
	private function obterMatriculasParaGrid(Turma $turma)
	{
		$matriculas = DB::table('matricula')
					->select(DB::raw("matricula.codigo, usuario_codigo, CONCAT(RTRIM(CONCAT(usuario.primeiro_nome, ' ', usuario.sobrenome)), ', ', usuario.login) as usuario_nome, matriz_curricular_codigo, CONCAT(matriz_curricular.nome, ' (', matriz_curricular.horas, ' horas)') as matriz_curricular_nome, status, saldo_anterior,
							if(matricula.horas_aceitas >= matriz_curricular.horas, 1, 0) permite_homologar"))
					->join('usuario', 'usuario.codigo', '=', 'usuario_codigo')
					->join('matriz_curricular', 'matriz_curricular.codigo', '=', 'matriz_curricular_codigo')
					->where('turma_codigo', $turma->codigo)
					->get();

		$matriculas_json = json_encode($matriculas);

		return $matriculas_json;
	}

	private function obterCursosComCampus()
	{		
		$query = Curso::with('campus')->orderBy('nome');

		if (Auth::user()->coordenador)
			$query->whereIn('curso.codigo', Auth::user()->cursos()->lists('curso_codigo'));

		return $query->get()->toArray();
	}
}
