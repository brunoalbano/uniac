<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Usuario extends BaseEloquent implements UserInterface, RemindableInterface {

	const ADMINISTRADOR  = 1;
	const ALUNO = 2;
	const CONVIDADO = 3;
	const SUPERVISOR = 4;

	public static function obterRegrasDeValidacaoAoInserir()
	{
		return array(
			'primeiro_nome'       => 'required|max:50',
			'sobrenome'           => 'max:100',
			'email'               => 'required|max:150|email|unique:usuario,email',
			'login'               => 'required|min:3|max:20|alpha_dash|unique:usuario,login',
			'senha'         	  => 'required|between:6,20',
			'confirmar_senha' 	  => 'required|between:6,20|same:senha',
			'acesso_liberado'     => 'in:0,1',
			'perfil'              => 'in:1,2,3,4');
	}

	public static function obterRegrasDeValidacaoAoEditar($codigo)
	{
		return array(
			'primeiro_nome'       => 'required|max:50',
			'sobrenome'           => 'max:100',
			'email'               => 'required|max:150|email|unique:usuario,email,' . $codigo . ',codigo',
			'login'               => 'required|min:3|max:20|alpha_dash|unique:usuario,login,' . $codigo . ',codigo',
			'acesso_liberado'     => 'in:0,1',
			'perfil'              => 'in:1,2,3,4');
	}

	public function getAdministradorAttribute()
	{
		return (int)$this->perfil === self::ADMINISTRADOR;
	}

	public function getAlunoAttribute()
	{
		return (int)$this->perfil === self::ALUNO;
	}

	public function getConvidadoAttribute()
	{
		return (int)$this->perfil === self::CONVIDADO;
	}

	public function getSupervisorAttribute()
	{
		return (int)$this->perfil === self::SUPERVISOR;
	}

	public function getCoordenadorAttribute()
	{
		return (int)$this->perfil === self::SUPERVISOR && $this->cursos()->where('coordenador', 1)->count() > 0;
	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'usuario';

	protected $visible = array('codigo', 'primeiro_nome', 'sobrenome', 'login', 'email', 'acesso_liberado', 'perfil');

	protected $hidden = array('senha');

	public function cursos()
    {
        return $this->belongsToMany('Curso', 'usuario_curso', 'usuario_codigo', 'curso_codigo')->withPivot('coordenador');
    }

    public function matriculas()
    {
        return $this->hasMany('Matricula', 'usuario_codigo');
    }

	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	public function getAuthPassword()
	{
		return $this->senha;
	}

	public function getReminderEmail()
	{
		return $this->email;
	}

	public function getNomeCompletoAttribute()
	{
		if (trim($this->sobrenome) !== '')
			return $this->primeiro_nome . ' ' . $this->sobrenome;
		else
			return $this->primeiro_nome;
	}

	public function testarPermissaoParaCurso($curso)
	{
		if (Auth::user()->supervisor)
		{
			if (is_numeric($curso) === false)
				$curso = $curso->codigo;

			if (Auth::user()->has('cursos', '=', $curso)->count() === false)
				App::abort(403);
		}
	}
}