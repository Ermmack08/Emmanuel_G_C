<?php
// ------------------------------------
// CONEXIÓN A LA BASE DE DATOS
// ------------------------------------
include("conexion.php"); // O cambia a conexion2.php si allí está tu $conn

if (!$conn) {
    die("❌ Error en la conexión: " . mysqli_connect_error());
}

// ------------------------------------
// VERIFICAR QUE EL FORMULARIO SE ENVIÓ
// ------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Capturar los datos del formulario
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $primer_apellido = $_POST['apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $tipo_documento = $_POST['tipo_documento'];
    $numero_documento = $_POST['numero_documento'];
    $lugar_expedicion = $_POST['lugar_expedicion'];
    $fecha_expedicion = $_POST['fecha_expedicion'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $ciudad_nacimiento = $_POST['ciudad_nacimiento'];
    $genero = $_POST['genero'];
    $celular = $_POST['celular_estudiante'];
    $email = $_POST['email_estudiante'];
    $barrio = $_POST['barrio_estudiante'];
    $direccion = $_POST['direccion_estudiante'];
    $grado = $_POST['grado'];
    $especialidad = isset($_POST['especialidad']) ? $_POST['especialidad'] : NULL;

    // Contraseña aleatoria
    $contrasena = password_hash(bin2hex(random_bytes(4)), PASSWORD_DEFAULT);
    $estado = 'pendiente';

    // ------------------------------------
    // MANEJO DE ARCHIVOS
    // ------------------------------------
    $upload_dir_fotos = "uploads/fotos/";
    $upload_dir_docs = "uploads/docs/";

    // Crear carpetas si no existen
    if (!file_exists($upload_dir_fotos)) {
        mkdir($upload_dir_fotos, 0777, true);
    }
    if (!file_exists($upload_dir_docs)) {
        mkdir($upload_dir_docs, 0777, true);
    }

    // Nombre base del archivo
    $nombre_base = $primer_nombre . "_" . $segundo_nombre . "_" . $primer_apellido . "_" . $segundo_apellido . "_Grado_" . $grado;

    // Guardar foto
    $foto_nombre = NULL;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_nombre = $upload_dir_fotos . $nombre_base . "_foto." . $foto_extension;
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto_nombre);
    }

    // Guardar documentos adicionales
    $documentos_guardados = [];
    if (isset($_FILES['documentos'])) {
        foreach ($_FILES['documentos']['tmp_name'] as $index => $tmp_name) {
            if ($_FILES['documentos']['error'][$index] == 0) {
                $doc_extension = pathinfo($_FILES['documentos']['name'][$index], PATHINFO_EXTENSION);
                $doc_nombre = $upload_dir_docs . $nombre_base . "_doc_" . ($index + 1) . "." . $doc_extension;
                move_uploaded_file($tmp_name, $doc_nombre);
                $documentos_guardados[] = $doc_nombre;
            }
        }
    }

    // ------------------------------------
    // GUARDAR EN BASE DE DATOS
    // ------------------------------------
    $sql = "INSERT INTO registro_estudiante 
        (Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido, 
         Tipo_de_Documento, Numero_de_Documento, Lugar_de_Expedicion, Fecha_de_Expedicion, 
         Fecha_de_Nacimiento, Ciudad_de_Nacimiento, Genero, Celular, 
         E_mail, Barrio_de_Residencia, Direccion, Grado, 
         Especialidad, contrasena, estado, creado_en) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, 
    "sssssssssssssssssss", // 👈 19 "s"
    $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido,
    $tipo_documento, $numero_documento, $lugar_expedicion, $fecha_expedicion,
    $fecha_nacimiento, $ciudad_nacimiento, $genero, $celular, 
    $email, $barrio, $direccion, $grado, 
    $especialidad, $contrasena, $estado
);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script>
              Swal.fire({
                  icon: 'success',
                  title: '¡Datos guardados!',
                  text: 'El registro del estudiante fue exitoso. Continúa con el registro del acudiente.',
                  confirmButtonText: 'Continuar'
              }).then(() => {
                  window.location.href = 'registro acudiente.html?identificacion=" . $numero_documento . "';
              });
              </script>";
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script>
              Swal.fire({
                  icon: 'error',
                  title: 'Error al guardar',
                  text: 'No se pudieron guardar los datos del estudiante. Inténtalo nuevamente.'
              });
              </script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
