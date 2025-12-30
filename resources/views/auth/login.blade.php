<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - PharmaOnline</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: linear-gradient(to bottom right, rgba(0, 123, 255, 0.8), rgba(40, 167, 69, 0.8)), url('/images/jar-2338584_1280.jpeg');
            background-size: cover;
            background-position: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: white;
            overflow: hidden;
        }

        .form-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 100%;
            transition: transform 0.3s ease-in-out;
        }

        .form-container:hover {
            transform: translateY(-5px);
        }

        h2 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #ffffff;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            color: #ddd;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        }

        .submit-button {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s, box-shadow 0.2s;
            width: 100%;
            margin-top: 20px;
        }

        .submit-button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .forgot-password {
            margin-top: 15px;
            font-size: 14px;
            color: #ffffff;
            text-decoration: none;
            display: inline-block;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .nav-menu {
            position: absolute;
            top: 20px;
            right: 20px; /* Aligning to the right */
            display: flex;
            gap: 20px;
            z-index: 10;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Adding shadow */
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 25px;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        .nav-menu a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .form-container {
                padding: 30px;
            }

            .nav-menu {
                top: 10px;
                right: 10px;
            }

            .nav-menu a {
                font-size: 16px;
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="nav-menu">
        <a href="{{ url('/') }}">Accueil</a>
        <a href="{{ url('/contact') }}">Contact</a>
    </div>
    <div class="form-container">
        <h2>Connexion</h2>
        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="submit-button">Se connecter</button>
            <a href="{{ url('/password/reset') }}" class="forgot-password">Mot de passe oublié ?</a>
        </form>
    </div>
</body>
</html>
