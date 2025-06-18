<?php
    class Database{
        private $host = "localhost";
        private $db_name = "agendamento";
        private $username = "root";
        private $password = "";
        private $conn;

        public function connect(){
            $this->conn = null;
            try{
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username, $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE);
            } catch(PDOException $e){
                echo "Erro de conexão: " . $e->getMessage();
            }
            return $this->conn;
        }

    }