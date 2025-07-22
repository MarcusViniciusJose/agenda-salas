<?php
require_once __DIR__ . '/../config/database.php';

class Notification {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create($userId, $message, $link, $eventId = null) {
        if (empty($userId) || empty($message)) {
            return false;
        }

        $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, message, link, is_read, event_id) 
            VALUES (?, ?, ?, 0, ?)
        ");
        return $stmt->execute([$userId, $message, $link, $eventId]);
    }

    public function getUnreadByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM notifications 
            WHERE user_id = ? AND is_read = 0 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByUser($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id) {
        $stmt = $this->conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    public function countByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT 
                SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS unread, 
                COUNT(*) AS total
            FROM notifications 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteByEventId($eventId){
        $stmt = $this->conn->prepare("DELETE FROM notifications WHERE event_id = ?");
        return $stmt->execute([$eventId]);
    }

}
