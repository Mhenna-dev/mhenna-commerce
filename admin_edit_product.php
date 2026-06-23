<?php
session_start();
include 'data.php'; // connexion PDO
include 'navbar_admin.php';

// Vérifier si l'admin est connecté
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}

// Vérifier si l'ID du produit est fourni
if(!isset($_GET['id'])){
    echo "Produit non spécifié.";
    exit;
}

$id = $_GET['id'];

// Récupérer les informations du produit
$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product){
    echo "Produit introuvable.";
    exit;
}

// Traitement du formulaire
if(isset($_POST['update_product'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $colors = $_POST['colors'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $shipping_cost = $_POST['shipping_cost'];
    $carrier = $_POST['carrier'];

    // Gérer l'image si upload
    $imageName = $product['image']; // garder l'ancienne image si pas de nouvelle
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = 'images/' . basename($_FILES['image']['name']);
        move_uploaded_file($imageTmp, $imageName);
    }

    // Mettre à jour le produit
    $stmt = $conn->prepare("UPDATE products SET
        name = :name,
        image = :image,
        price = :price,
        size = :size,
        colors = :colors,
        description = :description,
        type = :type,
        category = :category,
        shipping_cost = :shipping_cost,
        carrier = :carrier
        WHERE id = :id");

    $stmt->execute([
        'name' => $name,
        'image' => $imageName,
        'price' => $price,
        'size' => $size,
        'colors' => $colors,
        'description' => $description,
        'type' => $type,
        'category' => $category,
        'shipping_cost' => $shipping_cost,
        'carrier' => $carrier,
        'id' => $id
    ]);

    $success = "Produit mis à jour avec succès !";
}
?>

<h2>Modifier le produit</h2>
<link rel="stylesheet" href="admin_style.css">

<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post" enctype="multipart/form-data">
    <label>Nom :</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br><br>

    <label>Prix :</label><br>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required><br><br>

    <label>Taille(s) (séparées par des virgules) :</label><br>
    <input type="text" name="size" value="<?= htmlspecialchars($product['size']) ?>"><br><br>

    <label>Couleur(s) (séparées par des virgules) :</label><br>
    <input type="text" name="colors" value="<?= htmlspecialchars($product['colors']) ?>"><br><br>

    <label>Description :</label><br>
    <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br><br>

    <label>Type :</label><br>
    <input type="text" name="type" value="<?= htmlspecialchars($product['type']) ?>"><br><br>

    <label>Catégorie :</label><br>
    <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>"><br><br>

    <label>Frais de livraison :</label><br>
    <input type="number" step="0.01" name="shipping_cost" value="<?= $product['shipping_cost'] ?>"><br><br>

    <label>Transporteur :</label><br>
    <input type="text" name="carrier" value="<?= htmlspecialchars($product['carrier']) ?>"><br><br>

    <label>Image :</label><br>
    <?php if($product['image']): ?>
        <img src="<?= $product['image'] ?>" width="100"><br>
    <?php endif; ?>
    <input type="file" name="image"><br><br>

    <button type="submit" name="update_product">Mettre à jour le produit</button>
</form>

<a href="admin_products.php" style="display:inline-block; margin-top:10px;">Retour à la liste des produits</a>
