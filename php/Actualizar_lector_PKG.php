<?php
include "conexion.php";

$conexion = conectarOracle();

// Obtener datos del formulario
$cedula = $_POST['cedula'];
$primerNombre = $_POST['primerNombre'];
$segundoNombre = $_POST['segundoNombre'];
$primerApellido = $_POST['primerApellido'];
$segundoApellido = $_POST['segundoApellido'];
$fechaNacimiento = $_POST['fechaNacimiento'];
$departamento = $_POST['departamento'];
$ciudad = $_POST['ciudad'];
$calle = $_POST['calle'];
$numeroCasa = $_POST['numeroCasa'];
$barrio = $_POST['barrio'];
$numeroTelefono = $_POST['numeroTelefono'];
$correo = $_POST['correo'];

// Llamar al procedimiento almacenado
$stmt = oci_parse($conexion, "BEGIN GESTION_LECTORES_PKG.PRC_ActualizarLector(:cedula, :primerNombre, :segundoNombre, :primerApellido, :segundoApellido, TO_DATE(:fechaNacimiento, 'YYYY-MM-DD'), :departamento, :ciudad, :calle, :numeroCasa, :barrio, :numeroTelefono, :correo, :mensaje); END;");

oci_bind_by_name($stmt, ":cedula", $cedula);
oci_bind_by_name($stmt, ":primerNombre", $primerNombre);
oci_bind_by_name($stmt, ":segundoNombre", $segundoNombre);
oci_bind_by_name($stmt, ":primerApellido", $primerApellido);
oci_bind_by_name($stmt, ":segundoApellido", $segundoApellido);
oci_bind_by_name($stmt, ":fechaNacimiento", $fechaNacimiento);
oci_bind_by_name($stmt, ":departamento", $departamento);
oci_bind_by_name($stmt, ":ciudad", $ciudad);
oci_bind_by_name($stmt, ":calle", $calle);
oci_bind_by_name($stmt, ":numeroCasa", $numeroCasa);
oci_bind_by_name($stmt, ":barrio", $barrio);
oci_bind_by_name($stmt, ":numeroTelefono", $numeroTelefono);
oci_bind_by_name($stmt, ":correo", $correo);
oci_bind_by_name($stmt, ":mensaje", $mensaje, 4000);

// Desactivar temporalmente las advertencias
$oldErrorReporting = error_reporting();
error_reporting(0);

// Ejecutar el procedimiento almacenado
$executionResult = oci_execute($stmt);

// Restaurar la configuración original de advertencias
error_reporting($oldErrorReporting);

// Manejar el resultado
if ($executionResult) {
  echo $mensaje;
} 

// Cerrar la conexión
oci_close($conexion);
?>