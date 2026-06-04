<?php
// config.php
$db_configs = [
    'biblioteca' => [
        'host' => 'localhost',
        'user' => 'root',  // Cambia por tu usuario
        'password' => '',  // Cambia por tu contraseña
        'database' => 'biblioteca'
    ],
    'laboratorio' => [
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'database' => 'pagos de laboratorio'
    ]
];

// Función para conectar a una base de datos (no hace die() en fallo, devuelve null)
function conectarBD($db_name) {
    global $db_configs;
    if (!isset($db_configs[$db_name])) {
        return null;
    }
    $config = $db_configs[$db_name];
    
    $conn = @new mysqli($config['host'], $config['user'], $config['password'], $config['database']);
    
    if ($conn->connect_error) {
        error_log("Error de conexión a $db_name: " . $conn->connect_error);
        return null;
    }
    
    return $conn;
}

// Función para actualizar el estado de un documento
function actualizarEstadoDocumento($documento_id, $num_cuenta) {
    $conn_biblioteca = conectarBD('biblioteca');
    $conn_laboratorio = conectarBD('laboratorio');
    
    $respuesta = [];
    
    // Actualizar según el documento
    switch($documento_id) {
        case 'no_adeudo_biblioteca':
            if (!$conn_biblioteca) {
                $respuesta['biblioteca'] = '❌ No se pudo conectar a la BD biblioteca';
                break;
            }
            // Actualizar en tabla "biblioteca y pagos"
            $sql = "UPDATE `biblioteca y pagos` SET `adeudo de biblioteca` = 0 WHERE `Num. de cuenta` = ?";
            $stmt = $conn_biblioteca->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $num_cuenta);
                if ($stmt->execute()) {
                    $respuesta['biblioteca'] = "✅ Adeudo de biblioteca actualizado a 0";
                } else {
                    $respuesta['biblioteca'] = "❌ Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $respuesta['biblioteca'] = '❌ Error al preparar la consulta (biblioteca)';
            }
            break;
            
        case 'no_adeudo_laboratorio':
            if (!$conn_laboratorio) {
                $respuesta['laboratorio'] = '❌ No se pudo conectar a la BD laboratorio';
                break;
            }
            // Actualizar en tabla "pagos"
            $sql = "UPDATE pagos SET `has pago` = 1 WHERE `Num. de cuenta` = ?";
            $stmt = $conn_laboratorio->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $num_cuenta);
                if ($stmt->execute()) {
                    $respuesta['laboratorio'] = "✅ Pago de laboratorio actualizado a 1";
                } else {
                    $respuesta['laboratorio'] = "❌ Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $respuesta['laboratorio'] = '❌ Error al preparar la consulta (laboratorio)';
            }
            break;
            
        default:
            $respuesta['error'] = "Documento no reconocido";
    }
    
    if ($conn_biblioteca) $conn_biblioteca->close();
    if ($conn_laboratorio) $conn_laboratorio->close();
    
    return $respuesta;
}
?>