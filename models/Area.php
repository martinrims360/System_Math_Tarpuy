<?php
// models/Area.php

require_once __DIR__ . '/../config/database.php';

class Area {

    public static function all(): array {
        return getDB()->query(
            'SELECT id_area, nombre FROM areas ORDER BY nombre ASC'
        )->fetchAll();
    }

    public static function find(int $id): array|false {
        $stmt = getDB()->prepare(
            'SELECT id_area, nombre FROM areas WHERE id_area=:id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create(string $nombre): bool {
        $stmt = getDB()->prepare('INSERT INTO areas (nombre) VALUES (:nombre)');
        return $stmt->execute([':nombre' => trim($nombre)]);
    }

    public static function update(int $id, string $nombre): bool {
        $stmt = getDB()->prepare('UPDATE areas SET nombre=:nombre WHERE id_area=:id');
        return $stmt->execute([':nombre' => trim($nombre), ':id' => $id]);
    }

    public static function delete(int $id): bool {
        $stmt = getDB()->prepare('DELETE FROM areas WHERE id_area=:id');
        return $stmt->execute([':id' => $id]);
    }
}