<?php
require_once "modules/proveedor/model.php";
require_once "modules/proveedor/view.php";


class ProveedorController {

	function __construct() {
		$this->model = new Proveedor();
		$this->view = new ProveedorView();
	}

	function panel() {
    	SessionHandler()->check_session();
		
		$proveedor_collection = Collector()->get('Proveedor');
		$this->view->panel($proveedor_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/proveedor/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		
		$this->model->proveedor_id = $arg;
		$this->model->get();
		$proveedor_collection = Collector()->get('Proveedor');
		$this->view->editar($proveedor_collection, $this->model);
	}

	function verifica_proveedor($arg) {
		$pm = new Proveedor();
		$select = "CONCAT(proveedor_id, '_', denominacion) AS PROVEEDOR";
		$from = "proveedor";
		$where = "cuit = {$arg}";
		$proveedor = CollectorCondition()->get('Proveedor', $where, 4, $from, $select);
		$res = ($proveedor == 0) ? 0 : $proveedor[0]["PROVEEDOR"];
		print_r($res);
	}

	function guardar_ajax() {
		$this->model->denominacion = filter_input(INPUT_POST, "denominacion");
		$this->model->cuit = filter_input(INPUT_POST, "cuit");
		$this->model->detalle = "";
        $this->model->save();
	}
}
?>
