<?php
session_start();
include 'data.php';

if(!isset($_GET['order_id'])){
    echo "Commande non spécifiée.";
    exit;
}

$order_id = intval($_GET['order_id']);

// Ici tu peux afficher les infos de la commande et le message de points bonus
echo "<h2>Paiement effectué avec succès !</h2>";
echo "<p>Votre commande n°$order_id est confirmée.</p>";
echo "<p>Félicitations ! Vous avez gagné vos points bonus.</p>";

echo '<a href="index.php">Retour à l\'accueil</a>';
