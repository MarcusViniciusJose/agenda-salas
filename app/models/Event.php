<?php

require_once __DIR__ . '/../config/Database.php';

class Event {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->connect();
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM events");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($title, $start, $end, $sala, $created_by){
        $query = "INSERT INTO events (title, start, end, sala, created_by)
                  VALUES (:title, :start, :end, :sala, :created_by)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':title' => $title,
            ':start' => $start,
            ':end' => $end,
            ':sala' => $sala,
            ':created_by' => $created_by
        ]);
        return $this->conn->lastInsertId(); 
    }

    public function addParticipants($eventId, $userId) {
        $sql = "INSERT INTO event_participants (event_id, user_id) VALUES (:event_id, :user_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    public function getById($id){
        $stmt = $this->conn->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getParticipants($eventId){
        $stmt = $this->prepare("SELECT u.nome, u.email FROM event_participants ep JOIN users u ON ep.user_id = u.id WHERE ep.event_id = ?");

        $stmt->execute([$eventId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}