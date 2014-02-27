<?php namespace Controllers\Admin;

use BaseController;
use View;
use Response;
use Redirect;
use Campus;
use Input;
use Hash;
use Validator;
use Curso;
use DB;
use Session;

class CampiController extends BaseController {

	public function getIndex()
	{
    	return View::make('admin/campi/index');
	}

	public function getListar()
	{
		$input = Input::all();

		$resultado = Campus::selectToGrid('codigo', 'nome')->toGrid($input);

		return $resultado;
	}

	public function getInserir()
	{
		return View::make('admin/campi/campus')
					->with('titulo', 'Inserindo Campus')
					->with('readonly', FALSE);
	}

	public function postInserir()
	{
		$validator = Validator::make(Input::all(), Campus::obterRegrasDeValidacao());

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);

		$campus = new Campus;

		$campus->nome = Input::get('nome');
		
		$campus->save();

		return Redirect::to('campi')->with('success', 'Campus inserido com sucesso.');
	}

	public function getEditar(Campus $campus)
	{
		return View::make('admin/campi/campus')
					->with('titulo', 'Editando Campus')
					->with('model', $campus)
					->with('readonly', FALSE);
	}

	public function postEditar(Campus $campus)
	{
		$validator = Validator::make(Input::all(), Campus::obterRegrasDeValidacao($campus->codigo));

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);

		$campus->nome = Input::get('nome');
		
		$campus->save();

		return Redirect::to('campi')->with('success', 'Campus editado com sucesso.');
	}

	public function getVisualizar(Campus $campus)
	{
		return View::make('admin/campi/campus')
					->with('titulo', 'Visualizando Campus')
					->with('model', $campus)
					->with('readonly', TRUE);
	}

	public function postExcluir(Campus $campus)
	{
		$campus->delete();
	}

}