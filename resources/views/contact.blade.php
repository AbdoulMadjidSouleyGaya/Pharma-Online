<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - PharmaOnline</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        .contact-info {
            font-size: 18px;
            margin: 20px 0;
            color: #555;
        }

        .contact-info span {
            font-weight: bold;
        }

        form {
            margin-top: 20px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #3498DB;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980B9;
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

    <div class="container">
        <h1>Contactez-nous</h1>
        <p class="contact-info">Téléphone : <span>90528629</span></p>
        <p class="contact-info">Email : <span>abdoulmadjidoule8725@gmail.com</span></p>

    <form action="{{ route('contact.submit') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Votre nom" required>
        <input type="email" name="email" placeholder="Votre email" required>
        <textarea name="message" placeholder="Votre message" rows="4" required></textarea>
        <button type="submit">Envoyer</button>
    </form>

    </div>
</body>
</html>
