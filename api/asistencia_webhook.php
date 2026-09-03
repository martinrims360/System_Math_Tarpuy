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

// --- BUSCAR DOCENTE (CON LOGS) ---
function buscarDocenteId(PDO $db, string $nombreForm): ?int {
    // Eliminar prefijos comunes
    $nombreLimpio = preg_replace('/^\s*(profesor|profesora|prof\.?|docente|teacher)\s+/i', '', $nombreForm);
    $nombreLimpio = trim($nombreLimpio);
    
    error_log("[asistencia_webhook] Buscando docente: '{$nombreForm}' → limpio: '{$nombreLimpio}'");
    
    // 1) Coincidencia exacta (ignorando mayúsculas/minúsculas)
    $stmt = $db->prepare(
        'SELECT id_docente, nombre FROM docentes WHERE LOWER(TRIM(nombre)) = LOWER(:n) LIMIT 1'
    );
    $stmt->execute([':n' => $nombreLimpio]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        error_log("[asistencia_webhook] ✅ Coincidencia exacta: ID={$resultado['id_docente']}, Nombre='{$resultado['nombre']}'");
        return (int) $resultado['id_docente'];
    }
    
    // 2) Coincidencia exacta con el nombre original (sin limpiar)
    $stmt = $db->prepare(
        'SELECT id_docente, nombre FROM docentes WHERE LOWER(TRIM(nombre)) = LOWER(:n) LIMIT 1'
    );
    $stmt->execute([':n' => $nombreForm]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        error_log("[asistencia_webhook] ✅ Coincidencia exacta (original): ID={$resultado['id_docente']}, Nombre='{$resultado['nombre']}'");
        return (int) $resultado['id_docente'];
    }
    
    // 3) Coincidencia parcial (el nombre del Form está contenido en el nombre de la BD)
    $stmt = $db->prepare(
        'SELECT id_docente, nombre FROM docentes WHERE LOWER(nombre) LIKE LOWER(:n) LIMIT 1'
    );
    $stmt->execute([':n' => '%' . $nombreLimpio . '%']);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        error_log("[asistencia_webhook] ✅ Coincidencia parcial: ID={$resultado['id_docente']}, Nombre='{$resultado['nombre']}'");
        return (int) $resultado['id_docente'];
    }
    
    error_log("[asistencia_webhook] ❌ No se encontró ningún docente para: '{$nombreForm}'");
    return null;
}

// --- Buscar el docente ---
$idDocente = buscarDocenteId($db, $nombreForm);

if (!$idDocente) {
    responder(404, [
        'ok' => false, 
        'error' => "No se encontró un docente que coincida con '{$nombreForm}'",
        'nombre_buscado' => $nombreForm
    ]);
}

// --- Obtener datos ---
$fecha = $body['fecha'] ?? date('Y-m-d');
$hora  = $body['hora']  ?? date('H:i:s');
$observaciones = $body['observaciones'] ?? '';

error_log("[asistencia_webhook] Procesando: id_docente={$idDocente}, fecha={$fecha}, accion={$accion}");

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
                    observaciones = COALESCE(NULLIF(:observaciones, \'\'), observaciones)
                WHERE id_docente = :id_docente AND fecha = :fecha
            ');
            $stmt->execute([
                ':id_docente'    => $idDocente,
                ':fecha'         => $fecha,
                ':hora'          => $hora,
                ':estado'        => $estado,
                ':observaciones' => $observaciones
            ]);
            error_log("[asistencia_webhook] ✅ Registro ACTUALIZADO (entrada) para id_docente={$idDocente}");
        } else {
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
            error_log("[asistencia_webhook] ✅ Registro INSERTADO (entrada) para id_docente={$idDocente}");
        }
        
    } else {
        // "Hora de salida"
        if ($existe) {
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
                ':observaciones' => $observaciones
            ]);
            error_log("[asistencia_webhook] ✅ Registro ACTUALIZADO (salida) para id_docente={$idDocente}");
        } else {
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
            error_log("[asistencia_webhook] ✅ Registro INSERTADO (salida) para id_docente={$idDocente}");
        }
    }

    responder(200, ['ok' => true]);
    
} catch (PDOException $e) {
    error_log("[asistencia_webhook] ❌ ERROR SQL: " . $e->getMessage());
    responder(500, [
        'ok' => false, 
        'error' => 'Error al guardar en base de datos',
        'sql_error' => $e->getMessage(),
        'id_docente' => $idDocente,
        'fecha' => $fecha
    ]);
}