-- =============================================
-- Ajout des champs de localisation à la table des événements
-- Exécutez ce fichier pour ajouter le support des cartes
-- =============================================

-- Ajout des champs de localisation
ALTER TABLE evenements 
ADD COLUMN lieu VARCHAR(255) DEFAULT NULL COMMENT 'Nom du lieu',
ADD COLUMN adresse VARCHAR(500) DEFAULT NULL COMMENT 'Adresse complète',
ADD COLUMN latitude DECIMAL(10, 8) DEFAULT NULL COMMENT 'Latitude',
ADD COLUMN longitude DECIMAL(11, 8) DEFAULT NULL COMMENT 'Longitude';

-- Mise à jour des données existantes avec des emplacements par défaut (Tunis comme exemple)
UPDATE evenements SET 
    lieu = 'Centre de conférences',
    adresse = 'Avenue Habib Bourguiba, Tunis',
    latitude = 36.8065,
    longitude = 10.1815
WHERE id = 1;

UPDATE evenements SET 
    lieu = 'Centre d’innovation technologique',
    adresse = 'Pôle technologique, Ariana',
    latitude = 36.8625,
    longitude = 10.1956
WHERE id = 2;

UPDATE evenements SET 
    lieu = 'Université de Tunis',
    adresse = 'Avenue de la République, Tunis',
    latitude = 36.7992,
    longitude = 10.1800
WHERE id = 3;

-- Ajout d’un index pour la recherche géographique
CREATE INDEX idx_location ON evenements(latitude, longitude);
