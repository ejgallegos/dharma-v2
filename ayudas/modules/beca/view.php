<?php


class BecaView extends View {

	function panel($obj_plan, $beca_collection, $cant_beneficio_localidad, $pago_localidad_collection, $dict_pagos, $periodo) {
		$gui = file_get_contents("static/modules/beca/panel.html");
		$gui_tbl_beca = file_get_contents("static/modules/beca/tbl_beca.html");
		$gui_tbl_cant_beneficio_localidad = file_get_contents("static/modules/beneficiario/tbl_cant_beneficio_localidad.html");
		
		$gui_barchart_pago_localidad = file_get_contents("static/modules/beneficiario/carga_barchart_pago_localidad.html");
		$gui_barchart_pago_localidad = $this->render_regex_dict('BARCHART_PAGO', $gui_barchart_pago_localidad, $pago_localidad_collection);
		
		list($cant_beneficio_localidad1, $cant_beneficio_localidad2) = array_chunk($cant_beneficio_localidad, ceil(count($cant_beneficio_localidad) / 2));
		$gui_tbl_cant_beneficio_localidad1 = $this->render_regex_dict('TBL_CANT_BENEFICIO_LOCALIDAD', $gui_tbl_cant_beneficio_localidad, $cant_beneficio_localidad1);
		$gui_tbl_cant_beneficio_localidad2 = $this->render_regex_dict('TBL_CANT_BENEFICIO_LOCALIDAD', $gui_tbl_cant_beneficio_localidad, $cant_beneficio_localidad2);

		$obj_plan = $this->set_dict($obj_plan);
		$gui_tbl_beca = $this->render_regex_dict('TBL_BECA', $gui_tbl_beca, $beca_collection);
		$render = str_replace('{tbl_beca}', $gui_tbl_beca, $gui);
		$render = str_replace('{carga_barchart_pago_localidad}', $gui_barchart_pago_localidad, $render);
		$render = str_replace('{tbl_cant_beneficio_localidad1}', $gui_tbl_cant_beneficio_localidad1, $render);
		$render = str_replace('{tbl_cant_beneficio_localidad2}', $gui_tbl_cant_beneficio_localidad2, $render);
		$render = str_replace('{periodo}', $periodo, $render);
		$render = $this->render($dict_pagos, $render);
		$render = $this->render($obj_plan, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function agregar($localidad_collection, $tipobeca_collection, $obj_plan) {
		$gui = file_get_contents("static/modules/beca/agregar.html");
		$gui_slt_localidad = file_get_contents("static/common/slt_localidad.html");
		$gui_slt_tipobeca = file_get_contents("static/common/slt_tipobeca.html");

		$obj_plan = $this->set_dict($obj_plan);
		$gui_slt_localidad = $this->render_regex('SLT_LOCALIDAD', $gui_slt_localidad, $localidad_collection);
		$gui_slt_tipobeca = $this->render_regex('SLT_TIPOBECA', $gui_slt_tipobeca, $tipobeca_collection);
		$render = str_replace('{slt_localidad}', $gui_slt_localidad, $gui);
		$render = str_replace('{slt_tipobeca}', $gui_slt_tipobeca, $render);
		$render = $this->render($obj_plan, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function editar($localidad_collection, $tipobeca_collection, $obj_beca, $obj_plan) {
		$gui = file_get_contents("static/modules/beca/editar.html");
		$gui_lst_anexo = file_get_contents("static/modules/beca/lst_anexo.html");
		$gui_form_anexo = file_get_contents("static/modules/beca/form_agregar_anexo.html");
		$gui_slt_localidad = file_get_contents("static/common/slt_localidad.html");
		$gui_slt_tipobeca = file_get_contents("static/common/slt_tipobeca.html");

		$anexo_collection = $obj_beca->anexo_collection;
		unset($obj_beca->anexo_collection);
		
		if (empty($anexo_collection)) {
			$gui_lst_anexo = "";
		} else {
			$gui_lst_anexo = $this->render_regex('LST_ANEXO', $gui_lst_anexo, $anexo_collection);
			$gui_lst_anexo = str_replace('{display-editar_anexo}', 'block', $gui_lst_anexo);
		}

		$obj_beca = $this->set_dict($obj_beca);
		$gui_slt_localidad = $this->render_regex('SLT_LOCALIDAD', $gui_slt_localidad, $localidad_collection);
		$gui_slt_tipobeca = $this->render_regex('SLT_TIPOBECA', $gui_slt_tipobeca, $tipobeca_collection);
		$render = str_replace('{slt_localidad}', $gui_slt_localidad, $gui);
		$render = str_replace('{slt_tipobeca}', $gui_slt_tipobeca, $render);
		$render = str_replace('{lst_anexo}', $gui_lst_anexo, $render);
		$render = str_replace('{form_anexo}', $gui_form_anexo, $render);
		$render = $this->render($obj_beca, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function consultar($obj_beca) {
		$gui = file_get_contents("static/modules/beca/consultar.html");
		$gui_lst_anexo = file_get_contents("static/modules/beca/lst_anexo_sin_editar.html");
		
		$anexo_collection = $obj_beca->anexo_collection;
		unset($obj_beca->anexo_collection);
		
		if (empty($anexo_collection)) {
			$gui_lst_anexo = "";
		} else {
			$gui_lst_anexo = $this->render_regex('LST_ANEXO', $gui_lst_anexo, $anexo_collection);
			$gui_lst_anexo = str_replace('{display-editar_anexo}', 'none', $gui_lst_anexo);
		}

		$obj_beca = $this->set_dict($obj_beca);
		$render = str_replace('{lst_anexo}', $gui_lst_anexo, $gui);
		$render = $this->render($obj_beca, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function form_editar_anexo($obj_anexo, $plan_id) {
		$gui = file_get_contents("static/modules/beca/form_editar_anexo.html");
		$obj_anexo = $this->set_dict($obj_anexo);
		$render = $this->render($obj_anexo, $gui);
		$render = str_replace('{plan-plan_id}', $plan_id, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		print $render;
	}

	function consultar_anexo($obj_anexo) {
		$gui = file_get_contents("static/modules/beca/consultar_anexo.html");
		$obj_anexo = $this->set_dict($obj_anexo);
		$render = $this->render($obj_anexo, $gui);
		print $render;
	}
}
?>