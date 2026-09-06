<?php
require_once "modules/anexo/model.php";
require_once "modules/anexo/view.php";


class AnexoController {

	function __construct() {
		$this->model = new Anexo();
		$this->view = new AnexoView();
	}
}
?>