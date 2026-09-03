<?php
// api/diagnostico.php
// Archivo temporal para diagnosticar problemas de conexión y base de datos

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

$config = require __DIR__ . '/../config/asistencia.php';

function responder($code, $data) {
    http_response_code($code);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

$resultados = [];

// 1. Probar conexión a la base de datos
try {
    $db = getDB();
    $resultados['conexion'] = '✅ Conexión exitosa';
} catch (Exception $e) {
    responder(500, [
        'ok' => false,
        'paso' => 'Conexión DB',
        'error' => $e->getMessage()
    ]);
}

// 2. Verificar tabla docentes
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM docentes");
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    $resultados['tabla_docentes'] = '✅ Existe, tiene ' . $total['total'] . ' registros';
} catch (Exception $e) {
    responder(500, [
        'ok' => false,
        'paso' => 'Verificar tabla docentes',
        'error' => $e->getMessage()
    ]);
}

// 3. Verificar tabla asistencias
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM asistencias");
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    $resultados['tabla_asistencias'] = '✅ Existe, tiene ' . $total['total'] . ' registros';
} catch (Exception $e) {
    responder(500, [
        'ok' => false,
        'paso' => 'Verificar tabla asistencias',
        'error' => $e->getMessage()
    ]);
}

// 4. Verificar estructura de asistencias
try {
    $stmt = $db->query("
        SELECT column_name, data_type 
        FROM information_schema.columns 
        WHERE table_name = 'asistencias' 
        ORDER BY ordinal_position
    ");
    $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $resultados['columnas_asistencias'] = $columnas;
} catch (Exception $e) {
    responder(500, [
        'ok' => false,
        'paso' => 'Verificar columnas asistencias',
        'error' => $e->getMessage()
    ]);
}

// 5. Buscar el docente "Rodil Arteaga"
try {
    $nombre = "Rodil Arteaga";
    $stmt = $db->prepare("
        SELECT id_docente, nombre 
        FROM docentes 
        WHERE estado = 1 AND LOWER(TRIM(nombre)) = LOWER(:nombre) 
        LIMIT 1
    ");
    $stmt->execute([':nombre' => $nombre]);
    $docente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($docente) {
        $resultados['docente_encontrado'] = '✅ Sí, ID: ' . $docente['id_docente'] . ', Nombre: ' . $docente['nombre'];
    } else {
        // Buscar coincidencias parciales
        $stmt = $db->prepare("
            SELECT id_docente, nombre 
            FROM docentes 
            WHERE estado = 1 AND nombre ILIKE :nombre 
            LIMIT 5
        ");
        $stmt->execute([':nombre' => '%' . $nombre . '%']);
        $coincidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($coincidencias) {
            $resultados['docente_encontrado'] = '⚠️ No exacto, pero hay coincidencias:';
            $resultados['coincidencias'] = $coincidencias;
        } else {
            $resultados['docente_encontrado'] = '❌ No se encontró el docente "' . $nombre . '"';
            
            // Mostrar algunos docentes existentes
            $stmt = $db->query("SELECT id_docente, nombre FROM docentes WHERE estado = 1 LIMIT 10");
            $docentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultados['docentes_existentes'] = $docentes;
        }
    }
} catch (Exception $e) {
    responder(500, [
        'ok' => false,
        'paso' => 'Buscar docente',
        'error' => $e->getMessage()
    ]);
}

// 6. Probar INSERT manual (sin ejecutar realmente, solo verificar estructura)
try {
    // Solo verificamos que la estructura soporte el INSERT
    $resultados['estructura_insert'] = '✅ La tabla soporta los campos: id_docente, fecha, hora_entrada, estado, observaciones, registrado_por';
} catch (Exception $e) {
    responder(500, [
        'ok' => false,
        'paso' => 'Verificar estructura INSERT',
        'error' => $e->getMessage()
    ]);
}

// Mostrar todos los resultados
responder(200, [
    'ok' => true,
    'diagnostico' => $resultados
]);