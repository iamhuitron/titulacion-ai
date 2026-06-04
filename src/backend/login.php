<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$matricula = $_POST['matricula'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

if (!$matricula || !$contrasena) {
    echo json_encode(['success' => false, 'message' => 'Matrícula y contraseña requeridas']);
    exit;
}

// Configuración de base de datos - intenta cargar de config.php
include_once __DIR__ . '/config.php';
$dbHost = $DB_HOST ?? 'localhost';
$dbUser = $DB_USER ?? 'root';
$dbPass = $DB_PASS ?? '';
$dbName = 'alumnos';

// Permitir siempre el acceso con admin/admin para pruebas locales
if ($matricula === 'admin' && $contrasena === 'admin') {
    echo json_encode(['success' => true, 'redirect' => '/frontendV2/chart.html']);
    exit;
}

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_error) {
    // Fallback para pruebas locales si no hay BD disponible
    // Acepta la credencial numérica de prueba antigua y también admin/admin para testing rápido
    if (($matricula === '323332855' && $contrasena === '24092007') || ($matricula === 'admin' && $contrasena === 'admin')) {
        echo json_encode(['success' => true, 'redirect' => '/frontendV2/chart.html']);
        exit;
    }
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

// Preparar y ejecutar consulta segura
$stmt = $mysqli->prepare("SELECT `Num. de cuenta` FROM `cuentas` WHERE `Num. de cuenta` = ? AND `contraseña` = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error interno']);
    exit;
}

$stmt->bind_param('ss', $matricula, $contrasena);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => true, 'redirect' => '/frontendV2/chart.html']);
} else {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
}

$stmt->close();
$mysqli->close();
