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

// Función para conectar a una base de datos
function conectarBD($db_name) {
    global $db_configs;
    $config = $db_configs[$db_name];
    
    $conn = new mysqli($config['host'], $config['user'], $config['password'], $config['database']);
    
    if ($conn->connect_error) {
        die("Error de conexión a $db_name: " . $conn->connect_error);
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
            // Actualizar en tabla "biblioteca y pagos"
            $sql = "UPDATE `biblioteca y pagos` SET `adeudo de biblioteca` = 0 WHERE `Num. de cuenta` = ?";
            $stmt = $conn_biblioteca->prepare($sql);
            $stmt->bind_param("i", $num_cuenta);
            
            if ($stmt->execute()) {
                $respuesta['biblioteca'] = "✅ Adeudo de biblioteca actualizado a 0";
            } else {
                $respuesta['biblioteca'] = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
            break;
            
        case 'no_adeudo_laboratorio':
            // Actualizar en tabla "pagos"
            $sql = "UPDATE pagos SET `has pago` = 1 WHERE `Num. de cuenta` = ?";
            $stmt = $conn_laboratorio->prepare($sql);
            $stmt->bind_param("i", $num_cuenta);
            
            if ($stmt->execute()) {
                $respuesta['laboratorio'] = "✅ Pago de laboratorio actualizado a 1";
            } else {
                $respuesta['laboratorio'] = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
            break;
            
        default:
            $respuesta['error'] = "Documento no reconocido";
    }
    
    $conn_biblioteca->close();
    $conn_laboratorio->close();
    
    return $respuesta;
}
?>