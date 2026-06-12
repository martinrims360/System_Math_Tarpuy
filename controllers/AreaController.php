<?php
// controllers/AreaController.php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Area.php';

class AreaController {

    public function index(): void {
        Auth::requireCoord();
        $pageTitle  = 'Áreas Temáticas';
        $activePage = 'areas';
        $areas      = Area::all();

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/areas/index.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function create(): void {
        Auth::requireCoord();
        $pageTitle  = 'Nueva Área';
        $activePage = 'areas';
        $area       = null;
        $errors     = [];

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/areas/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function store(): void {
        Auth::requireCoord();
        $nombre = trim($_POST['nombre'] ?? '');
        $errors = [];

        if (empty($nombre)) {
            $errors[] = 'El nombre del área es obligatorio.';
        }

        if (empty($errors)) {
            try {
                Area::create($nombre);
                $_SESSION['flash_success'] = 'Área registrada correctamente.';
                header('Location: index.php?page=areas');
                exit;
            } catch (PDOException $e) {
                $errors[] = str_contains($e->getMessage(), 'unique')
                    ? 'Ya existe un área con ese nombre.'
                    : 'Error al guardar. Intente nuevamente.';
            }
        }

        $pageTitle  = 'Nueva Área';
        $activePage = 'areas';
        $area       = null;
        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/areas/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function edit(): void {
        Auth::requireCoord();
        $id   = (int)($_GET['id'] ?? 0);
        $area = Area::find($id);

        if (!$area) {
            $_SESSION['flash_error'] = 'Área no encontrada.';
            header('Location: index.php?page=areas');
            exit;
        }

        $pageTitle  = 'Editar Área';
        $activePage = 'areas';
        $errors     = [];

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/areas/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function update(): void {
        Auth::requireCoord();
        $id     = (int)($_POST['id'] ?? 0);
        $area   = Area::find($id);
        $nombre = trim($_POST['nombre'] ?? '');
        $errors = [];

        if (!$area) {
            $_SESSION['flash_error'] = 'Área no encontrada.';
            header('Location: index.php?page=areas');
            exit;
        }

        if (empty($nombre)) $errors[] = 'El nombre del área es obligatorio.';

        if (empty($errors)) {
            try {
                Area::update($id, $nombre);
                $_SESSION['flash_success'] = 'Área actualizada correctamente.';
                header('Location: index.php?page=areas');
                exit;
            } catch (PDOException $e) {
                $errors[] = str_contains($e->getMessage(), 'unique')
                    ? 'Ya existe un área con ese nombre.'
                    : 'Error al actualizar.';
            }
        }

        $pageTitle  = 'Editar Área';
        $activePage = 'areas';
        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/areas/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function delete(): void {
        Auth::requireCoord();
        try {
            Area::delete((int)($_GET['id'] ?? 0));
            $_SESSION['flash_success'] = 'Área eliminada.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'No se puede eliminar: el área tiene temas asociados.';
        }
        header('Location: index.php?page=areas');
        exit;
    }
}
