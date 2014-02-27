<?php

class LogModel extends BaseEloquent {

	public $timestamps = true;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'log';

    public function usuario()
    {
        return $this->belongsTo('Usuario', 'usuario_codigo');
    }
}