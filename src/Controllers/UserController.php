<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/User.php';

class UserController extends BaseController {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfToken();
            $userModel = new User($this->pdo);
            $user = $userModel->findByEmail($_POST['email']);

            if ($user && password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['full_name'];
                $this->setFlashMessage('Giriş başarılı! Hoş geldiniz, ' . $user['full_name']);
                $this->redirect('');
            } else {
                $this->setFlashMessage('Geçersiz e-posta veya şifre.', 'danger');
                $this->redirect('user/login');
            }
        } else {
            $this->view('login');
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfToken();
            $userModel = new User($this->pdo);
            $existingUser = $userModel->findByEmail($_POST['email']);

            if ($existingUser) {
                $this->setFlashMessage('Bu e-posta adresi zaten kullanılıyor.', 'danger');
                $this->redirect('user/register');
            }

            if ($_POST['password'] !== $_POST['password_confirm']) {
                $this->setFlashMessage('Şifreler uyuşmuyor.', 'danger');
                $this->redirect('user/register');
            }

            $userModel->create($_POST['full_name'], $_POST['email'], $_POST['password']);
            $this->setFlashMessage('Kaydınız başarıyla oluşturuldu. Lütfen giriş yapın.');
            $this->redirect('user/login');

        } else {
            $this->view('register');
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        $this->redirect('');
    }
    
    public function account() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('user/login');
        }
        
        $userModel = new User($this->pdo);
        $user = $userModel->findById($_SESSION['user_id']);
        
        require_once __DIR__ . '/../Models/Ticket.php';
        $ticketModel = new Ticket($this->pdo);
        
        // Biletleri çekmeden önce geçmiş seferlerin bilet durumunu güncelle
        $ticketModel->expireOldTickets($_SESSION['user_id']);
        
        $tickets = $ticketModel->getTicketsByUserId($_SESSION['user_id']);
        
        
        $this->view('account', ['user' => $user, 'tickets' => $tickets]);
    }
}
?>