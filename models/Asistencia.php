<?php
// models/Asistencia.php

require_once __DIR__ . '/../config/database.php';

class Asistencia {

    const ESTADOS = [
        'asistio'     => ['label'=>'Asistió',     'color'=>'#10B981','bg'=>'#D1FAE5','text'=>'#065F46'],
        'tardanza'    => ['label'=>'Tardanza',     'color'=>'#F59E0B','bg'=>'#FEF3C7','text'=>'#92400E'],
        'falta'       => ['label'=>'Falta',        'color'=>'#EF4444','bg'=>'#FEE2E2','text'=>'#991B1B'],
        'justificado' => ['label'=>'Justificado',  'color'=>'#8B5CF6','bg'=>'#EDE9FE','text'=>'#5B21B6'],
    ];

    // Registrar o actualizar asistencia del día
    public static function registrarHoy(int $idDocente, array $data): bool {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id_asistencia FROM asistencias
             WHERE id_docente=:id AND fecha=:fecha LIMIT 1'
        );
        $stmt->execute([':id'=>$idDocente, ':fecha'=>$data['fecha']]);
        $existe = $stmt->fetchColumn();

        if ($existe) {
            $stmt = $db->prepare(
                'UPDATE asistencias
                 SET hora_entrada=:he, hora_salida=:hs, estado=:estado,
                     observaciones=:obs, registrado_por=:rp
                 WHERE id_asistencia=:id'
            );
            return $stmt->execute([
                ':he'    => $data['hora_entrada'] ?: null,
                ':hs'    => $data['hora_salida']  ?: null,
                ':estado'=> $data['estado'],
                ':obs'   => trim($data['observaciones'] ?? ''),
                ':rp'    => $data['registrado_por'] ?? 'docente',
                ':id'    => $existe,
            ]);
        }

        $stmt = $db->prepare(
            'INSERT INTO asistencias
             (id_docente, fecha, hora_entrada, hora_salida, estado, observaciones, registrado_por)
             VALUES (:id,:fecha,:he,:hs,:estado,:obs,:rp)'
        );
        return $stmt->execute([
            ':id'    => $idDocente,
            ':fecha' => $data['fecha'],
            ':he'    => $data['hora_entrada'] ?: null,
            ':hs'    => $data['hora_salida']  ?: null,
            ':estado'=> $data['estado'],
            ':obs'   => trim($data['observaciones'] ?? ''),
            ':rp'    => $data['registrado_por'] ?? 'docente',
        ]);
    }

    // Asistencia de hoy para un docente
    public static function hoy(int $idDocente): array|false {
        $stmt = getDB()->prepare(
            'SELECT * FROM asistencias
             WHERE id_docente=:id AND fecha=CURRENT_DATE LIMIT 1'
        );
        $stmt->execute([':id'=>$idDocente]);
        return $stmt->fetch();
    }

    // Historial de un docente con filtros
    public static function historialDocente(int $idDocente, array $f = []): array {
        $where  = ['a.id_docente=:id'];
        $params = [':id'=>$idDocente];

        if (!empty($f['mes']))  { $where[]='EXTRACT(MONTH FROM a.fecha)=:mes';  $params[':mes']=$f['mes']; }
        if (!empty($f['anio'])) { $where[]='EXTRACT(YEAR  FROM a.fecha)=:anio'; $params[':anio']=$f['anio']; }
        if (!empty($f['estado'])) { $where[]='a.estado=:estado'; $params[':estado']=$f['estado']; }

        $sql = 'SELECT a.*, d.nombre AS docente
                FROM asistencias a
                JOIN docentes d ON d.id_docente=a.id_docente
                WHERE '.implode(' AND ',$where).'
                ORDER BY a.fecha DESC';

        $stmt = getDB()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Resumen estadístico de un docente
    public static function resumenDocente(int $idDocente, int $mes, int $anio): array {
        $stmt = getDB()->prepare(
            "SELECT
               COUNT(*) FILTER (WHERE estado='asistio')     AS asistencias,
               COUNT(*) FILTER (WHERE estado='tardanza')    AS tardanzas,
               COUNT(*) FILTER (WHERE estado='falta')       AS faltas,
               COUNT(*) FILTER (WHERE estado='justificado') AS justificados,
               COUNT(*) AS total
             FROM asistencias
             WHERE id_docente=:id
               AND EXTRACT(MONTH FROM fecha)=:mes
               AND EXTRACT(YEAR  FROM fecha)=:anio"
        );
        $stmt->execute([':id'=>$idDocente,':mes'=>$mes,':anio'=>$anio]);
        return $stmt->fetch() ?: ['asistencias'=>0,'tardanzas'=>0,'faltas'=>0,'justificados'=>0,'total'=>0];
    }

    // === ADMIN ===

    // Todos los docentes con su resumen del mes
    public static function resumenTodos(int $mes, int $anio): array {
        $stmt = getDB()->prepare(
            "SELECT d.id_docente, d.nombre,
               COUNT(a.id_asistencia) AS total,
               COUNT(a.id_asistencia) FILTER (WHERE a.estado='asistio')     AS asistencias,
               COUNT(a.id_asistencia) FILTER (WHERE a.estado='tardanza')    AS tardanzas,
               COUNT(a.id_asistencia) FILTER (WHERE a.estado='falta')       AS faltas,
               COUNT(a.id_asistencia) FILTER (WHERE a.estado='justificado') AS justificados
             FROM docentes d
             LEFT JOIN asistencias a
               ON a.id_docente=d.id_docente
               AND EXTRACT(MONTH FROM a.fecha)=:mes
               AND EXTRACT(YEAR  FROM a.fecha)=:anio
             WHERE d.estado=1
             GROUP BY d.id_docente, d.nombre
             ORDER BY d.nombre ASC"
        );
        $stmt->execute([':mes'=>$mes,':anio'=>$anio]);
        return $stmt->fetchAll();
    }

    // Detalle día a día de todos los docentes (admin)
    public static function diarioAdmin(array $f = []): array {
        $where  = ['1=1'];
        $params = [];

        if (!empty($f['fecha']))      { $where[]='a.fecha=:fecha';          $params[':fecha']=$f['fecha']; }
        if (!empty($f['id_docente'])) { $where[]='a.id_docente=:id_docente';$params[':id_docente']=$f['id_docente']; }
        if (!empty($f['estado']))     { $where[]='a.estado=:estado';        $params[':estado']=$f['estado']; }

        $sql = 'SELECT a.*, d.nombre AS docente
                FROM asistencias a
                JOIN docentes d ON d.id_docente=a.id_docente
                WHERE '.implode(' AND ',$where).'
                ORDER BY a.fecha DESC, d.nombre ASC';

        $stmt = getDB()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Editar registro (solo admin)
    public static function update(int $id, array $data): bool {
        $stmt = getDB()->prepare(
            'UPDATE asistencias
             SET estado=:estado, hora_entrada=:he, hora_salida=:hs,
                 observaciones=:obs, registrado_por=:rp
             WHERE id_asistencia=:id'
        );
        return $stmt->execute([
            ':estado' => $data['estado'],
            ':he'     => $data['hora_entrada'] ?: null,
            ':hs'     => $data['hora_salida']  ?: null,
            ':obs'    => trim($data['observaciones'] ?? ''),
            ':rp'     => 'coordinador',
            ':id'     => $id,
        ]);
    }

    public static function find(int $id): array|false {
        $stmt = getDB()->prepare('SELECT * FROM asistencias WHERE id_asistencia=:id LIMIT 1');
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch();
    }

    public static function delete(int $id): bool {
        $stmt = getDB()->prepare('DELETE FROM asistencias WHERE id_asistencia=:id');
        return $stmt->execute([':id'=>$id]);
    }
}