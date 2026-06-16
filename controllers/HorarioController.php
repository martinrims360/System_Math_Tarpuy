<?php
// controllers/HorarioController.php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Horario.php';
require_once __DIR__ . '/../models/Docente.php';
require_once __DIR__ . '/../models/Grupo.php';
require_once __DIR__ . '/../models/Salon.php';

class HorarioController {

    private array $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];

    public function index(): void {
        Auth::requireLogin();

        $user    = Auth::user();
        $filtros = array_filter($_GET);

        if (!Auth::isCoord()) {
            $filtros['solo_docente'] = $user['id'];
        }

        $vista      = $_GET['vista'] ?? 'semanal';
        $pageTitle  = 'Horarios';
        $activePage = 'horarios';
        $horariosDia = Horario::porDia($filtros);
        $horariosList = Horario::all($filtros);
        $docentes   = Auth::isCoord() ? Docente::all() : [];
        $grupos     = Grupo::activos();
        $salones    = Salon::activos();
        $dias       = $this->dias;

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/horarios/index.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function create(): void {
        Auth::requireLogin();

        $pageTitle  = 'Nuevo Horario';
        $activePage = 'horarios';
        $horario    = null;
        $errors     = [];
        $docentes   = Auth::isCoord() ? Docente::all() : [];
        $grupos     = Grupo::activos();
        $salones    = Salon::activos();
        $dias       = $this->dias;

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/horarios/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function store(): void {
        Auth::requireLogin();

        $errors    = $this->validar($_POST);
        $idDocente = Auth::isCoord()
            ? (int)$_POST['id_docente']
            : Auth::user()['id'];

        if (empty($errors)) {
            try {
                Horario::create([
                    'id_docente'   => $idDocente,
                    'id_grupo'     => (int)$_POST['id_grupo'],
                    'id_salon'     => (int)$_POST['id_salon'],
                    'dia_semana'   => $_POST['dia_semana'],
                    'hora_inicio'  => $_POST['hora_inicio'],
                    'hora_fin'     => $_POST['hora_fin'],
                    'fecha'        => $_POST['fecha'],
                    'observaciones'=> $_POST['observaciones'] ?? '',
                ]);
                $_SESSION['flash_success'] = 'Horario registrado correctamente.';
                header('Location: index.php?page=horarios');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Error al guardar. Intente nuevamente.';
            }
        }

        $pageTitle  = 'Nuevo Horario';
        $activePage = 'horarios';
        $horario    = null;
        $docentes   = Auth::isCoord() ? Docente::all() : [];
        $grupos     = Grupo::activos();
        $salones    = Salon::activos();
        $dias       = $this->dias;

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/horarios/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function edit(): void {
        Auth::requireLogin();

        $id      = (int)($_GET['id'] ?? 0);
        $horario = Horario::find($id);

        if (!$horario) {
            $_SESSION['flash_error'] = 'Horario no encontrado.';
            header('Location: index.php?page=horarios');
            exit;
        }

        if (!Auth::isCoord() && $horario['id_docente'] != Auth::user()['id']) {
            $_SESSION['flash_error'] = 'No tienes permiso para editar este horario.';
            header('Location: index.php?page=horarios');
            exit;
        }

        $pageTitle  = 'Editar Horario';
        $activePage = 'horarios';
        $errors     = [];
        $docentes   = Auth::isCoord() ? Docente::all() : [];
        $grupos     = Grupo::activos();
        $salones    = Salon::activos();
        $dias       = $this->dias;

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/horarios/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function update(): void {
        Auth::requireLogin();

        $id      = (int)($_POST['id'] ?? 0);
        $horario = Horario::find($id);

        if (!$horario) {
            $_SESSION['flash_error'] = 'Horario no encontrado.';
            header('Location: index.php?page=horarios');
            exit;
        }

        if (!Auth::isCoord() && $horario['id_docente'] != Auth::user()['id']) {
            $_SESSION['flash_error'] = 'No tienes permiso para editar este horario.';
            header('Location: index.php?page=horarios');
            exit;
        }

        $errors    = $this->validar($_POST);
        $idDocente = Auth::isCoord() ? (int)$_POST['id_docente'] : Auth::user()['id'];

        if (empty($errors)) {
            Horario::update($id, [
                'id_docente'   => $idDocente,
                'id_grupo'     => (int)$_POST['id_grupo'],
                'id_salon'     => (int)$_POST['id_salon'],
                'dia_semana'   => $_POST['dia_semana'],
                'hora_inicio'  => $_POST['hora_inicio'],
                'hora_fin'     => $_POST['hora_fin'],
                'fecha'        => $_POST['fecha'],
                'observaciones'=> $_POST['observaciones'] ?? '',
            ]);
            $_SESSION['flash_success'] = 'Horario actualizado correctamente.';
            header('Location: index.php?page=horarios');
            exit;
        }

        $pageTitle  = 'Editar Horario';
        $activePage = 'horarios';
        $docentes   = Auth::isCoord() ? Docente::all() : [];
        $grupos     = Grupo::activos();
        $salones    = Salon::activos();
        $dias       = $this->dias;

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/horarios/form.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    public function delete(): void {
        Auth::requireLogin();

        $id      = (int)($_GET['id'] ?? 0);
        $horario = Horario::find($id);

        if (!$horario) {
            $_SESSION['flash_error'] = 'Horario no encontrado.';
            header('Location: index.php?page=horarios');
            exit;
        }

        if (!Auth::isCoord() && $horario['id_docente'] != Auth::user()['id']) {
            $_SESSION['flash_error'] = 'No tienes permiso para eliminar este horario.';
            header('Location: index.php?page=horarios');
            exit;
        }

        Horario::delete($id);
        $_SESSION['flash_success'] = 'Horario eliminado.';
        header('Location: index.php?page=horarios');
        exit;
    }

    private function validar(array $data): array {
        $errors = [];
        if (empty($data['fecha']))        $errors[] = 'La fecha es obligatoria.';
        if (empty($data['dia_semana']))   $errors[] = 'El día es obligatorio.';
        if (empty($data['hora_inicio']))  $errors[] = 'La hora de inicio es obligatoria.';
        if (empty($data['hora_fin']))     $errors[] = 'La hora de fin es obligatoria.';
        if (!empty($data['hora_inicio']) && !empty($data['hora_fin'])) {
            if ($data['hora_fin'] <= $data['hora_inicio'])
                $errors[] = 'La hora de fin debe ser mayor a la hora de inicio.';
        }
        if (empty($data['id_grupo']))     $errors[] = 'El grupo es obligatorio.';
        if (empty($data['id_salon']))     $errors[] = 'El salón es obligatorio.';
        if (Auth::isCoord() && empty($data['id_docente'])) $errors[] = 'El docente es obligatorio.';
        return $errors;
    }
}