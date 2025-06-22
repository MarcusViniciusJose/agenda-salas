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
        return $stmt->execute([
            ':title' => $title,
            ':start' => $start,
            ':end' => $end,
            ':sala' => $sala,
            ':created_by' => $created_by]);
    }

    public function addParticipants($event_id, $participants){
        $stmt = $this->conn->prepare("INSERT INTO events_participants (event_id, user_id) VALUES (?, ?)");
        foreach($participants as $user_id){
            $stmt->execute([$event_id, $user_id]);
        }
    }
}