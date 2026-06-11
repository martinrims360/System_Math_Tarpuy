<?php
// models/Dashboard.php

require_once __DIR__ . '/../config/database.php';

class Dashboard {

    public static function stats(): array {
        $db = getDB();

        $temas = $db->query(
            'SELECT COUNT(*) AS total FROM registros_temas'
        )->fetchColumn();

        $horarios = $db->query(
            'SELECT COUNT(*) AS total FROM horarios'
        )->fetchColumn();

        $docentes = $db->query(
            'SELECT COUNT(*) AS total FROM docentes WHERE estado=1'
        )->fetchColumn();

        $grupos = $db->query(
            'SELECT COUNT(*) AS total FROM grupos WHERE estado=1'
        )->fetchColumn();

        return [
            'temas'    => (int)$temas,
            'horarios' => (int)$horarios,
            'docentes' => (int)$docentes,
            'grupos'   => (int)$grupos,
        ];
    }

    // Últimos 8 temas registrados
    public static function ultimosTemas(): array {
        $stmt = getDB()->query(
            'SELECT rt.fecha, d.nombre AS docente, g.nombre AS grupo,
                    a.nombre AS area, rt.tema
             FROM registros_temas rt
             JOIN docentes d ON d.id_docente = rt.id_docente
             JOIN grupos   g ON g.id_grupo   = rt.id_grupo
             JOIN areas    a ON a.id_area    = rt.id_area
             ORDER BY rt.created_at DESC LIMIT 8'
        );
        return $stmt->fetchAll();
    }

    // Próximas 5 sesiones (horarios con fecha >= hoy)
    public static function proximasSesiones(): array {
        $stmt = getDB()->prepare(
            'SELECT h.fecha, h.dia_semana, h.hora_inicio, h.hora_fin,
                    d.nombre AS docente, g.nombre AS grupo, s.nombre AS salon
             FROM horarios h
             JOIN docentes d ON d.id_docente = h.id_docente
             JOIN grupos   g ON g.id_grupo   = h.id_grupo
             JOIN salones  s ON s.id_salon   = h.id_salon
             WHERE h.fecha >= CURRENT_DATE
             ORDER BY h.fecha ASC, h.hora_inicio ASC
             LIMIT 5'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}