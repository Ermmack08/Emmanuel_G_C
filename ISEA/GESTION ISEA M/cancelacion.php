<?php

if (isset($_POST['enviar'])) {
    include 'conexion.php';

    $documento = $_POST['documento'];
    $Nombre_del_Estudiante = $_POST['nombre'];
    $Grado = $_POST['grado'];
    $Nombre_tutor = $_POST['nombre_tutor'];
    $Motivo = $_POST['motivo'];

    $sql = "DELETE FROM registro_estudiante WHERE `Numero_de_Documento` = '$documento'";

$verificar_Numero_de_Documento = mysqli_query($conexion, "SELECT * FROM registro_estudiante WHERE Numero_de_Documento = '$documento'");
if(mysqli_num_rows($verificar_Numero_de_Documento) > 0) {
    
    $consulta = mysqli_query($conexion, $sql);
    if ($consulta) {    
        echo '<script>
                alert("Cancelacion realizada de manera exitosa.");
                window.location.href = "CALENDARIO.html";
              </script>';
    } else {
        echo "Error al cancelar la matricula del estudiante: " . mysqli_error($conexion);
    }
    exit();
}
else {
    echo '<script>
            alert("El número de documento no está registrado. Por favor, ingrese un número válido.");
            window.location.href = "cancelacion de matricula.html";
          </script>';
    exit();

}
}