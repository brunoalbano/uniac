<?php

class Campus extends BaseEloquent {

	public static function obterRegrasDeValidacao()
	{
		return array('nome' => 'required|max:50|unique_ex:campus');
	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'campus';

	protected $visible = array('codigo', 'nome');
}