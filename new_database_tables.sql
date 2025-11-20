-- Utiliser la base de données
USE events_management;

-- Supprimer les tables existantes si nécessaire (dans l'ordre inverse des dépendances)
DROP TABLE IF EXISTS reponses_utilisateur;
DROP TABLE IF EXISTS fichiers;
DROP TABLE IF EXISTS participations;
DROP TABLE IF EXISTS resultats_quiz;
DROP TABLE IF EXISTS utilisateurs;

-- Table des utilisateurs (pour suivre les participants)
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des résultats de quiz
CREATE TABLE IF NOT EXISTS resultats_quiz (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    evenement_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    pourcentage DECIMAL(5,2) NOT NULL,
    temps_completion INT, -- en secondes
    date_passage TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_evenement (evenement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des réponses utilisateur pour quiz
CREATE TABLE IF NOT EXISTS reponses_utilisateur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resultat_id INT NOT NULL,
    question_id INT NOT NULL,
    reponse_id INT NOT NULL,
    est_correcte BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resultat_id) REFERENCES resultats_quiz(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (reponse_id) REFERENCES reponses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des participations aux événements normaux
CREATE TABLE IF NOT EXISTS participations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    evenement_id INT NOT NULL,
    commentaire TEXT,
    fichier_url VARCHAR(500),
    statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente',
    date_participation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
    INDEX idx_utilisateur_evenement (utilisateur_id, evenement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table pour stocker les fichiers uploadés
CREATE TABLE IF NOT EXISTS fichiers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    participation_id INT NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    type_fichier VARCHAR(100) NOT NULL,
    taille_fichier INT NOT NULL, -- en bytes
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (participation_id) REFERENCES participations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insérer quelques utilisateurs de test
INSERT INTO utilisateurs (nom, prenom, email) VALUES
('Utilisateur', 'Test', 'test@example.com'),
('Dupont', 'Jean', 'jean.dupont@example.com'),
('Martin', 'Sophie', 'sophie.martin@example.com');

-- Index supplémentaires pour améliorer les performances
CREATE INDEX idx_email ON utilisateurs(email);
CREATE INDEX idx_date_passage ON resultats_quiz(date_passage);
CREATE INDEX idx_participation_date ON participations(date_participation);

-- Vue pour les statistiques des quiz
CREATE OR REPLACE VIEW stats_quiz AS
SELECT 
    e.id as evenement_id,
    e.titre as evenement_titre,
    COUNT(DISTINCT rq.utilisateur_id) as nombre_participants,
    AVG(rq.pourcentage) as moyenne_score,
    MAX(rq.pourcentage) as meilleur_score,
    MIN(rq.pourcentage) as moins_bon_score
FROM evenements e
LEFT JOIN resultats_quiz rq ON e.id = rq.evenement_id
WHERE e.type = 'quiz'
GROUP BY e.id, e.titre;

-- Vue pour les participations aux événements
CREATE OR REPLACE VIEW stats_participations AS
SELECT 
    e.id as evenement_id,
    e.titre as evenement_titre,
    COUNT(p.id) as nombre_participations,
    COUNT(CASE WHEN p.statut = 'accepte' THEN 1 END) as acceptees,
    COUNT(CASE WHEN p.statut = 'en_attente' THEN 1 END) as en_attente,
    COUNT(CASE WHEN p.statut = 'refuse' THEN 1 END) as refusees
FROM evenements e
LEFT JOIN participations p ON e.id = p.evenement_id
WHERE e.type = 'normal'
GROUP BY e.id, e.titre;