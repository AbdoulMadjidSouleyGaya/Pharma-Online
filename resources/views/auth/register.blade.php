<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - PharmaOnline</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('/images/jar-2338584_1280.jpeg');
            background-size: cover;
            background-position: center;
            color: #333;
        }

        header {
            position: absolute;
            top: 0;
            right: 0;
            padding: 10px 30px;
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-bottom-left-radius: 12px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .nav-links li {
            display: inline;
        }

        .nav-links a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
            font-size: 14px;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #0056b3;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007BFF;
            font-size: 24px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            display: block;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .submit-button {
            width: 100%;
            padding: 12px;
            background-color: #28A745;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
        }

        .submit-button:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .form-container p {
            text-align: center;
            font-size: 14px;
            margin-top: 15px;
            color: #666;
        }

        .form-container p a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        .form-container p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <ul class="nav-links">
            <li><a href="/">Accueil</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </header>

    <div class="form-container">
        <h2>Créer un compte</h2>
        <form action="{{ url('/register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" placeholder="Votre nom complet" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Votre adresse email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Créez un mot de passe" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirmez le mot de passe" required>
            </div>
            <button type="submit" class="submit-button">S'inscrire</button>
        </form>
        <p>Déjà un compte ? <a href="{{ url('/login') }}">Connectez-vous</a></p>
    </div>
</body>
</html>
