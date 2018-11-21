<?php
function obtenerOrdenesCompra(){
    global $conexion_1;
    global $empresa;
    $SqlQuery="
        SELECT
            catalogos_biblioteca_parametros_sap.nombre_regla,
            catalogos_grupo_solicitudes.id_grupo_solicitud,
            catalogos_grupo_solicitudes.cod_catalogo,
            catalogos_grupo_solicitudes.usuario_creacion,
            catalogos_grupo_solicitudes.fecha_creacion,
            catalogos_grupo_solicitudes.usuario_actualizacion,
            catalogos_grupo_solicitudes.estado,
            catalogos_grupo_solicitudes.fecha_actualizacion,
            catalogos_grupo_solicitudes.cod_usuario_proveedor,
            catalogos_grupo_solicitudes.numero_compra,
            catalogos_grupo_solicitudes.usuario_aprobacion,
            catalogos_grupo_solicitudes.observaciones,
            catalogos.cod_sap
        FROM
            catalogos_grupo_solicitudes
            LEFT JOIN catalogos_biblioteca_parametros_sap ON catalogos_grupo_solicitudes.grupo_compras = catalogos_biblioteca_parametros_sap.cod_grupo_compras
            INNER JOIN catalogos ON catalogos_grupo_solicitudes.cod_catalogo = catalogos.id_catalogo
        WHERE
            catalogos_grupo_solicitudes.cod_empresa = ".$empresa."
        GROUP BY
            catalogos_grupo_solicitudes.id_grupo_solicitud
    ";
    $row = $conexion_1->query($SqlQuery);
    if ($row->num_rows != 0) {
        $arrTotal = [];
        while ($fila = $row->fetch_assoc()) {
            $fila["solicitudes"] = obtenerSolicitudesOrden($fila["id_grupo_solicitud"]);
            $fila["comentarios"] = obtenerComentariosOrden($fila["id_grupo_solicitud"]);
            array_push($arrTotal, $fila);
        }
        return $arrTotal;
    } else {
        return [];
    }
}

function obtenerComentariosOrden($id){
    global $conexion_1;
    global $empresa;
    $SqlQuery="
        SELECT
            observacion,
            fecha_creacion,
            usuario_creacion
        FROM
            catalogos_observaciones_solicitudes
        WHERE
            cod_grupo_solicitud = ".$id."
    ";
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

function obtenerSolicitudesOrden($cabecera){
    global $conexion_1;
    global $empresa;
    $SqlQuery="
        SELECT
            catalogos_solicitudes.id_solicitud,
            catalogos_solicitudes.cod_item,
            catalogos_solicitudes.cantidad_solicitud,
            catalogos_solicitudes.fecha_entrega_sap,
            catalogos_solicitudes.fecha_creacion,
            catalogos_solicitudes.usuario_creacion,
            catalogos_solicitudes.fecha_actualizacion,
            catalogos_solicitudes.usuario_actualizacion,
            catalogos_solicitudes.estado,
            catalogos_items.material,
            catalogos_items.cod_posicion_sap
        FROM
            catalogos_solicitudes
            INNER JOIN catalogos_items ON catalogos_items.id_item = catalogos_solicitudes.cod_item
        WHERE
            catalogos_solicitudes.cod_grupo_solicitud =".$cabecera."
    ";
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

function crearComentarioCompra($id,$dataObs,$tipo){
    global $conexion_2;
    $SqlQuery="
        INSERT INTO catalogo_carro_compras_aprobaciones (tipo,observaciones,registro_usuario,created_at,cod_cabecera)
        VALUES (".$tipo.",'".$dataObs["observacion"]."','".$dataObs["usuario_creacion"]."','".$dataObs["fecha_creacion"]."','".$id."')
    ";
    $row = $conexion_2->query($SqlQuery);
    if($row){
        return true;
    }else{
        return false;
        var_dump("<pre>","Error consulta crear comentario  ".$SqlQuery,"</pre>");
    }

}


function actualizarOrdenesCompra($compra){
    global $conexion_2;
    $regla = obtenerProyecto($compra["nombre_regla"]);
    $respuestaCatalogo=validarCatalogoContrato($compra["cod_sap"]);
    $idCatalogo="";
    if(!empty($respuestaCatalogo)){
        $idCatalogo=$respuestaCatalogo;

        switch ($compra["estado"]) {
            case 'activo':
                $compra["estado"]="activo";
            break;
            case 'eliminado':
                $compra["estado"]="eliminado";
            break;
            case 'finalizado':
                $compra["estado"]="finalizado";
            break;
            case 'en aprobacion':
                $compra["estado"]="en aprobacion";
            break;
            case 'cancelado':
                $compra["estado"]="denegado";
            break;
            default:
                $compra["estado"]="eliminado";
            break;
        }

        $SqlQuery = "INSERT INTO catalogo_carro_compras_cabecera (cod_empresa,cod_usuario_proveedor,observaciones,solicitud_inventario,numero_compra,regla_asociada,registro_usuario,created_at,updated_at,estado,cod_catalogo,compra_express) VALUES (6,'".$compra["cod_usuario_proveedor"]."','".$compra["observaciones"]."','NO','".$compra["numero_compra"]."','".$regla."','".$compra["usuario_creacion"]."','".$compra["fecha_creacion"]."','".$compra["fecha_actualizacion"]."','".$compra["estado"]."','".$idCatalogo."','NO') ";
        $row = $conexion_2->query($SqlQuery);
        if ($row) {
            $idCompra = $conexion_2->insert_id;
            var_dump("<pre>","Exito - se ha creado la compra ".$idCompra,"</pre>");


            //adicionar comentarios a la comora
            if(isset($compra["comentarios"]) && !empty($compra["comentarios"]) && count($compra["comentarios"]) != 0){
                foreach ($compra["comentarios"] as $key => $value) {
                    crearComentarioCompra($idCompra,$value,3);
                }
            }

            //Se adiciona finalizador
            if(isset($compra["numero_compra"]) && !empty($compra["numero_compra"])){ //Si tiene numero de compra, tiene finalizador
                $observacionesFinalizador = [];
                $observacionesFinalizador["observacion"]="";
                $observacionesFinalizador["usuario_creacion"]=$compra["usuario_aprobacion"];
                $observacionesFinalizador["fecha_creacion"]=$compra["fecha_actualizacion"];
                crearComentarioCompra($idCompra,$observacionesFinalizador,4);
            }

            //Se adicionan parametros SAP para la compra
            if(!empty($regla)){
                asignarParametrosSAPcompra($idCompra,$regla);
            }

            foreach ($compra["solicitudes"] as $key => $value) {


                //obtenerInformacionItem
                $respuestaItem = obtenerItemCatalogoPosSapMigrados($idCatalogo,$value["cod_posicion_sap"]);
                if($respuestaItem && !empty($respuestaItem)){


                    $SqlQuery="
                        INSERT INTO catalogo_carro_compras_contenido (cantidad,valor,dias_entrega,registro_usuario,created_at,cod_cabecera,cod_item)
                        VALUES ('".$value["cantidad_solicitud"]."','".$respuestaItem["valor"]."','".$respuestaItem["dias_entrega"]."','".$value["usuario_creacion"]."','".$value["fecha_creacion"]."','".$idCompra."','".$respuestaItem["id"]."');
                    ";
                    $row = $conexion_2->query($SqlQuery);
                    if ($row) {
                        var_dump("<pre>","Se adiciono correctamente item compra a la orden ".$idCompra,"</pre>");
                    }else{
                        var_dump("<pre>","Consulta erronea guardar item compra ".$SqlQuery,"</pre>");
                    }
                }else{
                    var_dump("<pre>","No se logro encontrar el item para el catalogo ".$idCatalogo." codigo SAP ".$value["cod_posicion_sap"],"</pre>");
                }

            }


        } else {
            var_dump("<pre>","Error consulta - insertar cabecera compra ".$SqlQuery,"</pre>");
        }
    }


}

?>
