<?php
require_once "modules/cuota/model.php";
require_once "modules/cuota/view.php";


class CuotaController {

	function __construct() {
		$this->model = new Cuota();
		$this->view = new CuotaView();
	}
}
?>