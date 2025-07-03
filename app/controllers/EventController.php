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

        if (!$created_by) {
            echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
            return;
        }

        $eventId = $event->create($title, $start, $end, $sala, $created_by);

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

        echo json_encode(['success' => true]);
    }

    public function show(){
        $eventId = $_GET['id'] ?? null;

        if(!eventId){
            echo "Evento não encontrado";
            return;
        }

        $eventId = $_GET['id'];
        $event = new Event();
        $user = new User();
        $eventData = $event->getById($eventId);
        $participants = $event->getParticipants($eventId);

        require '../app/views/event/show.php';
    }
}
