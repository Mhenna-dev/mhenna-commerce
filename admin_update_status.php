<?php
session_start();
include 'data.php'; // connexion PDO

// Vérifier que l'admin est connecté
if(!isset($_SESSION['admin_logged_in'])){
    http_response_code(403);
    echo "Non autorisé";
    exit;
}

// Vérifier que POST contient order_id et status
if(isset($_POST['order_id']) && isset($_POST['status'])){
    $order_id = intval($_POST['order_id']); // sécurité
    $status = $_POST['status'];

    // Préparer et exécuter la mise à jour
    $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $updated = $stmt->execute([
        'status' => $status,
        'id' => $order_id
    ]);

    if($updated){
        echo "OK"; // tout s'est bien passé
    } else {
        http_response_code(500);
        echo "Erreur lors de la mise à jour";
    }
} else {
    http_response_code(400);
    echo "Données manquantes";
}
?>
