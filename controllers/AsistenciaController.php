<?php
// controllers/AsistenciaController.php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/Docente.php';

class AsistenciaController {

    // Punto de entrada: decide vista según rol
    public function index(): void {
        Auth::requireLogin();
        if (Auth::isCoord()) {
            $this->admin();
        } else {
            $this->propia();
        }
    }

    // === DOCENTE ===

    // Vista propia: enlace al Google Form + estado de hoy (solo lectura) + historial del mes
    private function propia(): void {
        $idDocente = Auth::user()['id'];
        $hoy       = Asistencia::hoy($idDocente);

        $mes  = (int)($_GET['mes']  ?? date('n'));
        $anio = (int)($_GET['anio'] ?? date('Y'));

        $historial = Asistencia::historialDocente($idDocente, ['mes' => $mes, 'anio' => $anio]);
        $resumen   = Asistencia::resumenDocente($idDocente, $mes, $anio);

        $configAsistencia = require __DIR__ . '/../config/asistencia.php';
        $formUrl           = $configAsistencia['form_url'];

        $pageTitle  = 'Mi Asistencia';
        $activePage = 'asistencia';
        $estados    = Asistencia::ESTADOS;

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/asistencia/index.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    // === COORDINADOR ===

    // Tabla admin con filtros (fecha, docente, estado)
    private function admin(): void {
        $filtros = [
            'fecha'      => $_GET['fecha']      ?? '',
            'id_docente' => $_GET['id_docente'] ?? '',
            'estado'     => $_GET['estado']     ?? '',
        ];

        $registros = Asistencia::diarioAdmin($filtros);
        $docentes  = Docente::all();

        $pageTitle  = 'Control de Asistencia';
        $activePage = 'asistencia';
        $estados    = Asistencia::ESTADOS;

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/asistencia/admin.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    // Formulario editar registro puntual
    public function edit(): void {
        Auth::requireCoord();
        $id       = (int)($_GET['id'] ?? 0);
        $registro = Asistencia::find($id);

        if (!$registro) {
            $_SESSION['flash_error'] = 'Registro no encontrado.';
            header('Location: index.php?page=asistencia');
            exit;
        }

        $docente        = Docente::find($registro['id_docente']);
        $docenteNombre  = $docente['nombre'] ?? 'Docente';

        $pageTitle  = 'Editar Asistencia';
        $activePage = 'asistencia';
        $estados    = Asistencia::ESTADOS;
        $errors     = [];

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/asistencia/editar.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    // Guardar edición (coordinador)
    public function update(): void {
        Auth::requireCoord();
        $id       = (int)($_POST['id'] ?? 0);
        $registro = Asistencia::find($id);

        if (!$registro) {
            $_SESSION['flash_error'] = 'Registro no encontrado.';
            header('Location: index.php?page=asistencia');
            exit;
        }

        $errors = [];
        if (!array_key_exists($_POST['estado'] ?? '', Asistencia::ESTADOS)) {
            $errors[] = 'El estado seleccionado no es válido.';
        }

        if (empty($errors)) {
            try {
                Asistencia::update($id, [
                    'estado'        => $_POST['estado'],
                    'hora_entrada'  => $_POST['hora_entrada'] ?? null,
                    'hora_salida'   => $_POST['hora_salida']  ?? null,
                    'observaciones' => $_POST['observaciones'] ?? '',
                ]);
                $_SESSION['flash_success'] = 'Registro actualizado correctamente.';
                header('Location: index.php?page=asistencia');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Error al actualizar. Intente nuevamente.';
            }
        }

        // Volver al formulario con errores
        $registro       = Asistencia::find($id); // recargar por si acaso
        $docente        = Docente::find($registro['id_docente']);
        $docenteNombre  = $docente['nombre'] ?? 'Docente';
        $pageTitle      = 'Editar Asistencia';
        $activePage     = 'asistencia';
        $estados        = Asistencia::ESTADOS;

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/asistencia/editar.php';
        require __DIR__ . '/../views/shared/footer.php';
    }

    // Eliminar registro (coordinador)
    public function delete(): void {
        Auth::requireCoord();
        $id = (int)($_GET['id'] ?? 0);

        try {
            Asistencia::delete($id);
            $_SESSION['flash_success'] = 'Registro eliminado.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'No se pudo eliminar el registro.';
        }

        header('Location: index.php?page=asistencia');
        exit;
    }
}