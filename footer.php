<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Reset des marges et styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .footer {
            background-color: #1a1a1a;
            color: #4caf50;
            text-align: center;
            padding: 20px 10px;
            font-size: 14px;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.3);
        }

        .footer p {
            margin: 5px 0;
            color: #4caf50;
        }

        .footer a {
            color: #4caf50;
            text-decoration: none;
            font-weight: bold;
            padding: 0 8px;
        }

        .footer a:hover {
            color: #81c784;
            text-decoration: underline;
        }

        .footer .social-icons {
            margin-top: 10px;
        }

        .footer .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            margin: 0 5px;
            background-color: #4caf50;
            color: white;
            border-radius: 50%;
            font-size: 20px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .footer .social-icons a:hover {
            background-color: #81c784;
        }
    </style>
    <title>Footer en bas</title>
</head>
<body>

    <br><br><br><br>
  
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> M'henna SAADI. Tous droits réservés.</p>
        <p>
            <a href="privacy.php">Politique de confidentialité</a> |
            <a href="terms.php">Conditions d'utilisation</a>
        </p>
        <!-- Icônes des réseaux sociaux -->
        <div class="social-icons">
            <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
    </div>

</body>
</html>
