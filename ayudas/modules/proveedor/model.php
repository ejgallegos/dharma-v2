<?php


class Proveedor extends StandardObject {
	
	function __construct() {
		$this->proveedor_id = 0;
		$this->denominacion = '';
		$this->cuit = 0;
		$this->detalle = '';
	}
}
?>