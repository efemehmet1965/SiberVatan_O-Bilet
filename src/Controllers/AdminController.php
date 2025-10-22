<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/BusCompany.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Coupon.php';

class AdminController extends BaseController {

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->authorize(['Admin']);
    }

    public function dashboard() {
        $this->view('admin/dashboard');
    }

    public function companies() {
        $companyModel = new BusCompany($this->pdo);
        $companies = $companyModel->getAll();
        $this->view('admin/companies', ['companies' => $companies]);
    }

    public function createCompany() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $companyModel = new BusCompany($this->pdo);
            $companyModel->create($_POST['name']);
            $this->setFlashMessage('Yeni firma başarıyla oluşturuldu.');
            $this->redirect('admin/companies');
        } else {
            $this->view('admin/create_company');
        }
    }

    public function editCompany($id) {
        $companyModel = new BusCompany($this->pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $companyModel->update($id, $_POST['name']);
            $this->setFlashMessage('Firma başarıyla güncellendi.');
            $this->redirect('admin/companies');
        } else {
            $company = $companyModel->findById($id);
            $this->view('admin/edit_company', ['company' => $company]);
        }
    }

    public function deleteCompany($id) {
        $userModel = new User($this->pdo);
        $tripModel = new Trip($this->pdo);

        // Firmaya bağlı kullanıcı (Firma Admini) var mı?
        $userCount = $userModel->countByCompanyId($id);
        if ($userCount > 0) {
            $this->setFlashMessage('Bu firmaya atanmış kullanıcılar varken firma silinemez.', 'danger');
            $this->redirect('admin/companies');
            return;
        }

        // Firmaya ait sefer var mı?
        $tripCount = $tripModel->countByCompanyId($id);
        if ($tripCount > 0) {
            $this->setFlashMessage('Bu firmanın aktif seferleri varken firma silinemez.', 'danger');
            $this->redirect('admin/companies');
            return;
        }

        // Firmaya ait kupon var mı?
        $couponModel = new Coupon($this->pdo);
        $couponCount = $couponModel->countByCompanyId($id);
        if ($couponCount > 0) {
            $this->setFlashMessage('Bu firmaya ait kuponlar varken firma silinemez.', 'danger');
            $this->redirect('admin/companies');
            return;
        }

        $companyModel = new BusCompany($this->pdo);
        $companyModel->delete($id);
        $this->setFlashMessage('Firma başarıyla silindi.', 'danger');
        $this->redirect('admin/companies');
    }

    public function users() {
        $userModel = new User($this->pdo);
        $users = $userModel->getCompanyAdmins();
        $this->view('admin/users', ['users' => $users]);
    }

    public function createUser() {
        $companyModel = new BusCompany($this->pdo);
        $companies = $companyModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User($this->pdo);
            $userModel->createCompanyAdmin(
                $_POST['full_name'],
                $_POST['email'],
                $_POST['password'],
                $_POST['company_id']
            );
            $this->setFlashMessage('Firma admini başarıyla oluşturuldu.');
            $this->redirect('admin/users');
        } else {
        $this->view('admin/create_user', ['companies' => $companies]);
        }
    }

    public function editUser($id) {
        $userModel = new User($this->pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel->updateCompanyAdmin(
                $id,
                $_POST['full_name'],
                $_POST['email'],
                $_POST['company_id'],
                $_POST['password']
            );
            $this->setFlashMessage('Firma admini başarıyla güncellendi.');
            $this->redirect('admin/users');
        } else {
            $companyModel = new BusCompany($this->pdo);
            $user = $userModel->findById($id);
            $companies = $companyModel->getAll();
            $this->view('admin/edit_user', ['user' => $user, 'companies' => $companies]);
        }
    }

        public function deleteUser($id)

        {

            $userModel = new User($this->pdo);

            $userModel->delete($id);

            $this->setFlashMessage('Firma admini başarıyla silindi.', 'danger');

            $this->redirect('admin/users');

        }

    // Coupon Management
    public function coupons() {
        $couponModel = new Coupon($this->pdo);
        $coupons = $couponModel->getGlobal();
        $this->view('admin/coupons', ['coupons' => $coupons]);
    }

    public function createCoupon() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $couponModel = new Coupon($this->pdo);
            $couponModel->create(
                $_POST['code'],
                $_POST['discount'],
                $_POST['usage_limit'],
                $_POST['expire_date'],
                null // Global coupon
            );
            $this->setFlashMessage('Global kupon başarıyla oluşturuldu.');
            $this->redirect('admin/coupons');
        } else {
            $this->view('admin/create_coupon');
        }
    }

    public function editCoupon($id) {
        $couponModel = new Coupon($this->pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $couponModel->update(
                $id,
                $_POST['code'],
                $_POST['discount'],
                $_POST['usage_limit'],
                $_POST['expire_date']
            );
            $this->setFlashMessage('Kupon başarıyla güncellendi.');
            $this->redirect('admin/coupons');
        } else {
            $coupon = $couponModel->findById($id);
            $this->view('admin/edit_coupon', ['coupon' => $coupon]);
        }
    }

    public function deleteCoupon($id) {
        $couponModel = new Coupon($this->pdo);
        $couponModel->delete($id);
        $this->setFlashMessage('Kupon başarıyla silindi.', 'danger');
        $this->redirect('admin/coupons');
    }

    }
?>