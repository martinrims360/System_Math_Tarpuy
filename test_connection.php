<?php

require_once 'config/database.php';

try {

    $db = getDB();

    $stmt = $db->query("SELECT COUNT(*) FROM areas");

    echo "<h2>✅ Conexión exitosa</h2>";
    echo "<p>Total de áreas: " . $stmt->fetchColumn() . "</p>";

} catch (Exception $e) {

    echo "<h2>❌ Error</h2>";
    echo $e->getMessage();

}   