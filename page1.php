<?php
// Message à afficher sur la photo de couverture
$message = "Bienvenue dans notre boutique de mode!";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'Accueil - E-commerce Vêtements</title>
    <style>
       body {
    margin: 0;
    font-family: Arial, sans-serif;
}

/* Section de la photo de couverture */
.hero {
    position: relative;
    width: 100%;
    height: 100vh;
    background-image: url('images/image1.jpg');
    background-size: cover;
    background-position: center;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    text-align: center;
}

/* Superposition sombre */
.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); /* Légèrement plus sombre pour plus de contraste */
    z-index: 1;
}

/* Contenu du texte au-dessus de l'image */
.hero-content {
    z-index: 2;
}

/* Style pour le texte dans la section .hero */
.hero h1 {
    font-size: 3rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
    margin: 0;
}

/* Style pour le paragraphe sous le titre */
.hero p {
    font-size: 1.5rem;
    margin-top: 10px;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
}
@media (max-width: 768px) {
    .hero h1 {
        font-size: 2rem;
    }
    .hero p {
        font-size: 1.2rem;
    }
}

    </style>
</head>
<body>
    <?php  include 'navbarpric.php' ;?><br><br><br>

    <!-- Section de la photo de couverture -->
    <section class="hero">
        <div class="hero-content">
            <h1><?php echo $message; ?></h1>
            <p>Découvrez notre collection exclusive de vêtements tendance.</p>
        </div>
    </section>
    <?php  include 'footer.php' ;?>

</body>
</html>
