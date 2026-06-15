<?php
// helpers/Auth.php

class Auth {

    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(array $docente): void {
        self::start();
        session_regenerate_id(true);
        $_SESSION['user_id']  = $docente['id_docente'];
        $_SESSION['nombre']   = $docente['nombre'];
        $_SESSION['correo']   = $docente['correo'];
        $_SESSION['rol']      = $docente['rol'];
    }

    public static function logout(): void {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool {
        self::start();
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            header('Location:concursos_mat/index.php?page=login');
            exit;
        }
    }

    public static function requireCoord(): void {
        self::requireLogin();
        if ($_SESSION['rol'] !== 'coordinador') {
            header('Location:concursos_mat/index.php?page=dashboard');
            exit;
        }
    }

    public static function user(): array {
        self::start();
        return [
            'id'     => $_SESSION['user_id']  ?? null,
            'nombre' => $_SESSION['nombre']   ?? '',
            'correo' => $_SESSION['correo']   ?? '',
            'rol'    => $_SESSION['rol']      ?? '',
        ];
    }

    public static function isCoord(): bool {
        self::start();
        return ($_SESSION['rol'] ?? '') === 'coordinador';
    }
}