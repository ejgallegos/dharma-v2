<?php
require_once "modules/beca/model.php";
require_once "modules/beca/view.php";
require_once "modules/localidad/model.php";
require_once "modules/tipobeca/model.php";
require_once "modules/anexo/model.php";


class BecaController {

	function __construct() {
		$this->model = new Beca();
		$this->view = new BecaView();
	}

	function panel() {
    	SessionHandler()->check_session();
		
    	$select_beca = "b.beca_id AS BECA_ID, b.denominacion AS DENOMINACION, b.resolucion AS RESOLUCION, 
						tb.denominacion AS TIPOBECA, l.denominacion AS LOCALIDAD, DATE_FORMAT(b.fecha, '%Y') AS ANIO";
		$from_beca = "beca b INNER JOIN localidad l ON b.localidad = l.localidad_id INNER JOIN 
					  tipobeca tb ON b.tipobeca = tb.tipobeca_id";
		$beca_collection = CollectorCondition()->get('Beca', NULL, 4, $from_beca, $select_beca);

		$select_count_localidad = "COUNT(b.localidad) AS CANT_LOCALIDAD, l.denominacion AS LOCALIDAD";
		$from_count_localidad = "beca b INNER JOIN localidad l ON b.localidad = l.localidad_id";
		$groub_by_count_localidad = "b.localidad";
		$count_localidad = CollectorCondition()->get('Beca', NULL, 4, $from_count_localidad, $select_count_localidad, $groub_by_count_localidad);

		$select_count_tipobeca = "COUNT(b.tipobeca) AS CANT_TIPOBECA, tb.denominacion AS TIPOBECA";
		$from_count_tipobeca = "beca b INNER JOIN tipobeca tb ON b.tipobeca = tb.tipobeca_id";
		$groub_by_count_tipobeca = "b.tipobeca";
		$count_tipobeca = CollectorCondition()->get('Beca', NULL, 4, $from_count_tipobeca, $select_count_tipobeca, $groub_by_count_tipobeca);
		
		$this->view->panel($beca_collection, $count_localidad, $count_tipobeca);
	}

	function agregar() {
    	SessionHandler()->check_session();
		
		$localidad_collection = Collector()->get('Localidad');
		$tipobeca_collection = Collector()->get('TipoBeca');
		$this->view->agregar($localidad_collection, $tipobeca_collection);
	}

	function consultar($arg) {
		SessionHandler()->check_session();
		
		$this->model->beca_id = $arg;
		$this->model->get();
		$this->view->consultar($this->model);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		
		$this->model->beca_id = $arg;
		$this->model->get();
		$localidad_collection = Collector()->get('Localidad');
		$tipobeca_collection = Collector()->get('TipoBeca');
		$this->view->editar($localidad_collection, $tipobeca_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();
		
		foreach ($_POST as $key=>$value) $this->model->$key = $value;
		$this->model->save();
		$beca_id = $this->model->beca_id;
		header("Location: " . URL_APP . "/beca/editar/{$beca_id}");
	}

	function asociar_anexo() {
		SessionHandler()->check_session();

		$am = new Anexo();
		$am->denominacion = filter_input(INPUT_POST, 'denominacion');
		$am->monto = filter_input(INPUT_POST, 'monto');
		$am->detalle = filter_input(INPUT_POST, 'detalle');
		$am->save();
		$anexo_id = $am->anexo_id;

		$beca_id = filter_input(INPUT_POST, 'beca_id');
		$this->model->beca_id = $beca_id;
		$this->model->get();

		$am = new Anexo();
		$am->anexo_id = $anexo_id;
		$am->get();
		$this->model->add_anexo($am);

		$abm = new AnexoBeca($this->model);
		$abm->save();

		header("Location: " . URL_APP . "/beca/editar/{$beca_id}");		
	}

	function form_editar_anexo($arg) {
		$am = new Anexo();
		$am->anexo_id = $arg;
		$am->get();
		$this->view->form_editar_anexo($am);
	}

	function actualizar_anexo() {
		$beca_id = filter_input(INPUT_POST, 'beca_id');
		$am = new Anexo();
		$am->anexo_id = filter_input(INPUT_POST, 'anexo_id');
		$am->denominacion = filter_input(INPUT_POST, 'denominacion');
		$am->monto = filter_input(INPUT_POST, 'monto');
		$am->detalle = filter_input(INPUT_POST, 'detalle');
		$am->save();
		header("Location: " . URL_APP . "/beca/editar/{$beca_id}");		
	}

	function consultar_anexo($arg) {
		$am = new Anexo();
		$am->anexo_id = $arg;
		$am->get();
		$this->view->consultar_anexo($am);
	}
}
?>