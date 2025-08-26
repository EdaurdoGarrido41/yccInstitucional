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
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Obtener datos del formulario
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

    // Validar obligatorios
    if (empty($vin) || empty($stock)) {
        die("Error: VIN y Stock son obligatorios.");
    }

    // Manejo de imagen
    $nombreImagen = ""; // valor por defecto
    if (!empty($_FILES['imagen']['tmp_name'])) {

        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION); // obtener extensión original
        $nombreImagen = $vin . "." . $extension; // renombrar con VIN y conservar extensión

        $rutaDestino = "mastersales-inventario/" . $nombreImagen;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
    } else {
        $nombreImagen = ""; // Si no se sube imagen
    }

    // Insertar en la BD
    $sql = "INSERT INTO catalogo_motors 
            (id, titulo, vin, stock, exterior, interior, drivetrain, transsmision, engine, fuel_efficiency, mileage, make, model, year, imagen, activado, date_registration)
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en SQL: " . $conn->error);
    }
    $stmt->bind_param("sssssssssssssss",
        $titulo, $vin, $stock, $exterior, $interior, $drivetrain,
        $transsmision, $engine, $fuel_efficiency, $mileage,
        $make, $model, $year, $nombreImagen
    );

    if (!$stmt->execute()) die("Error al guardar vehículo: " . $stmt->error);

    $idVehiculo = $stmt->insert_id; // ID del auto insertado
    $stmt->close();

    // Subir imágenes múltiples
    if (!empty($_FILES['imagenes']['tmp_name'][0])) {
        $contador = 1;
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            $ext = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION);
            $nombreImagen = $vin . "_" . $contador . "." . $ext;
            $rutaDestino = "mastersales-inventario/" . $nombreImagen;
            move_uploaded_file($tmp_name, $rutaDestino);

            // Guardar en la tabla de imágenes
            $stmtImg = $conn->prepare("INSERT INTO catalogo_motors_imagenes (id_vehiculo, nombre_imagen) VALUES (?, ?)");
            $stmtImg->bind_param("is", $idVehiculo, $nombreImagen);
            $stmtImg->execute();
            $stmtImg->close();
            $contador++;
        }
    }

    $conn->close();
    echo "<script>alert('Auto y sus imágenes agregadas con éxito'); window.location='panel.html';</script>";
?>
