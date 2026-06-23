<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalisation de L'achat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Finalisation de l'achat</h1>

  <?php if (!empty($cart_items)); ?>
     <P>vous avez <?php echo count($cart_items); ?> article dans votre panier.</p>

     <form method="post" action="process_order.php">
        <label for="address">Adresse de livraison :</label><br>
        <input for="address" id="address" name="addressé" required >

        <label for ="payment">Méthode de paiment :</label><br>
        <select id="payment" name="payment_method">
            <option value="credit_card">Carte de Credit</option>
            <option value="paypal"> Paypal</option>
        </select><br>

        <button type="submit">Finaliser l'achat</button>
     </form>
    <?php else: ?>
        <p>Votre panier est vide.</p>
    <?php endif; ?>

</body>
</html>
