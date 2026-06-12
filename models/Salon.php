<?php
// models/Salon.php

require_once __DIR__ . '/../config/database.php';

class Salon {

    public static function all(): array {
        return getDB()->query(
            'SELECT id_salon, nombre, capacidad, ubicacion, estado FROM salones ORDER BY nombre ASC'
        )->fetchAll();
    }

    public static function activos(): array {
        return getDB()->query(
            'SELECT id_salon, nombre, capacidad, ubicacion FROM salones WHERE estado=1 ORDER BY nombre ASC'
        )->fetchAll();
    }

    public static function find(int $id): array|false {
        $stmt = getDB()->prepare(
            'SELECT id_salon, nombre, capacidad, ubicacion, estado FROM salones WHERE id_salon=:id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create(array $data): bool {
        $stmt = getDB()->prepare(
            'INSERT INTO salones (nombre, capacidad, ubicacion, estado)
             VALUES (:nombre, :capacidad, :ubicacion, :estado)'
        );
        return $stmt->execute([
            ':nombre'    => trim($data['nombre']),
            ':capacidad' => ($data['capacidad'] !== '' ? (int)$data['capacidad'] : null),
            ':ubicacion' => trim($data['ubicacion'] ?? ''),
            ':estado'    => $data['estado'] ?? 1,
        ]);
    }

    public static function update(int $id, array $data): bool {
        $stmt = getDB()->prepare(
            'UPDATE salones SET nombre=:nombre, capacidad=:capacidad,
             ubicacion=:ubicacion, estado=:estado WHERE id_salon=:id'
        );
        return $stmt->execute([
            ':nombre'    => trim($data['nombre']),
            ':capacidad' => ($data['capacidad'] !== '' ? (int)$data['capacidad'] : null),
            ':ubicacion' => trim($data['ubicacion'] ?? ''),
            ':estado'    => $data['estado'],
            ':id'        => $id,
        ]);
    }

    public static function toggleEstado(int $id): bool {
        $stmt = getDB()->prepare(
            'UPDATE salones SET estado = CASE WHEN estado=1 THEN 0 ELSE 1 END WHERE id_salon=:id'
        );
        return $stmt->execute([':id' => $id]);
    }

    public static function delete(int $id): bool {
        $stmt = getDB()->prepare('DELETE FROM salones WHERE id_salon=:id');
        return $stmt->execute([':id' => $id]);
    }
}