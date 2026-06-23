<?php
session_start();
date_default_timezone_set('Europe/Paris'); // Définit le fuseau horaire

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Inclure PHPMailer (ajuste le chemin si besoin)
require 'C:/wamp64/phpmailer/src/Exception.php';
require 'C:/wamp64/phpmailer/src/phpmailer.php';
require 'C:/wamp64/phpmailer/src/SMTP.php';

include 'data.php'; // connexion à la BDD

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Régler la timezone pour MySQL aussi (heure d'été +02:00, ou -01:00 en hiver)
        $conn->exec("SET time_zone = '" . date('P') . "'");


        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Génère un jeton unique
            $token = bin2hex(random_bytes(32));
            $expiration = date('Y-m-d H:i:s', time() + 3600); // 1 heure à partir de maintenant

            // Mets à jour la table users avec le token et sa date d'expiration
            $update = $conn->prepare("UPDATE users SET reset_token = :token, reset_expires = :expires WHERE email = :email");
            $update->bindParam(':token', $token);
            $update->bindParam(':expires', $expiration);
            $update->bindParam(':email', $email);
            $update->execute();

            // Prépare le lien de réinitialisation
            $reset_link = "http://localhost/projet1/reinitialiser-mot-de-passe.php?token=" . $token;

            // Envoie du mail avec PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'sandbox.smtp.mailtrap.io'; // serveur SMTP Mailtrap (exemple)
                $mail->SMTPAuth = true;
                $mail->Username = 'e5281bf5b83168'; // remplace par ton username
                $mail->Password = '31a7dd704dd5f5'; // remplace par ton password
                $mail->Port = 2525;

                $mail->setFrom('noreply@tonsite.com', 'Mon site e-commerce');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de votre mot de passe';
                $mail->Body = "Bonjour,<br><br>Vous avez demandé à réinitialiser votre mot de passe.<br>
                Cliquez sur ce lien pour le faire : <a href='$reset_link'>$reset_link</a><br><br>
                Ce lien est valable pendant 1 heure.";

                $mail->send();
                echo "<p style='color: green;'>Un lien de réinitialisation a été envoyé à votre adresse email.</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>Erreur lors de l'envoi du mail : {$mail->ErrorInfo}</p>";
            }
        } else {
            echo "<p style='color: red;'>Adresse email non trouvée.</p>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="mot-de-passe-oublie.css">
</head>
<body>
    
    <form action="mot-de-passe-oublie.php" method="post">
        <label for="email">Entrez votre adresse email :</label><br>
        <input type="email" name="email" id="email" required><br><br>
        <button type="submit">Réinitialiser le mot de passe</button>
    </form>
</body>
</html>
