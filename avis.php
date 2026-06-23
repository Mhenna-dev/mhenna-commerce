<?php
session_start();
include 'data.php';

$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer l'ID du produit depuis l'URL
if (!isset($_GET['id'])) {
    echo "Produit introuvable.";
    exit;
}
$product_id = $_GET['id'];

// Récupérer les infos du produit
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Produit introuvable.";
    exit;
}

// Soumission de l'avis
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user_id']) && !empty($_POST['comment'])) {
    $user_id = $_SESSION['user_id'];
    $comment = htmlspecialchars($_POST['comment']);
    $created_at = date("Y-m-d H:i:s");

    // Insertion
    $stmt = $pdo->prepare("INSERT INTO avis (product_id, user_id, comment, created_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$product_id, $user_id, $comment, $created_at]);
}

// Récupération des avis
$stmt = $pdo->prepare("SELECT avis.comment, avis.created_at, users.username FROM avis JOIN users ON avis.user_id = users.id WHERE avis.product_id = ? ORDER BY avis.created_at DESC");
$stmt->execute([$product_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction d'analyse basique du commentaire
function getStarRating($comment) {
    $positiveWords = ['excellent', 'parfait', 'très bon', 'génial', 'super', 'top'];
    $negativeWords = ['nul', 'horrible', 'mauvais', 'déçu', 'dommage'];
    $score = 3;

    foreach ($positiveWords as $word) {
        if (stripos($comment, $word) !== false) $score++;
    }
    foreach ($negativeWords as $word) {
        if (stripos($comment, $word) !== false) $score--;
    }

    return max(1, min($score, 5));
}

function displayStars($rating) {
    $stars = '';
    for ($i = 0; $i < 5; $i++) {
        $stars .= $i < $rating ? '★' : '☆';
    }
    return "<span class='stars'>$stars</span>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Avis sur <?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="avis.css">
</head>
<body>
    <div class="container">
        <div class="product-info">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        </div>

        <div class="reviews-section">
            <h2>Avis des clients</h2>
            <?php if (empty($reviews)): ?>
                <p>Aucun avis pour ce produit.</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review">
                        <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                        <em>Le<?php echo date('d/m/Y', strtotime($review['created_at'])); ?> á <?php echo date('H:i', strtotime($review['created_at'])); ?></em>
                        <p><?php echo htmlspecialchars($review['comment']); ?></p>
                        <?php echo displayStars(getStarRating($review['comment'])); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="form-section">
                <h2>Laisser un avis</h2>
                <form method="POST">
                    <textarea name="comment" required placeholder="Votre avis ici..."></textarea><br>
                    <button type="submit">Envoyer</button>
                </form>
            </div>
        <?php else: ?>
            <p class="login-warning">Veuillez vous <a href="connexion.php">connecter</a> pour laisser un avis.</p>
        <?php endif; ?>
    </div>
</body>
</html>
