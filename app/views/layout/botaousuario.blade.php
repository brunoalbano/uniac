
<div class="collapse navbar-collapse" id="navbar-opcoes">
    
    <ul class="nav navbar-nav navbar-right">                
        <li class="dropdown">
            {{-- Título submenu --}}
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <div class="un-navbar-usuario">
                    <span class="glyphicon glyphicon-user"></span> {{ Auth::user()->primeiro_nome }}
                </div>
            </a>

            {{-- Opções submenu --}}
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{ url('configuracoes') }}"><span class="glyphicon glyphicon-cog"></span> Configurações</a></li>

                @if (Auth::user()->aluno)
                    <li><a href="{{ url('listarmatriculas') }}"><span class="glyphicon glyphicon-list"></span> Selecionar matrícula</a></li>
                @endif

                @if (Auth::user()->administrador || Auth::user()->coordenador || Auth::user()->convidado)
                    <li><a href="{{ url('atividades') }}"><span class="glyphicon glyphicon-list"></span> Atividades</a></li>
                @endif

                @if (Auth::user()->administrador || Auth::user()->coordenador || Auth::user()->convidado)
                    <li><a href="{{ url('usuarios') }}"><span class="glyphicon glyphicon-wrench"></span> Administração</a></li>
                @endif

                <li class="divider"></li>
                <li><a href="{{ url('sair') }}"><span class="glyphicon glyphicon-log-out"></span> Sair</a></li>
                <li><a href="{{ url('ajuda') }}"><span class="glyphicon glyphicon-question-sign"></span> Ajuda</a></li>
            </ul>
        </li>
    </ul>

</div>