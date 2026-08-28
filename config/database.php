<?php
// config/database.php
//
// En local (XAMPP/Laragon) usa los valores por defecto de abajo.
// En Fly.io, se sobreescriben automáticamente con "fly secrets set ..."
// (variables de entorno), sin tener que tocar este archivo ni subir
// contraseñas al repositorio.

define('DB_HOST',     getenv('DB_HOST')     ?: 'localhost');
define('DB_PORT',     getenv('DB_PORT')     ?: '5432');
define('DB_NAME',     getenv('DB_NAME')     ?: 'concursos_mat');
define('DB_USER',     getenv('DB_USER')     ?: 'postgres');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'agd123');
// En local (XAMPP) tu Postgres no tiene SSL activado, así que por defecto
// no se exige. En Neon/Fly.io se sobreescribe con "fly secrets set DB_SSLMODE=require".
define('DB_SSLMODE',  getenv('DB_SSLMODE')  ?: 'prefer');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            // sslmode=require: Neon exige conexión cifrada (igual que Supabase)
            'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_SSLMODE
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