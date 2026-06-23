<?php
session_start();
include 'data.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT p.id, p.name, p.image, p.price FROM products p
                           JOIN favorites f ON p.id = f.product_id
                           WHERE f.user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes favoris</title>
    <link rel="stylesheet" href="./favorites.css">
</head>
<body>
<video class="video-background" autoplay muted loop>
    <source src="https://media.istockphoto.com/id/1412303894/fr/vid%C3%A9o/ic%C3%B4ne-de-panier-dachat-commerce-en-ligne-et-symbole-dentreprise-boucle-de-concept-num%C3%A9rique.mp4?s=mp4-480x480-is&k=20&c=JdjECwR1VcgmB6_F1Mhr96F8G66T3pCiSn2xuSyBEwA=" type="video/mp4">
    Votre navigateur ne supporte pas les vidéos.
</video>

<?php include 'navbarpric.php'; ?>

<h1>Mes produits favoris</h1>

<div class="products-list">
    <?php if (!empty($favorites)): ?>
        <?php foreach ($favorites as $product): ?>
            <div class="products-item">
                <a href="product_details.php?id=<?php echo htmlspecialchars($product['id']); ?>">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h2 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h2>
                </a>
                <p>Prix : <?php echo htmlspecialchars($product['price']); ?> €</p>

                <a href="remove-from-favorites.php?product_id=<?php echo $product['id']; ?>" class="remove-favorite">Retirer des favoris</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun produit ajouté aux favoris.</p>
    <?php endif; ?>
</div>

<a href="index.php" class="g">Retour au catalogue</a>

<?php include 'footer.php'; ?>

</body>
</html>
