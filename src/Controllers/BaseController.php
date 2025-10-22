<?php

class BaseController {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->initCsrfToken();
    }

    protected function initCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    protected function verifyCsrfToken() {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            // CSRF token does not match
            $this->setFlashMessage('Geçersiz veya süresi dolmuş bir form gönderdiniz. Lütfen tekrar deneyin.', 'danger');
            http_response_code(403);
            $this->redirect(''); // Redirect to home or a specific error page
        }
    }

    protected function verifyCsrfTokenApi() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $input['csrf_token'])) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            exit();
        }
    }

    protected function authorize($roles = []) {
        if (!isset($_SESSION['user_id'])) {
            $this->setFlashMessage('Bu sayfayı görüntülemek için giriş yapmalısınız.', 'danger');
            $this->redirect('user/login');
        }

        if (!empty($roles) && !in_array($_SESSION['user_role'], $roles)) {
            $this->setFlashMessage('Bu sayfayı görüntüleme yetkiniz yok.', 'danger');
            http_response_code(403);
            $this->redirect('');
        }
    }

    protected function authorizeApi($roles = []) {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401); // Unauthorized
            echo json_encode(['success' => false, 'message' => 'Bu işlemi yapmak için giriş yapmalısınız.']);
            exit();
        }

        if (!empty($roles) && !in_array($_SESSION['user_role'], $roles)) {
            http_response_code(403); // Forbidden
            echo json_encode(['success' => false, 'message' => 'Bu işlemi yapma yetkiniz yok.']);
            exit();
        }
    }

    protected function view($viewName, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . "/../Views/{$viewName}.php";
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout.php';
    }
    
    protected function redirect($url) {
        header("Location: " . BASE_URL . "/{$url}");
        exit();
    }

    protected function setFlashMessage($message, $type = 'success') {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
}
?>