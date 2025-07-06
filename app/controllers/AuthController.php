<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    public function loginForm() {
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo "<script>alert('Por favor, preencha todos os campos.'); window.location.href = '/login';</script>";
            return;
        }

        $userModel = new User();
        $user = $userModel->login($email, $password);

        if ($user) {
            session_start();
            $_SESSION['user'] = $user;
            header("Location: ../event/index");
        } else {
            echo "<script>alert('Login inválido! Verifique seu e-mail e senha.'); window.location.href = '/login';</script>";
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: /login");
    }
}
