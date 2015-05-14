<?php

    if (isset($status) === false)
        $status = 0;

    if (isset($pesquisa) === false)
        $pesquisa = '';

    if (isset($page) === false)
        $page = '';

	$printStatusOption = function ($description, $id) use($status) {
		$url = url('atividades') . "?status=$id";

		$active = $id === (int)$status && Request::segment(2) != 'relatorio' ? 'class="active"' : '';

		return "<li $active><a href=\"$url\">$description</a></li>";
	};

    $placeholderPesquisa = '';
    if (Auth::user()->aluno)
        $placeholderPesquisa = 'Pesquisar por título ou data';
    else
        $placeholderPesquisa = 'Pesquisar por título, data ou R.A.';
?>


<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>UNIAC - Atividades Complementares</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        {{ HTML::style('assets/css/bootstrap.min.css') }}

        {{ HTML::style('assets/css/main.css') }}

        {{ HTML::style('assets/css/jquery.fileupload.css') }}
        {{ HTML::style('assets/css/jquery.fileupload-ui.css') }}

        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
        <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png" />
        <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png" />
        <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png" />
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png" />
        <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-60x60.png" />
        <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-120x120.png" />
        <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-76x76.png" />
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-152x152.png" />

        <style type="text/css">
        	.menu-status > li.active:nth-child(1) > a {
        		background-color: #f0ad4e;
        	}

        	.menu-status > li.active:nth-child(2) > a {
        		background-color: #F0EA4E;
        	}

        	.menu-status > li.active:nth-child(3) > a {
        		background-color: #5cb85c;
        	}

        	.menu-status > li.active:nth-child(4) > a {
        		background-color: #d9534f;
        	}

            .menu-status > li.active:nth-child(5) > a {
                background-color: #39B3D7;
            }

        	.label {
        		color: black;
        		font-weight: normal;
        		font-size: .9em !important;
        	}

        </style>

        @yield('style')

        <style type="text/css">
            /*****************************************
            * Corrige problema com telas responsivas 
            * no IE 10 e Bootstrap 3.0.0
            *****************************************/

            @-webkit-viewport   { width: device-width; }
            @-moz-viewport      { width: device-width; }
            @-ms-viewport       { width: device-width; }
            @-o-viewport        { width: device-width; }
            @viewport           { width: device-width; }
        </style>

        <script type="text/javascript">
            /*****************************************
            * Corrige problema com telas responsivas 
            * no IE 10 e Bootstrap 3.0.0
            *****************************************/

            if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
              var msViewportStyle = document.createElement("style")
              msViewportStyle.appendChild(
                document.createTextNode(
                  "@-ms-viewport{width:auto!important}"
                )
              )
              document.getElementsByTagName("head")[0].appendChild(msViewportStyle)
            }
        </script>
        
        {{ HTML::script('assets/js/vendor/modernizr-2.7.1-respond-1.4.1.min.js') }}
    </head>
    <body>

        {{-- Barra superior --}}
        <nav class="navbar navbar-default navbar-fixed-top hidden-print" role="navigation">
            <div class="container">
                <div class="row">
                    <div class="col-sm-3 col-lg-2">
                        <div class="navbar-header">

                            {{-- Botão para telas muito pequenas --}}

                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-opcoes">
                                <span class="sr-only">Exibir opções</span>
                                <span class="glyphicon glyphicon-cog"></span>
                            </button>

                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-navegacao">
                                <span class="sr-only">Exibir navegação</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>

                            <a class="navbar-brand" href="{{ url('atividades') }}">{{ Html::image('assets/img/logo-min.png', null, array("class"=>"logo-min")) }} UNIAC</a>

                            @if (Auth::user()->convidado == false)
                            <button type="button" class="navbar-toggle pull-left btn-adicionar">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-9 col-lg-10">
                        <div class="row">

                            {{-- Barra de pesquisa --}}
                            <div class="col-sm-8 col-lg-9 hidden-xs">
                                <form class="navbar-form" role="search">
                                    <div class="input-group">
                                        <input type="search" class="form-control" placeholder="{{ $placeholderPesquisa }}" name="pesquisa" value="{{ $pesquisa }}" />
                                        <input type="hidden" name="status" value="{{ $status }}" />
                                        <span class="input-group-btn">
                                            <button class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
                                        </span>
                                    </div>
                                </form>
                            </div>

                            {{-- Sub menu --}}
                            <div class="col-sm-4 col-lg-3">

                                <div class="collapse navbar-collapse" id="navbar-navegacao">
                                    {{-- Sub menu para telas muito pequenas --}}
                                    <ul class="nav navbar-nav visible-xs">
                                        {{ $printStatusOption('Para Corrigir', Atividade::AGUARDANDO_CORRECAO) }}
                                        {{ $printStatusOption('Em Avaliação', Atividade::AGUARDANDO_AVALIACAO) }}
                                        {{ $printStatusOption('Aceitas', Atividade::ACEITA) }}
                                        {{ $printStatusOption('Recusadas', Atividade::RECUSADA) }}
                                        {{ $printStatusOption('Todas', 0) }}
                                        <li><hr/></li>

                                        @if(Auth::user()->aluno)
                                            <li class="{{ Request::segment(2) == 'relatorio' ? 'active' : '' }}"><a href="{{ url('atividades/relatorio/matricula/' . Session::get('matricula_codigo')) }}">Relatório</a></li>
                                        @elseif(Auth::user()->convidado || Auth::user()->administrador || Auth::user()->coordenador)
                                            <li class="{{ Request::segment(2) == 'relatorio' ? 'active' : '' }}"><a href="{{ url('atividades/relatorio/turma') }}">Relatórios</a></li>
                                        @endif
                                    </ul>

                                </div>

                                @include('layout/botaousuario')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
                
        <section class="container" id="principal-container">

        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

            <div style="margin-top: 60px" class="hidden-print">
                <div style="margin-top: 70px" class="visible-xs"></div>
            </div>

			<div class="row">
				<aside class="col-sm-3 col-md-2 col-lg-2 hidden-xs hidden-print">
                <div data-spy="affix" style="width: 150px">

                    @if (Auth::user()->convidado == false)
					<a href="{{ url('atividades/criar') }}" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-plus"></span> Criar atividade</a>

					<br/>
                    @endif

					<nav>
						<ul class="menu-status nav nav-pills nav-stacked">
							{{ $printStatusOption('Para Corrigir', Atividade::AGUARDANDO_CORRECAO) }}
							{{ $printStatusOption('Em Avaliação', Atividade::AGUARDANDO_AVALIACAO) }}
							{{ $printStatusOption('Aceitas', Atividade::ACEITA) }}
							{{ $printStatusOption('Recusadas', Atividade::RECUSADA) }}
							{{ $printStatusOption('Todas', 0) }}
                            <li><hr></li>

                                @if(Auth::user()->aluno)
                                    <li class="{{ Request::segment(2) == 'relatorio' ? 'active' : '' }}"><a href="{{ url('atividades/relatorio/matricula/' . Session::get('matricula_codigo')) }}">Relatório</a></li>
                                @elseif(Auth::user()->convidado || Auth::user()->administrador || Auth::user()->coordenador)
                                    <li class="{{ Request::segment(2) == 'relatorio' ? 'active' : '' }}"><a href="{{ url('atividades/relatorio/turma') }}">Relatórios</a></li>
                                @endif
						</ul>	
					</nav>
					
					<br/>

                    @if(Auth::user()->aluno)

                    <?php 
                        $matricula = Matricula::find(Session::get('matricula_codigo'));
                    ?>
					<section class="panel panel-info">
						<div class="panel-heading">Saldo de horas</div>
						<div class="panel-body">
							<p>Aceitas: <strong>{{ (int)$matricula->horas_aceitas }}</strong></p>
							<p>Faltando: <strong>{{ (int)$matricula->horas_faltando }}</strong></p>
						</div>
					</section>
                    @endif
                    </div>

				</aside>

				<section class="col-sm-9 col-md-10 col-lg-10" style="min-height: 500px">
					
		            @if(Session::has('success') && Session::get('success') !== '')            
		                <div class="alert alert-info alert-dismissable">
		                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		                    <strong>Completado!</strong> {{ Session::get('success') }}
		                </div>
		            @endif

					@yield('content')

				</section>
			</div>
            
            <footer class="hidden-print">
                <hr>

                <p>&copy; UNISAL 2015 <a class="pull-right" href="{{ url('sobre') }}">Sobre</a></p>
            </footer>

		</section> <!-- /container -->

<!--        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
<!--        <script>window.jQuery || document.write('<script src="{{ asset('assets/js/vendor/jquery-1.10.2.min.js') }}"><\/script>')</script>-->
        {{ HTML::script('assets/js/vendor/jquery-1.10.2.min.js') }}

        {{ HTML::script('assets/js/main.js') }}
        {{ HTML::script('assets/js/vendor/bootstrap.min.js') }}
        
        {{ HTML::script('assets/js/vendor/jquery.ui.widget.js') }}
        {{ HTML::script('assets/js/vendor/jquery.fileupload.tmpl.min.js') }}
        {{ HTML::script('assets/js/vendor/jquery.fileupload.js') }}
        {{ HTML::script('assets/js/vendor/jquery.fileupload-process.js') }}
        {{ HTML::script('assets/js/vendor/jquery.fileupload-validate.js') }}
        {{ HTML::script('assets/js/vendor/jquery.fileupload-ui.js') }}
        {{ HTML::script('assets/js/vendor/jquery.iframe-transport.js') }}

        {{ HTML::script('assets/js/vendor/jquery.appear.js') }}

        <?php
            $types = Config::get('anexos.formatospermitidos');
            $maxFileSize = Config::get('anexos.tamanhomaximo');
            $maxNumberOfFiles = Config::get('anexos.quantidademaxima');
        ?>

        <script>
            $(function(){
                $.uniacFileuploadDefaultOptions({{ $maxFileSize }}, {{ json_encode($types) }}, {{ $maxNumberOfFiles }});
            });

            $('.btn-adicionar').on('click', function(){
                window.location.href = '{{ url('atividades/criar') }}';
            });
        </script>
         
        @yield('script')
    </body>
</html>