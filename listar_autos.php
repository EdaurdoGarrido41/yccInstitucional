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

    // Filtro de búsqueda
    $busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : "";

    $where = "";
    if ($busqueda !== "") {
        $where = "WHERE vin LIKE '%$busqueda%' 
                OR titulo LIKE '%$busqueda%' 
                OR stock LIKE '%$busqueda%'";
    }

    $sql = "SELECT id, vin, stock, titulo, imagen, activado 
            FROM catalogo_motors $where";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table table-bordered align-middle">
                <thead class="table-dark">
                <tr>
                    <th>Imagen</th>
                    <th>Título</th>
                    <th>VIN</th>
                    <th>Stock</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>';
        while($row = $result->fetch_assoc()) {
            $estatusTexto = $row['activado'] == 1 ? "Visible" : "Oculto";

            echo '<tr>
                    <td><img src="mastersales-inventario/' . htmlspecialchars($row['imagen']) . '" style="height:80px; object-fit:cover;"></td>
                    <td>' . htmlspecialchars($row['titulo']) . '</td>
                    <td>' . htmlspecialchars($row['vin']) . '</td>
                    <td>' . htmlspecialchars($row['stock']) . '</td>
                    <td>' . $estatusTexto . '</td>
                    <td>';
        
                    // Mostrar solo el botón correspondiente al estado
                    if ($row['activado'] == 1) {
                        echo '<button class="btn btn-sm btn-danger btn-ocultar" data-vin="' . htmlspecialchars($row['vin']) . '">
                            <i class="bi bi-eye-slash"></i> Ocultar
                        </button>';
                    } else {
                        echo '<button class="btn btn-sm btn-success btn-mostrar" data-vin="' . htmlspecialchars($row['vin']) . '">
                            <i class="bi bi-eye"></i> Mostrar
                        </button>';
                    }

                    // Botón Modificar
                    echo "<a href='editar_auto.php?id=".$row['id']."' class='btn btn-info btn-sm'>Modificar</a>";

                    echo '</td>
                </tr>';
        }
        echo '</tbody></table>';
    } else {
        echo "<p>No se encontraron autos.</p>";
    }

    $conn->close();
?>