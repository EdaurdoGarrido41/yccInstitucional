<?php
    $host = "localhost";
    $user = "u676669933_catalogo";
    $pass = "Mastersales2025";
    $db   = "u676669933_mastersales";

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) die("Error conexión: " . $conn->connect_error);

    $id = $_GET['id'] ?? 0;

    // Obtener datos del auto
    $sql = "SELECT * FROM catalogo_motors WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $auto = $result->fetch_assoc();
    $stmt->close();

    // Obtener imágenes adicionales
    $sqlImg = "SELECT * FROM catalogo_motors_imagenes WHERE id_vehiculo = ?";
    $stmtImg = $conn->prepare($sqlImg);
    $stmtImg->bind_param("i", $id);
    $stmtImg->execute();
    $imagenes = $stmtImg->get_result();
    $stmtImg->close();

    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Editar Auto</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="container mt-5">

        <h2>Editar Vehículo</h2>
        <form action="actualizar_auto.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $auto['id']; ?>">

            <!-- Campos generales -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($auto['titulo']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">VIN *</label>
                    <input type="text" name="vin" class="form-control" value="<?php echo htmlspecialchars($auto['vin']); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Stock *</label>
                    <input type="text" name="stock" class="form-control" value="<?php echo htmlspecialchars($auto['stock']); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Exterior</label>
                    <input type="text" name="exterior" class="form-control" value="<?php echo htmlspecialchars($auto['exterior']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Interior</label>
                    <input type="text" name="interior" class="form-control" value="<?php echo htmlspecialchars($auto['interior']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Drivetrain</label>
                    <input type="text" name="drivetrain" class="form-control" value="<?php echo htmlspecialchars($auto['drivetrain']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Transmisión</label>
                    <input type="text" name="transsmision" class="form-control" value="<?php echo htmlspecialchars($auto['transsmision']); ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Motor</label>
                    <input type="text" name="engine" class="form-control" value="<?php echo htmlspecialchars($auto['engine']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Eficiencia de Combustible</label>
                    <input type="text" name="fuel_efficiency" class="form-control" value="<?php echo htmlspecialchars($auto['fuel_efficiency']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Kilometraje</label>
                    <input type="text" name="mileage" class="form-control" value="<?php echo htmlspecialchars($auto['mileage']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Marca</label>
                    <input type="text" name="make" class="form-control" value="<?php echo htmlspecialchars($auto['make']); ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Modelo</label>
                    <input type="text" name="model" class="form-control" value="<?php echo htmlspecialchars($auto['model']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Año</label>
                    <input type="text" name="year" class="form-control" value="<?php echo htmlspecialchars($auto['year']); ?>">
                </div>
            </div>

            <!-- Imagen principal -->
            <div class="mb-3">
                <label class="form-label">Imagen Principal</label><br>
                <?php if (!empty($auto['imagen'])): ?>
                    <img src="mastersales-inventario/<?php echo $auto['imagen']; ?>" width="200"><br><br>
                <?php endif; ?>
                <input type="file" name="imagen">
            </div>

            <!-- Imágenes adicionales -->
            <div class="mb-3">
                <label class="form-label">Imágenes Adicionales</label><br>
                <div class="row">
                    <?php while ($img = $imagenes->fetch_assoc()): ?>
                        <div class="col-md-3 text-center mb-3">
                            <img src="mastersales-inventario/<?php echo $img['nombre_imagen']; ?>" class="img-fluid rounded border" style="max-height:150px"><br>
                            <a href="eliminar_imagen.php?id=<?php echo $img['id']; ?>&vehiculo=<?php echo $auto['id']; ?>" class="btn btn-sm btn-danger mt-2">Eliminar</a>
                        </div>
                    <?php endwhile; ?>
                </div>
                <br>
                <input type="file" name="imagenes[]" multiple>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="panel.html" class="btn btn-secondary">Cancelar</a>
        </form>

    </body>
</html>
