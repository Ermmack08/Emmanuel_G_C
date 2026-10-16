<?php
session_start();
include 'conexion.php';

if (isset($_POST['inicio'])) {
    $user = $_POST['user'];
    $email = $_POST['email'];
    $password = $_POST['password'];

$validar_admin = mysqli_query($conexion, "SELECT * FROM registro_administrador WHERE Usuario='$user' AND E_mail='$email' AND contraseña='$password'");

if (mysqli_num_rows($validar_admin) > 0) {
    $row = mysqli_fetch_row($validar_admin);
    $_SESSION['user'] = $row[2];
    header("Location: panel_administrador.php");
    exit();
} else {
    echo '
    <script>
        alert("Usuario no existe, por favor verifique los datos ingresados.");
        window.location = "iniciar.html";
    </script>
    ';
    exit();
}
}