<?php
// api/asistencia_webhook.php
//
// Recibe un POST en JSON desde el Apps Script vinculado al Google Sheet
// cada vez que un docente llena el Google Form de asistencia.
//
// JSON esperado:
// {
//   "token": "el mismo valor que 'webhook_token' en config/asistencia.php",
//   "correo": "docente@correo.com",
//   "estado": "asistio" | "tardanza" | "falta" | "justificado",
//   "hora_entrada": "08:00",   // opcional
//   "hora_salida": "13:00",    // opcional
//   "observaciones": "texto",  // opcional
//   "fecha": "2026-08-26"      // opcional, por defecto: hoy
// }

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Asistencia.php';

$config = require __DIR__ . '/../config/asistencia.php';

function responder(int $code, array $data): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['ok' => false, 'error' => 'Método no permitido']);
}

$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
    responder(400, ['ok' => false, 'error' => 'JSON inválido']);
}

// --- Seguridad: token compartido ---
if (!hash_equals($config['webhook_token'], (string)($body['token'] ?? ''))) {
    responder(401, ['ok' => false, 'error' => 'Token inválido']);
}

// --- Validar estado ---
$estado = $body['estado'] ?? '';
if (!array_key_exists($estado, Asistencia::ESTADOS)) {
    responder(422, ['ok' => false, 'error' => 'Estado no válido']);
}

// --- Validar correo e identificar docente ---
$correo = trim($body['correo'] ?? '');
if (empty($correo)) {
    responder(422, ['ok' => false, 'error' => 'Correo requerido']);
}

$db = getDB();
// Comparación insensible a mayúsculas/espacios: evita fallos por cómo la
// persona escribió su correo a mano en el Form (personal o institucional,
// da igual, mientras coincida con lo guardado en docentes.correo).
$stmt = $db->prepare(
    'SELECT id_docente FROM docentes
     WHERE LOWER(TRIM(correo)) = LOWER(TRIM(:correo)) AND estado = 1 LIMIT 1'
);
$stmt->execute([':correo' => $correo]);
$idDocente = $stmt->fetchColumn();

if (!$idDocente) {
    responder(404, ['ok' => false, 'error' => 'No se encontró un docente activo con ese correo']);
}

// --- Guardar / actualizar asistencia ---
try {
    $ok = Asistencia::registrarHoy((int)$idDocente, [
        'fecha'          => $body['fecha'] ?? date('Y-m-d'),
        'hora_entrada'   => $body['hora_entrada']  ?: null,
        'hora_salida'    => $body['hora_salida']   ?: null,
        'estado'         => $estado,
        'observaciones'  => $body['observaciones'] ?? '',
        'registrado_por' => 'formulario',
    ]);

    responder(200, ['ok' => $ok]);
} catch (PDOException $e) {
    responder(500, ['ok' => false, 'error' => 'Error al guardar en base de datos']);
}