<?php
require_once __DIR__ . '/../models/Notification.php';

class NotificationController {

    public function get() {
        $user_id = $_SESSION['user']['id'] ?? null;

        if (!$user_id) {
            echo json_encode([]);
            return;
        }

        $model = new Notification();
        $data = $model->getUnreadByUser($user_id);
        echo json_encode($data);
    }

    public function read() {
        $id = $_POST['id'] ?? null;

        if ($id) {
            $model = new Notification();
            $model->markAsRead($id);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'ID não informado']);
        }
    }

    public function markAndRedirect() {
        $id = $_GET['id'] ?? null;
        $link = $_GET['link'] ?? '/event/index';

        if ($id) {
            $model = new Notification();
            $model->markAsRead($id);
        }

        if (!str_starts_with($link, '/')) {
            $link = '/event/index';
        }

        header("Location: $link");
        exit;
    }

    public function count() {
        session_start();
        $user_id = $_SESSION['user']['id'] ?? null;

        if (!$user_id) {
            echo json_encode(['unread' => 0, 'total' => 0]);
            return;
        }

        $model = new Notification();
        $data = $model->countByUser($user_id);
        echo json_encode($data);
    }
}
