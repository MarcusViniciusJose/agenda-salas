<?php

require_once __DIR__ . '/../config/database.php';

class Event {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM events");
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($events as &$event) {
            switch ($event['sala']) {
                case 'reuniao':
                    $event['color'] = '#007bff'; // azul
                    break;
                case 'treinamento':
                    $event['color'] = '#28a745'; // verde
                    break;
                default:
                    $event['color'] = '#6c757d'; // cinza
                    break;
            }
        }
    
        return $events;
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
        $sql = "INSERT INTO events_participants (event_id, user_id) VALUES (:event_id, :user_id)";
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
        $stmt = $this->conn->prepare(" SELECT u.id, u.name, u.email 
            FROM users u 
            INNER JOIN events_participants ep ON u.id = ep.user_id 
            WHERE ep.event_id = :event_id
        ");
        $stmt->execute([':event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function update($id, $title, $start, $end, $sala) {
        $stmt = $this->conn->prepare("UPDATE events 
            SET title = :title, start = :start, end = :end, sala = :sala 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':title' => $title,
            ':start' => $start,
            ':end' => $end,
            ':sala' => $sala,
            ':id' => $id
        ]);
    }

    public function removeParticipants($eventId){
        $stmt = $this->conn->prepare("DELETE FROM events_participants WHERE event_id = :event_id"); 
        return $stmt->execute([':event_id' => $eventId]);
    }

    public function delete($id) {
        // Remove os participantes do evento primeiro
        $stmtParticipants = $this->conn->prepare("DELETE FROM events_participants WHERE event_id = ?");
        $stmtParticipants->execute([$id]);

        // Agora pode excluir o evento
        $stmt = $this->conn->prepare("DELETE FROM events WHERE id = ?");
        return $stmt->execute([$id]);

        
       
    }

    public function hasConflict($start, $end, $sala){
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total 
            FROM events 
            WHERE sala = :sala 
              AND (start < :end AND end > :start)
        ");
        $stmt->execute([
            ':sala' => $sala,
            ':start' => $start,
            ':end' => $end
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    public function isOwner($eventId, $userId) {
        $stmt = $this->conn->prepare("SELECT created_by FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result && $result['created_by'] == $userId;
    }
    
}