<?php


class ProveedorView extends View {
	
	function panel($proveedor_collection) {
		$gui = file_get_contents("static/modules/proveedor/panel.html");

		$render = $this->render_regex('TBL_PROVEEDOR', $gui, $proveedor_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($proveedor_collection, $obj_proveedor) {
		$gui = file_get_contents("static/modules/proveedor/editar.html");
		$obj_proveedor = $this->set_dict($obj_proveedor);
		$render = $this->render_regex('TBL_PROVEEDOR', $gui, $proveedor_collection);
		$render = $this->render($obj_proveedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>