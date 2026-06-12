<?php
// models/Horario.php

require_once __DIR__ . '/../config/database.php';

class Horario {

    public static function all(array $filtros = []): array {
        $where  = [];
        $params = [];

        if (!empty($filtros['id_docente'])) {
            $where[]               = 'h.id_docente = :id_docente';
            $params[':id_docente'] = $filtros['id_docente'];
        }
        if (!empty($filtros['id_grupo'])) {
            $where[]             = 'h.id_grupo = :id_grupo';
            $params[':id_grupo'] = $filtros['id_grupo'];
        }
        if (!empty($filtros['id_salon'])) {
            $where[]             = 'h.id_salon = :id_salon';
            $params[':id_salon'] = $filtros['id_salon'];
        }
        if (!empty($filtros['dia_semana'])) {
            $where[]              = 'h.dia_semana = :dia_semana';
            $params[':dia_semana'] = $filtros['dia_semana'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $where[]                = 'h.fecha >= :fecha_desde';
            $params[':fecha_desde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $where[]                = 'h.fecha <= :fecha_hasta';
            $params[':fecha_hasta'] = $filtros['fecha_hasta'];
        }

        // Docente solo ve los suyos
        if (!empty($filtros['solo_docente'])) {
            $where[]                 = 'h.id_docente = :solo_docente';
            $params[':solo_docente'] = $filtros['solo_docente'];
        }

        $sql = "SELECT h.id_horario, h.fecha, h.dia_semana,
                       h.hora_inicio, h.hora_fin, h.observaciones,
                       d.nombre  AS docente,  d.id_docente,
                       g.nombre  AS grupo,    g.id_grupo,
                       s.nombre  AS salon,    s.id_salon,
                       s.ubicacion
                FROM horarios h
                JOIN docentes d ON d.id_docente = h.id_docente
                JOIN grupos   g ON g.id_grupo   = h.id_grupo
                JOIN salones  s ON s.id_salon   = h.id_salon";

        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY h.fecha ASC, h.hora_inicio ASC';

        $stmt = getDB()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): array|false {
        $stmt = getDB()->prepare(
            'SELECT h.*, d.nombre AS docente, g.nombre AS grupo,
                    s.nombre AS salon, s.ubicacion
             FROM horarios h
             JOIN docentes d ON d.id_docente = h.id_docente
             JOIN grupos   g ON g.id_grupo   = h.id_grupo
             JOIN salones  s ON s.id_salon   = h.id_salon
             WHERE h.id_horario = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create(array $data): bool {
        $stmt = getDB()->prepare(
            'INSERT INTO horarios
             (id_docente, id_grupo, id_salon, dia_semana, hora_inicio, hora_fin, fecha, observaciones)
             VALUES (:id_docente,:id_grupo,:id_salon,:dia,:hi,:hf,:fecha,:obs)'
        );
        return $stmt->execute([
            ':id_docente' => $data['id_docente'],
            ':id_grupo'   => $data['id_grupo'],
            ':id_salon'   => $data['id_salon'],
            ':dia'        => $data['dia_semana'],
            ':hi'         => $data['hora_inicio'],
            ':hf'         => $data['hora_fin'],
            ':fecha'      => $data['fecha'],
            ':obs'        => trim($data['observaciones'] ?? ''),
        ]);
    }

    public static function update(int $id, array $data): bool {
        $stmt = getDB()->prepare(
            'UPDATE horarios
             SET id_docente=:id_docente, id_grupo=:id_grupo, id_salon=:id_salon,
                 dia_semana=:dia, hora_inicio=:hi, hora_fin=:hf,
                 fecha=:fecha, observaciones=:obs
             WHERE id_horario=:id'
        );
        return $stmt->execute([
            ':id_docente' => $data['id_docente'],
            ':id_grupo'   => $data['id_grupo'],
            ':id_salon'   => $data['id_salon'],
            ':dia'        => $data['dia_semana'],
            ':hi'         => $data['hora_inicio'],
            ':hf'         => $data['hora_fin'],
            ':fecha'      => $data['fecha'],
            ':obs'        => trim($data['observaciones'] ?? ''),
            ':id'         => $id,
        ]);
    }

    public static function delete(int $id): bool {
        $stmt = getDB()->prepare('DELETE FROM horarios WHERE id_horario=:id');
        return $stmt->execute([':id' => $id]);
    }

    // Agrupar por día para vista semanal
    public static function porDia(array $filtros = []): array {
        $rows = self::all($filtros);
        $dias = ['Lunes'=>[],'Martes'=>[],'Miércoles'=>[],'Jueves'=>[],'Viernes'=>[],'Sábado'=>[]];
        foreach ($rows as $r) {
            if (isset($dias[$r['dia_semana']])) {
                $dias[$r['dia_semana']][] = $r;
            }
        }
        return $dias;
    }
}