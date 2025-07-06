<?php
require_once __DIR__ . '/../models/User.php';

class UserController {

    public function search() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Não autorizado']);
            return;
        }

        $search = $_GET['search'] ?? ''; 
        $search = substr(trim($search), 0, 100); 

        if (empty($search)) {
            echo json_encode([]);
            return;
        }

        $userModel = new User();
        $results = $userModel->searchByNameOrEmail($search);

        $formatted = array_map(function($user) {
            return [
                'id' => $user['id'],
                'text' => htmlspecialchars($user['name']) . ' (' . htmlspecialchars($user['email']) . ')'
            ];
        }, $results);

        echo json_encode($formatted);
    }

}
