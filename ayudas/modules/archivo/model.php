<?php


class Archivo extends StandardObject {
	
	function __construct() {
		$this->archivo_id = 0;
		$this->denominacion = '';
		$this->url = '';
		$this->fecha_carga = '';
		$this->formato = '';
	}
}
?>