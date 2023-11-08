<?php
// Incluye el archivo de conexión a Oracle
include 'conexion.php'; // Reemplaza 'conexion_oracle.php' con la ruta correcta a tu archivo de conexión

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prestamo_id = $_POST["prestamo_id"];
    $mensaje = devolverLibro($prestamo_id);
    
    echo $mensaje;
}

function devolverLibro($prestamo_id) {
    $conexion = conectarOracle(); // Llama a tu función de conexión

    // Llama a la función FN_DEVOLVER_LIBRO del paquete MIBIBLIOTECA y utiliza la fecha actual del sistema
    $sql = "BEGIN :mensaje := MIBIBLIOTECA.FN_DEVOLVER_LIBRO(:prestamo_id, SYSDATE); END;";
    
    $stmt = oci_parse($conexion, $sql);
    oci_bind_by_name($stmt, ":prestamo_id", $prestamo_id, 50);
    oci_bind_by_name($stmt, ":mensaje", $mensaje, 200);

    oci_execute($stmt);

    oci_close($conexion); // Cierra la conexión a la base de datos

    return $mensaje;
}
?>
