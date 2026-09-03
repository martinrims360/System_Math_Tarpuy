<?php
// api/asistencia_webhook.php

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

// --- Seguridad: token ---
if (!hash_equals($config['webhook_token'], (string)($body['token'] ?? ''))) {
    responder(401, ['ok' => false, 'error' => 'Token inválido']);
}

// --- Validar acción ---
$accion = $body['accion'] ?? '';
if (!in_array($accion, ['Hora de entrada', 'Hora de salida'], true)) {
    responder(422, ['ok' => false, 'error' => 'Acción no válida']);
}

// --- Validar nombre ---
$nombreForm = trim($body['nombre'] ?? '');
if (empty($nombreForm)) {
    responder(422, ['ok' => false, 'error' => 'Nombre requerido']);
}

$db = getDB();

// --- BUSCAR DOCENTE (VERSIÓN MEJORADA) ---
function buscarDocenteId(PDO $db, string $nombreForm): ?int {
    // Eliminar prefijos comunes
    $nombreLimpio = preg_replace('/^\s*(profesor|profesora|prof\.?|docente|teacher)\s+/i', '', $nombreForm);
    $nombreLimpio = trim($nombreLimpio);
    
    // 1) Coincidencia exacta (ignorando mayúsculas/minúsculas)
    $stmt = $db->prepare(
        'SELECT id_docente FROM docentes WHERE LOWER(TRIM(nombre)) = LOWER(:n) LIMIT 1'
    );
    $stmt->execute([':n' => $nombreLimpio]);
    $id = $stmt->fetchColumn();
    if ($id) return (int) $id;
    
    // 2) Coincidencia exacta con el nombre original (sin limpiar)
    $stmt = $db->prepare(
        'SELECT id_docente FROM docentes WHERE LOWER(TRIM(nombre)) = LOWER(:n) LIMIT 1'
    );
    $stmt->execute([':n' => $nombreForm]);
    $id = $stmt->fetchColumn();
    if ($id) return (int) $id;
    
    // 3) Coincidencia parcial (el nombre del Form está contenido en el nombre de la BD)
    $stmt = $db->prepare(
        'SELECT id_docente FROM docentes WHERE LOWER(nombre) LIKE LOWER(:n) LIMIT 1'
    );
    $stmt->execute([':n' => '%' . $nombreLimpio . '%']);
    $id = $stmt->fetchColumn();
    if ($id) return (int) $id;
    
    // 4) Coincidencia por primera palabra (nombre de pila)
    $primeraPalabra = strtok($nombreLimpio, ' ');
    if ($primeraPalabra && strlen($primeraPalabra) >= 3) {
        $stmt = $db->prepare(
            'SELECT id_docente FROM docentes WHERE LOWER(nombre) LIKE LOWER(:n)'
        );
        $stmt->execute([':n' => '%' . $primeraPalabra . '%']);
        $coincidencias = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (count($coincidencias) === 1) {
            return (int) $coincidencias[0];
        }
    }
    
    // 5) Coincidencia por cualquiera de las palabras del nombre
    $partes = explode(' ', $nombreLimpio);
    foreach ($partes as $parte) {
        if (strlen($parte) >= 3) {
            $stmt = $db->prepare(
                'SELECT id_docente FROM docentes WHERE LOWER(nombre) LIKE LOWER(:n)'
            );
            $stmt->execute([':n' => '%' . $parte . '%']);
            $coincidencias = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (count($coincidencias) === 1) {
                return (int) $coincidencias[0];
            }
        }
    }
    
    return null;
}

// --- Buscar el docente ---
$idDocente = buscarDocenteId($db, $nombreForm);

if (!$idDocente) {
    error_log("[asistencia_webhook] Docente no encontrado: '{$nombreForm}'");
    responder(404, ['ok' => false, 'error' => "No se encontró un docente que coincida con '{$nombreForm}'"]);
}

// --- Obtener datos ---
$fecha = $body['fecha'] ?? date('Y-m-d');
$hora  = $body['hora']  ?? date('H:i:s');
$observaciones = $body['observaciones'] ?? '';
$ahora = date('Y-m-d H:i:s'); // Para created_at

try {
    // Verificar si ya existe un registro para este docente y fecha
    $stmt = $db->prepare('SELECT id_asistencia FROM asistencias WHERE id_docente = :id_docente AND fecha = :fecha');
    $stmt->execute([':id_docente' => $idDocente, ':fecha' => $fecha]);
    $existe = $stmt->fetchColumn();

    if ($accion === 'Hora de entrada') {
        // Calcular estado
        $horaLimite = $config['hora_limite_tardanza'] ?? '08:15:00';
        $estado = ($hora <= $horaLimite) ? 'asistio' : 'tardanza';

        if ($existe) {
            $stmt = $db->prepare('
                UPDATE asistencias 
                SET hora_entrada = :hora, 
                    estado = :estado,
                    observaciones = COALESCE(NULLIF(:observaciones, \'\'), observaciones),
                    created_at = :created_at
                WHERE id_docente = :id_docente AND fecha = :fecha
            ');
            $stmt->execute([
                ':id_docente'    => $idDocente,
                ':fecha'         => $fecha,
                ':hora'          => $hora,
                ':estado'        => $estado,
                ':observaciones' => $observaciones,
                ':created_at'    => $ahora
            ]);
        } else {
            $stmt = $db->prepare('
                INSERT INTO asistencias (id_docente, fecha, hora_entrada, estado, observaciones, registrado_por, created_at)
                VALUES (:id_docente, :fecha, :hora, :estado, :observaciones, :registrado_por, :created_at)
            ');
            $stmt->execute([
                ':id_docente'    => $idDocente,
                ':fecha'         => $fecha,
                ':hora'          => $hora,
                ':estado'        => $estado,
                ':observaciones' => $observaciones,
                ':registrado_por' => 'formulario',
                ':created_at'    => $ahora
            ]);
        }
        
    } else {
        // "Hora de salida"
        if ($existe) {
            $stmt = $db->prepare('
                UPDATE asistencias 
                SET hora_salida = :hora,
                    observaciones = COALESCE(NULLIF(:observaciones, \'\'), observaciones),
                    created_at = :created_at
                WHERE id_docente = :id_docente AND fecha = :fecha
            ');
            $stmt->execute([
                ':id_docente'    => $idDocente,
                ':fecha'         => $fecha,
                ':hora'          => $hora,
                ':observaciones' => $observaciones,
                ':created_at'    => $ahora
            ]);
        } else {
            $stmt = $db->prepare('
                INSERT INTO asistencias (id_docente, fecha, hora_salida, estado, observaciones, registrado_por, created_at)
                VALUES (:id_docente, :fecha, :hora, :estado, :observaciones, :registrado_por, :created_at)
            ');
            $stmt->execute([
                ':id_docente'    => $idDocente,
                ':fecha'         => $fecha,
                ':hora'          => $hora,
                ':estado'        => 'asistio',
                ':observaciones' => $observaciones,
                ':registrado_por' => 'formulario',
                ':created_at'    => $ahora
            ]);
        }
    }

    responder(200, ['ok' => true]);
    
} catch (PDOException $e) {
    error_log("[asistencia_webhook] Error SQL: " . $e->getMessage());
    responder(500, [
        'ok' => false, 
        'error' => 'Error al guardar en base de datos',
        'sql_error' => $e->getMessage()
    ]);
}