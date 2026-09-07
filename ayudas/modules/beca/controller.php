<?php
require_once "modules/beca/model.php";
require_once "modules/beca/view.php";
require_once "modules/localidad/model.php";
require_once "modules/tipobeca/model.php";
require_once "modules/anexo/model.php";
require_once "modules/cuota/model.php";
require_once "modules/plan/model.php";


class BecaController {

	function __construct() {
		$this->model = new Beca();
		$this->view = new BecaView();
	}

	function panel($arg) {
    	SessionHandler()->check_session();
    	$ids = explode("@", $arg);
    	$plan_id = $ids[0];
    	$periodo_temp = (isset($ids[1])) ? $ids[1] : NULL;
		$periodo = (is_null($periodo_temp)) ? date('Ym',strtotime("-1 month")) : $periodo_temp;

		$pm = new Plan();
		$pm->plan_id = $plan_id;
		$pm->get();
		
    	$select_beca = "b.beca_id AS BECA_ID, b.denominacion AS DENOMINACION, b.resolucion AS RESOLUCION, p.plan_id AS PLAN_ID, 
						tb.denominacion AS TIPOBECA, l.denominacion AS LOCALIDAD, DATE_FORMAT(b.fecha, '%Y') AS ANIO";
		$from_beca = "beca b INNER JOIN localidad l ON b.localidad = l.localidad_id INNER JOIN 
					  tipobeca tb ON b.tipobeca = tb.tipobeca_id INNER JOIN plan p ON b.plan = p.plan_id";
		$where = "b.plan = {$plan_id}";
		$beca_collection = CollectorCondition()->get('Beca', $where, 4, $from_beca, $select_beca);

		$select_pago_total = "IFNULL(SUM(a.monto), 0) AS PAGO_TOTAL";
		$from_pago_total = "beneficiario b INNER JOIN beneficiobeneficiario bb ON b.beneficiario_id = bb.compuesto INNER JOIN
							beneficio be ON bb.compositor = be.beneficio_id INNER JOIN anexo a ON be.anexo = a.anexo_id INNER JOIN
							anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id INNER JOIN 
							plan p ON bc.plan = p.plan_id INNER JOIN localidad l ON bc.localidad = l.localidad_id";
		$where_pago_total = "p.plan_id = {$plan_id} AND be.estado = 1";
		$sum_pago_total = CollectorCondition()->get('Cuota', $where_pago_total, 4, $from_pago_total, $select_pago_total);

	    	$select_pago_periodo = "IFNULL(SUM(c.monto - c.descuento), 0) AS PAGO_PERIODO";
	    	$from_pago_periodo = "cuota c INNER JOIN cuotabeneficio cb ON c.cuota_id = cb.compositor INNER JOIN beneficio b ON cb.compuesto = b.beneficio_id INNER JOIN 
	    				 anexo a ON b.anexo = anexo_id INNER JOIN anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id INNER JOIN 
	    				 localidad l ON bc.localidad = l.localidad_id INNER JOIN plan p ON bc.plan = p.plan_id";
		$from_cantidad_pagos = "cuota c INNER JOIN cuotabeneficio cb ON c.cuota_id = cb.compositor INNER JOIN beneficio b ON cb.compuesto = b.beneficio_id INNER JOIN
							beneficiobeneficiario bb ON b.beneficio_id = bb.compositor INNER JOIN beneficiario bf ON bb.compuesto = bf.beneficiario_id INNER JOIN
							anexo a ON b.anexo = a.anexo_id INNER JOIN anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id INNER JOIN
							localidad l ON bc.localidad = l.localidad_id INNER JOIN plan p ON bc.plan = p.plan_id";
		$where_pago_periodo = "c.periodo = {$periodo} AND bc.plan = {$plan_id} AND b.estado = 1";
		$sum_pago_periodo = CollectorCondition()->get('Cuota', $where_pago_periodo, 4, $from_pago_periodo, $select_pago_periodo);

		$select_cantidad_pagos = "COUNT(DISTINCT bf.beneficiario_id) AS CANTIDAD_PAGOS";
		$cantidad_pagos = CollectorCondition()->get('Cuota', $where_pago_periodo, 4, $from_cantidad_pagos, $select_cantidad_pagos);

		$dict_pagos = array("{pago-total}"=>$sum_pago_total[0]["PAGO_TOTAL"],
							"{pago-periodo}"=>$sum_pago_periodo[0]["PAGO_PERIODO"],
							"{saldo-periodo}"=>$sum_pago_total[0]["PAGO_TOTAL"] - $sum_pago_periodo[0]["PAGO_PERIODO"],
							"{cantidad-pagos-periodo}"=>$cantidad_pagos[0]["CANTIDAD_PAGOS"]);

		$select_cant_beneficio_localidad = "l.denominacion AS LOCALIDAD, COUNT(l.localidad_id) AS CANT, SUM(a.monto) TOTAL,
												(SELECT IFNULL(SUM(sc.monto - sc.descuento), 0) FROM cuota sc INNER JOIN cuotabeneficio scb ON sc.cuota_id = scb.compositor INNER JOIN
											beneficio sb ON scb.compuesto = sb.beneficio_id INNER JOIN anexo sa ON sb.anexo = sa.anexo_id INNER JOIN
											anexobeca sab ON sa.anexo_id = sab.compositor INNER JOIN beca sbc ON sab.compuesto = sbc.beca_id INNER JOIN 
												localidad sl ON sbc.localidad = sl.localidad_id WHERE sb.estado = 1 AND sbc.plan = {$plan_id} AND sc.periodo = {$periodo} AND sl.localidad_id = l.localidad_id
											GROUP BY sl.localidad_id) AS PAGO";
    	$from_cant_beneficio_localidad = "beneficio b INNER JOIN anexo a ON b.anexo = a.anexo_id INNER JOIN anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN
    									  beca bc ON ab.compuesto = bc.beca_id INNER JOIN localidad l ON bc.localidad = l.localidad_id INNER JOIN 
    									  plan p ON bc.plan = p.plan_id INNER JOIN beneficiobeneficiario bb ON b.beneficio_id = bb.compositor INNER JOIN
    									  beneficiario bf ON bb.compuesto = bf.beneficiario_id";
		$where_cant_beneficio_localidad = "b.estado = 1 AND p.plan_id = {$plan_id}";
		$groupby_cant_beneficio_localidad = "l.localidad_id ORDER BY COUNT(bc.localidad) DESC";
		$cant_beneficio_localidad = CollectorCondition()->get('Beneficio', $where_cant_beneficio_localidad, 4, $from_cant_beneficio_localidad, 
															  $select_cant_beneficio_localidad, $groupby_cant_beneficio_localidad);

		foreach ($cant_beneficio_localidad as $clave=>$valor) {
			$saldo = $valor["TOTAL"] - $valor["PAGO"];
			$cant_beneficio_localidad[$clave]["SALDO"] = $saldo;
		}

		$select_pago_localidad = "SUM(c.monto - c.descuento) AS MONTO, l.denominacion AS LOCALIDAD";
		$group_by_pago_localidad = "l.localidad_id ORDER BY SUM(c.monto - c.descuento) DESC";
		$pago_localidad_collection = CollectorCondition()->get('Cuota', $where_pago_periodo, 4, $from_pago_periodo, $select_pago_localidad, $group_by_pago_localidad);

		$this->view->panel($pm, $beca_collection, $cant_beneficio_localidad, $pago_localidad_collection, $dict_pagos, $periodo);
	}

	function agregar($arg) {
    	SessionHandler()->check_session();
		
    	$pm = new Plan();
    	$pm->plan_id = $arg;
    	$pm->get();

		$localidad_collection = Collector()->get('Localidad');
		$tipobeca_collection = Collector()->get('TipoBeca');
		$this->view->agregar($localidad_collection, $tipobeca_collection, $pm);
	}

	function consultar($arg) {
		SessionHandler()->check_session();
		
		$this->model->beca_id = $arg;
		$this->model->get();
		$this->view->consultar($this->model);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$ids = explode("@", $arg);
		$pm = new Plan();
		$pm->plan_id = $arg[0];
		$pm->get();
		$this->model->beca_id = $ids[1];
		$this->model->get();
		$localidad_collection = Collector()->get('Localidad');
		$tipobeca_collection = Collector()->get('TipoBeca');
		$this->view->editar($localidad_collection, $tipobeca_collection, $this->model, $pm);
	}

	function guardar() {
		SessionHandler()->check_session();
		$plan_id = filter_input(INPUT_POST, "plan");
		foreach ($_POST as $key=>$value) $this->model->$key = $value;
		$this->model->save();
		$beca_id = $this->model->beca_id;
		header("Location: " . URL_APP . "/beca/editar/{$plan_id}@{$beca_id}");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();

		$ids = explode("@", $arg);
		$beca_id = (isset($ids[1])) ? $ids[1] : $ids[0];
		if (has_related_records("SELECT COUNT(*) AS total FROM anexobeca WHERE compuesto = ?", array($beca_id))) {
			set_app_message("No se puede eliminar la resolución porque tiene anexos asociados. Primero quite esos anexos.");
			$plan_id = (isset($ids[1])) ? $ids[0] : NULL;
			$destino = is_null($plan_id) ? "/plan/panel" : "/beca/panel/{$plan_id}";
			header("Location: " . URL_APP . $destino);
			return;
		}

		$this->model->beca_id = $beca_id;
		$this->model->delete();

		$plan_id = (isset($ids[1])) ? $ids[0] : NULL;
		$destino = is_null($plan_id) ? "/plan/panel" : "/beca/panel/{$plan_id}";
		header("Location: " . URL_APP . $destino);
	}

	function asociar_anexo() {
		SessionHandler()->check_session();
		$plan_id = filter_input(INPUT_POST, "plan");
		$am = new Anexo();
		$am->denominacion = filter_input(INPUT_POST, 'denominacion');
		$am->monto = filter_input(INPUT_POST, 'monto');
		$am->detalle = filter_input(INPUT_POST, 'detalle');
		$am->save();
		$anexo_id = $am->anexo_id;

		$beca_id = filter_input(INPUT_POST, 'beca_id');
		$this->model->beca_id = $beca_id;
		$this->model->get();

		$am = new Anexo();
		$am->anexo_id = $anexo_id;
		$am->get();
		$this->model->add_anexo($am);

		$abm = new AnexoBeca($this->model);
		$abm->save();

		header("Location: " . URL_APP . "/beca/editar/{$plan_id}@{$beca_id}");		
	}

	function form_editar_anexo($arg) {
		$ids = explode("@", $arg);
		$am = new Anexo();
		$am->anexo_id = $ids[0];
		$am->get();
		$this->view->form_editar_anexo($am, $ids[1]);
	}

	function actualizar_anexo() {
		$plan_id = filter_input(INPUT_POST, 'plan');
		$beca_id = filter_input(INPUT_POST, 'beca_id');
		$am = new Anexo();
		$am->anexo_id = filter_input(INPUT_POST, 'anexo_id');
		$am->denominacion = filter_input(INPUT_POST, 'denominacion');
		$am->monto = filter_input(INPUT_POST, 'monto');
		$am->detalle = filter_input(INPUT_POST, 'detalle');
		$am->save();
		header("Location: " . URL_APP . "/beca/editar/{$plan_id}@{$beca_id}");		
	}

	function eliminar_anexo($arg) {
		SessionHandler()->check_session();

		$ids = explode("@", $arg);
		if (has_related_records("SELECT COUNT(*) AS total FROM beneficio WHERE anexo = ?", array($ids[0]))) {
			set_app_message("No se puede eliminar el anexo porque tiene beneficios asociados.");
			$beca_id = (isset($ids[1])) ? $ids[1] : NULL;
			$plan_id = (isset($ids[2])) ? $ids[2] : NULL;
			$destino = (!is_null($beca_id) && !is_null($plan_id))
				? "/beca/editar/{$plan_id}@{$beca_id}"
				: "/plan/panel";
			header("Location: " . URL_APP . $destino);
			return;
		}

		$am = new Anexo();
		$am->anexo_id = $ids[0];
		$am->delete();

		$beca_id = (isset($ids[1])) ? $ids[1] : NULL;
		$plan_id = (isset($ids[2])) ? $ids[2] : NULL;
		$destino = (!is_null($beca_id) && !is_null($plan_id))
			? "/beca/editar/{$plan_id}@{$beca_id}"
			: "/plan/panel";
		header("Location: " . URL_APP . $destino);
	}

	function consultar_anexo($arg) {
		$am = new Anexo();
		$am->anexo_id = $arg;
		$am->get();
		$this->view->consultar_anexo($am);
	}
}
?>
