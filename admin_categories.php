<?php
session_start();
include 'navbar_admin.php';
include 'data.php'; // connexion PDO

/* =========================
   AJOUT D’UNE CATÉGORIE
   ========================= */
if (isset($_POST['add'])) {
    $category = trim($_POST['category_name']);

    if (!empty($category)) {
        // Vérifier si la catégorie existe déjà
        $check = $conn->prepare("
            SELECT COUNT(*) 
            FROM products 
            WHERE category = :cat
        ");
        $check->execute(['cat' => $category]);

        if ($check->fetchColumn() == 0) {
            // On crée la catégorie en l’attachant à un produit "fictif"
            $stmt = $conn->prepare("
                UPDATE products 
                SET category = :cat 
                WHERE category IS NULL 
                LIMIT 1
            ");
            $stmt->execute(['cat' => $category]);

            $success = "Catégorie '$category' ajoutée avec succès.";
        } else {
            $error = "Cette catégorie existe déjà.";
        }
    }
}

/* =========================
   SUPPRESSION D’UNE CATÉGORIE
   ========================= */
if (isset($_GET['delete'])) {
    $cat = $_GET['delete'];

    $stmt = $conn->prepare("
        UPDATE products 
        SET category = NULL 
        WHERE category = :cat
    ");
    $stmt->execute(['cat' => $cat]);

    header("Location: admin_categories.php");
    exit;
}

/* =========================
   RÉCUPÉRATION DES CATÉGORIES
   ========================= */
$stmt = $conn->prepare("
    SELECT DISTINCT category 
    FROM products 
    WHERE category IS NOT NULL
    ORDER BY category
");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="admin_style.css">

<h2>Ajouter une catégorie</h2>

<?php if (!empty($success)): ?>
    <p style="color:green;"><?= $success ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>

<form method="post">
    <input type="text" name="category_name" placeholder="Nom de la catégorie" required>
    <button type="submit" name="add">Ajouter</button>
</form>

<h2>Liste des catégories</h2>

<ul>
<?php foreach ($categories as $row): ?>
    <li>
        <?= htmlspecialchars($row['category']) ?>
        <a href="admin_categories.php?delete=<?= urlencode($row['category']) ?>"
           onclick="return confirm('Supprimer cette catégorie ?')">
           Supprimer
        </a>
    </li>
<?php endforeach; ?>
</ul>
