<?php

function obtenerCatalogosAC($empresa){
	global $conexion_1;
	$SqlQuery="SELECT * FROM catalogos INNER JOIN _0002103 ON catalogos.cod_proveedor = _0002103.nitempxx ";
	$SqlQuery.="WHERE catalogos.id_empresa='".$empresa."'";
	$row = $conexion_1->query($SqlQuery);
	if($row->num_rows!=0){
		$arrTotal=[];
		while($fila = $row->fetch_assoc()){
			$fila["item"] = obtenerItemsCatalogoAC($fila["id_catalogo"]);
            $fila["proyectos"] = obtenerProyectosCatalogo($fila["id_catalogo"]);
			$fila["imagen"] = obtenerImagenAC($fila["imagen_catalogo"]);
			array_push($arrTotal, $fila);
		}
		return $arrTotal;
	}else{
		return [];
	}
}

function obtenerProyectosCatalogo($catalogo){
    global $conexion_1;
    $SqlQuery="
            SELECT
                catalogos_biblioteca_parametros_sap.nombre_regla
            FROM
                catalogos_asignacion_proyectos
                INNER JOIN catalogos_biblioteca_parametros_sap ON catalogos_asignacion_proyectos.cod_proyecto = catalogos_biblioteca_parametros_sap.id_regla
            WHERE
                catalogos_asignacion_proyectos.cod_catalogo = ".$catalogo."
        ";

    $row = $conexion_1->query($SqlQuery);
    if($row->num_rows!=0){
        $arrTotal=[];
        while($fila = $row->fetch_assoc()){
            array_push($arrTotal, $fila["nombre_regla"]);
        }
        return $arrTotal;
    }else{
        return [];
    }
}

function obtenerItemsCatalogoAC($catalogo){
	global $conexion_1;
	$SqlQuery="SELECT CI.id_item,CI.id_catalogo,CI.id_producto,CI.cod_posicion_sap,CI.material, ";
	$SqlQuery.="CI.numero_parte_item,CI.valor_unitario_item,CI.observacion_item,CI.imagenes_item, ";
	$SqlQuery.="CI.fecha_creacion,CI.usuario_creacion,CI.usuario_actualizacion,CI.estado, ";
	$SqlQuery.="CI.dias_entrega,CI.acceso_rapido,CI.item_principal,CI.items_asociados, ";
	$SqlQuery.="CI.color,CI.apodo_item,CI.fecha_actualizacion,CI.disponible,PR.imagenes_producto ";
	$SqlQuery.="FROM catalogos_items CI INNER JOIN productos PR ON PR.id_producto = CI.id_producto ";
	$SqlQuery.="WHERE CI.id_catalogo='".$catalogo."';";
	$row = $conexion_1->query($SqlQuery);
	if($row){
		if($row->num_rows!=0){
			$arrTotal=[];
			while($fila = $row->fetch_assoc()){
				$arrListadoImagenes=[];
				$arrImagenes=json_decode($fila["imagenes_producto"]);
				foreach ($arrImagenes as $key => $value) {
					$resultado=obtenerImagenAC($value);
					array_push($arrListadoImagenes,$resultado);
				}
				$fila["imagenes"]=$arrListadoImagenes;
				array_push($arrTotal, $fila);
			}
			return $arrTotal;
		}else{
			return [];
		}
	}else{
		var_dump("<pre>","Consulta erronea : ".$SqlQuery,"</pre>");
	}

}

function obtenerImagenAC($id){
	global $conexion_1;
	$SqlQuery="SELECT * FROM `catalogos_imagenes` WHERE id_archivo='".$id."';";
	$row = $conexion_1->query($SqlQuery);
	if($row){
		if($row->num_rows!=0){
			$fila = $row->fetch_assoc();
			return $fila;
		}else{
			return [];
		}

	}else{
		return [];
	}
}

function actualizarCatalogosN($arr){

	$respuestaCatalogo=validarCatalogoContrato($arr["cod_sap"]);
	$idCatalogo="";
	if(!empty($respuestaCatalogo)){
		$idCatalogo=$respuestaCatalogo;
		actualizarCatalogoN($arr,$idCatalogo);
	}else{
		$idCatalogo=registrarCatalogoN($arr);
	}

	if(!empty($idCatalogo)){
		actualizarInformacionItems($arr["item"],$idCatalogo);
		actualizarInformacionImagenesCatalogo($arr["imagen"],$idCatalogo);
        actualizarInformacionProyectosCatalogos($arr["proyectos"],$idCatalogo);
		var_dump("<pre>","Catalogo creado o actualizado ".$arr["cod_sap"]." - ".$idCatalogo,"</pre>");

	}else{
		var_dump("Codigo de catalogo no identificado: Contrato - ".$arr["cod_sap"]);
	}

}

function validarCatalogoContrato($contrato){
	global $conexion_2;
	$SqlQuery="SELECT id FROM `catalogos` WHERE contrato='".$contrato."';";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		if($row->num_rows!=0){
			$fila = $row->fetch_assoc();
			return $fila["id"];
		}else{
			return "";
		}
	}else{
		var_dump("<pre>","Consulta erronea : ".$SqlQuery,"</pre>");
	}
}

function registrarCatalogoN($arr){
	global $conexion_2;
	$SqlQuery="INSERT INTO catalogos (cod_empresa,cod_proveedor,contrato,nombre,moneda,descripcion_larga) ";
	$SqlQuery.="VALUES (6,'".$arr["id_empresa"]."','".$arr["cod_sap"]."','".$arr["nombre_catalogo"]."', ";
	$SqlQuery.="'".$arr["moneda_catalogo"]."',''); ";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		return $conexion_2->insert_id;
	}else{
		var_dump("<pre>","Consulta erronea : ".$SqlQuery,"</pre>");
	}
}

function actualizarCatalogoN($arr,$id){
	global $conexion_2;
	$SqlQuery="UPDATE catalogos SET cod_proveedor='".$arr["id_empresa"]."',contrato='".$arr["cod_sap"]."',  ";
	$SqlQuery.="nombre='".$arr["nombre_catalogo"]."',moneda='".$arr["moneda_catalogo"]."' WHERE id='".$id."';";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		return $id;
	}else{
		var_dump("<pre>","Consulta erronea : ".$SqlQuery,"</pre>");
	}
}

function actualizarInformacionProyectosCatalogos($proyectos,$catalogo){
    global $conexion_2;
    foreach ($proyectos as $key => $value) {
        $id_proyecto = obtenerProyecto($value);
        if(!empty($id_proyecto)){
            if(validarProyectoCatalogo($id_proyecto,$catalogo)){
                $SqlQuery = "INSERT INTO catalogo_grupos_erp (cod_cabecera,cod_catalogo,registro_usuario) VALUES ('".$id_proyecto."','".$catalogo."',4);";
                $row = $conexion_2->query($SqlQuery);
            }
        }
    }


}

function actualizarInformacionImagenesCatalogo($imagen,$catalogo){
	$respuesta=obtenerIdImagenPorNombre($imagen["nombre_archivo"]);
	$resultadoValidacion=validarImagenCatalogo($respuesta,$catalogo);
	if($resultadoValidacion){
		var_dump("<pre>","El catalogo:".$catalogo." Ya tiene asociada la imagen: ".$imagen["nombre_archivo"],"</pre>");
	}else{
        if($respuesta != "1080"){
		  agregarImagenCatalogoN($respuesta,$catalogo);
        }
	}
}

function validarImagenCatalogo($imagen,$catalogo){
	global $conexion_2;
	$SqlQuery="SELECT * FROM `catalogo_imagen` WHERE cod_catalogo='".$catalogo."' AND cod_imagen='".$imagen."';";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		if($row->num_rows!=0){
			return true;
		}else{
			return false;
		}
	}else{
		var_dump("<pre>","Consulta erronea : ".$SqlQuery,"</pre>");
	}
}


function actualizarInformacionItems($arrItem,$catalogo){
	global $conexion_2;
	foreach ($arrItem as $key => $value) {
		$codItem="";
		$respuestaMaterial=obtenerMaterialNumero($value["material"]);
		$respuesta=validarItemCatalogo($value["material"],$catalogo);
		if(!empty($respuesta)){
			$codItem=$respuesta;
			actualizarItemN($value,$catalogo,$respuestaMaterial,$respuesta);
		}else{
            if(!empty($respuestaMaterial)){
    			$codItem=registrarItem($value,$catalogo,$respuestaMaterial);
            }else{
                var_dump("<pre>","numero de material: ".$value["material"]." NO encontrado. Valide","</pre>");
            }
		}

		if(!empty($codItem)){
			if(!empty($value["imagenes"])){
				actualizarInformacionImagenesItem($value["imagenes"],$respuestaMaterial);
			}else{
				var_dump("<pre>","El item NO tiene imagenes para el catalogo  : ".$catalogo." y numero de material: ".$value["material"],"</pre>");
			}

		}else{
			var_dump("<pre>","ERROR: No se logro obtener codigo item para el catalogo  : ".$catalogo." y numero de material: ".$value["material"],"</pre>");
		}
	}
}

function validarItemCatalogo($codMaterial,$codCatalogo){
	global $conexion_2;
	$SqlQuery="SELECT catalogo_items.id FROM catalogo_items INNER JOIN mst_materiales ON  ";
	$SqlQuery.="catalogo_items.cod_material = mst_materiales.id ";
	$SqlQuery.="WHERE catalogo_items.cod_catalogo = '".$codCatalogo."' AND mst_materiales.numero = '".$codMaterial."';";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		if($row->num_rows!=0){
			$fila = $row->fetch_assoc();
			return $fila["id"];
		}else{
			return "";
		}
	}else{
		var_dump("<pre>","Consulta erronea : ".$SqlQuery,"</pre>");
	}

}

function actualizarItemN($arrItem,$idCatalogo,$Material,$idItem){
	global $conexion_2;
	$SqlQuery="UPDATE catalogo_items SET valor='".$arrItem["valor_unitario_item"]."',dias_entrega='".$arrItem["dias_entrega"]."', ";
	$SqlQuery.="posicion='".$arrItem["cod_posicion_sap"]."',cod_material='".$Material."' WHERE id ='".$idItem."'; ";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		var_dump("<pre>","Item de material: ".$Material." actualizado al catalogo : ".$idCatalogo,"</pre>");
		return $conexion_2->insert_id;
	}else{
		var_dump("<pre>","Consulta erronea : ".$SqlQuery,"</pre>");
	}
}

function registrarItem($arrItem,$idCatalogo,$Material){
	global $conexion_2;
	$SqlQuery="INSERT INTO catalogo_items (valor,disponible,dias_entrega,registro_usuario,posicion,cod_catalogo,";
	$SqlQuery.="cod_producto,cod_material) VALUES ('".$arrItem["valor_unitario_item"]."','SI','".$arrItem["dias_entrega"]."',4,";
	$SqlQuery.="'".$arrItem["cod_posicion_sap"]."','".$idCatalogo."','1','".$Material."');";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		var_dump("<pre>","Item de material: ".$Material." asociado al catalogo : ".$idCatalogo,"</pre>");
		return $conexion_2->insert_id;
	}else{
		var_dump("<pre>","Consulta erronea : ".$SqlQuery,"</pre>");
	}
}

function actualizarInformacionImagenesItem($arrImagen,$idMaterial){
	foreach ($arrImagen as $key => $value) {
		$respuestaIdImagen=obtenerIdImagenPorNombre($value["nombre_archivo"]);
		$respuestaValidacionImagen=validarImagenMaterial($idMaterial,$respuestaIdImagen);
		if($respuestaValidacionImagen){
			var_dump("<pre>","El material:".$idMaterial." Ya tiene asociada la imagen: ".$value["nombre_archivo"],"</pre>");
		}else{
            if($value["nombre_archivo"] != "sin_productos.png"){
                if($respuestaIdImagen != "1080"){
			         agregarImagenMaterialN($idMaterial,$respuestaIdImagen);
                }
            }
		}
	}
}

function obtenerItemCatalogoPosSapMigrados($catalogo,$posSAP){
    global $conexion_2;
    $SqlQuery="
        SELECT
            *
        FROM
            catalogo_items
        WHERE
            cod_catalogo = '".$catalogo."'
            AND posicion = '".$posSAP."'
    ";
    $row = $conexion_2->query($SqlQuery);
    if($row){
        $fila = $row->fetch_assoc();
        return $fila;
    }else{
        return false;
    }
}




