
<div class="modal fade" id="modalTipoAtividade" tabindex="-1" role="dialog" aria-labelledby="labelTipoAtividade" aria-hidden="true">
    <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Selecionar Tipo de Atividade</h4>
			</div>
			<div class="modal-body">

				<div id="modal-content">
					
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
			</div>
		</div>
    </div>
</div>


@section('script')

@parent

<script>
	var TipoAtividade = function() {
		var oPublic = {};

		var _callback = null;

		var desenhar = function (container, data, codigoPai) {
			codigoPai = codigoPai || '';
			var html = '';

			for(var index in data) {				
				var item = data[index],
					possuiItens = item.possuiItens;

				if (possuiItens) {
					var idCollapse = 'collapse' + item.codigo;

					html +=
						'<div class="panel panel-default">' +
						'	<div class="panel-heading un-tipo-descricao collapsed" data-toggle="collapse" href="#' + idCollapse + '">' +
						'		<span>' + item.titulo + '</span>' + '<small> (limite: ' + item.horas + ' no total)</small>' +
						'	</div>' +
						'</div>' +
						'<div id="' + idCollapse + '" class="collapse" style="margin-left: 10px" data-codigo="' + item.codigo + '">' +
						'	Carregando...' +
						'</div>';
				}
				else {
					html +=
						'<div class="panel panel-default">' +
						'	<div class="panel-heading" data-codigo="' + item.codigo + '" data-carregado="' + !possuiItens + '" data-horas="' + item.horas + '" >' +
						'		<div class="row">' +
						'			<div class="col-xs-10 un-tipo-descricao">' +
						'				<span>' + item.titulo + '</span>' + '<small> (limite: ' + item.horas + ' por ativ.)</small>' +
						'			</div>' +
						'			<div class="col-xs-2">' +
						'       		<a class="btn btn-primary btn-sm pull-right selecionarTipo"><span class="glyphicon glyphicon-ok"></span></a>' +
						'			</div>' +
						'		</div>' +
						'	</div>' +
						'</div>';
				}
			}

			var htmlContainer = container;

			var body = container.find('.panel-body');
			if (body.length)
				htmlContainer = body;

			var div = $(html);

			htmlContainer.html(div);
		}

		var obterItems = function(pai, callBack) {
			var url = "{{ url('atividades/listararvore')}}";
			var urlParam = url;

			var data = {
				tipoatividade: pai
			};

			var turma = $('#turma').val();
			if (turma)
				data.turma = turma;

			$.ajax({
				method: 'GET',
				url: urlParam,
				cache: false,
				data: data,
				dataType: 'json'	
			})
			.done(callBack);
		}

		var carregar = function(container, codigo) {
			obterItems(codigo, function(data) {
				desenhar(container, data, codigo);
			});
		}

		var onClickTipoAtividadeExpandir = function(e) {
			var $target = $(e.target),
				carregado = $target.data('carregado') || false;

			if (carregado == false) {
				var codigo = $target.data('codigo');
				carregar($target, codigo);

				$target.data('carregado', true);				
			}
		}

		var onClickSelecionarTipoAtividade = function(e) {
			e.stopPropagation();
			
			var $target = $(e.target),
				$panel = $target.closest('.panel-heading');

			var data = {
				titulo: $.trim($panel.find('.un-tipo-descricao span').html()),
				codigo: $panel.data('codigo'),
				horas: $panel.data('horas')
			};

			_callback(data);

			$('#modalTipoAtividade').modal('hide');
		}

		oPublic.open = function (callback) {
			_callback = callback;

			var container = $('#modal-content');
			
			container.html('<div>Carregando...</div>');

			$('#modalTipoAtividade').modal('show');

			carregar(container);
		}

		oPublic.init = function() {
			$('#modalTipoAtividade').on('show.bs.collapse', onClickTipoAtividadeExpandir);

			$('#modalTipoAtividade').on('click', '.selecionarTipo', onClickSelecionarTipoAtividade);
		}

		return oPublic;
	}();
</script>

@stop