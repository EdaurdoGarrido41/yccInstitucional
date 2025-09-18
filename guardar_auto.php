<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Conexión a la base de datos
    $host = "localhost";
    $user = "u676669933_catalogo";
    $pass = "Mastersales2025";
    $db   = "u676669933_mastersales";

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

    // Obtener y validar datos del formulario
    $vin            = trim($_POST['vin'] ?? '');
    $stock          = trim($_POST['stock'] ?? '');
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

    // Validar campos obligatorios
    if ($vin === '' || $stock === '') die("Error: VIN y Stock son obligatorios.");

    // Manejo de imagen principal
    $nombreImagen = "";
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        if ($ext) {
            $nombreImagen = $vin . "." . $ext;
            $rutaDestino = "mastersales-inventario/" . $nombreImagen;
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                die("Error al subir la imagen principal.");
            }
        }
    }

    // Insertar vehículo en catalogo_motors
    $sql = "INSERT INTO catalogo_motors 
    (id, titulo, vin, stock, exterior, interior, drivetrain, transsmision, engine, fuel_efficiency, mileage, make, model, year, imagen, activado, date_registration)
    VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Error en SQL: " . $conn->error);

    // bind_param seguro con 14 parámetros
    $stmt->bind_param(
        "ssssssssssssss",
        $titulo,
        $vin,
        $stock,
        $exterior,
        $interior,
        $drivetrain,
        $transsmision,
        $engine,
        $fuel_efficiency,
        $mileage,
        $make,
        $model,
        $year,
        $nombreImagen
    );

    if (!$stmt->execute()) die("Error al guardar vehículo: " . $stmt->error);
    $idVehiculo = $stmt->insert_id;
    $stmt->close();

    // Subir imágenes múltiples si existen
    if (!empty($_FILES['imagenes']['tmp_name'][0])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            $ext = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION);
            if (!$ext) continue; // saltar si no tiene extensión
            $nombreImg = $vin . "_" . ($key+1) . "." . $ext;
            $rutaDestino = "mastersales-inventario/" . $nombreImg;
            if (move_uploaded_file($tmp_name, $rutaDestino)) {
                $stmtImg = $conn->prepare("INSERT INTO catalogo_motors_imagenes (id_vehiculo, nombre_imagen) VALUES (?, ?)");
                if ($stmtImg) {
                    $stmtImg->bind_param("is", $idVehiculo, $nombreImg);
                    $stmtImg->execute();
                    $stmtImg->close();
                }
            }
        }
    }

    $conn->close();
    echo "<script>alert('Auto y sus imágenes agregadas con éxito'); window.location='panel.html';</script>";
?>
