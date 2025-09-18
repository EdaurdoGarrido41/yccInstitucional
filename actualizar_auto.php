<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $host = "localhost";
    $user = "u676669933_catalogo";
    $pass = "Mastersales2025";
    $db   = "u676669933_mastersales";

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) die("Error conexión: " . $conn->connect_error);

    $id             = $_POST['id'];
    $vin            = $_POST['vin'];
    $stock          = $_POST['stock'];
    $titulo         = $_POST['titulo'] ?? '';
    $exterior       = $_POST['exterior'] ?? '';
    $interior       = $_POST['interior'] ?? '';
    $drivetrain     = $_POST['drivetrain'] ?? '';
    $transsmision   = $_POST['transsmision'] ?? '';
    $engine         = $_POST['engine'] ?? '';
    $fuel_efficiency= $_POST['fuel_efficiency'] ?? '';
    $mileage        = $_POST['mileage'] ?? '';
    $make           = $_POST['make'] ?? '';
    $model          = $_POST['model'] ?? '';
    $year           = $_POST['year'] ?? '';

    if (empty($vin) || empty($stock)) die("Error: VIN y Stock son obligatorios.");

    // Imagen principal
    $nombreImagen = $_POST['imagen_actual'] ?? ''; // si quieres conservarla
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombreImagen = $vin . "." . $ext;
        $rutaDestino = "mastersales-inventario/" . $nombreImagen;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
    }

    $sql = "UPDATE catalogo_motors SET 
        titulo=?, vin=?, stock=?, exterior=?, interior=?, drivetrain=?, transsmision=?, 
        engine=?, fuel_efficiency=?, mileage=?, make=?, model=?, year=?, imagen=?
        WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssssi",
        $titulo, $vin, $stock, $exterior, $interior, $drivetrain,
        $transsmision, $engine, $fuel_efficiency, $mileage,
        $make, $model, $year, $nombreImagen, $id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Vehículo actualizado con éxito'); window.location='panel.html';</script>";
    } else {
        die("Error al actualizar: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
?>
