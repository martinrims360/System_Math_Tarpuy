<?php
// controllers/SeguimientoController.php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Seguimiento.php';
require_once __DIR__ . '/../models/Docente.php';
require_once __DIR__ . '/../models/Grupo.php';
require_once __DIR__ . '/../models/Area.php';

class SeguimientoController {

    public function index(): void {
        Auth::requireLogin();

        $filtros = array_filter($_GET, fn($v) => $v !== '');
        unset($filtros['page']);

        $pageTitle       = 'Seguimiento Temático';
        $activePage      = 'seguimiento';
        $porGrupo        = Seguimiento::porGrupo();
        $porDocente      = Seguimiento::porDocente();
        $porArea         = Seguimiento::porArea();
        $historial       = Seguimiento::historial($filtros);
        $docentes        = Auth::isCoord() ? Docente::all() : [];
        $grupos          = Grupo::activos();
        $areas           = Area::all();

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/seguimiento/index.php';
        require __DIR__ . '/../views/shared/footer.php';
    }
}