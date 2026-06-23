<?php

if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}
?>
<style>
   /* =========================
   Admin Navbar - Style Amazon
   ========================= */

nav {
    background-color: #131921; /* Couleur sombre Amazon */
    padding: 15px 30px;
    text-align: center;       /* Centré */
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    margin-bottom: 20px;
}

nav a {
    color: #fff;             /* Texte blanc */
    font-weight: bold;
    margin: 0 15px;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 4px;
    transition: 0.2s;
}

nav a:hover {
    background-color: #ffd814; /* Fond jaune au hover */
    color: #131921;            /* Texte sombre */
}

hr {
    border: none;
    height: 1px;
    background-color: #ddd;
    margin: 0;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    nav {
        padding: 10px 15px;
    }

    nav a {
        display: block;
        margin: 8px 0;
    }
}


    </style>
<nav>
    <a href="admin_dashboard.php">Tableau de bord</a> |
    <a href="admin_products.php">Produits</a> |
    <a href="admin_categories.php">Catégories</a> |
    <a href="admin_orders.php">Commandes</a> |
    <a href="admin_logout.php">Déconnexion</a>
</nav>
<hr>
