<?php

require_once 'app/models/Event.php';

class EventController{
    public function index(){
        require 'app/views/dashboard.php';
    }

    public function all(){
        $event = new Event();
        $events = $event->getAll();
        echo json_encode($events);
    }

    public function store(){
        session_start();
        $event = new Event();

        $title = $_POST['title'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $sala = $_POST['sala'];
        $created_by = $_SESSION['user']['id'];

        $event->create($title,$start,$end,$sala,$created_by);
        echo json_encode(['success' => true]);
        

    }
}