<?php
require_once "modules/localidad/model.php";
require_once "modules/beneficio/model.php";


class Beneficiario extends StandardObject {
	
	function __construct(Localidad $localidad=NULL) {
		$this->beneficiario_id = 0;
		$this->apellido = '';
		$this->nombre = '';
		$this->fecha_nacimiento = '';
		$this->documento = 0;
        $this->cuil = 0;
		$this->domicilio = '';
		$this->telefono = 0;
		$this->correoelectronico = '';
		$this->localidad = $localidad;
		$this->beneficio_collection = array();
	}

	function add_beneficio(Beneficio $beneficio) {
        $this->beneficio_collection[] = $beneficio;
    }
}

class BeneficioBeneficiario {
    
    function __construct(Beneficiario $beneficiario=null) {
        $this->beneficiobeneficiario_id = 0;
        $this->compuesto = $beneficiario;
        $this->compositor = $beneficiario->beneficio_collection;
    }

    function get() {
        $sql = "SELECT compositor FROM beneficiobeneficiario WHERE compuesto=?";
        $datos = array($this->compuesto->beneficiario_id);
        $resultados = execute_query($sql, $datos);
        if($resultados){
            foreach($resultados as $array) {
                $obj = new Beneficio();
                $obj->beneficio_id = $array['compositor'];
                $obj->get();
                $this->compuesto->add_beneficio($obj);
            }
        }
    }

    function save() {
        $this->destroy();
        $tuplas = array();
        $datos = array();
        $sql = "INSERT INTO beneficiobeneficiario (compuesto, compositor)
                VALUES ";
        foreach($this->compositor as $beneficio) {
            $tuplas[] = "(?, ?)";
            $datos[] = $this->compuesto->beneficiario_id;
            $datos[] = $beneficio->beneficio_id;
        }
        $sql .= implode(', ', $tuplas);
        execute_query($sql, $datos);
    }

    function destroy() {
        $sql = "DELETE FROM beneficiobeneficiario WHERE compuesto=?";
        $datos = array($this->compuesto->beneficiario_id);
        execute_query($sql, $datos);
    }
}
?>