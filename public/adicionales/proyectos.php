<?php
function obtenerProyectosParaMigrar(){
    global $conexion_1;
    global $empresa;
    $SqlQuery="
    SELECT catalogos_biblioteca_parametros_sap.id_regla,catalogos_biblioteca_parametros_sap.nombre_regla,
    catalogos_biblioteca_parametros_sap.cod_empresa,catalogos_biblioteca_parametros_sap.estado,
    mst_grupo_compras.grupo_compras,mst_regiones.descripcion AS region,mst_centro_logistico.numero_centro_logistico,
    mst_impuestos.codigo_indicador,mst_almacenes.numero_almacen,mst_imputacion.numero_imputacion,
    mst_elementos_pep.numero elemento_pep FROM catalogos_biblioteca_parametros_sap
    INNER JOIN mst_grupo_compras ON mst_grupo_compras.id_grupo_compras = catalogos_biblioteca_parametros_sap.cod_grupo_compras
    INNER JOIN mst_regiones ON mst_regiones.id_region = catalogos_biblioteca_parametros_sap.cod_region
    INNER JOIN mst_centro_logistico ON mst_centro_logistico.id_centro_logistico = catalogos_biblioteca_parametros_sap.cod_centrol_logistico
    INNER JOIN mst_impuestos ON mst_impuestos.id_impuestos = catalogos_biblioteca_parametros_sap.cod_indicador
    INNER JOIN mst_almacenes ON mst_almacenes.id_almacen = catalogos_biblioteca_parametros_sap.cod_almacen
    INNER JOIN mst_imputacion ON mst_imputacion.id_imputacion = catalogos_biblioteca_parametros_sap.cod_imputacion
    INNER JOIN mst_elementos_pep ON mst_elementos_pep.id_elementopep = catalogos_biblioteca_parametros_sap.cod_imputacion_secundaria
    WHERE
    catalogos_biblioteca_parametros_sap.estado = 1 AND
    catalogos_biblioteca_parametros_sap.cod_empresa = ".$empresa."
    ";
    $row = $conexion_1->query($SqlQuery);
    if($row->num_rows!=0){
        $arrTotal=[];
        while($fila = $row->fetch_assoc()){
            array_push($arrTotal, $fila);
        }
        return $arrTotal;
    }else{
        return [];
    }
}

function obtenerNuevoGrupoCompras($dato){
    global $conexion_2;
    $SqlQuery="SELECT id FROM mst_grupo_compras WHERE numero_grupo_compra='".$dato."' AND estado =1 ";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        $fila = $row->fetch_assoc();
        return $fila["id"];
    }else{
        return 1;
    }
}

function obtenerNuevoRegion($dato){
    global $conexion_2;
    $SqlQuery="SELECT id FROM mst_regiones WHERE region ='".$dato."' AND estado =1 ";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        $fila = $row->fetch_assoc();
        return $fila["id"];
    }else{
        return 1;
    }
}

function obtenerNuevoCentroLogistico($dato){
    global $conexion_2;
    $SqlQuery="SELECT id FROM mst_centro_logistico WHERE estado=1 AND numero_centro='".$dato."' ";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        $fila = $row->fetch_assoc();
        return $fila["id"];
    }else{
        return 1;
    }
}

function obtenerNuevoIndicador($dato){
    global $conexion_2;
    $SqlQuery="SELECT id FROM mst_impuestos WHERE estado=1 AND numero_indicador='".$dato."' ";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        $fila = $row->fetch_assoc();
        return $fila["id"];
    }else{
        return 1;
    }
}

function obtenerNuevoAlmacen($dato){
    global $conexion_2;
    $SqlQuery="SELECT id FROM mst_almacenes WHERE estado=1 AND numero_almacen='".$dato."' ";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        $fila = $row->fetch_assoc();
        return $fila["id"];
    }else{
        return 1;
    }
}

function obtenerNuevoImputacion($dato){
    return 12;
}

function obtenerNuevoElementoPep($dato){
    global $conexion_2;
    $SqlQuery="SELECT id FROM mst_elementos_pep WHERE estado=1 AND numero_pep ='".$dato."' ";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        $fila = $row->fetch_assoc();
        return $fila["id"];
    }else{
        return 1;
    }
}

function validarProyecto($nombre){
    global $conexion_2;
    $SqlQuery = "SELECT id FROM catalogo_biblioteca_grupo_erp_cabecera WHERE nombre = '".$nombre."' AND cod_empresa=6";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        return false;
    }else{
        return true;
    }
}

function obtenerProyecto($nombre){
    global $conexion_2;
    $SqlQuery = "SELECT id FROM catalogo_biblioteca_grupo_erp_cabecera WHERE nombre = '".$nombre."' AND cod_empresa=6";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        $fila = $row->fetch_assoc();
        return $fila["id"];
    }else{
        return "";
    }
}

function actualizarNuevosProyectos($proyectos){
    global $conexion_2;
    foreach ($proyectos as $key => $value) {
        if(validarProyecto($value["titulo"])){
            $registroCabecera = registrarNuevosProyectos($value);
            if($registroCabecera){
                $SqlQuery = "INSERT INTO catalogo_biblioteca_grupo_erp_contenido (codigo_maestra,codigo_parametro,solicitud_manual,registro_usuario,cod_cabecera) VALUES ('".$value["cod_maestra_compras"]."','".$value["cod_grupo_compras"]."','NO',4,'".$registroCabecera."')";
                $row = $conexion_2->query($SqlQuery);
                $SqlQuery = "INSERT INTO catalogo_biblioteca_grupo_erp_contenido (codigo_maestra,codigo_parametro,solicitud_manual,registro_usuario,cod_cabecera) VALUES ('".$value["cod_maestra_region"]."','".$value["cod_region"]."','NO',4,'".$registroCabecera."')";
                $row = $conexion_2->query($SqlQuery);
                $SqlQuery = "INSERT INTO catalogo_biblioteca_grupo_erp_contenido (codigo_maestra,codigo_parametro,solicitud_manual,registro_usuario,cod_cabecera) VALUES ('".$value["cod_maestra_centro_logistico"]."','".$value["cod_centro_logistico"]."','NO',4,'".$registroCabecera."')";
                $row = $conexion_2->query($SqlQuery);
                $SqlQuery = "INSERT INTO catalogo_biblioteca_grupo_erp_contenido (codigo_maestra,codigo_parametro,solicitud_manual,registro_usuario,cod_cabecera) VALUES ('".$value["cod_maestra_indicador"]."','".$value["cod_indicador"]."','NO',4,'".$registroCabecera."')";
                $row = $conexion_2->query($SqlQuery);
                $SqlQuery = "INSERT INTO catalogo_biblioteca_grupo_erp_contenido (codigo_maestra,codigo_parametro,solicitud_manual,registro_usuario,cod_cabecera) VALUES ('".$value["cod_maestra_almacen"]."','".$value["cod_almacen"]."','NO',4,'".$registroCabecera."')";
                $row = $conexion_2->query($SqlQuery);
                $SqlQuery = "INSERT INTO catalogo_biblioteca_grupo_erp_contenido (codigo_maestra,codigo_parametro,solicitud_manual,registro_usuario,cod_cabecera) VALUES ('".$value["cod_maestra_imputacion"]."','".$value["cod_imputacion"]."','NO',4,'".$registroCabecera."')";
                $row = $conexion_2->query($SqlQuery);
                $SqlQuery = "INSERT INTO catalogo_biblioteca_grupo_erp_contenido (codigo_maestra,codigo_parametro,solicitud_manual,registro_usuario,cod_cabecera) VALUES ('".$value["cod_maestra_pep"]."','".$value["cod_pep"]."','NO',4,'".$registroCabecera."')";
                $row = $conexion_2->query($SqlQuery);
                var_dump("<pre>Se han registrado los atributos(Maestras)  exitosamente!</pre>");
            }
        }
    }
}

function registrarNuevosProyectos($proyecto){

    global $empresa;
    global $conexion_2;
    $SqlQuery = "INSERT INTO catalogo_biblioteca_grupo_erp_cabecera (cod_empresa,nombre,flujo_aprobacion,niveles_aprobacion,registro_usuario) VALUES ('".$empresa."','".$proyecto["titulo"]."','SI',2,4)";
    $row = $conexion_2->query($SqlQuery);
    if($row){
        var_dump("<pre>Proyecto ".$proyecto["titulo"]." registrado exitosamente!</pre>");
        return $conexion_2->insert_id;
    }else{
        var_dump("<pre>Error: No se logro guardar proyecto ".$proyecto["titulo"].".</pre>");
        return false;
    }
}

function validarProyectoCatalogo($proyecto,$catalogo){
    global $conexion_2;
    $SqlQuery="SELECT * FROM catalogo_grupos_erp WHERE cod_catalogo='".$catalogo."' AND cod_cabecera ='".$proyecto."' AND estado =1";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        return false;
    }else{
        return true;
    }
}

function obtenerParametrosProyecto($id){
    global $conexion_2;
    $SqlQuery="
        SELECT
            codigo_maestra,
            codigo_parametro
        FROM
            catalogo_biblioteca_grupo_erp_contenido
        WHERE
            cod_cabecera = ".$id."
            AND estado = 1
    ";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        $arrtotal=[];
        while ($fila = $row->fetch_assoc()) {
            array_push($arrtotal,$fila);
        }
        return $arrtotal;
    }else{
        return [];
    }
}

function asignarParametrosSAPcompra($compra,$reglaSAP){
    global $conexion_2;
    $resultadoMaestra = obtenerParametrosProyecto($reglaSAP);
    foreach ($resultadoMaestra as $key => $value) {
        $SqlQuery = "
            INSERT INTO catalogo_carro_compras_atributos_contenido (cod_maestra,cod_parametro,cod_cabecera)
            VALUES ('".$value["codigo_maestra"]."','".$value["codigo_parametro"]."','".$compra."');
        ";
        $row = $conexion_2->query($SqlQuery);
    }
}

?>
