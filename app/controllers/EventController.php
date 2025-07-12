<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Notification.php';

class EventController {
    public function index() {
        require '../app/views/dashboard.php';
    }

    public function all() {
        $event = new Event();
        $events = $event->getAll();
        echo json_encode($events);
    }

    public function store() {

        $event = new Event();
        $userModel = new User();
        $notificationModel = new Notification();

        $title = $_POST['title'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $sala = $_POST['sala'];
        $created_by = $_SESSION['user']['id'] ?? null;
        $participants = $_POST['participants'] ?? [];

        $titleSafe = htmlspecialchars($title);
        $startSafe = htmlspecialchars(date('d/m/Y H:i', strtotime($start)));

        if (!$created_by) {
            echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
            return;
        }

        $eventId = $_POST['event_id'] ?? null;
        if (!$eventId && $event->hasConflict($start, $end, $sala)) {
            echo json_encode(['success' => false, 'error' => 'Conflito de agendamento! Já existe um evento nessa sala nesse horário.']);
            return;
        }

        if ($eventId) {
            $event->update($eventId, $title, $start, $end, $sala);
            $event->removeParticipants($eventId);
        } else {
            $eventId = $event->create($title, $start, $end, $sala, $created_by);
        }

        if ($eventId && count($participants) > 0) {
            foreach ($participants as $userId) {
                $event->addParticipants($eventId, $userId);

                $notificationModel->create(
                    $userId,
                    "Você foi convidado para o evento <strong>$titleSafe</strong> no dia <strong>$startSafe</strong>.",
                    "/public/index.php?url=event/show&id=3"
                );
            }
        }

        header('Content-Type: application/json; charset=utf-8');
        http_response_code(200);

        echo json_encode([
            'success' => true,
            'event' => [
                'id' => $eventId,
                'title' => $title,
                'start' => $start,
                'end' => $end,
                'sala' => $sala
            ]
        ]);

        exit;
        
    }

    public function show() {
        $eventId = $_GET['id'] ?? null;
    
        if (!$eventId) {
            echo "Evento não encontrado";
            return;
        }
    
        $eventModel = new Event();
        $userModel = new User();
    
        $eventData = $eventModel->getById($eventId);
        $participants = $eventModel->getParticipants($eventId);
    
        // Verifica se encontrou o evento
        if (!$eventData) {
            echo "Evento não encontrado no banco de dados.";
            return;
        }
    
        // Inclui a view com as variáveis corretamente definidas
        require '../app/views/event_show.php';
    }

    public function getByIdAjax() {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo json_encode(['error' => 'ID não informado']);
            return;
        }

        $event = new Event();
        $eventData = $event->getById($_GET['id']);
        $participants = $event->getParticipants($_GET['id']);
        $participantsIds = array_column($participants, 'id');

        echo json_encode([
            'event' => $eventData,
            'participants' => $participantsIds
        ]);
    }

    public function delete() {
        header('Content-Type: application/json');
    
        // Lê o corpo JSON da requisição
        $data = json_decode(file_get_contents("php://input"), true);
        $eventId = $data['id'] ?? null;
    
        if (!$eventId) {
            echo json_encode(['success' => false, 'error' => 'ID do evento não fornecido.']);
            exit;
        }
    
        $event = new Event();
        $deleted = $event->delete($eventId);
    
        if ($deleted) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao excluir evento.']);
        }
    
        exit;
    }
    
    
    
    
}
