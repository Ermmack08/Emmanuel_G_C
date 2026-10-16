<?php
if (isset($_POST['Continuar'])) {
    include 'conexion.php';

    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $primer_apellido = $_POST['apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $tipo_documento = $_POST['tipo_documento'];
    $numero_documento = $_POST['numero_documento'];
    $expedido = $_POST['lugar_expedicion'];
    $fecha_expedicion = $_POST['fecha_expedicion'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $ciudad_nacimiento = $_POST['ciudad_nacimiento'];
    $genero = $_POST['genero'];
    $celular_estudiante = $_POST['celular_estudiante'];
    $email_estudiante = $_POST['email_estudiante'];
    $barrio_estudiante = $_POST['barrio_estudiante'];
    $direccion_residencia = $_POST['direccion_estudiante'];
    $grado = $_POST['grado'];
    $especialidad = $_POST['especialidad'];

    $sql = "INSERT INTO registro_estudiante (Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido, Tipo_de_Documento, Numero_de_Documento, Lugar_de_Expedicion, Fecha_de_Expedicion, Fecha_de_Nacimiento, Ciudad_de_Nacimiento, Genero, Celular, E_mail, Barrio_de_Residencia, Direccion, Grado, Especialidad, estado)
            VALUES ('$primer_nombre', '$segundo_nombre', '$primer_apellido', '$segundo_apellido', '$tipo_documento', '$numero_documento', '$expedido', '$fecha_expedicion', '$fecha_nacimiento', '$ciudad_nacimiento', '$genero', '$celular_estudiante',  '$email_estudiante', '$barrio_estudiante', '$direccion_residencia',  '$grado', '$especialidad', 'pendiente')";

    $verificar_Numero_de_Documento = mysqli_query($conexion, "SELECT * FROM registro_estudiante WHERE Numero_de_Documento = '$numero_documento'");
    if(mysqli_num_rows($verificar_Numero_de_Documento) > 0) {
        echo '<script>
                alert("El número de documento ya está registrado. Por favor, ingrese un número diferente.");
                window.location.href = "registro estudiante.html";
              </script>';
        exit();
    }

    $verificar_E_mail = mysqli_query($conexion, "SELECT * FROM registro_estudiante WHERE E_mail = '$email_estudiante'");
    if(mysqli_num_rows($verificar_E_mail) > 0) {
        echo '<script>
                alert("El E-mail ya está registrado. Por favor, ingrese un E-mail diferente.");
                window.location.href = "registro estudiante.html";
              </script>';
        exit();
    }

    $consulta = mysqli_query($conexion, $sql);
    if ($consulta) {
        // notificar al admin (pon tu correo real)
        $admin = "admin@tudominio.edu";
        @mail($admin, "Nueva solicitud de matrícula", "Hay una nueva solicitud pendiente de $primer_nombre $primer_apellido ($email_estudiante).");

        echo '<script>
                alert("Solicitud enviada. Te avisaremos por correo cuando sea aprobada o rechazada.");
                window.location.href = "ISEA.html";
              </script>';
    } else {
        echo "Error al guardar los datos del estudiante: " . mysqli_error($conexion);
    }
}
