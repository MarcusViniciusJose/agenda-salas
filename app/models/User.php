<?php

require_once __DIR__ . '/../config/database.php';

    class User{
        private $conn;

        public function __construct(){
            $db = new Database();
            $this->conn = $db->connect();
            
        }

        public function login($email, $password) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']); 
                return $user;
            }
        
            return false;
        }

        public function getAll(){
            $stmt = $this->conn->prepare('SELECT * FROM users WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function searchByNameOrEmail($term){
            $stmt = $this->conn->prepare("SELECT id, nome, email FROM users WHERE nome LIKE :term OR email LIKE :term LIMIT 10");
            $like = '%' . $term . '%';
            $stmt->bindParam(':term', $like);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        }

        public function getById($id) {
            $stmt = $this->conn->prepare("SELECT id, nome, email FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
