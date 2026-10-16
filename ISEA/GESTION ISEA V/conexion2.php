<?php
// conexion.php
// Conexión PDO (recomendado) + Compatibilidad con mysqli (opcional)
// Ajusta host/db/user/password si son distintas

$DB_HOST = 'localhost';
$DB_NAME = 'isea';
$DB_USER = 'root';
$DB_PASS = '';
$DB_CHARSET = 'utf8mb4';

// 1) Crear PDO ($pdo) - usado por renovar_matricula.php y el código PDO
$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";
$pdo_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,            // lanzar excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch por defecto como array asociativo
    PDO::ATTR_EMULATE_PREPARES => false,                    // usar prepares nativos cuando sea posible
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $pdo_options);
    // opcional: definir zona horaria o modo SQL si lo necesitas
    // $pdo->exec("SET time_zone = '+00:00'");
} catch (PDOException $e) {
    // En entorno de desarrollo puedes mostrar el error. En producción, registra y muestra mensaje genérico.
    error_log("Error al conectar con PDO: " . $e->getMessage());
    // Si quieres que el script muera aquí:
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error de conexión a la base de datos (PDO).']);
    exit;
}

// 2) (Opcional) Mantener $conexion mysqli para compatibilidad con código existente
$conexion = @mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conexion) {
    // No detenemos la ejecución si mysqli no funciona, porque preferimos PDO.
    error_log("Warning: no se pudo conectar por mysqli: " . mysqli_connect_error());
} else {
    // Opcional: establecer charset
    mysqli_set_charset($conexion, $DB_CHARSET);
}

/*
Uso:
- En archivos que esperan PDO: require 'conexion.php'; luego usar $pdo.
- Si tienes scripts antiguos que usan mysqli: siguen pudiendo usar $conexion (si no es null).
*/

// Pequeña prueba (descomenta para test manual, NO dejar en producción)
// try {
//     $stmt = $pdo->query("SELECT NOW() AS ahora");
//     $row = $stmt->fetch();
//     var_dump($row);
// } catch (Exception $e) {
//     var_dump($e->getMessage());
// }
