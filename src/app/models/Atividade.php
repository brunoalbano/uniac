<?php


class Atividade extends BaseEloquent {

	const ACEITA = 1;
	const RECUSADA = 2;
	const AGUARDANDO_CORRECAO = 3;
	const AGUARDANDO_AVALIACAO = 4;

	public static function obterRegrasDeValidacaoParaCriar()
	{
		$padrao = array(
			'titulo' 				=> 'required|max:100',
			'horas' 				=> 'required|integer|min:1',
			'tipo_atividade_codigo' => 'required|exists:tipo_atividade,codigo',
			'descricao' 			=> 'required',
			'justificativa' 		=> 'required',
			'files' 				=> 'required_files'
		);

		if (Auth::user()->aluno === false)
		{
			$amanha = date('Y-m-d', mktime(0,0,0,date("m"),date("d")+1,date("Y")));

			$regrasSupervisor = array(
				'turma' 	 		 	=> 'required',
				'matriculas' 		 	=> 'required',
				'status'	 		 	=> 'required|in:' . self::ACEITA . ',' . self::RECUSADA,
				'horas'					=> 'required|integer|min:1|limite_por_tipo_atividade'
			);

			return array_merge ($padrao, $regrasSupervisor);
		}
		else
			return $padrao;
	}

	public static function obterRegrasDeValidacaoParaAceitar()
	{
		$padrao = array(
			'tipo_atividade_codigo' => 'required|exists:tipo_atividade,codigo',
			'horas'					=> 'required|integer|min:1|limite_por_tipo_atividade'
		);

		return $padrao;
	}
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'atividade';

	public $timestamps = true;

	protected $visible = array('codigo', 'titulo');

	protected $guarded = array('codigo');

	public function anexos()
    {
        return $this->belongsToMany('Anexo', 'atividade_anexo', 'atividade_codigo', 'anexo_codigo');
    }

    public function comentarios()
    {
    	return $this->hasMany('Comentario', 'atividade_codigo');
    }

    public function tipoAtividade()
    {
        return $this->belongsTo('TipoAtividade', 'tipo_atividade_codigo');
    }

    public function matricula()
    {
        return $this->belongsTo('Matricula', 'matricula_codigo');
    }

    public function getStatusDescricaoAttribute()
	{
		switch ($this->status) {
			case self::ACEITA:
				return 'Aceita';
			
			case self::RECUSADA:
				return 'Recusada';

			case self::AGUARDANDO_CORRECAO:
				return 'Para Correção';

			case self::AGUARDANDO_AVALIACAO:
				return 'Em Avaliação';

		}

	    return $this->attributes['admin'] === 'yes';
	}

	public function UsuariosNomes()
	{
		$usuarios = $this->comentarios()
						->join('usuario', 'usuario.codigo', '=', 'comentario.usuario_codigo')
						->select(array('usuario.codigo',  'usuario.primeiro_nome'))
						->where('usuario.codigo', '<>', $this->matricula->usuario_codigo)
						->distinct()
						->orderBy('atualizado_em')
						->get();

		$usuario_atual = Auth::user()->codigo;

		$resultado = '';

		if ((int)$usuario_atual === (int)$this->matricula->usuario->codigo)
			$resultado = 'Eu';
		else
			$resultado = $this->matricula->usuario->primeiro_nome;
		
		foreach ($usuarios as $usuario) {

			if ((int)$usuario_atual === (int)$usuario->codigo)
				$nome = 'Eu';
			else
				$nome = $usuario->primeiro_nome;

			$resultado .= ', ';

			$resultado .= $nome;			
		}

		return $resultado;
	}

	public function getAceitaAttribute()
	{
		return (int)$this->status === self::ACEITA;
	}

	public function getRecusadaAttribute()
	{
		return (int)$this->status === self::RECUSADA;
	}

	public function getAguardandoCorrecaoAttribute()
	{
		return (int)$this->status === self::AGUARDANDO_CORRECAO;
	}

	public function getAguardandoAvaliacaoAttribute()
	{
		return (int)$this->status === self::AGUARDANDO_AVALIACAO;
	}

	public function scopeAceita($query, $matricula_codigo, $data_inicial = null, $data_final = null)
	{
		$query
			->where('matricula_codigo', '=', $matricula_codigo)
			->where('status', '=', Atividade::ACEITA);

		if (empty($data_inicial) === false && empty($data_final) === false)
    		$query->whereBetween('avaliado_em', array($data_inicial, $data_final));
    	else 
    		if (empty($data_inicial) === false)
    			$query->where('avaliado_em', '>=', $data_inicial);
    		else
    			if (empty($data_final) === false)
    			{
    				$data_final .= ' 23:59:59';
    				$query->where('avaliado_em', '<=', $data_final);
    			}
	}
}