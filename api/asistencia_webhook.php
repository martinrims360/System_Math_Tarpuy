<?php
// api/asistencia_webhook.php
//
// Recibe un POST en JSON desde el Apps Script vinculado al Google Sheet
// cada vez que un docente llena el Google Form de asistencia.
//
// A DIFERENCIA de la versión anterior: ya NO se envía "estado" desde el
// Form. Este endpoint lo calcula solo, comparando la hora de entrada
// contra 'hora_limite_tardanza' definida en config/asistencia.php.
//
// El docente se identifica por NOMBRE (texto libre tal como lo escribe
// en el Form), no por correo. Se hace un emparejamiento por aproximación
// contra docentes.nombre (ver buscarDocenteId más abajo).
//
// JSON esperado (lo que debe enviar el Apps Script -> ver apps_script_trigger.gs):
// {
//   "token":         "el mismo valor que 'webhook_token' en config/asistencia.php",
//   "nombre":        "Rodil Arteaga",  // texto tal cual del campo "Profesor(a) :" del Form
//   "accion":        "Hora de entrada" | "Hora de salida",
//   "fecha":         "2026-08-17",   // de la Marca temporal
//   "hora":          "14:56:00",     // de la Marca temporal
//   "salon":         "Circulo 3",    // opcional
//   "observaciones": "no tenia wifi" // opcional, Columna 1 del Sheet
// }

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

$config = require __DIR__ . '/../config/asistencia.php';
// Asegúrate de tener en config/asistencia.php, además de 'webhook_token':
//   'hora_limite_tardanza' => '08:15:00',

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

// --- Validar acción ---
$accion = $body['accion'] ?? '';
if (!in_array($accion, ['Hora de entrada', 'Hora de salida'], true)) {
    responder(422, ['ok' => false, 'error' => 'Acción no válida']);
}

// --- Validar nombre e identificar docente por aproximación ---
$nombreForm = trim($body['nombre'] ?? '');
if (empty($nombreForm)) {
    responder(422, ['ok' => false, 'error' => 'Nombre requerido']);
}

$db = getDB();
$idDocente = buscarDocenteId($db, $nombreForm);

if (!$idDocente) {
    // No se encontró ningún docente parecido a este nombre. Se registra igual
    // en un log de "sin emparejar" para que lo revises, en vez de perder el dato.
    error_log("[asistencia_webhook] Sin emparejar: '{$nombreForm}'");
    responder(404, ['ok' => false, 'error' => "No se encontró un docente que coincida con '{$nombreForm}'"]);
}

/**
 * Limpia el texto del profesor tal como viene del Form
 * (quita el prefijo "Profesor"/"Profesora" y espacios extra)
 */
function normalizarNombre(string $nombreForm): string
{
    $limpio = preg_replace('/^\s*(profesor|profesora|prof\.?)\s+/i', '', $nombreForm);
    return trim($limpio);
}

/**
 * Intenta emparejar el nombre del Form con un docente existente en la tabla.
 * Devuelve el id_docente o null si no encuentra ninguna coincidencia razonable.
 */
function buscarDocenteId(PDO $db, string $nombreForm): ?int
{
    $nombreLimpio = normalizarNombre($nombreForm);

    // 1) Coincidencia exacta (ignorando mayúsculas/espacios)
    $stmt = $db->prepare(
        'SELECT id_docente FROM docentes WHERE estado = 1 AND LOWER(TRIM(nombre)) = LOWER(:n) LIMIT 1'
    );
    $stmt->execute([':n' => $nombreLimpio]);
    $id = $stmt->fetchColumn();
    if ($id) return (int) $id;

    // 2) Coincidencia parcial: el nombre del Form aparece dentro del nombre completo
    $stmt = $db->prepare(
        'SELECT id_docente FROM docentes WHERE estado = 1 AND nombre ILIKE :n LIMIT 1'
    );
    $stmt->execute([':n' => '%' . $nombreLimpio . '%']);
    $id = $stmt->fetchColumn();
    if ($id) return (int) $id;

    // 3) Coincidencia por la primera palabra (nombre de pila), solo si es única
    $primeraPalabra = strtok($nombreLimpio, ' ');
    if ($primeraPalabra && mb_strlen($primeraPalabra) >= 3) {
        $stmt = $db->prepare(
            'SELECT id_docente FROM docentes WHERE estado = 1 AND nombre ILIKE :n'
        );
        $stmt->execute([':n' => '%' . $primeraPalabra . '%']);
        $coincidencias = $stmt->fetchAll(PDO::FETCH_COLUMN);
        // Solo la aceptamos si hay UNA sola coincidencia posible (evita confundir
        // a dos docentes que comparten primer nombre)
        if (count($coincidencias) === 1) {
            return (int) $coincidencias[0];
        }
    }

    return null;
}

$fecha = $body['fecha'] ?? date('Y-m-d');
$hora  = $body['hora']  ?? date('H:i:s');
$salon = $body['salon'] ?? null;
$observaciones = $body['observaciones'] ?? '';

try {
    if ($accion === 'Hora de entrada') {
        // Calcula asistio vs tardanza comparando contra la hora límite configurada
        $horaLimite = $config['hora_limite_tardanza'] ?? '08:15:00';
        $estado = ($hora <= $horaLimite) ? 'asistio' : 'tardanza';

        // Upsert: si ya existe un registro de HOY para este docente, actualiza;
        // si no existe, lo crea. Requiere UNIQUE(id_docente, fecha) en la tabla asistencias.
        $stmt = $db->prepare('
            INSERT INTO asistencias (id_docente, fecha, hora_entrada, estado, salon, observaciones, registrado_por)
            VALUES (:id_docente, :fecha, :hora, :estado, :salon, :observaciones, \'formulario\')
            ON CONFLICT (id_docente, fecha) DO UPDATE SET
                hora_entrada   = EXCLUDED.hora_entrada,
                estado         = EXCLUDED.estado,
                salon          = COALESCE(EXCLUDED.salon, asistencias.salon),
                observaciones  = COALESCE(NULLIF(EXCLUDED.observaciones, \'\'), asistencias.observaciones)
        ');
        $stmt->execute([
            ':id_docente'    => $idDocente,
            ':fecha'         => $fecha,
            ':hora'          => $hora,
            ':estado'        => $estado,
            ':salon'         => $salon,
            ':observaciones' => $observaciones,
        ]);
    } else {
        // "Hora de salida": solo actualiza hora_salida del registro de hoy.
        // Si por algún motivo no existe fila de entrada (caso raro), la crea
        // con estado 'asistio' por defecto para que quede visible y se pueda corregir.
        $stmt = $db->prepare('
            INSERT INTO asistencias (id_docente, fecha, hora_salida, estado, salon, observaciones, registrado_por)
            VALUES (:id_docente, :fecha, :hora, \'asistio\', :salon, :observaciones, \'formulario\')
            ON CONFLICT (id_docente, fecha) DO UPDATE SET
                hora_salida    = EXCLUDED.hora_salida,
                salon          = COALESCE(asistencias.salon, EXCLUDED.salon),
                observaciones  = COALESCE(NULLIF(EXCLUDED.observaciones, \'\'), asistencias.observaciones)
        ');
        $stmt->execute([
            ':id_docente'    => $idDocente,
            ':fecha'         => $fecha,
            ':hora'          => $hora,
            ':salon'         => $salon,
            ':observaciones' => $observaciones,
        ]);
    }

    responder(200, ['ok' => true]);
} catch (PDOException $e) {
    responder(500, ['ok' => false, 'error' => 'Error al guardar en base de datos']);
}