<?php
// api/asistencia_webhook.php
//
// Recibe un POST en JSON desde el Apps Script vinculado al Google Sheet
// cada vez que un docente llena el Google Form de asistencia.
//
// El docente se identifica por NOMBRE (texto libre tal como lo escribe
// en el Form), no por correo. Se hace un emparejamiento por aproximación
// contra docentes.nombre.
//
// JSON esperado (lo que debe enviar el Apps Script):
// {
//   "token":         "el mismo valor que 'webhook_token' en config/asistencia.php",
//   "nombre":        "Rodil Arteaga",
//   "accion":        "Hora de entrada" | "Hora de salida",
//   "fecha":         "2026-08-17",
//   "hora":          "14:56:00",
//   "observaciones": "no tenia wifi" // opcional
// }

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

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

// --- Validar acción ---
$accion = $body['accion'] ?? '';
if (!in_array($accion, ['Hora de entrada', 'Hora de salida'], true)) {
    responder(422, ['ok' => false, 'error' => 'Acción no válida']);
}

// --- Validar nombre e identificar docente ---
$nombreForm = trim($body['nombre'] ?? '');
if (empty($nombreForm)) {
    responder(422, ['ok' => false, 'error' => 'Nombre requerido']);
}

$db = getDB();
$idDocente = buscarDocenteId($db, $nombreForm);

if (!$idDocente) {
    error_log("[asistencia_webhook] Sin emparejar: '{$nombreForm}'");
    responder(404, ['ok' => false, 'error' => "No se encontró un docente que coincida con '{$nombreForm}'"]);
}

/**
 * Limpia el texto del profesor tal como viene del Form
 */
function normalizarNombre(string $nombreForm): string
{
    $limpio = preg_replace('/^\s*(profesor|profesora|prof\.?)\s+/i', '', $nombreForm);
    return trim($limpio);
}

/**
 * Intenta emparejar el nombre del Form con un docente existente en la tabla.
 */
function buscarDocenteId(PDO $db, string $nombreForm): ?int
{
    $nombreLimpio = normalizarNombre($nombreForm);

    // 1) Coincidencia exacta
    $stmt = $db->prepare(
        'SELECT id_docente FROM docentes WHERE estado = 1 AND LOWER(TRIM(nombre)) = LOWER(:n) LIMIT 1'
    );
    $stmt->execute([':n' => $nombreLimpio]);
    $id = $stmt->fetchColumn();
    if ($id) return (int) $id;

    // 2) Coincidencia parcial
    $stmt = $db->prepare(
        'SELECT id_docente FROM docentes WHERE estado = 1 AND nombre ILIKE :n LIMIT 1'
    );
    $stmt->execute([':n' => '%' . $nombreLimpio . '%']);
    $id = $stmt->fetchColumn();
    if ($id) return (int) $id;

    // 3) Coincidencia por primera palabra
    $primeraPalabra = strtok($nombreLimpio, ' ');
    if ($primeraPalabra && mb_strlen($primeraPalabra) >= 3) {
        $stmt = $db->prepare(
            'SELECT id_docente FROM docentes WHERE estado = 1 AND nombre ILIKE :n'
        );
        $stmt->execute([':n' => '%' . $primeraPalabra . '%']);
        $coincidencias = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (count($coincidencias) === 1) {
            return (int) $coincidencias[0];
        }
    }

    return null;
}

// --- Obtener datos ---
$fecha = $body['fecha'] ?? date('Y-m-d');
$hora  = $body['hora']  ?? date('H:i:s');
$observaciones = $body['observaciones'] ?? '';

try {
    // Verificar si ya existe un registro para este docente y fecha
    $stmt = $db->prepare('SELECT id_asistencia FROM asistencias WHERE id_docente = :id_docente AND fecha = :fecha');
    $stmt->execute([':id_docente' => $idDocente, ':fecha' => $fecha]);
    $existe = $stmt->fetchColumn();

    if ($accion === 'Hora de entrada') {
        // Calcular estado (asistio vs tardanza)
        $horaLimite = $config['hora_limite_tardanza'] ?? '08:15:00';
        $estado = ($hora <= $horaLimite) ? 'asistio' : 'tardanza';

        if ($existe) {
            // Actualizar registro existente
            $stmt = $db->prepare('
                UPDATE asistencias 
                SET hora_entrada = :hora, 
                    estado = :estado,
                    observaciones = COALESCE(NULLIF(:observaciones, \'\'), observaciones)
                WHERE id_docente = :id_docente AND fecha = :fecha
            ');
            $stmt->execute([
                ':id_docente'    => $idDocente,
                ':fecha'         => $fecha,
                ':hora'          => $hora,
                ':estado'        => $estado,
                ':observaciones' => $observaciones,
            ]);
        } else {
            // Insertar nuevo registro
            $stmt = $db->prepare('
                INSERT INTO asistencias (id_docente, fecha, hora_entrada, estado, observaciones, registrado_por)
                VALUES (:id_docente, :fecha, :hora, :estado, :observaciones, :registrado_por)
            ');
            $stmt->execute([
                ':id_docente'    => $idDocente,
                ':fecha'         => $fecha,
                ':hora'          => $hora,
                ':estado'        => $estado,
                ':observaciones' => $observaciones,
                ':registrado_por' => 'formulario'
            ]);
        }
        
    } else {
        // "Hora de salida"
        if ($existe) {
            // Actualizar registro existente
            $stmt = $db->prepare('
                UPDATE asistencias 
                SET hora_salida = :hora,
                    observaciones = COALESCE(NULLIF(:observaciones, \'\'), observaciones)
                WHERE id_docente = :id_docente AND fecha = :fecha
            ');
            $stmt->execute([
                ':id_docente'    => $idDocente,
                ':fecha'         => $fecha,
                ':hora'          => $hora,
                ':observaciones' => $observaciones,
            ]);
        } else {
            // Insertar nuevo registro (solo salida, estado por defecto 'asistio')
            $stmt = $db->prepare('
                INSERT INTO asistencias (id_docente, fecha, hora_salida, estado, observaciones, registrado_por)
                VALUES (:id_docente, :fecha, :hora, :estado, :observaciones, :registrado_por)
            ');
            $stmt->execute([
                ':id_docente'    => $idDocente,
                ':fecha'         => $fecha,
                ':hora'          => $hora,
                ':estado'        => 'asistio',
                ':observaciones' => $observaciones,
                ':registrado_por' => 'formulario'
            ]);
        }
    }

    responder(200, ['ok' => true]);
    
} catch (PDOException $e) {
    // DEVOLVER EL ERROR REAL PARA DEPURACIÓN
    $errorDetails = [
        'ok' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
        'id_docente' => $idDocente,
        'fecha' => $fecha,
        'accion' => $accion
    ];
    
    error_log("[asistencia_webhook] Error SQL: " . $e->getMessage());
    error_log("[asistencia_webhook] Detalles: " . json_encode($errorDetails));
    
    responder(500, $errorDetails);
}