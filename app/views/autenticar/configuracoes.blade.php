@extends('layout/principal')

@section('content')

    @if(Session::has('error') || Session::has('errors'))            
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Atenção!</strong> Corrija os erros para continuar
        </div>
    @endif

    <header>
    	<h3>Configurações</h3>
    	<hr>
    </header>
    
    {{ BForm::open() }}
    <section>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Notificações</h3>
			</div>
			<div class="panel-body">
				{{ BForm::radio('notificar', 'Enviar e-mails de notificações ao atualizar atividades.', 1, (int)$model->notificar === 1) }}

				{{ BForm::radio('notificar', 'Não enviar e-mails de notificações.', 0, (int)$model->notificar === 0) }}
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Alterar dados pessoais</h3>
			</div>
			<div class="panel-body">
				{{ BForm::email('email', 'E-mail', $model->email, array('placeholder' => 'endereco@email.com', 'required')) }}

				{{ BForm::password('senha', 'Nova senha') }}

				{{ BForm::password('confirmar_senha', 'Confirmar nova senha') }}
			</div>
		</div>

		<button class="btn btn-large btn-primary" type="submit">Salvar</button>
    </section>
	{{ BForm::close() }}
@stop


@section('script')

@stop