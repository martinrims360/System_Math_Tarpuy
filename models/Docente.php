<?php
// models/Docente.php

require_once __DIR__ . '/../config/database.php';

class Docente {

    // Listar todos
    public static function all(): array {
        $stmt = getDB()->query(
            'SELECT id_docente, nombre, correo, rol, estado, created_at
             FROM docentes ORDER BY nombre ASC'
        );
        return $stmt->fetchAll();
    }

    // Buscar por ID
    public static function find(int $id): array|false {
        $stmt = getDB()->prepare(
            'SELECT id_docente, nombre, correo, rol, estado
             FROM docentes WHERE id_docente = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Crear
    public static function create(array $data): bool {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt = getDB()->prepare(
            'INSERT INTO docentes (nombre, correo, password_hash, rol, estado)
             VALUES (:nombre, :correo, :hash, :rol, :estado)'
        );
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':correo' => $data['correo'],
            ':hash'   => $hash,
            ':rol'    => $data['rol'],
            ':estado' => $data['estado'] ?? 1,
        ]);
    }

    // Actualizar (sin cambiar password)
    public static function update(int $id, array $data): bool {
        $stmt = getDB()->prepare(
            'UPDATE docentes SET nombre=:nombre, correo=:correo, rol=:rol, estado=:estado
             WHERE id_docente=:id'
        );
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':correo' => $data['correo'],
            ':rol'    => $data['rol'],
            ':estado' => $data['estado'],
            ':id'     => $id,
        ]);
    }

    // Cambiar contraseña
    public static function updatePassword(int $id, string $password): bool {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = getDB()->prepare(
            'UPDATE docentes SET password_hash=:hash WHERE id_docente=:id'
        );
        return $stmt->execute([':hash' => $hash, ':id' => $id]);
    }

    // Activar / desactivar
    public static function toggleEstado(int $id): bool {
        $stmt = getDB()->prepare(
            'UPDATE docentes SET estado = CASE WHEN estado=1 THEN 0 ELSE 1 END
             WHERE id_docente=:id'
        );
        return $stmt->execute([':id' => $id]);
    }

    // Eliminar (solo si no tiene registros)
    public static function delete(int $id): bool {
        $stmt = getDB()->prepare(
            'DELETE FROM docentes WHERE id_docente=:id'
        );
        return $stmt->execute([':id' => $id]);
    }

    // Estadísticas para dashboard
    public static function stats(): array {
        $row = getDB()->query(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN estado=1 THEN 1 ELSE 0 END) AS activos,
                SUM(CASE WHEN rol='coordinador' THEN 1 ELSE 0 END) AS coordinadores
             FROM docentes"
        )->fetch();
        return $row ?: ['total'=>0,'activos'=>0,'coordinadores'=>0];
    }
}