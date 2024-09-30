<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaOnline - Inscription Réussie</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('/images/jar-2338584_1280.jpeg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            position: relative;
            text-align: center;
            color: white;
        }

        .logo {
            position: absolute;
            top: 15px;
            left: 15px;
            max-width: 150px;
        }

        .slogan {
            position: absolute;
            top: 170px;
            left: 20px;
            color: black;
            font-size: 16px;
            font-weight: bold;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .nav-menu {
            position: absolute;
            top: 20px;
            right: 50px;
            display: flex;
            gap: 10px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .nav-menu a {
            color: black;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .nav-menu a:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .success-container {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            margin-top: 80px;
        }

        .success-message {
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
            color: #2C3E50; /* Couleur plus sombre pour le texte */
        }

        .info-text {
            margin-top: 20px;
            font-size: 20px;
            color: #34495E; /* Couleur légèrement plus sombre */
            margin-bottom: 30px;
        }

        .button {
            padding: 15px 30px;
            font-size: 18px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            background-color: #C0392B; /* Couleur du bouton */
        }

        .button:hover {
            background-color: #D98880; /* Couleur du bouton au survol */
        }
    </style>
</head>
<body>
    <div class="nav-menu">
        <a href="{{ url('/') }}">Accueil</a>
        <a href="{{ url('/contact') }}">Contact</a>
    </div>
    <img src="/logo/caduceus-2029254_640.jpeg" alt="Logo" class="logo">
    <div class="slogan">PHARMA-ONLINE</div>
    
    <div class="success-container">
        <div class="success-message">Votre compte a été créé avec succès !</div>
        <div class="info-text">Vous pouvez maintenant vous connecter pour rechercher les produits que vous souhaitez acheter et connaître la pharmacie la plus proche.</div>
        <a href="{{ url('/login') }}" class="button">SE CONNECTER</a>
    </div>
</body>
</html>
