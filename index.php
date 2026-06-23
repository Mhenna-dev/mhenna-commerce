<?php
session_start();
include 'data.php';


try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête de base pour récupérer les produits
    $query = "SELECT id, name, image, price, category FROM products WHERE 1";
    $params = [];

    // Filtrage par catégorie
    if (!empty($_GET['category'])) {
        $query .= " AND category = :category";
        $params[':category'] = $_GET['category'];
    }

    // Filtrage par prix minimum
    if (!empty($_GET['min_price'])) {
        $query .= " AND price >= :min_price";
        $params[':min_price'] = $_GET['min_price'];
    }

    // Filtrage par prix maximum
    if (!empty($_GET['max_price'])) {
        $query .= " AND price <= :max_price";
        $params[':max_price'] = $_GET['max_price'];
    }

    // Filtrage par recherche (nom)
    if (!empty($_GET['search'])) {
        $query .= " AND name LIKE :search";
        $params[':search'] = "%" . $_GET['search'] . "%";
    }

    // Exécution de la requête avec les paramètres
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue de produits</title>
    <link rel="stylesheet" href="./stylescart.css">
</head>
<body>
<video class="video-background" autoplay muted loop>
    <source src="https://media.istockphoto.com/id/1412303894/fr/vid%C3%A9o/ic%C3%B4ne-de-panier-dachat-commerce-en-ligne-et-symbole-dentreprise-boucle-de-concept-num%C3%A9rique.mp4?s=mp4-480x480-is&k=20&c=JdjECwR1VcgmB6_F1Mhr96F8G66T3pCiSn2xuSyBEwA=" type="video/mp4">
    Votre navigateur ne supporte pas les vidéos.
</video>

<?php include 'navbarpric.php'; ?>

<!-- === DÉBUT : Ajout du Framework Alpine.js === -->
<!-- On charge le framework depuis un CDN -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Composant interactif géré par Alpine.js (x-data, x-show, @click) -->
<div x-data="{ showBanner: true }" 
     x-show="showBanner" 
     style="background-color: rgba(49, 102, 250, 0.9); color: white; padding: 15px; text-align: center; margin: 15px auto; width: 80%; border-radius: 8px; font-family: sans-serif; position: relative; z-index: 10;">
    
    <span>👋 Bienvenue sur notre catalogue ! Profitez de nos nouveautés.</span>
    
    <!-- Bouton pour fermer la bannière, géré par le framework -->
    <button @click="showBanner = false" 
            style="background: transparent; border: none; color: white; position: absolute; right: 15px; top: 15px; font-weight: bold; cursor: pointer; font-size: 16px;">
        X
    </button>
</div>
<!-- === FIN : Ajout du Framework Alpine.js === -->

<!-- Formulaire de filtrage -->
<form method="GET" action="index.php">
    <div class="filter-container">
        <div class="filter">
            <label for="category">Catégorie :</label>
            <select name="category" id="category">
                <option value="">Toutes</option>
                <option value="jean" <?php echo isset($_GET['category']) && $_GET['category'] == 'jean' ? 'selected' : ''; ?>>Jean</option>
                <option value="shirt" <?php echo isset($_GET['category']) && $_GET['category'] == 'shirt' ? 'selected' : ''; ?>>Shirt</option>
                <option value="Bascket" <?php echo isset($_GET['category']) && $_GET['category'] == 'Bascket' ? 'selected' : ''; ?>>Bascket</option>
                <option value="tete" <?php echo isset($_GET['category']) && $_GET['category'] == 'tete' ? 'selected' : ''; ?>>Tête</option>
            </select>
        </div>

        <div class="filter">
            <label for="min_price">Prix Min :</label>
            <input type="number" name="min_price" id="min_price" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>" min="0" step="0.01">
        </div>

        <div class="filter">
            <label for="max_price">Prix Max :</label>
            <input type="number" name="max_price" id="max_price" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>" min="0" step="0.01">
            
        </div>
           

        <div class="filter">
        <br>    
            <button type="submit">Filtrer</button>
        </div>
        
    </div>
</form>

<!-- Liste des produits filtrés -->
<div class="products-list">
    <?php foreach ($products as $product): ?>
        <div class="products-item">
            <a href="product_details.php?id=<?php echo htmlspecialchars($product['id']); ?>">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </a>
            <h2 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="p">Prix : <?php echo htmlspecialchars($product['price']); ?> €</p>

            <!-- Bouton Ajouter aux favoris (seulement pour les utilisateurs connectés) -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="add-to-favorites.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit">Ajouter aux favoris</button>
                </form>
            <?php else: ?>
                <a href="connexion.php">
                    <button>ajouter aux favoris</button>
                </a>
            <?php endif; ?>
            <form action="avis.php" method="GET">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                <button type="submit">Avis</button>
            </form>

        </div>
    <?php endforeach; ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
