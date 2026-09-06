<?php
require_once "modules/plan/model.php";
require_once "modules/plan/view.php";
require_once "modules/localidad/model.php";
require_once "modules/cuota/model.php";


class PlanController {

	function __construct() {
		$this->model = new Plan();
		$this->view = new PlanView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$plan_collection = Collector()->get('Plan');
		$this->view->panel($plan_collection);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->plan_id = $arg;
		$this->model->get();
		$plan_collection = Collector()->get('Plan');
		$this->view->editar($plan_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();		
		foreach ($_POST as $key=>$value) $this->model->$key = $value;
		$this->model->save();
		header("Location: " . URL_APP . "/plan/panel");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();

		if (has_related_records("SELECT COUNT(*) AS total FROM beca WHERE plan = ?", array($arg))) {
			set_app_message("No se puede eliminar el plan porque tiene resoluciones asociadas.");
			header("Location: " . URL_APP . "/plan/panel");
			return;
		}

		$this->model->plan_id = $arg;
		$this->model->delete();
		header("Location: " . URL_APP . "/plan/panel");
	}

	function home() {
		SessionHandler()->check_session();

		$periodo_temp = filter_input(INPUT_POST, "periodo");
		$periodo = (is_null($periodo_temp)) ? date('Ym',strtotime("-1 month")) : $periodo_temp;

		$select_plan = "plan_id";
		$from_plan = "plan";
		$where_plan = "conf_panel = 1";
		$plan_id = CollectorCondition()->get('Plan', $where_plan, 4, $from_plan, $select_plan);
		$plan_id = $plan_id[0]['plan_id'];

		$obj_plan = new Plan();
		$obj_plan->plan_id = $plan_id;
		$obj_plan->get();
		
		$localidad_collection = Collector()->get('Localidad');

		$select_pago_total = "IFNULL(SUM(c.monto - c.descuento), 0) AS PAGO_TOTAL";
		$from_pago_total = "cuota c INNER JOIN cuotabeneficio cb ON c.cuota_id = cb.compositor INNER JOIN beneficio be ON cb.compuesto = be.beneficio_id INNER JOIN
							beneficiobeneficiario bb ON be.beneficio_id = bb.compositor INNER JOIN beneficiario b ON bb.compuesto = b.beneficiario_id INNER JOIN anexo a ON be.anexo = a.anexo_id INNER JOIN
							anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id INNER JOIN 
							plan p ON bc.plan = p.plan_id INNER JOIN localidad l ON bc.localidad = l.localidad_id";
		$where_pago_total = "p.conf_panel = 1 AND be.estado = 1";
		$sum_pago_total = CollectorCondition()->get('Cuota', $where_pago_total, 4, $from_pago_total, $select_pago_total);

	    	$select_pago_periodo = "IFNULL(SUM(c.monto - c.descuento), 0) AS PAGO_PERIODO";
    	$from_pago_periodo = "cuota c INNER JOIN cuotabeneficio cb ON c.cuota_id = cb.compositor INNER JOIN beneficio b ON cb.compuesto = b.beneficio_id INNER JOIN 
    			 beneficiobeneficiario bb ON b.beneficio_id = bb.compositor INNER JOIN beneficiario bf ON bb.compuesto = bf.beneficiario_id INNER JOIN 
    			 anexo a ON b.anexo = anexo_id INNER JOIN anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id INNER JOIN 
    			 localidad l ON bc.localidad = l.localidad_id INNER JOIN plan p ON bc.plan = p.plan_id";
		$where_pago_periodo = "c.periodo = {$periodo} AND p.conf_panel = 1 AND b.estado = 1";
		$sum_pago_periodo = CollectorCondition()->get('Cuota', $where_pago_periodo, 4, $from_pago_periodo, $select_pago_periodo);

		$select_cantidad_pagos = "COUNT(DISTINCT bf.beneficiario_id) AS CANTIDAD_PAGOS";
		$cantidad_pagos = CollectorCondition()->get('Cuota', $where_pago_periodo, 4, $from_pago_periodo, $select_cantidad_pagos);

		$dict_pagos = array("{periodo}"=>$periodo,
							"{pago-total}"=>$sum_pago_total[0]["PAGO_TOTAL"],
							"{pago-periodo}"=>$sum_pago_periodo[0]["PAGO_PERIODO"],
							"{saldo-periodo}"=>$sum_pago_total[0]["PAGO_TOTAL"] - $sum_pago_periodo[0]["PAGO_PERIODO"],
							"{cantidad-pagos-periodo}"=>$cantidad_pagos[0]["CANTIDAD_PAGOS"]);

		$select_cant_beneficio_localidad = "l.denominacion AS LOCALIDAD, COUNT(l.localidad_id) AS CANT, SUM(a.monto) TOTAL,
												(SELECT IFNULL(SUM(sc.monto - sc.descuento), 0) FROM cuota sc INNER JOIN cuotabeneficio scb ON sc.cuota_id = scb.compositor INNER JOIN
											beneficio sb ON scb.compuesto = sb.beneficio_id INNER JOIN anexo sa ON sb.anexo = sa.anexo_id INNER JOIN
											anexobeca sab ON sa.anexo_id = sab.compositor INNER JOIN beca sbc ON sab.compuesto = sbc.beca_id INNER JOIN 
												localidad sl ON sbc.localidad = sl.localidad_id WHERE sb.estado = 1 AND sc.periodo = {$periodo} AND sl.localidad_id = l.localidad_id
											GROUP BY sl.localidad_id) AS PAGO";
    	$from_cant_beneficio_localidad = "beneficio b INNER JOIN anexo a ON b.anexo = a.anexo_id INNER JOIN anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN
    									  beca bc ON ab.compuesto = bc.beca_id INNER JOIN localidad l ON bc.localidad = l.localidad_id INNER JOIN 
    									  plan p ON bc.plan = p.plan_id INNER JOIN beneficiobeneficiario bb ON b.beneficio_id = bb.compositor INNER JOIN
    									  beneficiario bf ON bb.compuesto = bf.beneficiario_id";
		$where_cant_beneficio_localidad = "b.estado = 1 AND p.conf_panel = 1";
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

		$this->view->home($cant_beneficio_localidad, $pago_localidad_collection, $dict_pagos, $localidad_collection, $obj_plan);
	}

	function activar($arg) {
		$select_activo = "plan_id";
		$from_activo = "plan";
		$where_activo = "conf_panel = 1";
		$activo_id = CollectorCondition()->get('Plan', $where_activo, 4, $from_activo, $select_activo);

		$this->model->plan_id = $activo_id[0]['plan_id'];
		$this->model->get();
		$this->model->conf_panel = 0;
		$this->model->save();

		$this->model = new Plan();
		$this->model->plan_id = $arg;
		$this->model->get();
		$this->model->conf_panel = 1;
		$this->model->save();
		
		header("Location: " . URL_APP . "/plan/panel");
	}
}
?>
