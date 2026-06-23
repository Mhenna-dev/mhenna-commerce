<?php
session_start();

// Sécurité : utilisateur connecté obligatoire
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Connexion PDO
$pdo = new PDO('mysql:host=localhost;dbname=data;charset=utf8mb4', 'Saadi', '23091970Louizanath?', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// Récupération des commandes de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes commandes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body{margin:0;font-family:Arial;background:#eaeded;color:#0f1111;}
    .header{background:#131921;color:#fff;padding:15px 30px;font-size:22px;font-weight:bold;}
    .header span{color:#ff9900;}
    .container{max-width:1000px;margin:40px auto;padding:0 20px;}
    h1{margin-bottom:20px;}
    .order{background:#fff;border-radius:10px;padding:20px;margin-bottom:20px;box-shadow:0 2px 6px rgba(0,0,0,0.15);}
    .row{display:flex;justify-content:space-between;margin:6px 0;}
    .status{
      padding:5px 10px;
      border-radius:20px;
      font-size:13px;
      background:#e3e6e6;
      font-weight:bold;
    }
    .btn{
      display:inline-block;
      margin-top:10px;
      padding:8px 15px;
      background:#ffd814;
      border:1px solid #fcd200;
      border-radius:8px;
      color:#111;
      text-decoration:none;
      font-weight:bold;
      font-size:14px;
    }
    .btn:hover{background:#f7ca00;}
  </style>
</head>
<body>

<div class="header">Mon<span>Shop</span></div>

<div class="container">
  <h1>📦 Mes commandes</h1>

  <?php if (empty($orders)): ?>
    <p>Vous n'avez encore passé aucune commande.</p>
  <?php endif; ?>

  <?php foreach ($orders as $order): ?>
    <div class="order">
      <div class="row">
        <strong>Commande #<?= $order['id'] ?></strong>
        <span class="status"><?= htmlspecialchars($order['status']) ?></span>
      </div>

      <div class="row">
        <span>Date</span>
        <span><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
      </div>

      <div class="row">
        <span>Total</span>
        <span><?= number_format($order['total_price'], 2) ?> €</span>
      </div>

      <div class="row">
        <span>Paiement</span>
        <span><?= htmlspecialchars($order['payment_method']) ?></span>
      </div>

      <a class="btn" href="order_success.php?id=<?= $order['id'] ?>">Voir le détail</a>
    </div>
  <?php endforeach; ?>
</div>

</body>
</html>