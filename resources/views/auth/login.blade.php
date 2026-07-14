<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - PharmaOnline</title>
    <style>
        * {
            box-sizing: border-box;
        }

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
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 110px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #007bff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 8px;
        }

        .toggle-password:hover {
            background: rgba(0, 123, 255, 0.12);
        }

        .toggle-password:focus {
            outline: 2px solid rgba(0, 123, 255, 0.4);
            outline-offset: 2px;
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
            right: 20px;
            display: flex;
            gap: 20px;
            z-index: 10;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
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

        @media (max-width: 480px) {
            .form-container {
                padding: 30px;
                max-width: calc(100% - 24px);
            }

            .nav-menu {
                top: 10px;
                right: 10px;
                gap: 10px;
            }

            .nav-menu a {
                font-size: 16px;
                padding: 8px 12px;
            }

            .password-wrapper input {
                padding-right: 95px;
            }

            .toggle-password {
                font-size: 12px;
                right: 10px;
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
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="toggle-password" id="togglePassword" aria-label="Afficher le mot de passe">
                        Afficher
                    </button>
                </div>
            </div>

            <button type="submit" class="submit-button">Se connecter</button>
            <a href="{{ url('/password/reset') }}" class="forgot-password">Mot de passe oublié ?</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');

            if (!passwordInput || !toggleButton) {
                return;
            }

            toggleButton.addEventListener('click', function () {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                toggleButton.textContent = isHidden ? 'Masquer' : 'Afficher';
                toggleButton.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
            });
        });
    </script>
</body>
</html>
