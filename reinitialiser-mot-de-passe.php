<?php
session_start();
date_default_timezone_set('Europe/Paris'); // Pour la cohérence horaire

include 'data.php';

if (!isset($_GET['token'])) {
    echo "<p style='color:red;'>Aucun jeton fourni.</p>";
    exit();
}

$token = $_GET['token'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Régler timezone MySQL aussi
    $conn->exec("SET time_zone = '" . date('P') . "'");


    // Vérifie que le token est valide et non expiré
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = :token AND reset_expires > NOW()");
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        echo "<p style='color:red;'>Lien invalide ou expiré.</p>";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_expires = NULL WHERE reset_token = :token");
        $update->bindParam(':password', $new_password);
        $update->bindParam(':token', $token);
        $update->execute();

        echo "<p style='color:green;'>Mot de passe réinitialisé. <a href='connexion.php'>Se connecter</a></p>";
        exit();
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser mot de passe</title>
    <link rel="stylesheet" href="reinitialiser-mot-de-passe.css">
</head>
<body>
    
    <form method="post">
        <label for="password">Entrez un nouveau mot de passe :</label><br>
        <input type="password" name="password" id="password" required><br><br>
        <button type="submit">Réinitialiser</button>
    </form>
</body>
</html>
