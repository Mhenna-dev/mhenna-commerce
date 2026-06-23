<?php


// Calculer le nombre total d'articles dans le panier
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity']; // Additionner les quantités de chaque produit
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="styles3.css">
    <style>
        /* Style du nombre d'articles dans le panier */
    .cart-count {
        display: inline-block;
        background-color: red;  /* Couleur de fond du cercle */
        color: white;            /* Couleur du texte */
        font-size: 12px;         /* Taille du texte */
        font-weight: bold;       /* Poids du texte */
        width: 20px;             /* Largeur du cercle */
        height: 20px;            /* Hauteur du cercle */
        border-radius: 50%;      /* Rendre le div circulaire */
        text-align: center;      /* Centrer le texte à l'intérieur du cercle */
        line-height: 20px;       /* Centrer verticalement le texte */
        margin-left: 5px;        /* Espacement à gauche pour éviter que le cercle touche le texte */
    }

    </style>
</head>
<body>
<div class="navbar">
    <div class="navbar-left">
        <img src="images/logo.jpg" alt="Logo" class="navbar-logo">
    </div>

    <!-- Barre de recherche -->
    <div class="navbar-search">
        <input id="searchBar" placeholder="Rechercher un produit..." onkeyup="filterProducts()">
    </div>

    <div class="navbar-right">
        <?php if (isset($_SESSION['username'])): ?> <!-- Si l'utilisateur est connecté -->
            
            <a href="index.php#produits" class="nav">Produits</a> <!-- Lien vers la section des produits -->
            <a href="mon_compte.php" class="nav">Mon Compte</a> <!-- Lien vers la page Mon Compte -->
            <a href="logout.php" class="nav">Déconnexion</a> <!-- Lien pour se déconnecter -->
            <a href="cart.php" class="nav">Panier 
                <?php if ($cart_count > 0): ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span> <!-- Afficher le nombre d'articles dans un cercle -->
                <?php else: ?>
                    <span class="cart-count">0</span> <!-- Si le panier est vide -->
                <?php endif; ?>
            </a>
        <?php else: ?>
            <a href="page1.php" class="nav">Accueil</a>
            <a href="index.php" class="nav">Produits</a>
            <a href="connexion.php" class="nav">Se connecter</a>
            <a href="inscription.php" class="nav">S'inscrire</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
