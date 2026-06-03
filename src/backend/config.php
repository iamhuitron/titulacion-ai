<?php
// upload.php
require_once 'config.php';

// Configuración
$upload_dir = "uploads/";
$max_file_size = 5 * 1024 * 1024; // 5 MB

// Crear carpeta si no existe
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Función para sanitizar nombres de archivo
function sanitize_filename($filename) {
    return preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $documento_id = isset($_POST['documento_id']) ? $_POST['documento_id'] : '';
    $num_cuenta = isset($_POST['num_cuenta']) ? intval($_POST['num_cuenta']) : 0;
    
    // Validar datos requeridos
    if (empty($documento_id) || $num_cuenta <= 0) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
    }
    
    // Validar archivo
    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
        exit;
    }
    
    $file = $_FILES['archivo'];
    $file_name = sanitize_filename($file['name']);
    $file_tmp = $file['tmp_name'];
    $file_type = $file['type'];
    $file_size = $file['size'];
    
    // Validar tipo de archivo (PDF)
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if ($file_extension !== 'pdf' || $file_type !== 'application/pdf') {
        echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos PDF']);
        exit;
    }
    
    // Validar tamaño
    if ($file_size > $max_file_size) {
        echo json_encode(['success' => false, 'message' => 'El archivo no debe exceder los 5 MB']);
        exit;
    }
    
    // Crear nombre único para el archivo
    $unique_name = $documento_id . '_' . $num_cuenta . '_' . time() . '.pdf';
    $target_path = $upload_dir . $unique_name;
    
    // Mover archivo
    if (move_uploaded_file($file_tmp, $target_path)) {
        // Actualizar base de datos según el documento
        $resultado = actualizarEstadoDocumento($documento_id, $num_cuenta);
        
        // Registrar en log
        $log_entry = date('Y-m-d H:i:s') . " - Documento: $documento_id - Cuenta: $num_cuenta - Archivo: $unique_name\n";
        file_put_contents($upload_dir . 'upload_log.txt', $log_entry, FILE_APPEND);
        
        echo json_encode([
            'success' => true,
            'message' => 'Archivo subido exitosamente',
            'detalles' => $resultado,
            'archivo' => $unique_name
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>