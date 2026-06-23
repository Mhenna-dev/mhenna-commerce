<?php
session_start();
include 'data.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vous devez être connecté pour ajouter un produit au panier.'); window.location.href = 'connexion.php';</script>";
    exit;
}

function getCssColor($color) {
    $mapping = [
        'noir'    => 'black',
        'blanc'   => 'white',
        'rouge'   => 'red',
        'bleu'    => 'blue',
        'vert'    => 'green',
        'jaune'   => 'yellow',
        'orange'  => 'orange',
        'violet'  => 'purple',
        'gris'    => 'gray',
    ];
    $colorLower = strtolower($color);
    return isset($mapping[$colorLower]) ? $mapping[$colorLower] : $color;
}

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération de l'ID du produit depuis l'URL
    $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($productId <= 0) {
        echo "Produit non trouvé.";
        exit;
    }

    // Récupération des informations principales du produit
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Produit non trouvé.";
        exit;
    }

    // Récupérer les variantes (couleur, taille, quantité, image) depuis la table product_stock
    $sqlStock = "SELECT color, size, quantity, image FROM product_stock WHERE product_id = :pid";
    $stmtStock = $pdo->prepare($sqlStock);
    $stmtStock->execute([':pid' => $productId]);
    $variants = $stmtStock->fetchAll(PDO::FETCH_ASSOC);

    // Construire un tableau associatif pour le stock et des listes pour couleurs et tailles
    $stockVariants = [];
    $colors = [];
    $sizes = [];

    foreach ($variants as $variant) {
        $c = $variant['color'];
        $s = $variant['size'];
        $q = $variant['quantity'];
        $img = $variant['image'];

        if (!isset($stockVariants[$c])) {
            $stockVariants[$c] = [];
        }
        $stockVariants[$c][$s] = ['quantity' => $q, 'image' => $img];

        if (!in_array($c, $colors)) {
            $colors[] = $c;
        }
        if (!in_array($s, $sizes)) {
            $sizes[] = $s;
        }
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']); ?></title>
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    ;
}



/* Styles principaux pour le corps de la page */
body {
    font-family: 'Roboto', sans-serif;
    background-color: #f9f9f9;
    color: #fff; /* Texte clair pour contraster avec le fond sombre */
    line-height: 1.6;
    padding: 20px;
}

/* Conteneur principal pour l'alignement du contenu */
.hh {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    min-height: 100vh;
    text-align: center;
    max-width: 1200px;
    margin: 0 auto;
    background-color: rgba(255, 255, 255, 0.8); /* Fond légèrement transparent pour laisser transparaître l'arrière-plan vidéo */
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 40px;
}

/* Styles pour les images du produit */
img {
    max-width: 400px;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Titres principaux */
h1 {
    font-size: 28px;
    font-weight: 600;
    color: #fff; /* Couleur blanche pour le texte sur fond foncé */
    margin-bottom: 20px;
}

/* Paragraphe pour le prix */
p {
    font-size: 20px;
    color: #f1c40f; /* Jaune clair pour attirer l'attention et bien contraster */
    margin-bottom: 20px;
}

/* Styles pour les boutons de sélection de couleur */
.color-button {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin: 8px;
    cursor: pointer;
    border: 2px solid #ddd;
    transition: all 0.3s;
}

.color-button:hover {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

.color-button.selected {
    border-color: #2c3e50;
    box-shadow: 0 0 12px rgba(0, 0, 0, 0.3);
}

/* Styles pour les boutons de sélection de taille */
.size-button {
    padding: 12px 20px;
    margin: 5px;
    border: 2px solid #ccc;
    cursor: pointer;
    border-radius: 5px;
    font-size: 18px;
    background-color: #fff;
    transition: all 0.3s;
}

.size-button:hover {
    background-color: #f0f0f0;
    border-color: #2c3e50;
}

.size-button.selected {
    background-color: #f0f0f0;
    border-color: #2c3e50;
    font-weight: bold;
}

/* Informations sur le stock */
.stock-info {
    margin-top: 15px;
    font-size: 16px;
    color: #e74c3c;
    font-weight: bold;
}

.quantity-selection {
    margin-top: 20px;
}

.quantity-selection input {
    width: 60px;
    padding: 8px;
    font-size: 16px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 5px;
    outline: none;
    transition: all 0.3s;
}

.quantity-selection input:focus {
    border-color: #2c3e50;
}

/* Bouton d'ajout au panier */
.p {
    background-color: #2ecc71;
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 30px;
    cursor: pointer;
    font-size: 18px;
    margin-top: 20px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.p:hover {
    background-color: #27ae60;
    transform: scale(1.05);
}

/* Ajouter un effet pour les boutons avec une animation douce */
button:active, .p:active {
    transform: scale(0.98);
}

/* Pour la forme du formulaire */
form {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px;
}

/* Étiquette du formulaire */
label {
    font-size: 18px;
    color: gray; /* Texte en blanc pour le formulaire */
    margin-bottom: 10px;
    font-weight: 500;
}
</style>
</head>
<body>

    
    Votre navigateur ne supporte pas les vidéos.
</video>
    <div class="hh">
    <header>
        <h1><?= htmlspecialchars($product['name']); ?></h1>
    </header>
    <main>
        <div class="product-details">
            <img id="product-image" src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" style="max-width:300px;">
            <p><strong>Prix :</strong> <span id="price"><?= htmlspecialchars($product['price']); ?> €</span></p>

            <form method="post" action="add_to_cart.php">
                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">

                <div class="size-selection">
                    <label><strong>Taille :</strong></label><br>
                    <?php foreach ($sizes as $size): ?>
                        <button type="button" class="size-button" data-size="<?= htmlspecialchars($size); ?>" onclick="selectSize('<?= htmlspecialchars($size); ?>')">
                            <?= htmlspecialchars($size); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="color-selection">
                    <label><strong>Couleur :</strong></label><br>
                    <?php foreach ($colors as $color): ?>
                        <button type="button" class="color-button" 
                                style="background-color: <?= htmlspecialchars(getCssColor($color)); ?>;" 
                                data-color="<?= htmlspecialchars($color); ?>" 
                                onclick="selectColor('<?= htmlspecialchars($color); ?>')">
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="stock-info" id="stock-info">
                    Sélectionnez une couleur et une taille pour voir le stock disponible.
                </div>

                <div class="quantity-selection">
                    <label for="quantity"><strong>Quantité :</strong></label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" required onchange="updatePrice()">
                </div>

                <input type="hidden" name="size" id="selected-size" value="">
                <input type="hidden" name="color" id="selected-color" value="">

                
              
                <button class="p" type="submit" name="add_to_cart">Ajouter au panier</button>
            </form>
        </div>
    </main>

    <script>
    var stockVariants = <?= json_encode($stockVariants); ?>;
    var selectedSize = '';
    var selectedColor = '';
    var unitPrice = <?= htmlspecialchars($product['price']); ?>;

    function selectSize(size) {
        selectedSize = size;
        document.getElementById('selected-size').value = size;
        document.querySelectorAll('.size-button').forEach(function(btn) {
            btn.classList.remove('selected');
        });
        var btn = document.querySelector("[data-size='" + size + "']");
        if (btn) btn.classList.add('selected');
        updateStockInfo();
    }

    function selectColor(color) {
        selectedColor = color;
        document.getElementById('selected-color').value = color;
        document.querySelectorAll('.color-button').forEach(function(btn) {
            btn.classList.remove('selected');
        });
        var btn = document.querySelector("[data-color='" + color + "']");
        if (btn) btn.classList.add('selected');
        updateStockInfo();
    }

    function updateStockInfo() {
        var infoDiv = document.getElementById('stock-info');
        var quantityInput = document.getElementById('quantity');

        if (selectedColor && selectedSize) {
            var available = 0;
            var image = "";
            if (stockVariants[selectedColor] && stockVariants[selectedColor][selectedSize] !== undefined) {
                available = stockVariants[selectedColor][selectedSize].quantity;
                image = stockVariants[selectedColor][selectedSize].image;
            }
            infoDiv.textContent = "Stock disponible : " + available;
            quantityInput.max = available;
            if (available === 0) {
                quantityInput.value = 0;
            } else if (quantityInput.value === "0") {
                quantityInput.value = 1;
            }

            if (available > 0 && available <= 2) {
                alert("Attention : il ne reste que " + available + " unités en stock pour cette combinaison.");
            }

            updatePrice();
            updateProductImage(image);
        } else {
            infoDiv.textContent = "Sélectionnez une couleur et une taille pour voir le stock disponible.";
            quantityInput.removeAttribute('max');
        }
    }

    function updatePrice() {
        var quantity = document.getElementById('quantity').value;
        var totalPrice = unitPrice * quantity;
        document.getElementById('price').textContent = totalPrice.toFixed(2) + ' €';
    }

    function updateProductImage(image) {
        if (image) {
            document.getElementById('product-image').src = image;
        }
    }
    </script>
    </div>
</body>
</html>
