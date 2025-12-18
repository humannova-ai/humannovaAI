<?php
// Démarrer la session (auth removed)
session_start();


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres d'emploi - Human Nova AI</title>
    <link rel="stylesheet" href="../../assets/css/templatemo-prism-flux.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #6e45e2;
            --primary-dark: #5d3ac9;
            --secondary: #88d3ce;
            --accent: #ff6b9d;
            --dark: #1a1a2e;
            --darker: #0f0f1a;
            --light: #ffffff;
            --gray: #8b8b9e;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 100%);
            color: var(--light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Navigation moderne */
        .admin-nav {
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 0;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-main {
            display: flex;
            align-items: center;
            gap: 40px;
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            padding: 15px 0;
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 5px;
            margin: 0;
        }

        .nav-link {
            color: var(--gray);
            text-decoration: none;
            padding: 15px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover {
            color: var(--light);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-link:hover::before {
            width: 80%;
        }

        .nav-link.active {
            color: var(--light);
            background: rgba(110, 69, 226, 0.1);
        }

        .nav-link.active::before {
            width: 80%;
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Barre de recherche moderne */
        .search-bar {
            position: fixed;
            top: 140px !important;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 600px;
            z-index: 999;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 15px 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .search-bar:focus-within {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--primary);
            box-shadow: 0 0 30px rgba(110, 69, 226, 0.4);
        }

        .search-input {
            width: 100%;
            background: transparent;
            border: none;
            color: var(--light);
            font-size: 16px;
            outline: none;
            padding: 5px 10px;
        }

        .search-input::placeholder {
            color: var(--gray);
        }

        /* Section offres */
        .offers-section {
            max-width: 1100px;
            margin: 140px auto 30px;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--light) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            color: var(--gray);
            font-size: 1.1rem;
            margin-top: 5px;
        }

        /* Grille d'offres */
        .offers-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(220px, 1fr));
            gap: 28px;
            margin-top: 24px;
            justify-items: stretch;
            width: 100%;
            max-width: 980px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .pagination button {
            background: rgba(255,255,255,0.06);
            color: var(--light);
            border: 1px solid rgba(255,255,255,0.06);
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            min-width: 40px;
        }

        .pagination button.active {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border-color: transparent;
        }

        /* Defensive: hide unexpected fixed bottom progress bars injected by template or extensions */
        [style*="position: fixed"][style*="bottom: 0"] { display: none !important; }
        [style*="position:fixed"][style*="bottom:0"] { display: none !important; }
        [style*="position:fixed"][style*="bottom: 0"] { display: none !important; }
        [style*="position: fixed"][style*="bottom:0"] { display: none !important; }
        [style*="position:fixed"][style*="height:4px"] { display: none !important; }
        .progress, .progress-bar, .site-progress, .page-progress, .tm-progress, .page-loader { display: none !important; }

        /* AI Search Styles */
        .ai-search-section {
            background: linear-gradient(135deg, rgba(110, 69, 226, 0.1) 0%, rgba(136, 211, 206, 0.1) 100%);
            border: 2px solid rgba(110, 69, 226, 0.3);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            backdrop-filter: blur(10px);
        }

        .ai-search-section h2 {
            margin-bottom: 10px;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .ai-search-section p {
            color: var(--gray);
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        #ai-search-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        #ai-search-input {
            flex: 1;
            min-width: 250px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--light);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        #ai-search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        #ai-search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(110, 69, 226, 0.3);
            background: rgba(255, 255, 255, 0.15);
        }

        #ai-search-form button {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 150px;
            justify-content: center;
        }

        #ai-search-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(110, 69, 226, 0.4);
        }

        #ai-search-form button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .ai-search-header {
            grid-column: 1 / -1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: rgba(110, 69, 226, 0.1);
            border-radius: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .ai-search-header h3 {
            margin: 0;
            color: var(--light);
            flex: 1;
        }

        /* Make filter selects match the grey look used elsewhere */
        .filter-select {
            background: rgba(0,0,0,0.35);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.06);
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none !important;
        }

        .btn-reset-search {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--light);
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-reset-search:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Notification Styles */
        .notification {
            position: fixed;
            bottom: -100px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            z-index: 10000;
            transition: bottom 0.3s ease;
            max-width: 400px;
        }

        .notification.show {
            bottom: 20px;
        }

        .notification-success {
            background: rgba(40, 167, 69, 0.9);
            color: white;
            border: 1px solid rgba(40, 167, 69, 1);
        }

        .notification-error {
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: 1px solid rgba(220, 53, 69, 1);
        }

        .notification-warning {
            background: rgba(255, 193, 7, 0.9);
            color: #000;
            border: 1px solid rgba(255, 193, 7, 1);
        }

        .notification-info {
            background: rgba(23, 162, 184, 0.9);
            color: white;
            border: 1px solid rgba(23, 162, 184, 1);
        }

        .offer-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.03) 100%);
            border-radius: 16px;
            padding: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: transform 180ms ease, box-shadow 180ms ease;
            position: relative;
            overflow: hidden;
            will-change: transform, box-shadow;
            min-height: 260px;
            display: flex;
            flex-direction: column;
        }

        .offer-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .offer-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 32px rgba(0, 0, 0, 0.32);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .offer-image {
            width: 100%;
            height: 140px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .offer-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 220ms ease;
            will-change: transform;
            display: block;
        }

        .offer-card:hover .offer-img {
            transform: scale(1.02);
        }

        .offer-title {
            font-size: 1.12rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--light);
        }

        .offer-company {
            color: var(--secondary);
            font-size: 1rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .offer-location {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .offer-description {
            color: var(--gray);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-fulltime {
            background: rgba(110, 69, 226, 0.2);
            color: #9d7eff;
            border: 1px solid rgba(110, 69, 226, 0.3);
        }

        .badge-parttime {
            background: rgba(136, 211, 206, 0.2);
            color: var(--secondary);
            border: 1px solid rgba(136, 211, 206, 0.3);
        }

        .badge-remote {
            background: rgba(255, 107, 157, 0.2);
            color: var(--accent);
            border: 1px solid rgba(255, 107, 157, 0.3);
        }

        .badge-internship {
            background: rgba(255, 193, 7, 0.2);
            color: var(--warning);
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        /* Boutons */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(110, 69, 226, 0.3);
        }

        /* Admin-like action buttons (yellow, red) */
        .btn-edit {
            background: linear-gradient(135deg, var(--warning) 0%, #e0a800 100%);
            color: black;
            border: none;
        }

        .btn-edit:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(224,168,0,0.25); }

        .btn-delete {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: white;
            border: none;
        }

        .btn-delete:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(200,36,51,0.25); }

        .btn-secondary {
            background: rgba(255,255,255,0.06);
            color: var(--light);
            border: 1px solid rgba(255,255,255,0.06);
        }

        /* Modal détails */
        .details-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .details-modal-content {
            background: linear-gradient(135deg, var(--dark) 0%, var(--darker) 100%);
            border-radius: 20px;
            padding: 30px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .details-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .details-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--light);
        }

        .details-close {
            background: none;
            border: none;
            color: var(--gray);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s ease;
        }

        .details-close:hover {
            color: var(--light);
        }

        .details-body {
            color: var(--gray);
            line-height: 1.8;
        }

        .detail-item {
            margin: 20px 0;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-left: 3px solid var(--primary);
            border-radius: 6px;
        }

        .detail-label {
            color: var(--secondary);
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .detail-value {
            color: var(--light);
            font-size: 16px;
        }

        .offer-image-large {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 12px;
            margin: 20px 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Formulaire de candidature */
        .application-form {
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--secondary);
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(110, 69, 226, 0.3);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(110, 69, 226, 0.4);
        }

        /* Boutons d'action */
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-action {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(110, 69, 226, 0.5);
        }

        .btn-action.secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
        }

        /* Message de succès */
        .success-notification {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid var(--success);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                padding: 10px 20px;
            }

            .nav-main {
                width: 100%;
                justify-content: space-between;
                margin-bottom: 10px;
            }

            .nav-links {
                width: 100%;
                justify-content: space-around;
            }

            .offers-section {
                margin-top: 200px;
            }

            .offers-grid {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .details-modal-content {
                padding: 20px;
                margin: 20px;
            }

            .details-title {
                font-size: 1.5rem;
            }

            .offer-image-large {
                height: 200px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }
        }

        /* Medium screens: two columns */
        @media (max-width: 1024px) {
            .offers-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .nav-links {
                flex-direction: column;
                gap: 5px;
            }

            .nav-link {
                justify-content: center;
            }

            .search-bar {
                top: 140px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <header class="header" id="header">
        <nav class="nav-container">
            <a href="#home" class="logo">
                <div class="logo-icon">
                    <div class="logo-prism">
                        <div class="prism-shape"></div>
                    </div>
                </div>
                <span class="logo-text">
                    <span class="prism">PRO</span>
                    <span class="flux">MANAGE AI</span>
                </span>
            </a>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="/projectphp/view/front/offres.php" class="nav-link active">Offres</a></li>
                <li><a href="/projectphp/view/front/mes_candidatures.php" class="nav-link">Mes Candidatures</a></li>
            </ul>
            
            <div class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <script>
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function () {
                window.location.href = '/projectphp/view/deconnecter.php';
            });
        }
    </script>


    <!-- Section offres -->
    <section class="offers-section">
        <!-- AI Search Section -->
        <div class="ai-search-section">
            <h2><i class="fas fa-robot"></i> Recherche Intelligente avec IA</h2>
            <p>Décrivez le type d'emploi que vous recherchez, et notre IA trouvera les offres qui correspondent le mieux à votre description</p>
            <form id="ai-search-form">
                <input type="text" id="ai-search-input" placeholder="Ex: Je cherche un développeur web avec expérience en React et Node.js..." required>
                <button type="submit"><i class="fas fa-search"></i> Rechercher avec IA</button>
            </form>
        </div>

        <!-- Filters: basic search + category filter placed under the research bar -->
        <div class="filter-bar" style="max-width:600px; margin: 20px auto 0; display:flex; gap:10px; justify-content:center;">
            <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par titre, entreprise, lieu..." style="flex:1; padding:10px 14px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.03); color:var(--light);">
            <select id="categoryFilter" class="form-control filter-select" style="padding:10px 14px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.35); color:#fff; background-image:none;">
                <option value="">Toutes catégories</option>
            </select>
        </div>

        <div class="page-header">
            <div>
                <h1 class="page-title">Nos Offres d'Emploi</h1>
                <p class="page-subtitle">Trouvez l'opportunité qui correspond à vos compétences</p>
            </div>
        </div>
        
        <div class="offers-grid" id="offersGrid"></div>
        <div id="offersPagination" class="pagination" aria-label="Pagination"></div>
    </section>

    <!-- Modal détails -->
    <div id="detailsModal" class="details-modal">
        <div class="details-modal-content">
            <div class="details-header">
                <h2 class="details-title" id="modalTitle"></h2>
                <button class="details-close" onclick="closeDetailsModal()">×</button>
            </div>
            <div class="details-body" id="modalBody"></div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer" style="text-align: center; padding: 40px 20px; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 80px;">
        <p style="color: var(--gray);">&copy; 2026 Human Nova AI. Tous droits réservés.</p>
    </footer>

    <script>
        const API_URL = '../../controller/JobController.php';
        let allOffers = [];
        let currentOffer = null;
        let offersCurrentPage = 1;
        const offersPageSize = 6; // change per page

        // Charger les offres au chargement
        document.addEventListener('DOMContentLoaded', function() {
            loadOffres();
            setupSearchFilter();
        });

        // Debounce helper
        function debounce(fn, delay) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), delay);
            };
        }

        // Wire top AI search input for dynamic filtering (without submitting to AI)
        (function wireTopSearch(){
            const aiInput = document.getElementById('ai-search-input');
            const normalSearch = document.getElementById('searchInput');
            if (!aiInput) return;
            const handler = debounce(function() {
                if (normalSearch) normalSearch.value = aiInput.value;
                offersCurrentPage = 1;
                applyFilters();
            }, 220);
            aiInput.addEventListener('input', handler);
        })();

        // Charger toutes les offres
        function loadOffres() {
            fetch(`${API_URL}?action=getActiveJobs`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    allOffers = data;
                    offersCurrentPage = 1;
                    populateCategoryFilter();
                    displayOffres(data);
                    renderOffersPagination(data.length);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('offersGrid').innerHTML = 
                        '<p style="text-align:center; color: var(--gray);">Erreur lors du chargement des offres</p>';
                });
        }

        // Afficher les offres
        function displayOffres(offres) {
            const grid = document.getElementById('offersGrid');
            grid.innerHTML = '';
            
            if(offres.length === 0) {
                grid.innerHTML = '<p style="text-align:center; color: var(--gray);">Aucune offre d\'emploi trouvée</p>';
                return;
            }
            // Pagination: slice the offres array according to current page
            const total = offres.length;
            const start = (offersCurrentPage - 1) * offersPageSize;
            const end = start + offersPageSize;
            const pageItems = offres.slice(start, end);

            pageItems.forEach(offer => {
                const typeLabel = getTypeLabel(offer.contract_type);
                const typeClass = getTypeClass(offer.contract_type);
                
                const card = document.createElement('div');
                card.className = 'offer-card';
                card.innerHTML = `
                    <div class="offer-image">
                        <img src="${escapeHtml(offer.logo || 'https://via.placeholder.com/600x400?text=No+Image')}" 
                             alt="${escapeHtml(offer.title)}" 
                             class="offer-img"
                             onerror="this.src='https://via.placeholder.com/600x400?text=No+Image'" />
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h3 class="offer-title">${escapeHtml(offer.title)}</h3>
                        <span class="badge ${typeClass}">${typeLabel}</span>
                    </div>
                    <p class="offer-company">🏢 ${escapeHtml(offer.company)}</p>
                    <p class="offer-location">📍 ${escapeHtml(offer.location)}</p>
                    <p class="offer-description">${escapeHtml(offer.description.substring(0, 100))}...</p>
                    <p style="color: var(--secondary); font-size: 14px; margin-top: 8px;">💰 ${offer.salary || 'Salaire à négocier'}</p>
                    <div class="card-controls" style="display:flex; flex-direction:column; gap:8px; margin-top: 12px;">
                        <button class="btn btn-secondary" onclick="showDetails(${offer.id})" style="width:100%;">
                            <i class="fas fa-eye"></i> Détails
                        </button>
                        <button class="btn btn-primary" onclick="showDetails(${offer.id})" style="width:100%;">
                            <i class="fas fa-briefcase"></i> Voir l'offre
                        </button>
                        <button class="btn btn-edit" onclick="showDetails(${offer.id})" style="width:100%;">
                            <i class="fas fa-paper-plane"></i> Postuler
                        </button>
                    </div>
                `;
                grid.appendChild(card);
            });

            // Update pagination after rendering
            renderOffersPagination(offres.length);
        }

        function renderOffersPagination(totalItems) {
            const container = document.getElementById('offersPagination');
            if (!container) return;
            container.innerHTML = '';

            const totalPages = Math.max(1, Math.ceil(totalItems / offersPageSize));

            function makeBtn(label, page, cls) {
                const b = document.createElement('button');
                b.textContent = label;
                if (cls) b.classList.add(cls);
                b.addEventListener('click', () => {
                    if (page < 1 || page > totalPages) return;
                    offersCurrentPage = page;
                    // Recompute current filtered set and render that page slice
                    const filtered = getFilteredOffers();
                    displayOffres(filtered);
                    window.scrollTo({ top: document.querySelector('.offers-section').offsetTop - 80, behavior: 'smooth' });
                });
                return b;
            }

            // Prev
            container.appendChild(makeBtn('‹ Prev', offersCurrentPage - 1));

            // Page numbers (show up to 7 pages centered)
            const maxButtons = 7;
            let startPage = Math.max(1, offersCurrentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage < maxButtons - 1) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            for (let p = startPage; p <= endPage; p++) {
                const btn = makeBtn(p, p, p === offersCurrentPage ? 'active' : '');
                if (p === offersCurrentPage) btn.classList.add('active');
                container.appendChild(btn);
            }

            // Next
            container.appendChild(makeBtn('Next ›', offersCurrentPage + 1));
        }

        // Obtenir le libellé du type de contrat
        function getTypeLabel(type) {
            const types = {
                'fulltime': 'Temps plein',
                'parttime': 'Temps partiel',
                'remote': 'Télétravail',
                'internship': 'Stage'
            };
            return types[type] || 'Autre';
        }

        // Obtenir la classe CSS du type de contrat
        function getTypeClass(type) {
            const classes = {
                'fulltime': 'badge-fulltime',
                'parttime': 'badge-parttime',
                'remote': 'badge-remote',
                'internship': 'badge-internship'
            };
            return classes[type] || 'badge-fulltime';
        }

        // Configuration du filtre de recherche (texte + catégorie)
        function setupSearchFilter() {
            const searchInput = document.getElementById('searchInput');
            const categorySelect = document.getElementById('categoryFilter');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    offersCurrentPage = 1;
                    applyFilters();
                });
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    offersCurrentPage = 1;
                    applyFilters();
                });
            }
        }

        // Populate categories select from loaded offers
        function populateCategoryFilter() {
            const select = document.getElementById('categoryFilter');
            if (!select || !Array.isArray(allOffers)) return;

            const categories = Array.from(new Set(allOffers.map(o => (o.category || '').trim()).filter(c => c)));
            // Clear existing (except default)
            select.innerHTML = '<option value="">Toutes catégories</option>';
            categories.sort().forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                select.appendChild(opt);
            });
        }

        // Apply both text and category filters
        function applyFilters() {
            const searchInput = document.getElementById('searchInput');
            const categorySelect = document.getElementById('categoryFilter');

            const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';
            const category = categorySelect ? categorySelect.value : '';

            const filtered = allOffers.filter(offer => {
                // Category filter
                if (category && (offer.category || '') !== category) return false;

                // Text search across title, company, description, location
                if (!searchTerm) return true;
                const hay = (offer.title + ' ' + (offer.company||'') + ' ' + (offer.description||'') + ' ' + (offer.location||'')).toLowerCase();
                return hay.indexOf(searchTerm) !== -1;
            });

            displayOffres(filtered);
        }

        // Return filtered offers array without rendering (used by pagination)
        function getFilteredOffers() {
            const searchInput = document.getElementById('searchInput');
            const categorySelect = document.getElementById('categoryFilter');

            const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';
            const category = categorySelect ? categorySelect.value : '';

            return allOffers.filter(offer => {
                if (category && (offer.category || '') !== category) return false;
                if (!searchTerm) return true;
                const hay = (offer.title + ' ' + (offer.company||'') + ' ' + (offer.description||'') + ' ' + (offer.location||'')).toLowerCase();
                return hay.indexOf(searchTerm) !== -1;
            });
        }

        // Afficher les détails d'une offre
        function showDetails(offerId) {
            fetch(`${API_URL}?action=getOne&id=${offerId}`)
                .then(response => response.json())
                .then(offer => {
                    if(!offer) {
                        alert('Offre introuvable');
                        return;
                    }
                    
                    currentOffer = offer;
                    document.getElementById('modalTitle').textContent = offer.title;
                    
                    let bodyHTML = `
                        <img src="${offer.logo || 'https://via.placeholder.com/600x400'}" 
                             class="offer-image-large" 
                             onerror="this.src='https://via.placeholder.com/600x400'">
                        
                        <div class="detail-item">
                            <div class="detail-label">Entreprise</div>
                            <div class="detail-value">${escapeHtml(offer.company)}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Description</div>
                            <div class="detail-value">${escapeHtml(offer.description)}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Lieu</div>
                            <div class="detail-value">📍 ${escapeHtml(offer.location)}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Type de contrat</div>
                            <div class="detail-value">${getTypeLabel(offer.contract_type)}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Salaire</div>
                            <div class="detail-value">${offer.salary || 'À négocier'}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Date de publication</div>
                            <div class="detail-value">${formatDateLong(offer.date_posted)}</div>
                        </div>
                    `;
                    
                    
                    bodyHTML += `<div class="action-buttons">
                        <button class="btn-action" onclick="showApplicationForm()">
                            <i class="fas fa-paper-plane"></i> Postuler
                        </button>
                    </div>`;
                    
                    document.getElementById('modalBody').innerHTML = bodyHTML;
                    openModal();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des détails');
                });
        }

        // Afficher le formulaire de candidature
        function showApplicationForm() {
            const formHTML = `
                <div class="application-form">
                    <h3 style="color: var(--secondary); margin-bottom: 20px;">Formulaire de candidature</h3>
                    
                    <form id="applicationForm">
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" id="appNom" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Prénom *</label>
                            <input type="text" id="appPrenom" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" id="appEmail" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="tel" id="appTelephone" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>Lettre de motivation *</label>
                            <textarea id="appMotivation" class="form-control" rows="5" placeholder="Pourquoi souhaitez-vous postuler à cette offre ?" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>CV * (PDF uniquement)</label>
                            <div class="file-upload">
                                <input type="file" id="appCV" accept=".pdf" required>
                                <div class="file-upload-label">
                                    <i class="fas fa-file-pdf"></i>
                                    <span id="cvFileName">Choisir votre CV</span>
                                </div>
                            </div>
                            <small style="color: var(--gray); display: block; margin-top: 5px;">
                                Format accepté: PDF (max 5MB)
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label>Lettre de motivation (optionnel)</label>
                            <div class="file-upload">
                                <input type="file" id="appLettre" accept=".pdf,.doc,.docx">
                                <div class="file-upload-label">
                                    <i class="fas fa-file-alt"></i>
                                    <span id="lettreFileName">Choisir votre lettre</span>
                                </div>
                            </div>
                            <small style="color: var(--gray); display: block; margin-top: 5px;">
                                Formats acceptés: PDF, DOC, DOCX (max 5MB)
                            </small>
                        </div>
                    </form>
                    
                    <div class="action-buttons">
                        <button class="btn-action secondary" onclick="showDetails(${currentOffer.id})">
                            <i class="fas fa-arrow-left"></i> Retour
                        </button>
                        <button class="btn-action" id="btnSoumettreCandidature" onclick="submitApplication()">
                            <i class="fas fa-paper-plane"></i> Soumettre ma candidature
                        </button>
                    </div>
                </div>
            `;
            
            document.getElementById('modalBody').innerHTML = formHTML;
            
            document.getElementById('appCV').addEventListener('change', function(e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Choisir votre CV';
                document.getElementById('cvFileName').textContent = fileName;
            });
            
            document.getElementById('appLettre').addEventListener('change', function(e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Choisir votre lettre';
                document.getElementById('lettreFileName').textContent = fileName;
            });
        }

        // Soumettre la candidature
        function submitApplication() {
            const nom = document.getElementById('appNom').value.trim();
            const prenom = document.getElementById('appPrenom').value.trim();
            const email = document.getElementById('appEmail').value.trim();
            const telephone = document.getElementById('appTelephone').value.trim();
            const motivation = document.getElementById('appMotivation').value.trim();
            
            if(!nom || !prenom || !email || !motivation) {
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }
            
            const cvFile = document.getElementById('appCV').files[0];
            if(!cvFile) {
                alert('Veuillez joindre votre CV');
                return;
            }
            
            // Vérifier que le CV est un PDF
            if(cvFile.type !== 'application/pdf') {
                alert('Veuillez joindre un CV au format PDF');
                return;
            }
            
            const formData = new FormData();
            formData.append('job_id', currentOffer.id);
            formData.append('user_id', <?php echo $_SESSION['user_id']; ?>);
            formData.append('name', `${prenom} ${nom}`);
            formData.append('email', email);
            formData.append('cover_letter', motivation);
            formData.append('cv_filename', cvFile.name);
            formData.append('cv_file', cvFile);
            
            if(telephone) {
                formData.append('telephone', telephone);
            }
            
            const lettreFile = document.getElementById('appLettre').files[0];
            if(lettreFile) {
                formData.append('lettre_motivation', lettreFile);
            }
            
            const btnSoumettre = document.getElementById('btnSoumettreCandidature');
            if(btnSoumettre) {
                const originalText = btnSoumettre.textContent;
                btnSoumettre.textContent = 'Envoi en cours...';
                btnSoumettre.disabled = true;
                
                fetch(API_URL + '?action=submitApplication', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(text => {
                    try {
                        const result = JSON.parse(text);
                        if(result.success) {
                            showSuccessMessage('Votre candidature a été envoyée avec succès !');
                        } else {
                            alert('Erreur: ' + result.message);
                        }
                    } catch(e) {
                        alert('Erreur: Réponse invalide du serveur');
                    }
                })
                .catch(error => {
                    alert('Erreur lors de l\'envoi de la candidature: ' + error.message);
                })
                .finally(() => {
                    btnSoumettre.textContent = originalText;
                    btnSoumettre.disabled = false;
                });
            }
        }

        // Afficher un message de succès
        function showSuccessMessage(message) {
            const successHTML = `
                <div class="success-notification">
                    <h3 style="color: var(--success); font-size: 24px; margin-bottom: 15px;">
                        <i class="fas fa-check-circle"></i> Candidature envoyée !
                    </h3>
                    <p style="color: var(--light); font-size: 16px;">${message}</p>
                    <p style="color: var(--gray); font-size: 14px; margin-top: 10px;">
                        Nous étudierons votre candidature et vous recontacterons rapidement.
                    </p>
                </div>
                
                <div class="action-buttons">
                    <button class="btn-action secondary" onclick="closeDetailsModal()">
                        <i class="fas fa-times"></i> Fermer
                    </button>
                    <button class="btn-action" onclick="showDetails(${currentOffer.id})">
                        <i class="fas fa-eye"></i> Voir les détails
                    </button>
                </div>
            `;
            
            document.getElementById('modalBody').innerHTML = successHTML;
        }

        // Ouvrir/Fermer le modal
        function openModal() {
            document.getElementById('detailsModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            currentOffer = null;
        }

        // Fonctions utilitaires
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR', { 
                day: '2-digit', 
                month: 'long', 
                year: 'numeric'
            });
        }

        function formatDateLong(dateStr) {
            const date = new Date(dateStr);
            const options = { 
                weekday: 'long',
                day: 'numeric', 
                month: 'long', 
                year: 'numeric'
            };
            return date.toLocaleDateString('fr-FR', options);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Fermer avec Échap
        document.addEventListener('keydown', (e) => {
            if(e.key === 'Escape') {
                closeDetailsModal();
            }
        });

        function initializeAISearch() {
            const aiSearchForm = document.getElementById('ai-search-form');
            if (!aiSearchForm) return;

            aiSearchForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const query = document.getElementById('ai-search-input').value.trim();
                
                if (!query) {
                    showNotification('Veuillez décrire le job que vous recherchez', 'warning');
                    return;
                }

                const searchBtn = aiSearchForm.querySelector('button');
                const originalText = searchBtn.textContent;
                searchBtn.disabled = true;
                searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recherche en cours...';

                try {
                    const response = await fetch('../../controller/JobController.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=aiSearch&query=' + encodeURIComponent(query)
                    });

                    const responseText = await response.text();
                    let data;
                    
                    try {
                        data = JSON.parse(responseText);
                    } catch (parseError) {
                        console.error('Response parsing error:', responseText);
                        showNotification('Erreur serveur: réponse invalide', 'error');
                        return;
                    }
                    
                    if (data.success && data.jobs && data.jobs.length > 0) {
                        displayAISearchResults(data.jobs, query);
                        showNotification('Résultats trouvés: ' + data.count + ' emploi(s)', 'success');
                    } else if (data.jobs && data.jobs.length === 0) {
                        displayNoResults(query);
                        showNotification('Aucun emploi ne correspond à votre description', 'info');
                    } else if (data.message) {
                        showNotification(data.message, 'error');
                    } else {
                        console.error('Response data:', data);
                        showNotification('Erreur lors de la recherche', 'error');
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    showNotification('Erreur de connexion. Veuillez réessayer.', 'error');
                } finally {
                    searchBtn.disabled = false;
                    searchBtn.textContent = originalText;
                }
            });
        }

        function displayAISearchResults(jobs, query) {
            const offersGrid = document.querySelector('.offers-grid');
            offersGrid.innerHTML='';
            if (!offersGrid) return;

            let html = '<div class="ai-search-header"><h3>Résultats de recherche IA pour: <strong>"' + escapeHtml(query) + '"</strong></h3><button class="btn-reset-search" onclick="resetSearch()">Réinitialiser</button></div>';

  
            jobs.forEach(offer => {
                const typeLabel = getTypeLabel(offer.contract_type);
                const typeClass = getTypeClass(offer.contract_type);
                
                const card = document.createElement('div');
                card.className = 'offer-card';
                card.innerHTML = `
                    <div class="offer-image">
                        <img src="${escapeHtml(offer.logo || 'https://via.placeholder.com/600x400?text=No+Image')}" 
                             alt="${escapeHtml(offer.title)}" 
                             class="offer-img"
                             onerror="this.src='https://via.placeholder.com/600x400?text=No+Image'" />
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h3 class="offer-title">${escapeHtml(offer.title)}</h3>
                        <span class="badge ${typeClass}">${typeLabel}</span>
                    </div>
                    <p class="offer-company">🏢 ${escapeHtml(offer.company)}</p>
                    <p class="offer-location">📍 ${escapeHtml(offer.location)}</p>
                    <p class="offer-description">${escapeHtml(offer.description.substring(0, 100))}...</p>
                    <p style="color: var(--secondary); font-size: 14px; margin-top: 8px;">💰 ${offer.salary || 'Salaire à négocier'}</p>
                    <div class="card-controls" style="display:flex; gap:8px; margin-top: 12px;">
                        <button class="btn btn-secondary" onclick="showDetails(${offer.id})" style="flex:1; min-width:110px;">
                            <i class="fas fa-eye"></i> Détails
                        </button>
                        <button class="btn btn-primary" onclick="showDetails(${offer.id})" style="flex:1; min-width:110px;">
                            <i class="fas fa-briefcase"></i> Voir l'offre
                        </button>
                        <button class="btn btn-edit" onclick="showDetails(${offer.id})" style="flex:1; min-width:110px;">
                            <i class="fas fa-paper-plane"></i> Postuler
                        </button>
                    </div>
                `;
                offersGrid.appendChild(card);
            });

            offersGrid.scrollIntoView({ behavior: 'smooth' });
        }

        function displayNoResults(query) {
            const offersGrid = document.querySelector('.offers-grid');
            if (!offersGrid) return;

            offersGrid.innerHTML = `
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--gray);">
                    <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                    <h3>Aucun emploi ne correspond à votre description</h3>
                    <p>Essayez avec d'autres mots-clés ou consultez tous les emplois disponibles</p>
                    <button class="btn-primary" onclick="resetSearch()" style="margin-top: 20px;">Afficher tous les emplois</button>
                </div>
            `;
        }

        function resetSearch() {
            document.getElementById('ai-search-input').value = '';
            // reset filters and reload
            const si = document.getElementById('searchInput'); if(si) si.value = '';
            const cf = document.getElementById('categoryFilter'); if(cf) cf.value = '';
            loadOffres();
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
                ${message}
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('show');
            }, 10);

            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', initializeAISearch);
    </script>
</body>
</html>