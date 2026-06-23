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
    $new_username = htmlspecialchars(trim($_POST['username']));
    $new_email = htmlspecialchars(trim($_POST['email']));

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email WHERE email = :current_email");
        $stmt->bindParam(':username', $new_username);
        $stmt->bindParam(':email', $new_email);
        $stmt->bindParam(':current_email', $email);

        if ($stmt->execute()) {
            $_SESSION['username'] = $new_username;
            $_SESSION['email'] = $new_email;
            header("Location: mon_compte.php"); // Redirige vers "Mon Compte" après modification
            exit();
        } else {
            echo "<p style='color: red;'>Une erreur est survenue lors de la mise à jour de vos informations.</p>";
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
    <title>Modifier Mes Informations</title>
    <link rel="stylesheet" href="edit_account.css">
</head>
<body>
    <?php include 'navbarpric.php'; ?>
    <br><br><br><br><br>

    <div class="account-container">
        <h2>Modifier vos informations personnelles</h2>

        <form class="fo" action="edit_account.php" method="POST">
            <label for="username">Nom d'utilisateur :</label>
            <input class="i" type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>

            <label for="email">Email :</label>
            <input class="i" type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <button class="b" type="submit">Enregistrer les modifications</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
