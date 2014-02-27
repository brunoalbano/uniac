<?php

class MotivoRecusa extends BaseEloquent {


	public static function obterRegrasDeValidacao()
	{
		return array('nome' => 'required|max:100|unique_ex:motivo_recusa');
	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'motivo_recusa';
}