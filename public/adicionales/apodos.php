<?php
function obtenerApodosMateriales(){
    global $conexion_1;
    $SqlQuery = "SELECT material,apodo_item FROM `catalogos_items` GROUP BY material";
    $row = $conexion_1->query($SqlQuery);
    if ($row->num_rows != 0) {
        $arrTotal = [];
        while ($fila = $row->fetch_assoc()) {
            array_push($arrTotal, $fila);
        }
        return $arrTotal;
    } else {
        return [];
    }
}

function validarApodoCreado($apodo)
{
    global $conexion_2;
    $SqlQuery = "SELECT id FROM `catalogos_mst_apodos_item` WHERE apodo ='". $apodo ."' AND estado=1;";
    $row = $conexion_2->query($SqlQuery);
    if($row){
        if ($row->num_rows != 0) {
            $fila = $row->fetch_assoc();
            return $fila["id"];
        } else {
            return "";
        }
    }else{
        return "";
    }
    
}

function actualizarRegistrarApodo($idApodo,$nombre){
    global $conexion_2;
    if (!empty($idApodo)) {
        return $idApodo;
    } else {
        $SqlQuery = "INSERT INTO catalogos_mst_apodos_item (cod_empresa,apodo,registro_usuario) VALUES ";
        $SqlQuery .= "(6,'". strtoupper($nombre) ."',4); ";
        $row = $conexion_2->query($SqlQuery);
        if($row){
            return $conexion_2->insert_id;
        }else{
            return "";
        }
    }
}

function validarAsociacionApodoMaterial($material,$id_apodo){
    global $conexion_2;
    $SqlQuery = "SELECT estado,cod_material,cod_apodo FROM `material_apodos` WHERE estado=1 ";
    $SqlQuery .= "AND cod_material='" . $material . "' AND cod_apodo = '". $id_apodo ."';";
    $row = $conexion_2->query($SqlQuery);
    if($row){
        if ($row->num_rows != 0) {
            $fila = $row->fetch_assoc();
            return $fila;
        } else {
            return "";
        }
    }else{
        return "";
    }
}

function guardarApodoMateriales($cod_material,$nombre_apodo){
    global $conexion_2;
    $resultado = validarApodoCreado($nombre_apodo);
    $resultadoApodo = actualizarRegistrarApodo($resultado, $nombre_apodo);
    if(!empty($resultadoApodo)){
        $resultadoAsociacionApodo = validarAsociacionApodoMaterial($cod_material, $resultadoApodo);
        if(empty($resultadoAsociacionApodo) ){
            $SqlQuery = "INSERT INTO material_apodos (cod_apodo,cod_material,registro_usuario) VALUES ";
            $SqlQuery .= " ('". $resultadoApodo ."','". $cod_material ."',4); ";
            $row = $conexion_2->query($SqlQuery);
            if($row){
                var_dump("<pre>","Se asocio correctamente  ". $nombre_apodo." al material ". $cod_material, "</pre>");
            }else{
                var_dump("<pre>", "ERROR: NO se asocio correctamente  " . $nombre_apodo . " al material " . $cod_material, "</pre>");
            }
        }else{
            var_dump("<pre>", "Ya estaba asociado correctamente  " . $nombre_apodo . " al material " . $cod_material, "</pre>");
        }
    }
}

?>