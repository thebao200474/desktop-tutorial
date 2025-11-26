<?php
use function htmlspecialchars as h;
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Đăng ký tài khoản ChemLearn</h1>
                <p class="text-muted">Tạo tài khoản để theo dõi tiến độ học tập và lưu kết quả làm bài.</p>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= h($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken); ?>">
                    <div class="col-12">
                        <label for="hoten" class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" id="hoten" name="hoten" value="<?= h($_POST['hoten'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label for="tendangnhap" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="tendangnhap" name="tendangnhap" value="<?= h($_POST['tendangnhap'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label for="matkhau" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="matkhau" name="matkhau" required>
                    </div>
                    <div class="col-12 d-grid">
                        <button type="submit" class="btn btn-primary">Đăng ký</button>
                    </div>
                </form>

                <p class="mt-3 mb-0">Đã có tài khoản? <a href="<?= app_url('dangnhap.php'); ?>">Đăng nhập ngay</a>.</p>
            </div>
        </div>
    </div>
</div>
