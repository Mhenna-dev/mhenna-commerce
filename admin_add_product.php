<?php
session_start();
include 'data.php';
include 'navbar_admin.php';

if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}

if(isset($_POST['add_product'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $shipping_cost = $_POST['shipping_cost'];
    $carrier = $_POST['carrier'];

    // Image du produit
    $imageName = "";
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = 'images/' . basename($_FILES['image']['name']);
        move_uploaded_file($imageTmp, $imageName);
    }

    // Ajouter le produit dans products
    $stmt = $conn->prepare("INSERT INTO products 
        (name, image, price, description, type, category, shipping_cost, carrier)
        VALUES (:name, :image, :price, :description, :type, :category, :shipping_cost, :carrier)");
    $stmt->execute([
        'name' => $name,
        'image' => $imageName,
        'price' => $price,
        'description' => $description,
        'type' => $type,
        'category' => $category,
        'shipping_cost' => $shipping_cost,
        'carrier' => $carrier
    ]);

    // Récupérer l'ID du produit ajouté
    $product_id = $conn->lastInsertId();

    // Ajouter les stocks dans product_stock
    // Les tailles et couleurs sont séparées par des virgules dans le formulaire
    $sizes = explode(',', $_POST['size']);      // Exemple: "S,M,L"
    $colors = explode(',', $_POST['colors']);   // Exemple: "Noir,Blanc"

    foreach($sizes as $size){
        $size = trim($size);
        foreach($colors as $color){
            $color = trim($color);
            $stmtStock = $conn->prepare("INSERT INTO product_stock (product_id, color, size, quantity, image) 
                                        VALUES (:product_id, :color, :size, 0, :image)");
            $stmtStock->execute([
                'product_id' => $product_id,
                'color' => $color,
                'size' => $size,
                'image' => $imageName
            ]);
        }
    }

    $success = "Produit ajouté avec succès !";
}
?>

<h2>Ajouter un nouveau produit</h2>
<link rel="stylesheet" href="admin_style.css">

<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post" enctype="multipart/form-data">
    <label>Nom :</label><br>
    <input type="text" name="name" required><br><br>

    <label>Prix :</label><br>
    <input type="number" step="0.01" name="price" required><br><br>

    <label>Description :</label><br>
    <textarea name="description"></textarea><br><br>

    <label>Type :</label><br>
    <input type="text" name="type"><br><br>

    <label>Catégorie :</label><br>
    <input type="text" name="category"><br><br>

    <label>Taille(s) (séparées par des virgules) :</label><br>
    <input type="text" name="size" placeholder="S,M,L"><br><br>

    <label>Couleur(s) (séparées par des virgules) :</label><br>
    <input type="text" name="colors" placeholder="Noir,Blanc"><br><br>

    <label>Frais de livraison :</label><br>
    <input type="number" step="0.01" name="shipping_cost"><br><br>

    <label>Transporteur :</label><br>
    <input type="text" name="carrier"><br><br>

    <label>Image :</label><br>
    <input type="file" name="image"><br><br>

    <button type="submit" name="add_product">Ajouter le produit</button>
</form>

<a href="admin_products.php" style="display:inline-block; margin-top:10px;">Retour à la liste des produits</a>
