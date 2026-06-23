<?php
session_start();
include 'data.php'; // connexion PDO
include 'navbar_admin.php';

// Vérifier si l'admin est connecté
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}

// Supprimer un produit si demandé
if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    // Supprimer le produit
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);

    // Supprimer aussi le stock associé
    $stmt2 = $conn->prepare("DELETE FROM product_stock WHERE product_id = :id");
    $stmt2->execute(['id' => $id]);

    header("Location: admin_products.php");
    exit;
}

// Récupérer tous les produits
$stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Gestion des produits</h2>
<link rel="stylesheet" href="admin_style.css">
<!-- Bouton Ajouter un produit -->
<a href="admin_add_product.php" style="display:inline-block; padding:10px 15px; background-color:green; color:white; text-decoration:none; margin-bottom:10px;">Ajouter un produit</a>

<table border="1" cellpadding="10" cellspacing="0">
<tr>
    <th>ID</th>
    <th>Nom</th>
    <th>Image</th>
    <th>Prix</th>
    <th>Catégorie</th>
    <th>Actions</th>
</tr>

<?php foreach($products as $row): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td>
        <?php if($row['image']): ?>
            <img src="<?= $row['image'] ?>" width="50">
        <?php else: ?>
            Pas d'image
        <?php endif; ?>
    </td>
    <td><?= $row['price'] ?> €</td>
    <td><?= htmlspecialchars($row['category']) ?></td>
    <td>
        <a href="admin_edit_product.php?id=<?= $row['id'] ?>" style="color:blue;">Modifier</a> |
        <a href="admin_products.php?delete=<?= $row['id'] ?>" style="color:red;" onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
    </td>
</tr>
<?php endforeach; ?>

</table>
