<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Formatos permitidos
	|--------------------------------------------------------------------------
	|
	| Tipos MIME de arquivos permitidos para upload de anexos.
	| Exemplo: array('jpeg', 'jpg', 'bmp', 'png')
	|
	*/

	'formatospermitidos' => array('jpeg', 'jpg', 'bmp', 'png', 'gif', 'pdf', 'doc', 'docx', 'odt'),

	/*
	|--------------------------------------------------------------------------
	| Tamanho máximo em kilobytes
	|--------------------------------------------------------------------------
	|
	| Tamanho máximo de um arquivo em kilobytes
	|
	*/

	'tamanhomaximo' => 2000,

	/*
	|--------------------------------------------------------------------------
	| Quantidade máxima por atividade
	|--------------------------------------------------------------------------
	|
	| Quantidade máxima de anexos por atividade/curso.
	|
	*/

	'quantidademaxima' => 5,

	/*
	|--------------------------------------------------------------------------
	| Pasta de anexos
	|--------------------------------------------------------------------------
	|
	| Caminho de destinho onde os anexos serão salvos.
	|
	*/

	'destino' => base_path() . '/anexos',

);
