<?php
include "conexion.php";

if (isset($_POST['busqueda'])) {
    $searchTerm = $_POST['busqueda'];

    $conexion = conectarOracle(); // Suponiendo que la función conectarOracle() está definida en tu archivo conexion.php

    if ($conexion) {
        // Preparar la llamada a la función FN_ListarLectores del paquete GESTION_LECTORES_PKG
        $query = "BEGIN
                    :result := GESTION_LECTORES_PKG.FN_ListarLectores(:pPrimerNombre, :pSegundoNombre, :pPrimerApellido, :pSegundoApellido, :pCedula);
                  END;";

        // Preparar el statement
        $stmt = oci_parse($conexion, $query);

        // Bind de los parámetros y el resultado
        oci_bind_by_name($stmt, ":result", $result, -1, OCI_B_CURSOR);
        oci_bind_by_name($stmt, ":pPrimerNombre", $searchTerm);
        oci_bind_by_name($stmt, ":pSegundoNombre", $searchTerm);
        oci_bind_by_name($stmt, ":pPrimerApellido", $searchTerm);
        oci_bind_by_name($stmt, ":pSegundoApellido", $searchTerm);
        oci_bind_by_name($stmt, ":pCedula", $searchTerm);

        // Ejecutar el statement
        oci_execute($stmt);

        // Obtener los resultados utilizando el cursor
        oci_execute($result);

        // Inicializar el array de resultados
        $resultados = array();

        // Obtener los resultados en un array asociativo
        while ($row = oci_fetch_assoc($result)) {
            $resultados[] = $row;
        }

        // Liberar los recursos
        oci_free_statement($stmt);
        oci_free_statement($result);
        oci_close($conexion);

        // Devolver los resultados como JSON
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit(); // Termina la ejecución del script después de enviar los resultados
    } else {
        // Manejo de error si la conexión no se establece correctamente
        echo "Error: No se pudo conectar a la base de datos.";
    }
}
?>


