<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5 mx-auto">
    <div class="card shadow-lg">
        <div class="card-header text-center bg-dark text-white">
            <img src="/sibervatanbilet/public/assets/img/logo.png" alt="Logo" style="height: 60px;">
            <h4 class="mt-2 mb-0">Hesap Oluştur</h4>
        </div>
        <div class="card-body p-4">
            <form action="/sibervatanbilet/public/user/register" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Ad Soyad" required>
                        <label for="full_name">Ad Soyad</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="E-posta Adresiniz" required>
                        <label for="email">E-posta Adresi</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Şifreniz" required>
                        <label for="password">Şifre</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Şifre Tekrar" required>
                        <label for="password_confirm">Şifre Tekrar</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Kayıt Ol</button>
                    </div>
                </form>
                <div class="text-center mt-4">
                    <p class="mb-0">Zaten bir hesabın var mı? <a href="/sibervatanbilet/public/user/login" class="fw-bold">Giriş Yap</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
