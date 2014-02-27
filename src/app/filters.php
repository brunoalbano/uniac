<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('autenticar');

	if (Auth::user()->aluno && Session::has('matricula_codigo') === false)
		return Redirect::to('listarmatriculas');
});


Route::filter('administrador', function($route, $request)
{
	if (Auth::guest()) return Redirect::guest('autenticar');

	$action = $route->getAction();

	$usuario = Auth::user();

	if (empty($usuario) || $usuario->administrador === false)
		App::abort('403');
});

Route::filter('aluno', function($route, $request)
{
	if (Auth::guest()) return Redirect::guest('autenticar');

	$action = $route->getAction();

	$usuario = Auth::user();

	if (empty($usuario) || $usuario->aluno === false)
		App::abort('403');
});

Route::filter('coordenador_administrador', function($route, $request)
{
	if (Auth::guest()) return Redirect::guest('autenticar');

	$action = $route->getAction();

	$usuario = Auth::user();

	if (empty($usuario) || 
		$usuario->administrador === false && $usuario->coordenador === false)
		App::abort('403');
});

Route::filter('supervisor_administrador', function($route, $request)
{
	if (Auth::guest()) return Redirect::guest('autenticar');

	$action = $route->getAction();

	$usuario = Auth::user();

	if (empty($usuario) || 
		$usuario->administrador === false && $usuario->supervisor === false)
		App::abort('403');
});

Route::filter('coordenador_convidado_administrador', function($route, $request)
{
	if (Auth::guest()) return Redirect::guest('autenticar');

	$action = $route->getAction();

	$usuario = Auth::user();

	// Convidado possui permissão apenas de visualização
	$permicoesConvidado = '/(Index|Listar|Visualizar|ListarAnexos|DownloadAnexo)$/';

	if (empty($usuario) || 
		$usuario->administrador === false && $usuario->convidado === false && $usuario->coordenador === false ||
		$usuario->convidado && preg_match($permicoesConvidado, $action) === false)
		App::abort('403');
});

// Convidado ou Administrador
Route::filter('convidado_administrador', function($route, $request)
{
	if (Auth::guest()) return Redirect::guest('autenticar');

	$action = $route->getAction();

	$usuario = Auth::user();

	// Convidado possui permissão apenas de visualização
	$permicoesConvidado = '/(Index|Listar|Visualizar|ListarAnexos|DownloadAnexo)$/';

	if (empty($usuario) || 
		$usuario->administrador === false && $usuario->convidado === false ||
		$usuario->convidado && preg_match($permicoesConvidado, $action) === false)
		App::abort('403');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/*
|--------------------------------------------------------------------------
| XSS Protection Filter
|---------------------------------------------------------------------
|
*/

Route::filter('xss', function()
{
	//Helpers::globalXssClean();
});