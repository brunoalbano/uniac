<?php namespace Controllers\Admin;

use BaseController;
use View;
use Response;
use Redirect;
use Usuario;
use Input;
use Hash;
use Validator;
use Curso;
use DB;
use Session;
use Auth;
use App;
use Password;
use Turma;
use MatrizCurricular;
use Matricula;

class UsuariosController extends BaseController {

	public function getIndex()
	{		
    	return View::make('admin/usuarios/index');
	}

	public function getListar()
	{
		$input = Input::all();

		$query = Usuario::selectToGrid('codigo', 'primeiro_nome', 'sobrenome', 'perfil', 'login', 'email', 'acesso_liberado');

		if (Auth::user()->coordenador)
			$query->where('perfil', Usuario::ALUNO);

		$resultado = $query->toGrid($input);

		return $resultado;
	}

	public function getInserir()
	{
		return View::make('admin/usuarios/usuario')
					->with('titulo', 'Inserindo Usuário')
					->with('readonly', FALSE);
	}

	public function postInserir()
	{
		$validator = Validator::make(Input::all(), Usuario::obterRegrasDeValidacaoAoInserir());

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);

		$usuario = new Usuario;

		$usuario->perfil			= Input::get('perfil');
		$usuario->primeiro_nome		= Input::get('primeiro_nome');
		$usuario->sobrenome			= Input::get('sobrenome');
		$usuario->login				= trim(Input::get('login'));
		$usuario->email				= Input::get('email');
		$usuario->senha				= Hash::make(Input::get('senha'));
		$usuario->acesso_liberado	= Input::get('acesso_liberado', 0);
		$usuario->notificar			= Input::get('notificar', 0);

		DB::transaction(function() use ($usuario)
		{
			$usuario->save();

			$cursos = UsuariosController::obterCursosParaSalvar();
			$usuario->cursos()->sync($cursos);
			$usuario->save();
		});		

		return Redirect::to('usuarios')->with('success', 'Usuário inserido com sucesso.');
	}

	public function getEditar(Usuario $usuario)
	{
		$cursos = $this->obterCursosParaGrid($usuario);

		Session::flash('cursos', $cursos);

		return View::make('admin/usuarios/usuario')
					->with('titulo', 'Editando Usuário')
					->with('model', $usuario)
					->with('readonly', FALSE);
	}

	public function postEditar(Usuario $usuario)
	{
		$validator = Validator::make(Input::all(), Usuario::obterRegrasDeValidacaoAoEditar($usuario->codigo));

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);

		$usuario->perfil			= Input::get('perfil');
		$usuario->primeiro_nome		= Input::get('primeiro_nome');
		$usuario->sobrenome			= Input::get('sobrenome');
		$usuario->login				= Input::get('login');
		$usuario->email				= Input::get('email');
		$usuario->acesso_liberado	= Input::get('acesso_liberado', 0);
		$usuario->notificar			= Input::get('notificar', 0);

		DB::transaction(function() use ($usuario)
		{
			$cursos = UsuariosController::obterCursosParaSalvar();
			$usuario->cursos()->sync($cursos);
			$usuario->save();
		});	

		return Redirect::to('usuarios')->with('success', 'Usuário editado com sucesso.');
	}

	public function getVisualizar(Usuario $usuario)
	{
		$cursos = $this->obterCursosParaGrid($usuario);

		Session::flash('cursos', $cursos);
		
		return View::make('admin/usuarios/usuario')
					->with('titulo', 'Visualizando Usuário')
					->with('model', $usuario)
					->with('readonly', TRUE);
	}

	public function postExcluir(Usuario $usuario)
	{
		$usuario->delete();
	}

	public function getResetarSenha($usuario)
	{
		$credentials = array('email' => $usuario->email);

    	Password::remind($credentials, function($message, $user)
		{
		    $message->subject('[UNIAC] Redefinir senha');
		});

    	if (Session::has('error'))
    		return Redirect::to('usuarios')->with('error', 'Falha ao enviar e-mail de redefinição de senha.');
    	else
			return Redirect::to('usuarios')->with('success', 'E-mail de redefinição de senha enviado com sucesso.');
	}

	private function obterCursosParaGrid(Usuario $usuario)
	{
		$resultado = array();

		$usuario->cursos->each(function($curso) use(&$resultado){
			$c = array('coordenador' => $curso->pivot->coordenador, 'codigo' => $curso->codigo);
			$resultado[] = $c;
		});		

		return json_encode($resultado);
	}

	public static function obterCursosParaSalvar()
	{
		$cursos = json_decode(Input::get('cursos', '[]'));

		$resultado = array();

		if (is_array($cursos))
			foreach ($cursos as $curso) 
			{
				if (isset($curso->codigo) && $curso->codigo != 0)
				{
					$coordenador = isset($curso->coordenador) && (int)$curso->coordenador === 1;

					$resultado[$curso->codigo] =array('coordenador' => $coordenador);
				}
			}

		return $resultado;
	}

	public function getImportar($resultadoValidacao = null)
	{
		return View::make('admin/usuarios/importar')
					->with('titulo', 'Importando Alunos')
					->with('resultadoValidacao', $resultadoValidacao);
	}

	public function postImportar()
	{
		$validacaoArquivo = array(
			'arquivo' => 'required',
			'matriz_curricular' => 'required_with:turma'
		);

		$validator = Validator::make(Input::all(), $validacaoArquivo);

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);

		$resultadoValidacao = array();

		DB::transaction(function() use(&$arquivo, &$resultadoValidacao)
		{
			$arquivo = fopen(Input::file('arquivo')->getRealPath(),'r');
			
			$regrasDeValicadaoInserir = array(
				'nome' 	              => 'required|max:150',
				'login'               => 'required|max:20|alpha_dash|unique:usuario,login',
				'email'               => 'required|max:150|email|unique:usuario,email',
				'acesso_liberado'     => 'in:0,1',
				'saldo_anterior'	  => 'integer'
			);

			$regrasDeValicadaoAlterar = array(
				'nome' 	              => 'required|max:150',
				'login'               => 'required|max:20|alpha_dash',
				'acesso_liberado'     => 'in:0,1',
			);

			$turma = Input::get('turma');
			$matriz_curricular = Input::get('matriz_curricular');

			$linhasCount = 0;
			while($linhaCsv = fgetcsv($arquivo, 0, ';')) {
				$linhasCount++;

				$nome = isset($linhaCsv[0]) ? $linhaCsv[0] : '';
				$login = isset($linhaCsv[1]) ? $linhaCsv[1] : '';
				$email = isset($linhaCsv[2]) ? $linhaCsv[2] : '';
				$acesso_liberado = isset($linhaCsv[3]) ? $linhaCsv[3] : '';
				$saldo_anterior = isset($linhaCsv[4]) ? $linhaCsv[4] : 0;

				$linha = array('nome' => $nome, 'login' => $login, 'email' => $email, 
					'acesso_liberado' => $acesso_liberado, 'saldo_anterior' => $saldo_anterior);

				$validator = null;

				$usuario = null;

				if (empty($login))
					$validator = Validator::make($linha, $regrasDeValicadaoInserir);
				else
				{
					$usuario = Usuario::where('login', $linha['login'])->get()->first();

					if (empty($usuario))
						$validator = Validator::make($linha, $regrasDeValicadaoInserir);	
					else
						$validator = Validator::make($linha, $regrasDeValicadaoAlterar);
				}
				
				if ($validator->fails())
					$resultadoValidacao[count($linhasCount)] = $validator->messages()->all();
				else
				{
					if (empty($usuario) === false)
					{
						if (empty($acesso_liberado) === false)
							$usuario->acesso_liberado = $acesso_liberado;
					}
					else
					{
						$usuario = new Usuario;

						$nome_parts = explode(' ', $nome);

						$usuario->primeiro_nome = array_shift($nome_parts);
						$usuario->sobrenome = implode(' ', $nome_parts);
						$usuario->email = $email;
						$usuario->login = $login;
						$usuario->acesso_liberado = $acesso_liberado;
						$usuario->senha = Hash::make(rand() . '_'); // concatenação de string para converter o valor aleatório para string
						$usuario->perfil = Usuario::ALUNO;
					}

					$usuario->save();

					if (empty($turma) === false)
						if (!Matricula::where('usuario_codigo', $usuario->codigo)->where('turma_codigo', $turma)->count())
						{
							$matricula = new Matricula;

							$matricula->usuario_codigo = $usuario->codigo;
							$matricula->turma_codigo = $turma;
							$matricula->matriz_curricular_codigo = $matriz_curricular;
							$matricula->saldo_anterior = $saldo_anterior;
							$matricula->horas_aceitas = $saldo_anterior;
							$matricula->status = Matricula::ATIVO;						

							$matricula->save();
						}
				}
			}
		
			fclose ($arquivo);
		});

		if (count($resultadoValidacao)){
			Session::flash('errorsMessages', $resultadoValidacao);

			return Redirect::back()->withInput();
		}

		return Redirect::to('usuarios')->with('success', 'Alunos importados com sucesso.');
	}

	public function getTurmas() 
	{
		return Turma::getForGroupedSelect();
	}
	
	/**
	* Retorna lista de matrizes curriculares de um curso para a lista de matrículas.
	*
	* Recebe: 
	* 		int $turma Código do curso da turma
	*/
	public function getMatrizesCurriculares()
	{
		$turma = Turma::find(Input::get('turma'));

		$curso = $turma->curso_codigo;

		return MatrizCurricular::select(DB::raw("codigo, CONCAT(nome, ' (', horas, ' horas)') as nome"))
							  ->where('curso_codigo', $curso)
							  ->orderBy('codigo', 'desc')
							  ->get()
							  ->toJson();
	}

	public function getExportar()
	{
		$alunos = Usuario::select('codigo', 'primeiro_nome', 'sobrenome', 'email', 'login')->where('perfil', Usuario::ALUNO)->get()->toArray();

		$resultado = '';

		foreach ($alunos as $aluno) {
			$resultado .= implode(';', $aluno) . '<br />';
		}

		return $resultado;
	}
}