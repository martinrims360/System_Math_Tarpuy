<?php
// config/asistencia.php
// Configuración del módulo de Asistencia (formulario externo)
 
return [
    // Enlace del Google Form que verán los docentes (mismo para todos)
    'form_url' => 'https://docs.google.com/forms/d/e/1FAIpQLSfRdzxW0RSDVgcBQOASAY8CJDpIxtHq3RvMyIAfc6qJ84gf7Q/viewform',
 
    // Token secreto compartido con el Apps Script del Google Sheet.
    // Debe coincidir EXACTO con el valor "TOKEN" que pongas en el script (OnFormSubmit.gs).
    // Cámbialo por algo largo y aleatorio antes de subir a producción.
    'webhook_token' => '80da486647fb3705dd5cd4c72638a561668f31a3f7cf5d2dd030921d90caa23e',
];