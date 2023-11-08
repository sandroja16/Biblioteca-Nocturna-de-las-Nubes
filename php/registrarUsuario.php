<?php
include "conexion.php"; // Incluye el archivo de conexión

// Obtener datos del formulario
$cedula = $_POST['cedula'];
$primerNombre = $_POST['primerNombre'];
$segundoNombre = $_POST['segundoNombre'];
$primerApellido = $_POST['primerApellido'];
$segundoApellido = $_POST['segundoApellido'];
$fechaNacimiento = $_POST['fechaNacimiento'];
$departamentoID = $_POST['departamento'];
$ciudadID = $_POST['ciudad'];
$calle = $_POST['calle'];
$numeroCasa = $_POST['numeroCasa'];
$barrio = $_POST['barrio'];
$numeroTelefono = $_POST['numeroTelefono'];
$correo = $_POST['correo'];

$conexion = conectarOracle(); // Obtiene la conexión desde el archivo de conexión

try {
    $sql = "INSERT INTO lectores (Cedula, PrimerNombre, SegundoNombre, PrimerApellido, SegundoApellido, FechaNacimiento, DepartamentoID, CiudadID, Calle, NumeroCasa, Barrio, NumeroTelefono, Correo)
            VALUES (:cedula, :primerNombre, :segundoNombre, :primerApellido, :segundoApellido, TO_DATE(:fechaNacimiento, 'YYYY-MM-DD'), :departamentoID, :ciudadID, :calle, :numeroCasa, :barrio, :numeroTelefono, :correo)";
    
    $stmt = oci_parse($conexion, $sql);
    oci_bind_by_name($stmt, ':cedula', $cedula);
    oci_bind_by_name($stmt, ':primerNombre', $primerNombre);
    oci_bind_by_name($stmt, ':segundoNombre', $segundoNombre);
    oci_bind_by_name($stmt, ':primerApellido', $primerApellido);
    oci_bind_by_name($stmt, ':segundoApellido', $segundoApellido);
    oci_bind_by_name($stmt, ':fechaNacimiento', $fechaNacimiento);
    oci_bind_by_name($stmt, ':departamentoID', $departamentoID);
    oci_bind_by_name($stmt, ':ciudadID', $ciudadID);
    oci_bind_by_name($stmt, ':calle', $calle);
    oci_bind_by_name($stmt, ':numeroCasa', $numeroCasa);
    oci_bind_by_name($stmt, ':barrio', $barrio);
    oci_bind_by_name($stmt, ':numeroTelefono', $numeroTelefono);
    oci_bind_by_name($stmt, ':correo', $correo);

    if (oci_execute($stmt)) {
        echo "Registro exitoso";
    } else {
        $e = oci_error($stmt);
        echo "Error en el registro: " . $e['message'];
    }
} catch (Exception $e) {
    echo "Error en la ejecución de la consulta: " . $e->getMessage();
}


?>


