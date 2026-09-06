<?php
function execute_query($sql, $datos=array()) {
     $conexion = "mysql:host=". DB_HOST .";dbname=". DB_NAME .";charset=utf8";
     $opciones = array(
         PDO::ATTR_PERSISTENT => true,
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_EMULATE_PREPARES => true
     );
     $conn = new PDO($conexion, DB_USER, DB_PASS, $opciones);
     $query = $conn->prepare($sql);
     // execute() recibe los valores directamente y evita problemas de
     // referencias al usar bindParam() dentro de un foreach.
     $query->execute(array_values($datos));
     $id_ingresado = $conn->lastInsertId();
     // UPDATE, INSERT y DELETE no devuelven un result set. Intentar
     // fetchAll() sobre ellos provoca "SQLSTATE[HY000]: General error" en
     // PDO con MariaDB.
     $registros_leidos = ($query->columnCount() > 0)
         ? $query->fetchAll(PDO::FETCH_ASSOC)
         : array();
     return ($registros_leidos) ? $registros_leidos : $id_ingresado;
}

function has_related_records($sql, $datos=array()) {
    $resultado = execute_query($sql, $datos);
    return !empty($resultado) && isset($resultado[0]['total'] ) && ((int) $resultado[0]['total'] > 0);
}
?>
