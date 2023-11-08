<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $copia_id = $_POST['copia_id'];
    $cedula = $_POST['cedula'];

    $conexion = conectarOracle();

    // Realizar el préstamo utilizando el procedimiento
    $prestamo_id = uniqid(); // Generar un ID de préstamo único
    $fecha_prestamo = date('Y-m-d'); // Fecha actual
    $fecha_devolucion = date('Y-m-d', strtotime($fecha_prestamo . ' +8 days')); // Fecha de entrega
    $resultado_prestamo = '';
    $mensaje = '';

    // Llamar al procedimiento PRC_REALIZAR_PRESTAMO
    $stmt = oci_parse($conexion, "BEGIN MIBIBLIOTECA.PRC_REALIZAR_PRESTAMO(:prestamo_id, TO_DATE(:fecha_prestamo, 'YYYY-MM-DD'), :cedula, :copia_id); END;");
    oci_bind_by_name($stmt, ':prestamo_id', $prestamo_id);
    oci_bind_by_name($stmt, ':fecha_prestamo', $fecha_prestamo);
     oci_bind_by_name($stmt, ':cedula', $cedula);
    oci_bind_by_name($stmt, ':copia_id', $copia_id);

    oci_execute($stmt);
    
    // Obtener el resultado del procedimiento
    oci_free_statement($stmt);

    // Comprobar si se completó el préstamo
    if ($resultado_prestamo == 'EXITOSO') {
        // Obtener información de la factura utilizando la función FN_OBTENER_FACTURA
        $stmt = oci_parse($conexion, "BEGIN :result := MIBIBLIOTECA.FN_OBTENER_FACTURA(:prestamo_id); END;");
        oci_bind_by_name($stmt, ':result', $cursor, -1, OCI_B_CURSOR);
        oci_bind_by_name($stmt, ':prestamo_id', $prestamo_id);

        oci_execute($stmt);
        
        // Obtener los datos de la factura
        oci_fetch_all($cursor, $factura, null, null, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC);
        
        // Cerrar el cursor y el procedimiento
        oci_free_statement($stmt);
        
        // Mostrar la factura en HTML
        echo '<div id="factura">';
        echo '<div class="encabezado">Biblioteca Nocturna de las Nubes</div>';
        echo '<div class="detalle">Referencia de préstamo: ' . $factura[0]['REFERENCIA_PRESTAMO'] . '</div>';
        echo '<div class="detalle">Fecha del préstamo: ' . $factura[0]['FECHA_PRESTAMO'] . '</div>';
        echo '<div class="detalle">Fecha de entrega: ' . $factura[0]['FECHA_ENTREGA'] . '</div>';
        echo '<div class="detalle nombre-libro">Libro prestado: ' . $factura[0]['LIBRO_PRESTADO'] . '</div>';
        echo '<div class="detalle">Autor: ' . $factura[0]['AUTOR_LIBRO'] . '</div>';
        echo '<div class="detalle">Lector: ' . $factura[0]['NOMBRE_LECTOR'] . '</div>';
        echo '<div class="detalle">Cedula: ' . $factura[0]['CEDULA_LECTOR'] . '</div>';
        echo '<div class="pie-pagina">Gracias por su préstamo</div>';
        echo '<br>';
        echo '<button id="boton-imprimir"  onclick="imprimirRecibo()" >Imprimir factura</button>';
        echo '<br>';
        echo '</div>';
    } else {
        // Mostrar el mensaje de error
        echo 'No se puede realizar el préstamo: ' . $mensaje;
    }
} else {
    echo 'Método de solicitud no válido.';
}

?>
