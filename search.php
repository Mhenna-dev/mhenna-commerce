<?php
// search.php - Traitement de la recherche des produits avec PDO

// Connexion à la base de données
include('data.php');  // Assurez-vous que le fichier db.php utilise PDO pour la connexion

// Vérifier si une recherche a été effectuée
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];

    // Préparer la requête SQL pour rechercher dans la table "products" avec PDO
    $sql = "SELECT * FROM products WHERE name LIKE :searchTerm";
    
    // Préparer la requête
    $stmt = $conn->prepare($sql);
    
    // Ajouter les pourcentages autour du terme de recherche (pour effectuer une recherche LIKE)
    $searchTerm = "%" . $searchTerm . "%";
    
    // Lier le paramètre de la recherche
    $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
    
    // Exécuter la requête
    $stmt->execute();
    
    // Récupérer les résultats
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche</title>
    <link rel="stylesheet" href="styles3.css">
</head>
<body>
    <div class="navbar">
        <div class="navbar-left">
            <img src="images/logo.jpg" alt="Logo" class="navbar-logo">
        </div>

        <div class="navbar-search">
            <form action="search.php" method="get">
                <input id="searchBar" type="text" name="search" placeholder="Rechercher un produit..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <div class="navbar-right">
            <!-- Ajouter ici les liens de votre navbar -->
        </div>
    </div>

    <div class="products-list">
        <?php
        // Afficher les produits trouvés
        if (count($result) > 0) {
            foreach ($result as $product) {
                echo "<div class='product'>
                        <h3 class='product-name'>" . htmlspecialchars($product['name']) . "</h3>
                        <p class='product-description'>" . htmlspecialchars($product['description']) . "</p>
                        <p class='product-price'>" . htmlspecialchars($product['price']) . "€</p>
                      </div>";
            }
        } else {
            echo "<p>Aucun produit trouvé.</p>";
        }
        ?>
    </div>

</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn = null;
?>
