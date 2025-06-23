<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

                // Enviar e-mail
                $userModel = new User();
                $user = $userModel->getById($userId);

                if ($user) {
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'seuemail@gmail.com';
                        $mail->Password = 'app_senha';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom('seuemail@gmail.com', 'Agenda de Salas');
                        $mail->addAddress($user['email'], $user['nome']);

                        $mail->isHTML(true);
                        $mail->Subject = 'Você foi convidado para um evento';
                        $mail->Body = "Olá {$user['nome']},<br>Você foi convidado para o evento <strong>$title</strong> no dia <strong>$start</strong>.";

                        $mail->send();
                    } catch (Exception $e) {
                        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
                    }
                }
            }
        }

        echo json_encode(['success' => true]);
    }
}
