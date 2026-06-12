<?php
// controllers/GrupoController.php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Grupo.php';

class GrupoController {

    public function index(): void {
        Auth::requireCoord();
        $pageTitle  = 'Grupos de Preparación';
        $activePage = 'grupos';
        $grupos     = Grupo::all();

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/grupos/index.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function create(): void {
        Auth::requireCoord();
        $pageTitle  = 'Nuevo Grupo';
        $activePage = 'grupos';
        $grupo      = null;
        $errors     = [];

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/grupos/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function store(): void {
        Auth::requireCoord();
        $errors = $this->validar($_POST);

        if (empty($errors)) {
            try {
                Grupo::create([
                    'nombre'      => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'] ?? '',
                    'estado'      => isset($_POST['estado']) ? 1 : 0,
                ]);
                $_SESSION['flash_success'] = 'Grupo registrado correctamente.';
                header('Location: /index.php?page=grupos');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Error al guardar. Intente nuevamente.';
            }
        }

        $pageTitle  = 'Nuevo Grupo';
        $activePage = 'grupos';
        $grupo      = null;
        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/grupos/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function edit(): void {
        Auth::requireCoord();
        $id    = (int)($_GET['id'] ?? 0);
        $grupo = Grupo::find($id);

        if (!$grupo) {
            $_SESSION['flash_error'] = 'Grupo no encontrado.';
            header('Location: /index.php?page=grupos');
            exit;
        }

        $pageTitle  = 'Editar Grupo';
        $activePage = 'grupos';
        $errors     = [];

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/grupos/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function update(): void {
        Auth::requireCoord();
        $id    = (int)($_POST['id'] ?? 0);
        $grupo = Grupo::find($id);

        if (!$grupo) {
            $_SESSION['flash_error'] = 'Grupo no encontrado.';
            header('Location: index.php?page=grupos');
            exit;
        }

        $errors = $this->validar($_POST);

        if (empty($errors)) {
            Grupo::update($id, [
                'nombre'      => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? '',
                'estado'      => isset($_POST['estado']) ? 1 : 0,
            ]);
            $_SESSION['flash_success'] = 'Grupo actualizado correctamente.';
            header('Location: index.php?page=grupos');
            exit;
        }

        $pageTitle  = 'Editar Grupo';
        $activePage = 'grupos';
        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/grupos/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function toggle(): void {
        Auth::requireCoord();
        Grupo::toggleEstado((int)($_GET['id'] ?? 0));
        $_SESSION['flash_success'] = 'Estado del grupo actualizado.';
        header('Location: index.php?page=grupos');
        exit;
    }

    public function delete(): void {
        Auth::requireCoord();
        try {
            Grupo::delete((int)($_GET['id'] ?? 0));
            $_SESSION['flash_success'] = 'Grupo eliminado.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'No se puede eliminar: el grupo tiene registros asociados.';
        }
        header('Location: index.php?page=grupos');
        exit;
    }

    private function validar(array $data): array {
        $errors = [];
        if (empty(trim($data['nombre'] ?? ''))) $errors[] = 'El nombre del grupo es obligatorio.';
        return $errors;
    }
}