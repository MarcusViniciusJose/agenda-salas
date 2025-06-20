<?php
require_once __DIR__ . '/../models/User.php';

class AuthController{
    public function loginForm(){
        require __DIR__ . '/../views/auth/login.php';

    }

    public function login(){
        $email = $_POST['email'];
        $password = $_POST['password'];
        $userModel = new User();
        $user = $userModel->login($email,$password);

        if($user){
            session_start();
            $_SESSION['user'] = $user;
            header("Location: ../event/index");
        }else{
            echo "<pre>Login inválido.<br>Email: $email<br>Senha: $password</pre>";
        }
    }
    
    public function logout(){
        session_start();
        session_destroy();
        header("Location: /login");
    }
}