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
    $celular = $_POST['celular_acudiente'];
    $email = $_POST['email_acudiente'];
    $direccion = $_POST['direccion_acuediente'];
    $barrio = $_POST['barrio_acuedinte'];

    $update = "UPDATE registro_acudiente     
               SET Celular='$celular', E_mail='$email', Direccion='$direccion', Barrio_de_Residencia='$barrio' 
               WHERE Numero_de_Documento='$documento'";

if (mysqli_query($conexion, $update)) {
        echo "<script>alert('Datos actualizados correctamente'); 
        window.location='acudiente.php'
        ;</script>";
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }
}

// Consultar datos del acudiente del estudiante logueado
$documento_estudiante = $_SESSION['documento'];
$query = "SELECT * FROM registro_acudiente WHERE Identificacion_Estudiante = '$documento_estudiante' LIMIT 1";
$result = mysqli_query($conexion, $query);
$acudiente = mysqli_fetch_assoc($result);

// Helper para imprimir valores sin errores
function e($key) {
    global $acudiente;
    return isset($acudiente[$key]) ? htmlspecialchars($acudiente[$key]) : '';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GESTIÓN DE MATRÍCULAS DE ISEA</title>
    <link rel="shortcut icon" href="IMAGENES/ISEA-removebg-preview (1).png" type="image/x-icon">
    <link rel="stylesheet" href="acudiente.css"> 
    <style>
        /* Estilo adicional para campos de solo lectura */
        .read-only {
            background-color: #f4f4f4;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <header>
        <div id="header-container">
            <img src="IMAGENES/ISEAblanca-sin-fondo.png" id="logoalumno" alt="Logo de ISEA">
            <h1>Datos del Acudiente</h1>
            <nav>
                <ul class="menu">
                    <li><a href="hoja_de_vida.php">Atrás</a></li>
                    <li><a href="Panel_estudiante.php">Salir</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div id="acudiente" class="tab-content container">
        <form id="guardianForm">
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
                <label for="tipo_documento_acudiente">Tipo de Documento</label>
                <select id="tipo_documento_acudiente" name="tipo_documento_acudiente" readonly value="<?php echo e('Tipo_de_Documento'); ?>">
                    <option value="" disabled selected>Seleccione</option>
                    <option value="cedula">Cédula de Ciudadanía</option>
                    <option value="registro_civil">Cédula Extranjera</option>
                </select>
            </div>
            <div>
                <label for="numero_documento_acudiente">Número de Documento</label>
                <input type="text" id="numero_documento_acudiente" name="numero_documento_acudiente" readonly value="<?php echo e('Numero_de_Documento'); ?>">
            </div>
            <div>
                <label for="expedido_acudiente">Expedido</label>
                <input type="text" id="expedido_acudiente" name="expedido_acudiente" readonly value="<?php echo e('Lugar_de_Expedicion'); ?>">
            </div>
            <div>
                <label for="fecha_expedicion_acudiente">Fecha de Expedición</label>
                <input type="date" id="fecha_expedicion_acudiente" name="fecha_expedicion_acudiente" readonly value="<?php echo e('Fecha_de_Expedicion'); ?>">
            </div>
            <div>
                <label for="genero">Género</label>
                <select id="genero" name="genero" readonly value="<?php echo e('Genero'); ?>">
                    <option value="" disabled selected>Seleccione</option>
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            
            <div>
                <label for="celular_acudiente">Celular</label>
                <input type="text" id="celular_acudiente" name="celular_acudiente" readonly value="<?php echo e('Celular'); ?>">
            </div>
            <div>
                <label for="email_acudiente">E-mail</label>
                <input type="email" id="email_acudiente" name="email_acudiente" readonly value="<?php echo e('E_mail'); ?>">
            </div>
            <div>
                <label for="barrio_acudiente">Barrio de Residencia</label>
                <input type="text" id="barrio_acudiente" name="barrio_acudiente" readonly value="<?php echo e('Barrio_de_Residencia'); ?>">
            </div>
            <div>
                <label for="direccion_acudiente">Dirección</label>
                <input type="text" id="direccion_acudiente" name="direccion_acudiente" readonly value="<?php echo e('Direccion'); ?>">
            </div>
            <div>
                <label for="identificacion_acudiente">Identificación Estudiante</label>
                <input type="text" id="identificacion_acudiente" name="identificacion_acudiente" readonly value="<?php echo e('Identificacion_Estudiante'); ?>">
            </div>
            <div>
                <label for="parentesco_acudiente">Parentesco</label>
                <input type="text" id="parentesco_acudiente" name="parentesco_acudiente" readonly value="<?php echo e('Parentesco'); ?>">
            </div>
        </form>
    </div>

   
</body>
</html>