<?php

class Comentario extends BaseEloquent {

	protected $touches = array('atividade');

	public $timestamps = true;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comentario';
	
    public function usuario()
    {
        return $this->belongsTo('Usuario', 'usuario_codigo');
    }

    public function atividade()
    {
        return $this->belongsTo('Atividade', 'atividade_codigo');
    }
}