<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaOnline - Recherche Produits</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('/images/jar-2338584_1280.jpeg');
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
            top: 170px;
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
            transition: background-color 0.3s ease;
            margin: 10px;
            text-decoration: none;
        }

        #create-account {
            background-color: #28A745;
        }

        #create-account:hover {
            background-color: #58D68D;
        }

        #login {
            background-color: #C0392B;
        }

        #login:hover {
            background-color: #D98880;
        }

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
        <a href="{{ url('/') }}">Accueil</a>
        <a href="{{ url('/contact') }}">Contact</a>
    </div>
    <img src="/logo/caduceus-2029254_640.jpeg" alt="Logo" class="logo">
    <div class="slogan">PHARMA-ONLINE</div>
    <div class="container">
        <a href="{{ url('/register') }}" class="button" id="create-account">CRÉER UN COMPTE</a>
        <a href="{{ url('/login') }}" class="button" id="login">SE CONNECTER</a>
        <div class="info-text">Accédez à vos produits pharmaceutiques en quelques clics</div>
    </div>
</body>
</html>
