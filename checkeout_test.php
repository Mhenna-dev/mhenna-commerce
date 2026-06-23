<?php

include 'data.php'; // connexion PDO

// Vérifier que l'utilisateur est connecté
if(!isset($_SESSION['user_id'])){
    header("Location: connexion.php");
    exit;
}

// Récupérer l'ID de la commande depuis GET ou SESSION
if(!isset($_GET['order_id'])){
    echo "Commande non spécifiée.";
    exit;
}

$order_id = intval($_GET['order_id']);

// Récupérer les infos de la commande
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$order){
    echo "Commande introuvable.";
    exit;
}

// Traitement du paiement test
if(isset($_POST['pay'])){
    // Ici on simule le paiement, on peut mettre à jour le statut de la commande
    $stmt = $conn->prepare("UPDATE orders SET status = 'payé' WHERE id = :id");
    $stmt->execute(['id' => $order_id]);

    // Rediriger vers une page de confirmation ou donner les points bonus
    header("Location: confirmation_test.php?order_id=$order_id");
    exit;
}
?>

<h2>Paiement Test</h2>

<p>Commande n°: <?= $order['id'] ?> | Montant: <?= $order['total_price'] ?> €</p>

<form method="post">
    <p>Simuler le paiement pour tester et obtenir les points bonus.</p>
    <button type="submit" name="pay">Payer la commande</button>
</form>

<a href="index.php" style="display:inline-block; margin-top:10px;">Retour à l'accueil</a>
