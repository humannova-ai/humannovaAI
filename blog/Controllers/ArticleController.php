<?php
require_once "Models/Article.php";
require_once "Models/Interaction.php";

class ArticleController {
    private $model;

    public function __construct() {
        // DÉMARRER LA SESSION DANS LE CONSTRUCTEUR
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new Article();
    }

    // List all articles
    public function index() {
        $articles = $this->model->readAll();
        include "Views/articles/index.php";
    }

    // Show a single article with interactions
    public function show($id) {
        $article = $this->model->readById($id);
        include "Views/articles/show.php";
    }

    // Create a new article - VERSION CORRIGÉE
public function create() {
    // Afficher le formulaire
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        include "Views/articles/create.php";
        return;
    }
    
    // Traiter la soumission du formulaire (POST)
    try {
        // Valider les données
        $errors = [];
        
        if (empty(trim($_POST['titre']))) {
            $errors[] = "Le titre est obligatoire";
        }
        
        if (empty(trim($_POST['contenu']))) {
            $errors[] = "Le contenu est obligatoire";
        }
        
        // Si il y a des erreurs, réafficher le formulaire
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            include "Views/articles/create.php";
            return;
        }
        
        // Récupérer les données
        $titre = trim($_POST['titre']);
        $contenu = trim($_POST['contenu']);
        $excerpt = trim($_POST['excerpt'] ?? '');
        $tags = trim($_POST['tags'] ?? '');
        $categorie = trim($_POST['categorie'] ?? '');
        
        // Si excerpt est vide, le générer automatiquement
        if (empty($excerpt)) {
            $excerpt = substr(strip_tags($contenu), 0, 150);
            if (strlen($contenu) > 150) {
                $excerpt .= '...';
            }
        }
        
        // Ajouter la catégorie aux tags si elle existe
        if (!empty($categorie)) {
            if (!empty($tags)) {
                $tags = $categorie . ', ' . $tags;
            } else {
                $tags = $categorie;
            }
        }
        
        // Appeler la méthode create du modèle
        $success = $this->model->create($titre, $contenu, $excerpt, '', $tags);
        
        if ($success) {
            $_SESSION['success_message'] = "Article créé avec succès !";
            header("Location: index.php?controller=article&action=index");
            exit;
        } else {
            $_SESSION['error_message'] = "Erreur lors de la création de l'article";
            include "Views/articles/create.php";
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Une erreur est survenue : " . $e->getMessage();
        include "Views/articles/create.php";
    }
}
    // Edit an existing article
    public function edit($id) {
        $article = $this->model->readById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->setId($id);
            $this->model->setTitre($_POST['titre']);
            $this->model->setContenu($_POST['contenu']);
            $this->model->update();
            header("Location: index.php?controller=article&action=index");
            exit;
        }

        include "Views/articles/edit.php";
    }

    // Delete an article
    public function delete($id) {
        $this->model->delete($id);
        header("Location: index.php?controller=article&action=index");
        exit;
    }
}
?>