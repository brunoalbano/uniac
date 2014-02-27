<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| such as the size rules. Feel free to tweak each of these messages.
	|
	*/

	"accepted"         => "O campo :attribute deve ser aceito.",
	"active_url"       => "A URL :attribute não é válida.",
	"after"          => "A data :attribute deve ser posterior a :date.",
	"alpha"          => "O campo :attribute deve conter somente letras.",
	"alpha_dash"     => "O campo :attribute deve conter somente letras, números e traços.",
	"alpha_num"      => "O campo :attribute deve conter somente letras e números.",
	"array"          => "O campo :attribute deve ser uma lista.",
	"before"         => "A data :attribute deve ser anterior a :date.",
	"between"          => array(
		"numeric" => "O campo :attribute deve estar entre :min e :max.",
		"file"    => "O arquivo :attribute deve possuir entre :min e :max kilobytes.",
		"string"  => "O campo :attribute deve possuir entre :min e :max caracteres.",
		"array"   => "O campo :attribute deve possuir entre :min e :max itens.",
	),
	"confirmed"        => "O campo :attribute não confere.",
	"date"             => "O campo :attribute não é uma data válida.",
	"date_format"      => "O campo :attribute não confere com o formato :format.",
	"different"        => "Os campos :attribute e :other devem ser diferentes.",
	"digits"           => "O campo :attribute deve possuir :digits dígitos.",
	"digits_between"   => "O campo :attribute deve possuir entre :min e :max dígitos.",
	"email"            => "O campo :attribute não é um e-mail válido.",
	"exists"           => "O valor de :attribute é inválido.",
	"image"            => "O campo :attribute deve ser uma imagem.",
	"in"               => "O valor de :attribute é inválido.",
	"integer"          => "O campo :attribute deve ser um inteiro.",
	"ip"               => "O campo :attribute deve ser um endereço IP válido.",
	"max"              => array(
		"numeric" => "O campo :attribute deve ser menor que :max.",
		"file"    => "O arquivo :attribute deve possuir menos que :max kb.",
		"string"  => "O campo :attribute deve possuir menos que :max caracteres.",
		"array"   => "O campo :attribute deve possuir menos que :max itens.",
	),
	"mimes"            => "O campo :attribute deve ser um arquivo dos tipos: :values.",
	"min"              => array(
		"numeric" => "O campo :attribute deve ser pelo menos :min.",
		"file"    => "O arquivo :attribute deve possuir mais que :min kilobytes.",
		"string"  => "O campo :attribute deve possuir pelo menos :min caracteres.",
		"array"   => "O campo :attribute deve possuir pelo menos :min itens.",
	),
	"not_in"           => "O campo :attribute é inválido.",
	"numeric"          => "O campo :attribute deve ser um número.",
	"regex"            => "O formato do campo :attribute é inválido.",
	"required"         => "O campo :attribute é obrigatório.",
	"required_if"      => "O campo :attribute é obrigatório quando :other for :value.",
	"required_with"    => "O campo :attribute é obrigatório quando :values está presente.",
	"required_without" => "O campo :attribute é obrigatório quando :values não está presente.",
	"same"             => "Os campos :attribute e :other não conferem.",
	"size"             => array(
		"numeric" => "O campo :attribute deve ter tamanho :size.",
		"file"    => "O arquivo :attribute deve ter :size kilobyte.",
		"string"  => "O campo :attribute deve ter :size caracteres.",
		"array"   => "O campo :attribute deve ter :size itens.",
	),
	"unique"           => "O campo :attribute já foi cadastrado.",
	"url"              => "O campo :attribute não é uma URL válida.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation
	|--------------------------------------------------------------------------
	|
	*/
	"tipos_de_atividade"   	   => "Existem tipos de atividade sem descrição ou horas.",
	"matrizes_curriculares"    => "Existem matrizes curriculares sem nome ou horas.",
	"unique_ex"                => "O campo :attribute já foi cadastrado.",
	"matriculas"			   => "Existem matrículas sem aluno ou matriz curricular.",
	"matriculas_repetidas"	   => "Existem matrículas com alunos repetidos.",
	"matriculas_obrigatorias"  => "Selecione ao menos um aluno.",
	"limite_por_tipo_atividade"=> "As horas excedem o limite do tipo de atividade.",
	"required_files"		   => "Selecione ao menos um :attribute.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(
		"descricao" => "descrição",
		"titulo" => "título",
		"horas_requisitadas" => "horas",
		"files" => "anexo",
		"tipo_atividade_codigo" => "tipo de atividade",
		"campus_codigo" => "campus",
		"curso_codigo" => "curso",
		"files" => "anexo"
	),

);
