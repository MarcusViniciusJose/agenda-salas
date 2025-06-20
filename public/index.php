<?php
session_start();
require_once __DIR__ . '/../routes.php';

    $url = isset($_GET['url']) ? explode('/', $_GET['url']) : ['auth', 'loginForm'];
    $controllerName = ucfirst($url[0]) . 'Controller';
    $method = $url[1] ?? 'index';



$controller = new $controllerName();
call_user_func([$controller, $method]);

?>