<?php
session_start();
include 'data.php';

if (isset($_POST['product_id'], $_POST['size'], $_POST['color'], $_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $size = trim($_POST['size']);
    $color = trim($_POST['color']);
    $qtyWanted = intval($_POST['quantity']);

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifie la disponibilité dans product_stock
        $sqlCheck = "SELECT quantity, img FROM product_stock 
                     WHERE product_id = :pid AND color = :color AND size = :size LIMIT 1";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            ':pid' => $productId,
            ':color' => $color,
            ':size' => $size
        ]);
        $stockAvailable = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$stockAvailable) {
            die("La variante sélectionnée (taille : $size, couleur : $color) n'existe pas.");
        }

        if ($qtyWanted > $stockAvailable['quantity']) {
            die("Quantité demandée trop élevée. Il reste seulement {$stockAvailable['quantity']} exemplaire(s).");
        }

        // Initialisation du panier
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $productExistsInCart = false;
        foreach ($_SESSION['cart'] as &$cartItem) {
            if (
                is_array($cartItem) && 
                $cartItem['id'] == $productId && 
                $cartItem['size'] == $size && 
                $cartItem['color'] == $color
            ) {
                $cartItem['quantity'] += $qtyWanted;
                $productExistsInCart = true;
                break;
            }
        }
        unset($cartItem);

        if (!$productExistsInCart) {
            // Ajouter le produit avec image binaire dans le panier
            $_SESSION['cart'][] = [
                'id' => $productId,
                'size' => $size,
                'color' => $color,
                'quantity' => $qtyWanted,
                'img' => $stockAvailable['img'], // image binaire issue de product_stock
            ];
        }

        // Mettre à jour la quantité dans la base
        $newStock = $stockAvailable['quantity'] - $qtyWanted;
        $sqlUpdate = "UPDATE product_stock SET quantity = :newQty 
                      WHERE product_id = :pid AND color = :color AND size = :size";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            ':newQty' => $newStock,
            ':pid' => $productId,
            ':color' => $color,
            ':size' => $size
        ]);

        header("Location: cart.php");
        exit;

    } catch (PDOException $e) {
        die("Erreur base de données : " . $e->getMessage());
    }
} else {
    die("Informations incomplètes pour l'ajout au panier.");
}
