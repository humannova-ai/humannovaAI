<?php
// Base Model class - can be extended by other models if needed
class Model {
    protected $conn;
    
    public function __construct() {
        $db = new Connection();
        $this->conn = $db->connect();
    }
    
    public function getConnection() {
        return $this->conn;
    }
}
