<?php
// controllers/DashboardController.php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Dashboard.php';

class DashboardController {

    public function index(): void {
        Auth::requireLogin();

        $pageTitle  = 'Dashboard';
        $activePage = 'dashboard';
        $stats      = Dashboard::stats();
        $ultimos    = Dashboard::ultimosTemas();
        $proximas   = Dashboard::proximasSesiones();

        require __DIR__ . '/../views/shared/header.php';
        require __DIR__ . '/../views/dashboard/index.php';
        require __DIR__ . '/../views/shared/footer.php';
    }
}