<?php
// models/Tema.php

require_once __DIR__ . '/../config/database.php';

class Tema {

    public static function all(array $filtros = []): array {
        $where  = [];
        $params = [];

        if (!empty($filtros['id_docente'])) {
            $where[]               = 'rt.id_docente = :id_docente';
            $params[':id_docente'] = $filtros['id_docente'];
        }
        if (!empty($filtros['id_grupo'])) {
            $where[]             = 'rt.id_grupo = :id_grupo';
            $params[':id_grupo'] = $filtros['id_grupo'];
        }
        if (!empty($filtros['id_area'])) {
            $where[]            = 'rt.id_area = :id_area';
            $params[':id_area'] = $filtros['id_area'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $where[]                = 'rt.fecha >= :fecha_desde';
            $params[':fecha_desde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $where[]                = 'rt.fecha <= :fecha_hasta';
            $params[':fecha_hasta'] = $filtros['fecha_hasta'];
        }

        // Docente solo ve sus propios registros
        if (!empty($filtros['solo_docente'])) {
            $where[]               = 'rt.id_docente = :solo_docente';
            $params[':solo_docente'] = $filtros['solo_docente'];
        }

        $sql = "SELECT rt.id_registro, rt.fecha, rt.tema, rt.subtema, rt.observaciones,
                       rt.created_at,
                       d.nombre  AS docente,
                       g.nombre  AS grupo,
                       a.nombre  AS area,
                       s.nombre  AS salon
                FROM registros_temas rt
                JOIN docentes d ON d.id_docente = rt.id_docente
                JOIN grupos   g ON g.id_grupo   = rt.id_grupo
                JOIN areas    a ON a.id_area    = rt.id_area
                LEFT JOIN salones s ON s.id_salon = rt.id_salon";

        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY rt.fecha DESC, rt.created_at DESC';

        $stmt = getDB()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): array|false {
        $stmt = getDB()->prepare(
            'SELECT rt.*, d.nombre AS docente, g.nombre AS grupo, a.nombre AS area
             FROM registros_temas rt
             JOIN docentes d ON d.id_docente = rt.id_docente
             JOIN grupos   g ON g.id_grupo   = rt.id_grupo
             JOIN areas    a ON a.id_area    = rt.id_area
             WHERE rt.id_registro = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create(array $data): bool {
        $stmt = getDB()->prepare(
            'INSERT INTO registros_temas
             (fecha, id_docente, id_grupo, id_area, id_salon, tema, subtema, observaciones)
             VALUES (:fecha,:id_docente,:id_grupo,:id_area,:id_salon,:tema,:subtema,:obs)'
        );
        return $stmt->execute([
            ':fecha'      => $data['fecha'],
            ':id_docente' => $data['id_docente'],
            ':id_grupo'   => $data['id_grupo'],
            ':id_area'    => $data['id_area'],
            ':id_salon'   => $data['id_salon'] ?: null,
            ':tema'       => trim($data['tema']),
            ':subtema'    => trim($data['subtema'] ?? ''),
            ':obs'        => trim($data['observaciones'] ?? ''),
        ]);
    }

    public static function update(int $id, array $data): bool {
        $stmt = getDB()->prepare(
            'UPDATE registros_temas
             SET fecha=:fecha, id_docente=:id_docente, id_grupo=:id_grupo,
                 id_area=:id_area, id_salon=:id_salon,
                 tema=:tema, subtema=:subtema, observaciones=:obs
             WHERE id_registro=:id'
        );
        return $stmt->execute([
            ':fecha'      => $data['fecha'],
            ':id_docente' => $data['id_docente'],
            ':id_grupo'   => $data['id_grupo'],
            ':id_area'    => $data['id_area'],
            ':id_salon'   => $data['id_salon'] ?: null,
            ':tema'       => trim($data['tema']),
            ':subtema'    => trim($data['subtema'] ?? ''),
            ':obs'        => trim($data['observaciones'] ?? ''),
            ':id'         => $id,
        ]);
    }

    public static function delete(int $id): bool {
        $stmt = getDB()->prepare('DELETE FROM registros_temas WHERE id_registro=:id');
        return $stmt->execute([':id' => $id]);
    }
}