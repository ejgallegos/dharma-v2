<?php
require_once "modules/tipobeca/model.php";
require_once "modules/tipobeca/view.php";


class TipoBecaController {

	function __construct() {
		$this->model = new TipoBeca();
		$this->view = new TipoBecaView();
	}

	function panel() {
    	SessionHandler()->check_session();
		
		$tipobeca_collection = Collector()->get('TipoBeca');
		$this->view->panel($tipobeca_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/tipobeca/panel");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();

		if (has_related_records("SELECT COUNT(*) AS total FROM beca WHERE tipobeca = ?", array($arg))) {
			set_app_message("No se puede eliminar el tipo de beca porque tiene resoluciones asociadas.");
			header("Location: " . URL_APP . "/tipobeca/panel");
			return;
		}

		$this->model->tipobeca_id = $arg;
		$this->model->delete();
		header("Location: " . URL_APP . "/tipobeca/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		
		$this->model->tipobeca_id = $arg;
		$this->model->get();
		$tipobeca_collection = Collector()->get('TipoBeca');
		$this->view->editar($tipobeca_collection, $this->model);
	}
}
?>
