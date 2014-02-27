<?php namespace Controllers\Admin;

use BaseController;
use View;
use Response;
use Redirect;
use LogModel;
use Input;
use Hash;
use Validator;
use Curso;
use DB;
use Session;

class AuditoriaController extends BaseController {

	public function getIndex()
	{
    	return View::make('admin.auditoria.index');
	}

	public function getListar()
	{
		$input = Input::all();

		$resultado = LogModel::selectToGrid('acao', 'criado_em', 'url', 'tabela', 'tabela_codigo_campo', 
											'tabela_codigo_valor', 'navegador', 'ip', 'usuario.login')
							 ->join('usuario', 'usuario.codigo', '=', 'log.usuario_codigo')
							 ->toGrid($input);

		return $resultado;
	}

}