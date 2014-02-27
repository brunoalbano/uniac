<?php namespace Controllers;

use BaseController;
use View;
use Response;
use Redirect;
use Auth;
use Input;
use Password;
use Hash;
use LogHandler;
use Matricula;
use Session;
use App;
use Validator;
use DateTime;
use Usuario;

class AutenticarController extends BaseController {

	public function getLogin()
	{
		if (Auth::check())
		{
			return Redirect::to('atividades');
		}

		if (Input::has('emailinvalido'))
    		return View::make('autenticar.autenticar')->with('error', 'Entre em contato com o coordenador do seu curso para corrigir o seu endereço de e-mail.');
    	else
    		return View::make('autenticar.autenticar');
	}

	public function postLogin()
	{
		$login = Input::get('login');
		$senha = Input::get('senha');
		$lembrar = Input::get('lembrar') === 'true';

		if (Auth::attempt(array('login' => $login, 'password' => $senha, 'acesso_liberado' => true), $lembrar))
		{
			$usuario = Auth::user();
			$usuario->ultimo_acesso = new DateTime();
			$usuario->save();

			if ($usuario->aluno)
			{
				Session::forget('matricula_codigo');

				$matriculas = Matricula::where('usuario_codigo', $usuario->codigo)->get();

				if (count($matriculas) === 0) {
					Auth::logout();
					return Redirect::back()->withInput()->with('error', 'Não foram encontradas matrículas para o usuário.');
				}
				else
				if (count($matriculas) === 1) {
					Session::put('matricula_codigo', $matriculas[0]->codigo);
				}
				else
					return Redirect::to('listarmatriculas');
			}

		    return Redirect::intended('atividades');
		}

		return Redirect::back()->withInput()->with('error', 'Usuário e/ou senha inválidos.');
	}

	public function getListarMatriculas()
	{
		$matriculas = Matricula::with('turma')
							   ->where('usuario_codigo', Auth::user()->codigo)
							   ->get();

    	return View::make('autenticar.listarmatriculas')->with('matriculas', $matriculas);
	}

	public function getSelecionarMatricula($matricula)
	{
		if ($matricula->usuario_codigo !== Auth::user()->codigo || $matricula->status == Matricula::BLOQUEADO)
			App::abort(403);

		Session::put('matricula_codigo', $matricula->codigo);

		return Redirect::to('atividades');
	}

	public function getSair()
	{
		if (Auth::check())
		{
			Auth::logout();
		}

		return Redirect::to('autenticar');
	}

	public function getRedefinirSenha($token)
	{
		return View::make('autenticar.redefinir')->with('token', $token);
	}

	public function postRedefinirSenha($token)
	{
		$email = Input::get('email');
		$password = Input::get('password');
		$password_confirmation = Input::get('password_confirmation');
		$token = Input::get('token');

	    $credentials = array(
	    	'email' => $email, 
	    	'password' => $password,
	    	'password_confirmation' => $password_confirmation,
	    	'token' => $token
	    );

	    Password::reset($credentials, function($usuario, $senha) use($email)
	    {
	        $usuario->senha = Hash::make($senha);

	        $usuario->save();

	        LogHandler::registrar('Redefiniu a senha para: ' . $email);

	        return Redirect::to('atividades');
	    });

	    return Redirect::to('autenticar')->with('success', 'Senha redefinida com sucesso.');
	}

	public function getEnviarRedefinirSenha()
	{
    	return View::make('autenticar.enviarredefinir');		
	}

	public function postEnviarRedefinirSenha()
	{
		$credentials = array('email' => Input::get('email'));

    	Password::remind($credentials, function($message, $user)
		{
		    $message->subject('[UNIAC] Redefinir senha');
		});

    	if (Session::has('error'))
    		return Redirect::to('autenticar')->with('error', 'Falha ao enviar e-mail de redefinição de senha.');
    	else
			return Redirect::to('autenticar')->with('success', 'E-mail de redefinição de senha enviado. Confira sua caixa de e-mail.');
    }

    public function getConfiguracoes()
    {    	
    	return View::make('autenticar.configuracoes')->with('model', Auth::user());
    }

    public function postConfiguracoes()
    {    	
    	$regrasDeValidacao = array(
    		'email' 			=> 'required|email', 
    		'senha' 			=> 'between:6,20',
    		'confirmar_senha' 	=> 'same:senha',
    		'notificar' 		=> 'in:0,1'
    	);

		$validator = Validator::make(Input::all(), $regrasDeValidacao);

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);

		$usuario = Auth::user();
		$email = Input::get('email');
		$senha = Input::get('senha');
		$notificar = (int)Input::get('notificar');

		if ($usuario->email != $email || 
			empty($senha) === false || 
			(int)$usuario->notificar != $notificar) {

			$usuario->email = $email;
			$usuario->notificar = $notificar === 1;

			if (empty($senha) === false) {
				$senha = Hash::make($senha);
				$usuario->senha = $senha;
			}

			$usuario->save();
		}

		return Redirect::to('atividades')->with('success', 'Configurações alteradas com sucesso.');
    }

    public function getPrimeiroAcesso()
    {
    	return View::make('autenticar.primeiroacesso');
    }

    public function postPrimeiroAcesso()
    {
    	$confirma = Input::get('confirma', 0);

    	if ((int)$confirma === 1) {
			$credentials = array('email' => Input::get('email'));

	    	Password::remind($credentials, function($message, $user)
			{
			    $message->subject('[UNIAC] Redefinir senha');
			});
	    	
	    	if (Session::has('error'))
	    		return Redirect::to('autenticar')->with('error', 'Falha ao enviar e-mail de redefinição de senha.');
	    	else
				return Redirect::to('autenticar')->with('success', 'E-mail de redefinição de senha enviado. Confira sua caixa de e-mail.');
    	}
    	else {
	    	$ra = Input::get('login');

	    	$usuario = Usuario::where('login', $ra)->first();

	    	if (empty($usuario) || (int)$usuario->acesso_liberado === 0)
	    		return Redirect::back()->withInput()->with('error', 'Usuário não encontrado');

    		return View::make('autenticar.primeiroacessoconfirmar')->with('model', $usuario);
    	}
    }
}