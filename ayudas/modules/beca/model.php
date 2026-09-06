<?php
require_once "modules/tipobeca/model.php";
require_once "modules/localidad/model.php";
require_once "modules/anexo/model.php";
require_once "modules/plan/model.php";


class Beca extends StandardObject {
	
	function __construct(TipoBeca $tipobeca=NULL, Localidad $localidad=NULL, Plan $plan=NULL) {
		$this->beca_id = 0;
		$this->denominacion = '';
		$this->resolucion = '';
		$this->fecha = '';
		$this->detalle = 0;
        $this->tipobeca = $tipobeca;
        $this->localidad = $localidad;
		$this->plan = $plan;
		$this->anexo_collection = array();
	}

	function add_anexo(Anexo $anexo) {
        $this->anexo_collection[] = $anexo;
    }
}

class AnexoBeca {
    
    function __construct(Beca $beca=null) {
        $this->anexobeca_id = 0;
        $this->compuesto = $beca;
        $this->compositor = $beca->anexo_collection;
    }

    function get() {
        $sql = "SELECT compositor FROM anexobeca WHERE compuesto=?";
        $datos = array($this->compuesto->beca_id);
        $resultados = execute_query($sql, $datos);
        if($resultados){
            foreach($resultados as $array) {
                $obj = new Anexo();
                $obj->anexo_id = $array['compositor'];
                $obj->get();
                $this->compuesto->add_anexo($obj);
            }
        }
    }

    function save() {
        $this->destroy();
        $tuplas = array();
        $datos = array();
        $sql = "INSERT INTO anexobeca (compuesto, compositor)
                VALUES ";
        foreach($this->compositor as $anexo) {
            $tuplas[] = "(?, ?)";
            $datos[] = $this->compuesto->beca_id;
            $datos[] = $anexo->anexo_id;
        }
        $sql .= implode(', ', $tuplas);
        execute_query($sql, $datos);
    }

    function destroy() {
        $sql = "DELETE FROM anexobeca WHERE compuesto=?";
        $datos = array($this->compuesto->beca_id);
        execute_query($sql, $datos);
    }
}
?>