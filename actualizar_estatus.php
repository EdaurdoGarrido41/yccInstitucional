<?php
    // Conexión a la base de datos
    $host = "localhost"; 
    $user = "u676669933_catalogo"; 
    $pass = "Mastersales2025"; 
    $db   = "u676669933_mastersales"; 

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    if (isset($_POST['vin']) && isset($_POST['estatus'])) {
        $vin = $conn->real_escape_string($_POST['vin']);
        $estatus = (int)$_POST['estatus'];

        $sql = "UPDATE catalogo_motors SET activado = $estatus WHERE vin = '$vin'";
        if ($conn->query($sql) === TRUE) {
            echo "Estatus actualizado correctamente.";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Parámetros incompletos.";
    }

    $conn->close();
?>