<?php
header('Content-Type: application/json; charset=utf-8');
require 'conexion2.php';

// Configuración
$maxFileSize = 6 * 1024 * 1024; // 6 MB
$allowedImageExt = ['png','jpg','jpeg','webp','gif'];
$allowedAllExt = array_merge($allowedImageExt, ['pdf']);
$uploadDirFirmas = __DIR__ . '/uploads/firmas/';
$uploadDirImagenes = __DIR__ . '/uploads/imagenes/';

// crear carpetas si no existen
if (!is_dir($uploadDirFirmas)) @mkdir($uploadDirFirmas, 0755, true);
if (!is_dir($uploadDirImagenes)) @mkdir($uploadDirImagenes, 0755, true);

// Helpers
function ext_from_name($name) {
  return strtolower(pathinfo($name, PATHINFO_EXTENSION));
}
function safeFilename($prefix, $ext) {
  return $prefix . '_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
}
function handleUploadedFile($file, $uploadDir, $allowedExt, $maxSize, $prefix) {
  if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
  if ($file['size'] > $maxSize) throw new Exception('Archivo demasiado grande.');
  $ext = ext_from_name($file['name']);
  if (!in_array($ext, $allowedExt)) throw new Exception('Tipo de archivo no permitido.');
  if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) throw new Exception('No se pudo crear carpeta de uploads.');
  $safe = safeFilename($prefix, $ext);
  $target = $uploadDir . $safe;
  if (!move_uploaded_file($file['tmp_name'], $target)) throw new Exception('No se pudo mover el archivo subido.');
  // devolver ruta relativa
  // Nota: devuelve la ruta relativa tal y como la guardas en BD (ajusta si tu web apunta a otra carpeta)
  return str_replace(__DIR__ . '/', '', $target);
}
function handleCanvasFile($file, $uploadDir, $maxSize, $prefix) {
  if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
  if ($file['size'] > $maxSize) throw new Exception('Archivo demasiado grande.');
  if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) throw new Exception('No se pudo crear carpeta de uploads.');
  $safe = safeFilename($prefix, 'png');
  $target = $uploadDir . $safe;
  if (!move_uploaded_file($file['tmp_name'], $target)) throw new Exception('No se pudo mover el blob del canvas.');
  return str_replace(__DIR__ . '/', '', $target);
}

// Generar matrícula única de 12 dígitos
function generarMatriculaUnica(PDO $pdo): string {
  $tries = 0;
  do {
    $num = str_pad((string)random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
    $q = $pdo->prepare("SELECT 1 FROM matriculas WHERE matricula = :n LIMIT 1");
    $q->execute([':n'=>$num]);
    $existe = $q->fetchColumn();
    $tries++;
    if ($tries > 15) break;
  } while ($existe);
  return $num;
}

// Recolectar datos
$tipoDocumento = $_POST['tipoDocumento'] ?? '';
$documento     = $_POST['documento'] ?? '';

if (!$tipoDocumento || !$documento) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'Faltan campos obligatorios.']);
  exit;
}

// Procesar archivos: firma (prioridad archivo) o firmaCanvas, y opcional imagen_file
$firmaPath = null;
$imagenPath = null;

try {
  if (isset($_FILES['archivoFirma']) && $_FILES['archivoFirma']['error'] === UPLOAD_ERR_OK) {
    $firmaPath = handleUploadedFile($_FILES['archivoFirma'], $uploadDirFirmas, $allowedAllExt, $maxFileSize, 'firma_'.$documento);
  } elseif (isset($_FILES['firmaCanvas']) && $_FILES['firmaCanvas']['error'] === UPLOAD_ERR_OK) {
    $firmaPath = handleCanvasFile($_FILES['firmaCanvas'], $uploadDirFirmas, $maxFileSize, 'firma_'.$documento);
  } else {
    // si no quieres exigir la firma, comenta las 3 líneas siguientes
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Debe adjuntar o dibujar una firma.']);
    exit;
  }

  // imagen auxiliar opcional
  if (isset($_FILES['imagen_file']) && $_FILES['imagen_file']['error'] === UPLOAD_ERR_OK) {
    $imagenPath = handleUploadedFile($_FILES['imagen_file'], $uploadDirImagenes, $allowedImageExt, $maxFileSize, 'imagen_'.$documento);
  }

} catch (Exception $ex) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>$ex->getMessage()]);
  exit;
}

// Insert / update en tabla `matriculas`
try {
  $pdo->beginTransaction();

  // Buscar registro existente por estudiante_id
  $sel = $pdo->prepare("SELECT id, estudiante_id, matricula FROM matriculas WHERE estudiante_id = :id LIMIT 1");
  $sel->execute([':id'=>$documento]);
  $row = $sel->fetch(PDO::FETCH_ASSOC);

  if ($row) {
    // actualizar: mantener matricula
    $sqlUpd = "UPDATE matriculas
               SET fecha_renovacion = NOW(),
                   ruta_firma = :ruta_firma,
                   ruta_imagen = :ruta_imagen
               WHERE estudiante_id = :id";
    $stmtUpd = $pdo->prepare($sqlUpd);
    $stmtUpd->execute([
      ':ruta_firma' => $firmaPath,
      ':ruta_imagen' => $imagenPath,
      ':id' => $documento
    ]);
    $matricula = $row['matricula'];
  } else {
    // insertar: generar matricula única
    $matricula = generarMatriculaUnica($pdo);
    $sqlIns = "INSERT INTO matriculas (estudiante_id, matricula, fecha_renovacion, ruta_firma, ruta_imagen, created_at)
               VALUES (:estudiante_id, :matricula, NOW(), :ruta_firma, :ruta_imagen, NOW())";
    $stmtIns = $pdo->prepare($sqlIns);
    try {
      $stmtIns->execute([
        ':estudiante_id' => $documento,
        ':matricula' => $matricula,
        ':ruta_firma' => $firmaPath,
        ':ruta_imagen' => $imagenPath
      ]);
    } catch (PDOException $e) {
      // si hay duplicate (raro), reintentar una vez
      if (strpos($e->getMessage(), 'Duplicate') !== false || ($e->errorInfo[1] ?? null) == 1062) {
        $matricula = generarMatriculaUnica($pdo);
        $stmtIns->execute([
          ':estudiante_id' => $documento,
          ':matricula' => $matricula,
          ':ruta_firma' => $firmaPath,
          ':ruta_imagen' => $imagenPath
        ]);
      } else {
        throw $e;
      }
    }
  }

  $pdo->commit();

  echo json_encode([
    'ok' => true,
    'matricula' => $matricula,
    'fecha' => date('Y-m-d H:i:s'),
    'ruta_firma' => $firmaPath,
    'ruta_imagen' => $imagenPath
  ]);
  exit;

} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  error_log("Error renovar_matricula.php: " . $e->getMessage());
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Error al procesar la renovación.']);
  exit;
}
