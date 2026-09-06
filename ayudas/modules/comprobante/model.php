<?php
require_once "modules/proveedor/model.php";


class Comprobante extends StandardObject {
	
	function __construct(Proveedor $proveedor=NULL) {
		$this->comprobante_id = 0;
        $this->punto_venta = 0;
		$this->numero = 0;
		$this->fecha = '';
		$this->importe = 0.00;
        $this->detalle = '';
		$this->estado = 0;
		$this->proveedor = $proveedor;
	}
}
?>