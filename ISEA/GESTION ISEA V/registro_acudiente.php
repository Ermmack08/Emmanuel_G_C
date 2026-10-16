<?php

if (isset($_POST['Enviar'])) {
    include 'conexion.php';

    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $primer_apellido = $_POST['apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $tipo_documento = $_POST['tipo_documento_acudiente'];
    $numero_documento = $_POST['numero_documento_acudiente'];
    $expedido = $_POST['lugar_expedicion_acudiente'];
    $fecha_expedicion = $_POST['fecha_expedicion_acudiente'];
    $fecha_nacimiento = $_POST['fecha_nacimiento_acudiente'];
    $ciudad_nacimiento = $_POST['ciudad_nacimiento_acudiente'];
    $genero = $_POST['genero'];
    $celular_acudiente = $_POST['celular_acudiente'];
    $email_acudiente = $_POST['email_acudiente'];
    $barrio_acudiente = $_POST['barrio_acudiente'];
    $direccion_residencia = $_POST['direccion_acudiente'];
    $identificacion = $_POST ['identificacion_estudiante'];
    $parentesco = $_POST ['parentesco'];

    $sql = "INSERT INTO registro_acudiente (Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido, Tipo_de_Documento, Numero_de_Documento, Lugar_de_Expedicion, Fecha_de_Expedicion, Fecha_de_Nacimiento, Ciudad_de_Nacimiento, Genero, Celular, E_mail, Barrio_de_Residencia, Direccion, Identificacion_Estudiante, Parentesco) VALUES ('$primer_nombre', '$segundo_nombre', '$primer_apellido', '$segundo_apellido', '$tipo_documento', '$numero_documento', '$expedido', '$fecha_expedicion', '$fecha_nacimiento', '$ciudad_nacimiento', '$genero', '$celular_acudiente',  '$email_acudiente', '$barrio_acudiente', '$direccion_residencia', '$identificacion', '$parentesco')";

$verificar_Numero_de_Documento = mysqli_query($conexion, "SELECT * FROM registro_acudiente WHERE Numero_de_Documento = '$numero_documento'");
if(mysqli_num_rows($verificar_Numero_de_Documento) > 0) {
    echo '<script>
            alert("El número de documento ya está registrado. Por favor, ingrese un número diferente.");
            window.location.href = "registro acudiente.html";
          </script>';
    exit();
}
$verificar_E_mail = mysqli_query($conexion, "SELECT * FROM registro_acudiente WHERE E_mail = '$email_acudiente'");
if(mysqli_num_rows($verificar_E_mail) > 0) {
    echo '<script>
            alert("El E-mail ya está registrado. Por favor, ingrese un E-mail diferente.");
            window.location.href = "registro acudiente.html";
          </script>';
    exit();
}
$consulta = mysqli_query($conexion, $sql);
if ($consulta) {    
    echo '<script>
            alert("Tu solicitud fue enviada correctamente. Esta en proceso aceptacion, queda atento a tu correo o numero de celular.");
            window.location.href = "ISEA.html";
          </script>';
} else {
    echo "Error al enviar la solicitud: " . mysqli_error($conexion);
}
}

