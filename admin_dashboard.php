<?php
session_start();

// Vérifie que l'admin est connecté
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; }
        h1 { color: #131921; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
        .cards { display: flex; gap: 20px; flex-wrap: wrap; margin-top: 20px; }
        .card { background: #ffd814; padding: 20px; border-radius: 8px; flex: 1; min-width: 200px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card a { color: #131921; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> !</h1>
    <p>Vous êtes connecté en tant qu'administrateur.</p>

    <div class="cards">
        <div class="card">
            <a href="admin_products.php">Gérer les produits</a>
        </div>
        <div class="card">
            <a href="admin_categories.php">Gérer les catégories</a>
        </div>
        <div class="card">
            <a href="admin_orders.php">Gérer les commandes</a>
        </div>
        <div class="card">
            <a href="admin_logout.php">Se déconnecter</a>
        </div>
    </div>
</div>

</body>
</html>
