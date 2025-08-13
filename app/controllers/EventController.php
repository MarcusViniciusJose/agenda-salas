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
        $repeat = $_POST['repeat'] ?? 'none'; 
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
            if (!$event->isOwner($eventId, $created_by)) {
                echo json_encode(['success' => false, 'error' => 'Você não tem permissão para editar este evento.']);
                return;
            }
        
            $event->update($eventId, $title, $start, $end, $sala);
            $event->removeParticipants($eventId);
        } else {
            $eventId = $event->create($title, $start, $end, $sala, $created_by);
    
            if ($repeat !== 'none') {
                $this->createRecurringEvents($repeat, $title, $start, $end, $sala, $created_by, $event, $participants, $notificationModel);
            }
        }
        
        if ($eventId && count($participants) > 0) {
            foreach ($participants as $userId) {
                $event->addParticipants($eventId, $userId);
    
                $notificationModel->create(
                    $userId,
                    "Você foi convidado para o evento <strong>$titleSafe</strong> no dia <strong>$startSafe</strong>.",
                    "/public/index.php?url=event/show&id=$eventId",
                    $eventId
                );
            }
        }
    
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
    
   
    private function createRecurringEvents($repeat, $title, $start, $end, $sala, $created_by, $event, $participants, $notificationModel) {
        $interval = match($repeat) {
            'daily' => '+1 day',
            'weekly' => '+1 week',
            'monthly' => '+1 month',
            default => null
        };
    
        if (!$interval) return;
    
        $titleSafe = htmlspecialchars($title);
    
        for ($i = 1; $i <= 10; $i++) {
            $newStart = date('Y-m-d H:i:s', strtotime($interval, strtotime($start)));
            $newEnd = date('Y-m-d H:i:s', strtotime($interval, strtotime($end)));
    
            $start = $newStart;
            $end = $newEnd;
    
            if ($event->hasConflict($start, $end, $sala)) {
                continue; 
            }
    
            $newEventId = $event->create($title, $start, $end, $sala, $created_by);
    
            foreach ($participants as $userId) {
                $event->addParticipants($newEventId, $userId);
    
                $notificationModel->create(
                    $userId,
                    "Você foi convidado para o evento <strong>$titleSafe</strong> no dia <strong>" .
                    htmlspecialchars(date('d/m/Y H:i', strtotime($start))) . "</strong>.",
                    "/public/index.php?url=event/show&id=$newEventId",
                    $newEventId
                );
            }
        }
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
    
        if (!$eventData) {
            echo "Evento não encontrado no banco de dados.";
            return;
        }
    
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

        echo json_encode([
            'event' => $eventData,
            'participants' => $participants
        ]);
    }

    public function updateDate(){
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents("php://input"), true);
    
    $id = $input['id'] ?? null;
    $start = $input['start'] ?? null;
    $end = $input['end'] ?? null;

    if (!$id || !$start || !$end) {
        echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
        return;
    }

    $diaSemana = date('w', strtotime($start)); 
    if ($diaSemana == 0 || $diaSemana == 6) {
        echo json_encode(['success' => false, 'error' => 'Não é permitido mover eventos para finais de semana.']);
        return;
    }

    try {
        $event = new Event();
        
        $currentEvent = $event->getById($id);
        if (!$currentEvent) {
            echo json_encode(['success' => false, 'error' => 'Evento não encontrado.']);
            return;
        }
        
        $sala = $currentEvent['sala'];

        if ($event->hasConflict($start, $end, $sala, $id)) { 
            echo json_encode(['success' => false, 'error' => 'Conflito de agendamento! Já existe um evento nesta sala nesse horário.']);
            return;
        }

        $result = $event->updateDate($id, $start, $end);
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao atualizar']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}



    public function delete() {
        header('Content-Type: application/json');
    
        $data = json_decode(file_get_contents("php://input"), true);
        $eventId = $data['id'] ?? null;
    
        if (!$eventId) {
            echo json_encode(['success' => false, 'error' => 'ID do evento não fornecido.']);
            exit;
        }
    
        $event = new Event();
        $notificationModel = new Notification();

        $notificationModel->deleteByEventId($eventId);

        $deleted = $event->delete($eventId);
    
        if ($deleted) {

            $notificationModel->deleteByEventId($eventId);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao excluir evento.']);
        }
    
        exit;
    }
    
}
