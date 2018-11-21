<?php

function obtenerImagenesAC(){
	global $conexion_1;
	$SqlQuery="SELECT * FROM `catalogos_imagenes`;";
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

function obtenerIdImagenPorNombre($nombre){
	global $conexion_2;
	$SqlQuery="SELECT id FROM `imagen_general` WHERE nombre='".$nombre."';";
	$row = $conexion_2->query($SqlQuery);
	if($row->num_rows!=0){
		$fila = $row->fetch_assoc();
		return $fila["id"];
	}else{
		return "1080";
	}
}

function agregarImagenCatalogoN($cod_imagen,$cod_catalogo){
	global $conexion_2;
	$SqlQuery="INSERT INTO catalogo_imagen (cod_catalogo,cod_imagen,registro_usuario) VALUES ('".$cod_catalogo."','".$cod_imagen."',4) ;";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		var_dump("<pre>","Al catalogo:".$cod_catalogo." se le asocio la imagen: ".$cod_imagen,"</pre>");

	}else{
		var_dump("<pre>","Al catalogo:".$cod_catalogo." NO- se le asocio la imagen: ".$cod_imagen. " QUERY : ".$SqlQuery,"</pre>");
	}
}

function agregarImagenMaterialN($cod_material,$cod_imagen){
	global $conexion_2;
	$SqlQuery="INSERT INTO materiales_imagenes (cod_material,cod_imagen,registro_usuario) VALUES ('".$cod_material."','".$cod_imagen."',4) ;";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		var_dump("<pre>","Al material:".$cod_material." se le asocio la imagen: ".$cod_imagen,"</pre>");

	}else{
		var_dump("<pre>","Al material:".$cod_material." NO- se le asocio la imagen: ".$cod_imagen. " QUERY : ".$SqlQuery,"</pre>");
	}
}


function obtenerImagenesCatalogosAsociadasAnterior(){
    global $conexion_local;
    $SqlQuery="
    SELECT
        catalogo_imagen.cod_imagen,
        catalogos.contrato,
        catalogos.nombre
    FROM
        catalogos
        INNER JOIN catalogo_imagen ON catalogo_imagen.cod_catalogo = catalogos.id
    WHERE
        catalogo_imagen.cod_imagen != 1080
    ";
    $row = $conexion_local->query($SqlQuery);
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

function validarImagenNuevoCatalogo($data){
    global $conexion_2;
    $SqlQuery="
        SELECT
            catalogos.contrato,
            catalogo_imagen.cod_imagen
        FROM
            catalogo_imagen
            INNER JOIN catalogos ON catalogo_imagen.cod_catalogo = catalogos.id
        WHERE
            catalogos.contrato = '".$data["contrato"]."'
            AND catalogo_imagen.cod_imagen = '".$data["cod_imagen"]."'
    ";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        return false;
    }else{
        return true;
    }
}


function obtenerImagenesMateriales(){
    global $conexion_local;
    $SqlQuery="
        SELECT
            mst_materiales.numero,
            materiales_imagenes.cod_imagen,
            mst_materiales.material
        FROM
            materiales_imagenes
            INNER JOIN mst_materiales ON mst_materiales.id = materiales_imagenes.cod_material
        WHERE
            materiales_imagenes.cod_imagen != 1080
    ";
    $row = $conexion_local->query($SqlQuery);
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
