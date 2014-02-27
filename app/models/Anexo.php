<?php

class Anexo extends BaseEloquent {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'anexo';

	protected $visible = array('codigo', 'nome', 'tamanho', 'tipo');
}