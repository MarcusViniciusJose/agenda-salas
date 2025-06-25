<?php
require_once __DIR__ . '/../models/User.php';

class UserController{

    public function search(){
        if(!asset($_GET['term'])){
            echo json_encode([]);
            return;
        }
        $term = $_GET['term'];
        $userModel = new User();
        $results = $userModel->searchByNameOrEmail($term);

        $formatted = array_map(function($user)){
            return[
                'id' => $user['id'];
                'text' => $user['nome'] . ' (' . $user['email'] . ')'
            ];
        }, $results);

        echo json_encode($formatted);
    }
}