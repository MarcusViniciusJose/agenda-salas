<?php

require_once __DIR__ . '/../config/database.php';

class CarEvent{
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll(){
        $stmt = $this->conn->query("SELECT * FROM car_events");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByUser($userId){
        $stmt = $this->conn->prepare("SELECT * FROM car_events WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data){
        $stmt = $this->conn->prepare("INSERT INTO car_events (title, description, start, end, user_id) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([ $data['title'], $data['description'], $data['start'], $data['end'], $data['user_id']]);
    }

    public function update($id, $data){
        $stmt = $this->conn->prepare("UPDATE car_events SET title = ?, description = ?, start = ?, end = ?, WHERE id = ?");
        return $stmt->execute([$data['title'], $data['description'], $data['start'], $data['end'], $id]);
    }

    public function delete($id){
        $stmt = $this->conn->prepare("DELETE FROM car_events WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function hasConflict($data){
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total 
            FROM car_events 
            WHERE (start < :end AND end > :start)
        ");
        $stmt->execute([$data['start'], $data['end']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    public function getById($id){
        $stmt = $this->conn->prepare("SELECT * FROM car_events WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}