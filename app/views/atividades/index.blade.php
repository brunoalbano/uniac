@extends('layout/principal')

@section('content')
	<?php

		$baseUrl = url('atividades?status=' . $status . '&pesquisa=' . $pesquisa . '&page=');

		$anteriorUrl = $baseUrl . ($model->getCurrentPage() - 1);
		$anteriorAttr = $model->getCurrentPage() == 1 ? 'disabled' : '';

		$proximaUrl = $baseUrl . ($model->getCurrentPage() + 1);
		$proximaAttr = $model->getCurrentPage() == $model->getLastPage() ? 'disabled' : '';

		$primeiraUrl = $baseUrl;
		$primeiroClass = $model->getCurrentPage() == 1 ? 'disabled' : '';

		$ultimaUrl = $baseUrl . $model->getLastPage();
		$ultimaClass = $model->getCurrentPage() == $model->getLastPage() ? 'disabled' : '';
	?>

	<header>
		<div class="row">			
			<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 pull-right">

		        <form class="visible-xs" role="search" style="margin-bottom: 10px">
		            <div class="input-group">
		                <input type="search" class="form-control" placeholder="Pesquisar por título ou data" name="pesquisa" value="{{ $pesquisa }}" />
		                <input type="hidden" name="status" value="{{ $status }}" />
		                <span class="input-group-btn">
		                    <button class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
		                </span>
		            </div>
		        </form>

				<div class="btn-group btn-block">
			  		<a href="{{ $anteriorUrl }}" class="btn btn-default col-xs-3 col-sm-3 col-md-3" {{ $anteriorAttr }}>
			  			<span class="glyphicon glyphicon-chevron-left"></span>
			  		</a>
			  		<button type="button" class="btn btn-default col-xs-6 col-sm-6 col-md-6" data-toggle="dropdown">
			  			{{ $model->getFrom() }} - {{ $model->getTo() }} de {{ $model->getTotal() }}
			  		</button>
					<ul class="dropdown-menu" role="menu">
						<li class="{{ $primeiroClass }}"><a href="{{ $primeiraUrl }}">Primeira página</a></li>
						<li class="{{ $ultimaClass }}"><a href="{{ $ultimaUrl }}">Última página</a></li>
					</ul>
			  		<a href="{{ $proximaUrl }}" class="btn btn-default col-xs-3 col-sm-3 col-md-3" {{ $proximaAttr }}>
			  			<span class="glyphicon glyphicon-chevron-right"></span>
			  		</a>
				</div>
			</div>
		</div>
<!--
		<div class="row">		
			<div class="col-sm-12">
				<div class="well well-sm">
					<span class="label label-warning">&nbsp;&nbsp;</span> Exibindo as atividades que estão aguardando a sua correção.
				</div>
			</div>
		</div>
-->
	</header>

	<div class="atividades">

		@if(count($model) === 0)
			<article style="text-align: center">
				@if(empty($pesquisa))
					Sem atividades para exibir.
				@else
					Sem atividades para exibir na sua pesquisa.
				@endif
			</article>
		@endif

		@foreach($model as $atividade)

			<article data-codigo="{{ $atividade->codigo }}">
				<a href="{{ route('visualizarAtividade', array('atividade' => $atividade->codigo)) . '?status=' . $status . '&pesquisa=' . $pesquisa . '&page=' . $page }}">				
					<div class="row" style="padding-left: 3px; padding-right: 3px;">
						<div class="col-xs-8 col-md-3 col-lg-3"> 						
							<span class="label {{ Helpers::getStatusClass($atividade->status) }}" style="margin-right: 5px">&nbsp;&nbsp;</span>						
							{{ $atividade->UsuariosNomes() }}
						</div>

						<div class="hidden-xs hidden-sm col-md-7 col-lg-7">
							<strong>{{ $atividade->titulo }}</strong>
							<small>{{ $atividade->descricao }}</small>
						</div>

						<div class="col-xs-4 col-md-2 col-lg-2">
							<span class="pull-right">{{ Helpers::dateDescription($atividade->atualizado_em) }}</span>
						</div>
					</div>

					<div class="visible-xs visible-sm row" style="padding-left: 3px; padding-right: 3px;">
						<div class="col-xs-12 atividade-descricao">
							<strong>{{ $atividade->titulo }}</strong>
						</div>
					</div>

				</a>
			</article>

		@endforeach		
	</div>
@stop

@section('style')
	<style type="text/css">
		.atividades {
			margin-top: 10px;			
		}

		.atividades article {
			border-bottom: 1px solid #eee;
		}

		.atividades article .row{
			padding-top: 8px;
			padding-bottom: 8px;
		}

		.atividades article .label {
			border-radius: 4px;
		}

		.atividades article .row + .row{
			padding-top: 0px;
			padding-bottom: 2px;
		}

		.atividades article:first-child {
			margin-top: 6px;
			border-top: 1px solid #eee;
		}

		.atividades article:nth-child(2n+1) {
			background-color: #F9F9F9;
		}

		.atividades article:hover {
			background-color: #f6f6f6;
		}

		.atividades article a,
		.atividades article a:hover,
		.atividades article a:active,
		.atividades article a:visited {
			color: #333;
			text-decoration: none;
		}

		.atividades article small {
			color: gray;
		}
		/*
		.atividades article div:last-child{
			text-align: right;
		}*/

		.atividades article div{
			white-space: nowrap;
			overflow:hidden;
			text-overflow:ellipsis;
		}

		.atividade-descricao {
			
		}

		.well {
			margin-top: 10px;
			margin-bottom: 0px;
		}
	</style>
@stop