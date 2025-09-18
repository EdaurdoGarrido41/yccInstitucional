<?php
    $host = "localhost";
    $user = "u676669933_catalogo";
    $pass = "Mastersales2025";
    $db   = "u676669933_mastersales";

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) die("Error conexión: " . $conn->connect_error);

    $idImg = $_GET['id'] ?? 0;
    $idVehiculo = $_GET['vehiculo'] ?? 0;

    // Obtener nombre de la imagen
    $sql = "SELECT nombre_imagen FROM catalogo_motors_imagenes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idImg);
    $stmt->execute();
    $result = $stmt->get_result();
    $img = $result->fetch_assoc();
    $stmt->close();

    if ($img) {
        $ruta = "mastersales-inventario/".$img['nombre_imagen'];
        if (file_exists($ruta)) unlink($ruta);

        $stmtDel = $conn->prepare("DELETE FROM catalogo_motors_imagenes WHERE id = ?");
        $stmtDel->bind_param("i", $idImg);
        $stmtDel->execute();
        $stmtDel->close();
    }

    $conn->close();
    header("Location: editar_auto.php?id=".$idVehiculo);
    exit;
?>
