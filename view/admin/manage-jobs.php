<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - Gestion des offres d'emploi</title>
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
        
        .admin-badge {
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            margin-left: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
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
        
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: var(--secondary);
            font-weight: 500;
        }
        
        /* Contenu principal */
        .dashboard-container {
            max-width: 1100px;
            margin: 140px auto 30px !important;
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
        
        /* Cartes de statistiques */
        .stats-grid {
            display: flex;
            gap: 26px;
            justify-content: center;
            align-items: stretch;
            flex-wrap: wrap;
            margin-bottom: 36px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.03) 100%);
            border-radius: 14px;
            padding: 18px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.18s ease;
            position: relative;
            overflow: hidden;
            min-width: 200px;
        }
        
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stat-icon.secondary {
            background: linear-gradient(135deg, var(--secondary) 0%, #6abeb9 100%);
        }
        
        .stat-icon.accent {
            background: linear-gradient(135deg, var(--accent) 0%, #e55a87 100%);
        }
        
        .stat-content {
            text-align: left;
        }
        
        .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--light) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin: 8px 0;
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 1rem;
            font-weight: 500;
        }
        
        /* Grille d'offres */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 18px;
            align-items: start;
            justify-content: center; /* center grid within container */
            padding: 8px 0 20px 0;
            width: 100%;
            max-width: 980px;
        }

        /* Pagination for admin jobs */
        .pagination {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .pagination button {
            background: rgba(255,255,255,0.04);
            color: var(--light);
            border: 1px solid rgba(255,255,255,0.04);
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

        /* Medium screens: two columns */
        @media (max-width: 1024px) {
            .events-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .events-grid {
                grid-template-columns: 1fr;
            }

            .admin-controls .btn {
                min-width: unset;
                width: 100%;
            }
        }
        
        .event-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.03) 100%);
            border-radius: 12px;
            padding: 12px 14px 18px 14px;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.04);
            transition: transform 180ms ease, box-shadow 180ms ease;
            position: relative;
            overflow: hidden;
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            min-height: 300px;
            will-change: transform, box-shadow;
        }
        
        .event-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 32px rgba(0, 0, 0, 0.32);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .event-image {
            width: 100%;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 10px;
            flex: 0 0 auto;
        }
        
        .event-img {
            height: 100%;
            object-fit: cover;  
        }
        
        .event-title {
            font-size: 0.98rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--light);
        }
        
        .event-date {
            color: var(--secondary);
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .event-description {
            color: var(--gray);
            font-size: 0.85rem;
            line-height: 1.35;
            margin-bottom: 10px;
            flex: 1 1 auto;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .admin-controls {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: auto;
            align-items: center; /* center buttons horizontally */
            justify-content: flex-end;
            padding-top: 8px;
        }
        
        /* Boutons */
        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.18s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 0.75rem;
            border-radius: 8px;
        }

        /* Ensure buttons in admin-controls are uniform and do not wrap */
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

        /* Primary button uses full width like others */
        .admin-controls .btn.btn-primary {
            width: auto;
            min-width: 160px;
        }

        /* Tweak hover to be less aggressive */
        .admin-controls .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.18);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(110, 69, 226, 0.3);
        }
        
        .btn-add {
            background: linear-gradient(135deg, var(--success) 0%, #219653 100%);
            color: white;
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
        }
        
        .btn-edit {
            background: linear-gradient(135deg, var(--warning) 0%, #e0a800 100%);
            color: black;
        }
        
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 193, 7, 0.3);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: white;
        }
        
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.3);
        }
        
        .btn-logout {
            background: linear-gradient(135deg, var(--accent) 0%, #e55a87 100%);
            color: white;
        }
        
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 157, 0.3);
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
        
        .badge-active {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .badge-inactive {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        /* Alertes */
        .alert {
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            backdrop-filter: blur(20px);
            border: 1px solid;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-color: rgba(40, 167, 69, 0.3);
            color: #88ffaa;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.3);
            color: #ff6b6b;
        }
        
        /* Modal Styles */
        .event-modal {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 2000;
            background: linear-gradient(135deg, var(--dark), var(--darker));
            color: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
            min-width: 380px;
            max-width: 500px;
            border: 1px solid rgba(110, 69, 226, 0.3);
            max-height: 90vh;
            overflow-y: auto;
        }

        .event-modal .modal-content {
            display: block;
        }

        .event-modal h3 {
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            color: var(--secondary);
        }

        .event-modal form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .event-modal input,
        .event-modal textarea,
        .event-modal select {
            background: var(--dark);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            padding: 12px 15px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .event-modal input:focus,
        .event-modal textarea:focus,
        .event-modal select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 12px rgba(110, 69, 226, 0.4);
            background: rgba(110, 69, 226, 0.05);
        }

        .event-modal textarea {
            resize: vertical;
            min-height: 80px;
        }

        .event-modal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .modal-close {
            background: transparent;
            color: var(--gray);
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 6px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .modal-close:hover {
            background: rgba(255,255,255,0.03);
            color: var(--light);
        }

        .event-modal .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .event-modal .btn-cancel {
            background: var(--dark);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .event-modal .btn-cancel:hover {
            background: var(--darker);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .event-modal .btn-submit {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            padding: 12px 20px;
            border-radius: 6px;
            border: none;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .event-modal .btn-submit:hover {
            box-shadow: 0 8px 24px rgba(110, 69, 226, 0.4);
            transform: translateY(-2px);
        }

        /* Modal backdrop (overlay) */
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1999;
            animation: fadeIn 0.2s ease;
        }
        
        /* Styles for application cards: match event/offers card style */
        .application-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.03) 100%);
            border-radius: 12px;
            padding: 16px;
            margin: 12px 0;
            border: 1px solid rgba(255, 255, 255, 0.04);
            transition: transform 200ms ease, box-shadow 200ms ease;
            width: 100%;
            box-sizing: border-box;
            overflow: hidden;
        }

        .application-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 32px rgba(0,0,0,0.28);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .status-accepted {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .status-refused {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .applications-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .cv-download {
            color: var(--secondary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }
        
        .cv-download:hover {
            color: var(--light);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
        
        select.form-control {
            cursor: pointer;
        }
        
        .image-preview {
            width: 100%;
            max-width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .hidden {
            display: none;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .btn-cancel {
            background: rgba(255, 255, 255, 0.1);
            color: var(--gray);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(110, 69, 226, 0.3);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .nav-main {
                gap: 20px;
            }
            
            .nav-links {
                gap: 0;
            }
            
            .nav-link {
                padding: 15px;
                font-size: 0.9rem;
            }
        }
        
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
            
            .nav-actions {
                width: 100%;
                justify-content: center;
            }
            
            .dashboard-container {
                margin-top: 140px;
            }
            
            .stats-grid,
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .admin-controls {
                flex-direction: column;
                gap: 8px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
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
            
            .user-info {
                flex-direction: column;
                text-align: center;
                gap: 5px;
            }
            
            .modal-content {
                padding: 20px;
            }
            .status-badge {
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
            }

            .status-pending {
                background: rgba(255, 193, 7, 0.2);
                color: #ffc107;
                border: 1px solid rgba(255, 193, 7, 0.3);
            }

            .status-accepted {
                background: rgba(40, 167, 69, 0.2);
                color: #28a745;
                border: 1px solid rgba(40, 167, 69, 0.3);
            }

            .status-refused {
                background: rgba(220, 53, 69, 0.2);
                color: #dc3545;
                border: 1px solid rgba(220, 53, 69, 0.3);
            }

            .status-reviewed {
                background: rgba(23, 162, 184, 0.2);
                color: #17a2b8;
                border: 1px solid rgba(23, 162, 184, 0.3);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation moderne -->
    <nav class="admin-nav">
        <div class="nav-container">
            <div class="nav-main">
                <a href="#" class="logo">
                    <span class="logo-text">PRO MANAGE AI</span>
                    <span class="admin-badge">RECRUTEUR</span>
                </a>
                
                <ul class="nav-links">
                    <li>
                        <a href="manage-jobs.php" class="nav-link active">
                            <i class="fas fa-briefcase"></i>
                            <span>Gestion Offres</span>
                        </a>
                </ul>
            </div>
            
            <div class="nav-actions">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php 
                            $initial = isset($_SESSION['user_prenom']) ? strtoupper(substr($_SESSION['user_prenom'], 0, 1)) : 'R';
                            echo $initial;
                        ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name">
                            <?php echo htmlspecialchars($_SESSION['user_prenom'] ?? 'admin') . ' ' . htmlspecialchars($_SESSION['user_nom'] ?? ''); ?>
                        </div>
                        <div class="user-role">Recruteur</div>
                    </div>
                </div>
                
                <button id="adminDeconnectBtn" class="btn btn-logout btn-sm" title="Logout" style="background:transparent;color:#fff;border:none;border-radius:6px;padding:6px 8px;">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
    </nav>
    <script>
        const adminDeconnectBtn = document.getElementById('adminDeconnectBtn');
        if (adminDeconnectBtn) {
            adminDeconnectBtn.addEventListener('click', function () {
                window.location.href = '../deconnecter.php';
            });
        }
    </script>

    <!-- Contenu principal -->
    <div class="dashboard-container" style="margin-top:220px !important;">
        <!-- En-tête de page -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Gestion des offres d'emploi</h1>
                <p class="page-subtitle">Créez et gérez vos offres d'emploi et candidatures</p>
            </div>
            <div style="display:flex; gap:12px; align-items:center;">
                <input id="adminSearchInput" type="search" placeholder="Rechercher offres / candidatures..." style="padding:10px 14px; border-radius:10px; border:1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.03); color:var(--light); min-width:260px;">
                <button class="btn btn-add" onclick="showAddJobModal()">
                    <i class="fas fa-plus"></i> Ajouter une offre
                </button>
            </div>
        </div>
        
        <!-- Messages d'alerte -->
        <div id="success-container"></div>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="totalJobs">0</div>
                    <div class="stat-label">Offres totales</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon secondary">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="activeJobs">0</div>
                    <div class="stat-label">Offres actives</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon accent">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="totalApplications">0</div>
                    <div class="stat-label">Candidatures</div>
                </div>
            </div>
        </div>
        
        <!-- Grille d'offres d'emploi -->
        <div class="events-grid" id="jobsGrid">
            <!-- Les offres seront chargées ici dynamiquement -->
        </div>
        <div id="jobsPagination" class="pagination" aria-label="Pagination"></div>
    </div>

    <!-- Modal Ajouter/Modifier une offre -->
    <div id="jobModal" class="event-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Ajouter une offre d'emploi</h3>
                <button class="modal-close" onclick="closeJobModal()">✕</button>
            </div>
            
            <form id="jobForm" class="event-form">
                <input type="hidden" id="jobId" value="">
                
                <div class="form-group">
                    <label for="jobTitle">Titre du poste *</label>
                    <input type="text" id="jobTitle" class="form-control" placeholder="Ex: Développeur Web Full Stack" required maxlength="255">
                       <div class="field-error" id="jobTitleError" style="display:none;color:#ffcccc;margin-top:6px;font-size:0.9rem;"></div>
                </div>
                
                <div class="form-group">
                    <label for="jobCompany">Entreprise *</label>
                    <input type="text" id="jobCompany" class="form-control" placeholder="Nom de l'entreprise" required maxlength="255">
                       <div class="field-error" id="jobCompanyError" style="display:none;color:#ffcccc;margin-top:6px;font-size:0.9rem;"></div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="jobSalary">Salaire</label>
                           <input type="text" id="jobSalary" class="form-control" placeholder="Ex: 2000 ou 2000 TND">
                           <div class="field-error" id="jobSalaryError" style="display:none;color:#ffcccc;margin-top:6px;font-size:0.9rem;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="jobLocation">Lieu *</label>
                        <input type="text" id="jobLocation" class="form-control" placeholder="Ex: Paris, France" required>
                           <div class="field-error" id="jobLocationError" style="display:none;color:#ffcccc;margin-top:6px;font-size:0.9rem;"></div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="jobCategory">Catégorie *</label>
                        <select id="jobCategory" class="form-control" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="IT">IT</option>
                            <option value="Business">Business</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Design">Design</option>
                            <option value="Finance">Finance</option>
                            <option value="Sales">Sales</option>
                            <option value="RH">RH</option>
                            <option value="Product">Product</option>
                            <option value="Operations">Operations</option>
                            <option value="Other">Autre</option>
                        </select>
                           <div class="field-error" id="jobCategoryError" style="display:none;color:#ffcccc;margin-top:6px;font-size:0.9rem;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="jobContractType">Type de contrat *</label>
                        <select id="jobContractType" class="form-control" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="CDI">CDI</option>
                            <option value="CDD">CDD</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Stage">Stage</option>
                            <option value="Alternance">Alternance</option>
                        </select>
                           <div class="field-error" id="jobContractTypeError" style="display:none;color:#ffcccc;margin-top:6px;font-size:0.9rem;"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="jobDescription">Description du poste *</label>
                    <textarea id="jobDescription" class="form-control" placeholder="Décrivez les missions, compétences requises..." rows="6" required></textarea>
                       <div class="field-error" id="jobDescriptionError" style="display:none;color:#ffcccc;margin-top:6px;font-size:0.9rem;"></div>
                </div>
                
                <div class="form-group">
                    <label for="jobLogo">URL du logo</label>
                    <input type="url" id="jobLogo" class="form-control" placeholder="https://example.com/logo.png">
                       <div class="field-error" id="jobLogoError" style="display:none;color:#ffcccc;margin-top:6px;font-size:0.9rem;"></div>
                </div>
                
                <div class="form-group">
                    <label for="jobDatePosted">Date de publication</label>
                    <input type="date" id="jobDatePosted" class="form-control" required>
                       <div class="field-error" id="jobDatePostedError" style="display:none;color:#ffcccc;margin-top:6px;font-size:0.9rem;"></div>
                </div>
                
                <div class="form-group">
                    <label for="jobStatus">Statut</label>
                    <select id="jobStatus" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeJobModal()">Annuler</button>
                    <button type="submit" class="btn btn-submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Voir les candidatures -->
    <div id="applicationsModal" class="event-modal" style="max-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="applicationsModalTitle">Candidatures</h3>
                <button class="modal-close" onclick="closeApplicationsModal()">✕</button>
            </div>
            
            <div id="applicationsList" class="applications-section">
                <!-- Les candidatures seront chargées ici -->
            </div>
        </div>
    </div>

    <div id="modalBackdrop" class="modal-backdrop" onclick="closeJobModal(); closeApplicationsModal();"></div>

    <script>
        const JOB_API_URL = '../../controller/JobController.php';
        let currentEditId = null;
        let currentJobApplications = null;
        let jobsCurrentPage = 1;
        const jobsPageSize = 6;
        let allJobs = [];

        // Charger les offres au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadJobs();
            loadStats();
            setupEventListeners();
            
            // Date par défaut pour la publication
            document.getElementById('jobDatePosted').valueAsDate = new Date();
        });

        // Configuration des écouteurs d'événements
        function setupEventListeners() {
            // Soumettre le formulaire
            document.getElementById('jobForm').addEventListener('submit', handleFormSubmit);
            const searchEl = document.getElementById('adminSearchInput');
            if (searchEl) {
                let t;
                searchEl.addEventListener('input', function() {
                    clearTimeout(t);
                    t = setTimeout(() => {
                        jobsCurrentPage = 1;
                        const filtered = getFilteredJobs();
                        displayJobs(filtered);
                    }, 180);
                });
            }
        }

        // Charger toutes les offres
        function loadJobs() {
            fetch(`${JOB_API_URL}?action=getAllJobs`)
                .then(response => response.json())
                .then(data => {
                    allJobs = data;
                    // apply current admin search filter if any
                    const filtered = getFilteredJobs();
                    // ensure current page is valid
                    if (jobsCurrentPage > Math.ceil(Math.max(1, filtered.length) / jobsPageSize)) jobsCurrentPage = 1;
                    displayJobs(filtered);
                    renderJobsPagination(filtered.length);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showMessage('Erreur lors du chargement des offres', 'error');
                });
        }

        // Charger les statistiques
        function loadStats() {
            fetch(`${JOB_API_URL}?action=getStats`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalJobs').textContent = data.total_jobs || 0;
                    document.getElementById('activeJobs').textContent = data.active_jobs || 0;
                    document.getElementById('totalApplications').textContent = data.total_applications || 0;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
        }

        // Afficher les offres
        function displayJobs(jobs) {
            const grid = document.getElementById('jobsGrid');
            grid.innerHTML = '';

            if(jobs.length === 0) {
                grid.innerHTML = '<p style="text-align:center; color: var(--gray);">Aucune offre d\'emploi trouvée</p>';
                return;
            }

            // Pagination slice
            const total = jobs.length;
            const start = (jobsCurrentPage - 1) * jobsPageSize;
            const end = start + jobsPageSize;
            const pageItems = jobs.slice(start, end);

            pageItems.forEach(job => {
                const statusClass = job.status === 'active' ? 'badge-active' : 'badge-inactive';
                const statusText = job.status === 'active' ? 'ACTIVE' : 'INACTIVE';
                
                const card = document.createElement('div');
                card.className = 'event-card';
                card.innerHTML = `
                    <div class="event-image">
                        <img src="${escapeHtml(job.logo || 'https://via.placeholder.com/600x400?text=LOGO')}" 
                             alt="${escapeHtml(job.company)}" 
                             class="event-img" 
                             onerror="this.src='https://via.placeholder.com/600x400?text=LOGO'" />
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h3 class="event-title">${escapeHtml(job.title)}</h3>
                        <span class="badge ${statusClass}">${statusText}</span>
                    </div>
                    <p class="event-date">🏢 ${escapeHtml(job.company)}</p>
                    <p class="event-date">📍 ${escapeHtml(job.location)}</p>
                    <p class="event-date">💰 ${escapeHtml(job.salary || 'Non spécifié')}</p>
                    <p class="event-date">📄 ${escapeHtml(job.contract_type)} - ${escapeHtml(job.category)}</p>
                    <p class="event-description">${escapeHtml(job.description.substring(0, 150))}...</p>
                    <p class="event-date">📅 ${formatDate(job.date_posted)}</p>
                    <p style="color: var(--secondary); font-size: 12px; margin-top: 8px;">📋 ${job.application_count || 0} candidature(s)</p>
                    <div class="admin-controls">
                        <button onclick="viewApplications(${job.id})" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Voir candidatures
                        </button>
                        <button onclick="editJob(${job.id})" class="btn btn-edit btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <button onclick="deleteJob(${job.id})" class="btn btn-delete btn-sm">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                `;
                grid.appendChild(card);
            });

            renderJobsPagination(jobs.length);
        }

        // Return filtered jobs according to admin search input
        function getFilteredJobs() {
            const q = (document.getElementById('adminSearchInput') && document.getElementById('adminSearchInput').value.trim().toLowerCase()) || '';
            if (!q) return allJobs.slice();
            return allJobs.filter(j => {
                const hay = (j.title + ' ' + (j.company||'') + ' ' + (j.description||'') + ' ' + (j.location||'') + ' ' + (j.category||'')).toLowerCase();
                return hay.indexOf(q) !== -1;
            });
        }

        function renderJobsPagination(totalItems) {
            const container = document.getElementById('jobsPagination');
            if (!container) return;
            container.innerHTML = '';

            const totalPages = Math.max(1, Math.ceil(totalItems / jobsPageSize));

            function makeBtn(label, page, cls) {
                const b = document.createElement('button');
                b.textContent = label;
                if (cls) b.classList.add(cls);
                b.addEventListener('click', () => {
                    if (page < 1 || page > totalPages) return;
                    jobsCurrentPage = page;
                    const filtered = getFilteredJobs();
                    displayJobs(filtered);
                    window.scrollTo({ top: document.querySelector('.dashboard-container').offsetTop - 60, behavior: 'smooth' });
                });
                return b;
            }

            // Prev
            container.appendChild(makeBtn('‹ Prev', jobsCurrentPage - 1));

            const maxButtons = 7;
            let startPage = Math.max(1, jobsCurrentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage < maxButtons - 1) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            for (let p = startPage; p <= endPage; p++) {
                const btn = makeBtn(p, p, p === jobsCurrentPage ? 'active' : '');
                if (p === jobsCurrentPage) btn.classList.add('active');
                container.appendChild(btn);
            }

            container.appendChild(makeBtn('Next ›', jobsCurrentPage + 1));
        }

        // Ouvrir le modal d'ajout
        function showAddJobModal() {
            currentEditId = null;
            document.getElementById('modalTitle').textContent = 'Ajouter une offre d\'emploi';
            document.getElementById('jobForm').reset();
            document.getElementById('jobId').value = '';
            document.getElementById('jobDatePosted').valueAsDate = new Date();
            document.getElementById('jobStatus').value = 'active';
            showJobModal();
        }

        // Éditer une offre
        function editJob(id) {
            currentEditId = id;
            document.getElementById('modalTitle').textContent = 'Modifier l\'offre d\'emploi';
            
            fetch(`${JOB_API_URL}?action=getJob&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if(!data) {
                        showMessage('Offre introuvable', 'error');
                        return;
                    }
                    
                    document.getElementById('jobId').value = data.id;
                    document.getElementById('jobTitle').value = data.title;
                    document.getElementById('jobCompany').value = data.company;
                    document.getElementById('jobSalary').value = data.salary || '';
                    document.getElementById('jobDescription').value = data.description;
                    document.getElementById('jobLocation').value = data.location;
                    document.getElementById('jobDatePosted').value = data.date_posted;
                    // Ensure category option exists in case it's a custom value
                    const jobCatEl = document.getElementById('jobCategory');
                    if(jobCatEl) {
                        const exists = Array.from(jobCatEl.options).some(o => o.value === data.category);
                        if(!exists && data.category) {
                            const opt = document.createElement('option');
                            opt.value = data.category;
                            opt.textContent = data.category;
                            jobCatEl.appendChild(opt);
                        }
                        jobCatEl.value = data.category;
                    }
                    document.getElementById('jobContractType').value = data.contract_type;
                    document.getElementById('jobLogo').value = data.logo || '';
                    document.getElementById('jobStatus').value = data.status;
                    
                    showJobModal();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showMessage('Erreur lors du chargement de l\'offre', 'error');
                });
        }

        // Supprimer une offre
        function deleteJob(id) {
            if(!confirm('Êtes-vous sûr de vouloir supprimer cette offre d\'emploi ?')) {
                return;
            }
            
            fetch(`${JOB_API_URL}?action=deleteJob&id=${id}`, { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        showMessage('Offre supprimée avec succès', 'success');
                        loadJobs();
                        loadStats();
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showMessage('Erreur lors de la suppression', 'error');
                });
        }

        // Charger toutes les candidatures de l'utilisateur
        function loadApplications() {
            const userId = <?php echo $_SESSION['user_id']; ?>;
            
            fetch(`${API_URL}?action=getApplicationsByUser&user_id=${userId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    allApplications = data;
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
        // Voir les candidatures
// Voir les candidatures - CORRECTION DE L'URL
        function viewApplications(jobId) {
            // CORRECTION: Utiliser le bon paramètre 'job_id' au lieu de 'id'
            fetch(`${JOB_API_URL}?action=getApplicationsByJob&job_id=${jobId}`)
                .then(response => response.json())
                .then(data => {
                    if(!data || data.length === 0) {
                        showMessage('Aucune candidature pour cette offre', 'info');
                        return;
                    }
                    
                    // Stocker les données pour référence future
                    currentJobApplications = {
                        id: jobId,
                        applications: data
                    };
                    
                    displayApplications(data);
                    showApplicationsModal();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showMessage('Erreur lors du chargement des candidatures', 'error');
                });
        }
        // Afficher les candidatures
       // Afficher les candidatures - VERSION CORRIGÉE
        function displayApplications(applications) {
            const container = document.getElementById('applicationsList');
            const title = document.getElementById('applicationsModalTitle');
            
            // Récupérer le titre du job depuis la première candidature (si disponible)
            const jobTitle = applications.length > 0 && applications[0].job_title 
                ? applications[0].job_title 
                : 'Offre #' + currentJobApplications.id;
            
            title.textContent = `Candidatures pour: ${escapeHtml(jobTitle)}`;
            
            if(applications.length === 0) {
                container.innerHTML = '<p style="text-align:center; color: var(--gray);">Aucune candidature pour cette offre</p>';
                return;
            }
            
            let html = `<p style="margin-bottom: 20px; color: var(--secondary);">${applications.length} candidature(s) reçue(s)</p>`;
            
            applications.forEach(app => {
                const statusClass = `status-${app.status}`;
                const statusText = getStatusLabel(app.status);
                
                html += `
                    <div class="application-card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                            <div style="flex: 1;">
                                <h4 style="color: var(--light); margin-bottom: 5px;">${escapeHtml(app.name)}</h4>
                                <p style="color: var(--secondary); margin: 0;">${escapeHtml(app.email)}</p>
                                <p style="color: var(--gray); font-size: 0.9rem; margin: 5px 0;">
                                    <i class="fas fa-calendar"></i> Postulé le: ${formatDate(app.created_at)}
                                </p>
                            </div>
                            <span class="status-badge ${statusClass}">${statusText}</span>
                        </div>
                        
                        ${app.cover ? `
                            <div style="margin: 10px 0;">
                                <strong style="color: var(--secondary);">Lettre de motivation:</strong>
                                <p style="color: var(--light); line-height: 1.5; margin-top: 5px; white-space: pre-wrap;">${escapeHtml(app.cover)}</p>
                            </div>
                        ` : '<p style="color: var(--gray); font-style: italic;">Aucune lettre de motivation</p>'}
                        
                        ${app.cv_filename ? `
                            <div style="margin-top: 10px;">
                                <a href="../../uploads/cv/${escapeHtml(app.cv_filename)}" class="cv-download" target="_blank" style="display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-download"></i> Télécharger le CV (${escapeHtml(app.cv_filename)})
                                </a>
                            </div>
                        ` : '<p style="color: var(--gray); font-style: italic;">Aucun CV joint</p>'}
                        
                        <div style="display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
                            ${app.status !== 'accepted' ? `
                                <button onclick="updateApplicationStatus(${app.id}, 'accepted')" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check"></i> Accepter
                                </button>
                            ` : ''}
                            
                            ${app.status !== 'refused' ? `
                                <button onclick="updateApplicationStatus(${app.id}, 'refused')" class="btn btn-delete btn-sm">
                                    <i class="fas fa-times"></i> Refuser
                                </button>
                            ` : ''}
                            
                            <button onclick="deleteApplication(${app.id})" class="btn btn-delete btn-sm">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        // Fonction utilitaire pour les libellés de statut
        function getStatusLabel(status) {
            const labels = {
                'pending': 'En attente',
                'accepted': 'Acceptée',
                'refused': 'Refusée',
                'reviewed': 'En revue'
            };
            return labels[status] || 'En attente';
        }
        // Mettre à jour le statut d'une candidature
        function updateApplicationStatus(applicationId, status) {
            fetch(`${JOB_API_URL}?action=updateApplicationStatus&application_id=${applicationId}&status=${status}`, { 
                method: 'POST' 
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Afficher un message de succès avec confirmation d'email
                    const statusLabel = status === 'accepted' ? 'acceptée' : 'refusée';
                    showMessage(`✓ Candidature marquée comme ${statusLabel} et email envoyé au candidat`, 'success');
                    // Recharger les candidatures après 1 seconde
                    setTimeout(() => {
                        viewApplications(currentJobApplications.id);
                        loadStats();
                    }, 1000);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showMessage('Erreur lors de la mise à jour', 'error');
            });
        }

        // Supprimer une candidature
        function deleteApplication(applicationId) {
            if(!confirm('Êtes-vous sûr de vouloir supprimer cette candidature ?')) {
                return;
            }
            
            fetch(`${JOB_API_URL}?action=deleteApplication&application_id=${applicationId}`, { 
                method: 'POST' 
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showMessage('Candidature supprimée avec succès', 'success');
                    // Recharger les candidatures
                    viewApplications(currentJobApplications.id);
                    loadStats();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showMessage('Erreur lors de la suppression', 'error');
            });
        }

        // Soumettre le formulaire
        function handleFormSubmit(e) {
            e.preventDefault();
            if(!validateForm()) {
                return;
            }

            // collect form values
            const data = {
                user_id: <?php echo $_SESSION['user_id']; ?>,
                title: document.getElementById('jobTitle').value.trim(),
                company: document.getElementById('jobCompany').value.trim(),
                salary: document.getElementById('jobSalary').value.trim(),
                description: document.getElementById('jobDescription').value.trim(),
                location: document.getElementById('jobLocation').value.trim(),
                date_posted: document.getElementById('jobDatePosted').value,
                category: document.getElementById('jobCategory').value.trim(),
                contract_type: document.getElementById('jobContractType').value,
                logo: document.getElementById('jobLogo').value.trim(),
                status: document.getElementById('jobStatus').value
            };
            
            // Validation
            if(!data.title || !data.company || !data.location || !data.description || !data.category || !data.contract_type) {
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }
            
            // Envoyer les données
            const action = currentEditId ? 'updateJob' : 'createJob';
            const url = currentEditId ? `${JOB_API_URL}?action=updateJob&id=${currentEditId}` : `${JOB_API_URL}?action=createJob`;
            
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    showMessage(result.message, 'success');
                    closeJobModal();
                    loadJobs();
                    loadStats();
                } else {
                    showMessage(result.message, 'error');
                    closeJobModal();
                    loadJobs();
                    loadStats();
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                closeJobModal();
                loadJobs();
                loadStats();
            });
        }

        // Validation helpers
        function showError(elId, message) {
            const el = document.getElementById(elId);
            if(!el) return;
            el.style.display = 'block';
            el.textContent = message;
        }

        function clearError(elId) {
            const el = document.getElementById(elId);
            if(!el) return;
            el.style.display = 'none';
            el.textContent = '';
        }

        function validateSalary(value) {
            if(!value) return true; // optional field
            // Remove inner spaces (e.g., "2 000" -> "2000") for validation
            const v = value.replace(/\s+/g, '');
            // Strict formats allowed:
            //  - integer: 2000
            //  - decimal: 2000.50 or 2000,50
            //  - optional suffix: TND (case-insensitive)
            // Reject ranges (e.g., 2000-3000) or currency symbols like €
            const re = /^\d+(?:[\.,]\d+)?(?:tnd)?$/i;
            return re.test(v);
        }

        function isValidURL(value) {
            if(!value) return true; // optional
            try {
                const u = new URL(value);
                return ['http:', 'https:'].includes(u.protocol);
            } catch(e) {
                return false;
            }
        }

        function validateForm() {
            let ok = true;

            // Clear previous errors
            ['jobTitleError','jobCompanyError','jobSalaryError','jobLocationError','jobCategoryError','jobContractTypeError','jobDescriptionError','jobLogoError','jobDatePostedError'].forEach(clearError);

            const title = document.getElementById('jobTitle').value.trim();
            const company = document.getElementById('jobCompany').value.trim();
            const salary = document.getElementById('jobSalary').value.trim();
            const location = document.getElementById('jobLocation').value.trim();
            const category = document.getElementById('jobCategory').value;
            const contractType = document.getElementById('jobContractType').value;
            const description = document.getElementById('jobDescription').value.trim();
            const logo = document.getElementById('jobLogo').value.trim();
            const datePosted = document.getElementById('jobDatePosted').value;

            if(!title) { showError('jobTitleError','Le titre est requis'); ok = false; }
            if(!company) { showError('jobCompanyError','Le nom de l\'entreprise est requis'); ok = false; }
            if(salary && !validateSalary(salary)) { showError('jobSalaryError','Salaire invalide. Utilisez uniquement des chiffres (ex: "2000" ou "2000 TND").'); ok = false; }
            if(!location) { showError('jobLocationError','Le lieu est requis'); ok = false; }
            if(!category) { showError('jobCategoryError','La catégorie est requise'); ok = false; }
            if(!contractType) { showError('jobContractTypeError','Le type de contrat est requis'); ok = false; }
            if(!description) { showError('jobDescriptionError','La description est requise'); ok = false; }
            if(logo && !isValidURL(logo)) { showError('jobLogoError','URL du logo invalide'); ok = false; }
            if(!datePosted || isNaN(new Date(datePosted).getTime())) { showError('jobDatePostedError','Date de publication invalide'); ok = false; }

            // Focus first error
            if(!ok) {
                const first = document.querySelector('.field-error[style*="display:block"]');
                if(first) {
                    const id = first.id.replace('Error','');
                    const field = document.getElementById(id);
                    if(field) field.focus();
                }
            }

            return ok;
        }

        // Live validation listeners
        ['jobTitle','jobCompany','jobSalary','jobLocation','jobCategory','jobContractType','jobDescription','jobLogo','jobDatePosted'].forEach(id => {
            const el = document.getElementById(id);
            if(!el) return;
            el.addEventListener('input', function() {
                // Clear specific error as user types
                clearError(id + 'Error');
                // Additional inline checks
                if(id === 'jobSalary') {
                    const v = el.value.trim();
                    if(v && !validateSalary(v)) {
                        showError('jobSalaryError','Format attendu: nombre (ex: 2000) ou nombre + TND');
                    } else {
                        clearError('jobSalaryError');
                    }
                }
                if(id === 'jobLogo') {
                    const v = el.value.trim();
                    if(v && !isValidURL(v)) showError('jobLogoError','URL invalide');
                }
            });
        });

        // Fonctions utilitaires
        function showJobModal() {
            document.getElementById('jobModal').style.display = 'block';
            document.getElementById('modalBackdrop').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeJobModal() {
            document.getElementById('jobModal').style.display = 'none';
            document.getElementById('modalBackdrop').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function showApplicationsModal() {
            document.getElementById('applicationsModal').style.display = 'block';
            document.getElementById('modalBackdrop').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeApplicationsModal() {
            document.getElementById('applicationsModal').style.display = 'none';
            document.getElementById('modalBackdrop').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function showMessage(message, type) {
            const container = document.getElementById('success-container');
            const className = type === 'success' ? 'alert-success' : 'alert-danger';
            container.innerHTML = `<div class="${className}">${message}</div>`;
            
            setTimeout(() => {
                container.innerHTML = '';
            }, 5000);
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR', { 
                day: '2-digit', 
                month: 'long', 
                year: 'numeric'
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Fermer les modals avec Échap
        document.addEventListener('keydown', (e) => {
            if(e.key === 'Escape') {
                closeJobModal();
                closeApplicationsModal();
            }
        });

    </script>
</body>
</html>