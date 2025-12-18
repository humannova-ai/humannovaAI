<?php
// Utilisateur model stub: user management removed. Minimal API to avoid fatal errors.
class Utilisateur {
    private $id = 0;
    private $nom = '';
    private $prenom = '';
    private $email = '';
    private $role = 'utilisateur';

    public function __construct($db = null) {}
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }

    public function setNom($v) { $this->nom = $v; }
    public function setPrenom($v) { $this->prenom = $v; }
    public function setEmail($v) { $this->email = $v; }
    public function setMdp($v) { /* noop */ }
    public function setAdresse($v) { /* noop */ }
    public function setTelephone($v) { /* noop */ }
    public function setRole($v) { $this->role = $v; }

    public static function trouverParEmail($db, $email) { return null; }
    public function emailExists($email) { return false; }
    public function updatePassword($email, $newPassword) { return false; }
    public function enregistrer($db) { return false; }
    public function verifierMdp($mdp) { return false; }
}
?>
