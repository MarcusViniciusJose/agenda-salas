<?php

require_once 'app/config/database.php';


Class Event{
private $conn;

    public function _construct(){
        $db = new DataBase();
        $this->conn = $db->connect();
        
    }

    public function getAll(){
        $query = "SELECT * FROM events";
        $stmt = $this->conn->prepare(query);
        stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function create($title, $start, $end, $sala, $created_by){
        $query = "INSERT INTO events (title, start, end, sala, created_by)
                  VALUES (:title, :start, :end, :sala, :created_by)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':title' => $title,
            ':start' => $start,
            ':end' => $end,
            ':sala' => $sala,
            ':created_by' => $created_by]);
    }
}