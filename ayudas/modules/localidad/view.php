<?php


class LocalidadView extends View {
	
	function panel($localidad_collection) {
		$gui = file_get_contents("static/modules/localidad/panel.html");

		$render = $this->render_regex('TBL_LOCALIDAD', $gui, $localidad_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($localidad_collection, $obj_localidad) {
		$gui = file_get_contents("static/modules/localidad/editar.html");
		$obj_localidad = $this->set_dict($obj_localidad);
		$render = $this->render_regex('TBL_LOCALIDAD', $gui, $localidad_collection);
		$render = $this->render($obj_localidad, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>