<?php
// Démarrer la session (auth removed)
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Candidatures - Human Nova AI</title>
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
            --info: #17a2b8;
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


        /* Barre de recherche moderne (placed in the flow and sticky under header) */
        .search-bar {
            position: -webkit-sticky; /* Safari */
            position: sticky;
            top: 80px; /* sticks below fixed nav */
            margin: 10px auto 22px;
            width: 100%;
            max-width: 980px;
            z-index: 900; /* put under the fixed navbar (z-index:1000) */
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 50px;
            padding: 12px 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
        }

        .search-bar:focus-within {
            background: rgba(255, 255, 255, 0.09);
            border-color: var(--primary);
            box-shadow: 0 8px 28px rgba(110, 69, 226, 0.12);
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

        /* Section candidatures */
        .applications-section {
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

        /* Statistiques */
        .stats-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            justify-content: center;
            align-items: stretch;
            flex-wrap: wrap;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-pending { color: var(--warning); }
        .stat-accepted { color: var(--success); }
        .stat-refused { color: var(--danger); }
        .stat-total { color: var(--info); }

        /* Grille de candidatures */
        .applications-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(220px, 1fr));
            gap: 28px;
            margin-top: 24px;
            justify-items: stretch;
            width: 100%;
            max-width: 980px;
        }

        .application-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.03) 100%);
            border-radius: 16px;
            padding: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.18s ease;
            position: relative;
            overflow: hidden;
            min-height: 240px;
            display: flex;
            flex-direction: column;
        }

        .application-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .application-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .application-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .application-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--light);
            margin-bottom: 5px;
        }

        .application-company {
            color: var(--secondary);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .application-date {
            color: var(--gray);
            font-size: 0.85rem;
            text-align: right;
        }

        .application-description {
            color: var(--gray);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            line-clamp: 3;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .application-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Badges de statut */
        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: var(--warning);
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-accepted {
            background: rgba(40, 167, 69, 0.2);
            color: var(--success);
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-refused {
            background: rgba(220, 53, 69, 0.2);
            color: var(--danger);
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .status-reviewed {
            background: rgba(23, 162, 184, 0.2);
            color: var(--info);
            border: 1px solid rgba(23, 162, 184, 0.3);
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

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.3);
        }

        /* Actions */
        .application-actions {
            display: flex;
            gap: 10px;
            justify-content: space-between;
            margin-top: 15px;
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

        /* Admin-like action buttons */
        .btn-edit {
            background: linear-gradient(135deg, var(--warning) 0%, #e0a800 100%);
            color: black;
            border: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
        }

        .btn-delete {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
        }

        .btn-secondary {
            background: rgba(255,255,255,0.06);
            color: var(--light);
            border: 1px solid rgba(255,255,255,0.06);
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
        }

        /* Stack admin-like controls vertically like backoffice */
        .admin-controls {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: auto;
            align-items: center;
            justify-content: flex-end;
            padding-top: 8px;
        }

        .admin-controls .btn {
            white-space: normal;
            padding: 8px 12px;
            font-size: 0.78rem;
            width: auto;
            min-width: 140px;
            max-width: 220px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: none;
            border-radius: 6px;
            text-align: center;
        }

        .admin-controls .btn.btn-primary {
            width: auto;
            min-width: 160px;
        }

        @media (max-width: 768px) {
            .admin-controls .btn {
                width: 100%;
                min-width: unset;
                max-width: unset;
            }
        }

        /* Defensive: hide unexpected fixed bottom progress bars injected by template or extensions */
        [style*="position: fixed"][style*="bottom: 0"] { display: none !important; }
        [style*="position:fixed"][style*="bottom:0"] { display: none !important; }
        [style*="position:fixed"][style*="bottom: 0"] { display: none !important; }
        [style*="position: fixed"][style*="bottom:0"] { display: none !important; }
        [style*="position:fixed"][style*="height:4px"] { display: none !important; }
        .progress, .progress-bar, .site-progress, .page-progress, .tm-progress, .page-loader { display: none !important; }

        /* Filtres */
        .filters-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .filters-title {
            color: var(--secondary);
            font-size: 1.1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filters-grid {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            color: var(--gray);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .filter-select {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--light);
            padding: 10px 12px;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(110, 69, 226, 0.3);
        }

        /* Default: filter selects use dark background with white text */
        .filters-section .filter-select {
            color: #fff;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255,255,255,0.06);
        }

        /* Category select: match the filters' grey background so label is readable */
        #categoryFilter {
            background: rgba(0,0,0,0.35);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.06);
        }

        /* Message vide */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--light);
        }

        .empty-state p {
            margin-bottom: 30px;
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

            .applications-section {
                margin-top: 200px;
            }

            .applications-grid {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .application-details {
                grid-template-columns: 1fr;
            }

            .application-actions {
                flex-direction: column;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .details-modal-content {
                padding: 20px;
                margin: 20px;
            }

            .details-title {
                font-size: 1.5rem;
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

            

            .stats-grid {
                grid-template-columns: 1fr;
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
                <li><a href="/projectphp/view/front/offres.php" class="nav-link">Offres</a></li>
                <li><a href="/projectphp/view/front/mes_candidatures.php" class="nav-link active">Mes Candidatures</a></li>
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

    <!-- Section candidatures -->
    <section class="applications-section">
        <div class="page-header">
            <div>
                <h1 class="page-title">Mes Candidatures</h1>
                <p class="page-subtitle">Suivez l'état de vos candidatures en temps réel</p>
            </div>
        </div>
        <!-- Barre de recherche (placed under page header so it can be sticky) -->
        <div class="search-bar">
            <input type="text" class="search-input" id="searchInput" placeholder="🔍 Rechercher dans mes candidatures...">
        </div>
        
        <!-- Statistiques -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-number stat-total" id="statTotal">0</div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number stat-pending" id="statPending">0</div>
                <div class="stat-label">En attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-number stat-accepted" id="statAccepted">0</div>
                <div class="stat-label">Acceptées</div>
            </div>
            <div class="stat-card">
                <div class="stat-number stat-refused" id="statRefused">0</div>
                <div class="stat-label">Refusées</div>
            </div>
        </div>

        <!-- Category selector placed directly under stats cards -->
        <div class="category-row" style="width:100%; max-width:980px; display:flex; justify-content:center; margin: 8px auto 18px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <label for="categoryFilter" style="color:var(--secondary); font-weight:700;">Catégorie</label>
                <select id="categoryFilter" class="filter-select">
                    <option value="">Toutes les catégories</option>
                    <option value="it">IT / Développement</option>
                    <option value="marketing">Marketing</option>
                    <option value="design">Design</option>
                    <option value="management">Management</option>
                    <option value="other">Autre</option>
                </select>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <h3 class="filters-title"><i class="fas fa-filter"></i> Filtres</h3>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Statut</label>
                    <select class="filter-select" id="statusFilter">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="accepted">Acceptée</option>
                        <option value="refused">Refusée</option>
                        <option value="reviewed">En revue</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Date</label>
                    <select class="filter-select" id="dateFilter">
                        <option value="">Toutes les dates</option>
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois</option>
                        <option value="older">Plus anciennes</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Trier par</label>
                    <select class="filter-select" id="sortFilter">
                        <option value="newest">Plus récentes</option>
                        <option value="oldest">Plus anciennes</option>
                        <option value="company">Entreprise</option>
                        <option value="title">Poste</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="applications-grid" id="applicationsGrid"></div>
        <div id="applicationsPagination" class="pagination" aria-label="Pagination"></div>
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
        let allApplications = [];
        let currentApplication = null;
        let applicationsCurrentPage = 1;
        const applicationsPageSize = 6; // 6 cards per page to match other pages

        // simple debounce helper
        function debounce(fn, wait) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        // Charger les candidatures au chargement
        document.addEventListener('DOMContentLoaded', function() {
            loadApplications();
            setupFilters();

            // If there's a global AI search input (shared UI on other pages), mirror it to the local search
            const aiInput = document.getElementById('ai-search-input');
            const localSearch = document.getElementById('searchInput');
            if (aiInput && localSearch) {
                const mirrored = debounce(function(e) {
                    localSearch.value = aiInput.value;
                    applyFilters();
                }, 250);
                aiInput.addEventListener('input', mirrored);
            }
        });

        // Charger toutes les candidatures de l'utilisateur
        function loadApplications() {
            // Dans un cas réel, vous devriez avoir un endpoint spécifique pour les candidatures de l'utilisateur
            // Pour l'instant, nous allons filtrer côté client
            fetch(`${API_URL}?action=getAllApplications`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    // Filtrer les candidatures de l'utilisateur connecté
                    const userId = <?php echo $_SESSION['user_id']; ?>;
                    allApplications = data.filter(app => app.user_id == userId);
                    updateStats();
                    displayApplications(allApplications);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('applicationsGrid').innerHTML = 
                        '<div class="empty-state">' +
                        '<i class="fas fa-exclamation-circle"></i>' +
                        '<h3>Erreur de chargement</h3>' +
                        '<p>Impossible de charger vos candidatures. Veuillez réessayer.</p>' +
                        '</div>';
                });
        }

        // Mettre à jour les statistiques
        function updateStats() {
            const total = allApplications.length;
            const pending = allApplications.filter(app => app.status === 'pending').length;
            const accepted = allApplications.filter(app => app.status === 'accepted').length;
            const refused = allApplications.filter(app => app.status === 'refused').length;

            document.getElementById('statTotal').textContent = total;
            document.getElementById('statPending').textContent = pending;
            document.getElementById('statAccepted').textContent = accepted;
            document.getElementById('statRefused').textContent = refused;
        }

        // Afficher les candidatures (paginated)
        function displayApplications(applications) {
            const grid = document.getElementById('applicationsGrid');
            grid.innerHTML = '';

            if (!applications || applications.length === 0) {
                grid.innerHTML = 
                    '<div class="empty-state">' +
                    '<i class="fas fa-file-alt"></i>' +
                    '<h3>Aucune candidature</h3>' +
                    '<p>Vous n\'avez pas encore postulé à des offres d\'emploi.</p>' +
                    '<a href="offres.php" class="btn btn-primary">' +
                    '<i class="fas fa-briefcase"></i> Voir les offres' +
                    '</a>' +
                    '</div>';
                document.getElementById('applicationsPagination').innerHTML = '';
                return;
            }

            const total = applications.length;
            const start = (applicationsCurrentPage - 1) * applicationsPageSize;
            const end = start + applicationsPageSize;
            const pageItems = applications.slice(start, end);

            pageItems.forEach(application => {
                const statusClass = getStatusClass(application.status);
                const statusLabel = getStatusLabel(application.status);
                const formattedDate = formatDate(application.created_at);

                const card = document.createElement('div');
                card.className = 'application-card';
                card.innerHTML = `
                    <div class="application-header">
                        <div>
                            <h3 class="application-title">${escapeHtml(application.job_title || 'Poste non spécifié')}</h3>
                            <p class="application-company">🏢 ${escapeHtml(application.company || 'Entreprise non spécifiée')}</p>
                        </div>
                        <span class="status-badge ${statusClass}">
                            <i class="fas ${getStatusIcon(application.status)}"></i>
                            ${statusLabel}
                        </span>
                    </div>
                    
                    <p class="application-description">${escapeHtml(application.cover?.substring(0, 150) || 'Aucune lettre de motivation fournie')}...</p>
                    
                    <div class="application-details">
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <span>Postulé le: ${formattedDate}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-file-pdf"></i>
                            <span>CV: ${application.cv_filename || 'Non fourni'}</span>
                        </div>
                    </div>
                    
                    <div class="admin-controls">
                        <button onclick="viewApplicationDetails(${application.id})" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Détails
                        </button>
                        <button onclick="viewJobDetails(${application.job_id})" class="btn btn-edit btn-sm">
                            <i class="fas fa-briefcase"></i> Voir l'offre
                        </button>
                        <button onclick="deleteApplication(${application.id})" class="btn btn-delete btn-sm">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                `;
                grid.appendChild(card);
            });

            renderApplicationsPagination(total);
        }

        function renderApplicationsPagination(totalItems) {
            const container = document.getElementById('applicationsPagination');
            container.innerHTML = '';
            const totalPages = Math.max(1, Math.ceil(totalItems / applicationsPageSize));

            const prev = document.createElement('button');
            prev.textContent = '‹ Prev';
            prev.disabled = applicationsCurrentPage === 1;
            prev.addEventListener('click', () => {
                if (applicationsCurrentPage > 1) {
                    applicationsCurrentPage--;
                    applyFilters(false);
                }
            });
            container.appendChild(prev);

            for (let p = 1; p <= totalPages; p++) {
                const btn = document.createElement('button');
                btn.textContent = p;
                if (p === applicationsCurrentPage) btn.classList.add('active');
                btn.addEventListener('click', () => {
                    applicationsCurrentPage = p;
                    applyFilters(false);
                });
                container.appendChild(btn);
            }

            const next = document.createElement('button');
            next.textContent = 'Next ›';
            next.disabled = applicationsCurrentPage === totalPages;
            next.addEventListener('click', () => {
                if (applicationsCurrentPage < totalPages) {
                    applicationsCurrentPage++;
                    applyFilters(false);
                }
            });
            container.appendChild(next);
        }

        function getFilteredApplications() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilterVal = document.getElementById('statusFilter').value;
            const categoryFilterVal = (document.getElementById('categoryFilter') ? document.getElementById('categoryFilter').value : '');
            const dateFilterVal = document.getElementById('dateFilter').value;
            const sortFilterVal = document.getElementById('sortFilter').value;

            let filtered = allApplications.filter(application => {
                const matchesSearch = 
                    (application.job_title && application.job_title.toLowerCase().includes(searchTerm)) ||
                    (application.company && application.company.toLowerCase().includes(searchTerm)) ||
                    (application.cover && application.cover.toLowerCase().includes(searchTerm));

                const matchesStatus = !statusFilterVal || application.status === statusFilterVal;
                const matchesDate = filterByDate(application.created_at, dateFilterVal);
                // Category may be present on application (if backend provides it) or on nested job info
                const appCategory = application.category || application.job_category || '';
                const matchesCategory = !categoryFilterVal || (appCategory && appCategory.toLowerCase() === categoryFilterVal.toLowerCase());

                return matchesSearch && matchesStatus && matchesDate && matchesCategory;
            });

            filtered = sortApplications(filtered, sortFilterVal);
            return filtered;
        }

        // Obtenir la classe CSS du statut
        function getStatusClass(status) {
            const classes = {
                'pending': 'status-pending',
                'accepted': 'status-accepted',
                'refused': 'status-refused',
                'reviewed': 'status-reviewed'
            };
            return classes[status] || 'status-pending';
        }

        // Obtenir le libellé du statut
        function getStatusLabel(status) {
            const labels = {
                'pending': 'En attente',
                'accepted': 'Acceptée',
                'refused': 'Refusée',
                'reviewed': 'En revue'
            };
            return labels[status] || 'En attente';
        }

        // Obtenir l'icône du statut
        function getStatusIcon(status) {
            const icons = {
                'pending': 'fa-clock',
                'accepted': 'fa-check-circle',
                'refused': 'fa-times-circle',
                'reviewed': 'fa-eye'
            };
            return icons[status] || 'fa-clock';
        }

        // Configuration des filtres
        function setupFilters() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const categoryFilter = document.getElementById('categoryFilter');
            const dateFilter = document.getElementById('dateFilter');
            const sortFilter = document.getElementById('sortFilter');

            

            // Recherche
            searchInput.addEventListener('input', function(e) {
                applyFilters();
            });

            // Filtres
            statusFilter.addEventListener('change', applyFilters);
            if(categoryFilter) categoryFilter.addEventListener('change', applyFilters);
            dateFilter.addEventListener('change', applyFilters);
            sortFilter.addEventListener('change', applyFilters);
        }

        // Appliquer les filtres (optionally reset to page 1)
        function applyFilters(resetPage = true) {
            if (resetPage) applicationsCurrentPage = 1;
            const filtered = getFilteredApplications();
            displayApplications(filtered);
        }

        // Filtrer par date
        function filterByDate(dateString, filterType) {
            if (!filterType) return true;

            const applicationDate = new Date(dateString);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            switch(filterType) {
                case 'today':
                    return applicationDate.toDateString() === today.toDateString();
                case 'week':
                    const weekAgo = new Date(today);
                    weekAgo.setDate(today.getDate() - 7);
                    return applicationDate >= weekAgo;
                case 'month':
                    const monthAgo = new Date(today);
                    monthAgo.setMonth(today.getMonth() - 1);
                    return applicationDate >= monthAgo;
                case 'older':
                    const monthAgoOlder = new Date(today);
                    monthAgoOlder.setMonth(today.getMonth() - 1);
                    return applicationDate < monthAgoOlder;
                default:
                    return true;
            }
        }

        // Trier les candidatures
        function sortApplications(applications, sortType) {
            return [...applications].sort((a, b) => {
                switch(sortType) {
                    case 'oldest':
                        return new Date(a.created_at) - new Date(b.created_at);
                    case 'company':
                        return (a.company || '').localeCompare(b.company || '');
                    case 'title':
                        return (a.job_title || '').localeCompare(b.job_title || '');
                    case 'newest':
                    default:
                        return new Date(b.created_at) - new Date(a.created_at);
                }
            });
        }

        // Voir les détails d'une candidature
        function viewApplicationDetails(applicationId) {
            fetch(`${API_URL}?action=getApplication&id=${applicationId}`)
                .then(response => response.json())
                .then(application => {
                    if(!application) {
                        alert('Candidature introuvable');
                        return;
                    }
                    
                    currentApplication = application;
                    document.getElementById('modalTitle').textContent = 'Détails de la candidature';
                    
                    let bodyHTML = `
                        <div class="detail-item">
                            <div class="detail-label">Poste</div>
                            <div class="detail-value">${escapeHtml(application.job_title || 'Non spécifié')}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Entreprise</div>
                            <div class="detail-value">${escapeHtml(application.company || 'Non spécifiée')}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Statut</div>
                            <div class="detail-value">
                                <span class="status-badge ${getStatusClass(application.status)}">
                                    <i class="fas ${getStatusIcon(application.status)}"></i>
                                    ${getStatusLabel(application.status)}
                                </span>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Date de candidature</div>
                            <div class="detail-value">${formatDateLong(application.created_at)}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">CV joint</div>
                            <div class="detail-value">${application.cv_filename || 'Aucun CV joint'}</div>
                        </div>
                    `;
                    
                    if(application.cover) {
                        bodyHTML += `
                            <div class="detail-item">
                                <div class="detail-label">Lettre de motivation</div>
                                <div class="detail-value" style="white-space: pre-wrap;">${escapeHtml(application.cover)}</div>
                            </div>
                        `;
                    }
                    
                    document.getElementById('modalBody').innerHTML = bodyHTML;
                    openModal();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des détails');
                });
        }

        // Voir les détails de l'offre d'emploi
        function viewJobDetails(jobId) {
            fetch(`${API_URL}?action=getJob&id=${jobId}`)
                .then(response => response.json())
                .then(job => {
                    if(!job) {
                        alert('Offre introuvable');
                        return;
                    }
                    
                    document.getElementById('modalTitle').textContent = job.title;
                    
                    let bodyHTML = `
                        <div class="detail-item">
                            <div class="detail-label">Entreprise</div>
                            <div class="detail-value">${escapeHtml(job.company)}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Description</div>
                            <div class="detail-value">${escapeHtml(job.description)}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Lieu</div>
                            <div class="detail-value">📍 ${escapeHtml(job.location)}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Type de contrat</div>
                            <div class="detail-value">${getTypeLabel(job.contract_type)}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Salaire</div>
                            <div class="detail-value">${job.salary || 'À négocier'}</div>
                        </div>
                    `;
                    
                    document.getElementById('modalBody').innerHTML = bodyHTML;
                    openModal();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des détails de l\'offre');
                });
        }

        // Supprimer une candidature
        function deleteApplication(applicationId) {
            if(!confirm('Êtes-vous sûr de vouloir supprimer cette candidature ? Cette action est irréversible.')) {
                return;
            }
            
            fetch(`${API_URL}?action=deleteApplication&application_id=${applicationId}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Candidature supprimée avec succès');
                    loadApplications(); // Recharger la liste
                } else {
                    alert('Erreur: ' + result.message);
                }
            })
            .catch(error => {
                alert('Erreur lors de la suppression: ' + error.message);
            });
        }

        // Obtenir le libellé du type de contrat (identique à offres.php)
        function getTypeLabel(type) {
            const types = {
                'fulltime': 'Temps plein',
                'parttime': 'Temps partiel',
                'remote': 'Télétravail',
                'internship': 'Stage'
            };
            return types[type] || 'Autre';
        }

        // Ouvrir/Fermer le modal
        function openModal() {
            document.getElementById('detailsModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            currentApplication = null;
        }

        // Fonctions utilitaires
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric'
            });
        }

        function formatDateLong(dateStr) {
            const date = new Date(dateStr);
            const options = { 
                weekday: 'long',
                day: 'numeric', 
                month: 'long', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return date.toLocaleDateString('fr-FR', options);
        }

        function escapeHtml(text) {
            if (!text) return '';
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
    </script>
</body>
</html>