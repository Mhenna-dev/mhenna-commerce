<?php
session_start();
include 'data.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["product_id"])) {
    $productId = intval($_POST["product_id"]);
    $userId = $_SESSION["user_id"];

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier si le produit est déjà dans les favoris
        $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":product_id", $productId);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            // Ajouter aux favoris si ce n'est pas déjà dedans
            $stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (:user_id, :product_id)");
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":product_id", $productId);
            $stmt->execute();
        }

        header("Location: favorites.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        exit();
    }
} else {
    echo "Requête invalide.";
    exit();
}
?>
