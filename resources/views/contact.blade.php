<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - PharmaOnline</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:
                linear-gradient(135deg, rgba(21, 101, 192, 0.78), rgba(15, 157, 88, 0.75)),
                url('{{ asset('images/jar-2338584_1280.jpeg') }}') center/cover no-repeat fixed;
            color: #0f172a;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(2px);
        }

        .nav-menu {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px 24px 0;
            flex-wrap: wrap;
        }

        .nav-menu a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .nav-menu a:hover {
            background: rgba(255, 255, 255, 0.22);
            transform: translateY(-1px);
        }

        .page {
            position: relative;
            z-index: 2;
            width: min(1180px, calc(100% - 32px));
            margin: 18px auto 40px;
            display: grid;
            grid-template-columns: 1.02fr 1fr;
            gap: 26px;
            align-items: stretch;
        }

        .panel {
            background: rgba(255, 255, 255, 0.94);
            border-radius: 24px;
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.55);
            overflow: hidden;
        }

        .info-panel {
            padding: 34px 34px 30px;
            position: relative;
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .logo-wrap img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 18px;
            background: #fff;
            padding: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .brand-title {
            margin: 0;
            font-size: 30px;
            line-height: 1.1;
            color: #0f172a;
        }

        .brand-subtitle {
            margin: 6px 0 0;
            color: #475569;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.04em;
        }

        .hero-title {
            margin: 0 0 14px;
            font-size: 36px;
            line-height: 1.15;
            color: #0f172a;
        }

        .hero-text {
            margin: 0 0 26px;
            color: #475569;
            font-size: 16px;
            line-height: 1.7;
        }

        .contact-grid {
            display: grid;
            gap: 14px;
            margin-bottom: 26px;
        }

        .contact-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 16px 18px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.06), rgba(22, 163, 74, 0.08));
            border: 1px solid rgba(37, 99, 235, 0.08);
        }

        .contact-icon {
            min-width: 46px;
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 20px;
            background: linear-gradient(135deg, #2563eb, #16a34a);
            color: #fff;
            box-shadow: 0 10px 18px rgba(37, 99, 235, 0.18);
        }

        .contact-item h3 {
            margin: 0 0 4px;
            font-size: 17px;
            color: #0f172a;
        }

        .contact-item p,
        .contact-item a {
            margin: 0;
            color: #475569;
            line-height: 1.6;
            text-decoration: none;
            word-break: break-word;
        }

        .contact-item a:hover {
            color: #2563eb;
        }

        .features {
            display: grid;
            gap: 12px;
            margin-top: 8px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #334155;
            font-weight: 600;
        }

        .feature .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #16a34a);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .form-panel {
            padding: 34px;
        }

        .form-panel h2 {
            margin: 0 0 8px;
            font-size: 29px;
            color: #0f172a;
        }

        .form-panel .subtitle {
            margin: 0 0 24px;
            color: #64748b;
            font-size: 15px;
            line-height: 1.6;
        }

        .alert {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            font-size: 14px;
            line-height: 1.6;
        }

        .alert-success {
            background: rgba(22, 163, 74, 0.12);
            color: #166534;
            border: 1px solid rgba(22, 163, 74, 0.22);
        }

        .alert-error {
            background: rgba(220, 38, 38, 0.10);
            color: #991b1b;
            border: 1px solid rgba(220, 38, 38, 0.20);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .field,
        .field-full {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field-full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        input,
        textarea {
            width: 100%;
            border: 1px solid #dbe3ee;
            border-radius: 14px;
            padding: 14px 15px;
            font-size: 15px;
            color: #0f172a;
            background: #f8fafc;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
            background: #fff;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        .helper {
            font-size: 12px;
            color: #64748b;
        }

        .actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .btn-primary,
        .btn-secondary {
            text-decoration: none;
            border: none;
            cursor: pointer;
            border-radius: 999px;
            padding: 14px 22px;
            font-size: 15px;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #16a34a);
            color: #fff;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.24);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(37, 99, 235, 0.28);
        }

        .btn-secondary {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid rgba(37, 99, 235, 0.16);
        }

        .btn-secondary:hover {
            background: #dbeafe;
            transform: translateY(-1px);
        }

        .field-error {
            font-size: 12px;
            color: #b91c1c;
            margin-top: -2px;
        }

        @media (max-width: 980px) {
            .page {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .page {
                width: min(100% - 18px, 100%);
                gap: 16px;
                margin-top: 8px;
            }

            .info-panel,
            .form-panel {
                padding: 24px 18px;
            }

            .hero-title {
                font-size: 28px;
            }

            .brand-title {
                font-size: 24px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .nav-menu {
                justify-content: center;
                padding-top: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="nav-menu">
        <a href="{{ route('home') }}">Accueil</a>
        <a href="{{ route('login') }}">Connexion</a>
        <a href="{{ route('contact') }}">Contact</a>
    </div>

    <div class="page">
        <section class="panel info-panel">
            <div class="logo-wrap">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PharmaOnline">
                <div>
                    <h1 class="brand-title">PharmaOnline</h1>
                    <p class="brand-subtitle">Support & Assistance</p>
                </div>
            </div>

            <h2 class="hero-title">Restons en contact.</h2>
            <p class="hero-text">
                Une question sur la plateforme, une difficulté de connexion ou un besoin d’assistance sur une commande ?
                Envoyez-nous votre message et nous vous répondrons dans les meilleurs délais.
            </p>

            <div class="contact-grid">
                <div class="contact-item">
                    <div class="contact-icon">📞</div>
                    <div>
                        <h3>Téléphone</h3>
                        <p><a href="tel:90528629">90528629</a></p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">✉️</div>
                    <div>
                        <h3>Email</h3>
                        <p><a href="mailto:abdoulmadjidsouley@gmail.com">abdoulmadjidsouley@gmail.com</a></p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">💊</div>
                    <div>
                        <h3>PharmaOnline</h3>
                        <p>Plateforme de recherche, de commande et d’assistance pour les produits pharmaceutiques.</p>
                    </div>
                </div>
            </div>

            <div class="features">
                <div class="feature"><span class="dot"></span><span>Assistance générale sur l’utilisation de la plateforme</span></div>
                <div class="feature"><span class="dot"></span><span>Aide sur les commandes et les ordonnances</span></div>
                <div class="feature"><span class="dot"></span><span>Retour rapide et formulaire clair</span></div>
            </div>
        </section>

        <section class="panel form-panel">
            <h2>Envoyer un message</h2>
            <p class="subtitle">Remplissez le formulaire ci-dessous. Les champs marqués par * sont obligatoires.</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    Merci de corriger les informations du formulaire avant l’envoi.
                </div>
            @endif

            <form action="{{ route('contact.submit') }}" method="POST">
                @csrf

                <div class="form-grid">
                    <div class="field">
                        <label for="name">Nom complet *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Votre nom complet" required>
                        @error('name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="exemple@email.com" required>
                        @error('email')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="phone">Téléphone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Votre numéro de téléphone">
                        @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="subject">Sujet</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Objet de votre message">
                        @error('subject')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field-full">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" placeholder="Décrivez votre besoin ou votre difficulté..." required>{{ old('message') }}</textarea>
                        <div class="helper">Soyez précis pour faciliter le traitement de votre demande.</div>
                        @error('message')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field-full actions">
                        <button type="submit" class="btn-primary">Envoyer le message</button>
                        <a href="{{ route('home') }}" class="btn-secondary">Retour à l’accueil</a>
                    </div>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
