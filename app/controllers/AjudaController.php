<?php namespace Controllers;

use BaseController;
use View;
use Response;
use Redirect;
use Auth;
use Input;
use Matricula;
use Session;
use App;
use File;
use AnexoControlador;

class AjudaController extends BaseController {

	private $anexos;

	public function __construct(AnexoControlador $anexos)
	{
		parent::__construct();
		
		$this->anexos = $anexos;
		$this->anexos->definirEscopo('ajuda');
	}

	public function getAjuda()
	{
		$matricula_codigo = Session::get('matricula_codigo');
		if (empty($matricula_codigo) === false) {
			$matricula = Matricula::find($matricula_codigo);

			$documentos = $matricula->turma()->first()->curso()->first()->anexos()->get();

			return View::make('ajuda.ajuda')->with('documentos', $documentos);
		}
		else
			return View::make('ajuda.ajuda');
	}

	public function getSobre()
	{
		return View::make('ajuda.sobre');
	}

	public function getDocumento($codigo)
	{
		$matricula_codigo = Session::get('matricula_codigo');

		$matricula = Matricula::find($matricula_codigo);

		$curso = $matricula->turma()->first()->curso()->first();

		$documentos = $curso->anexos()->get();

		$this->anexos->inicializar($curso->codigo, $documentos);

		return $this->anexos->download($curso->codigo, $codigo);
	}

	public function getManual($manual)
	{
		$name = $manual;
		$path = base_path() . '/public/manuais/' . $manual;
 
		$response = Response::make(File::get($path));
 
		$response->header('Content-Type', 'application/pdf');
		$response->header('Content-Disposition', 'inline; filename="' . $name . '"');
		$response->header('Content-Transfer-Encoding', 'binary');
		$response->header('Cache-Control', 'private, max-age=86400');
 
		return $response;
	}
}