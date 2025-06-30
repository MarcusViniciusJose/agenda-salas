<?php
require_once __DIR__ . '/../models/User.php';

class UserController{

    public function search() {
        $search = $_GET['search'] ?? ''; 
    
        if (empty($search)) {
            echo json_encode([]);
            return;
        }
    
        $userModel = new User();
        $results = $userModel->searchByNameOrEmail($search);
    
        $formatted = array_map(function($user) {
            return [
                'id' => $user['id'],
                'text' => $user['nome'] . ' (' . $user['email'] . ')'
            ];
        }, $results);
    
        echo json_encode($formatted);
    }
    
}