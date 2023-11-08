<?php
function conectarOracle() {
    $tns = "(DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = sandro)(PORT = 1521))
        (CONNECT_DATA =
            (SERVER = DEDICATED)
            (SERVICE_NAME = xepdb1)
        )
    )";

    $usuario = "db_administrador"; // Tu nombre de usuario
    $contrasena = "77175742"; // Tu contraseña

    $conexion = oci_connect($usuario, $contrasena, $tns);

    if (!$conexion) {
        $e = oci_error();
        die("Error en la conexión: " . $e['message']);
    }

    return $conexion;
}
?>

