<?php
session_start();
include 'data.php'; // connexion PDO
include 'navbar_admin.php';

if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}

// Supprimer une commande
if(isset($_GET['delete'])){
    $order_id = $_GET['delete'];

    // Supprimer les produits liés
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = :order_id");
    $stmt->execute(['order_id' => $order_id]);

    // Supprimer la commande
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = :id");
    $stmt->execute(['id' => $order_id]);

    header("Location: admin_orders.php");
    exit;
}

// Récupérer toutes les commandes
$stmt = $conn->prepare("SELECT * FROM orders ORDER BY created_at DESC");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statuts possibles
$statuses = ['en attente', 'en cours', 'expédiée', 'livrée', 'annulée'];
?>

<h2>Gestion des commandes</h2>
<link rel="stylesheet" href="admin_style.css">

<table border="1" cellpadding="10" cellspacing="0">
<tr>
    <th>ID</th>
    <th>Client</th>
    <th>Montant</th>
    <th>Statut</th>
    <th>Actions</th>
    <th>Paiement</th>
</tr>

<?php foreach($orders as $order): ?>
<tr>
    <td><?= $order['id'] ?></td>
    <td><?= htmlspecialchars($order['full_name']) ?></td>
    <td><?= number_format($order['total_price'],2) ?> €</td>
    <td>
        <select class="status-select" data-id="<?= $order['id'] ?>">
            <?php foreach($statuses as $status): ?>
                <option value="<?= $status ?>" <?= $status == $order['status'] ? 'selected' : '' ?>><?= $status ?></option>
            <?php endforeach; ?>
        </select>
        <span class="status-msg" style="color:green;"></span>
    </td>
    <td>
        <a href="admin_view_order.php?id=<?= $order['id'] ?>" style="color:blue;">Voir</a> |
        <a href="admin_orders.php?delete=<?= $order['id'] ?>" style="color:red;" onclick="return confirm('Supprimer cette commande ?')">Supprimer</a>
    </td>
    <td>
        <!-- Afficher "Payé" si le statut est payé -->
        <?php if($order['status'] === 'payé'): ?>
            <span style="color:green;font-weight:bold;">Payé</span>
        <?php else: ?>
            <span style="color:gray;">Non payé</span>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>

</table>

<!-- JS pour AJAX -->
<script>
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function(){
        const orderId = this.getAttribute('data-id');
        const status = this.value;
        const msg = this.nextElementSibling;

        fetch('admin_update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `order_id=${orderId}&status=${encodeURIComponent(status)}`
        })
        .then(response => response.text())
        .then(data => {
            msg.textContent = " Statut mis à jour !";
            setTimeout(() => { msg.textContent = ""; }, 2000);
        })
        .catch(err => {
            msg.textContent = " Erreur !";
            setTimeout(() => { msg.textContent = ""; }, 2000);
        });
    });
});
</script>
