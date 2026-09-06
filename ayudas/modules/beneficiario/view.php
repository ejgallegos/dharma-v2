<?php


class BeneficiarioView extends View {
	function panel($localidad_collection, $pago_collection, $cantidad_pagos, $cant_beneficio_localidad) {
		$gui = file_get_contents("static/modules/beneficiario/panel.html");
		$gui_tbl_cant_beneficio_localidad = file_get_contents("static/modules/beneficiario/tbl_cant_beneficio_localidad.html");
		$gui_piechart_localidad = file_get_contents("static/modules/beneficiario/carga_piechart_localidad.html");

		$gui_barchart_pago_localidad = file_get_contents("static/modules/beneficiario/carga_barchart_pago_localidad.html");
		$gui_barchart_pago_localidad = $this->render_regex_dict('BARCHART_PAGO', $gui_barchart_pago_localidad, $pago_collection);
		
		$gui_slt_localidad = file_get_contents("static/common/slt_localidad.html");
		$gui_slt_localidad = $this->render_regex('SLT_LOCALIDAD', $gui_slt_localidad, $localidad_collection);
		
		list($cant_beneficio_localidad1, $cant_beneficio_localidad2) = array_chunk($cant_beneficio_localidad, ceil(count($cant_beneficio_localidad) / 2));
		$monto_total = 0;
		foreach ($pago_collection as $clave=>$valor) $monto_total = $monto_total + $valor["MONTO"];

		$mayor = reset($pago_collection);
		$mayor_total = $mayor["MONTO"];
		$localidad_mayor_total = $mayor["LOCALIDAD"];
		$menor = end($pago_collection);
		$menor_total = $menor["MONTO"];
		$localidad_menor_total = $menor["LOCALIDAD"];
		$dict_pagos = array("{total_pago}"=>$monto_total,
							"{localidad_mayor_total}"=>$localidad_mayor_total,
							"{mayor_total}"=>$mayor_total,
							"{localidad_menor_total}"=>$localidad_menor_total,
							"{menor_total}"=>$menor_total,
							"{cantidad_pagos}"=>$cantidad_pagos[0]["CANTIDAD"]);


		$gui_tbl_cant_beneficio_localidad1 = $this->render_regex_dict('TBL_CANT_BENEFICIO_LOCALIDAD', $gui_tbl_cant_beneficio_localidad, $cant_beneficio_localidad1);
		$gui_tbl_cant_beneficio_localidad2 = $this->render_regex_dict('TBL_CANT_BENEFICIO_LOCALIDAD', $gui_tbl_cant_beneficio_localidad, $cant_beneficio_localidad2);
		$gui_piechart_localidad = $this->render_regex_dict('PIECHART_CANTIDAD', $gui_piechart_localidad, $cant_beneficio_localidad);
		$gui_piechart_localidad = $this->render_regex_dict('PIECHART_LOCALIDAD', $gui_piechart_localidad, $cant_beneficio_localidad);
		$render = str_replace('{slt_localidad}', $gui_slt_localidad, $gui);
		$render = str_replace('{carga_barchart_pago_localidad}', $gui_barchart_pago_localidad, $render);
		$render = str_replace('{tbl_cant_beneficio_localidad1}', $gui_tbl_cant_beneficio_localidad1, $render);
		$render = str_replace('{tbl_cant_beneficio_localidad2}', $gui_tbl_cant_beneficio_localidad2, $render);
		$render = str_replace('{carga_piechart_localidad}', $gui_piechart_localidad, $render);
		$render = $this->render($dict_pagos, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function listado($beneficiario_collection) {
		$gui = file_get_contents("static/modules/beneficiario/listado.html");
		$gui_tbl_beneficiario = file_get_contents("static/modules/beneficiario/tbl_beneficiario.html");
		
		$gui_tbl_beneficiario = $this->render_regex_dict('TBL_BENEFICIARIO', $gui_tbl_beneficiario, $beneficiario_collection);
		$render = str_replace('{tbl_beneficiario}', $gui_tbl_beneficiario, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function filtro_beneficiario($beneficiario_collection, $condicion, $tipo_filtro) {
		$gui = file_get_contents("static/modules/beneficiario/filtro_beneficiario.html");
		$gui_tbl_beneficiario = file_get_contents("static/modules/beneficiario/tbl_beneficiario_filtro.html");
		
		$gui_tbl_beneficiario = $this->render_regex_dict('TBL_BENEFICIARIO', $gui_tbl_beneficiario, $beneficiario_collection);
		$render = str_replace('{tbl_beneficiario}', $gui_tbl_beneficiario, $gui);
		$render = str_replace('{condicion}', $condicion, $render);
		$render = str_replace('{tipo_filtro}', $tipo_filtro, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function filtro_pago($pago_collection, $condicion, $tipo_filtro, $estado) {
		$gui = file_get_contents("static/modules/beneficiario/filtro_pago.html");
		$gui_tbl_pago = file_get_contents("static/modules/beneficiario/tbl_pago_filtro.html");
		
		$gui_tbl_pago = $this->render_regex_dict('TBL_PAGO', $gui_tbl_pago, $pago_collection);
		$render = str_replace('{tbl_pago}', $gui_tbl_pago, $gui);
		$render = str_replace('{condicion}', $condicion, $render);
		$render = str_replace('{tipo_filtro}', $tipo_filtro, $render);
		$render = str_replace('{estado}', $estado, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function agregar($localidad_collection) {
		$gui = file_get_contents("static/modules/beneficiario/agregar.html");
		$gui_slt_localidad = file_get_contents("static/common/slt_localidad.html");
		
		$gui_slt_localidad = $this->render_regex('SLT_LOCALIDAD', $gui_slt_localidad, $localidad_collection);
		$render = str_replace('{slt_localidad}', $gui_slt_localidad, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function editar($localidad_collection, $obj_beneficiario) {
		$gui = file_get_contents("static/modules/beneficiario/editar.html");
		$gui_slt_localidad = file_get_contents("static/common/slt_localidad.html");
		
		$beneficiario_collection = $obj_beneficiario->beneficio_collection;
		unset($obj_beneficiario->beneficio_collection);

		$obj_beneficiario = $this->set_dict($obj_beneficiario);
		$gui_slt_localidad = $this->render_regex('SLT_LOCALIDAD', $gui_slt_localidad, $localidad_collection);
		$render = str_replace('{slt_localidad}', $gui_slt_localidad, $gui);
		$render = $this->render($obj_beneficiario, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function consultar($obj_beneficiario) {
		$gui = file_get_contents("static/modules/beneficiario/consultar.html");
	
		$legajo_id = $obj_beneficiario->legajo_id;
		$cuil1 = substr($obj_beneficiario->cuil, 0, 2);
		$cuil2 = substr($obj_beneficiario->cuil, 2, 8);
		$cuil3 = substr($obj_beneficiario->cuil, 10);
		$obj_beneficiario->cuil = "{$cuil1}-{$cuil2}-{$cuil3}";
		$obj_beneficiario = $this->set_dict($obj_beneficiario);
		$render = $this->render($obj_beneficiario, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function becas($obj_beneficiario) {
		$gui = file_get_contents("static/modules/beneficiario/becas.html");
		$gui_beneficio_activo = file_get_contents("static/modules/beneficiario/beneficio_activo.html");
		$gui_lst_beneficios_no_activos = file_get_contents("static/modules/beneficiario/lst_beneficio_no_activo.html");
		$gui_cuotas_beneficio_activo = file_get_contents("static/modules/beneficiario/cuotas_beneficio_activo.html");
		$gui_sin_beneficio_activo = file_get_contents("static/modules/beneficiario/sin_beneficio_activo.html");
		$gui_sin_beneficios_no_activos = file_get_contents("static/modules/beneficiario/sin_beneficios_no_activos.html");
		$gui_sin_pagos_efectuados = file_get_contents("static/modules/beneficiario/sin_pagos_efectuados.html");
		
		$beneficio_collection = $obj_beneficiario->beneficio_collection;
		unset($obj_beneficiario->beneficio_collection);

		$beneficio_activo = NULL;
		foreach ($beneficio_collection as $clave => $valor) {
			$estado_temp = $valor->estado;
			if ($estado_temp == 1) {
				$beneficio_activo = $valor;
				unset($beneficio_collection[$clave]);
			} else {
				unset($beneficio_collection[$clave]->cuota_collection);
			}
		}

		if (is_null($beneficio_activo)) {
			$beneficioactivo_id = 0;
			$gui = str_replace('{beneficio_activo}', $gui_sin_beneficio_activo, $gui);
			$gui = str_replace('{cuotas_beneficio_activo}', "", $gui);
		} else {
			$beneficioactivo_id = $beneficio_activo->beneficio_id;
			$cuotas_activas = $beneficio_activo->cuota_collection;
			unset($beneficio_activo->cuota_collection);
			$beneficio_activo = $this->set_dict($beneficio_activo);
			
			if (empty($cuotas_activas)) {
				$gui = str_replace('{cuotas_beneficio_activo}', $gui_sin_pagos_efectuados, $gui);
			} else {
				foreach ($cuotas_activas as $clave=>$valor) $valor->total_liquidacion = $valor->monto - $valor->descuento;
				$gui_cuotas_beneficio_activo = $this->render_regex('CUOTAS_BENEFICIO_ACTIVO', $gui_cuotas_beneficio_activo, $cuotas_activas);
				$gui = str_replace('{cuotas_beneficio_activo}', $gui_cuotas_beneficio_activo, $gui);
			}

			$gui = str_replace('{flag_beneficio_activo}', 1, $gui);
			$gui = str_replace('{beneficio_activo}', $gui_beneficio_activo, $gui);			
			$gui = str_replace('{cuotas_beneficio_activo}', $gui_cuotas_beneficio_activo, $gui);			
			$gui = $this->render($beneficio_activo, $gui);
		}
		if (empty($beneficio_collection)) {
			$gui = str_replace('{beneficio_collection_no_activos}', $gui_sin_beneficios_no_activos, $gui);
		} else {
			$gui_lst_beneficios_no_activos = $this->render_regex('LST_BENEFICIO_NO_ACTIVO', $gui_lst_beneficios_no_activos, $beneficio_collection);
			$gui = str_replace('{beneficio_collection_no_activos}', $gui_lst_beneficios_no_activos, $gui);
			$gui = str_replace('{beneficioactivo_id}', $beneficioactivo_id, $gui);
		}

		$obj_beneficiario = $this->set_dict($obj_beneficiario);
		$render = $this->render($obj_beneficiario, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function form_efectuar_pago($obj_beneficio, $beneficiario_id) {
		$gui = file_get_contents("static/modules/beneficiario/form_efectuar_pago.html");
		
		$obj_beneficio->anio = date('Y');
		$meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $mes = date('m',strtotime("-1 month"));
        $mes_denominacion = $meses[$mes - 1];

		$obj_beneficio->mes = date('m',strtotime("-1 month"));
		$obj_beneficio->mes_denominacion = $mes_denominacion;
		$obj_beneficio->fecha_actual = date("Y-m-d");
	
		$cuota_collection = $obj_beneficio->cuota_collection;
		unset($obj_beneficio->cuota_collection);
		$obj_beneficio = $this->set_dict($obj_beneficio);
		$gui = str_replace('{beneficiario-beneficiario_id}', $beneficiario_id, $gui);
		$gui = $this->render($obj_beneficio, $gui);
		$gui = str_replace('{url_app}', URL_APP, $gui);
		print $gui;
	}

	function form_editar_cuota($obj_cuota, $beneficiario_id) {
		$gui = file_get_contents("static/modules/beneficiario/form_editar_cuota.html");

		$periodo_array = $this->descomponer_periodo($obj_cuota->periodo);
		$obj_cuota->anio = $periodo_array["{fecha_anio}"];
		$obj_cuota->mes = $periodo_array["{mes}"];
		$obj_cuota->mes_denominacion = $periodo_array["{fecha_mes}"];

		$obj_cuota = $this->set_dict($obj_cuota);
		$gui = str_replace('{beneficiario-beneficiario_id}', $beneficiario_id, $gui);
		$gui = $this->render($obj_cuota, $gui);
		$gui = str_replace('{url_app}', URL_APP, $gui);
		print $gui;
	}

	function consultar_cuota($obj_cuota) {
		$gui = file_get_contents("static/modules/beneficiario/consultar_cuota.html");

		$obj_cuota = $this->set_dict($obj_cuota);
		$gui = $this->render($obj_cuota, $gui);
		print $gui;
	}

	function imprimir_cuota($obj_beneficiario, $obj_beca, $obj_beneficio, $obj_cuota) {
		$gui = file_get_contents("static/modules/beneficiario/imprimir_cuota.html");

		$beneficio_collection = $obj_beneficiario->beneficio_collection;
		unset($obj_beneficiario->beneficio_collection);

		$anexo_collection = $obj_beca->anexo_collection;
		unset($obj_beca->anexo_collection);

		$cuota_collection = $obj_beneficio->cuota_collection;
		unset($obj_beneficio->cuota_collection);

		$periodo = $this->descomponer_periodo($obj_cuota->periodo);
		$obj_cuota->periodo = $periodo["{fecha_mes}"] . '/' . $periodo['{fecha_anio}'];
		
		$obj_beca = $this->set_dict($obj_beca);
		$obj_cuota = $this->set_dict($obj_cuota);
		$obj_beneficio = $this->set_dict($obj_beneficio);
		$obj_beneficiario = $this->set_dict($obj_beneficiario);


		$render = $this->render($obj_beneficio, $gui);
		$render = $this->render($obj_beneficiario, $render);
		$render = $this->render($obj_beca, $render);
		$render = $this->render($obj_cuota, $render);
		
		$render = str_replace('{url_static}', URL_STATIC, $render);
		print $render;	
	}

	function agregar_beneficio($beca_collection, $obj_beneficiario) {
		$gui = file_get_contents("static/modules/beneficiario/agregar_beneficio.html");
		$gui_beneficio_activo = file_get_contents("static/modules/beneficiario/beneficio_activo.html");
		$gui_lst_beneficios_no_activos = file_get_contents("static/modules/beneficiario/lst_beneficio_no_activo.html");
		$gui_sin_beneficio_activo = file_get_contents("static/modules/beneficiario/sin_beneficio_activo.html");
		$gui_sin_beneficios_no_activos = file_get_contents("static/modules/beneficiario/sin_beneficios_no_activos.html");
		$gui_slt_beca_array_ajax = file_get_contents("static/modules/beneficiario/slt_beca_array_ajax.html");
		$gui_slt_beca_array_ajax = $this->render_regex_dict('SLT_BECA', $gui_slt_beca_array_ajax, $beca_collection);
		
		$beneficio_collection = $obj_beneficiario->beneficio_collection;
		unset($obj_beneficiario->beneficio_collection);

		$beneficio_activo = NULL;
		foreach ($beneficio_collection as $clave => $valor) {
			$estado_temp = $valor->estado;
			if ($estado_temp == 1) {
				$beneficio_activo = $valor;
				unset($beneficio_collection[$clave]);
			} else {
				unset($beneficio_collection[$clave]->cuota_collection);
			}
		}
			
		if (is_null($beneficio_activo)) {
			$gui = str_replace('{beneficio_activo}', $gui_sin_beneficio_activo, $gui);
			$beneficioactivo_id = 0;
		} else {
			$beneficioactivo_id = $beneficio_activo->beneficio_id;
			$cuotas_activas = $beneficio_activo->cuota_collection;
			unset($beneficio_activo->cuota_collection);
			$beneficio_activo = $this->set_dict($beneficio_activo);
			
			$gui = str_replace('{beneficio_activo}', $gui_beneficio_activo, $gui);			
			$gui = str_replace('{flag_beneficio_activo}', 1, $gui);
			$gui = $this->render($beneficio_activo, $gui);
			$gui = str_replace('{beneficioactivo_id}', $beneficioactivo_id, $gui);		
		}

		if (empty($beneficio_collection)) {
			$gui = str_replace('{beneficio_collection_no_activos}', $gui_sin_beneficios_no_activos, $gui);
		} else {
			$gui_lst_beneficios_no_activos = $this->render_regex('LST_BENEFICIO_NO_ACTIVO', $gui_lst_beneficios_no_activos, $beneficio_collection);
			$gui = str_replace('{beneficio_collection_no_activos}', $gui_lst_beneficios_no_activos, $gui);			
			$gui = str_replace('{beneficioactivo_id}', $beneficioactivo_id, $gui);			
		}

		$obj_beneficiario = $this->set_dict($obj_beneficiario);
		$render = $this->render($obj_beneficiario, $gui);
		$render = str_replace('{slt_beca}', $gui_slt_beca_array_ajax, $render);			
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function carga_anexos($anexo_collection) {
		$gui_slt_anexo = file_get_contents("static/common/slt_anexo_array.html");
		$gui_slt_anexo = $this->render_regex_dict('SLT_ANEXO', $gui_slt_anexo, $anexo_collection);
		print $gui_slt_anexo;
	}
}
?>