<?php
require_once "modules/beneficiario/model.php";
require_once "modules/beneficiario/view.php";
require_once "modules/localidad/model.php";
require_once "modules/beca/model.php";
require_once "modules/beneficio/model.php";
require_once "modules/cuota/model.php";


class BeneficiarioController {

	function __construct() {
		$this->model = new Beneficiario();
		$this->view = new BeneficiarioView();
	}

	function listado() {
    	SessionHandler()->check_session();
		
    	$select_beneficiario = "b.beneficiario_id AS BENEFICIARIO_ID, b.apellido AS APELLIDO, b.nombre AS NOMBRE, 
    							b.documento AS DOCUMENTO, b.telefono AS TELEFONO, l.denominacion AS LOCALIDAD";
		$from_beneficiario = "beneficiario b INNER JOIN localidad l ON b.localidad = l.localidad_id";
		$beneficiario_collection = CollectorCondition()->get('Beneficiario', NULL, 4, $from_beneficiario, $select_beneficiario);
		$this->view->listado($beneficiario_collection);
	}

	function agregar() {
    	SessionHandler()->check_session();
		
		$localidad_collection = Collector()->get('Localidad');
		$this->view->agregar($localidad_collection);
	}

	function consultar($arg) {
		SessionHandler()->check_session();
		
		$this->model->beneficiario_id = $arg;
		$this->model->get();
		$this->view->consultar($this->model);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		
		$this->model->beneficiario_id = $arg;
		$this->model->get();
		$localidad_collection = Collector()->get('Localidad');
		$this->view->editar($localidad_collection, $this->model);
	}

	function becas($arg) {
		SessionHandler()->check_session();
		
		$this->model->beneficiario_id = $arg;
		$this->model->get();
		$beneficio_collection = $this->model->beneficio_collection;

		$select_beca = "bc.beca_id AS BECA_ID, bc.denominacion AS BECA, bc.resolucion AS RESOLUCION, tb.denominacion AS TIPOBECA";
		$from_beca = "anexo a INNER JOIN anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id
					  INNER JOIN tipobeca tb ON bc.tipobeca = tb.tipobeca_id";
		foreach ($beneficio_collection as $clave => $valor) {
			$valor->beca_id = 0;
			$valor->beca = 'Sin beca asociada';
			$valor->resolucion = '-';
			$valor->tipobeca = '-';

			if (!is_object($valor->anexo) || empty($valor->anexo->anexo_id)) {
				$valor->anexo = new Anexo();
				continue;
			}

			$anexo_id = $valor->anexo->anexo_id;
			$where_beca = "a.anexo_id = {$anexo_id}";
			$beca = CollectorCondition()->get('Beca', $where_beca, 4, $from_beca, $select_beca);
			if (empty($beca)) {
				continue;
			}

			$valor->beca_id = $beca[0]['BECA_ID'];
			$valor->beca = $beca[0]['BECA'];
			$valor->resolucion = $beca[0]['RESOLUCION'];
			$valor->tipobeca = $beca[0]['TIPOBECA'];
		}

		$this->view->becas($this->model);
	}

	function guardar() {
		SessionHandler()->check_session();
		
		foreach ($_POST as $key=>$value) $this->model->$key = $value;
		$this->model->save();
		$beneficiario_id = $this->model->beneficiario_id;
		header("Location: " . URL_APP . "/beneficiario/editar/{$beneficiario_id}");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();

		if (has_related_records("SELECT COUNT(*) AS total FROM beneficiobeneficiario WHERE compuesto = ?", array($arg))) {
			set_app_message("No se puede eliminar el beneficiario porque tiene beneficios asociados. Primero quite esas relaciones.");
			header("Location: " . URL_APP . "/beneficiario/listado");
			return;
		}

		$this->model->beneficiario_id = $arg;
		$this->model->delete();
		header("Location: " . URL_APP . "/beneficiario/listado");
	}

	function form_efectuar_pago($arg) {
		$ids = explode('@', $arg);
		$beneficiario_id = $ids[0];
		$bm = new Beneficio();
		$bm->beneficio_id = $ids[1];
		$bm->get();
		$this->view->form_efectuar_pago($bm, $beneficiario_id);
	}

	function efectuar_pago() {
		$anio = filter_input(INPUT_POST, 'anio');
		$mes = filter_input(INPUT_POST, 'mes');

		$cm = new Cuota();
		$cm->fecha = filter_input(INPUT_POST, 'fecha');
		$cm->periodo = "{$anio}{$mes}";
		$cm->monto = filter_input(INPUT_POST, 'monto');
		$cm->cantidad = filter_input(INPUT_POST, 'cantidad');
		$cm->descuento = filter_input(INPUT_POST, 'descuento');
		$cm->detalle = filter_input(INPUT_POST, 'detalle');
		$cm->save();
		$cuota_id = $cm->cuota_id;

		$cm = new Cuota();
		$cm->cuota_id = $cuota_id;
		$cm->get();

		$beneficio_id = filter_input(INPUT_POST, 'beneficio_id');
		$bm = new Beneficio();
		$bm->beneficio_id = $beneficio_id;
		$bm->get();
		$bm->add_cuota($cm);

		$cbm = new CuotaBeneficio($bm);
		$cbm->save();

		$beneficiario_id = filter_input(INPUT_POST, 'beneficiario_id');
		header("Location: " . URL_APP . "/beneficiario/becas/{$beneficiario_id}");
	}

	function form_editar_cuota($arg) {
		$ids = explode('@', $arg);
		$beneficiario_id = $ids[0];
		$cm = new Cuota();
		$cm->cuota_id = $ids[1];
		$cm->get();
		$this->view->form_editar_cuota($cm, $beneficiario_id);
	}

	function editar_pago() {
		$anio = filter_input(INPUT_POST, 'anio');
		$mes = filter_input(INPUT_POST, 'mes');
		$cm = new Cuota();
		$cm->cuota_id = filter_input(INPUT_POST, 'cuota_id');
		$cm->fecha = filter_input(INPUT_POST, 'fecha');
		$cm->periodo = "{$anio}{$mes}";
		$cm->monto = filter_input(INPUT_POST, 'monto');
		$cm->cantidad = filter_input(INPUT_POST, 'cantidad');
		$cm->descuento = filter_input(INPUT_POST, 'descuento');
		$cm->detalle = filter_input(INPUT_POST, 'detalle');
		$cm->save();

		$beneficiario_id = filter_input(INPUT_POST, 'beneficiario_id');
		header("Location: " . URL_APP . "/beneficiario/becas/{$beneficiario_id}");
	}

	function eliminar_cuota($arg) {
		$ids = explode('@', $arg);
		$cm = new Cuota;
		$cm->cuota_id = $ids[1];
		$cm->delete();

		$beneficiario_id = $ids[0];
		header("Location: " . URL_APP . "/beneficiario/becas/{$beneficiario_id}");		
	}

	function consultar_cuota($arg) {
		$cm = new Cuota();
		$cm->cuota_id = $arg;
		$cm->get();
		$this->view->consultar_cuota($cm);
	}

	function imprimir_cuota($arg) {
		$ids = explode('@', $arg);

		$this->model->beneficiario_id = $ids[0];
		$this->model->get();

		$bc = new Beca();
		$bc->beca_id = $ids[1];
		$bc->get();

		$bm = new Beneficio();
		$bm->beneficio_id = $ids[2];
		$bm->get();

		$cm = new Cuota();
		$cm->cuota_id = $ids[3];
		$cm->get();
		
		$lm = new Localidad();
		$lm->localidad_id = $ids[4];
		$lm->get();
		
		$this->view->imprimir_cuota($this->model, $bc, $bm, $cm, $lm);
	}

	function agregar_beneficio($arg) {
		SessionHandler()->check_session();
		
		$this->model->beneficiario_id = $arg;
		$this->model->get();
		$beneficio_collection = $this->model->beneficio_collection;

		$select_beca = "bc.beca_id AS BECA_ID, bc.denominacion AS BECA, bc.resolucion AS RESOLUCION, tb.denominacion AS TIPOBECA";
		$from_beca = "anexo a INNER JOIN anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id
					  INNER JOIN tipobeca tb ON bc.tipobeca = tb.tipobeca_id";
		foreach ($beneficio_collection as $clave => $valor) {
			$anexo_id = $valor->anexo->anexo_id;
			$where_beca = "a.anexo_id = {$anexo_id}";
			$beca = CollectorCondition()->get('Beca', $where_beca, 4, $from_beca, $select_beca);
			$valor->beca_id = $beca[0]['BECA_ID'];
			$valor->beca = $beca[0]['BECA'];
			$valor->resolucion = $beca[0]['RESOLUCION'];
			$valor->tipobeca = $beca[0]['TIPOBECA'];
		}

		$select_beca = "bc.beca_id AS BECA_ID, bc.denominacion AS BECA, bc.resolucion AS RESOLUCION, l.denominacion AS LOCALIDAD";
		$from_beca = "beca bc INNER JOIN localidad l ON bc.localidad = l.localidad_id";
		$beca_collection = CollectorCondition()->get('Beca', NULL, 4, $from_beca, $select_beca);
		$this->view->agregar_beneficio($beca_collection, $this->model);
	}

	function activar_beneficio() {
		SessionHandler()->check_session();

		$beneficiario_id = filter_input(INPUT_POST, 'beneficiario_id'); 
		$beneficioactivo_id = filter_input(INPUT_POST, 'beneficioactivo_id'); 
		$beneficio_id = filter_input(INPUT_POST, 'beneficio_id'); 

		if ($beneficioactivo_id != 0) {
			$bm = new Beneficio();
			$bm->beneficio_id = $beneficioactivo_id;
			$bm->get();
			$bm->estado = 0;
			$bm->save();
		}

		$bm = new Beneficio();
		$bm->beneficio_id = $beneficio_id;
		$bm->get();
		$bm->estado = 1;
		$bm->save();

		header("Location: " . URL_APP . "/beneficiario/becas/{$beneficiario_id}");
	}

	function desactivar_beneficio() {
		SessionHandler()->check_session();

		$beneficiario_id = filter_input(INPUT_POST, 'beneficiario_id');
		$beneficio_id = filter_input(INPUT_POST, 'beneficio_id');

		$bm = new Beneficio();
		$bm->beneficio_id = $beneficio_id;
		$bm->get();
		$bm->estado = 0;
		$bm->save();
		header("Location: " . URL_APP . "/beneficiario/becas/{$beneficiario_id}");
	}

	function adjuntar_beneficio() {
		SessionHandler()->check_session();

		$beneficiario_id = filter_input(INPUT_POST, 'beneficiario_id');

		$bm = new Beneficio();
		$bm->beneficio_id = filter_input(INPUT_POST, 'beneficioactivo_id');
		$bm->get();
		$bm->estado = 0;
		$bm->save();

		$bm = new Beneficio();
		$bm->fecha_alta = filter_input(INPUT_POST, 'fecha_alta');
		$bm->estado = 1;
		$bm->anexo = filter_input(INPUT_POST, 'anexo');
		$bm->save();
		$beneficio_id = $bm->beneficio_id;

		$bm = new Beneficio();
		$bm->beneficio_id = $beneficio_id;
		$bm->get();

		$this->model->beneficiario_id = $beneficiario_id;
		$this->model->get();
		$this->model->add_beneficio($bm);

		$bbm = new BeneficioBeneficiario($this->model);
		$bbm->save();
		header("Location: " . URL_APP . "/beneficiario/becas/{$beneficiario_id}");
	}

	function traer_anexos($arg) {
		$listadoSelects=array(
			"beca"=>"beca",
			"anexo"=>"anexo"
		);

		$argumentos = explode("_", $arg);
		$selectDestino = $argumentos[0];
		$opcionSeleccionada = $argumentos[1];


		if (isset($listadoSelects[$selectDestino])) {
			if (is_numeric($opcionSeleccionada)) {
				$tabla = $listadoSelects[$selectDestino];
				$select = "a.anexo_id AS ANEXO_ID, a.denominacion AS ANEXO, a.monto AS MONTO";
				$from = "beca b INNER JOIN anexobeca ab ON b.beca_id = ab.compuesto INNER JOIN anexo a ON ab.compositor = a.anexo_id";
				$where = "b.beca_id = {$opcionSeleccionada}";
    			$anexo_collection = CollectorCondition()->get('Beca', $where, 4, $from, $select);
			}
		}
		$this->view->carga_anexos($anexo_collection);
	}

	function verifica_documento($arg) {
		$select = "COUNT(*) AS DUPLICADO";
		$from = "beneficiario b";
		$where = "b.documento = {$arg}";
		$flag = CollectorCondition()->get('Comprobante', $where, 4, $from, $select);
		print $flag[0]["DUPLICADO"];
	}

	function eliminar_beneficio() {
		$beneficiario_id = filter_input(INPUT_POST, 'beneficiario_id');
		$beneficio_id = filter_input(INPUT_POST, 'beneficio_id');

		$select = "cb.compositor AS CUOTA_ID";
		$from = "cuotabeneficio cb";
		$where = "cb.compuesto = {$beneficio_id}";
		$cuota_collection = CollectorCondition()->get('Beneficio', $where, 4, $from, $select);

		foreach ($cuota_collection as $clave=>$valor) {
			$cm = new Cuota();
			$cm->cuota_id = $valor["CUOTA_ID"];
			$cm->delete();
		}
		
		$bm = new Beneficio();
		$bm->beneficio_id = $beneficio_id;
		$bm->delete();

		header("Location: " . URL_APP . "/beneficiario/becas/{$beneficiario_id}");
	}

	function filtro_beneficiario() {
		SessionHandler()->check_session();

		$tipo_filtro = filter_input(INPUT_POST, 'tipo_filtro');
		
		$select = "b.beneficiario_id AS B_ID, CONCAT(b.apellido, ', ', b.nombre) AS BENEFICIARIO, b.documento AS DOCUMENTO, 
				   b.telefono AS TELEFONO, bc.denominacion AS BECA, bc.resolucion AS RESOLUCION, lb.denominacion AS LOCALIDAD,
				   a.denominacion AS ANEXO, a.monto AS MONTO";
		$from = "beneficiario b LEFT JOIN beneficiobeneficiario bb ON b.beneficiario_id = bb.compuesto LEFT JOIN
				 beneficio bf ON bb.compositor = bf.beneficio_id INNER JOIN anexo a ON bf.anexo = a.anexo_id INNER JOIN 
				 anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id INNER JOIN 
				 localidad lb ON bc.localidad = lb.localidad_id";
		
		switch ($tipo_filtro) {
			case 1:
				$localidad_id = filter_input(INPUT_POST, 'localidad');
				$where = "bc.localidad = {$localidad_id} AND bf.estado = 1";
				break;
			case 2:
				$monto = filter_input(INPUT_POST, 'monto');
				$where = "a.monto = {$monto} AND bf.estado = 1";
				break;
		}

		$beneficiario_collection = CollectorCondition()->get('Beneficiario', $where, 4, $from, $select);
		$this->view->filtro_beneficiario($beneficiario_collection, $where, $tipo_filtro);
	}

	function filtro_pago() {
		SessionHandler()->check_session();

		$tipo_filtro = filter_input(INPUT_POST, 'tipo_filtro');
		$select = "bc.denominacion AS BECA, bc.resolucion AS RESOLUCION, a.denominacion AS ANEXO, bf.beneficiario_id AS B_ID, 
				   CONCAT(bf.apellido, ', ', bf.nombre) AS BENEFICIARIO, c.monto AS MONTO, c.fecha AS FECHA, l.denominacion AS LOCALIDAD";
		$from = "beneficiario bf INNER JOIN beneficiobeneficiario bb ON bf.beneficiario_id = bb.compuesto INNER JOIN 
				 beneficio b ON bb.compositor = b.beneficio_id INNER JOIN anexo a ON b.anexo = a.anexo_id INNER JOIN
				 anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id INNER JOIN
				 localidad l ON bc.localidad = l.localidad_id INNER JOIN cuotabeneficio cb ON b.beneficio_id = cb.compuesto INNER JOIN
				 cuota c ON cb.compositor = c.cuota_id";
		
		switch ($tipo_filtro) {
			case 1:
				$periodo = filter_input(INPUT_POST, 'periodo');
				$where = "c.periodo = {$periodo}";
				$estado = 0;
				break;
			case 2:
				$periodo = filter_input(INPUT_POST, 'periodo');
				$localidad = filter_input(INPUT_POST, 'localidad');
				$estado = filter_input(INPUT_POST, 'estado');
				$where = "c.periodo = {$periodo} AND l.localidad_id = {$localidad} AND b.estado = 1";
				if ($estado == 2) {
					$select = "bc.denominacion AS BECA, bc.resolucion AS RESOLUCION, a.denominacion AS ANEXO, bf.beneficiario_id AS B_ID, 
				   			   CONCAT(bf.apellido, ', ', bf.nombre) AS BENEFICIARIO, a.monto AS MONTO, '' AS FECHA, l.denominacion AS LOCALIDAD";
					$from = "beneficiario bf INNER JOIN beneficiobeneficiario bb ON bf.beneficiario_id = bb.compuesto INNER JOIN 
					 		 beneficio b ON bb.compositor = b.beneficio_id INNER JOIN anexo a ON b.anexo = a.anexo_id INNER JOIN
					 		 anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id INNER JOIN
					 		 localidad l ON bc.localidad = l.localidad_id";
					$where = "b.estado = 1 AND l.localidad_id = {$localidad} AND bf.beneficiario_id NOT IN (SELECT bebe.beneficiario_id FROM	cuota cc INNER JOIN
							  cuotabeneficio ccbb ON cc.cuota_id = ccbb.compositor INNER JOIN  beneficio bbb ON ccbb.compuesto = bbb.beneficio_id INNER JOIN
							  beneficiobeneficiario bbbb ON bbb.beneficio_id = bbbb.compositor INNER JOIN beneficiario bebe ON bbbb.compuesto = bebe.beneficiario_id
							  WHERE	cc.periodo = {$periodo})";
				}

				break;	
		}
		
		$cuota_collection = CollectorCondition()->get('Cuota', $where, 4, $from, $select);
		
		$this->view->filtro_pago($cuota_collection, $where, $tipo_filtro, $estado);
	}

	function descargar_excel_pago() {
		require_once "tools/excelreport.php";
		$tipo_filtro = filter_input(INPUT_POST, 'tipo_filtro');
		$select = "CONCAT(bc.denominacion, ' - ', a.denominacion, ' - ', bc.resolucion) AS BENEFICIO, bf.beneficiario_id AS B_ID, 
				   CONCAT(bf.apellido, ', ', bf.nombre) AS BENEFICIARIO, c.monto AS MONTO, c.fecha AS FECHA, l.denominacion AS LOCALIDAD";
		$from = "beneficiario bf INNER JOIN beneficiobeneficiario bb ON bf.beneficiario_id = bb.compuesto INNER JOIN 
				 beneficio b ON bb.compositor = b.beneficio_id INNER JOIN anexo a ON b.anexo = a.anexo_id INNER JOIN
				 anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id INNER JOIN
				 localidad l ON bc.localidad = l.localidad_id INNER JOIN cuotabeneficio cb ON b.beneficio_id = cb.compuesto INNER JOIN
				 cuota c ON cb.compositor = c.cuota_id";
		$where = filter_input(INPUT_POST, 'condicion');

		switch ($tipo_filtro) {
			case 1:
				$subtitulo = "Listado de beneficiarios";
				break;
			case 2:
				$estado = filter_input(INPUT_POST, "estado");
				$subtitulo = "Listado de beneficiarios";
				if ($estado == 2) {
					$select = "CONCAT(bc.denominacion, ' - ', a.denominacion, ' - ', bc.resolucion) AS BENEFICIO, bf.beneficiario_id AS B_ID, 
				   			   CONCAT(bf.apellido, ', ', bf.nombre) AS BENEFICIARIO, a.monto AS MONTO, '' AS FECHA, l.denominacion AS LOCALIDAD";
					$from = "beneficiario bf INNER JOIN beneficiobeneficiario bb ON bf.beneficiario_id = bb.compuesto INNER JOIN 
					 		 beneficio b ON bb.compositor = b.beneficio_id INNER JOIN anexo a ON b.anexo = a.anexo_id INNER JOIN
					 		 anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id INNER JOIN
					 		 localidad l ON bc.localidad = l.localidad_id";
				}
				break;
		}
		$beneficiario_collection = CollectorCondition()->get('Beneficiario', $where, 4, $from, $select);
		$array_encabezados = array('BENEFICIO', 'FECHA PAGO', 'BENEFICIARIO', 'MONTO', 'LOCALIDAD');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		foreach ($beneficiario_collection as $clave=>$valor) {
			$array_temp = array();
			$array_temp = array(
						  $valor["BENEFICIO"]
						, $valor["FECHA"]
						, $valor["BENEFICIARIO"]
						, $valor["MONTO"]
						, $valor["LOCALIDAD"]);
			$array_exportacion[] = $array_temp;
		}
		
		ExcelReport()->extraer_informe($array_exportacion, $subtitulo);	
	}

	function descargar_excel_beneficiario() {
		require_once "tools/excelreport.php";
		$tipo_filtro = filter_input(INPUT_POST, 'tipo_filtro');
		$select = "CONCAT(b.apellido, ', ', b.nombre) AS BENEFICIARIO, b.documento AS DOCUMENTO, b.telefono AS TELEFONO,
				   bc.denominacion AS BECA, bc.resolucion AS RESOLUCION, lb.denominacion AS LOCALIDAD,
				   a.denominacion AS ANEXO, a.monto AS MONTO";
		$from = "beneficiario b LEFT JOIN beneficiobeneficiario bb ON b.beneficiario_id = bb.compuesto LEFT JOIN
				 beneficio bf ON bb.compositor = bf.beneficio_id INNER JOIN anexo a ON bf.anexo = a.anexo_id INNER JOIN 
				 anexobeca ab ON a.anexo_id = ab.compositor INNER JOIN beca bc ON ab.compuesto = bc.beca_id INNER JOIN 
				 localidad lb ON bc.localidad = lb.localidad_id";
		$where = filter_input(INPUT_POST, 'condicion');
		$beneficiario_collection = CollectorCondition()->get('Beneficiario', $where, 4, $from, $select);

		foreach ($beneficiario_collection as $clave=>$valor) {
		  $localidad = $valor["LOCALIDAD"];  
		}

		switch ($tipo_filtro) {
			case 1:
				$subtitulo = "$localidad";
				break;
			case 2:
				$subtitulo = "Listado de Beneficiarios por monto";
				break;
		}

		$array_encabezados = array('BENEFICIARIO', 'DOCUMENTO', 'MONTO');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		foreach ($beneficiario_collection as $clave=>$valor) {
			$array_temp = array();
			$array_temp = array(
						  $valor["BENEFICIARIO"]
						, $valor["DOCUMENTO"]
						, $valor["MONTO"]);
			$array_exportacion[] = $array_temp;
		}
		
		ExcelReport()->extraer_informe($array_exportacion, $subtitulo);	
	}



}
?>
