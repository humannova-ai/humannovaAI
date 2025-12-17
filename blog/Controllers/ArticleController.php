<?php
require_once ROOT_PATH . '/shared/Models/Article.php';
require_once ROOT_PATH . '/shared/Models/Interaction.php';

class ArticleController {
    private $model;
    private $blogPath;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new Article();
        // Resolve blog base path so controller works when invoked from admin (`blog_admin`) too
        $this->blogPath = defined('BLOG_PATH') ? BLOG_PATH : dirname(__DIR__);
    }

    // List all articles
    public function index() {
        $articles = $this->model->readAll();

        // Use admin table view if accessed from admin panel, otherwise use feed
        try {
            if (defined('USE_ADMIN_VIEW') && USE_ADMIN_VIEW) {
                include $this->blogPath . '/Views/articles/index.php';
            } else {
                include $this->blogPath . '/Views/articles/feed.php';
            }
        } catch (Throwable $e) {
                error_log('ArticleController::index error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                http_response_code(500);
                echo "<h2>Erreur interne</h2><p>Une erreur est survenue lors du rendu des articles.</p>";
                // In dev mode, display the exception message to aid debugging
                if (ini_get('display_errors')) {
                    echo "<pre style='color:#900;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
                }
        }
    }

    // Show a single article with interactions
    public function show($id) {
        $article = $this->model->readById($id);
        include $this->blogPath . '/Views/articles/show.php';
    }

    // Create a new article
    public function create() {
        // Afficher le formulaire
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            try {
                include $this->blogPath . '/Views/articles/create.php';
            } catch (Throwable $e) {
                    error_log('ArticleController::create (GET) error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                    http_response_code(500);
                    echo "<h2>Erreur interne</h2><p>Impossible d'afficher le formulaire de création.</p>";
                    if (ini_get('display_errors')) {
                        echo "<pre style='color:#900;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
                    }
            }
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
                include $this->blogPath . '/Views/articles/create.php';
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
                include $this->blogPath . '/Views/articles/create.php';
            }
            
        } catch (Exception $e) {
                error_log('ArticleController::create (POST) error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                $_SESSION['error_message'] = "Une erreur est survenue : " . $e->getMessage();
                if (ini_get('display_errors')) {
                    echo "<pre style='color:#900;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
                }
                include $this->blogPath . '/Views/articles/create.php';
        }
    }

    // Store a new article (POST handler alias to create)
    public function store() {
        // Reuse the create() method which already handles POST submission
        return $this->create();
    }

    // Edit an existing article
    public function edit($id) {
        $article = $this->model->readById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre']);
            $contenu = trim($_POST['contenu']);
            $this->model->update($id, $titre, $contenu);
            header("Location: index.php?controller=article&action=index");
            exit;
        }

        include $this->blogPath . '/Views/articles/edit.php';
    }

    // Delete an article
    public function delete($id) {
        $this->model->delete($id);
        header("Location: index.php?controller=article&action=index");
        exit;
    }

    public function update($id) {
        // Ensure the article exists
        $article = $this->model->readById($id);
        if (!$article) {
            $_SESSION['error_message'] = "Article introuvable.";
            header("Location: index.php?controller=article&action=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // If not POST, show edit form
            include $this->blogPath . '/Views/articles/edit.php';
            return;
        }

        $errors = [];
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $statut = trim($_POST['statut'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $tags = trim($_POST['tags'] ?? '');

        if ($titre === '') {
            $errors[] = "Le titre est obligatoire";
        }
        if ($contenu === '') {
            $errors[] = "Le contenu est obligatoire";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            // Keep current article to prefill the form
            $article['titre'] = $titre ?: $article['titre'];
            $article['contenu'] = $contenu ?: $article['contenu'];
            $article['excerpt'] = $excerpt ?: ($article['excerpt'] ?? '');
            $article['image'] = $image ?: ($article['image'] ?? '');
            $article['tags'] = $tags ?: ($article['tags'] ?? '');
            include $this->blogPath . '/Views/articles/edit.php';
            return;
        }

        // If excerpt empty, regenerate a short excerpt from content
        if ($excerpt === '') {
            $excerpt = substr(strip_tags($contenu), 0, 150);
            if (strlen($contenu) > 150) {
                $excerpt .= '...';
            }
        }

        // Capture old status to detect transition to 'publie'
        $oldStatus = $article['statut'] ?? null;
        $newStatus = $statut !== '' ? $statut : null;

        $success = $this->model->update($id, $titre, $contenu, $excerpt, $image, $tags, $newStatus);

        if ($success) {
            $_SESSION['success_message'] = "Article mis à jour avec succès !";
            // If article was just published, send notifications
            if ($oldStatus !== 'publie' && $newStatus === 'publie') {
                require_once ROOT_PATH . '/shared/Utils/Mailer.php';
                // Notify admin(s) — determine recipient and use Mailer if available
                $to = defined('ADMIN_EMAIL') ? constant('ADMIN_EMAIL') : ($_SESSION['user_email'] ?? 'admin@example.com');
                $payload = array_merge($article, ['id' => $id]);
                $subject = "Nouvel article publié: " . ($payload['titre'] ?? 'Nouvel article');
                $messageBody = $payload['excerpt'] ?? substr(strip_tags($payload['contenu'] ?? ''), 0, 150);

                if (class_exists('Mailer')) {
                    // Prefer a notify helper if provided by Mailer, otherwise try a generic send method
                    if (is_callable(['Mailer', 'notifyPostPublished'])) {
                        call_user_func(['Mailer', 'notifyPostPublished'], $payload);
                    } elseif (is_callable(['Mailer', 'send'])) {
                        call_user_func(['Mailer', 'send'], $to, $subject, $messageBody);
                    } else {
                        @mail($to, $subject, $messageBody);
                    }
                } else {
                    @mail($to, $subject, $messageBody);
                }
            }
            header("Location: index.php?controller=article&action=index");
            exit;
        } else {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour de l'article";
            include $this->blogPath . '/Views/articles/edit.php';
        }
    }
}
