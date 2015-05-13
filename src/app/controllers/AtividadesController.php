<?php namespace Controllers;

use BaseController;
use View;
use Response;
use Redirect;
use Validator;
use Input;
use Atividade;
use Auth;
use File;
use Session;
use AnexoControlador;
use DB;
use Comentario;
use MotivoRecusa;
use DateTime;
use Turma;
use App;
use Helpers;
use Matricula;
use TipoAtividade;
use MatrizCurricular;
use Mail;
use Curso;

class AtividadesController extends BaseController {

	private $anexos;

	public function __construct(AnexoControlador $anexos)
	{
		parent::__construct();

		$this->anexos = $anexos;
		$this->anexos->definirEscopo('atividades');
	}

	private function getDateFromStr($str)
	{
		if (empty($str))
			return null;

		$parts = explode('/', $str);

		if (count($parts) === 3) {
			if ((int)$parts[2] > 99)
				return DateTime::createFromFormat('d/m/Y', $str);
			else
				return DateTime::createFromFormat('d/m/y', $str);
		}
		else {
			$parts = explode('-', $str);

			if (count($parts) === 3)
				return DateTime::createFromFormat('Y-m-d', $str);
		}

		return null;
	}

	public function getIndex()
	{
		$status = (int)Input::get('status', 0);

		$pesquisa = Input::get('pesquisa');

		$page = Input::get('page', 1);

		$query = Atividade::orderBy('atualizado_em', 'desc');

		if (Auth::user()->aluno) {
			$query->where('matricula_codigo', Session::get('matricula_codigo', 0));
		}
		else {
			if (Auth::user()->supervisor) {
				$query
					->whereExists(function($query)
		            {
		                $query->select(DB::raw(1))
		                      ->from('matricula')
		                      ->join('turma', 'turma.codigo', '=', 'matricula.turma_codigo')
							  ->join('usuario_curso', 'usuario_curso.curso_codigo', '=', 'turma.curso_codigo')
		                      ->whereRaw('matricula.codigo = atividade.matricula_codigo')
		                      ->where('usuario_curso.usuario_codigo', Auth::user()->codigo);
		            });
			}
		}

		if ($status > 0)
			$query->where('status', $status);

		if (empty($pesquisa) === false) {

			$date = $this->getDateFromStr($pesquisa);

			if (empty($date) === false) {
				$date->setTime(0, 0, 0);
				$start = $date->format('c');

				$date->setTime(23, 59, 59);
				$end = $date->format('c');

				$query->whereRaw('atividade.atualizado_em BETWEEN ? AND ?', array($start, $end));
			}
			else
			{
				if (Auth::user()->aluno == false) {

					$query
						->where(function($query) use($pesquisa) {
							$query
								->orWhere('atividade.titulo', 'LIKE', '%' . $pesquisa . '%')
								->orWhereExists(function($query) use($pesquisa) {
					                $query->select(DB::raw(1))
					                      ->from('matricula')
										  ->join('usuario', 'usuario.codigo', '=', 'matricula.usuario_codigo')
					                      ->whereRaw('matricula.codigo = atividade.matricula_codigo')
					                      ->where('usuario.login', $pesquisa);
					            });
						});
				}
				else
					$query->where('atividade.titulo', 'LIKE', '%' . $pesquisa . '%');
			}
		}

		$atividades = $query->paginate(15);

    	return View::make('atividades/index')
    					->with('model', $atividades)
    					->with('status', $status)
    					->with('pesquisa', $pesquisa)
    					->with('page', $page);
	}

	public function getCriar()
	{
		$model = new Atividade;
		$model->codigo = Input::old('codigo', uniqid(Auth::user()->codigo));

    	return View::make('atividades/criar')->with('model', $model);
	}

	public function postCriar()
	{
		if (Auth::user()->convidado)
			App::abort(403);

		$validator = Validator::make(Input::all(), Atividade::obterRegrasDeValidacaoParaCriar(), array('matriculas.required' => 'Selecione ao menos um aluno.'));

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);

		$modelo = array(
			'titulo' => Input::get('titulo'),
			'horas_requisitadas' => Input::get('horas'),
			'tipo_atividade_codigo' => Input::get('tipo_atividade_codigo'),
			'descricao' => Input::get('descricao'),
			'justificativa' => Input::get('justificativa'),
			'usuario_resp_criacao_codigo' => Auth::user()->codigo
		);

		$tipo = TipoAtividade::find($modelo['tipo_atividade_codigo']);

		$matriculas = null;

		if (Auth::user()->aluno)
		{
			// Verifica permissão para o tipo de atividade
			if ($tipo->visivel_para_aluno == 0)
				return App::abort(403);

			$matricula = Session::get('matricula_codigo');
			$matriculas = array(array('codigo' => $matricula));
			$modelo['status'] = Atividade::AGUARDANDO_AVALIACAO;
		}
		else
		{
			// Verifica permissão para o tipo de atividade
			if (Auth::user()->supervisor && $tipo->ativo == 0)
				return App::abort(403);

			$matriculas = Input::get('matriculas');
			$modelo['status'] = Input::get('status');

			if ((int)$modelo['status'] === Atividade::RECUSADA)
				$modelo['horas_aceitas'] = 0;
			else
				$modelo['horas_aceitas'] = $modelo['horas_requisitadas'];

			$modelo['usuario_resp_avaliacao_codigo'] = Auth::user()->codigo;
			$modelo['atualizado_em'] = new DateTime('NOW');
		}

		$anexosControlador = $this->anexos;

		$alunos = array();

		DB::transaction(function() use ($modelo, $matriculas, $anexosControlador, &$alunos)
		{
			$atividadeCodigo = Input::get('codigo');
			$anexos = $anexosControlador->confirmarAlteracoes($atividadeCodigo);

			if ($anexos !== false) {

				foreach ($matriculas as $matricula) {

					if (Auth::user()->usuario === false)
						AtividadesController::_validarPermissaoDoUsuarioParaMatricula($matricula['codigo']);

					$atividade = new Atividade($modelo);
					$atividade->matricula_codigo = $matricula['codigo'];
					$atividade->save();

					$atividade->anexos()->sync($anexos);
					$atividade->save();

					if (Auth::user()->aluno == false) {
						$matricula = Matricula::find($atividade->matricula_codigo);
						$matricula->horas_aceitas = $matricula->processarHoras();
						$matricula->save();

						$alunos[] = $matricula->usuario()->first();
					}

					$comentario = new Comentario();
					$comentario->interno = 'Atividade criada';
					$comentario->comentario = '';
					$comentario->usuario()->associate(Auth::user());

					$atividade->comentarios()->save($comentario);
				}

			}
		});

		if (Auth::user()->aluno) {
			$matricula_codigo = Session::get('matricula_codigo');
			$matricula = Matricula::find($matricula_codigo);

			$this->enviarEmailNotificacaoParaSupervisoresDoCurso($matricula->turma()->first()->curso()->first());
		}
		else
			if (empty($alunos) === false)
				$this->enviarEmailNotificacaoParaAluno($alunos, $modelo['titulo']);

		$this->anexos->limpar(Input::get('codigo'));
		return Redirect::to('atividades')->with('success', 'Atividade criada com sucesso.');
	}

	private function validarPermissaoParaTurma($turma_codigo)
	{
		if (Auth::user()->administrador || Auth::user()->convidado)
			return;

		$turma = Turma::find($turma_codigo);

		$possuiPermissaoParaCurso = DB::table('usuario_curso')
										->where('usuario_codigo', Auth::user()->codigo)
										->where('curso_codigo', $turma->curso_codigo)
										->count() > 0;

		if (!$possuiPermissaoParaCurso)
			App::abort(403);
	}

	private function validarPermissaoDoUsuarioParaMatricula($matricula_codigo)
	{
		return AtividadesController::_validarPermissaoDoUsuarioParaMatricula($matricula_codigo);
	}

	public static function _validarPermissaoDoUsuarioParaMatricula($matricula_codigo)
	{
		if (Auth::user()->administrador || Auth::user()->convidado)
			return;

		if (Auth::user()->supervisor) {
			$qtde = DB::table('matricula')
					->join('turma', 'turma.codigo', '=', 'matricula.turma_codigo')
					->join('usuario_curso', 'usuario_curso.curso_codigo', '=', 'turma.curso_codigo')
					->where('matricula.codigo', $matricula_codigo)
					->where('usuario_curso.usuario_codigo', Auth::user()->codigo)
					->count();

			if (!$qtde)
				App::abort(403);
		}
		else {
			$matricula = Matricula::find($matricula_codigo);

			if ($matricula->usuario_codigo !== Auth::user()->codigo)
				App::abort(403);
		}
	}

	public function postEnviarAnexo($atividade_codigo)
	{
		return $this->anexos->upload($atividade_codigo);
	}

	public function postExcluirAnexo($atividade_codigo, $anexo_codigo)
	{
		return $this->anexos->excluir($atividade_codigo, $anexo_codigo);
	}

	public function getDownloadAnexo($atividade_codigo, $anexo_codigo)
	{
		return $this->anexos->download($atividade_codigo, $anexo_codigo);
	}

	public function getListarAnexos($atividade_codigo)
	{
		return $this->anexos->listar($atividade_codigo);
	}

	public function getVisualizar($atividade)
	{
		$status = (int)Input::get('status', 0);
		$pesquisa = Input::get('pesquisa');
		$page = Input::get('page', 1);

		$this->anexos->inicializar($atividade->codigo, $atividade->anexos()->get());

		return View::make('atividades/visualizar')
					->with('model', $atividade)
					->with('status', $status)
					->with('pesquisa', $pesquisa)
					->with('page', $page);
	}

	public function getEditar($atividade)
	{
		$status = (int)Input::get('status', 0);
		$pesquisa = Input::get('pesquisa');
		$page = Input::get('page', 1);

		$this->anexos->inicializar($atividade->codigo, $atividade->anexos()->get());

		return View::make('atividades/criar')
					->with('model', $atividade)
					->with('status', $status)
					->with('pesquisa', $pesquisa)
					->with('page', $page);
	}

	public function postEditar($atividade_codigo)
	{
		$status = (int)Input::get('status', 0);
		$pesquisa = Input::get('pesquisa');
		$page = Input::get('page', 1);

		$validator = Validator::make(Input::all(), Atividade::obterRegrasDeValidacaoParaCriar());

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);

		$atividade = Atividade::find($atividade_codigo);

		// Aluno só pode editar atividades aguardando correção
		if ($atividade->aguardando_correcao === false)
			App::abort(403);

		$this->validarPermissaoDoUsuarioParaMatricula($atividade->matricula_codigo);

		$atividade->titulo = Input::get('titulo');
		$atividade->horas_requisitadas = Input::get('horas');
		$atividade->tipo_atividade_codigo = Input::get('tipo_atividade_codigo');
		$atividade->descricao = Input::get('descricao');
		$atividade->justificativa = Input::get('justificativa');
		$atividade->status = Atividade::AGUARDANDO_AVALIACAO;

		$anexosControlador = $this->anexos;

		DB::transaction(function() use (&$atividade, $anexosControlador)
		{
			$atividade->save();

			$anexos = $anexosControlador->confirmarAlteracoes(Input::get('codigo'));

			if ($anexos !== false)
			{
				$atividade->anexos()->sync($anexos);
				$atividade->save();
			}

			$comentario = new Comentario();

			$comentario->interno = 'Atividade editada';
			$comentario->comentario = '';
			$comentario->usuario()->associate(Auth::user());

			$atividade->comentarios()->save($comentario);
		});

		$this->enviarEmailNotificacaoParaSupervisoresDoCurso($atividade->matricula()->first()->turma()->first()->curso()->first());

		$this->anexos->limpar(Input::get('codigo'));

		return Redirect::to('atividades/' . $atividade->codigo . $this->obterQueryString())->with('success', 'Atividade editada e enviada para avaliação.');
	}

	public function postResponder($atividade)
	{
		if (Auth::user()->convidado)
			App::abort(403);

		$this->validarPermissaoDoUsuarioParaMatricula($atividade->matricula_codigo);

		$mensagem = '';

		if (Auth::user()->aluno)
		{
			if ($atividade->aguardando_correcao)
			{
				$mensagem = 'Atividade enviada para avaliação';
				$atividade->status = Atividade::AGUARDANDO_AVALIACAO;

				$atividade->save();
			}

			$this->enviarEmailNotificacaoParaSupervisoresDoCurso($atividade->matricula()->first()->turma()->first()->curso()->first());
		}
		else
		{
			if ($atividade->aguardando_avaliacao)
			{
				$mensagem = 'Atividade devolvida para correção';
				$atividade->status = Atividade::AGUARDANDO_CORRECAO;

				$atividade->save();
			}

			$this->enviarEmailNotificacaoParaAluno($atividade->matricula()->first()->usuario()->first(), $atividade->titulo);
		}

		$comentario = new Comentario();

		$comentario->interno = $mensagem;
		$comentario->comentario = Input::get('comentario', '');
		$comentario->usuario()->associate(Auth::user());

		$atividade->comentarios()->save($comentario);

		return Redirect::to('atividades/' . $atividade->codigo . $this->obterQueryString())->with('success', $mensagem);
	}

	public function postAceitar($atividade)
	{
		$validator = Validator::make(Input::all(), Atividade::obterRegrasDeValidacaoParaAceitar());

		if ($validator->fails())
			return Redirect::to('atividades/' . $atividade->codigo . $this->obterQueryString())->withErrors($validator);

		$this->validarPermissaoDoUsuarioParaMatricula($atividade->matricula_codigo);

		$tipo_atividade_codigo = (int)Input::get('tipo_atividade_codigo');
		if (Auth::user()->supervisor && $tipo_atividade_codigo !== (int)$atividade->tipo_atividade_codigo) {
			$tipo = TipoAtividade::find($tipo_atividade_codigo);

			// Verifica permissão para o tipo de atividade
			if (Auth::user()->supervisor && $tipo->ativo == 0)
				return App::abort(403);
		}

		DB::transaction(function() use(&$atividade)
		{
			$tipo_atividade_codigo = (int)Input::get('tipo_atividade_codigo');

			$atividade->status = Atividade::ACEITA;
			$atividade->horas_aceitas = Input::get('horas');
			$atividade->tipo_atividade_codigo = $tipo_atividade_codigo;
			$atividade->usuario_resp_avaliacao_codigo = Auth::user()->codigo;
			$atividade->avaliado_em = new DateTime('NOW');
			$sucesso = $atividade->save();

			$comentario = new Comentario();

			$mensagem = 'Atividade aceita';

			if ($tipo_atividade_codigo !== (int)$atividade->tipo_atividade_codigo)
				$mensagem .= ". Tipo de atividade alterado pelo supervisor, valor antigo: " . $atividade->tipo_atividade->descricao;

			$comentario->interno = $mensagem;
			$comentario->comentario = Input::get('comentario', '');
			$comentario->usuario()->associate(Auth::user());

			$atividade->comentarios()->save($comentario);

			$matricula = Matricula::find($atividade->matricula_codigo);
			$matricula->horas_aceitas = $matricula->processarHoras();
			$matricula->save();
		});

		$this->enviarEmailNotificacaoParaAluno($atividade->matricula()->first()->usuario()->first(), $atividade->titulo);

		return Redirect::to('atividades/' . $atividade->codigo . $this->obterQueryString())->with('success', 'Atividade aceita com sucesso.');
	}

	public function getExcluir($atividade)
	{
		// Aluno não pode excluir atividades aceitas ou recusadas
		if ($atividade->aceita || $atividade->recusada)
			App::abort(403);

		$this->validarPermissaoDoUsuarioParaMatricula($atividade->matricula_codigo);

		$atividade->delete();

		return Redirect::to('atividades')->with('success', 'Atividade excluída com sucesso.');
	}

	public function getReabrir($atividade)
	{
		$permissao = (Auth::user()->supervisor || Auth::user()->administrador) && ($atividade->aceita || $atividade->recusada);

		// Aluno não pode excluir atividades aceitas ou recusadas
		if (!$permissao)
			App::abort(403);

		$this->validarPermissaoDoUsuarioParaMatricula($atividade->matricula_codigo);

		DB::transaction(function() use(&$atividade)
		{
			$tipo_atividade_codigo = (int)Input::get('tipo_atividade_codigo');

			$atividade->status = Atividade::AGUARDANDO_AVALIACAO;
			$atividade->horas_aceitas = 0;
			$atividade->usuario_resp_avaliacao_codigo = null;
			$atividade->motivo_recusa_codigo = null;
			$atividade->avaliado_em = null;
			$sucesso = $atividade->save();

			$comentario = new Comentario();

			$mensagem = 'Atividade reaberta';

			$comentario->interno = $mensagem;
			$comentario->usuario()->associate(Auth::user());

			$atividade->comentarios()->save($comentario);

			$matricula = Matricula::find($atividade->matricula_codigo);
			$matricula->horas_aceitas = $matricula->processarHoras();
			$matricula->save();
		});

		return Redirect::to('atividades/' . $atividade->codigo . $this->obterQueryString())->with('success', 'Atividade reaberta com sucesso.');
	}

	public function getMotivosRecusa()
	{
		return Response::json(MotivoRecusa::all());
	}

	public function postRecusar($atividade)
	{
		$this->validarPermissaoDoUsuarioParaMatricula($atividade->matricula_codigo);

		DB::transaction(function() use(&$atividade)
		{

			$atividade->status = Atividade::RECUSADA;
			$atividade->horas_aceitas = 0;
			$atividade->motivo_recusa_codigo = Input::get('motivo_recusa_codigo');
			$atividade->usuario_resp_avaliacao_codigo = Auth::user()->codigo;
			$atividade->avaliado_em = new DateTime('NOW');
			$sucesso = $atividade->save();

			$mensagem = "Atividade recusada. Motivo: " . MotivoRecusa::find($atividade->motivo_recusa_codigo)->nome;

			$comentario = new Comentario();

			$comentario->interno = $mensagem;
			$comentario->comentario = Input::get('comentario', '');
			$comentario->usuario()->associate(Auth::user());

			$atividade->comentarios()->save($comentario);
		});

		$this->enviarEmailNotificacaoParaAluno($atividade->matricula()->first()->usuario()->first(), $atividade->titulo);

		return Redirect::to('atividades/' . $atividade->codigo . $this->obterQueryString())->with('success', 'Atividade recusada com sucesso.');
	}

	public function getTurmas()
	{
		return Turma::getForGroupedSelect();
	}

	public function getTurmasCoordenador()
	{
		return Turma::getForGroupedSelectForCoordenador();
	}

	public function getAlunos()
	{
		$turma_codigo = Input::get('turma');

		$this->validarPermissaoParaTurma($turma_codigo);

		$alunos = DB::table('matricula')
					->select(DB::raw("matricula.codigo as codigo, CONCAT(primeiro_nome, ' ', sobrenome, ', ', login) as nome"))
					->join('usuario', 'usuario.codigo', '=', 'matricula.usuario_codigo')
					->where('matricula.turma_codigo', $turma_codigo)
					->get();

		return $alunos;
	}

	public function obterQueryString()
	{
		$status = (int)Input::get('status', 0);

		$pesquisa = Input::get('pesquisa');

		$page = Input::get('page', 1);

		return '?status=' . $status . '&pesquisa=' . $pesquisa . '&page=' . $page;
	}

	public function getListarArvore()
	{
		$tipoatividade = Input::get('tipoatividade');
		$curso_codigo = 0;

		if (Auth::user()->aluno) {
			$matricula = Matricula::find(Session::get('matricula_codigo'));

			$turma = Turma::find($matricula->turma_codigo);
		}
		else {
			$turma_codigo = Input::get('turma');

			$turma = Turma::find($turma_codigo);
		}

		$curso_codigo = $turma->curso_codigo;

		$query = TipoAtividade
						::select(DB::raw('codigo, descricao, horas, nivel,
							EXISTS (SELECT 1 FROM tipo_atividade as item WHERE item.tipo_atividade_codigo = tipo_atividade.codigo) as possuiItens'))
						->where('tipo_atividade_codigo', $tipoatividade)
						->where('curso_codigo', $curso_codigo);

		if (Auth::user()->aluno)
			$query->where('visivel_para_aluno', 1);
		elseif (Auth::user()->supervisor)
			$query->where('ativo', 1);

		$tipos = $query->orderBy('nivel')
			  		   ->get();

		$resultado = array();

		foreach ($tipos as $tipo) {
			$item = array(
				'codigo' => $tipo->codigo,
				'titulo' => $tipo->descricao,
				'possuiItens' => (int)$tipo->possuiItens === 1,
				'horas' => $tipo->horas
				);
			$resultado[] = $item;
		}

		return Response::json($resultado);
	}

	public function getRelatorioAluno($matricula_codigo)
	{		
		$this->validarPermissaoDoUsuarioParaMatricula($matricula_codigo);

		$matricula = Matricula::find($matricula_codigo);

		$tiposProcessados = $matricula->processarTipos();

		$turma = Turma::find($matricula->turma_codigo);

		$tipos = DB::table('tipo_atividade')
					->select(DB::raw('codigo, descricao, horas, nivel, obrigatorio, ' .
						'EXISTS (SELECT 1 FROM tipo_atividade as item WHERE item.tipo_atividade_codigo = tipo_atividade.codigo) as possui_itens'))
					->where('curso_codigo', $turma->curso_codigo)
					->orderBy('nivel')
					->get();

		$horas_necessarias = (int)$matricula->horas_necessarias;
		$horas_aceitas = (int)$matricula->horas_aceitas;
		$horas_faltando = (int)$matricula->horas_faltando;
		$horas_necessarias_normais = (int)$matricula->horas_necessarias_normais;
		$horas_aceitas_normais = 0;
		$horas_faltando_normais = 0;
		$tipos_obrigatorios = array();
		$tipos_normais = array();
		$saldo_anterior = (int)$matricula->saldo_anterior;

		$horas_aceitas_obrigatorias = 0;

		foreach ($tipos as $tipo) {
			$tipo->horas_aceitas = 0;

			if (array_key_exists($tipo->codigo, $tiposProcessados))
				$tipo->horas_aceitas = $tiposProcessados[$tipo->codigo]['horas_aceitas'];

			if ((int)$tipo->obrigatorio === 1) {
				$tipos_obrigatorios[] = $tipo;
				$horas_aceitas_obrigatorias += $tipo->horas_aceitas;
				$tipo->horas_necessarias = (int)$tipo->horas;

				if ((int)$matricula->status == Matricula::HOMOLOGADO)
					$tipo->horas_faltando = 0;
				else
					$tipo->horas_faltando = max(0, $tipo->horas_necessarias - $tipo->horas_aceitas);
			}
			else {
				$tipos_normais[] = $tipo;
				$tipo->horas_disponiveis = max(0, $tipo->horas - $tipo->horas_aceitas);
			}
		}

		$horas_aceitas_normais = max(0, $horas_aceitas - $horas_aceitas_obrigatorias);
		$horas_faltando_normais = max(0, $horas_necessarias_normais - $horas_aceitas_normais);

		$model = array(
			'horas_necessarias' => $horas_necessarias,
			'horas_aceitas' => $horas_aceitas,
			'horas_faltando' => $horas_faltando,
			'horas_necessarias_normais' => $horas_necessarias_normais,
			'horas_aceitas_normais' => $horas_aceitas_normais,
			'horas_faltando_normais' => $horas_faltando_normais,
			'tipos_obrigatorios' => $tipos_obrigatorios,
			'tipos_normais' => $tipos_normais,
			'saldo_anterior' => $saldo_anterior
		);

		return View::make('atividades/relatorioaluno')
					->with('model', $model)
					->with('matricula', $matricula);
	}

	public function getRelatorioTurma()
	{
		$turma_codigo = Input::get('turma', Input::get('turma_codigo'));
		$status = Input::get('status');
		$data_inicial = Input::get('data_inicial');
		$data_final = Input::get('data_final');
		$matriculas = array();

		if (empty($turma_codigo) === false) {
			$this->validarPermissaoParaTurma($turma_codigo);

			$query = Matricula::where('turma_codigo', $turma_codigo)
								->select(DB::raw('matricula.*'))
								->join('usuario', 'usuario.codigo', '=', 'matricula.usuario_codigo');

			if (empty($status) === false) {
				if ($status == 99)
					$query->where('status', Matricula::ATIVO);
				else
					$query->where('status', $status);
			}

			$query
				->orderBy('usuario.primeiro_nome')
				->orderBy('usuario.sobrenome');

			$temp = $query->get();

			if ($status == 99)
			{
				foreach ($temp as $matricula) {
					if ($matricula->horas_faltando == 0)
						$matriculas[] = $matricula;
				}
			}
			else
				$matriculas = $temp;

			if (empty($data_inicial) === false || empty($data_final) === false) {
				foreach ($matriculas as $matricula) {
                    $horas_aceitas = Atividade::aceita($matricula->codigo, $data_inicial, $data_final)->sum("horas_aceitas");

                    if (empty($horas_aceitas))
                    	$horas_aceitas = 0;

                    $matricula->horas_aceitas = (int)$horas_aceitas;
				}
			}
		}

		Input::flash();

		return View::make('atividades/relatorioturma')
			->with('model', $matriculas)
			->with('turma', Turma::find($turma_codigo));
	}

	private function enviarEmailNotificacaoParaAluno($alunos, $tituloAtividade = null)
	{
		if (is_array($alunos) === false)
			$alunos = array($alunos);

		$emails = array();

		foreach ($alunos as $aluno) {
			if ($aluno->notificar)
				$emails[] = $aluno->email;
		}

		if (count($emails))
			Mail::queue('emails.notificaraluno', array('tituloAtividade' => $tituloAtividade), function($message) use($emails)
			{
			    $message->bcc($emails)->subject('[UNIAC] Atualiação de atividades complementares');
			});
	}

	private function enviarEmailNotificacaoParaSupervisoresDoCurso($curso)
	{
		$usuarios = $curso->supervisores()->get();

		$emails = array();

		foreach ($usuarios as $usuario) {
			if ($usuario->notificar)
				$emails[] = $usuario->email;
		}

		if (count($emails)) {
			Mail::queue('emails.notificarsupervisor', array(), function($message) use($emails)
			{
			    $message->bcc($emails)->subject('[UNIAC] Novas atividades para avaliação');
			});


		}
	}
}
