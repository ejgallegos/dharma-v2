<?php


class PlanView extends View {

	function panel($plan_collection) {
		$gui = file_get_contents("static/modules/plan/panel.html");

		foreach ($plan_collection as $clave=>$valor) {
			$conf_panel = $valor->conf_panel;
			$btn_activar_display = ($conf_panel == 1) ? "none" : "block";
			$btn_activo_display = ($conf_panel == 1) ? "block" : "none";
			$txt_small = ($conf_panel == 1) ? "Activo" : "";
			$valor->btn_activar_display = $btn_activar_display;
			$valor->btn_activo_display = $btn_activo_display;
			$valor->txt_small = $txt_small;
		}

		$render = $this->render_regex('TBL_PLAN', $gui, $plan_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function editar($plan_collection, $obj_plan) {
		$gui = file_get_contents("static/modules/plan/editar.html");
		$obj_plan = $this->set_dict($obj_plan);
		$render = $this->render_regex('TBL_PLAN', $gui, $plan_collection);
		$render = $this->render($obj_plan, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function home($cant_beneficio_localidad, $pago_localidad_collection, $dict_pagos, $localidad_collection, $obj_plan) {
		$gui = file_get_contents("static/modules/plan/home.html");
		$gui_tbl_cant_beneficio_localidad = file_get_contents("static/modules/beneficiario/tbl_cant_beneficio_localidad.html");
		$gui_slt_localidad = file_get_contents("static/common/slt_localidad.html");
		$gui_slt_localidad = $this->render_regex('SLT_LOCALIDAD', $gui_slt_localidad, $localidad_collection);
		
		$gui_barchart_pago_localidad = file_get_contents("static/modules/beneficiario/carga_barchart_pago_localidad.html");
		$gui_barchart_pago_localidad = $this->render_regex_dict('BARCHART_PAGO', $gui_barchart_pago_localidad, $pago_localidad_collection);
		
		list($cant_beneficio_localidad1, $cant_beneficio_localidad2) = array_chunk($cant_beneficio_localidad, ceil(count($cant_beneficio_localidad) / 2));
		$gui_tbl_cant_beneficio_localidad1 = $this->render_regex_dict('TBL_CANT_BENEFICIO_LOCALIDAD', $gui_tbl_cant_beneficio_localidad, $cant_beneficio_localidad1);
		$gui_tbl_cant_beneficio_localidad2 = $this->render_regex_dict('TBL_CANT_BENEFICIO_LOCALIDAD', $gui_tbl_cant_beneficio_localidad, $cant_beneficio_localidad2);

		$obj_plan = $this->set_dict($obj_plan);
		$render = str_replace('{carga_barchart_pago_localidad}', $gui_barchart_pago_localidad, $gui);
		$render = str_replace('{tbl_cant_beneficio_localidad1}', $gui_tbl_cant_beneficio_localidad1, $render);
		$render = str_replace('{tbl_cant_beneficio_localidad2}', $gui_tbl_cant_beneficio_localidad2, $render);
		$render = str_replace('{slt_localidad}', $gui_slt_localidad, $render);
		$render = $this->render($dict_pagos, $render);
		$render = $this->render($obj_plan, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>