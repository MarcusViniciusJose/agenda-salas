<?php

require_once __DIR__ . '/../models/CarEvent.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/User.php';

class CarEventController{

    public function index(){
        require '../app/views/cars/index.php';
    }

    public function all(){
        $model = new CarEvent();
        $events = $model->getAll();

        $formatted = array_map(function ($e){
            return[
                'id' => $e['id'],
                'title' => $e['responsavel'] . '-' . $e['destino'],
                'start' => $e['saida'],
                'end' => $e['retorno']
            ];
        }, $events);

        echo json_encode($formatted);
    }

    public function getById(){
        if(!isset($_GET['id']) || empty($_GET['id'])){
            echo json_encode(['error' => 'ID não informado']);
            return;
        }

        $model = new CarEvent();
        $event = $model->getById($_GET['id']);

        if(!$event){
            echo json_encode(['error' => 'Agendamento não encontrado']);
            return;
        }

        echo json_encode($event);
    }

   public function store(){

    if(!isset($_SESSION['user_id'])){
        echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
        return;
    }

    $model = new CarEvent();

    $id = $_POST['id'] ?? null;

    $data = [
        'responsavel' => $_SESSION['user_name'],
            'saida' => $_POST['saida'] ?? '',
            'retorno' => $_POST['retorno'] ?? '',
            'destino' => $_POST['destino'] ?? '',
            'motivo' => $_POST['motivo'] ?? ''
    ];

    if (!$data['responsavel'] || !$data['saida'] || !$data['retorno']) {
        echo json_encode(['success' => false, 'error' => 'Preencha todos os campos obrigatórios.']);
        return;
    }

    // Verifica conflito
    if (!$id && $model->hasConflict($data['saida'], $data['retorno'])) {
        echo json_encode(['success' => false, 'error' => 'Já existe uma reserva nesse horário.']);
        return;
    }

    if ($id) {
        $model->update($id, $data);
    } else {
        $model->create($data);
    }

    echo json_encode(['success' => true]);
}

public function delete() {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'ID não fornecido.']);
        return;
    }

    $model = new CarEvent();
    $model->delete($id);
    echo json_encode(['success' => true]);
}
    
}