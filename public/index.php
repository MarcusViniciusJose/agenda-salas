<?php

session_start();

$url = isset($_GET['url']) ? explode('/', $_GET['url']) : ['auth', 'loginForm'];

require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/EventController.php';
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/NotificationController.php'; 

$controllerName = ucfirst($url[0]) . 'Controller';
$method = $url[1] ?? 'index';

if (class_exists($controllerName) && method_exists($controllerName, $method)) {
    $controller = new $controllerName();
    call_user_func([$controller, $method]);
} else {
    echo "Rota não encontrada.";
}

if($_GET['action'] == 'confirm_notification'){
    (new NotificationController())->confirm();

}elseif($_GET['action'] == 'reject_notification'){
    (new NotificationController())->reject();
}
