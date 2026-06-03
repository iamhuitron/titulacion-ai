<?php
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cuenta = $_POST['num_cuenta'];
    $password = $_POST['password'];

    // Consulta para verificar usuario
    // Nota: Usamos comillas invertidas porque el nombre de la columna tiene espacios
    $query = "SELECT * FROM cuentas WHERE `Num. de cuenta` = '$cuenta' AND `contraseña` = '$password'";
    $resultado = mysqli_query($conexion, $query);

    if (mysqli_num_rows($resultado) > 0) {
        // Si es correcto, redirigir a chart.html
        header("Location: chart.html");
    } else {
        // Si es incorrecto, mostrar error
        echo "<script>alert('Datos incorrectos'); window.location.href='frontend.html';</script>";
    }
}
?>
