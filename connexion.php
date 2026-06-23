<?php
session_start();

// Génère un captcha si ce n'est pas déjà fait
if (!isset($_SESSION['captcha_result'])) {
    $a = rand(1, 10);
    $b = rand(1, 10);
    $_SESSION['captcha_question'] = "Combien font $a + $b ?";
    $_SESSION['captcha_result'] = $a + $b;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $captcha_input = intval($_POST['captcha']);

    include 'data.php';

    if ($captcha_input !== $_SESSION['captcha_result']) {
        $error = "Captcha incorrect. Veuillez réessayer.";
    } else {
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $dbusername, $dbpassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $user['password'])) {
                    // Stockage des infos dans la session
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'] ?? 'user'; // par défaut "user"

                    // Redirection selon le rôle
                    if ($_SESSION['role'] === 'admin') {
                        $_SESSION['admin_logged_in'] = true; // <-- pour les pages admin
                        header("Location: admin_dashboard.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit();
                } else {
                    $error = "Mot de passe incorrect.";
                }
            } else {
                $error = "Email non trouvé.";
            }
        } catch (PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }

    // Génère un nouveau captcha pour chaque tentative
    $a = rand(1, 10);
    $b = rand(1, 10);
    $_SESSION['captcha_question'] = "Combien font $a + $b ?";
    $_SESSION['captcha_result'] = $a + $b;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="connexion.css">
</head>
<body>

<?php include 'navbarpric.php'; ?>

<div class="signup-container">
    <h2>Connexion</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form class="fo" action="connexion.php" method="post">
        <label for="email">Email :</label>
        <input class="i" type="email" id="email" name="email" required>

        <label for="password">Mot de passe :</label>
        <input class="i" type="password" id="password" name="password" required>

        <label for="captcha">Captcha :</label>
        <p><strong><?= $_SESSION['captcha_question'] ?></strong></p>
        <input class="i" type="number" id="captcha" name="captcha" required>

        <button class="b" type="submit">Se connecter</button>
    </form>

    <p><a href="mot-de-passe-oublie.php">Mot de passe oublié ?</a></p>
    <p>Pas encore inscrit ? <a href="inscription.php" class="register-link">Inscrivez-vous ici</a></p>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
