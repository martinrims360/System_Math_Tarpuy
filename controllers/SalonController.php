<?php
// controllers/SalonController.php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Salon.php';

class SalonController {

    public function index(): void {
        Auth::requireCoord();
        $pageTitle  = 'Salones';
        $activePage = 'salones';
        $salones    = Salon::all();

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/salones/index.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function create(): void {
        Auth::requireCoord();
        $pageTitle  = 'Nuevo Salón';
        $activePage = 'salones';
        $salon      = null;
        $errors     = [];

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/salones/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function store(): void {
        Auth::requireCoord();
        $errors = $this->validar($_POST);

        if (empty($errors)) {
            try {
                Salon::create([
                    'nombre'     => $_POST['nombre'],
                    'capacidad'  => $_POST['capacidad'] ?? '',
                    'ubicacion'  => $_POST['ubicacion'] ?? '',
                    'estado'     => isset($_POST['estado']) ? 1 : 0,
                ]);
                $_SESSION['flash_success'] = 'Salón registrado correctamente.';
                header('Location: index.php?page=salones');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Error al guardar. Intente nuevamente.';
            }
        }

        $pageTitle  = 'Nuevo Salón';
        $activePage = 'salones';
        $salon      = null;
        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/salones/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function edit(): void {
        Auth::requireCoord();
        $id    = (int)($_GET['id'] ?? 0);
        $salon = Salon::find($id);

        if (!$salon) {
            $_SESSION['flash_error'] = 'Salón no encontrado.';
            header('Location: index.php?page=salones');
            exit;
        }

        $pageTitle  = 'Editar Salón';
        $activePage = 'salones';
        $errors     = [];

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/salones/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function update(): void {
        Auth::requireCoord();
        $id    = (int)($_POST['id'] ?? 0);
        $salon = Salon::find($id);

        if (!$salon) {
            $_SESSION['flash_error'] = 'Salón no encontrado.';
            header('Location: index.php?page=salones');
            exit;
        }

        $errors = $this->validar($_POST);

        if (empty($errors)) {
            Salon::update($id, [
                'nombre'    => $_POST['nombre'],
                'capacidad' => $_POST['capacidad'] ?? '',
                'ubicacion' => $_POST['ubicacion'] ?? '',
                'estado'    => isset($_POST['estado']) ? 1 : 0,
            ]);
            $_SESSION['flash_success'] = 'Salón actualizado correctamente.';
            header('Location: index.php?page=salones');
            exit;
        }

        $pageTitle  = 'Editar Salón';
        $activePage = 'salones';
        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/salones/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function toggle(): void {
        Auth::requireCoord();
        Salon::toggleEstado((int)($_GET['id'] ?? 0));
        $_SESSION['flash_success'] = 'Estado del salón actualizado.';
        header('Location: index.php?page=salones');
        exit;
    }

    public function delete(): void {
        Auth::requireCoord();
        try {
            Salon::delete((int)($_GET['id'] ?? 0));
            $_SESSION['flash_success'] = 'Salón eliminado.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'No se puede eliminar: el salón tiene horarios asociados.';
        }
        header('Location: index.php?page=salones');
        exit;
    }

    private function validar(array $data): array {
        $errors = [];
        if (empty(trim($data['nombre'] ?? ''))) $errors[] = 'El nombre del salón es obligatorio.';
        if (!empty($data['capacidad']) && (!is_numeric($data['capacidad']) || $data['capacidad'] < 1))
            $errors[] = 'La capacidad debe ser un número mayor a 0.';
        return $errors;
    }
}
