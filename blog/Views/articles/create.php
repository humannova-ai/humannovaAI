<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prism Flux | Créer un Nouvel Article</title>
    
    <!-- Google Fonts pour Orbitron et Rajdhani -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lien vers le style Prism Flux -->
    <link rel="stylesheet" href="./templatemo-prism-flux.css">
    
    <!-- Style spécifique pour la création d'article -->
    <style>
        /* ============================================
           CRÉATION D'ARTICLE - Style Prism Flux
           ============================================ */
        
        /* Reset et structure */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Orbitron', 'Rajdhani', sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Arrière-plan carbon fiber */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                repeating-linear-gradient(0deg,
                    transparent,
                    transparent 2px,
                    rgba(255, 255, 255, 0.03) 2px,
                    rgba(255, 255, 255, 0.03) 4px),
                repeating-linear-gradient(90deg,
                    transparent,
                    transparent 2px,
                    rgba(255, 255, 255, 0.03) 2px,
                    rgba(255, 255, 255, 0.03) 4px),
                linear-gradient(135deg,
                    #0a0a0a 0%,
                    #121212 25%,
                    #1a1a1a 50%,
                    #121212 75%,
                    #0a0a0a 100%);
            z-index: -2;
        }
        
        /* Grille animée */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(255, 51, 51, 0.05) 2px, transparent 2px),
                linear-gradient(90deg, rgba(255, 51, 51, 0.05) 2px, transparent 2px);
            background-size: 150px 150px;
            animation: gridMove 20s linear infinite;
            z-index: -1;
            opacity: 0.5;
        }
        
        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(150px, 150px); }
        }
        
        /* Container principal */
        .prism-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 30px 50px;
            position: relative;
            z-index: 1;
        }
        
        /* Header de page */
        .page-header {
            margin-bottom: 40px;
            padding-bottom: 25px;
            border-bottom: 2px solid #3a3a3a;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 200px;
            height: 2px;
            background: linear-gradient(90deg, #9945ff, #00a8ff, transparent);
            animation: headerGlow 3s ease-in-out infinite;
        }
        
        @keyframes headerGlow {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
        
        .creation-badge {
            display: inline-block;
            background: linear-gradient(135deg, #00ff88, #00a8ff);
            color: #0a0a0a;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 15px;
            font-family: 'Orbitron', monospace;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.4);
            animation: pulseBadge 2s infinite;
        }
        
        @keyframes pulseBadge {
            0%, 100% { transform: scale(1); box-shadow: 0 0 20px rgba(0, 255, 136, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 0 30px rgba(0, 255, 136, 0.6); }
        }
        
        h1 {
            font-size: 42px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #ffffff, #00ff88, #00ffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
            animation: gradientFlow 5s ease infinite;
        }
        
        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .page-subtitle {
            font-size: 16px;
            color: #b0b0b0;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        /* Formulaire de création */
        .create-form-container {
            background: linear-gradient(135deg,
                rgba(42, 42, 42, 0.3),
                rgba(26, 26, 26, 0.5));
            border: 1px solid #3a3a3a;
            border-radius: 20px;
            padding: 40px;
            margin-top: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.6s ease-out;
        }
        
        .form-group {
            margin-bottom: 30px;
            position: relative;
        }
        
        .form-label {
            display: block;
            color: #b0b0b0;
            margin-bottom: 12px;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .form-label span {
            color: #ff3333;
            margin-left: 3px;
        }
        
        .required-indicator {
            color: #ff3333;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        
        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 18px 20px;
            background: #121212;
            border: 1px solid #3a3a3a;
            border-radius: 10px;
            color: #ffffff;
            font-size: 16px;
            font-family: 'Rajdhani', sans-serif;
            transition: all 0.3s ease;
        }
        
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 0 2px rgba(0, 255, 136, 0.2);
            background: #1a1a1a;
        }
        
        .form-textarea {
            min-height: 250px;
            resize: vertical;
            line-height: 1.6;
        }
        
        .form-input::placeholder, .form-textarea::placeholder {
            color: #4a4a4a;
            font-style: italic;
        }
        
        /* Indicateur de caractères */
        .char-counter {
            text-align: right;
            font-size: 12px;
            color: #808080;
            margin-top: 8px;
            font-family: 'Orbitron', monospace;
        }
        
        .char-counter.warning {
            color: #ffaa00;
        }
        
        .char-counter.danger {
            color: #ff3333;
        }
        
        /* Prévisualisation en temps réel */
        .preview-section {
            margin-top: 40px;
            padding: 30px;
            background: rgba(26, 26, 26, 0.5);
            border-radius: 15px;
            border: 1px dashed #3a3a3a;
            display: none;
        }
        
        .preview-section.active {
            display: block;
            animation: slideDown 0.4s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #3a3a3a;
        }
        
        .preview-title {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #00ff88;
        }
        
        .preview-content {
            color: #b0b0b0;
            line-height: 1.6;
        }
        
        .toggle-preview {
            background: transparent;
            border: 1px solid #00ff88;
            color: #00ff88;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
            font-family: 'Orbitron', sans-serif;
            margin-bottom: 20px;
        }
        
        .toggle-preview:hover {
            background: rgba(0, 255, 136, 0.1);
        }
        
        /* Boutons d'action */
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #3a3a3a;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 35px;
            border: none;
            border-radius: 30px;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-family: 'Orbitron', sans-serif;
            font-size: 14px;
        }
        
        .btn-create {
            background: linear-gradient(135deg, #00ff88, #00a8ff);
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.3);
        }
        
        .btn-create:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.5);
        }
        
        .btn-cancel {
            background: linear-gradient(135deg, #4a4a4a, #2a2a2a);
            border: 1px solid #3a3a3a;
        }
        
        .btn-cancel:hover {
            background: linear-gradient(135deg, #3a3a3a, #1a1a1a);
            transform: translateY(-3px);
        }
        
        .btn-icon {
            font-size: 18px;
        }
        
        /* Suggestions de catégories */
        .category-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .category-tag {
            padding: 8px 16px;
            background: rgba(153, 69, 255, 0.1);
            border: 1px solid #9945ff;
            border-radius: 20px;
            color: #9945ff;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .category-tag:hover {
            background: #9945ff;
            color: #ffffff;
            transform: translateY(-2px);
        }
        
        /* Sections optionnelles */
        .optional-section {
            margin-top: 40px;
            padding: 25px;
            background: rgba(26, 26, 26, 0.3);
            border-radius: 15px;
            border: 1px dashed #3a3a3a;
        }
        
        .section-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #00a8ff;
            cursor: pointer;
            margin-bottom: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .section-toggle::before {
            content: '▶';
            transition: transform 0.3s ease;
        }
        
        .section-toggle.active::before {
            transform: rotate(90deg);
        }
        
        .optional-content {
            display: none;
            animation: fadeIn 0.3s ease-out;
        }
        
        .section-toggle.active + .optional-content {
            display: block;
        }
        
        /* Animation d'entrée */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Footer */
        .prism-footer {
            margin-top: 60px;
            padding: 30px;
            text-align: center;
            color: #808080;
            font-size: 14px;
            border-top: 1px solid #3a3a3a;
        }
        
        .prism-footer .highlight {
            color: #00ffff;
            font-weight: 700;
        }
        
        /* État de chargement */
        .loading {
            position: relative;
            pointer-events: none;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 10, 10, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            z-index: 10;
        }
        
        .loading::before {
            content: '⚡';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            z-index: 11;
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .prism-container {
                padding: 100px 15px 30px;
            }
            
            h1 {
                font-size: 32px;
            }
            
            .create-form-container {
                padding: 25px;
            }
            
            .form-input, .form-textarea, .form-select {
                padding: 15px;
                font-size: 14px;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
                padding: 14px;
            }
            
            .category-suggestions {
                justify-content: center;
            }
        }
        
        /* Guide de création */
        .creation-guide {
            margin-top: 40px;
            background: rgba(0, 168, 255, 0.1);
            border: 1px solid #00a8ff;
            border-radius: 15px;
            padding: 25px;
        }
        
        .guide-title {
            color: #00a8ff;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .guide-tip {
            color: #b0b0b0;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
        }
        
        .guide-tip::before {
            content: '💡';
            position: absolute;
            left: 0;
            top: 0;
        }
    </style>
</head>
<body>
    <div class="prism-container">
        <!-- Header -->
        <div class="page-header">
            <div class="creation-badge">
                NOUVEAU ARTICLE
            </div>
            <h1>Créer un Article</h1>
            <p class="page-subtitle">Rédigez votre nouvelle publication</p>
        </div>
        
        <!-- Formulaire de création -->
       <form method="POST" action="index.php?controller=article&action=create" class="create-form-container" id="createForm">
            <div class="form-group">
                <label for="titre" class="form-label">
                    Titre de l'article <span>*</span>
                </label>
                <input type="text" 
                       id="titre" 
                       name="titre" 
                       class="form-input" 
                       required 
                       placeholder="Donnez un titre percutant à votre article..."
                       maxlength="200"
                       autocomplete="off">
                <span class="required-indicator">Ce champ est obligatoire</span>
                <div class="char-counter" id="titleCounter">
                    <span id="titleLength">0</span> / 200 caractères
                </div>
            </div>
            
            <div class="form-group">
                <label for="contenu" class="form-label">
                    Contenu <span>*</span>
                </label>
                <textarea id="contenu" 
                          name="contenu" 
                          class="form-textarea" 
                          required 
                          placeholder="Commencez à rédiger votre contenu ici..."
                          rows="8"></textarea>
                <span class="required-indicator">Ce champ est obligatoire</span>
                <div class="char-counter" id="contentCounter">
                    <span id="contentLength">0</span> caractères
                </div>
            </div>
            
            <!-- Catégorie optionnelle -->
            <div class="optional-section">
                <div class="section-toggle" id="categoryToggle">
                    Ajouter une catégorie
                </div>
                <div class="optional-content" id="categoryContent">
                    <div class="form-group">
                        <label for="categorie" class="form-label">Catégorie</label>
                        <select id="categorie" name="categorie" class="form-select">
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="technologie">Technologie</option>
                            <option value="science">Science</option>
                            <option value="design">Design</option>
                            <option value="business">Business</option>
                            <option value="lifestyle">Lifestyle</option>
                            <option value="education">Éducation</option>
                        </select>
                        <div class="category-suggestions">
                            <div class="category-tag" data-category="technologie">Technologie</div>
                            <div class="category-tag" data-category="science">Science</div>
                            <div class="category-tag" data-category="design">Design</div>
                            <div class="category-tag" data-category="business">Business</div>
                            <div class="category-tag" data-category="lifestyle">Lifestyle</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statut optionnel -->
            <div class="optional-section">
                <div class="section-toggle" id="statusToggle">
                    Paramètres avancés
                </div>
                <div class="optional-content" id="statusContent">
                    <div class="form-group">
                        <label for="statut" class="form-label">Statut</label>
                        <select id="statut" name="statut" class="form-select">
                            <option value="brouillon">Brouillon</option>
                            <option value="publie" selected>Publié</option>
                            <option value="archive">Archivé</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="visible_tags" class="form-label">Mots-clés</label>
                        <input type="text" 
                               id="visible_tags" 
                               name="visible_tags" 
                               class="form-input" 
                               placeholder="Ajoutez des mots-clés séparés par des virgules"
                               maxlength="100">
                    </div>
                </div>
            </div>
            
            <!-- Bouton de prévisualisation -->
            <button type="button" class="toggle-preview" id="togglePreview">
                Aperçu en direct
            </button>
            
            <!-- Zone de prévisualisation -->
            <div class="preview-section" id="previewSection">
                <div class="preview-header">
                    <div class="preview-title">APERÇU</div>
                    <button type="button" class="toggle-preview" id="closePreview">
                        Masquer
                    </button>
                </div>
                <div class="preview-content" id="previewContent">
                    <!-- Le contenu sera inséré ici par JavaScript -->
                </div>
            </div>
            
            <!-- Actions du formulaire -->
            <div class="form-actions">
                <div class="btn-group-left">
                    <button type="submit" class="btn btn-create" id="createBtn">
                        <span class="btn-icon">🚀</span>
                        Publier l'article
                    </button>
                </div>
                
                <div class="btn-group-right">
                    <a href="index.php?controller=article&action=index" class="btn btn-cancel">
                        <span class="btn-icon">↩️</span>
                        Annuler
                    </a>
                </div>
            </div>
                        <!-- Actions du formulaire -->
            <div class="form-actions">
                <div class="btn-group-left">
                    <button type="submit" class="btn btn-create" id="createBtn">
                        <span class="btn-icon">🚀</span>
                        Publier l'article
                    </button>
                </div>
                
                <div class="btn-group-right">
                    <a href="index.php?controller=article&action=index" class="btn btn-cancel">
                        <span class="btn-icon">↩️</span>
                        Annuler
                    </a>
                </div>
            </div>

            <!-- AJOUTE ICI LES CHAMPS CACHÉS -->
            <!-- Champs cachés pour les données supplémentaires -->
            <input type="hidden" name="excerpt" id="hiddenExcerpt" value="">
            <input type="hidden" name="tags" id="hiddenTags" value="">

            <!-- Mettre à jour le champ tags pour qu'il corresponde au formulaire -->
            <div class="form-group" id="tagsGroup" style="display: none;">
                <label for="tags" class="form-label">Mots-clés</label>
                <input type="text" 
                       id="tags" 
                       name="tags" 
                       class="form-input" 
                       placeholder="Ajoutez des mots-clés séparés par des virgules"
                       maxlength="100"
                       value="">
            </div>
        </form>
        
        <!-- Guide de création -->
        <div class="creation-guide">
            <div class="guide-title">Conseils pour un article réussi</div>
            <div class="guide-tip">Rédigez un titre clair et accrocheur (idéalement entre 50 et 70 caractères)</div>
            <div class="guide-tip">Structurez votre contenu avec des paragraphes courts et des sous-titres</div>
            <div class="guide-tip">Vérifiez l'orthographe et la grammaire avant de publier</div>
            <div class="guide-tip">Utilisez la catégorisation pour améliorer la visibilité de votre article</div>
        </div>
        
        <!-- Footer -->
        <div class="prism-footer">
            Système de Gestion de Contenu Prism Flux | 
            Contrôleur: <span class="highlight">article</span> | 
            Action: <span class="highlight">create</span>
        </div>
    </div>
    
    <!-- Script pour les fonctionnalités interactives -->
    <script>document.addEventListener('DOMContentLoaded', function() {
    // Compteurs de caractères
    const titleInput = document.getElementById('titre');
    const contentInput = document.getElementById('contenu');
    const titleCounter = document.getElementById('titleCounter');
    const contentCounter = document.getElementById('contentCounter');
    const titleLength = document.getElementById('titleLength');
    const contentLength = document.getElementById('contentLength');
    
    // Champs cachés pour l'extrait et les tags
    const hiddenExcerpt = document.getElementById('hiddenExcerpt');
    const hiddenTags = document.getElementById('hiddenTags');
    
    function updateCounters() {
        const titleLen = titleInput.value.length;
        const contentLen = contentInput.value.length;
        
        titleLength.textContent = titleLen;
        contentLength.textContent = contentLen;
        
        // Mettre à jour les couleurs des compteurs
        titleCounter.className = 'char-counter';
        if (titleLen > 180) {
            titleCounter.classList.add('warning');
        }
        if (titleLen >= 200) {
            titleCounter.classList.add('danger');
        }
        
        contentCounter.className = 'char-counter';
        if (contentLen > 5000) {
            contentCounter.classList.add('warning');
        }
        if (contentLen > 10000) {
            contentCounter.classList.add('danger');
        }
    }
    
    // Générer l'extrait automatique
    function generateExcerpt() {
        const content = contentInput.value;
        // Enlever le HTML si présent et prendre les premiers 150 caractères
        const plainText = content.replace(/<[^>]*>/g, '');
        if (plainText.length > 150) {
            return plainText.substring(0, 147) + '...';
        }
        return plainText;
    }
    
    // Mettre à jour l'extrait automatiquement
    function updateExcerpt() {
        hiddenExcerpt.value = generateExcerpt();
    }
    
    titleInput.addEventListener('input', function() {
        updateCounters();
        updatePreview();
    });
    
    contentInput.addEventListener('input', function() {
        updateCounters();
        updateExcerpt(); // Générer l'extrait quand le contenu change
        updatePreview();
    });
    
    // Initialiser les compteurs
    updateCounters();
    updateExcerpt(); // Générer l'extrait initial
    
    // Prévisualisation en temps réel
    const togglePreviewBtn = document.getElementById('togglePreview');
    const closePreviewBtn = document.getElementById('closePreview');
    const previewSection = document.getElementById('previewSection');
    const previewContent = document.getElementById('previewContent');
    
    function updatePreview() {
        const title = titleInput.value || 'Titre de l\'article';
        const content = contentInput.value || 'Contenu de l\'article...';
        
        previewContent.innerHTML = `
            <h3 style="color: #ffffff; margin-bottom: 15px;">${title}</h3>
            <div style="color: #b0b0b0; line-height: 1.6; white-space: pre-wrap;">${content.replace(/\n/g, '<br>')}</div>
        `;
    }
    
    togglePreviewBtn.addEventListener('click', function() {
        updatePreview();
        previewSection.classList.toggle('active');
        togglePreviewBtn.textContent = previewSection.classList.contains('active') 
            ? 'Masquer l\'aperçu' 
            : 'Aperçu en direct';
    });
    
    closePreviewBtn.addEventListener('click', function() {
        previewSection.classList.remove('active');
        togglePreviewBtn.textContent = 'Aperçu en direct';
    });
    
    titleInput.addEventListener('input', updatePreview);
    contentInput.addEventListener('input', updatePreview);
    
    // Sections optionnelles
    const categoryToggle = document.getElementById('categoryToggle');
    const statusToggle = document.getElementById('statusToggle');
    const categoryContent = document.getElementById('categoryContent');
    const statusContent = document.getElementById('statusContent');
    
    categoryToggle.addEventListener('click', function() {
        categoryToggle.classList.toggle('active');
    });
    
    statusToggle.addEventListener('click', function() {
        statusToggle.classList.toggle('active');
    });
    
    // Suggestions de catégories
    const categoryTags = document.querySelectorAll('.category-tag');
    const categorySelect = document.getElementById('categorie');
    
    categoryTags.forEach(tag => {
        tag.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            categorySelect.value = category;
            
            // Animation de confirmation
            this.style.background = '#00ff88';
            this.style.color = '#0a0a0a';
            this.style.boxShadow = '0 0 15px rgba(0, 255, 136, 0.5)';
            
            setTimeout(() => {
                this.style.background = '';
                this.style.color = '';
                this.style.boxShadow = '';
            }, 1000);
        });
    });
    
    // Synchroniser le champ tags visible avec le champ caché
    const visibleTagsInput = document.getElementById('visible_tags'); // Changé de 'tags' à 'visible_tags'
    
    if (visibleTagsInput) {
        visibleTagsInput.addEventListener('input', function() {
            hiddenTags.value = this.value;
        });
        
        // Initialiser la valeur
        hiddenTags.value = visibleTagsInput.value;
    }
    
    // Synchroniser la catégorie avec les tags
    categorySelect.addEventListener('change', function() {
        const category = this.value;
        const currentTags = visibleTagsInput ? visibleTagsInput.value : '';
        
        // Si une catégorie est sélectionnée et n'est pas déjà dans les tags
        if (category && !currentTags.includes(category)) {
            if (currentTags) {
                visibleTagsInput.value = category + ', ' + currentTags;
            } else {
                visibleTagsInput.value = category;
            }
            hiddenTags.value = visibleTagsInput.value;
        }
    });
    
    // Validation du formulaire
    const form = document.getElementById('createForm');
    const createBtn = document.getElementById('createBtn');
    
    form.addEventListener('submit', function(e) {
        // Vérifier le titre
        if (!titleInput.value.trim()) {
            e.preventDefault();
            alert('Le titre est obligatoire.');
            titleInput.focus();
            titleInput.style.borderColor = '#ff3333';
            titleInput.style.boxShadow = '0 0 0 2px rgba(255, 51, 51, 0.2)';
            return;
        }
        
        // Vérifier le contenu
        if (!contentInput.value.trim()) {
            e.preventDefault();
            alert('Le contenu est obligatoire.');
            contentInput.focus();
            contentInput.style.borderColor = '#ff3333';
            contentInput.style.boxShadow = '0 0 0 2px rgba(255, 51, 51, 0.2)';
            return;
        }
        
        // S'assurer que l'extrait est bien généré
        if (!hiddenExcerpt.value.trim()) {
            updateExcerpt();
        }
        
        // S'assurer que les tags sont synchronisés
        if (visibleTagsInput) {
            hiddenTags.value = visibleTagsInput.value;
        }
        
        // Animation de chargement
        createBtn.innerHTML = '<span class="btn-icon">⚡</span> Publication en cours...';
        createBtn.disabled = true;
        form.classList.add('loading');
        
        // Simulation d'un délai de publication
        setTimeout(() => {
            form.classList.remove('loading');
        }, 2000);
    });
    
    // Effets de focus améliorés
    const formInputs = document.querySelectorAll('.form-input, .form-textarea, .form-select');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#00ff88';
            this.style.boxShadow = '0 0 0 2px rgba(0, 255, 136, 0.2)';
        });
        
        input.addEventListener('blur', function() {
            if (this.value.trim() === '' && this.hasAttribute('required')) {
                this.style.borderColor = '#ff3333';
                this.style.boxShadow = '0 0 0 2px rgba(255, 51, 51, 0.2)';
            } else {
                this.style.borderColor = '#3a3a3a';
                this.style.boxShadow = 'none';
            }
        });
    });
    
    // Génération de titre suggéré
    const titleExamples = [
        "Les Tendances Technologiques de l'Année",
        "Guide Complet pour Débutants en Programmation",
        "L'Impact de l'IA sur Notre Quotidien",
        "Découverte des Dernières Innovations Scientifiques",
        "Conseils pour Améliorer Votre Productivité"
    ];
    
    titleInput.addEventListener('click', function() {
        if (!this.value.trim()) {
            this.placeholder = titleExamples[Math.floor(Math.random() * titleExamples.length)];
        }
    });
    
    // Animation d'entrée
    const animatedElements = document.querySelectorAll('.create-form-container, .creation-guide');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    
    setTimeout(() => {
        animatedElements.forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    }, 100);
    
    // Focus automatique sur le titre
    titleInput.focus();
});
</script>

</body>
</html>