<?php
session_start();
include 'conexion.php';

if (isset($_POST['iniciar'])) {
    $documento = $_POST['documento'];
    $password = $_POST['password'];

    $validar_estudiante = mysqli_query($conexion, 
        "SELECT CONCAT(Primer_Nombre, ' ', Segundo_Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) AS Nombre_Completo, 
                Numero_de_Documento 
         FROM registro_estudiante 
         WHERE Numero_de_Documento='$documento' AND contrasena='$password'");

    if (mysqli_num_rows($validar_estudiante) > 0) {
        $row = mysqli_fetch_assoc($validar_estudiante);

        $_SESSION['nombre'] = $row['Nombre_Completo']; 
        $_SESSION['documento'] = $row['Numero_de_Documento'];

        header("Location: Panel_estudiante.php");
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
?>
