<?php
$host = 'localhost';
$dbname = 'webshoppen';
$username = 'root';
$password = 'root';
$port = 8889;

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;port=$port",
        $username,
        $password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (PDOException $e) {
    die("Anslutning misslyckades: " . $e->getMessage());
} 