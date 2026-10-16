<?php
// solicitud_cambiar_estado.php (versión tolerante)
header('Content-Type: application/json; charset=utf-8');
include 'conexion.php';

// Modo diagnóstico rápido (coméntalo si te molesta):
// ini_set('display_errors', 1); error_reporting(E_ALL);

// 1) Leer payload venga como venga: JSON, POST o GET
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) $data = [];
if (empty($data) && !empty($_POST)) $data = $_POST;
if (empty($data) && !empty($_GET))  $data = $_GET;

// 2) Normalizar campos
$doc    = isset($data['id']) ? (int)$data['id'] : 0;               // del front llega como "id"
$accion = isset($data['accion']) ? strtolower(trim($data['accion'])) : '';

// 3) Validar
if (!$doc || !in_array($accion, ['aceptar','rechazar'], true)) {
  echo json_encode(['ok'=>false,'error'=>'Datos inválidos','recibido'=>$data]); 
  exit;
}

// 4) Traer datos del estudiante por Numero_de_Documento (tu PK real)
$q = "SELECT E_mail, Primer_Nombre, Primer_Apellido 
      FROM registro_estudiante 
      WHERE Numero_de_Documento=$doc";
$r = mysqli_query($conexion, $q);
$est = mysqli_fetch_assoc($r);
if (!$est) { echo json_encode(['ok'=>false,'error'=>'No encontrado']); exit; }

$email  = $est['E_mail'];
$nombre = $est['Primer_Nombre'].' '.$est['Primer_Apellido'];

if ($accion === 'rechazar') {
  mysqli_query($conexion, "UPDATE registro_estudiante SET estado='rechazado' WHERE Numero_de_Documento=$doc");
  if (!empty($email)) {
    @mail($email, "Solicitud de matrícula rechazada", "Hola $nombre,\n\nTu solicitud ha sido rechazada.\n\nISEA");
  }
  echo json_encode(['ok'=>true,'nuevoEstado'=>'Rechazada']); 
  exit;
}

// aprobar
mysqli_query($conexion, "UPDATE registro_estudiante SET estado='aprobado' WHERE Numero_de_Documento=$doc");

// password temporal sencillito
$pwd = substr(md5(uniqid('', true)), 0, 8);

// crear tabla usuarios si no existe (tal como venías haciéndolo)
mysqli_query($conexion, "CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  estudiante_id INT NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  rol VARCHAR(20) DEFAULT 'estudiante'
)");

// guardamos Numero_de_Documento en estudiante_id (porque no tienes id)
mysqli_query($conexion, "INSERT INTO usuarios (estudiante_id, email, password, rol)
                         VALUES ($doc, '$email', '$pwd', 'estudiante')");

if (!empty($email)) {
  @mail($email, "Solicitud de matrícula aprobada",
"Hola $nombre,\n\nTu solicitud fue aprobada.\n\nAccede al portal:
URL: https://tudominio/portal/login.php
Usuario: $email
Contraseña temporal: $pwd

Cámbiala al iniciar sesión.");
}

echo json_encode(['ok'=>true,'nuevoEstado'=>'Aceptada']);
