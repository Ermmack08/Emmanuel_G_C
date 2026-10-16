<?php

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';

// Leer datos JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (
    !$data ||
    empty($data['correo']) ||
    empty($data['nombre']) ||
    empty($data['accion'])
) {
    echo json_encode(['ok' => false, 'error' => 'Faltan datos: correo, nombre o acción.']);
    exit;
}

$correo = trim($data['correo']);
$nombre = trim($data['nombre']);
$accion = strtolower(trim($data['accion']));


$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'g.matricula.isea@gmail.com'; 
    $mail->Password   = 'pfil ddfx wwgl kaly'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('g.matricula.isea@gmail.com', 'Sistema de Matrículas ISEA');
    $mail->addAddress($correo, $nombre);

    $mail->isHTML(true);

    if ($accion === 'aceptar') {
        $mail->Subject = 'Solicitud de Matrícula Aprobada - ISEA';
        $mail->Body = "
        <h2 style='color:#4a1763;'>¡Felicidades, $nombre!</h2>
        <p>Felicidaddes tu solicitud de matrícula ha sido <b>aprobada</b> exitosamente.</p>
        <p>Puedes ingresar al portal de estudiantes en el siguiente enlace:</p>
        <p><a href='https://isea.edu.co/portal/login.php' style='color:#4a1763; font-weight:bold;'>Ingresar al portal</a></p>
        <p>¡Bienvenido a la comunidad ISEA! 🎓</p>
        <hr>
        <p style='font-size:12px;color:#555;'>Sistema de Matrículas ISEA</p>
        ";
    } elseif ($accion === 'rechazar') {
        $mail->Subject = 'Resultado de tu Solicitud - ISEA';
        $mail->Body = "
        <h2 style='color:#e74c3c;'>Hola, $nombre</h2>
        <p>Lamentamos informarte que tu solicitud de matrícula ha sido <b>rechazada</b>.</p>
        <p>Por favor comunícate con la institución para más detalles o vuelve a aplicar cuando esté disponible el próximo proceso.</p>
        <hr>
        <p style='font-size:12px;color:#555;'>Sistema de Matrículas ISEA</p>
        ";
    } else {
        echo json_encode(['ok' => false, 'error' => 'Acción no reconocida (usar aceptar o rechazar).']);
        exit;
    }

    $mail->send();
    echo json_encode(['ok' => true, 'mensaje' => 'Correo enviado correctamente a ' . $correo]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => 'Error al enviar: ' . $mail->ErrorInfo]);
}
?>
