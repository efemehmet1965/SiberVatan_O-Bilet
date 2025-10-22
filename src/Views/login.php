<div class="row justify-content-center">
<div class="col-md-6 col-lg-4 mx-auto">
    <div class="card shadow-lg">
        <div class="card-header text-center bg-dark text-white">
            <img src="/sibervatanbilet/public/assets/img/logo.png" alt="Logo" style="height: 60px;">
            <h4 class="mt-2 mb-0">Kullanıcı Girişi</h4>
        </div>
        <div class="card-body p-4">
            <form action="/sibervatanbilet/public/user/login" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="E-posta Adresiniz" required>
                        <label for="email">E-posta Adresi</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Şifreniz" required>
                        <label for="password">Şifre</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Giriş Yap</button>
                    </div>
                </form>
                <div class="text-center mt-4">
                    <p class="mb-0">Hesabın yok mu? <a href="/sibervatanbilet/public/user/register" class="fw-bold">Hemen Kayıt Ol</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
