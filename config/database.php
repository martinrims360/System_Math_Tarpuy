<?php
// config/database.php

define('DB_HOST',     'localhost'); // reemplaza con tu host
define('DB_PORT',     '5432');
define('DB_NAME',     'concursos_mat');
define('DB_USER',     'postgres');
define('DB_PASSWORD', 'agd123');            // reemplaza

function getDB(): PDO {
        static $pdo = null;
        if ($pdo === null) {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                DB_HOST, DB_PORT, DB_NAME
            );
            $pdo = new PDO(
                $dsn, 
                DB_USER, 
                DB_PASSWORD, 
                [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT         => false,
            ]
        );
    }
    return $pdo;
}