<?php
require_once "modules/comprobante/model.php";
require_once "modules/comprobante/view.php";
require_once "modules/proveedor/model.php";


class ComprobanteController {

	function __construct() {
		$this->model = new Comprobante();
		$this->view = new ComprobanteView();
	}

	function listado() {
    	SessionHandler()->check_session();
    	$select = "c.comprobante_id AS COMPROBANTE_ID, CONCAT(LPAD(c.punto_venta, 4, '0'), '-', LPAD(c.numero, 8, '0')) AS COMPROBANTE, p.cuit AS CUIT, 
    			   p.denominacion AS PROVEEDOR, CONCAT('$', c.importe) AS IMPORTE, DATE_FORMAT(c.fecha, '%d/%m/%Y') AS FECHA, CASE WHEN c.estado = 0 
    			   THEN 'Pendiente' ELSE 'Pagado' END AS ESTADO, CASE WHEN c.estado = 0 THEN 'red' ELSE 'blue' END AS CLR_EST";
		$from = "comprobante c INNER JOIN proveedor p ON c.proveedor = p.proveedor_id";
		$comprobante_collection = CollectorCondition()->get('Comprobante', NULL, 4, $from, $select);
		$this->view->listado($comprobante_collection);
	}

	function agregar() {
    	SessionHandler()->check_session();
		$this->view->agregar();
	}

	function guardar() {
		SessionHandler()->check_session();
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
		unset($this->model->cuit);
		$this->model->save();
		header("Location: " . URL_APP . "/comprobante/listado");
	}

	function editar($arg) {
    	SessionHandler()->check_session();
		$this->model->comprobante_id = $arg;
		$this->model->get();
		$this->view->editar($this->model);
	}	

	function consultar($arg) {
		$this->model->comprobante_id = $arg;
		$this->model->get();
		$this->view->consultar($this->model);
	}

	function abonar() {
		$this->model->comprobante_id = filter_input(INPUT_POST, "comprobante_id");
		$this->model->get();
		$this->model->estado = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/comprobante/listado");
	}

	function verifica_comprobante($arg) {
    	$array = explode("@", $arg);

    	$proveedor_id = $array[0];
    	$punto_venta = $array[1];
    	$numero = $array[2];
    	
		$select = "COUNT(*) AS DUPLICADO";
		$from = "comprobante c";
		$where = "c.proveedor = {$proveedor_id} AND c.punto_venta = {$punto_venta} AND c.numero = {$numero}";
		$flag = CollectorCondition()->get('Comprobante', $where, 4, $from, $select);
		print $flag[0]["DUPLICADO"];
	}
}
?>
