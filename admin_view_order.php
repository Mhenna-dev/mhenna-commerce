<?php
session_start();
include 'data.php'; // connexion PDO
include 'navbar_admin.php';

// Vérifier si l'admin est connecté
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}

// Vérifier si l'ID de commande est fourni
if(!isset($_GET['id'])){
    echo "Commande non spécifiée.";
    exit;
}

$order_id = $_GET['id'];

// Récupérer les infos de la commande
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$order){
    echo "Commande introuvable.";
    exit;
}

// Récupérer les produits de la commande
$stmt2 = $conn->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
$stmt2->execute(['order_id' => $order_id]);
$items = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Détails de la commande #<?= $order['id'] ?></h2>
<link rel="stylesheet" href="admin_style.css">

<h3>Informations client</h3>
<p><strong>Nom :</strong> <?= htmlspecialchars($order['full_name']) ?></p>
<p><strong>Adresse :</strong> <?= htmlspecialchars($order['address']) ?></p>
<p><strong>Ville :</strong> <?= htmlspecialchars($order['city']) ?></p>
<p><strong>Code postal :</strong> <?= htmlspecialchars($order['postal_code']) ?></p>

<h3>Infos commande</h3>
<p><strong>Méthode de livraison :</strong> <?= htmlspecialchars($order['delivery_method']) ?> (<?= $order['delivery_price'] ?> €)</p>
<p><strong>Méthode de paiement :</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
<p><strong>Emballage cadeau :</strong> <?= $order['gift_wrap'] ? "Oui" : "Non" ?></p>
<p><strong>Assurance :</strong> <?= $order['insurance'] ? "Oui" : "Non" ?></p>
<p><strong>Note client :</strong> <?= htmlspecialchars($order['customer_note']) ?></p>
<p><strong>Statut :</strong> <?= htmlspecialchars($order['status']) ?></p>
<p><strong>Total :</strong> <?= $order['total_price'] ?> €</p>

<h3>Produits commandés</h3>
<table border="1" cellpadding="10" cellspacing="0">
<tr>
    <th>Produit</th>
    <th>Prix</th>
    <th>Quantité</th>
    <th>Couleur</th>
    <th>Taille</th>
    <th>Sous-total</th>
</tr>
<?php foreach($items as $item): ?>
<tr>
    <td><?= htmlspecialchars($item['product_name']) ?></td>
    <td><?= $item['price'] ?> €</td>
    <td><?= $item['quantity'] ?></td>
    <td><?= htmlspecialchars($item['color']) ?></td>
    <td><?= htmlspecialchars($item['size']) ?></td>
    <td><?= $item['price'] * $item['quantity'] ?> €</td>
</tr>
<?php endforeach; ?>
</table>

<a href="admin_orders.php" style="display:inline-block; margin-top:10px;">Retour à la liste des commandes</a>
