<?php
include "conexion.php";

if (isset($_POST['busqueda'])) {
    $searchTerm = $_POST['busqueda'];

    $conexion = conectarOracle(); // Suponiendo que la función conectarOracle() está definida en tu archivo conexion.php

    if ($conexion) {
        // Preparar la llamada a la función FN_ListarLectores del paquete GESTION_LECTORES_PKG
        $query = "BEGIN
                    :result := GESTION_AUTORES_PKG.FN_ListarAutores(:pBusqueda);
                  END;";

        // Preparar el statement
        $stmt = oci_parse($conexion, $query);

        // Variables para el resultado y los parámetros
        $result = oci_new_descriptor($conexion, OCI_D_LOB); // Crear un nuevo descriptor para el resultado

        // Bind de los parámetros y el resultado
        oci_bind_by_name($stmt, ":result", $result, -1, OCI_B_CLOB); // El tipo OCI_B_CLOB indica que es un CLOB
        oci_bind_by_name($stmt, ":pBusqueda", $searchTerm);

        // Ejecutar el statement
        oci_execute($stmt);

        // Leer el resultado del CLOB
        $clobData = $result->load(); // Cargar el CLOB en una variable

        // Liberar los recursos
        oci_free_statement($stmt);
        $result->free(); // Liberar el descriptor del resultado
        oci_close($conexion);

        // Devolver los datos como respuesta JSON
        header('Content-Type: application/json');
        echo $clobData;
        exit();
    } else {
        // Manejo de error si la conexión no se establece correctamente
        echo "Error: No se pudo conectar a la base de datos.";
    }
}
?>
