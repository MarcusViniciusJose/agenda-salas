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
    }
