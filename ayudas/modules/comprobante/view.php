<?php


class ComprobanteView extends View {
	function listado($comprobante_collection) {
		$gui = file_get_contents("static/modules/comprobante/listado.html");
		$gui_tbl_comprobante = file_get_contents("static/modules/comprobante/tbl_comprobante.html");
		$gui_tbl_comprobante = $this->render_regex_dict('TBL_COMPROBANTE', $gui_tbl_comprobante, $comprobante_collection);		
		$render = str_replace('{tbl_comprobante}', $gui_tbl_comprobante, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function agregar() {
		$gui = file_get_contents("static/modules/comprobante/agregar.html");		
		$render = $this->render_breadcrumb($gui);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($obj_comprobante) {
		$gui = file_get_contents("static/modules/comprobante/editar.html");
		$obj_comprobante = $this->set_dict($obj_comprobante);
		$render = $this->render($obj_comprobante, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function consultar($obj_comprobante) {
		$gui = file_get_contents("static/modules/comprobante/consultar.html");
		$obj_comprobante->comprobante = str_pad($obj_comprobante->punto_venta, 4, 0, STR_PAD_LEFT);
		$obj_comprobante->comprobante .= "-" . str_pad($obj_comprobante->numero, 8, 0, STR_PAD_LEFT);
		$obj_comprobante->estado_color = ($obj_comprobante->estado == 0) ? "red" : "blue";
		$obj_comprobante->estado_icon = ($obj_comprobante->estado == 0) ? "times" : "check";
		$obj_comprobante->display_abonar = ($obj_comprobante->estado == 0) ? "block" : "none";
		$obj_comprobante->estado = ($obj_comprobante->estado == 0) ? "Pendiente" : "Abonada";
		$obj_comprobante = $this->set_dict($obj_comprobante);
		$gui = $this->render($obj_comprobante, $gui);
		$gui = str_replace("{url_app}", URL_APP, $gui);
		print $gui;	
	}
}
?>