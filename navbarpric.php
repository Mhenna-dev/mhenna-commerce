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

        /* Style du champ de recherche dans la navbar */
        .search-bar {
            margin-top: 10px;
            margin-right: 10px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 150px;
            
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            text-align: center;
            background-color: rgba(14, 13, 13, 0.8);
            position: relative; /* Nécessaire pour positionner les éléments à l'intérieur */
            width: 100%; /* Prend toute la largeur de la page */
            
        }

        /* Style pour la partie gauche de la navbar (logo) */
        .navbar-left {
            flex-grow: 0;
            
        }

        /* Style pour l'image du logo */
        .navbar-logo {
            height: 40px; /* Ajustez la taille du logo si nécessaire */
        }

        /* Style pour la partie droite de la navbar (liens) */
        .navbar-right {
            display: flex;
            gap: 15px; /* Espace entre les éléments */
            position: absolute; /* Pour les positionner sur le côté droit */
            right:  10px; /* Espacement du côté droit de la page */
            
        }

        /* Style pour les liens */
        .navbar-right a {
            color: white;
            text-decoration: none;
            font-size: 14px; /* Taille des liens réduite */
            padding: 8px 0px; /* Un peu de padding pour rendre les boutons plus visibles */
            background-color: rgba(49, 102, 250, 0.8); /* Fond bleu pour les boutons */
            border-radius: 5px; /* Coins arrondis */
            transition: background-color 0.3s ease; /* Animation de changement de fond */
        }

        /* Effet de survol pour les liens */
        .navbar-right a:hover {
            background-color: #0056b3; /* Changement de fond en bleu plus foncé lors du survol */
            text-decoration: none;
        }

        /* Style pour l'élément connecté/déconnecté */
        .navbar-right a.logout {
            background-color: #dc3545; /* Fond rouge pour le bouton de déconnexion */
        }

        /* Effet de survol pour le bouton de déconnexion */
        .navbar-right a.logout:hover {
            background-color: #c82333;
        }
        .nav {
            width: 120px;
            margin:auto;
        }

       

        .f{
            float: left ;
            width: 1000px;
        }
        .o{
            width: 2000px;
            float: left;
        }

        .l{
            float: left ;
            width: 200px;
            height: 20px;
        }
        .navbar-search {
            flex-grow: 1;
            text-align: center;
            
        }
        
        #searchBar {
            width: 30%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin-right: 45%;
            
        }
        
  
        
    </style>
</head>
<body>
<div class="navbar">
    <div class="navbar-left">
        <img src="images/logo.jpg" alt="Logo" class="navbar-logo">
    </div>

    <div class="navbar-right">
        <!-- Formulaire de recherche (visible uniquement dans la navbar) -->
        <form action="index.php" method="GET" style="display: flex; align-items: center;">
            <input type="text" name="search" class="search-bar" placeholder="Rechercher un produit..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="search-bar">Rechercher</button>
        </form>

        <?php if (isset($_SESSION['username'])): ?> <!-- Si l'utilisateur est connecté -->
            <a href="index.php#produits" class="nav">Produits</a> <!-- Lien vers la section des produits -->
            <a href="mon_compte.php" class="nav">Mon Compte</a> <!-- Lien vers la page Mon Compte -->
            <a href="logout.php" class="nav">Déconnexion</a> <!-- Lien pour se déconnecter -->
            <a href="favorites.php" class="nav"> Mes Favories</a>
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
