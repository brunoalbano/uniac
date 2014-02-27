<?php

class TipoAtividade extends BaseEloquent {

	const RAIZ_CODIGO = -1;

	public static function obterRegrasDeValidacao()
	{
	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tipo_atividade';

	//protected $visible = array('codigo', 'descricao', 'numero');

	/*public function itens()
	{
		return $this->hasMany('TipoAtividade', 'tipo_atividade_codigo');
	}*/

	public function getDescricaoCompletaAttribute()
	{
		return $this->numero . ' ' . $this->descricao;
	}

    public function tipos_de_atividade()
    {
    	return $this->hasMany('TipoAtividade', 'tipo_atividade_codigo');
    }

    public function curso()
    {
        return $this->belongsTo('Curso', 'curso_codigo');
    }
}