<?php

require_once __DIR__ . '/../models/CarEvent.php';

class CarEventController{

    public function index(){
        require '../app/views/cars/index.php';
    }

    public function all(){
        $model = new CarEvent();
        $events = $model->getAll();
        echo json_encode($events);
    }

    public function create(){
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'start' => $_POST['start'],
            'end' => $_POST['end'],
            'user_id' => $_SESSION['user']['id']
        ];

        $model = new CarEvent();
        $model->create($data);
        echo json_encode(['success' => true]);
        
    }

    public function update(){
        $id = $_POST['id'];
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'start' => $_POST['start'],
            'end' => $_POST['end']
        ];

        $model = new CarEvent();
        $model->update($id, $data);
        echo json_encode(['success' => true]);
    }

    public function delete(){
        $id = $_POST['id'];
        $model = new CarEvent();
        $model->delete($id);
        echo json_encode(['success' => true]);
    }

    
}