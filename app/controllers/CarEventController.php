<?php

require_once __DIR__ . '/../models/CarEvent.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Notification.php';

class CarEventController {

    public function index() {
        require '../app/views/cars/index.php';
    }

    public function all() {
        $carEvent = new CarEvent();
        $events = $carEvent->getAll();

        $formatted = array_map(function ($e) {
            return [
                'id' => $e['id'],
                'title' => $e['title'],
                'start' => $e['start'],
                'end' => $e['end'],
                'description' => $e['description']
            ];
        }, $events);

        echo json_encode($formatted);
    }

    public function getByIdAjax() {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo json_encode(['error' => 'ID não informado']);
            return;
        }

        $carEvent = new CarEvent();
        $event = $carEvent->getById($_GET['id']);

        if (!$event) {
            echo json_encode(['error' => 'Evento não encontrado']);
            return;
        }

        echo json_encode(['event' => $event]);
    }

    public function store() {
        $carEvent = new CarEvent();
        $userModel = new User();

        $title = $_POST['title'] ?? '';
        $start = $_POST['start'] ?? '';
        $end = $_POST['end'] ?? '';
        $description = $_POST['description'] ?? '';
        $created_by = $_SESSION['user']['id'] ?? null;

        $titleSafe = htmlspecialchars($title);
        $startSafe = htmlspecialchars(date('d/m/Y H:i', strtotime($start)));

        if (!$created_by) {
            echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
            return;
        }

        if (!$title || !$start || !$end) {
            echo json_encode(['success' => false, 'error' => 'Preencha todos os campos obrigatórios.']);
            return;
        }

        $eventId = $_POST['id'] ?? null;

        if (!$eventId && $carEvent->hasConflict($start, $end)) {
            echo json_encode(['success' => false, 'error' => 'Conflito de agendamento! Já existe uma reserva nesse horário.']);
            return;
        }

        $data = [
            'title' => $title,
            'description' => $description,
            'start' => $start,
            'end' => $end,
            'user_id' => $created_by
        ];

        if ($eventId) {
            $carEvent->update($eventId, $data);
        } else {
            $eventId = $carEvent->create($data);
        }

        echo json_encode([
            'success' => true,
            'event' => [
                'id' => $eventId,
                'title' => $title,
                'start' => $start,
                'end' => $end
            ]
        ]);
    }

    public function show() {
        $eventId = $_GET['id'] ?? null;

        if (!$eventId) {
            echo "Evento não encontrado.";
            return;
        }

        $eventModel = new CarEvent();
        $userModel = new User();

        $eventData = $eventModel->getById($eventId);

        if (!$eventData) {
            echo "Evento não encontrado no banco de dados.";
            return;
        }

        require '../app/views/cars/show.php';
    }

    public function delete() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $eventId = $data['id'] ?? null;

        if (!$eventId) {
            echo json_encode(['success' => false, 'error' => 'ID do evento não fornecido.']);
            return;
        }

        $carEvent = new CarEvent();
        $deleted = $carEvent->delete($eventId);

        if ($deleted) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao excluir evento.']);
        }
    }
}
