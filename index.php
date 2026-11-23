<?php
$loginUrl = "login.php";
$registerUrl = "register.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReadFlags - A home for every book review</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,800;1,400&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a1614;
            color: #f9f6f0;
            background-image: linear-gradient(to bottom, rgba(26, 22, 20, 0.9), rgba(26, 22, 20, 0.95)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=2580&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        .site-header {
            padding: 24px 32px;
            position: relative;
            z-index: 10;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background-color: #f9f6f0;
            color: #1a1614;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 20px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .logo:hover {
            color: #1a1614;
            transform: scale(1.05);
        }

        .logo svg {
            fill: #e68eb0;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-links a {
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .nav-item-link {
            background-color: #f9f6f0;
            color: #1a1614;
            padding: 10px 24px;
            border-radius: 50px;
        }

        .nav-item-link:hover {
            background-color: #e8e4de;
            color: #1a1614;
        }

        .nav-divider {
            width: 1px;
            height: 24px;
            background-color: rgba(255, 255, 255, 0.2);
            margin: 0 8px;
        }

        .nav-login {
            color: #f9f6f0;
            border: 1px solid rgba(249, 246, 240, 0.4);
            padding: 10px 24px;
            border-radius: 50px;
        }

        .nav-login:hover {
            color: white;
            border-color: white;
        }

        .nav-register {
            background-color: #e68eb0;
            color: #1a1614;
            padding: 10px 24px;
            border-radius: 50px;
        }

        .nav-register:hover {
            background-color: #d67da0;
            color: #1a1614;
        }

        .mobile-menu {
            color: #f9f6f0;
            border: 1px solid rgba(255, 255, 255, 0.35);
            font-size: 24px;
            padding: 4px 10px;
        }

        .mobile-menu:hover {
            color: #f9f6f0;
            border-color: white;
        }

        .hero {
            flex: 1;
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            padding: 48px 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            width: fit-content;
            background-color: rgba(26, 22, 20, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            padding: 6px 20px 6px 6px;
            margin-bottom: 48px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
        }

        .badge-new {
            background-color: #e68eb0;
            color: #1a1614;
            padding: 5px 13px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-text {
            font-size: 14px;
            font-weight: 500;
            color: rgba(249, 246, 240, 0.9);
        }

        .hero-title-wrap {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 24px;
            margin-bottom: 80px;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 190px;
            line-height: 0.8;
            letter-spacing: -10px;
            color: #f9f6f0;
            margin: 0;
        }

        .hero-flag {
            width: 150px;
            height: 150px;
            fill: #e68eb0;
            flex-shrink: 0;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .hero-bottom {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            gap: 40px;
            width: 100%;
            margin-top: auto;
        }

        .hero-subtitle {
            max-width: 650px;
            font-size: 56px;
            line-height: 1.1;
            letter-spacing: -2px;
            font-weight: 500;
            margin: 0;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }

        .btn-primary-custom,
        .btn-secondary-custom {
            display: inline-block;
            text-align: center;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-primary-custom {
            background-color: #e68eb0;
            color: #1a1614;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-primary-custom:hover {
            background-color: #d67da0;
            color: #1a1614;
            transform: translateY(-4px);
        }

        .btn-secondary-custom {
            border: 1px solid rgba(249, 246, 240, 0.6);
            color: #f9f6f0;
        }

        .btn-secondary-custom:hover {
            border-color: #f9f6f0;
            background-color: rgba(255, 255, 255, 0.05);
            color: #f9f6f0;
        }

        .site-footer {
            margin-top: 50px;
            position: relative;
            z-index: 1;
            width: 100%;
        }

        .marquee-container {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            background-color: #f9f6f0;
            color: #1a1614;
            padding: 12px 0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            font-weight: 600;
            border-top: 1px solid #f9f6f0;
            border-bottom: 1px solid #f9f6f0;
        }

        .marquee-content {
            display: inline-block;
            animation: marquee 25s linear infinite;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        /* desktop */
        @media (min-width: 992px) {
            .hero-bottom {
                flex-direction: row;
                align-items: flex-end;
            }
        }

        /* tablet */
        @media (max-width: 991px) {
            .hero {
                padding: 48px 40px;
            }

            .hero-title {
                font-size: 120px;
            }

            .hero-flag {
                width: 100px;
                height: 100px;
            }

            .hero-subtitle {
                font-size: 44px;
            }
        }

        /* mobile */
        @media (max-width: 767.98px) {
            .site-header {
                padding: 16px;
            }

            .hero {
                padding: 48px 24px;
            }

            .hero-badge {
                margin-bottom: 40px;
            }

            .hero-title-wrap {
                gap: 16px;
                margin-bottom: 64px;
            }

            .hero-title {
                font-size: 68px;
                letter-spacing: -4px;
            }

            .hero-flag {
                width: 72px;
                height: 72px;
            }

            .hero-subtitle {
                font-size: 34px;
                letter-spacing: -1px;
            }

            .hero-buttons {
                width: 100%;
                flex-direction: column;
            }

            .hero-buttons a {
                width: 100%;
            }

            .site-footer {
                margin-top: 48px;
            }
        }

        @media (max-width: 400px) {
            .hero-title {
                font-size: 56px;
            }

            .hero-flag {
                width: 55px;
                height: 55px;
            }

            .hero-subtitle {
                font-size: 30px;
            }

            .badge-text {
                font-size: 12px;
            }
        }
    </style>

</head>
<body>
    <header class="site-header container-fluid d-flex justify-content-between align-items-center">
        <a href="index.php" class="logo">
            <svg width="24" height="24" viewBox="0 0 24 24">
                <rect width="24" height="24" rx="6" />
                <path d="M7 6h11v12H7l4-6-4-6z" fill="#ffffff" />
            </svg>
            ReadFlags
        </a>

        <nav class="nav-links d-none d-md-flex">
            <a href="#books" class="nav-item-link">books</a>
            <a href="#reviews" class="nav-item-link">reviews</a>
            <a href="#about" class="nav-item-link">about</a>
            <div class="nav-divider"></div>
            <a href="<?= $loginUrl; ?>" class="nav-login">log in</a>
            <a href="<?= $registerUrl; ?>" class="nav-register">create account</a>
        </nav>

        <div class="dropdown d-md-none">
            <button class="btn mobile-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#books">books</a></li>
                <li><a class="dropdown-item" href="#reviews">reviews</a></li>
                <li><a class="dropdown-item" href="#about">about</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= $loginUrl; ?>">log in</a></li>
                <li><a class="dropdown-item" href="<?= $registerUrl; ?>">create account</a></li>
            </ul>
        </div>
    </header>

    <main class="hero">
        <div class="hero-badge">
            <span class="badge-new">Welcome</span>
            <span class="badge-text">your next favorite book is waiting &rarr;</span>
        </div>
        <div class="hero-title-wrap">
            <h1 class="hero-title">readflags</h1>

            <svg class="hero-flag" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M4 2v20h2v-8h11.2c.5 0 .9-.3 1-.8.1-.4-.1-.9-.5-1.1l-3.3-2.6 3.3-2.6c.4-.2.6-.7.5-1.1-.1-.5-.5-.8-1-.8H6V2H4z"/>
            </svg>
        </div>
        <div class="hero-bottom">
            <h2 class="hero-subtitle">
                a home for every book<br>you read, review and recommend
            </h2>

            <div class="hero-buttons">
                <a href="<?= $registerUrl; ?>" class="btn-primary-custom">
                    create your account
                </a>
                <a href="<?= $loginUrl; ?>" class="btn-secondary-custom">
                    log in to ReadFlags
                </a>
            </div>
        </div>
    </main>
    <footer class="site-footer">
        <div class="marquee-container">
            <div class="marquee-content">
                <span>• ReadFlags (n.) a place for every book lover • discover new stories, share your thoughts • ReadFlags (n.) a place for every book lover • discover new stories, share your thoughts </span>
                <span>• ReadFlags (n.) a place for every book lover • discover new stories, share your thoughts • ReadFlags (n.) a place for every book lover • discover new stories, share your thoughts </span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>