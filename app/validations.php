<?php

class CustomValidator extends Illuminate\Validation\Validator {

    /**
	 * Validação utilizada no cadastro de cursos.
	 * Valida se uma lista de tipos de atividade possui descrição e horas preenchidas.
	 *
	 * Extenção para a validação unique.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
    public function validateTiposDeAtividade($attribute, $value, $parameters)
    {
	    $tipos;

	    if (is_array($value))
	    	$tipos = $value;
	    else
	    	$tipos = json_decode($value);

	    if (is_array($tipos))
		    foreach ($tipos as $tipo) {
		    	if ($tipo->codigo != TipoAtividade::RAIZ_CODIGO) {
			    	if (isset($tipo->descricao) === false || isset($tipo->horas) === false)
			    		return false;

			    	$descricao = trim($tipo->descricao);
			    	$horas = (int)$tipo->horas || 0;

			    	if (empty($descricao) || $horas <= 0) {
			    		return false;
			    	}
		    	}
		    }

	    return true;
    }

    /**
	 * Validação utilizada no cadastro de cursos.
	 * Valida se uma lista de tipos de atividade possui descrição e horas preenchidas.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
    public function validateMatrizesCurriculares($attribute, $value, $parameters)
    {
	    $matrizes;

	    if (is_array($value))
	    	$matrizes = $value;
	    else
	    	$matrizes = json_decode($value);

	    if (is_array($matrizes))
		    foreach ($matrizes as $matriz) {
		    	if (isset($matriz->nome) === false || isset($matriz->horas) === false)
		    		return false;

		    	$nome = trim($matriz->nome);
		    	$horas = (int)$matriz->horas || 0;

		    	if (empty($nome) || $horas <= 0)
		    	{
		    		return false;
		    	}
		    }

	    return true;
    }



    /**
	 * Validação utilizada no cadastro de turmas.
	 * Valida se uma lista de matriculas possui usuario e matriz curricular preenchidas.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
    public function validateMatriculas($attribute, $value, $parameters)
    {
	    $matriculas;

	    if (is_array($value))
	    	$matriculas = $value;
	    else
	    	$matriculas = json_decode($value);

	    if (is_array($matriculas))
		    foreach ($matriculas as $matricula) {
		    	if (empty($matricula->usuario_codigo) || empty($matricula->matriz_curricular_codigo))
		    		return false;
		    }

	    return true;
    }

    /**
	 * Validação utilizada no cadastro de turmas.
	 * Valida se uma lista de matriculas possui usuários repetidos.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
    public function validateMatriculasRepetidas($attribute, $value, $parameters)
    {
	    $matriculas;

	    if (is_array($value))
	    	$matriculas = $value;
	    else
	    	$matriculas = json_decode($value);

	    $usuarios = array();

	    if (is_array($matriculas))
		    foreach ($matriculas as $matricula) {
		    	if (empty($matricula->usuario_codigo) === false)
		    	{
			    	if (isset($usuarios[$matricula->usuario_codigo]))
			    		return false;

			    	$usuarios[$matricula->usuario_codigo] = 1;
		    	}
		    }

	    return true;
    }


    /**
	 * Valida se as horas estão dentro do limite de atividade.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
    protected function validateLimitePorTipoAtividade($attribute, $value, $parameters)
    {
    	// Recebe o nome do campo que guarda o tipo de atividade
    	$campo_tipo = empty($parameters[0]) ? 'tipo_atividade_codigo' : $parameters[0];

    	$tipo_codigo = Input::get('tipo_atividade_codigo');

    	$tipo = TipoAtividade::find($tipo_codigo);

    	// Valida se o tipo de atividade existe
    	if (empty($tipo))
    		return true;

    	// Valida se o tipo de atividade é o último nível
    	if (TipoAtividade::where('tipo_atividade_codigo', $tipo_codigo)->count())
    		return false;

    	// Valida se as horas são superiores ao limite por atividade
    	if (((int)$value) > $tipo->horas)
    		return false;

    	return true;
    }


    /**
	 * Valida se os arquivos foram enviados.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
    protected function validateRequiredFiles($attribute, $value, $parameters)
    {
    	$codigo = Input::get('codigo');

    	if (empty($codigo))
    		return true;

    	$anexos = new AnexoControlador;
		$anexos->definirEscopo('atividades');

		if ($anexos->possuiAnexos($codigo) === false)
	    	if (empty($value) || is_array($value) === false || empty($value[0]))
	    		return false;

    	return true;
    }

    /**
	 * Valida se um campo é único no banco de dados e ignora o codigo atual.
	 *
	 * Extenção para a validação unique.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validateUniqueEx($attribute, $value, $parameters)
	{
		if (isset($parameters[1]) === false)
			$parameters[] = $attribute;

		if (isset($parameters[2]) === false && isset($this->data['codigo']))
		{
			$parameters[] = $this->data['codigo'];
			$parameters[] = 'codigo';
		}

		return $this->validateUnique($attribute, $value, $parameters);
	}

}

Validator::resolver(function($translator, $data, $rules, $messages)
{
    return new CustomValidator($translator, $data, $rules, $messages);
});