<?php
$servername = "localhost";
$dbname = "data";
$dbusername = "Saadi";
$dbpassword = "23091970Louizanath?";
// Connexion à la base de données
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>





