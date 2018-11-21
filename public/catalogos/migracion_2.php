<?php
$whitelist = array(
    '127.0.0.1',
    '::1',
);

if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {

    $conexion_1 = mysqli_connect("72.29.91.114", "pro7int", "B+ZNr%i47*", "pro7int_bd_ic_cliente"); // origin
    $conexion_2 = mysqli_connect("98.142.97.26", "cliente", "Int3lPas.2017-1", "cliente_bd_ic_cliente"); // destino
    //$conexion_local = mysqli_connect("localhost", "root","","BK_ALPHA_DB"); // destino
} else {
    $conexion_local = mysqli_connect("localhost", "root", "", "BK_ALPHA_DB"); // destino
    $conexion_1     = mysqli_connect("72.29.91.114", "pro7int", "B+ZNr%i47*", "pro7int_bd_ic_cliente"); // origin
    $conexion_2     = mysqli_connect("98.142.97.26", "cliente", "Int3lPas.2017-1", "cliente_bd_ic_cliente"); // destino
}

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

require_once "../adicionales/materiales.php";
require_once "../adicionales/catalogos.php";
require_once "../adicionales/imagenes.php";
require_once "../adicionales/apodos.php";
require_once "../adicionales/proyectos.php";
require_once "../adicionales/ordenes_compra.php";

$tiempo_inicial = microtime(true);

//--------------------------Obtiene los antiguos materiales y los actualiza --------------------------------------------
//$materiales = obtenerMaterialesAC();
$materiales = [];

foreach ($materiales as $key => $value) {
    //RECIBE UN NUMERO DE MATERIAL TIPO ENTERO
    $respuestaWsMaterial        = obtenerMaterialWs($value);
    $arregoDefinitivoMateriales = [];

    if ($respuestaWsMaterial["bandera"]) {
        foreach ($respuestaWsMaterial["respuesta"] as $key => $value) {
            $materialDefinitivo                = [];
            $materialDefinitivo["material"]    = (int) $value->Material;
            $materialDefinitivo["descripcion"] = $value->Descripcion;
            $materialDefinitivo["medida"]      = obtenerUnidadMedida($value->Unidad);
            $materialDefinitivo["familia"]     = obtenerFamiliaMaterial($value->Familia);
            array_push($arregoDefinitivoMateriales, $materialDefinitivo);
        }
    }

    if (!empty($arregoDefinitivoMateriales)) {
        actualizarTablaMateriales($arregoDefinitivoMateriales);
    } else {
        var_dump("No se encontraron materiales para ingresar - problema retorno Web Service.");
    }
}

//$respuestaWsMaterial = obtenerMaterialWs("");
$respuestaWsMaterial["bandera"] = false;
$arregoDefinitivoMateriales     = [];
if ($respuestaWsMaterial["bandera"]) {
    foreach ($respuestaWsMaterial["respuesta"] as $key => $value) {
        $materialDefinitivo                = [];
        $materialDefinitivo["material"]    = (int) $value->Material;
        $materialDefinitivo["descripcion"] = $value->Descripcion;
        $materialDefinitivo["medida"]      = obtenerUnidadMedida($value->Unidad);
        $materialDefinitivo["familia"]     = obtenerFamiliaMaterial($value->Familia);
        var_dump("<pre>", "Se formo material :" . $materialDefinitivo["material"], "</pre>");
        array_push($arregoDefinitivoMateriales, $materialDefinitivo);
    }
    var_dump("<pre>", "Se recuperaron " . count($arregoDefinitivoMateriales) . " materiales.", "</pre>");
    if (!empty($arregoDefinitivoMateriales)) {
        actualizarTablaMateriales($arregoDefinitivoMateriales);
    } else {
        var_dump("No se encontraron materiales para ingresar - problema retorno Web Service.");
    }
}

//--------------------------Obtiene las imagenes completas y luego las asocia al material --------------------------------------------
//$arrImagenes=obtenerImagenesAC();
$arrImagenes = [];
foreach ($arrImagenes as $key => $value) {
    var_dump("<pre>", $value, "</pre>");
}

//--------------------------Obtiene los catalogos e items y los actualiza --------------------------------------------
//$arrcatalogos = obtenerCatalogosAC(6);
$arrcatalogos = [];

/*foreach ($arrcatalogos as $key => $value) {
var_dump("<pre>", $value, "</pre>");
}*/
if (!empty($arrcatalogos) && count($arrcatalogos) != 0) {
    foreach ($arrcatalogos as $key => $value) {
        actualizarCatalogosN($value);
    }
}

// ---------------- Obtener los apodos de los items - materiales ---------------------------------------

$arrApodos = [];
//$arrApodos = obtenerApodosMateriales();
foreach ($arrApodos as $key => $value) {

    $idMaterial = obtenerMaterialNumero($value["material"]);
    if (!empty($idMaterial)) {
        //var_dump("<pre>", $value["material"], $value["apodo_item"], $idMaterial, "</pre>");
        if (!empty($value["apodo_item"])) {
            guardarApodoMateriales($idMaterial, $value["apodo_item"]);
        }
    }
}

// ---------------------------------------------- PROYECTOS -------------------------------------
$arrProyectos = [];
//$arrProyectos = obtenerProyectosParaMigrar();

if (isset($arrProyectos) && !empty($arrProyectos) && count($arrProyectos) != 0) {
    $arrNuevosProyectos = [];
    foreach ($arrProyectos as $key => $value) {
        $obj                                 = [];
        $obj["titulo"]                       = strtoupper($value["nombre_regla"]);
        $obj["cod_grupo_compras"]            = obtenerNuevoGrupoCompras($value["grupo_compras"]);
        $obj["cod_maestra_compras"]          = 7;
        $obj["cod_region"]                   = obtenerNuevoRegion($value["region"]);
        $obj["cod_maestra_region"]           = 6;
        $obj["cod_centro_logistico"]         = obtenerNuevoCentroLogistico($value["numero_centro_logistico"]);
        $obj["cod_maestra_centro_logistico"] = 1;
        $obj["cod_indicador"]                = obtenerNuevoIndicador($value["codigo_indicador"]);
        $obj["cod_maestra_indicador"]        = 4;
        $obj["cod_almacen"]                  = obtenerNuevoAlmacen($value["numero_almacen"]);
        $obj["cod_maestra_almacen"]          = 2;
        $obj["cod_imputacion"]               = obtenerNuevoImputacion($value["numero_imputacion"]);
        $obj["cod_maestra_imputacion"]       = 10;
        $obj["cod_pep"]                      = obtenerNuevoElementoPep($value["elemento_pep"]);
        $obj["cod_maestra_pep"]              = 8;
        array_push($arrNuevosProyectos, $obj);
    }
    if (!empty($arrNuevosProyectos) && count($arrNuevosProyectos) != 0) {
        actualizarNuevosProyectos($arrNuevosProyectos);
    }
}

// ------------------- ordenes de compra --------------------------------------
//
$arrCompras = [];
//$arrCompras = obtenerOrdenesCompra();

if (!empty($arrCompras) && count($arrCompras) != 0) {
    foreach ($arrCompras as $key => $value) {
        //var_dump("<pre>",$value,"</pre>");
        actualizarOrdenesCompra($value);
    }
}

// ---------- ASOCIAR IMAGENES ANTIGUAS A CATALOGOS -----------------------------
$arrImagenesAC = [];
//$arrImagenesAC = obtenerImagenesCatalogosAsociadasAnterior();

foreach ($arrImagenesAC as $key => $value) {
    if (validarImagenNuevoCatalogo($value)) {
        $respuestaCatalogo = validarCatalogoContrato($value["contrato"]);
        if (!empty($respuestaCatalogo)) {
            agregarImagenCatalogoN($value["cod_imagen"], $respuestaCatalogo);
        }
    }
}

$arrImagenesAC = [];
//$arrImagenesAC = obtenerImagenesMateriales();
foreach ($arrImagenesAC as $key => $value) {
    //var_dump("<pre>",$value,"</pre>");
    $cod_material = obtenerMaterialNumero($value["numero"]);
    if (!empty($cod_material)) {
        if (validarImagenMaterial($cod_material, $value["cod_imagen"])) {
            if (!empty($value["cod_imagen"])) {
                agregarImagenMaterialN($cod_material, $value["cod_imagen"]);
            }
        }
    }
}

//--------------- obtener usuarios ----------------------------
$arrUusarios = [];
$arrUusarios = obtenerUsuariosEmpresa();
foreach ($arrUusarios as $key => $value) {
    var_dump("<pre>", $value, "</pre>");
}

$tiempo_final = microtime(true);
$tiempo       = $tiempo_final - $tiempo_inicial;
echo "El tiempo de ejecución del archivo ha sido de " . $tiempo . " segundos";
