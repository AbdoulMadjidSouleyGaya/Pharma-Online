<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaOnline</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('{{ asset('images/jar-2338584_1280.jpeg') }}');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            position: relative;
        }

        .logo {
            position: absolute;
            top: 15px;
            left: 15px;
            max-width: 150px;
        }

        .slogan {
            position: absolute;
            top: 170px;   /* ton positionnement souhaité sous le logo */
            left: 20px;
            color: black;
            font-size: 16px;
            font-weight: bold;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            text-align: center;
            color: white;
            margin-top: 30px;
        }

        .button {
            padding: 20px 40px;
            font-size: 18px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.06s ease;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 6px 14px rgba(0,0,0,0.18);
        }
        .button:active { transform: translateY(1px); }

        /* Bleu = CRÉER UN COMPTE */
        #creer-compte { background-color: #3498DB; }
        #creer-compte:hover { background-color: #5DADE2; }

        /* Rouge = SE CONNECTER */
        #se-connecter { background-color: #C0392B; }
        #se-connecter:hover { background-color: #D98880; }

        .info-text {
            margin-top: 50px;
            font-size: 30px;
            font-weight: bold;
            text-transform: uppercase;
            color: black;
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
    </style>
</head>
<body>
    <div class="nav-menu">
        <a href="{{ route('home') }}">Accueil</a>
        <a href="{{ url('/contact') }}">Contact</a> <!-- remplace par ta route quand elle existera -->
    </div>

    <img src="{{ asset('images/logo.png') }}" alt="Logo de PharmaOnline" class="logo">
    <div class="slogan">PHARMA-ONLINE</div>

    <div class="container">
        <!-- BOUTON BLEU : CRÉER UN COMPTE -->
        <a href="{{ route('register') }}" class="button" id="creer-compte">CRÉER UN COMPTE</a>

        <!-- BOUTON ROUGE : SE CONNECTER -->
        <a href="{{ route('login') }}" class="button" id="se-connecter">SE CONNECTER</a>

        <div class="info-text">Fini la perte de temps à la recherche de vos produits pharmaceutiques</div>
    </div>
</body>
</html>
