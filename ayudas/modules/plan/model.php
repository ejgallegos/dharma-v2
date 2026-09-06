<?php


class Plan extends StandardObject {
	
	function __construct() {
		$this->plan_id = 0;
		$this->denominacion = '';
		$this->fecha = '';
        $this->detalle = 0;
        $this->conf_panel = 0;
	}
}
?>