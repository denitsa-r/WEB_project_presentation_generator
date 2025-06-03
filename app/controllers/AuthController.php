<?php

class AuthController extends Controller
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = $this->model('User');
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: ' . BASE_URL . '/home/index');
                exit;
            } else {
                $error = 'Грешен имейл или парола.';
                $this->view('auth/login', ['error' => $error]);
                return;
            }
        }

        $this->view('auth/login');
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = $this->model('User');
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

            if ($userModel->findByEmail($email)) {
                $error = 'Имейлът вече съществува.';
                $this->view('auth/register', ['error' => $error]);
                return;
            }

            if ($userModel->create($username, $email, $password_hash)) {
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            } else {
                $error = 'Възникна грешка при регистрацията.';
                $this->view('auth/register', ['error' => $error]);
                return;
            }
        }

        $this->view('auth/register');
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}
