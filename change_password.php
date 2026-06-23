<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: connexion.php");
    exit();
}

$username = $_SESSION['username']; 
$email = $_SESSION['email']; 

include 'data.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = htmlspecialchars(trim($_POST['current_password']));
    $new_password = htmlspecialchars(trim($_POST['new_password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

    // Vérifier que les mots de passe correspondent
    if ($new_password !== $confirm_password) {
        echo "<p style='color: red;'>Les mots de passe ne correspondent pas.</p>";
        exit();
    }

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier l'ancien mot de passe
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current_password, $user['password'])) {
            // Si l'ancien mot de passe est correct, on le met à jour
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                echo "<p style='color: green;'>Mot de passe mis à jour avec succès.</p>";
            } else {
                echo "<p style='color: red;'>Une erreur est survenue lors de la mise à jour du mot de passe.</p>";
            }
        } else {
            echo "<p style='color: red;'>L'ancien mot de passe est incorrect.</p>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe</title>
    <link rel="stylesheet" href="change_password.css">
</head>
<body>
    <?php include 'navbarpric.php'; ?>
    

    <div class="account-container">
        <h2>Changer votre mot de passe</h2>
        <br>

        <form class="fo" action="change_password.php" method="POST">
            <label for="current_password">Ancien mot de passe :</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">Nouveau mot de passe :</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">Confirmer le mot de passe :</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button class="b" type="submit">Enregistrer les modifications</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
