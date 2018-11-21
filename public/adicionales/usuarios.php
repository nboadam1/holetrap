<?php
function obtenerUsuariosEmpresa()
{
    global $empresa;
    global $conexion_1;
    $SqlQuery = "
		SELECT * FROM usuarios WHERE empresa_id='" . $empresa . "'
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
