<?php
function obtenerMaterialesAC(){
	global $conexion_1;
	$SqlQuery="SELECT material FROM `catalogos_items` GROUP BY material";
	$row = $conexion_1->query($SqlQuery);
	if($row->num_rows!=0){
		$arrTotal=[];
		while($fila = $row->fetch_assoc()){
			array_push($arrTotal, $fila["material"]);
		}
		return $arrTotal;
	}else{
		return [];
	}
}

function obtenerFamiliaMaterial($familia){
	global $conexion_2;
	$SqlQuery="SELECT id FROM `mst_familias` WHERE numero_familia = '".$familia."'";
	$row = $conexion_2->query($SqlQuery);
	if($row->num_rows!=0){
		$fila = $row->fetch_assoc();
		return $fila["id"];
	}else{
		return 1;
	}
}

function obtenerUnidadMedida($unidadMedida){
	global $conexion_2;
	$SqlQuery="SELECT id FROM `mst_unidad_medida` WHERE um = '".$unidadMedida."'";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		if($row->num_rows!=0){
			$fila = $row->fetch_assoc();
		return $fila["id"];
		}else{
			return 1;
		}
	}else{
		return 1;
	}

}


function actualizarTablaMateriales($arr){
	global $conexion_2;

	foreach ($arr as $key => $value) {
		$respuestaValidacionMaterial=validarExistenciaMaterial($value["material"]);
		if($respuestaValidacionMaterial){
			registrarNuevoMaterial($value);
		}else{
			var_dump("<pre>","El material ".$value["descripcion"]." ya se encuentra registrado en Intelcost.","</pre>");
		}
	}


}

function validarExistenciaMaterial($numeroMaterial){
	global $conexion_2;
	$SqlQuery="SELECT id FROM `mst_materiales` WHERE numero = '".$numeroMaterial."'";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		if($row->num_rows==0){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function registrarNuevoMaterial($arr){
	global $conexion_2;
	$SqlQuery="INSERT INTO mst_materiales (cod_empresa,numero,material,registro_usuario,cod_medida,cod_familia) VALUES ";
	$SqlQuery.="(6,'".$arr["material"]."','".$arr["descripcion"]."',4,'".$arr["medida"]."','".$arr["familia"]."');";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		var_dump("<pre>","Material registrado: ".$arr["material"]." - ".$arr["descripcion"],"</pre>");
	}else{
		var_dump("<pre>","Material NO registrado: ".$arr["material"]." - ".$arr["descripcion"],"</pre>");
	}
}

function obtenerMaterialWs($material){
	$arrResultado=[];
	ini_set('soap.wsdl_cache_enabled', 0);
	ini_set('soap.wsdl_cache_ttl', 0);
	$clientparams           = array(
		'login'    => "INTELCOST",
		'password' => "Steven2018",
		'trace'    => 1,
		'encoding' => 'UTF-8',
		'cache_wsdl' => WSDL_CACHE_NONE
	);
	$soapClient = new \SoapClient("http://conclus01.conconcreto.com:8001/sap/bc/srt/wsdl/flv_10002A101AD1/srvc_url/sap/bc/srt/rfc/sap/zitc_g_po/300/zitc_g_po/zitc_g_po?sap-client=300", $clientparams);
	try {

		$ItConsult = array(
			'Tipo'        => "",
			'Familia'     => "",
			'Fecha'       => "",
			'Texto'       => "",
			'DatosCentro' => "",
			'Borrado'     => "",
			'Bloqueado'   => "",
		);

		if (isset($material) && !empty($material)) {
			$Material     = array(
				'Material' => str_pad($material, 18, "0", STR_PAD_LEFT),
			);
			$ItMatnr = array(
				'item' => $Material,
			);
		} else {
			$ItMatnr = array();
		}
		$ItWerks = array();

        $respuestaWsdl ="";
		$respuestaWsdl = $soapClient->ZitcFMaterialConsult(array('ItConsult' => $ItConsult, 'ItMatnr' => $ItMatnr, 'ItWerks' => $ItWerks));

		if (isset($respuestaWsdl->EtDatbasic) && !empty($respuestaWsdl->EtDatbasic)) {
			if (isset($respuestaWsdl->EtDatbasic->item) && !empty($respuestaWsdl->EtDatbasic->item)) {
				if(is_array($respuestaWsdl->EtDatbasic->item)){
					$arrResultado["bandera"] = true;
					$arrResultado["respuesta"]  = $respuestaWsdl->EtDatbasic->item;
				}else{
					$arrmateriales = [];
					array_push($arrmateriales,$respuestaWsdl->EtDatbasic->item);
					$arrResultado["bandera"] = true;
					$arrResultado["respuesta"]  = $arrmateriales;
				}

			} else {
				$arrResultado["bandera"] = false;
				$arrResultado["respuesta"]  = "La consulta no logro obtener resultados.";
			}

		} else {
			$arrResultado["bandera"] = false;
			$arrResultado["respuesta"]  = "La consulta no logro obtener resultados.";
		}

	} catch (SoapFault $fault) {
		$arrResultado["bandera"] = false;
		$arrResultado["respuesta"] = "<p>Error conexion</p>" . $fault->faultcode . " - " . $fault->faultstring;
	}

	return $arrResultado;
}

function obtenerMaterialNumero($numeroMaterial){
	global $conexion_2;
	$SqlQuery="SELECT id FROM `mst_materiales` WHERE numero = '".$numeroMaterial."'";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		if($row->num_rows!=0){
			$fila = $row->fetch_assoc();
			return $fila["id"];
		}else{
			return "";
		}
	}else{
		return "";
	}
}

function validarImagenMaterial($material,$iamgen){
	global $conexion_2;
	$SqlQuery="SELECT * FROM `materiales_imagenes` WHERE cod_imagen='".$iamgen."' AND cod_material='".$material."';";
	$row = $conexion_2->query($SqlQuery);
	if($row){
		if($row->num_rows!=0){
			return false;
		}else{
            return true;

		}
	}else{
		var_dump("<pre>","Consulta erronea : ".$SqlQuery,"</pre>");
	}
}


?>
