<?php
session_start();
include 'data.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['product_id'])) {
    echo "Erreur : Utilisateur non connecté ou ID de produit manquant.";
    exit;
}

$productId = intval($_GET['product_id']);

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Suppression du produit des favoris de l'utilisateur
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':product_id', $productId);
    $stmt->execute();

    // Redirection vers la page des favoris après suppression
    header("Location: favorites.php");
    exit;

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>
