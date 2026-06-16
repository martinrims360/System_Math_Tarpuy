<?php
// models/Seguimiento.php

require_once __DIR__ . '/../config/database.php';

class Seguimiento {

    // Resumen de temas por grupo
    public static function porGrupo(): array {
        return getDB()->query(
            "SELECT g.nombre AS grupo,
                    COUNT(rt.id_registro)  AS total_temas,
                    COUNT(DISTINCT rt.id_area) AS areas_cubiertas,
                    COUNT(DISTINCT rt.id_docente) AS docentes_participantes,
                    MAX(rt.fecha) AS ultima_sesion
             FROM grupos g
             LEFT JOIN registros_temas rt ON rt.id_grupo = g.id_grupo
             WHERE g.estado = 1
             GROUP BY g.id_grupo, g.nombre
             ORDER BY g.nombre ASC"
        )->fetchAll();
    }

    // Resumen de temas por docente
    public static function porDocente(): array {
        return getDB()->query(
            "SELECT d.nombre AS docente,
                    COUNT(rt.id_registro)  AS total_temas,
                    COUNT(DISTINCT rt.id_grupo) AS grupos_atendidos,
                    COUNT(DISTINCT rt.id_area)  AS areas_trabajadas,
                    MAX(rt.fecha) AS ultima_sesion
             FROM docentes d
             LEFT JOIN registros_temas rt ON rt.id_docente = d.id_docente
             WHERE d.estado = 1
             GROUP BY d.id_docente, d.nombre
             ORDER BY total_temas DESC"
        )->fetchAll();
    }

    // Resumen de temas por área
    public static function porArea(): array {
        return getDB()->query(
            "SELECT a.nombre AS area,
                    COUNT(rt.id_registro) AS total_temas,
                    COUNT(DISTINCT rt.id_grupo) AS grupos_cubiertos
             FROM areas a
             LEFT JOIN registros_temas rt ON rt.id_area = a.id_area
             GROUP BY a.id_area, a.nombre
             ORDER BY total_temas DESC"
        )->fetchAll();
    }

    // Historial completo con filtros
    public static function historial(array $filtros = []): array {
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

        $sql = "SELECT rt.fecha, rt.tema, rt.subtema, rt.observaciones,
                       d.nombre AS docente,
                       g.nombre AS grupo,
                       a.nombre AS area,
                       s.nombre AS salon
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
}