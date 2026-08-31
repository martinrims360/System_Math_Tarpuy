<?php
// controllers/AuthController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';

class AuthController {

    public function index(): void {
        $this->login();
    }

    public function login(): void {
        if (Auth::check()) {
            header('Location: /index.php?page=dashboard');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo   = trim($_POST['correo']   ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($correo && $password) {
                try {
                    $db   = getDB();
                    $stmt = $db->prepare(
                        'SELECT id_docente, nombre, correo, password_hash, rol, estado
                         FROM docentes WHERE correo = :correo LIMIT 1'
                    );
                    $stmt->execute([':correo' => $correo]);
                    $docente = $stmt->fetch();

                    if ($docente && $docente['estado'] == 1 &&
                        password_verify($password, $docente['password_hash'])) {
                        Auth::login($docente);
                        header('Location: /index.php?page=dashboard');
                        exit;
                    } else {
                        $error = 'Correo o contraseña incorrectos, o cuenta inactiva.';
                    }
                } catch (PDOException $e) {
                    $error = 'Error de conexión. Intente nuevamente.';
                }
            } else {
                $error = 'Completa todos los campos.';
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function logout(): void {
        Auth::logout();
        header('Location: /index.php?page=login');
        exit;
    }
}