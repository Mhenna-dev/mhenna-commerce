<?php
session_start();

// Sécurité minimale
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Connexion PDO
$pdo = new PDO('mysql:host=localhost;dbname=data;charset=utf8mb4', 'Saadi', '23091970Louizanath?', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$user_id = $_SESSION['user_id'] ?? null;

// Données formulaire
$full_name = $_POST['full_name'] ?? '';
$address = $_POST['address'] ?? '';
$postal_code = $_POST['postal_code'] ?? '';
$city = $_POST['city'] ?? '';
$delivery_method = $_POST['delivery_method'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$gift_wrap = isset($_POST['gift_wrap']) ? 1 : 0;
$insurance = isset($_POST['insurance']) ? 1 : 0;
$customer_note = $_POST['customer_note'] ?? null;

// Prix livraison
$delivery_price = match($delivery_method) {
    'express' => 9.99,
    'standard' => 4.99,
    default => 0
};

$total = 0;
$cart = $_SESSION['cart'];

$pdo->beginTransaction();

try {
    // Calcul total réel depuis DB
    foreach ($cart as $item) {
        $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
        $stmt->execute([$item['id']]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new Exception('Produit introuvable');
        }

        $total += $product['price'] * $item['quantity'];
    }

    $final_total = $total + $delivery_price;

    // Insertion commande
    $stmt = $pdo->prepare("INSERT INTO orders
        (user_id, full_name, address, postal_code, city, delivery_method, delivery_price, payment_method, gift_wrap, insurance, customer_note, total_price)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $full_name,
        $address,
        $postal_code,
        $city,
        $delivery_method,
        $delivery_price,
        $payment_method,
        $gift_wrap,
        $insurance,
        $customer_note,
        $final_total
    ]);

    $order_id = $pdo->lastInsertId();

    // Insertion produits commande + stock
    foreach ($cart as $item) {
        $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
        $stmt->execute([$item['id']]);
        $product = $stmt->fetch();

        // order_items
        $stmt = $pdo->prepare("INSERT INTO order_items
            (order_id, product_id, product_name, price, quantity, color, size)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $order_id,
            $item['id'],
            $product['name'],
            $product['price'],
            $item['quantity'],
            $item['color'],
            $item['size']
        ]);

        // Décrément stock
        $stmt = $pdo->prepare("UPDATE product_stock
            SET quantity = quantity - ?
            WHERE product_id = ? AND color = ? AND size = ? AND quantity >= ?
        ");

        $stmt->execute([
            $item['quantity'],
            $item['product_id'],
            $item['color'],
            $item['size'],
            $item['quantity']
        ]);
    }

    $pdo->commit();

    // Vider panier
    unset($_SESSION['cart']);

    header('Location: order_success.php?id=' . $order_id);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erreur commande : " . $e->getMessage();
}
