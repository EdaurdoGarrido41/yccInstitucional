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

// Consulta inventario
$sql = "SELECT * FROM catalogo_motors WHERE activado = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {        
        echo '
        <div class="col-md-3 mb-4">
            <div class="card h-100" style="width: 15rem;">
                <img src="mastersales-inventario/' . htmlspecialchars($row['imagen']) . '" 
                    class="card-img-top" 
                    alt="' . htmlspecialchars($row['vin']) . '">
                <div class="card-body">
                    <h5 class="card-title texto-secundario">' . htmlspecialchars($row['titulo']) . '</h5>
                    <p class="card-text">VIN: ' . htmlspecialchars($row['vin']) . '</p>
                    <p class="card-text">Stock: ' . htmlspecialchars($row['stock']) . '</p>
                </div>
            </div>
        </div>';
    }
} else {
    echo "<p>No hay autos en inventario.</p>";
}

$conn->close();

?>
