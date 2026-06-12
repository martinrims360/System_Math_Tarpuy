<?php
// models/Grupo.php

require_once __DIR__ . '/../config/database.php';

class Grupo {

    public static function all(): array {
        return getDB()->query(
            'SELECT id_grupo, nombre, descripcion, estado FROM grupos ORDER BY nombre ASC'
        )->fetchAll();
    }

    public static function activos(): array {
        return getDB()->query(
            'SELECT id_grupo, nombre FROM grupos WHERE estado=1 ORDER BY nombre ASC'
        )->fetchAll();
    }

    public static function find(int $id): array|false {
        $stmt = getDB()->prepare(
            'SELECT id_grupo, nombre, descripcion, estado FROM grupos WHERE id_grupo=:id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create(array $data): bool {
        $stmt = getDB()->prepare(
            'INSERT INTO grupos (nombre, descripcion, estado) VALUES (:nombre, :desc, :estado)'
        );
        return $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':desc'   => trim($data['descripcion'] ?? ''),
            ':estado' => $data['estado'] ?? 1,
        ]);
    }

    public static function update(int $id, array $data): bool {
        $stmt = getDB()->prepare(
            'UPDATE grupos SET nombre=:nombre, descripcion=:desc, estado=:estado WHERE id_grupo=:id'
        );
        return $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':desc'   => trim($data['descripcion'] ?? ''),
            ':estado' => $data['estado'],
            ':id'     => $id,
        ]);
    }

    public static function toggleEstado(int $id): bool {
        $stmt = getDB()->prepare(
            'UPDATE grupos SET estado = CASE WHEN estado=1 THEN 0 ELSE 1 END WHERE id_grupo=:id'
        );
        return $stmt->execute([':id' => $id]);
    }

    public static function delete(int $id): bool {
        $stmt = getDB()->prepare('DELETE FROM grupos WHERE id_grupo=:id');
        return $stmt->execute([':id' => $id]);
    }
}