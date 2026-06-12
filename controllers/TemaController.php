<?php
// controllers/TemaController.php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Tema.php';
require_once __DIR__ . '/../models/Docente.php';
require_once __DIR__ . '/../models/Grupo.php';
require_once __DIR__ . '/../models/Area.php';
require_once __DIR__ . '/../models/Salon.php';

class TemaController {

    public function index(): void {
        Auth::requireLogin();

        $user    = Auth::user();
        $filtros = $_GET;

        // Docente solo ve sus propios registros
        if (!Auth::isCoord()) {
            $filtros['solo_docente'] = $user['id'];
        }

        $pageTitle  = 'Registro de Temas';
        $activePage = 'temas';
        $temas      = Tema::all($filtros);
        $docentes   = Auth::isCoord() ? Docente::all() : [];
        $grupos     = Grupo::activos();
        $areas      = Area::all();

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/temas/index.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function create(): void {
        Auth::requireLogin();

        $pageTitle  = 'Registrar Tema';
        $activePage = 'temas';
        $tema       = null;
        $errors     = [];
        $docentes   = Auth::isCoord() ? Docente::all() : [];
        $grupos     = Grupo::activos();
        $areas      = Area::all();
        $salones    = Salon::activos();

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/temas/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function store(): void {
        Auth::requireLogin();

        $errors = $this->validar($_POST);
        $user   = Auth::user();

        // Docente solo puede registrar en su nombre
        $idDocente = Auth::isCoord()
            ? (int)$_POST['id_docente']
            : $user['id'];

        if (empty($errors)) {
            try {
                Tema::create([
                    'fecha'         => $_POST['fecha'],
                    'id_docente'    => $idDocente,
                    'id_grupo'      => (int)$_POST['id_grupo'],
                    'id_area'       => (int)$_POST['id_area'],
                    'id_salon'      => (int)($_POST['id_salon'] ?? 0),
                    'tema'          => $_POST['tema'],
                    'subtema'       => $_POST['subtema'] ?? '',
                    'observaciones' => $_POST['observaciones'] ?? '',
                ]);
                $_SESSION['flash_success'] = 'Tema registrado correctamente.';
                header('Location: /index.php?page=temas');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Error al guardar. Intente nuevamente.';
            }
        }

        $pageTitle  = 'Registrar Tema';
        $activePage = 'temas';
        $tema       = null;
        $docentes   = Auth::isCoord() ? Docente::all() : [];
        $grupos     = Grupo::activos();
        $areas      = Area::all();
        $salones    = Salon::activos();

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/temas/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function edit(): void {
        Auth::requireLogin();

        $id   = (int)($_GET['id'] ?? 0);
        $tema = Tema::find($id);

        if (!$tema) {
            $_SESSION['flash_error'] = 'Registro no encontrado.';
            header('Location: /index.php?page=temas');
            exit;
        }

        // Docente solo puede editar sus propios registros
        if (!Auth::isCoord() && $tema['id_docente'] != Auth::user()['id']) {
            $_SESSION['flash_error'] = 'No tienes permiso para editar este registro.';
            header('Location: /index.php?page=temas');
            exit;
        }

        $pageTitle  = 'Editar Tema';
        $activePage = 'temas';
        $errors     = [];
        $docentes   = Auth::isCoord() ? Docente::all() : [];
        $grupos     = Grupo::activos();
        $areas      = Area::all();
        $salones    = Salon::activos();

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/temas/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function update(): void {
        Auth::requireLogin();

        $id   = (int)($_POST['id'] ?? 0);
        $tema = Tema::find($id);

        if (!$tema) {
            $_SESSION['flash_error'] = 'Registro no encontrado.';
            header('Location: /index.php?page=temas');
            exit;
        }

        if (!Auth::isCoord() && $tema['id_docente'] != Auth::user()['id']) {
            $_SESSION['flash_error'] = 'No tienes permiso para editar este registro.';
            header('Location: /index.php?page=temas');
            exit;
        }

        $errors    = $this->validar($_POST);
        $idDocente = Auth::isCoord() ? (int)$_POST['id_docente'] : Auth::user()['id'];

        if (empty($errors)) {
            Tema::update($id, [
                'fecha'         => $_POST['fecha'],
                'id_docente'    => $idDocente,
                'id_grupo'      => (int)$_POST['id_grupo'],
                'id_area'       => (int)$_POST['id_area'],
                'id_salon'      => (int)($_POST['id_salon'] ?? 0),
                'tema'          => $_POST['tema'],
                'subtema'       => $_POST['subtema'] ?? '',
                'observaciones' => $_POST['observaciones'] ?? '',
            ]);
            $_SESSION['flash_success'] = 'Tema actualizado correctamente.';
            header('Location: /index.php?page=temas');
            exit;
        }

        $pageTitle  = 'Editar Tema';
        $activePage = 'temas';
        $docentes   = Auth::isCoord() ? Docente::all() : [];
        $grupos     = Grupo::activos();
        $areas      = Area::all();
        $salones    = Salon::activos();

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/temas/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function delete(): void {
        Auth::requireLogin();

        $id   = (int)($_GET['id'] ?? 0);
        $tema = Tema::find($id);

        if (!$tema) {
            $_SESSION['flash_error'] = 'Registro no encontrado.';
            header('Location: /index.php?page=temas');
            exit;
        }

        if (!Auth::isCoord() && $tema['id_docente'] != Auth::user()['id']) {
            $_SESSION['flash_error'] = 'No tienes permiso para eliminar este registro.';
            header('Location: /index.php?page=temas');
            exit;
        }

        Tema::delete($id);
        $_SESSION['flash_success'] = 'Registro eliminado.';
        header('Location: /index.php?page=temas');
        exit;
    }

    private function validar(array $data): array {
        $errors = [];
        if (empty($data['fecha']))              $errors[] = 'La fecha es obligatoria.';
        if (empty($data['id_grupo']))           $errors[] = 'El grupo es obligatorio.';
        if (empty($data['id_area']))            $errors[] = 'El área es obligatoria.';
        if (empty(trim($data['tema'] ?? '')))   $errors[] = 'El tema es obligatorio.';
        if (Auth::isCoord() && empty($data['id_docente'])) $errors[] = 'El docente es obligatorio.';
        return $errors;
    }
}