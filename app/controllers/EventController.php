<?php

require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../models/User.php';

class EventController{
    public function index(){
        require '../app/views/dashboard.php';
    }

    public function all(){
        $event = new Event();
        $events = $event->getAll();
        echo json_encode($events);
    }

    
    public function sendEmailToUser($user_id, $title, $start, $end, $sala){
        $userModel = new User();
        $users = $userModel->getAll();

        foreach($users as $u){
            if($u['id'] == $user_id){
                $to = $u['email'];
                $subject = "Novo evento: $title";
                $body = "<p>Você foi convidado para o evento <strong>$title</strong>.</p>
                        <p><b>Data:</b> $start até $end<br><b>Sala: </b> $sala</p>";
                sendEmail($to, $subject, $body);
                break;
            }

        }

        
    }

    public function store() {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user'])) {
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }
    
        $event = new Event();
    
        $title = $_POST['title'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $sala = $_POST['sala'];
        $created_by = $_SESSION['user']['id'];
    
        $event_id = $event->create($title, $start, $end, $sala, $created_by);
        $participants = $_POST['participants'] ?? [];

        if(!empty($participants)){
            $event->addParticipants($event_id, $participants);
        }
    
        foreach($participants as $user_id){
            $this->sendEmailToUser($user_id, $title, $start, $end, $sala);
        }
        echo json_encode(['success' => true]);
    }

    
} 