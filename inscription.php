<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirmPassword = htmlspecialchars(trim($_POST['confirm-password']));
    
    if ($password !== $confirmPassword) {
        echo "<p style='color: red;'>Les mots de passe ne correspondent pas.</p>";
        exit;
    }

    include 'data.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<p style='color: red;'>Cet email est déjà utilisé.</p>";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                header("Location: connexion.php");
                exit();
            } else {
                echo "<p style='color: red;'>Une erreur est survenue.</p>";
            }
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
    <meta name="viewport" content="device-width, initial-scale=1.0">
    <title>Page d'inscription</title>
    <link rel="stylesheet" href="inscription.css">
</head>
<body>
    <?php include 'navbarpric.php';?>
    <div class="signup-container">
        <h2>S'INSCRIRE : </h2>
        <form class="fo" action="" method="POST" onsubmit="return validateForm()">
            <label class="l" for="username">Nom d'utilisateur :</label>
            <input class="i" type="text" id="username" name="username" required>

            <label class="l" for="email">Email :</label>
            <input class="i" type="email" id="email" name="email" required>

            <label class="l" for="password">Mot de passe :</label>
            <input class="i" type="password" id="password" name="password" required onkeyup="checkPasswordStrength()">

            <label class="l" for="confirm-password">Confirmer le mot de passe :</label><br>
            <input class="i" type="password" id="confirm-password" name="confirm-password" required>

            <p id="password-strength-msg"></p>

            <button class="b" type="submit">S'INSCRIRE</button>
        </form>
    </div>

    <script>
        function checkPasswordStrength() {
            var password = document.getElementById("password").value;
            var strengthMsg = document.getElementById("password-strength-msg");

            var strongPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            var mediumPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{6,}$/;

            if (strongPasswordPattern.test(password)) {
                strengthMsg.textContent = "Mot de passe fort";
                strengthMsg.style.color = "green";
            } else if (mediumPasswordPattern.test(password)) {
                strengthMsg.textContent = "Mot de passe moyen";
                strengthMsg.style.color = "orange";
            } else {
                strengthMsg.textContent = "Mot de passe faible";
                strengthMsg.style.color = "red";
            }
        }

        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm-password").value;
            var errorMsg = document.getElementById("error-msg");

            if (password !== confirmPassword) {
                errorMsg.textContent = "Les mots de passe ne correspondent pas.";
                errorMsg.style.color = "red";
                return false;
            }

            var strengthMsg = document.getElementById("password-strength-msg").textContent;
            if (strengthMsg === "Mot de passe faible") {
                alert("Le mot de passe est trop faible.");
                return false;
            }

            return true;
        }
    </script>

    <?php include 'footer.php';?>
</body>
</html>
