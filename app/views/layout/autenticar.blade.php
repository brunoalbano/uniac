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
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        {{ HTML::style('assets/css/bootstrap.min.css') }}

        {{ HTML::style('assets/jqwidgets/styles/jqx.base.css') }}

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
    </head>

<style type="text/css">
    body {
      padding-top: 40px;
      padding-bottom: 40px;
      background-color: #eee;
    }

    .form-signin {
      max-width: 330px;
      padding: 15px;
      margin: 0 auto;
    }
    .form-signin .form-signin-heading,
    .form-signin .checkbox {
      margin-bottom: 10px;
    }
    .form-signin .checkbox {
      font-weight: normal;
    }
    .form-signin .form-control {
      position: relative;
      font-size: 16px;
      height: auto;
      padding: 10px;
      -webkit-box-sizing: border-box;
         -moz-box-sizing: border-box;
              box-sizing: border-box;
    }
    .form-signin .form-control:focus {
      z-index: 2;
    }
</style>

@yield('style')

<body>

    <div class="container form-signin">
      <center>
        {{ Html::image('assets/img/logo.png') }}
      </center>          
            <h3 class="form-signin-heading">@yield('title')</h3>

            @if(Session::has('error') || empty($error) === false)
                <div class="alert alert-danger">
                  @if (Session::has('reason'))
                    {{ trans(Session::get('reason')) }}
                  @else
                    {{ Session::get('error') }}
                    {{ empty($error) === false ? $error : '' }}
                  @endif
                </div>
            @elseif(Session::has('success') || empty($success) === false)
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                    {{ empty($success) === false ? $success : '' }}
                </div>
            @endif

            @yield('content')

    </div> <!-- /container -->

@yield('script')

</body>
</html>