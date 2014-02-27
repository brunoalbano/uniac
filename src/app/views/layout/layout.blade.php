<?php 
    function printMenu($action, $label)
    {
        $currentController = Request::segment(1);

        $class = $currentController === $action ? 'class="active"' : '';

        return '<li ' . $class . '><a href="' . url($action) . '">' . $label . '</a></li>';
    }
?>



<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>{{ isset($titulo) ? $titulo . ' - ' : '' }}UNIAC - Atividades Complementares</title>
        <meta name="description" content="">
        <!--<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />-->


        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        {{ HTML::style('assets/css/bootstrap.min.css') }}

        {{ HTML::style('assets/jqwidgets/styles/jqx.base.css') }}

        {{ HTML::style('assets/css/jquery.fileupload.css') }}
        {{ HTML::style('assets/css/jquery.fileupload-ui.css') }}

        {{ HTML::style('assets/css/main.css') }}

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

        <style type="text/css">
            body {
                padding-top: 50px;
            }

            #jqxWidget {
                padding-top: 15px;
            }
        </style>

        @yield('style')
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{ url('atividades') }}">{{ Html::image('assets/img/logo-min.png', null, array("class"=>"logo-min")) }} UNIAC</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            {{ printMenu('usuarios', 'Usuários') }}
            {{ printMenu('turmas', 'Turmas') }}
            {{ printMenu('cursos', 'Cursos') }}
            @if(Auth::user()->administrador || Auth::user()->convidado)
            {{ printMenu('campi', 'Campi') }}
            {{ printMenu('motivosrecusa', 'Motivos de Recusa') }}
            {{ printMenu('auditoria', 'Auditoria') }}
            @endif
          </ul>
        
          @include('layout/botaousuario')
        </div><!--/.nav-collapse -->
      </div>
    </nav>

        <section class="container">        
            @if(isset($titulo))

            <header>
                <h3>{{ $titulo }}</h3>
                <hr/>
            </header>

            @else

            <header style="margin-bottom: 15px"></header>

            @endif

            @if(Session::has('error'))            
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Atenção!</strong> 
                    
                    @if(Session::has('reason')) 
                        {{ trans(Session::get('reason')) }} 
                    @else 
                        {{ Session::get('error') }} 
                    @endif
                </div>
            @endif

            @if(Session::has('success'))            
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Completado!</strong> {{ Session::get('success') }}
                </div>
            @endif

        	@yield('content')

            <footer class="hidden-print">
                <hr>

                <p>&copy; UNISAL 2014 <a class="pull-right" href="{{ url('sobre') }}">Sobre</a></p>
            </footer>

		</section> <!-- /container -->

        <!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
        <script>window.jQuery || document.write('<script src="{{ asset('assets/js/vendor/jquery-1.10.2.min.js') }}"><\/script>')</script>

        {{ HTML::script('assets/js/main.js') }}
        {{ HTML::script('assets/js/vendor/bootstrap.min.js') }}
        {{ HTML::script('assets/jqwidgets/jqxcore.js') }}
        {{ HTML::script('assets/jqwidgets/jqxbuttons.js') }}
        {{ HTML::script('assets/jqwidgets/jqxscrollbar.js') }}
        {{ HTML::script('assets/jqwidgets/jqxmenu.js') }}
        {{ HTML::script('assets/jqwidgets/jqxgrid.js') }}
        {{ HTML::script('assets/jqwidgets/jqxgrid.selection.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxgrid.sort.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxgrid.pager.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxgrid.filter.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxgrid.columnsresize.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxgrid.edit.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxdata.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxlistbox.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxdropdownlist.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxcheckbox.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxdragdrop.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxcombobox.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxpanel.js') }} 
        {{ HTML::script('assets/jqwidgets/jqxtree.js') }} 
        {{ HTML::script('assets/jqwidgets/globalization/globalize.js') }}
        {{ HTML::script('assets/jqwidgets/globalization/globalize.grid.pt-BR.js') }}

        {{ HTML::script('assets/js/vendor/jquery.validate.min.js') }}
        {{ HTML::script('assets/js/vendor/messages_pt_BR.js') }}

        {{ HTML::script('assets/js/vendor/jquery.ui.widget.js') }}
        {{ HTML::script('assets/js/vendor/jquery.fileupload.tmpl.min.js') }}
        {{ HTML::script('assets/js/vendor/jquery.fileupload.js') }}
        {{ HTML::script('assets/js/vendor/jquery.fileupload-process.js') }}
        {{ HTML::script('assets/js/vendor/jquery.fileupload-validate.js') }}
        {{ HTML::script('assets/js/vendor/jquery.fileupload-ui.js') }}
        {{ HTML::script('assets/js/vendor/jquery.iframe-transport.js') }}
        
        <?php
            $types = Config::get('anexos.formatospermitidos');
            $maxFileSize = Config::get('anexos.tamanhomaximo');
            $maxNumberOfFiles = Config::get('anexos.quantidademaxima');
        ?>

        <script>
            $(function(){
                // Configuração padrão para upload
                $.uniacFileuploadDefaultOptions({{ $maxFileSize }}, {{ json_encode($types) }}, {{ $maxNumberOfFiles }});

                // Resolve bug no scroll do jqxGrid com telas responsivas
                $(window).on('resize', function() {
                    $('.jqx-grid').jqxGrid('scrolloffset', 0, 0);
                });
            });
        </script>

        @yield('script')
    </body>
</html>