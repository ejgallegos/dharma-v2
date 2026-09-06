<?php


class Cuota extends StandardObject {
	
	function __construct() {
		$this->cuota_id = 0;
		$this->fecha = '';
		$this->periodo = 0;
		$this->monto = 0.0;
		$this->descuento = 0.0;
		$this->cantidad = 0;
		$this->detalle = '';
	}
}
?>