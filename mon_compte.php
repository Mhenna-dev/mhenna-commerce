<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: connexion.php");
    exit();
}

$username = $_SESSION['username']; 
$email = $_SESSION['email']; 

// Tu peux récupérer d'autres informations si nécessaire (par exemple depuis la base de données).
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="mon_compte.css"> <!-- Ton fichier CSS principal -->
</head>
<body>
    <?php include 'navbarpric.php'; ?>
    

    <div class="account-container">
        <div class="account-header">
            <h2>Bienvenue, <?php echo htmlspecialchars($username); ?> !</h2>
            <p>Voici vos informations personnelles.</p>
        </div>

        <div class="account-info">
            <div class="info-item">
                <label for="username">Nom d'utilisateur:</label>
                <span><?php echo htmlspecialchars($username); ?></span>
            </div>
            <div class="info-item">
                <label for="email">Email:</label>
                <span><?php echo htmlspecialchars($email); ?></span>
            </div>
        </div>

        <div class="account-actions">
            <a href="edit_account.php" class="btn">Modifier mes informations</a>
            <a href="change_password.php" class="btn">Changer le mot de passe</a>
             <a href="mes_commandes.php" class="btn">Historique de commandes</a>
        </div>
       
    </div>

    

    <?php include 'footer.php'; ?>
</body>
</html>
