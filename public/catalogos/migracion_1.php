<?php

$conexion_1 = mysqli_connect("127.0.0.1", "root", "", "cliente_local");
$conexion_2 = mysqli_connect("127.0.0.1", "root", "", "cliente_local");
$conexion_1->set_charset("utf8");
$conexion_2->set_charset("utf8");
$empresa = 6;

if (!$conexion_1) {
    echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
    echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
    echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

if (!$conexion_2) {
    echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
    echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
    echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

/*$respuestaProductosOld=obtenerProductosBDAntigua();

while ($fila = $respuestaProductosOld->fetch_assoc()) {
    $obj = new stdClass();
    $obj->id_producto=$fila["id_producto"];
    $obj->cod_categoria=$fila["cod_categoria"];
    $obj->titulo_producto=$fila["titulo_producto"];
    $obj->descripcion_producto=$fila["descripcion_producto"];
    $obj->unidad_medida=$fila["unidad_medida"];
    $obj->imagenes_producto=$fila["imagenes_producto"];
    $obj->usuario_creacion=$fila["usuario_creacion"];
    if(isset($obj->imagenes_producto) && !empty($obj->imagenes_producto)){
        $obj->imagenes_producto = json_decode($obj->imagenes_producto);
        $obj->arrImagenesNuevaBd=[];
        foreach ($obj->imagenes_producto as $key => $value) {
            $respuestaImagenes=obtenerInformacionImagen($value);
            if(isset($respuestaImagenes) && !empty($respuestaImagenes)){
                $nombreArchivo=$respuestaImagenes["nombre_archivo"];
                $imagen = obtenerImagenBDgeneral($nombreArchivo);
                if(isset($imagen) && !empty($imagen)){
                    array_push($obj->arrImagenesNuevaBd, $imagen["id"]);
                }
            }
        }

    }else{
        $obj->arrImagenesNuevaBd=[];
    }
    registrarNuevoProducto($obj);
    echo('<pre>');
        var_dump($obj);
    echo('</pre>');
}
echo "Productos REGISTRADOS. \n";
*/



$respuestaCatalogosOold=obtenerListadoCatalogosBDAntigua();
while ($fila = $respuestaCatalogosOold->fetch_assoc()) {
    $obj = new stdClass();
    $obj->id=$fila["id_catalogo"];
    $obj->empresa=$fila["id_empresa"];
    $obj->cod_proveedor=$fila["cod_proveedor"];
    $obj->decripcion_corta="";
    $obj->nombre=$fila["nombre_catalogo"];
    $obj->descripcion_larga=$fila["descripcion_catalogo"];
    $obj->fecha_inicio="";
    $obj->fecha_fin="";
    $obj->flujo_aprobacion="";
    $obj->solicitud_adjunto="";
    $obj->registro_usuario=$fila["usuario_creacion"];
    $obj->created_at="";
    $obj->updated_at="";
    $obj->estado="";
    $obj->deleted_at="";
    $obj->moneda=$fila["moneda_catalogo"];
    $obj->contrato=$fila["cod_sap"];

    $obj->items=[];

    $respuestaItem=obtenerItemsCatalogo($obj->id);
    if(!empty($respuestaItem)){
        while ($filaItem = $respuestaItem->fetch_assoc()) {
            array_push($obj->items,$filaItem);
        }
    }


    $respuestaImagen=obtenerInformacionImagen($fila["imagen_catalogo"]);
    if(!empty($respuestaImagen)){
        $ubicacionImagen=obtenerImagenBDgeneral($respuestaImagen["nombre_archivo"]);
        if(!empty($ubicacionImagen)){
            $obj->imagen=$ubicacionImagen["id"];
        }else{
            $obj->imagen="";
        }
    }else{
        $obj->imagen="";
    }
    $respuestaProveedor = obtenerInformacionProveedor($fila["cod_proveedor"]);
    if(!empty($respuestaProveedor)){
        $obj->idempresaProveedor=$respuestaProveedor["id_empresa"];
    }else{
        $obj->idempresaProveedor="";
    }
    //echo('<pre>');
    //var_dump($obj);
    registrarNuevoCatalogo($obj); //Guarda el catalogo en las nuevas BD
    //echo('</pre>');
}


function obtenerProductosBDAntigua(){
    global $conexion_1;
    global $empresa;
    $SqlQuery="SELECT * FROM `productos`;";
    $row = $conexion_1->query($SqlQuery);
    if($row->num_rows!=0){
        return $row;
    }else{
        return [];
    }
}

function obtenerItemsCatalogo($catalogo){
    global $conexion_1;
    global $empresa;
    $SqlQuery="SELECT * FROM catalogos_items INNER JOIN productos ON catalogos_items.id_producto = productos.id_producto ";
    $SqlQuery.="WHERE id_catalogo='".$catalogo."' ";
    $row = $conexion_1->query($SqlQuery);
    if($row->num_rows!=0){
        return $row;
    }else{
        return [];
    }
}

function obtenerListadoCatalogosBDAntigua(){
	global $conexion_1;
	global $empresa;
	$SqlQuery="SELECT * FROM `catalogos` WHERE id_empresa=".$empresa.";";
	$row = $conexion_1->query($SqlQuery);
	if($row->num_rows!=0){
		return $row;
	}else{
		return [];
	}
}

function obtenerInformacionImagen($codigo){
	global $conexion_1;
	global $empresa;
	$SqlQuery="SELECT * FROM `catalogos_imagenes` WHERE id_archivo=".$codigo.";";
	$row = $conexion_1->query($SqlQuery);
	if($row->num_rows!=0){
		return $row->fetch_assoc();
	}else{
		return [];
	}
}

function obtenerInformacionProveedor($nit){
    global $conexion_1;
    $SqlQuery = "SELECT * FROM `_0002103` WHERE nitempxx = '".$nit."';";
    $row = $conexion_1->query($SqlQuery);
    if($row->num_rows!=0){
        return $row->fetch_assoc();
    }else{
        return [];
    }
}



//--------------------------------------- Nuevas BD -----------------------------------------------

function obtenerImagenBDgeneral($nombre){
    global $conexion_2;
    $SqlQuery = "SELECT * FROM `imagen_general` WHERE nombre='".$nombre."';";
    $row = $conexion_2->query($SqlQuery);
    if($row->num_rows!=0){
        return $row->fetch_assoc();
    }else{
        return [];
    }
}

function asociarImagenesCatalogos($catalogo,$imagen){
    global $conexion_2;
    $SqlQuery="INSERT INTO catalogo_imagen (cod_catalogo,cod_imagen,registro_usuario) VALUES ";
    $SqlQuery.=" ('".$catalogo."','".$imagen."',4) ";
    $row = $conexion_2->query($SqlQuery);
}

function asociarImagenesProductos($producto,$imagen){
    global $conexion_2;
    $SqlQuery="INSERT INTO producto_imagenes (cod_producto,cod_imagen,registro_usuario) VALUES ";
    $SqlQuery.=" ('".$producto."','".$imagen."',4) ";
    $row = $conexion_2->query($SqlQuery);
}

function registrarNuevoCatalogo($obj){
	global $conexion_2;
	//Obtener codigo de la empresa
	//Guardar y asociar imagen
	$SqlQuery="INSERT INTO catalogos (cod_empresa,cod_proveedor,nombre,descripcion_larga, ";
	$SqlQuery.="registro_usuario,contrato,moneda) ";
	$SqlQuery.="VALUES ('".$obj->empresa."','".$obj->idempresaProveedor."','".$obj->nombre."','".$obj->descripcion_larga."',";
	$SqlQuery.="'".$obj->registro_usuario."','".$obj->contrato."','".$obj->moneda."');";
	$row = $conexion_2->query($SqlQuery);
	if($row){
        $cod_catalogo = $conexion_2->insert_id;
        if(isset($obj->imagen) && !empty($obj->imagen)){
            asociarImagenesCatalogos($cod_catalogo,$obj->imagen);
        }
        foreach ($obj->items as $key => $value) {
            registrarNuevoItem($value,$cod_catalogo);
        }
        echo('<pre>');
        var_dump("OK CATALOGO -".$obj->nombre);
        echo('</pre>');
	}else{
		echo 'Error - Registro insertado';
	}

}

function registrarNuevoItem($item,$cod_catalogo){
    global $conexion_2;






    $cod_producto="";
    $SqlQuery ="SELECT * FROM `productos` WHERE nombre ='".$item["titulo_producto"]."';";
    $CscQury = $conexion_2->query($SqlQuery);
    if($CscQury){
        $row = $CscQury->fetch_assoc();
        $cod_producto=$row["id"];
    }




    $item["disponible"]=strtoupper($item["disponible"]);
    $SqlQuery="INSERT INTO catalogo_items (valor,disponible,dias_entrega,cod_catalogo,cod_producto,cod_material,posicion)";
    $SqlQuery.=" VALUES ('".$item["valor_unitario_item"]."','".$item["disponible"]."','".$item["dias_entrega"]."','".$cod_catalogo."','".$cod_producto."','2','".$item["cod_posicion_sap"]."')";
    $CscQury = $conexion_2->query($SqlQuery);
    if($CscQury){
        echo('<pre>');
        var_dump("OK ITEM -".$item["titulo_producto"]);
        echo('</pre>');
    }else{
        echo('<pre>');
        var_dump($SqlQuery);
        echo('</pre>');
    }


}

function registrarNuevoProducto($obj){
    global $conexion_2;
    $SqlQuery="INSERT INTO productos (cod_empresa,cod_medida,nombre,origen_cliente,registro_usuario) ";
    $SqlQuery.="VALUES ('6','".$obj->unidad_medida."','".$obj->titulo_producto."',1,'".$obj->usuario_creacion."');";
    $row = $conexion_2->query($SqlQuery);
    if($row){
        if(isset($obj->arrImagenesNuevaBd) && !empty($obj->arrImagenesNuevaBd)){
            foreach ($obj->arrImagenesNuevaBd as $key => $value) {
                asociarImagenesProductos($conexion_2->insert_id,$value);
            }
        }
        echo('<pre>');
        var_dump("OK - PRDO - ".$obj->titulo_producto);
        echo('</pre>');
    }else{
        echo 'Error - Registro insertado';
    }
}




mysqli_close($conexion_1);
mysqli_close($conexion_2);
