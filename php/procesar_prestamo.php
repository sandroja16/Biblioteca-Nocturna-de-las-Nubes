<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $copia_id = $_POST['copia_id'];
    $cedula = $_POST['cedula'];

    $conexion = conectarOracle();

    // Verificar si el usuario (identificado por la cédula) existe
    $usuario_existe = false;
    $stmt = oci_parse($conexion, "SELECT * FROM Lectores WHERE Cedula = :cedula");
    oci_bind_by_name($stmt, ':cedula', $cedula);
    oci_execute($stmt);

    if ($usuario = oci_fetch_assoc($stmt)) {
        $usuario_existe = true;
        $primer_nombre = $usuario['PRIMERNOMBRE'];
        $primer_apellido = $usuario['PRIMERAPELLIDO'];
    }

    if ($usuario_existe) {
        // Verificar si el usuario tiene menos de tres préstamos activos
        $prestamos_activos = 0;
        $stmt = oci_parse($conexion, "SELECT COUNT(*) AS prestamos_activos FROM Prestamos WHERE LECTORID = :cedula AND EstadoPrestamo = 'Pendiente'");
        oci_bind_by_name($stmt, ':cedula', $cedula);
        oci_execute($stmt); 

        if ($row = oci_fetch_assoc($stmt)) {
            $prestamos_activos = $row['PRESTAMOS_ACTIVOS'];
        }

        if ($prestamos_activos <= 3) {
            // Comprobar si la copia está disponible
            $disponible = 0;
            $stmt = oci_parse($conexion, "SELECT CASE WHEN Estado = 'Disponible' THEN 1 ELSE 0 END AS disponible FROM Copias WHERE CopiaID = :copia_id");
            oci_bind_by_name($stmt, ':copia_id', $copia_id);
            oci_execute($stmt);

            if ($row = oci_fetch_assoc($stmt)) {
                $disponible = $row['DISPONIBLE'];
            }

            if ($disponible == 1) {
                // Realizar el préstamo
                $prestamo_id = uniqid(); // Puedes generar un ID de préstamo único
                $fecha_prestamo = date('Y-m-d'); // Fecha actual
              //  $fecha_entrega = date('Y-m-d', strtotime($fecha_prestamo . ' +8 weekdays')); // Fecha de entrega excluye fines de semana
                $fecha_entrega = date('Y-m-d', strtotime($fecha_prestamo . ' +8 days'));// Fecha de entrega 
                // Consulta para obtener el título del libro y el nombre del autor
                $stmt = oci_parse($conexion, "SELECT l.TITULO, a.PRIMERNOMBRE, a.PRIMERAPELLIDO FROM LIBROS l INNER JOIN AUTORES a ON l.AUTORID = a.AUTORID WHERE l.ISBN IN (SELECT LIBROISBN FROM COPIAS WHERE COPIAID = :copia_id)");
                oci_bind_by_name($stmt, ':copia_id', $copia_id);
                oci_execute($stmt);
                $row = oci_fetch_assoc($stmt);
                $titulo_libro = $row['TITULO'];
                $nombre_autor = $row['PRIMERNOMBRE'] . ' ' . $row['PRIMERAPELLIDO'];

                // Actualizar el estado de la copia
                $stmt = oci_parse($conexion, "UPDATE Copias SET Estado = 'Prestado' WHERE CopiaID = :copia_id");
                oci_bind_by_name($stmt, ':copia_id', $copia_id);
                oci_execute($stmt);

                // Insertar registro de préstamo
                $stmt = oci_parse($conexion, "INSERT INTO Prestamos (PrestamoID, FechaPrestamo, LECTORID, CopiaID, EstadoPrestamo) VALUES (:prestamo_id, TO_DATE(:fecha_prestamo, 'YYYY-MM-DD'), :cedula, :copia_id, 'Pendiente')");
                oci_bind_by_name($stmt, ':prestamo_id', $prestamo_id);
                oci_bind_by_name($stmt, ':fecha_prestamo', $fecha_prestamo);

                oci_bind_by_name($stmt, ':cedula', $cedula);
                oci_bind_by_name($stmt, ':copia_id', $copia_id);
                oci_execute($stmt);

                // Generar factura en PDF
                $factura = ' <div id="factura">
                <div class="encabezado">Biblioteca Nocturna de las Nubes</div>
                <div class="detalle">Referencia de préstamo:' . $prestamo_id . '</div>
                <div class="detalle">Fecha del préstamo: ' . $fecha_prestamo . '</div>
                <div class="detalle">Fecha de entrega: ' . $fecha_entrega . '</div>
                <div class="detalle nombre-libro">Libro prestado: ' . $titulo_libro . '</div>
                <div class="detalle">Autor: ' . $nombre_autor . '</div>
                <div class="detalle">Lector: ' . $primer_nombre . ' ' . $primer_apellido . '</div>
                <div class="detalle">Cedula: ' . $cedula . '</div>
                <div class="pie-pagina">Gracias por su préstamo</div>
                <br>
                <button id="boton-imprimir"  onclick="imprimirRecibo()" >Imprimir factura</button>
                <br>
                </div>';
                echo $factura;

            } else {
                $mensaje = 'No se puede realizar el préstamo. La copia del libro no está disponible.';
                echo $mensaje;
            }
        } else {
            $mensaje = 'No se puede realizar el préstamo. El usuario ya tiene tres préstamos activos.';
            echo $mensaje;
        }
    } else {
        $mensaje = 'No se puede realizar el préstamo. El usuario no existe.';
        echo $mensaje;
    }
} else {
    echo 'Método de solicitud no válido.';
}

?>