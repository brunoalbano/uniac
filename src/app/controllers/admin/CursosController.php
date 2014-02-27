<?php namespace Controllers\Admin;

use BaseController;
use View;
use Response;
use Redirect;
use Input;
use Hash;
use Validator;
use Curso;
use DB;
use Session;
use Campus;
use TipoAtividade;
use AnexoControlador;
use Auth;
use MatrizCurricular;
use App;

class CursosController extends BaseController {
	
	/**
	* Controlador de anexos
	* @var AnexoControlador
	*/
	private $anexos;

	public function __construct(AnexoControlador $anexos)
	{
		parent::__construct();
		
		$this->anexos = $anexos;
		$this->anexos->definirEscopo('cursos');
	}

	public function getIndex()
	{
    	return View::make('admin/cursos/index');
	}

	public function getListar()
	{
		$input = Input::all();

		$query = Curso::selectToGrid('curso.codigo', 'curso.nome', 'campus.nome')
						  ->join('campus', 'campus.codigo', '=', 'curso.campus_codigo');

		if (Auth::user()->coordenador)
			$query->whereIn('curso.codigo', Auth::user()->cursos()->lists('curso_codigo'));

		$resultado = $query->toGrid($input);

		return $resultado;
	}

	public function getInserir()
	{
		$campi = Campus::all()->toArray();

		$tipos_de_atividade_exemplo = array(/*
            array('codigo' => 'a', 'descricao' => '1. Exemplo A', 'inserido' => true),
            array('codigo' => 'b', 'descricao' => '2. Exemplo B', 'inserido' => true),
            array('codigo' => 'c', 'descricao' => '2.1. Exemplo C', 'tipo_atividade_codigo' => 'b', 'inserido' => true),
            array('codigo' => 'd', 'descricao' => '2.2. Exemplo D', 'tipo_atividade_codigo' => 'b', 'inserido' => true)*/
        );

		// É necessário informar o código para o controle de anexos
		$codigo = Input::old('codigo', uniqid(Auth::user()->codigo));

		return View::make('admin/cursos/curso')
					->with('titulo', 'Inserindo Curso')
					->with('codigo', $codigo)
					->with('readonly', FALSE)
					->with('campi', $campi)
					->with('tipos_de_atividade', json_encode($tipos_de_atividade_exemplo));
	}

	public function postInserir()
	{
		$validator = Validator::make(Input::all(), Curso::obterRegrasDeValidacao());

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);
				
		$anexosControlador = $this->anexos;
		DB::transaction(function() use($anexosControlador)
		{
			$curso = new Curso;
			$curso->nome = Input::get('nome');
			$curso->campus_codigo = Input::get('campus_codigo');			
			$curso->save();

			CursosController::salvarTiposDeAtividade($curso);

			CursosController::salvarAnexos($curso, $anexosControlador);

			CursosController::salvarMatrizesCurriculares($curso);
		});

		return Redirect::to('cursos')->with('success', 'Curso inserido com sucesso.');
	}

	public function getEditar(Curso $curso)
	{
		Auth::user()->testarPermissaoParaCurso($curso);

		$campi = Campus::all()->toArray();

		$this->anexos->inicializar($curso->codigo, $curso->anexos()->get());

		return View::make('admin/cursos/curso')
					->with('titulo', 'Editando Curso')
					->with('model', $curso)
					->with('readonly', FALSE)
					->with('campi', $campi)
					->with('tipos_de_atividade', $this->obterTiposParaArvore($curso))
					->with('matrizes_curriculares', $this->obterMatrizerParaGrade($curso));
	}

	public function postEditar(Curso $curso)
	{
		Auth::user()->testarPermissaoParaCurso($curso);

		$validator = Validator::make(Input::all(), Curso::obterRegrasDeValidacao($curso->codigo));

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);
		
		$anexosControlador = $this->anexos;
		DB::transaction(function() use(&$curso, $anexosControlador)
		{
			$curso->nome = Input::get('nome');
			$curso->campus_codigo = Input::get('campus_codigo');			
			$curso->save();

			CursosController::salvarTiposDeAtividade($curso);

			CursosController::salvarAnexos($curso, $anexosControlador);

			CursosController::salvarMatrizesCurriculares($curso);
		});

		return Redirect::to('cursos')->with('success', 'Curso editado com sucesso.');
	}

	public function getVisualizar(Curso $curso)
	{
		Auth::user()->testarPermissaoParaCurso($curso);

		$this->anexos->inicializar($curso->codigo, $curso->anexos()->get());

		return View::make('admin/cursos/curso')
					->with('titulo', 'Visualizando Curso')
					->with('model', $curso)
					->with('readonly', TRUE)
					->with('tipos_de_atividade', $this->obterTiposParaArvore($curso))
					->with('matrizes_curriculares', $this->obterMatrizerParaGrade($curso));
	}

	public function postExcluir(Curso $curso)
	{
		$curso->delete();
	}

	/**
	* Retorna uma lista de Tipos de atividade no formato necessário 
	* para a árvore de tipos na view. Retorna em JSON.
	* @param Curso $curso
	* @return string
	*/
	private function obterTiposParaArvore(Curso $curso)
	{
		return $curso->tipos_de_atividade()->orderBy('nivel')->get()->toJson();
	}

	/**
	* Retorna uma lista de Matriz curricular no formato necessário 
	* para a grade de matriz na view. Retorna em JSON.
	* @param Curso $curso
	* @return string
	*/
	private function obterMatrizerParaGrade(Curso $curso)
	{
		return $curso->matrizes_curriculares()->orderBy('nome')->get()->toJson();
	}

	/**
	* Retorna os tipos de atividade que são filhos diretos de 
	* um determinado tipo
	* @param Array $tipos Lista de tipos de atividade
	* @param string|int $pai_codigo Código do tipo de atividade pai
	*/
	public static function filtrarFilhos(Array $tipos, $pai_codigo)
	{
		return array_filter($tipos, 
					function($tipo) use($pai_codigo) 
					{
						return 
							((int)$pai_codigo) <= 0 && empty($tipo->tipo_atividade_codigo)
							||
							isset($tipo->tipo_atividade_codigo) && $tipo->tipo_atividade_codigo == $pai_codigo;
					});	
	}

	/**
	* Salva os tipos de atividade.
	*
	* Na view cada tipo de atividade possui algumas propriedades 
	* de controle que não são enviadas para o banco de dados:
	*		inserido: indica que o tipo de atividade foi inserido
	*		editado: indica que o tipo de atividade foi editado
	*		removidos: lista de tipos de atividade filhos que foram removidos
	* @param Curso $curso
	* @param Array $tipos Lista de Tipos de atividade
	* @param string|int %pai_codigo_view Código do tipo pai na view
	* @param int $pai_codigo_banco Código do tipo pai no banco de dados
	*/
	public static function salvarTiposDeAtividade(Curso $curso, Array $tipos = null, $pai_codigo_view = -1, $tipo_pai = null)
	{
		if (is_null($tipos)) // Obtem todos os tipos de ativiade
			$tipos = json_decode(Input::get('tipos_de_atividade', '[]'));

		// Filtra os tipos de atividade do pai atual. 
		// A raiz (primeiro item) não é salva e o seu código é -1.
		$tipos_filhos = self::filtrarFilhos($tipos, $pai_codigo_view);

		$nivel_atual = 0;

		foreach ($tipos_filhos as $tipo_filho)
		{
			$tipo = null;

			$get = function($key, $default = null) use($tipo_filho) {
				return isset($tipo_filho->$key) ? $tipo_filho->$key : $default;
			};
			
			if ($get('codigo') != TipoAtividade::RAIZ_CODIGO) {
				$nivel = '';

				if (empty($tipo_pai) === false)
					$nivel = $tipo_pai->nivel . '.';
				
				$nivel .= str_pad(++$nivel_atual, 3, '0', STR_PAD_LEFT);

				if ($get('inserido'))
					$tipo = new TipoAtividade();
				else 
					if ($get('editado') || $get('nivel') !== $nivel)
						$tipo = TipoAtividade::find($tipo_filho->codigo);

				if (empty($tipo) === false)
				{
					$tipo->descricao = $get('descricao', '');
					$tipo->horas = $get('horas', 0);
					$tipo->visivel_para_aluno = $get('visivel_para_aluno', 0);
					$tipo->ativo = $get('ativo', 0);
					$tipo->nivel = $nivel;

					// Para manter a integridade dos tipos de atividade não é
					// possível editar um tipo já salvo e marca-lo como obrigatório.
					// Também não é permitido indicar subtipos como obrigatórios.
					if (empty($tipo->codigo) && empty($tipo_pai))
						$tipo->obrigatorio = $get('obrigatorio', 0);

					$tipo->curso()->associate($curso);

					if (empty($tipo_pai) === false)
						$tipo->tipo_atividade_codigo = $tipo_pai->codigo;
					else
						$tipo->tipo_atividade_codigo = null;

					$tipo->save();

					if (empty($tipo->obrigatorio))
						self::salvarTiposDeAtividade($curso, $tipos, $tipo_filho->codigo, $tipo);
				}
				else
					self::salvarTiposDeAtividade($curso, $tipos, $tipo_filho->codigo, $tipo_filho);
			}

			if (isset($tipo_filho->removidos))
				TipoAtividade::destroy($tipo_filho->removidos);
		}
	}

	/**
	* Salva os anexos
	* @param Curso $curso
	*/
	public static function salvarAnexos(Curso $curso, $anexosControlador)
	{		
		$anexos = $anexosControlador->confirmarAlteracoes(Input::get('codigo'));

		$curso->anexos()->sync($anexos);
		$curso->save();	
	}

	/**
	* Salva as matrizes curriculares.
	* 
	* Cada matriz curricular na view possui propriedades de controle
	* que não são envidadas para o banco de dados:
	* 		inserido: indica que o tipo de atividade foi inserido
	* @param Curso $curso
	*/
	public static function salvarMatrizesCurriculares(Curso $curso)
	{
		$matrizes_input = Input::get('matrizes_curriculares', '[]');
		$matrizes = json_decode($matrizes_input);

		$codigos = array();

		foreach ($matrizes as $matriz) {
			if (isset($matriz->inserido) && $matriz->inserido)
				$model = new MatrizCurricular();
			else
				$model = MatrizCurricular::find($matriz->codigo);
			
			$model->nome = $matriz->nome;
			$model->horas = $matriz->horas;

			$curso->matrizes_curriculares()->save($model);
			$codigos[] = $model->codigo;
		}

		if (count($codigos))
			$curso->matrizes_curriculares()->whereNotIn('codigo', $codigos)->delete();
		else
			$curso->matrizes_curriculares()->delete();
	}

	public function postEnviarAnexo($curso_codigo)
	{
		return $this->anexos->upload($curso_codigo);
	}

	public function postExcluirAnexo($curso_codigo, $anexo_codigo)
	{
		return $this->anexos->excluir($curso_codigo, $anexo_codigo);
	}

	public function getDownloadAnexo($curso_codigo, $anexo_codigo)
	{
		Auth::user()->testarPermissaoParaCurso($curso_codigo);

		return $this->anexos->download($curso_codigo, $anexo_codigo);
	}

	public function getListarAnexos($curso_codigo)
	{
		Auth::user()->testarPermissaoParaCurso($curso_codigo);

		return $this->anexos->listar($curso_codigo);	
	}

	public function getTipoDeAtividadeUsado($tipo_atividade_codigo)
	{
		$tipo = TipoAtividade::find($tipo_atividade_codigo);
		$nivel = $tipo->nivel;
		$curso_codigo = $tipo->curso_codigo;

		$usado = DB::table('atividade')
					->select(DB::raw('1'))
					->join('tipo_atividade', 'tipo_atividade.codigo', '=', 'atividade.tipo_atividade_codigo')
					->whereRaw("(nivel = '$nivel' OR nivel LIKE '$nivel.%') AND curso_codigo = $curso_codigo ")
					->count() > 0;

		return array('usado' => $usado);
	}
}
