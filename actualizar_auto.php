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
    $nombreImagen = null;
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombreImagen = $vin . "." . $ext;
        $rutaDestino = "mastersales-inventario/" . $nombreImagen;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
    }

    // Actualizar vehículo
    $sql = "UPDATE catalogo_motors SET 
        titulo=?, vin=?, stock=?, exterior=?, interior=?, drivetrain=?, transsmision=?, 
        engine=?, fuel_efficiency=?, mileage=?, make=?, model=?, year=?".($nombreImagen ? ", imagen=?" : "")."
        WHERE id=?";

    $stmt = $conn->prepare($sql);

    if ($nombreImagen) {
        $stmt->bind_param(
            "ssssssssssssssi",
            $titulo, $vin, $stock, $exterior, $interior, $drivetrain,
            $transsmision, $engine, $fuel_efficiency, $mileage,
            $make, $model, $year, $nombreImagen, $id
        );
    } else {
        $stmt->bind_param(
            "sssssssssssssi",
            $titulo, $vin, $stock, $exterior, $interior, $drivetrain,
            $transsmision, $engine, $fuel_efficiency, $mileage,
            $make, $model, $year, $id
        );
    }

    if (!$stmt->execute()) die("Error al actualizar: " . $stmt->error);
    $stmt->close();

    // Manejar imágenes adicionales
    if (!empty($_FILES['imagenes']['tmp_name'][0])) {
        $contador = 1;
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            if (!empty($tmp_name)) {
                $ext = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION);
                $nombreImg = $vin . "_extra_" . time() . "_$contador." . $ext;
                $rutaDestino = "mastersales-inventario/" . $nombreImg;
                if (move_uploaded_file($tmp_name, $rutaDestino)) {
                    $stmtImg = $conn->prepare("INSERT INTO catalogo_motors_imagenes (id_vehiculo, nombre_imagen) VALUES (?, ?)");
                    $stmtImg->bind_param("is", $id, $nombreImg);
                    $stmtImg->execute();
                    $stmtImg->close();
                }
                $contador++;
            }
        }
    }

    $conn->close();
    echo "<script>alert('Vehículo actualizado con éxito'); window.location='panel.html';</script>";
?>
