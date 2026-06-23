<?php
session_start();
include 'data.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT id, name, price, image FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

$cart_items = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}

// Modification de la quantité d'un produit
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $action = $_POST['update_quantity'];

    foreach ($cart_items as &$cart_item) {
        if ($cart_item['id'] == $product_id) {
            if ($action === 'increase') {
                $cart_item['quantity']++;
            } elseif ($action === 'decrease' && $cart_item['quantity'] > 1) {
                $cart_item['quantity']--;
            }
            break;
        }
    }
    $_SESSION['cart'] = $cart_items;
    header("Location: cart.php");
    exit;
}

// Suppression d'un produit spécifique du panier
if (isset($_POST['remove_product'])) {
    $product_id_to_remove = $_POST['remove_product'];
    foreach ($cart_items as $key => $cart_item) {
        if ($cart_item['id'] == $product_id_to_remove) {
            unset($cart_items[$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($cart_items);
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="carte.css">
</head>
<body>


<?php include 'navbarpric.php'; ?>
<br><br><br><br>
<h1>Votre Panier</h1>

<?php if (!empty($cart_items)): ?>
    <ul>
        <?php foreach ($cart_items as $cart_item): ?>
            <?php
            if (!is_array($cart_item) || !isset($cart_item['id'])) {
                continue;
            }

            $product = array_filter($products, function ($prod) use ($cart_item) {
                return $prod['id'] == $cart_item['id'];
            });
            $product = array_values($product);
            if (empty($product)) {
                continue;
            }
            $product = $product[0];

            $totalProductPrice = $product['price'] * $cart_item['quantity'];
            $total += $totalProductPrice;
            ?>
            <li>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="100">
 
                <?php echo htmlspecialchars($product['name']); ?> - 
                Taille: <?php echo htmlspecialchars($cart_item['size']); ?> - 
                Couleur: <?php echo htmlspecialchars($cart_item['color']); ?> - 
                Quantité: 
               
                - Prix unitaire: <?php echo htmlspecialchars($product['price']); ?> € - 
                Prix total: <?php echo htmlspecialchars($totalProductPrice); ?> €
                <form method="post" style="display:inline;">
                    <button class="b" type="submit" name="remove_product" value="<?php echo htmlspecialchars($cart_item['id']); ?>">Supprimer</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($cart_item['id']); ?>">
                    <button class="b" type="submit" name="update_quantity" value="increase">+</button>
                    <?php echo htmlspecialchars($cart_item['quantity']); ?>
                    <button class="b" type="submit" name="update_quantity" value="decrease">-</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <p>Total: <?php echo htmlspecialchars($total); ?> €</p>
    <form method="post">
        <button class="b" type="submit" name="clear_cart">Vider le Panier</button>
    </form>
<?php else: ?>
    <p>Votre panier est vide.</p>
   
<?php endif; ?>

<a href="index.php" class="m">Retourner au Catalogue</a>
<a href="chekoutcommend.php"><p>Valider ma commande</p></a>

<?php include 'footer.php'; ?>
</body>
</html>