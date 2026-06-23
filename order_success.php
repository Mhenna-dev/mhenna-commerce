<?php
session_start();

// Sécurité : numéro de commande obligatoire
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$order_id = (int) $_GET['id'];

// Connexion PDO
$pdo = new PDO('mysql:host=localhost;dbname=data;charset=utf8mb4', 'Saadi', '23091970Louizanath?', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// Récupération commande
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "Commande introuvable";
    exit;
}

// Récupération produits
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Commande validée</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body{margin:0;font-family:Arial;background:#eaeded;color:#0f1111;}
    .header{background:#131921;color:#fff;padding:15px 30px;font-size:22px;font-weight:bold;}
    .header span{color:#ff9900;}
    .container{max-width:900px;margin:40px auto;padding:0 20px;}
    .card{background:#fff;border-radius:10px;padding:25px;box-shadow:0 2px 6px rgba(0,0,0,0.15);}
    h1{color:#067d62;margin-bottom:10px;}
    h2{margin-top:30px;}
    .row{display:flex;justify-content:space-between;margin:8px 0;}
    .total{font-size:18px;font-weight:bold;}
    .btn{
      display:inline-block;
      margin-top:25px;
      padding:12px 25px;
      background:#ffd814;
      color:#111;
      text-decoration:none;
      border-radius:8px;
      border:1px solid #fcd200;
      font-weight:bold;
    }
    .btn:hover{background:#f7ca00;}
    .success{font-size:60px;}
  </style>
</head>
<body>

<div class="header">Mon<span>Shop</span></div>

<div class="container">
  <div class="card">
    <div class="success">✅</div>
    <h1>Merci pour votre commande !</h1>
    <p>Votre commande <strong>#<?= $order_id ?></strong> a été validée avec succès.</p>

    <h2>Récapitulatif</h2>
    <?php foreach ($items as $item): ?>
      <div class="row">
        <span><?= htmlspecialchars($item['product_name']) ?> (x<?= $item['quantity'] ?>)</span>
        <span><?= number_format($item['price'] * $item['quantity'], 2) ?> €</span>
      </div>
    <?php endforeach; ?>

    <hr>

    <div class="row">
      <span>Livraison</span>
      <span><?= number_format($order['delivery_price'], 2) ?> €</span>
    </div>

    <div class="row total">
      <span>Total payé</span>
      <span><?= number_format($order['total_price'], 2) ?> €</span>
    </div>

    <p><strong>Adresse de livraison :</strong><br>
      <?= htmlspecialchars($order['full_name']) ?><br>
      <?= htmlspecialchars($order['address']) ?><br>
      <?= htmlspecialchars($order['postal_code']) ?> <?= htmlspecialchars($order['city']) ?>
    </p>

    <p><strong>Moyen de paiement :</strong> <?= htmlspecialchars($order['payment_method']) ?></p>

    <a href="index.php" class="btn">Retour à la boutique</a>
  </div>
</div>

</body>
</html>