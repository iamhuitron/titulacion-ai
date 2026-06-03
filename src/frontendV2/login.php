<?php
$host = "localhost:8080";
$user = "root";
<?php
// 1. Conexión a la base de datos
$conexion = mysqli_connect("localhost:8080", "root", "", "alumnos");

// 2. Recibir datos del formulario
$cuenta = $_POST['num_cuenta'];
$password = $_POST['password'];

// 3. Consultar la tabla 'cuentas' (usamos comillas invertidas por el nombre con puntos)
$query = "SELECT * FROM cuentas WHERE `Num. de cuenta` = '$cuenta' AND `contraseña` = '$password'";
$resultado = mysqli_query($conexion, $query);

// 4. LA CONEXIÓN A LA SIGUIENTE PÁGINA
if (mysqli_num_rows($resultado) > 0) {
    // Si los datos son correctos, manda a la página del semáforo
    header("Location: chart.html");
    exit();
} else {
    // Si fallan, avisa y regresa al login
    echo "<script>alert('Datos incorrectos'); window.location.href='frontend.html';</script>";
}
$pass = "";
$db   = "alumnos";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

if (mysqli_num_rows($resultado) > 0) {
    // Esta línea es la que "une" las páginas tras el éxito
    header("Location: chart.html");
    exit(); 
} else {
    // Si los datos no existen en la tabla 'cuentas'
    echo "<script>alert('Usuario o contraseña incorrectos'); window.location.href='frontend.html';</script>";
}
<a href="chart.html" class="back-link">Volver</a>


?>
