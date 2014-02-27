<?php namespace Uniac\Anexos;

use Input;
use Anexo;
use Config;
use Validator;
use File;
use Session;
use Response;
use Hash;
use App;

/**
* Classe que controla o upload/download de anexos usando a sessão.
*
* Os anexos são mantidos em sessão separados por escopo (ex: uma tela)
* e por chave de escopo (ex: chave primária de um registro dessa tela).
*
* Cada anexo possui um código único dentro do escopo+chaveEscopo.
*
* A classe foi preparada para trabalhar com o componente jQuery-File-Upload,
* assim os retornos em JSON estão nos formato desse componente.
*/
class AnexoControlador {

	/**
	* Nome do campo enviado no JSON de listagem de anexos e
	* recebido no upload
	* @var string
	*/
	protected $campojson = 'files';

	protected $formatospermitidos;
	protected $tamanhomaximo;
	protected $quantidademaxima;
	protected $destino;

	protected $escopo;

	protected $separador = '_';
	protected $prefixo = 'anexo';

	public function __construct() 
	{
		$this->formatospermitidos	= Config::get('anexos.formatospermitidos');
		$this->tamanhomaximo 		= Config::get('anexos.tamanhomaximo');
		$this->quantidademaxima 	= Config::get('anexos.quantidademaxima');
		$this->destino				= Config::get('anexos.destino');
	}

	/**
	* Define o escopo
	* @param string $escopo Escopo atual
	*/
	public function definirEscopo($escopo)
	{
		$this->escopo = $escopo;
	}

	/**
	* Inicializa os anexos pre-existente de um sub-escopo
	* @param mixed $escopoChave
	* @param array $anexos
	*/
	public function inicializar($escopoChave, $anexos)
	{
		foreach ($anexos as $anexo) 
		{
			$anexo->tamanho = (float)$anexo->tamanho;
			$anexo->novo = false;
			$anexo->excluido = false;
		}

		$this->salvarAnexos($escopoChave, $anexos);
	}

	/**
	* Retorna a chave dos anexos na sessão.
	* @param string $chaveEscopo
	* @return string
	*/
	protected function obterChaveAnexos($chaveEscopo)
	{
		return $this->prefixo . $this->separador . $this->escopo . $this->separador . $chaveEscopo;
	}

	/**
	* Retorna os anexos da sessão.
	* @param string $chaveEscopo
	* @return array
	*/
	protected function obterAnexos($chaveEscopo)
	{
		return Session::get($this->obterChaveAnexos($chaveEscopo), array());
	}

	/**
	* Salva os anexos na sessão.
	* @param string $chaveEscopo
	* @param array $anexos
	*/
	protected function salvarAnexos($chaveEscopo, $anexos)
	{
		Session::put($this->obterChaveAnexos($chaveEscopo), $anexos);
	}

	/**
	* Remove todos os anexos da sessão.
	* @param string $chaveEscopo
	*/
	public function limpar($chaveEscopo)
	{
		Session::forget($this->obterChaveAnexos($chaveEscopo));
	}

	/**
	* Marca um anexo como excluído.
	* @param string $chaveEscopo
	* @param int|string $anexoCodigo
	* @return array Retorna a relação de ('códigos de anexos' => '(bool) se foi possível excluir').
	*/
	public function excluir($chaveEscopo, $anexoCodigo)
	{
		$anexos = $this->obterAnexos($chaveEscopo);

		foreach ($anexos as $indice => $anexo)  {
			if ($anexo->codigo == $anexoCodigo) 
			{
				if ($anexo->novo) 
				{
					unset($anexos[$indice]);
					$this->salvarAnexos($chaveEscopo, $anexos);
				}
				else
					$anexo->excluido = true;

				return Response::json(array($anexoCodigo => true), 200, array('Content-Type' => 'text/plain'));
			}
		}

		return Response::json(array($anexoCodigo => false), 200, array('Content-Type' => 'text/plain'));
	}

	/**
	* Executa o download de um arquivo.
	* Se não encontrar o anexo
	* @param string $chaveEscopo
	* @param int|string $anexoCodigo
	* @return mixed Retorna a Response do download do arquivo.
	*/
	public function download($chaveEscopo, $anexoCodigo)
	{
		$anexos = $this->obterAnexos($chaveEscopo);

		$anexo = null;

		foreach ($anexos as $a) 
		{
			if ($a->codigo == $anexoCodigo) 
			{
				$anexo = $a;

				break;
			}
		}

		if ($anexo === null)
			App::abort(404);

		$caminhoCompleto;

		if (is_string($anexo->caminho)) 
		{
			$caminhoCompleto = $this->caminhoCompleto($anexo->caminho);		
		}
		else 
		{
			$tempdir = $this->obterPastaTemporaria();

			// Cria um arquivo temporário para o anexo
			$caminhoCompleto = tempnam($tempdir, "uni");

			// Registrador para remover o arquivo quando o download terminar
			register_shutdown_function(create_function('', "unlink('{$caminhoCompleto}');")); 

			// Salva o anexo em um arquivo temporário
			File::put($caminhoCompleto, $anexo->arquivo);
		}
/*
header('Content-Type: ' . $anexo->tipo);
header('Content-Disposition: attachment; filename=' . $anexo->nome);
header('Pragma: no-cache');
readfile($caminhoCompleto);

dd($caminhoCompleto);*/
		//return Response::download($caminhoCompleto, $anexo->nome, array('content-type' => $anexo->tipo, 'inline'));	
		return $this->inline($caminhoCompleto, $anexo->nome, $anexo->tipo, $anexo->tamanho);
	}

	private function inline($path, $name, $mime, $size)
	{
		if (is_null($name))
		{
			$name = basename($path);
		}
 
		$response = Response::make(File::get($path));
 
		$response->header('Content-Type', $mime);
		$response->header('Content-Disposition', 'inline; filename="' . $name . '"');
		$response->header('Content-Transfer-Encoding', 'binary');
		$response->header('Cache-Control', 'private, max-age=86400');
		$response->header('Content-Length', $size);
 
		return $response;
	}

	/**
	* Recebe um arquivo, valida e salva na sessão.
	* @param string $chaveEscopo
	* @return array Lista de arquivos recebidos
	*/
	public function upload($chaveEscopo)
	{
		$arquivo = Input::file($this->campojson);

		if (is_array($arquivo))
			$arquivo = $arquivo[0];

		$anexo = new Anexo();

		$anexo->codigo = uniqid($this->prefixo);
		$anexo->nome = $arquivo->getClientOriginalName();
		$anexo->tamanho = $arquivo->getSize();
		$anexo->tipo = $arquivo->getMimeType();
		$anexo->extensao = $arquivo->guessClientExtension();
		$anexo->novo = true;
		$anexo->excluido = false;

		$validationRules = array('anexos' => 'required|mimes:' . implode(',', $this->formatospermitidos) . '|max:' . $this->tamanhomaximo);
		$validator = Validator::make(array('anexos' => $arquivo), $validationRules);

		if ($validator->fails())
			$anexo->error = $validator->messages()->first('anexos');
		else
		{
			$anexos = $this->obterAnexos($chaveEscopo);

			if (count($anexos) >= $this->quantidademaxima)
				$anexo->error = 'Número máximo de anexos excedido';
			else
			{
				$anexo->arquivo = File::get($arquivo->getRealPath());

				$anexos[] = $anexo;

				$this->salvarAnexos($chaveEscopo, $anexos);
			}
		}

		$resultado = $this->prepararAnexoParaRetorno($chaveEscopo, $anexo);

		return $this->prepararListaParaRetorno(array($resultado));
	}	

	/**
	* Retorna a lista de anexos
	* @param string $chaveEscopo
	* @return array 
	*/
	public function listar($chaveEscopo)
	{
		$anexos = $this->obterAnexos($chaveEscopo);

		if (count($anexos) === 0)
			return;

		$resultado = array();

		foreach ($anexos as $anexo) 
		{
			$resultado[] = $this->prepararAnexoParaRetorno($chaveEscopo, $anexo);
		}

		return $this->prepararListaParaRetorno($resultado);
	}

	/**
	* Retorna um anexo no formato para a lista de anexos em JSON
	* @param string $chaveEscopo
	* @param object $anexo
	* @param array
	*/
	protected function prepararAnexoParaRetorno($chaveEscopo, $anexo) 
	{
		if (isset($anexo->anexo))
			$anexo = $anexo->anexo;		

		$resultado = array();
		$resultado['codigo'] = $anexo->codigo;
		$resultado['name'] = $anexo->nome;
		$resultado['size'] = $anexo->tamanho;
		$resultado['type'] = $anexo->tipo;

		if (isset($anexo->error))			
			$resultado['error'] = $anexo->error;
		else
		{
			$origem = $this->escopo . '/' . $chaveEscopo;

			$resultado['url'] = url($origem . '/anexos/' . $anexo->codigo . '/download');
			$resultado['deleteUrl'] = url($origem . '/anexos/' . $anexo->codigo . '/excluir');
			$resultado['deleteType'] = 'POST';
		}

		return $resultado;
	}

	/**
	* Prepara a lista de anexos para retorno no formato JSON
	* @param array $anexos
	* @return array
	*/
	protected function prepararListaParaRetorno(array $anexos) 
	{		
		return Response::json(array($this->campojson => $anexos), 200, array('Content-Type' => 'text/plain'));
	}

	/**
	* Retorna o caminho completo de um anexo salvo.
	* @param string $caminhoAnexo
	* @return string
	*/
	protected function caminhoCompleto($caminhoAnexo) 
	{
		return $this->destino . '/' . $caminhoAnexo;
	}

	/**
	* Retorna o caminho da pasta temporária.
	* @return string
	*/
	protected function obterPastaTemporaria()
	{
		$tempdir = storage_path() . '/temp';

		if (File::isDirectory($tempdir) === false)
			File::makeDirectory($tempdir);

		return $tempdir;
	}

	public function possuiAnexos($chaveEscopo)
	{
		$anexos = $this->obterAnexos($chaveEscopo);
		return count($anexos) > 0;
	}
	
	/**
	* Salva as alterações nos anexos no banco de dados e
	* no sistema de arquivos.
	* Retorna a lista de ids dos anexos existentes.
	* @param string $chaveEscopo
	* @return array
	*/
	public function confirmarAlteracoes($chaveEscopo)
	{
		$resultado = array();

		$sucesso = true;

		try 
		{
			$anexos = $this->obterAnexos($chaveEscopo);

			foreach ($anexos as $anexoTemp) 
			{
				if ($anexoTemp->novo)
				{
					$caminho = uniqid($anexoTemp->codigo) .  '.' . $anexoTemp->extensao;

					$anexo = new Anexo;

					$anexo->nome = $anexoTemp->nome;
					$anexo->tipo = $anexoTemp->tipo;
					$anexo->tamanho = $anexoTemp->tamanho;
					$anexo->caminho = $caminho;

					$caminhoCompleto = $this->caminhoCompleto($caminho);

					File::put($caminhoCompleto, $anexoTemp->arquivo);

					if (File::exists($caminhoCompleto) === false || $anexo->save() === false)
					{
						$sucesso = false;
						break;
					}

					$resultado[] = $anexo;
				}
				else
				{
					$anexo = Anexo::find($anexoTemp->codigo);

					if ($anexoTemp->excluido)
					{
						$anexo->delete();

						$caminhoCompleto = $this->caminhoCompleto($anexo->caminho);

						File::delete($caminhoCompleto);

						$anexo = null;
					}
					else
						$resultado[] = $anexo;
				}				
			}
		}
		catch(Exception $e) 
		{
			$sucesso = false;
		}

		if ($sucesso === false)
		{
			foreach ($resultado as $anexo) 
			{
				$caminhoCompleto = $this->caminhoCompleto($anexo->caminho);
				File::delete($caminhoCompleto);
				$anexo->delete();
			}

			return false;
		}
		else 
		{			
			$listaDeId = array_map(function($item){ return $item->codigo; }, $resultado);

			return $listaDeId;
		}
	}
}