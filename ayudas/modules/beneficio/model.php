<?php
require_once "modules/anexo/model.php";
require_once "modules/cuota/model.php";


class Beneficio extends StandardObject {
	
	function __construct(Anexo $anexo=NULL) {
		$this->beneficio_id = 0;
		$this->fecha_alta = '';
		$this->estado = 0;
        $this->anexo = $anexo;
		$this->cuota_collection = array();
	}

	function add_cuota(Cuota $cuota) {
        $this->cuota_collection[] = $cuota;
    }
}

class CuotaBeneficio {
    
    function __construct(Beneficio $beneficio=null) {
        $this->cuotabeneficio_id = 0;
        $this->compuesto = $beneficio;
        $this->compositor = $beneficio->cuota_collection;
    }

    function get() {
        $sql = "SELECT compositor FROM cuotabeneficio WHERE compuesto=?";
        $datos = array($this->compuesto->beneficio_id);
        $resultados = execute_query($sql, $datos);
        if(!empty($resultados) && $resultados != 0){
            foreach($resultados as $array) {
                $obj = new Cuota();
                $obj->cuota_id = $array['compositor'];
                $obj->get();
                $this->compuesto->add_cuota($obj);
            }
        }
    }

    function save() {
        $this->destroy();
        $tuplas = array();
        $datos = array();
        $sql = "INSERT INTO cuotabeneficio (compuesto, compositor)
                VALUES ";
        foreach($this->compositor as $cuota) {
            $tuplas[] = "(?, ?)";
            $datos[] = $this->compuesto->beneficio_id;
            $datos[] = $cuota->cuota_id;
        }
        $sql .= implode(', ', $tuplas);
        execute_query($sql, $datos);
    }

    function destroy() {
        $sql = "DELETE FROM cuotabeneficio WHERE compuesto=?";
        $datos = array($this->compuesto->beneficio_id);
        execute_query($sql, $datos);
    }
}
?>