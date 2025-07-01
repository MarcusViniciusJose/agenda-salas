<?php
require_once __DIR__ . '/../models/Notification.php';

class NotificationController{
    public function get(){
        session_start();
        $user_id = $_SESSION['user']['id'] ?? null;

        if(!$user_id){
            echo json_encode([]);
            return;
        }

        $model = new Notification();
        $notifications = $model->getUnreadByUser($user_id);
        echo json_encode($notifications);
    }

    public function read(){
        $id = $_POST['id'] ?? null;
        if($id){
            $model  = new Notification();
            $model->markAsRead($id);
        }
    }
}