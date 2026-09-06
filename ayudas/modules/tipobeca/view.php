<?php


class TipoBecaView extends View {
	
	function panel($tipobeca_collection) {
		$gui = file_get_contents("static/modules/tipobeca/panel.html");

		$render = $this->render_regex('TBL_TIPOBECA', $gui, $tipobeca_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($tipobeca_collection, $obj_tipobeca) {
		$gui = file_get_contents("static/modules/tipobeca/editar.html");
		$obj_tipobeca = $this->set_dict($obj_tipobeca);
		$render = $this->render_regex('TBL_TIPOBECA', $gui, $tipobeca_collection);
		$render = $this->render($obj_tipobeca, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>