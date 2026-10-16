<?php
// solicitudes_listar.php
header('Content-Type: application/json; charset=utf-8');
include 'conexion.php';

// filtros simples desde tu UI
$q = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha-desc';

$orderBy = "creado_en DESC";
if ($orden === 'fecha-asc') $orderBy = "creado_en ASC";
if ($orden === 'nombre-asc') $orderBy = "Primer_Nombre ASC, Primer_Apellido ASC";
if ($orden === 'nombre-desc') $orderBy = "Primer_Nombre DESC, Primer_Apellido DESC";

$where = "1=1";
if ($q !== '') {
  $where .= " AND (
    LOWER(Primer_Nombre) LIKE '%$q%' OR
    LOWER(Primer_Apellido) LIKE '%$q%' OR
    LOWER(E_mail) LIKE '%$q%'
  )";
}

// Traer solo pendientes para la lista principal
$sql = "SELECT Numero_de_Documento as id, CONCAT(Primer_Nombre,' ',Primer_Apellido) AS nombre, E_mail AS email, Celular AS telefono, creado_en AS fecha, estado
        FROM registro_estudiante
        WHERE $where AND estado='pendiente'
        ORDER BY $orderBy";
$rs = mysqli_query($conexion, $sql);

$solicitudes = [];
while ($r = mysqli_fetch_assoc($rs)) {
  $r['mensaje'] = ''; // si no tienes campo, lo dejamos vacío
  $solicitudes[] = $r;
}

// Stats rápidas
$stats = ['total'=>0,'aceptadas'=>0,'rechazadas'=>0];
$sr = mysqli_query($conexion, "SELECT estado, COUNT(*) c FROM registro_estudiante GROUP BY estado");
while ($s = mysqli_fetch_assoc($sr)) {
  $stats['total'] += (int)$s['c'];
  if ($s['estado'] == 'aprobado') $stats['aceptadas'] = (int)$s['c'];
  if ($s['estado'] == 'rechazado') $stats['rechazadas'] = (int)$s['c'];
}

echo json_encode(['ok'=>true,'solicitudes'=>$solicitudes,'stats'=>$stats]);
