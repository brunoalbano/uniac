<?php namespace Uniac\Log;

use LogModel;
use Auth;
use Input;
use Event;

class LogEventHandler {

	const CRIADO = 'Criou';
	const ATUALIZADO = 'Atualizou';
	const EXCLUIDO = 'Excluiu';
	const AUTENTICADO = 'Autenticou';
	const SAIU = 'Saiu';
	const HTTP403 = 'Acesso negado';
	const HTTP404 = 'Página não encontrada';
	const HTTP500 = 'Erro interno do servidor';

	public function registrar($acao, $model = null)
	{
		$log = new LogModel;

		if ($model !== null) 
		{
			$tabela = $model->getTable();

			if ($tabela === $log->getTable())
				return;

			$log->tabela = $tabela;
			$log->tabela_codigo_campo = $model->getKeyName();
			$log->tabela_codigo_valor = $model->getKey();
		}

		if (Auth::check() && empty(Auth::user()->codigo) === false)
		{
			$log->usuario_codigo = Auth::user()->codigo;
		}

		$log->acao = $acao;
		$log->url = Input::server('REQUEST_URI');
		$log->navegador = Input::server('HTTP_USER_AGENT');
		$log->ip = $this->getIp();

		$log->save();
	}

	private function getIp()
	{	 
		$ip = Input::server('HTTP_CLIENT_IP');
	    if (empty($ip)) {
	        $ip = Input::server('HTTP_X_FORWARDED_FOR');
	        if (empty($ip)) {
	        	$ip = Input::server('REMOTE_ADDR');
	        }
	    }
	 
	    return $ip;	 
	}

	public function criado($model)
	{
		$this->registrar(self::CRIADO, $model);
	}

	public function atualizado($model)
	{
		$this->registrar(self::ATUALIZADO, $model);
	}

	public function excluido($model)
	{
		$this->registrar(self::EXCLUIDO, $model);
	}

	public function autenticado()
	{
		$this->registrar(self::AUTENTICADO);
	}

	public function saiu()
	{
		$this->registrar(self::SAIU);
	}

	public function http404()
	{
		$this->registrar(self::HTTP404);
	}

	public function http403()
	{
		$this->registrar(self::HTTP403);
	}

	public function http500()
	{
		$this->registrar(self::HTTP500);
	}

	public static function subscribe()
	{
		Event::listen('auth.login', 'LogEventHandler@autenticado');
		Event::listen('auth.logout', 'LogEventHandler@saiu');
		Event::listen('500', 'LogEventHandler@http500');
		Event::listen('404', 'LogEventHandler@http404');
	}
}