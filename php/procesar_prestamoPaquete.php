<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $copia_id = $_POST['copia_id'];
    $cedula = $_POST['cedula'];

    $conexion = conectarOracle();

    // Realizar el préstamo utilizando el procedimiento
    $prestamo_id = uniqid(); // Generar un ID de préstamo único
    $fecha_prestamo = date('Y-m-d'); // Fecha actual formateada como YYYY-MM-DD
    $mensaje = '';
    $no_prestamo = 'No se puede realizar el préstamo. ';

    try {
        // Llamar al procedimiento PRC_REALIZAR_PRESTAMO
        $stmt = oci_parse($conexion, "BEGIN MIBIBLIOTECA.PRC_REALIZAR_PRESTAMO(:prestamo_id, TO_DATE(:fecha_prestamo, 'YYYY-MM-DD'), :cedula, :copia_id, :mensaje); END;");
        oci_bind_by_name($stmt, ':prestamo_id', $prestamo_id, 32); // Suponiendo que PrestamoID es un VARCHAR2(32)
        oci_bind_by_name($stmt, ':fecha_prestamo', $fecha_prestamo);
        oci_bind_by_name($stmt, ':cedula', $cedula);
        oci_bind_by_name($stmt, ':copia_id', $copia_id);
        oci_bind_by_name($stmt, ':mensaje', $mensaje, 200);

        oci_execute($stmt);

        // Cerrar el cursor
        oci_free_statement($stmt);

        // Comprobar si se completó el préstamo
        if ($mensaje === 'EXITOSO') {
            // Obtener información de la factura utilizando la función FN_OBTENER_FACTURA
            $stmt = oci_parse($conexion, "BEGIN :result := MIBIBLIOTECA.FN_OBTENER_FACTURA(:prestamo_id); END;");
            oci_bind_by_name($stmt, ':result', $factura_json, 2000);
            oci_bind_by_name($stmt, ':prestamo_id', $prestamo_id);
            oci_execute($stmt);

            // Cerrar el procedimiento
            oci_free_statement($stmt);

            // Mostrar la factura en JSON
            echo $factura_json;
        } else {
            // Manejar otros casos de error devolviendo un JSON
            $error = ['error' => $mensaje];
            echo json_encode($error);
        }
    } catch (Exception $e) {
        // Proporcionar información detallada sobre la excepción en la respuesta JSON
        echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Método de solicitud no válido.']);
}
?>




