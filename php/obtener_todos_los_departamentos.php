<?php
include "conexion.php"; // Reemplaza con la ruta correcta a tu archivo de conexión

$conexion = conectarOracle(); // Función de conexión a Oracle definida en tu archivo de conexión

if (!$conexion) {
    $error = oci_error();
    echo json_encode(array("error" => "Error de conexión: " . $error['message']));
    exit;
}

$departamentos_cursor = oci_new_cursor($conexion);

$prc = oci_parse($conexion, "BEGIN GESTION_LOCALIDAD_PKG.PRC_ObtenerTodosLosDepartamentos(:departamentos_cursor); END;");
oci_bind_by_name($prc, ":departamentos_cursor", $departamentos_cursor, -1,  OCI_B_CURSOR);

oci_execute($prc);

$departamentos = array();

oci_execute($departamentos_cursor);

while ($row = oci_fetch_assoc($departamentos_cursor)) {
    $departamentos[] = $row;
}

oci_free_statement($departamentos_cursor);
oci_free_statement($prc);
oci_close($conexion);

header('Content-Type: application/json');
echo json_encode($departamentos);
?>
