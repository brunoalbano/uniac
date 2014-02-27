<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/*******************
/* AUTENTICAR
/******************/

Route::group(array('prefix' => 'autenticar'), function() 
{
	Route::get('/', 'Controllers\AutenticarController@getLogin');

	Route::post('/', 'Controllers\AutenticarController@postLogin');

	Route::get('sair', 'Controllers\AutenticarController@getSair');
});

Route::get('sair', 'Controllers\AutenticarController@getSair');

Route::get('configuracoes', 'Controllers\AutenticarController@getConfiguracoes');

Route::post('configuracoes', 'Controllers\AutenticarController@postConfiguracoes');

Route::get('primeiroacesso', 'Controllers\AutenticarController@getPrimeiroAcesso');

Route::post('primeiroacesso', 'Controllers\AutenticarController@postPrimeiroAcesso');

Route::get('redefinirsenha', 'Controllers\AutenticarController@getEnviarRedefinirSenha');

Route::post('redefinirsenha', 'Controllers\AutenticarController@postEnviarRedefinirSenha');

Route::get('password/reset/{token}', 'Controllers\AutenticarController@getRedefinirSenha');

Route::post('password/reset/{token}', 'Controllers\AutenticarController@postRedefinirSenha');

Route::get('sobre', 'Controllers\AjudaController@getSobre');

Route::get('ajuda', 'Controllers\AjudaController@getAjuda');

Route::get('manuais/{manual}', 'Controllers\AjudaController@getManual');

Route::get('ajuda/documento/{codigo}', 'Controllers\AjudaController@getDocumento');

Route::model('matricula', 'Matricula');

Route::get('listarmatriculas', 'Controllers\AutenticarController@getListarMatriculas');

Route::get('selecionarmatricula/{matricula}', 'Controllers\AutenticarController@getSelecionarMatricula');

Route::group(array('before' => 'auth'), function()
{

	Route::get('/', 'Controllers\AtividadesController@getIndex');

	/*******************
	/* ATIVIDADES
	/******************/
	Route::group(array('prefix' => 'atividades'), function() 
	{
		Route::model('atividade', 'Atividade');

		Route::get('/', 'Controllers\AtividadesController@getIndex');

		Route::get('listararvore', 'Controllers\AtividadesController@getListarArvore');

		Route::get('criar', 'Controllers\AtividadesController@getCriar');

		Route::post('criar', 'Controllers\AtividadesController@postCriar');

		Route::get('relatorio/matricula/{matricula_codigo}', 'Controllers\AtividadesController@getRelatorioAluno');

		Route::group(array('before' => 'coordenador_convidado_administrador'), function()
		{
			Route::get('turmascoordenador', 'Controllers\AtividadesController@getTurmasCoordenador');

			Route::get('relatorio/turma', 'Controllers\AtividadesController@getRelatorioTurma');
		});

		Route::group(array('before' => 'supervisor_administrador'), function() 
		{
			Route::get('turmas', 'Controllers\AtividadesController@getTurmas');

			Route::get('motivosrecusa', 'Controllers\AtividadesController@getMotivosRecusa');

			Route::get('alunos', 'Controllers\AtividadesController@getAlunos');
		});

		Route::get('{atividade_codigo}/anexos', 'Controllers\AtividadesController@getListarAnexos');

		Route::post('{atividade_codigo}/anexos', 'Controllers\AtividadesController@postEnviarAnexo');

		Route::get('{atividade_codigo}/anexos/{anexo_codigo}/download', 'Controllers\AtividadesController@getDownloadAnexo');

		Route::post('{atividade_codigo}/anexos/{anexo_codigo}/excluir', 'Controllers\AtividadesController@postExcluirAnexo');

		Route::post('{atividade}/responder', 'Controllers\AtividadesController@postResponder');

		Route::get('{atividade}', array('as' => 'visualizarAtividade', 'uses' => 'Controllers\AtividadesController@getVisualizar'));

		Route::group(array('before' => 'aluno'), function() {

			Route::get('{atividade}/editar', 'Controllers\AtividadesController@getEditar');

			Route::post('{atividade_codigo}/editar', 'Controllers\AtividadesController@postEditar');

			Route::get('{atividade}/excluir', 'Controllers\AtividadesController@getExcluir');
		});

		Route::group(array('before' => 'supervisor_administrador'), function() 
		{
			Route::post('{atividade}/aceitar', 'Controllers\AtividadesController@postAceitar');

			Route::post('{atividade}/recusar', 'Controllers\AtividadesController@postRecusar');
		});
	});
});

Route::group(array('before' => 'coordenador_convidado_administrador'), function()
{
	/*******************
	/* TURMAS
	/******************/
	Route::group(array('prefix' => 'turmas'), function() 
	{
		Route::model('turma', 'Turma');

		Route::get('/', 'Controllers\Admin\TurmasController@getIndex');

		Route::get('listar', 'Controllers\Admin\TurmasController@getListar');

		Route::get('inserir', 'Controllers\Admin\TurmasController@getInserir');

		Route::post('inserir', 'Controllers\Admin\TurmasController@postInserir');

		Route::get('alunos', 'Controllers\Admin\TurmasController@getAlunos');

		Route::get('matrizescurriculares', 'Controllers\Admin\TurmasController@getMatrizesCurriculares');

		Route::get('{turma}/editar', 'Controllers\Admin\TurmasController@getEditar');

		Route::post('{turma}/editar', 'Controllers\Admin\TurmasController@postEditar');

		Route::post('{turma}/excluir', 'Controllers\Admin\TurmasController@postExcluir');

		Route::get('{turma}', 'Controllers\Admin\TurmasController@getVisualizar');
	});
	
	/*******************
	/* USUÁRIOS
	/******************/
	Route::group(array('prefix' => 'usuarios'), function() 
	{
		Route::model('usuario', 'Usuario');

		Route::get('/', 'Controllers\Admin\UsuariosController@getIndex');

		Route::get('listar', 'Controllers\Admin\UsuariosController@getListar');

		Route::get('inserir', 'Controllers\Admin\UsuariosController@getInserir');

		Route::post('inserir', 'Controllers\Admin\UsuariosController@postInserir');

		Route::get('importar', 'Controllers\Admin\UsuariosController@getImportar');

		Route::post('importar', 'Controllers\Admin\UsuariosController@postImportar');

		Route::get('exportar', 'Controllers\Admin\UsuariosController@getExportar');

		Route::get('turmas', 'Controllers\Admin\UsuariosController@getTurmas');

		Route::get('matrizescurriculares', 'Controllers\Admin\UsuariosController@getMatrizesCurriculares');

		Route::get('{usuario}/resetarsenha', 'Controllers\Admin\UsuariosController@getResetarSenha');

		Route::get('{usuario}/editar', 'Controllers\Admin\UsuariosController@getEditar');

		Route::post('{usuario}/editar', 'Controllers\Admin\UsuariosController@postEditar');

		Route::post('{usuario}/excluir', 'Controllers\Admin\UsuariosController@postExcluir');

		Route::get('{usuario}', 'Controllers\Admin\UsuariosController@getVisualizar');
	});

	/*******************
	/* CURSOS
	/******************/
	Route::group(array('prefix' => 'cursos'), function() 
	{
		Route::model('curso', 'Curso');

		Route::get('/', 'Controllers\Admin\CursosController@getIndex');

		Route::get('listar', 'Controllers\Admin\CursosController@getListar');

		Route::get('tipoatividadeusado/{tipo_atividade_codigo}', 'Controllers\Admin\CursosController@getTipoDeAtividadeUsado');

		// somente administrador possui permissão de inserir e excluir curso
		Route::group(array('before' => 'administrador'), function()
		{
			Route::get('inserir', 'Controllers\Admin\CursosController@getInserir');

			Route::post('inserir', 'Controllers\Admin\CursosController@postInserir');

			Route::post('{curso}/excluir', 'Controllers\Admin\CursosController@postExcluir');
		});

		Route::get('{curso}/editar', 'Controllers\Admin\CursosController@getEditar');

		Route::post('{curso}/editar', 'Controllers\Admin\CursosController@postEditar');

		Route::get('{curso}', 'Controllers\Admin\CursosController@getVisualizar');

		Route::get('{curso_codigo}/anexos', 'Controllers\Admin\CursosController@getListarAnexos');

		Route::post('{curso_codigo}/anexos', 'Controllers\Admin\CursosController@postEnviarAnexo');

		Route::post('{curso_codigo}/anexos/{anexo_codigo}/excluir', 'Controllers\Admin\CursosController@postExcluirAnexo');

		Route::get('{curso_codigo}/anexos/{anexo_codigo}/download', 'Controllers\Admin\CursosController@getDownloadAnexo');
	});
});

Route::group(array('before' => 'convidado_administrador'), function()
{
	/*******************
	/* CAMPI
	/******************/

	Route::group(array('prefix' => 'campi'), function() 
	{
		Route::model('campus', 'Campus');

		Route::get('/', 'Controllers\Admin\CampiController@getIndex');

		Route::get('listar', 'Controllers\Admin\CampiController@getListar');

		Route::get('inserir', 'Controllers\Admin\CampiController@getInserir');

		Route::post('inserir', 'Controllers\Admin\CampiController@postInserir');

		Route::get('{campus}/editar', 'Controllers\Admin\CampiController@getEditar');

		Route::post('{campus}/editar', 'Controllers\Admin\CampiController@postEditar');

		Route::post('{campus}/excluir', 'Controllers\Admin\CampiController@postExcluir');

		Route::get('{campus}', 'Controllers\Admin\CampiController@getVisualizar');
	});

	/*******************
	/* MOTIVOS DE RECUSA
	/******************/

	Route::group(array('prefix' => 'motivosrecusa'), function() 
	{
		Route::model('motivorecusa', 'MotivoRecusa');

		Route::get('/', 'Controllers\Admin\MotivosRecusaController@getIndex');

		Route::get('listar', 'Controllers\Admin\MotivosRecusaController@getListar');

		Route::get('inserir', 'Controllers\Admin\MotivosRecusaController@getInserir');

		Route::post('inserir', 'Controllers\Admin\MotivosRecusaController@postInserir');

		Route::get('{motivorecusa}/editar', 'Controllers\Admin\MotivosRecusaController@getEditar');

		Route::post('{motivorecusa}/editar', 'Controllers\Admin\MotivosRecusaController@postEditar');

		Route::post('{motivorecusa}/excluir', 'Controllers\Admin\MotivosRecusaController@postExcluir');

		Route::get('{motivorecusa}', 'Controllers\Admin\MotivosRecusaController@getVisualizar');
	});

	/*******************
	/* AUDITORIA
	/******************/

	Route::group(array('prefix' => 'auditoria'), function() 
	{
		Route::get('/', 'Controllers\Admin\AuditoriaController@getIndex');

		Route::get('listar', 'Controllers\Admin\AuditoriaController@getListar');
	});
});



/*******************
/* Páginas de erro
/******************/
App::error(function($exception, $code)
{
	if (Config::get('app.debug', false) === false)
	    switch ($code)
	    {
	        case 403:
	        	LogHandler::http403();
	            return Response::view('errors.403', array(), 403);

	        case 404:
	        	LogHandler::http404();
	            return Response::view('errors.404', array(), 404);

	        default:
	        	LogHandler::http500();
	            return Response::view('errors.default', array(), $code);
	    }
});