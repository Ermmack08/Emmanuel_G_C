<?php
/**
 * Archivo de conexión a la base de datos ISEA
 * Autor: [Tu nombre o ISEA]
 * Descripción: Establece conexión segura con MySQL utilizando mysqli
 */

 // ⚙️ Configuración de la base de datos
$host = "localhost";
$user = "root"; // usuario por defecto en XAMPP
$pass = "";     // contraseña vacía por defecto
$dbname = "isea"; // nombre de tu base de datos

// 🧩 Crear conexión
$conn = new mysqli($host, $user, $pass, $dbname);

// 🛑 Verificar conexión
if ($conn->connect_error) {
    die("<h3 style='color:red;'>❌ Error en la conexión a la base de datos:</h3> " . $conn->connect_error);
}

// ✅ Forzar uso de UTF-8 (importante para tildes, ñ, etc.)
$conn->set_charset("utf8mb4");

// 💡 Mensaje opcional (desactiva en producción)
# echo "<p style='color:green;'>✅ Conexión establecida correctamente con la base de datos '$dbname'.</p>";

/**
 * Función para acceder fácilmente a la conexión
 * (por si en el futuro deseas incluir funciones externas)
 */
function obtenerConexion() {
    global $conn;
    return $conn;
}

// Puedes probar la conexión si lo ejecutas directamente:
// if (basename($_SERVER['PHP_SELF']) === 'conexion.php') {
//     echo "<h4>Conexión exitosa ✅</h4>";
// }
?>
