<?php
include "conexion.php"; // Asegúrate de incluir tu archivo de conexión

// Obtener la cédula desde la solicitud GET o POST
$cedulaBuscar = $_GET['cedula']; // O $_POST['cedula'] si es una solicitud POST

// Establecer la conexión con Oracle
$conexion = conectarOracle();

// Preparar la llamada a la función para obtener detalles del lector
$stmt = oci_parse($conexion, "BEGIN :result := GESTION_LECTORES_PKG.FN_OBTENER_LECTOR(:cedula); END;");
oci_bind_by_name($stmt, ":result", $result, 4000); 
oci_bind_by_name($stmt, ":cedula", $cedulaBuscar, 20); 
oci_execute($stmt);
oci_free_statement($stmt);
// Devolver la respuesta JSON al cliente
echo $result;

// Liberar recursos y cerrar la conexión

oci_close($conexion);
?>



