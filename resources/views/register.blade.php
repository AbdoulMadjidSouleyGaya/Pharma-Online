<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaOnline - Créer un Compte</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            max-width: 150px;
        }

        .slogan {
            position: absolute;
            top: 170px;
            left: 20px;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        .form-group {
            margin: 20px 0;
        }

        .form-group input {
            padding: 15px;
            font-size: 16px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #3498DB;
            outline: none;
        }

        .submit-button {
            padding: 15px 30px;
            font-size: 18px;
            color: white;
            border: none;
            border-radius: 5px;
            background-color: #28A745;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .submit-button:hover {
            background-color: #58D68D;
            transform: translateY(-2px);
        }

        .nav-menu {
            position: absolute;
            top: 20px;
            right: 50px;
            display: flex;
            gap: 10px;
        }

        .nav-menu a {
            color: #3498DB;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .nav-menu a:hover {
            background-color: rgba(0, 0, 0, 0.1);
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
        <h2 style="margin-bottom: 20px;">Créer un Compte</h2>
        <form action="{{ url('/register') }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="text" name="name" placeholder="Nom" required>
            </div>
            <div class="form-group">
                <input type="text" name="surname" placeholder="Prénom" required>
            </div>
            <div class="form-group">
                <input type="tel" name="contact" placeholder="Contact" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email (Facultatif)">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
            <div class="form-group">
                <input type="password" name="password_confirmation" placeholder="Confirmation du mot de passe" required>
            </div>
            <button type="submit" class="submit-button">Créer un Compte</button>
        </form>
    </div>
</body>
</html>
