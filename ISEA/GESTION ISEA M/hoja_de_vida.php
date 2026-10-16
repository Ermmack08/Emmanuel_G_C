<?php
session_start();
include 'conexion.php';

// Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['documento'])) {
    header("Location: iniciar.html");
    exit();
}


// Si se envía el formulario de actualización
if (isset($_POST['actualizar'])) {
    $documento = $_SESSION['documento'];
    $celular = $_POST['celular_estudiante'];
    $email = $_POST['email_estudiante'];
    $direccion = $_POST['direccion_estudiante'];
    $barrio = $_POST['barrio_estudiante'];

    $update = "UPDATE registro_estudiante 
               SET Celular='$celular', E_mail='$email', Direccion='$direccion', Barrio_de_Residencia='$barrio' 
               WHERE Numero_de_Documento='$documento'";

if (mysqli_query($conexion, $update)) {
        echo "<script>alert('Datos actualizados correctamente'); 
        window.location='hoja_de_vida.php'
        ;</script>";
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }
}

// Consultar datos del estudiante logueado
$documento = $_SESSION['documento'];
$query = "SELECT * FROM registro_estudiante WHERE Numero_de_Documento = '$documento' LIMIT 1";
$result = mysqli_query($conexion, $query);
$estudiante = mysqli_fetch_assoc($result);

// Helper para imprimir valores sin errores
function e($key) {
    global $estudiante;
    return isset($estudiante[$key]) ? htmlspecialchars($estudiante[$key]) : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GESTIÓN DE MATRÍCULAS DE ISEA</title>
    <link rel="shortcut icon" href="IMAGENES/ISEA-removebg-preview (1).png" type="image/x-icon">
    <link rel="stylesheet" href="formulario.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" crossorigin="anonymous">
</head>
<body>
    <header>
        <div id="header-container">
            <img src="IMAGENES/ISEAblanca-sin-fondo.png" id="logoalumno" alt="logo">
            <div class="column-container">
                <h1 class="NuevoAlumno">Datos del Alumno</h1>
            </div>
            <nav>
                <ul class="menu">
                    <li><a href="Panel_estudiante.php">Atras</a></li>
                    <li><a href="acudiente.php">Continuar</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <div class="photo-upload-container">
            <img id="fotoEstudiante" src="placeholder.jpg" alt="Foto del Estudiante">
        </div>
        <div id="datosPersonales" class="tab-content active">
            <form action="hoja_de_vida.php" method="post" id="studentForm">
                <h2>Información Personal</h2><br>

                <div>
                    <label for="primer_nombre">Primer Nombre</label>
                    <input type="text" id="primer_nombre" name="primer_nombre" readonly value="<?php echo e('Primer_Nombre'); ?>">
                </div>
                <div>
                    <label for="segundo_nombre">Segundo Nombre</label>
                    <input type="text" id="segundo_nombre" name="segundo_nombre" readonly value="<?php echo e('Segundo_Nombre'); ?>">
                </div>
                <div>
                    <label for="apellido">Primer Apellido</label>
                    <input type="text" id="apellido" name="apellido" readonly value="<?php echo e('Primer_Apellido'); ?>">
                </div>
                <div>
                    <label for="segundo_apellido">Segundo Apellido</label>
                    <input type="text" id="segundo_apellido" name="segundo_apellido" readonly value="<?php echo e('Segundo_Apellido'); ?>">
                </div>
                <div>
                    <label for="tipo_documento">Tipo de Documento</label>
                    <input type="text" id="tipo_documento" name="tipo_documento" readonly value="<?php echo e('Tipo_de_Documento'); ?>">
                </div>
                <div>
                    <label for="numero_documento">Número de Documento</label>
                    <input type="text" id="numero_documento" name="numero_documento" readonly value="<?php echo e('Numero_de_Documento'); ?>">
                </div>
                <div>
                    <label for="lugar_expedicion">Lugar de Expedición</label>
                    <input type="text" id="lugar_expedicion" name="lugar_expedicion" readonly value="<?php echo e('Lugar_de_Expedicion'); ?>">
                </div>
                <div>
                    <label for="fecha_expedicion">Fecha de Expedición</label>
                    <input type="date" id="fecha_expedicion" name="fecha_expedicion" readonly value="<?php echo e('Fecha_de_Expedicion'); ?>">
                </div>
                <div>
                    <label for="genero">Género</label>
                    <input type="text" id="genero" name="genero" readonly value="<?php echo e('Genero'); ?>">
                </div>
                <div>
                    <label for="celular_estudiante">Celular</label>
                    <input type="text" id="celular_estudiante" name="celular_estudiante" value="<?php echo e('Celular'); ?>">
                </div>
                <div>
                    <label for="email_estudiante">E-mail</label>
                    <input type="email" id="email_estudiante" name="email_estudiante" value="<?php echo e('E_mail'); ?>">
                </div>
                <div>
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" readonly value="<?php echo e('Fecha_de_Nacimiento'); ?>">
                </div>
                <div>
                    <label for="ciudad_nacimiento">Ciudad de Nacimiento</label>
                    <input type="text" id="ciudad_nacimiento" name="ciudad_nacimiento" readonly value="<?php echo e('Ciudad_de_Nacimiento'); ?>">
                </div>
                <div>
                    <label for="barrio_estudiante">Barrio de Residencia</label>
                    <input type="text" id="barrio_estudiante" name="barrio_estudiante" value="<?php echo e('Barrio_de_Residencia'); ?>">
                </div>
                <div>
                    <label for="direccion_estudiante">Dirección</label>
                    <input type="text" id="direccion_estudiante" name="direccion_estudiante" value="<?php echo e('Direccion'); ?>">
                </div><br>

                <h2>Datos Académicos</h2><br>
                <div>
                    <label for="numero_matricula">Número de Matrícula</label>
                    <input type="text" id="numero_matricula" name="numero_matricula" readonly value="<?php echo e('Numero_Matricula'); ?>">
                </div>
                <div>
                    <label for="fecha_matricula">Fecha de Matrícula</label>
                    <input type="date" id="fecha_matricula" name="fecha_matricula" readonly value="<?php echo e('Fecha_Matricula'); ?>">
                </div>
                <div>
                    <label for="fecha_renovacion">Fecha de Renovación</label>
                    <input type="date" id="fecha_renovacion" name="fecha_renovacion" readonly value="<?php echo e('Fecha_Renovacion'); ?>">
                </div>
                <div>
                    <label for="grupo">Grupo</label>
                    <input type="text" id="grupo" name="grupo" readonly value="<?php echo e('Grupo'); ?>">
                </div>
                <div>
                    <label for="email_institucional">E-mail Institucional</label>
                    <input type="email" id="email_institucional" name="email_institucional" readonly value="<?php echo e('Email_Institucional'); ?>">
                </div>
                <div>
                    <label for="especialidad">Especialidad</label>
                    <input type="text" id="especialidad" name="especialidad" readonly value="<?php echo e('Especialidad'); ?>">
                </div>

                <!-- Botón de actualización -->
                <div style="margin-top:20px;">
                    <button type="submit" name="actualizar">Actualizar Hoja de Vida</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
