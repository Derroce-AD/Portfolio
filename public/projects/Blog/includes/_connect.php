<?php
// FILE WHERE WE TYPE THE CONNECTION CODE
$dbServer = '136.243.172.164:30005';
$dbUser = 'cdpi_groupe4_dev1';
$dbPassword = 'cdpi_groupe4_dev1';
$dbName = 'cdpi_groupe4_dev1';

try {
    // CONNECT TO THE DATABASE
    $conn = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Echec de la connexion : " . $e->getMessage());
}
?>