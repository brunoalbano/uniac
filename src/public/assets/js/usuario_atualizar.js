(function(){
	var UsuarioViewModel = function(cursos) {
	    var self = this;
	    self.cursos = ko.observableArray(cursos || []);
	 
	    self.inserirCurso = function() {
	        self.cursos.push({
	            curso_codigo: 0,
	            coordenador: false
	        });
	    };
	    
	    self.removerCurso = function(curso) {
	        self.cursos.remove(curso);
	    };
	    
	    self.limparCursos = function() {
	    	self.cursos.removeAll();
	    };
	};
	
	$(document).ready(function(){
		var model_cursos = window['model_cursos'] || [],
			USUARIO_PERFIL_SUPERVISOR = 4;
		
		var viewModel = new UsuarioViewModel(model_cursos);
		
		ko.applyBindings(viewModel);
		
		function onPerfilChange() {
			var perfil = $("#perfil"),
				controles = $(".supervisor-visivel");
			
			if (perfil.val() == USUARIO_PERFIL_SUPERVISOR)
				controles.show();
			else {
				controles.hide();
				viewModel.limparCursos();
			}			
		}
		
		$("#perfil").on("change", onPerfilChange);
		
		onPerfilChange();
	});
})();
