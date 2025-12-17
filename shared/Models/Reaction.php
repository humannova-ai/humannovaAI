<?php
require_once ROOT_PATH . '/shared/Core/Connection.php';

class Reaction {
    private $conn;
    private $emojis = ['👍', '❤️', '😮', '😄', '🔥', '👏'];

    public function __construct() {
        $db = new Connection();
        $this->conn = $db->connect();
    }

    public function add($article_id, $user_id, $emoji) {
        if (!in_array($emoji, $this->emojis)) {
            return false;
        }

        try {
            $stmt = $this->conn->prepare("
                INSERT INTO reactions (article_id, user_id, emoji, created_at) 
                VALUES (:article_id, :user_id, :emoji, NOW())
                ON DUPLICATE KEY UPDATE emoji = :emoji, created_at = NOW()
            ");
            
            return $stmt->execute([
                'article_id' => $article_id,
                'user_id' => $user_id,
                'emoji' => $emoji
            ]);
        } catch (PDOException $e) {
            error_log("Reaction add error: " . $e->getMessage());
            return false;
        }
    }

    public function getByArticle($article_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT emoji, COUNT(*) as count 
                FROM reactions 
                WHERE article_id = :article_id 
                GROUP BY emoji 
                ORDER BY count DESC
            ");
            $stmt->execute(['article_id' => $article_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Reaction get error: " . $e->getMessage());
            return [];
        }
    }

    public function getUserReaction($article_id, $user_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT emoji FROM reactions 
                WHERE article_id = :article_id AND user_id = :user_id
                LIMIT 1
            ");
            $stmt->execute(['article_id' => $article_id, 'user_id' => $user_id]);
            $result = $stmt->fetch(PDO::FETCH_COLUMN);
            return $result ? $result : null;
        } catch (PDOException $e) {
            error_log("Get user reaction error: " . $e->getMessage());
            return null;
        }
    }

    public function getAvailableEmojis() {
        return $this->emojis;
    }

    public function remove($article_id, $user_id) {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM reactions 
                WHERE article_id = :article_id AND user_id = :user_id
            ");
            return $stmt->execute(['article_id' => $article_id, 'user_id' => $user_id]);
        } catch (PDOException $e) {
            error_log("Reaction remove error: " . $e->getMessage());
            return false;
        }
    }

    public function getStats($article_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    COUNT(*) as total_reactions,
                    GROUP_CONCAT(DISTINCT emoji) as emojis_used
                FROM reactions 
                WHERE article_id = :article_id
            ");
            $stmt->execute(['article_id' => $article_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get stats error: " . $e->getMessage());
            return ['total_reactions' => 0, 'emojis_used' => ''];
        }
    }

    public function readAll() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM reactions ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur readAll: " . $e->getMessage());
            return [];
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM reactions WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Reaction delete error: " . $e->getMessage());
            return false;
        }
    }
}
?>