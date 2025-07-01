<?php

require_once __DIR__ . '/../config/database.php';

class Notification{
    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create(){
        $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, message, link, is_read) VALUES (?, ?, ?, 0)");
        return $stmt->execute([$id_user, $message, $link]);
    }
    
    public function getUnreadByUser($user_id){
        $stmt = $this->conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY create_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id){
        $stmt = $this->conn->prepare("UPDATE notification SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
        
    }
}