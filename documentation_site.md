# Documentation du Projet E-commerce

Ce document regroupe le **Guide d'Utilisation** destiné aux utilisateurs finaux et aux administrateurs, ainsi que le **Guide Technique** destiné aux développeurs.

---

# 1. Guide d'Utilisation

## 1.1 Côté Client (Utilisateurs)

### Inscription et Connexion
* **Inscription** : Rendez-vous sur la page `S'inscrire` (`inscription.php`) pour créer un compte en renseignant vos informations.
* **Connexion** : Utilisez la page `Se connecter` (`connexion.php`) pour accéder à votre espace personnel.
* **Déconnexion** : Un bouton "Déconnexion" est disponible dans la barre de navigation une fois connecté.

### Navigation et Recherche
* **Catalogue** : La page d'accueil (`index.php`) affiche la liste des produits.
* **Filtres** : Vous pouvez filtrer les produits par :
  * Catégorie (Jean, Shirt, Basket, Tête, etc.)
  * Prix minimum et maximum.
  * Recherche textuelle (par nom de produit).

### Panier et Commandes
* **Ajout au panier** : Cliquez sur un produit pour voir ses détails (`product_details.php`), puis ajoutez-le au panier. La quantité s'affiche dans une bulle rouge sur la barre de navigation.
* **Favoris** : Les utilisateurs connectés peuvent ajouter des produits à leurs favoris pour les retrouver plus tard (`favorites.php`).
* **Passer commande** : Dans le panier (`cart.php`), vous pouvez valider votre commande en remplissant les informations de livraison et de paiement (`chekout.php`).

### Avis
* Il est possible de laisser un avis sur un produit (`avis.php`) pour partager son expérience avec les autres utilisateurs.

## 1.2 Côté Administrateur

### Accès au tableau de bord
L'administrateur dispose d'une interface dédiée (`admin_login.php`) pour se connecter et gérer le site via le tableau de bord (`admin_dashboard.php`).

### Gestion de la boutique
* **Produits** : Ajout, modification et suppression des produits du catalogue (`admin_products.php`, `admin_add_product.php`, `admin_edit_product.php`).
* **Catégories** : Gestion des catégories de produits (`admin_categories.php`).
* **Commandes** : Suivi et mise à jour du statut des commandes des clients (`admin_orders.php`, `admin_update_status.php`, `admin_view_order.php`).

---

# 2. Guide Technique

## 2.1 Architecture du Projet
Le projet est développé de manière structurée sans framework lourd (Vanilla PHP) avec une approche procédurale classique. Il repose sur les standards du web : PHP pour le traitement des données côté serveur, HTML/CSS/JS pour le rendu côté client, et MySQL pour le stockage des données.

## 2.2 Technologies Utilisées
* **Backend** : PHP 8+
* **Frontend** : HTML5, CSS3, JavaScript (Vanilla)
* **Base de données** : MySQL
* **Bibliothèques externes** :
  * **PHPMailer** : Utilisé pour la gestion et l'envoi fiable d'e-mails (notifications de commandes, réinitialisation de mots de passe, etc.).
  * **Alpine.js** : Micro-framework JavaScript inclus récemment pour ajouter des composants interactifs légers (comme des bannières refermables) sans surcharger le DOM.

## 2.3 Structure de la Base de Données (Entités principales)
L'application s'articule autour des entités suivantes (la configuration de connexion se trouve dans `data.php`) :
* `users` : Gestion des comptes clients et de l'administration.
* `products` : Informations des produits (id, name, image, price, category).
* `orders` : Enregistrement des commandes des clients.
* `favorites` : Liaison entre les utilisateurs et leurs produits favoris.
* `reviews` / `avis` : Avis laissés par les clients sur les produits.

## 2.4 Sécurité et Bonnes Pratiques
* **Prévention des Injections SQL** : Les communications avec la base de données s'effectuent via l'interface **PDO (PHP Data Objects)**. L'utilisation systématique de requêtes préparées (ex: `$stmt = $pdo->prepare(...)`) garantit la sécurisation des entrées utilisateurs.
* **Gestion des Sessions** : L'accès aux espaces sécurisés (panier, favoris, mon compte, administration) est contrôlé par le mécanisme natif de sessions PHP (`session_start()` et `$_SESSION`).

## 2.5 Arborescence Principale (Fichiers clés)
* `index.php` : Point d'entrée principal, affichage du catalogue avec les formulaires de filtres.
* `data.php` : Fichier de configuration contenant les paramètres de connexion à la base de données.
* `/phpmailer/` : Dossier contenant la librairie PHPMailer (installée via `composer.json`).
* `admin_*.php` : Ensemble des vues et contrôleurs réservés à l'administration du site.
* `navbarpric.php`, `footer.php` : Composants de page partagés.
* Fichiers de style (`*.css`) : Le CSS est organisé par vues ou fonctionnalités pour faciliter la maintenance (`style.css`, `stylescart.css`, `admin_style.css`, etc.).
* `script.js` : Animations et scripts frontend spécifiques (ex: gestion du style de la navbar au défilement).
