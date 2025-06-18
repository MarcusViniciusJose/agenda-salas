<?php
require_once 'app/models/User.php';

class AuthController{
    public function loginForm(){
        require 'app/views/auth/login.php';

    }

    public function login(){
        $email = $_POST['email'];
        $password = $_POST['password'];
        $userModel = new User();
        $user = $userModel->login($email,$password);

        if($user){
            session_start();
            $_SESSION['user'] = $user;
            header("Location: /dashboard");
        }else{
            echo "Login inválido";
        }
    }
    
    public function logout(){
        session_start();
        session_destroy();
        header("Location: /login");
    }
}