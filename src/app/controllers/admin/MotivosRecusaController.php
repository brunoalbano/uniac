<?php namespace Controllers\Admin;

use BaseController;
use View;
use Response;
use Redirect;
use MotivoRecusa;
use Input;
use Hash;
use Validator;
use Curso;
use DB;
use Session;

class MotivosRecusaController extends BaseController {

	public function getIndex()
	{
    	return View::make('admin/motivosrecusa/index');
	}

	public function getListar()
	{
		$input = Input::all();

		$resultado = MotivoRecusa::selectToGrid('codigo', 'nome')->toGrid($input);

		return $resultado;
	}

	public function getInserir()
	{
		return View::make('admin/motivosrecusa/motivorecusa')
					->with('titulo', 'Inserindo Motivo de Recusa')
					->with('readonly', FALSE);
	}

	public function postInserir()
	{
		$validator = Validator::make(Input::all(), MotivoRecusa::obterRegrasDeValidacao());

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);

		$motivorecusa = new MotivoRecusa;

		$motivorecusa->nome = Input::get('nome');
		
		$motivorecusa->save();

		
		return Redirect::to('motivosrecusa')->with('success', 'Motivo de recusa inserido com sucesso.');
	}

	public function getEditar(MotivoRecusa $motivorecusa)
	{
		return View::make('admin/motivosrecusa/motivorecusa')
					->with('titulo', 'Editando Motivo de Recusa')
					->with('model', $motivorecusa)
					->with('readonly', FALSE);
	}

	public function postEditar(MotivoRecusa $motivorecusa)
	{
		$validator = Validator::make(Input::all(), MotivoRecusa::obterRegrasDeValidacao($motivorecusa->codigo));

		if ($validator->fails())
			return Redirect::back()->withInput()->withErrors($validator);

		$motivorecusa->nome = Input::get('nome');
		
		$motivorecusa->save();

		
		return Redirect::to('motivosrecusa')->with('success', 'Motivo de recusa editado com sucesso.');
	}

	public function getVisualizar(MotivoRecusa $motivorecusa)
	{
		return View::make('admin/motivosrecusa/motivorecusa')
					->with('titulo', 'Visualizando Motivo de Recusa')
					->with('model', $motivorecusa)
					->with('readonly', TRUE);
	}

	public function postExcluir(MotivoRecusa $motivorecusa)
	{
		$motivorecusa->delete();
	}

}