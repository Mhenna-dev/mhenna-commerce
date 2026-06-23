<?php
session_start();
include 'data.php'; // connexion PDO

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Préparer la requête PDO
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user){
        // Vérifier le mot de passe
        if(password_verify($password, $user['password'])){
            // Vérifier le rôle admin
            if($user['role'] === 'admin'){
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_id'] = $user['id'];
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error = "Vous n'avez pas les droits administrateur.";
            }
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Nom d'utilisateur incorrect.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion Admin</title>
</head>
<body>
<h2>Connexion Admin</h2>
<form method="post">
    <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
    <input type="password" name="password" placeholder="Mot de passe" required><br>
    <button type="submit" name="login">Connexion</button>
</form>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</body>
</html>
