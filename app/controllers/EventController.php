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
        session_start();

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
                    "/event/show/$eventId"
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
    }

    public function show() {
        $eventId = $_GET['id'] ?? null;

        if (!$eventId) {
            echo "Evento não encontrado";
            return;
        }

        $event = new Event();
        $user = new User();
        $eventData = $event->getById($eventId);
        $participants = $event->getParticipants($eventId);

        require '../app/views/event/show.php';
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
        session_start();

        $eventId = $_POST['id'] ?? null;
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$eventId) {
            echo json_encode(['success' => false, 'error' => 'ID do evento não informado.']);
            return;
        }
        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
            return;
        }

        $event = new Event();
        $eventData = $event->getById($eventId);

        if (!$eventData || $eventData['created_by'] != $userId) {
            echo json_encode(['success' => false, 'error' => 'Você não tem permissão para excluir este evento']);
            return;
        }

        $event->delete($eventId);
        echo json_encode(['success' => true]);
    }
}
