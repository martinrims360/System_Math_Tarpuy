<?php
// controllers/DocenteController.php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Docente.php';

class DocenteController {

    // Listar
    public function index(): void {
        Auth::requireCoord();
        $pageTitle  = 'Gestión de Docentes';
        $activePage = 'docentes';
        $docentes   = Docente::all();

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/docentes/index.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    // Formulario crear
    public function create(): void {
        Auth::requireCoord();
        $pageTitle  = 'Nuevo Docente';
        $activePage = 'docentes';
        $docente    = null;
        $errors     = [];

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/docentes/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    // Guardar nuevo
    public function store(): void {
        Auth::requireCoord();
        $errors = $this->validar($_POST, true);

        if (empty($errors)) {
            try {
                Docente::create([
                    'nombre'   => trim($_POST['nombre']),
                    'correo'   => trim($_POST['correo']),
                    'password' => $_POST['password'],
                    'rol'      => $_POST['rol'],
                    'estado'   => isset($_POST['estado']) ? 1 : 0,
                ]);
                $_SESSION['flash_success'] = 'Docente registrado correctamente.';
                header('Location: /index.php?page=docentes');
                exit;
            } catch (PDOException $e) {
                $errors[] = str_contains($e->getMessage(), 'unique') || str_contains($e->getMessage(), 'duplicate')
                    ? 'El correo ya está registrado en el sistema.'
                    : 'Error al guardar. Intente nuevamente.';
            }
        }

        // Volver al formulario con errores
        $pageTitle  = 'Nuevo Docente';
        $activePage = 'docentes';
        $docente    = null;
        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/docentes/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    // Formulario editar
    public function edit(): void {
        Auth::requireCoord();
        $id      = (int)($_GET['id'] ?? 0);
        $docente = Docente::find($id);

        if (!$docente) {
            $_SESSION['flash_error'] = 'Docente no encontrado.';
            header('Location: /index.php?page=docentes');
            exit;
        }

        $pageTitle  = 'Editar Docente';
        $activePage = 'docentes';
        $errors     = [];

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/docentes/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    // Guardar edición
    public function update(): void {
        Auth::requireCoord();
        $id      = (int)($_POST['id'] ?? 0);
        $docente = Docente::find($id);

        if (!$docente) {
            $_SESSION['flash_error'] = 'Docente no encontrado.';
            header('Location: /index.php?page=docentes');
            exit;
        }

        $errors = $this->validar($_POST, false);

        if (empty($errors)) {
            try {
                Docente::update($id, [
                    'nombre' => trim($_POST['nombre']),
                    'correo' => trim($_POST['correo']),
                    'rol'    => $_POST['rol'],
                    'estado' => isset($_POST['estado']) ? 1 : 0,
                ]);

                // Cambiar contraseña solo si se ingresó
                if (!empty($_POST['password'])) {
                    if (strlen($_POST['password']) < 6) {
                        $errors[] = 'La nueva contraseña debe tener al menos 6 caracteres.';
                    } else {
                        Docente::updatePassword($id, $_POST['password']);
                    }
                }

                if (empty($errors)) {
                    $_SESSION['flash_success'] = 'Docente actualizado correctamente.';
                    header('Location: /index.php?page=docentes');
                    exit;
                }
            } catch (PDOException $e) {
                $errors[] = str_contains($e->getMessage(), 'unique') || str_contains($e->getMessage(), 'duplicate')
                    ? 'El correo ya está en uso por otro docente.'
                    : 'Error al actualizar. Intente nuevamente.';
            }
        }

        $pageTitle  = 'Editar Docente';
        $activePage = 'docentes';
        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/docentes/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    // Activar / desactivar
    public function toggle(): void {
        Auth::requireCoord();
        $id = (int)($_GET['id'] ?? 0);

        // Evitar desactivarse a sí mismo
        if ($id === Auth::user()['id']) {
            $_SESSION['flash_error'] = 'No puedes desactivar tu propia cuenta.';
            header('Location: /index.php?page=docentes');
            exit;
        }

        Docente::toggleEstado($id);
        $_SESSION['flash_success'] = 'Estado del docente actualizado.';
        header('Location: /index.php?page=docentes');
        exit;
    }

    // Eliminar
    public function delete(): void {
        Auth::requireCoord();
        $id = (int)($_GET['id'] ?? 0);

        if ($id === Auth::user()['id']) {
            $_SESSION['flash_error'] = 'No puedes eliminar tu propia cuenta.';
            header('Location: /index.php?page=docentes');
            exit;
        }

        try {
            Docente::delete($id);
            $_SESSION['flash_success'] = 'Docente eliminado.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'No se puede eliminar: el docente tiene registros asociados.';
        }

        header('Location: /index.php?page=docentes');
        exit;
    }

    // Validación interna
    private function validar(array $data, bool $esNuevo): array {
        $errors = [];
        if (empty(trim($data['nombre'] ?? '')))  $errors[] = 'El nombre es obligatorio.';
        if (empty(trim($data['correo'] ?? '')))  $errors[] = 'El correo es obligatorio.';
        if (!filter_var($data['correo'] ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = 'El correo no es válido.';
        if (!in_array($data['rol'] ?? '', ['docente','coordinador']))  $errors[] = 'El rol no es válido.';
        if ($esNuevo) {
            if (empty($data['password']))            $errors[] = 'La contraseña es obligatoria.';
            elseif (strlen($data['password']) < 6)   $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        return $errors;
    }
}
