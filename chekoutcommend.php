<?php
session_start();
// Connexion PDO
$pdo = new PDO('mysql:host=localhost;dbname=data;charset=utf8mb4', 'Saadi', '23091970Louizanath?', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

if(!isset($_SESSION['user_id'])){
    header("Location: connexion.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
$items = [];

// Calcul du total et préparation des items
foreach ($cart as $item) {
    $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id = ?");
    $stmt->execute([$item['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $lineTotal = $product['price'] * $item['quantity'];
        $total += $lineTotal;
        $items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $item['quantity'],
            'color' => $item['color'],
            'size' => $item['size'],
            'lineTotal' => $lineTotal
        ];
    }
}

// Traitement du formulaire
if(isset($_POST['checkout'])){
    // Créer une commande dans la table orders
    $stmt = $pdo->prepare("INSERT INTO orders 
        (user_id, full_name, address, postal_code, city, delivery_method, delivery_price, payment_method, total_price, status)
        VALUES (:user_id, :full_name, :address, :postal_code, :city, :delivery_method, :delivery_price, :payment_method, :total_price, 'en attente')");
    
    $delivery_price = ($_POST['delivery_method'] == 'express') ? 9.99 : 4.99;

    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'full_name' => $_POST['full_name'],
        'address' => $_POST['address'],
        'postal_code' => $_POST['postal_code'],
        'city' => $_POST['city'],
        'delivery_method' => $_POST['delivery_method'],
        'delivery_price' => $delivery_price,
        'payment_method' => $_POST['payment_method'],
        'total_price' => $total + $delivery_price
    ]);

    $order_id = $pdo->lastInsertId();

    // Ajouter les items dans order_items
    foreach($items as $i){
        $stmtItem = $pdo->prepare("INSERT INTO order_items 
            (order_id, product_id, product_name, price, quantity, color, size)
            VALUES (:order_id, :product_id, :product_name, :price, :quantity, :color, :size)");
        $stmtItem->execute([
            'order_id' => $order_id,
            'product_id' => $i['id'],
            'product_name' => $i['name'],
            'price' => $i['price'],
            'quantity' => $i['quantity'],
            'color' => $i['color'],
            'size' => $i['size']
        ]);
    }

    // Vider le panier
    unset($_SESSION['cart']);

    // Rediriger vers le paiement test
    header("Location: checkout_test.php?order_id=$order_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Checkout | Style Amazon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body{margin:0;font-family:Arial;background:#eaeded;}
    .header{background:#131921;color:#fff;padding:15px 30px;font-size:22px;font-weight:bold;}
    .header span{color:#ff9900;}
    .container{max-width:1200px;margin:30px auto;display:flex;gap:20px;padding:0 20px;}
    .left{flex:3;} .right{flex:1;}
    .card{background:#fff;border-radius:8px;padding:20px;margin-bottom:20px;box-shadow:0 2px 5px rgba(0,0,0,0.1);}
    h2{margin-bottom:10px;} label{display:block;margin:8px 0;}
    input,textarea{width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:4px;}
    .row{display:flex;gap:10px;}
    .btn{background:#ffd814;border:1px solid #fcd200;padding:12px;width:100%;border-radius:8px;font-size:16px;cursor:pointer;}
    .btn:hover{background:#f7ca00;}
    .summary p{display:flex;justify-content:space-between;}
  </style>
</head>
<body>
<div class="header">Mon<span>Shop</span></div>

<form method="POST">
<div class="container">

<div class="left">
  <div class="card">
    <h2>Adresse de livraison</h2>
    <input name="full_name" required placeholder="Nom et prénom">
    <input name="address" required placeholder="Adresse">
    <div class="row">
      <input name="postal_code" required placeholder="Code postal">
      <input name="city" required placeholder="Ville">
    </div>
  </div>

  <div class="card">
    <h2>Livraison</h2>
    <label><input type="radio" name="delivery_method" value="standard" required> Standard – 4,99€</label>
    <label><input type="radio" name="delivery_method" value="express"> Express – 9,99€</label>
  </div>

  <div class="card">
    <h2>Paiement</h2>
    <label><input type="radio" name="payment_method" value="carte" required> Carte bancaire</label>
    <label><input type="radio" name="payment_method" value="paypal"> PayPal</label>
  </div>
</div>

<div class="right">
  <div class="card summary">
    <h2>Votre commande</h2>
    <?php foreach($items as $i): ?>
      <p><span><?= htmlspecialchars($i['name']) ?> x<?= $i['quantity'] ?></span><span><?= number_format($i['lineTotal'],2) ?>€</span></p>
    <?php endforeach; ?>
    <hr>
    <p><strong>Sous-total</strong><strong><?= number_format($total,2) ?>€</strong></p>
    <button class="btn" type="submit" name="checkout">Commander</button>
  </div>
</div>

</div>
</form>
</body>
</html>
