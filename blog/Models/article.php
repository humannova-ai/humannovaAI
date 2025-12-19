<?php
// Models/article.php
require_once __DIR__ . "/../Core/Connection.php";

class Article {
    private $conn;
    private $table = 'articles';

    public function __construct() {
        $db = new Connection();
        $this->conn = $db->connect();
    }

    // Méthode pour obtenir la connexion PDO
    public function getConnection() {
        return $this->conn;
    }

    // Méthode pour lire tous les articles (corrige l'erreur)
    public function readAll() {
        try {
            $stmt = $this->conn->prepare("
                SELECT *, 
                LENGTH(contenu) / 1500 as reading_time 
                FROM {$this->table} 
                ORDER BY date_creation DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur readAll: " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour lire un article par son ID
    public function readById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT *, 
                LENGTH(contenu) / 1500 as reading_time 
                FROM {$this->table} 
                WHERE id = :id
            ");
            $stmt->execute(['id' => $id]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($article) {
                $article['reading_time'] = ceil($article['reading_time']);
                if ($article['reading_time'] < 1) $article['reading_time'] = 1;
            }
            
            return $article;
        } catch (PDOException $e) {
            error_log("Erreur readById: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour créer un nouvel article
    public function create($titre, $contenu, $excerpt = '', $image = '', $tags = '') {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO {$this->table} (titre, contenu, excerpt, image, tags, date_creation) 
                VALUES (:titre, :contenu, :excerpt, :image, :tags, NOW())
            ");
            
            $reading_time = ceil(strlen($contenu) / 1500);
            if ($reading_time < 1) $reading_time = 1;
            
            return $stmt->execute([
                'titre' => $titre,
                'contenu' => $contenu,
                'excerpt' => $excerpt,
                'image' => $image,
                'tags' => $tags
            ]);
        } catch (PDOException $e) {
            error_log("Erreur create: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour mettre à jour un article
    public function update($id, $titre, $contenu, $excerpt = '', $image = '', $tags = '') {
        try {
            $stmt = $this->conn->prepare("
                UPDATE {$this->table} 
                SET titre = :titre, 
                    contenu = :contenu, 
                    excerpt = :excerpt, 
                    image = :image, 
                    tags = :tags,
                    date_modification = NOW()
                WHERE id = :id
            ");
            
            return $stmt->execute([
                'id' => $id,
                'titre' => $titre,
                'contenu' => $contenu,
                'excerpt' => $excerpt,
                'image' => $image,
                'tags' => $tags
            ]);
        } catch (PDOException $e) {
            error_log("Erreur update: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour supprimer un article
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur delete: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour incrémenter le compteur de vues
    public function incrementViews($id) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE {$this->table} 
                SET views = COALESCE(views, 0) + 1 
                WHERE id = :id
            ");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur incrementViews: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour rechercher des articles
    public function search($query) {
        try {
            $stmt = $this->conn->prepare("
                SELECT *, 
                LENGTH(contenu) / 1500 as reading_time 
                FROM {$this->table} 
                WHERE titre LIKE :query 
                OR contenu LIKE :query 
                OR tags LIKE :query 
                ORDER BY date_creation DESC
            ");
            $stmt->execute(['query' => "%$query%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur search: " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour obtenir les articles les plus populaires
    public function getPopular($limit = 5) {
        try {
            $stmt = $this->conn->prepare("
                SELECT *, 
                LENGTH(contenu) / 1500 as reading_time 
                FROM {$this->table} 
                ORDER BY COALESCE(views, 0) DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getPopular: " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour obtenir les articles récents
    public function getRecent($limit = 5) {
        try {
            $stmt = $this->conn->prepare("
                SELECT *, 
                LENGTH(contenu) / 1500 as reading_time 
                FROM {$this->table} 
                ORDER BY date_creation DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getRecent: " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour obtenir le nombre total d'articles
    public function getTotalCount() {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erreur getTotalCount: " . $e->getMessage());
            return 0;
        }
    }

    // Méthode pour obtenir les articles par tag
    public function getByTag($tag, $limit = 10) {
        try {
            $stmt = $this->conn->prepare("
                SELECT *, 
                LENGTH(contenu) / 1500 as reading_time 
                FROM {$this->table} 
                WHERE tags LIKE :tag 
                ORDER BY date_creation DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':tag', "%$tag%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getByTag: " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour obtenir les statistiques des articles
    public function getStats() {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    COUNT(*) as total_articles,
                    SUM(COALESCE(views, 0)) as total_views,
                    AVG(LENGTH(contenu)) as avg_length,
                    MIN(date_creation) as first_article_date,
                    MAX(date_creation) as last_article_date
                FROM {$this->table}
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getStats: " . $e->getMessage());
            return [
                'total_articles' => 0,
                'total_views' => 0,
                'avg_length' => 0,
                'first_article_date' => null,
                'last_article_date' => null
            ];
        }
    }

    // Méthode pour vérifier si un article existe
    public function exists($id) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result['count'] ?? 0) > 0;
        } catch (PDOException $e) {
            error_log("Erreur exists: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour obtenir les tags uniques
    public function getUniqueTags() {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT TRIM(tags) as tag 
                FROM {$this->table} 
                WHERE tags IS NOT NULL AND tags != ''
            ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $tags = [];
            foreach ($results as $row) {
                if (!empty($row['tag'])) {
                    $tagArray = explode(',', $row['tag']);
                    foreach ($tagArray as $tag) {
                        $tag = trim($tag);
                        if (!empty($tag) && !in_array($tag, $tags)) {
                            $tags[] = $tag;
                        }
                    }
                }
            }
            
            return $tags;
        } catch (PDOException $e) {
            error_log("Erreur getUniqueTags: " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour générer un slug à partir du titre
    public function generateSlug($titre) {
        $slug = strtolower($titre);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }

    // Méthode pour obtenir l'article précédent
    public function getPreviousArticle($currentId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, titre 
                FROM {$this->table} 
                WHERE id < :id 
                ORDER BY id DESC 
                LIMIT 1
            ");
            $stmt->execute(['id' => $currentId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getPreviousArticle: " . $e->getMessage());
            return null;
        }
    }

    // Méthode pour obtenir l'article suivant
    public function getNextArticle($currentId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, titre 
                FROM {$this->table} 
                WHERE id > :id 
                ORDER BY id ASC 
                LIMIT 1
            ");
            $stmt->execute(['id' => $currentId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getNextArticle: " . $e->getMessage());
            return null;
        }
    }

    // Méthode pour mettre à jour les statistiques de lecture
    public function updateReadingStats($id, $reading_time) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE {$this->table} 
                SET reading_time = :reading_time 
                WHERE id = :id
            ");
            return $stmt->execute([
                'id' => $id,
                'reading_time' => $reading_time
            ]);
        } catch (PDOException $e) {
            error_log("Erreur updateReadingStats: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour archiver un article
    public function archive($id) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE {$this->table} 
                SET archived = 1, 
                    archived_at = NOW() 
                WHERE id = :id
            ");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur archive: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour restaurer un article archivé
    public function restore($id) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE {$this->table} 
                SET archived = 0, 
                    archived_at = NULL 
                WHERE id = :id
            ");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur restore: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour obtenir les articles archivés
    public function getArchived() {
        try {
            $stmt = $this->conn->prepare("
                SELECT * 
                FROM {$this->table} 
                WHERE archived = 1 
                ORDER BY archived_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getArchived: " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour nettoyer le cache (si vous avez un système de cache)
    public function clearCache() {
        // Implémentez votre logique de cache ici si nécessaire
        return true;
    }

    // Méthode pour valider les données d'un article
    public function validate($data) {
        $errors = [];
        
        if (empty($data['titre'])) {
            $errors[] = 'Le titre est requis';
        } elseif (strlen($data['titre']) < 3) {
            $errors[] = 'Le titre doit contenir au moins 3 caractères';
        }
        
        if (empty($data['contenu'])) {
            $errors[] = 'Le contenu est requis';
        } elseif (strlen($data['contenu']) < 10) {
            $errors[] = 'Le contenu doit contenir au moins 10 caractères';
        }
        
        return $errors;
    }

    // Méthode pour formater les données d'un article
    public function format($article) {
        if (!$article) {
            return null;
        }
        
        // Calcul du temps de lecture
        $word_count = str_word_count(strip_tags($article['contenu']));
        $article['reading_time'] = ceil($word_count / 200);
        if ($article['reading_time'] < 1) $article['reading_time'] = 1;
        
        // Formatage de la date
        if (isset($article['date_creation'])) {
            $article['formatted_date'] = date('d/m/Y H:i', strtotime($article['date_creation']));
        }
        
        // Extraction d'un extrait si non fourni
        if (empty($article['excerpt']) && !empty($article['contenu'])) {
            $article['excerpt'] = substr(strip_tags($article['contenu']), 0, 150) . '...';
        }
        
        return $article;
    }
}
?>