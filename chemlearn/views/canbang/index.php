<?php
use function htmlspecialchars as h;
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Cân bằng phương trình hóa học</h1>
                <p class="text-muted">Nhập phương trình dạng "H2 + O2 -> H2O" để tra cứu dữ liệu trong hệ thống.</p>
                <form method="post" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken); ?>">
                    <div class="col-12">
                        <label for="equation" class="form-label">Phương trình cần cân bằng</label>
                        <input type="text" class="form-control" id="equation" name="equation" value="<?= h($inputEquation); ?>" placeholder="Ví dụ: H2 + O2 -> H2O">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Cân bằng</button>
                    </div>
                </form>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info mt-3"><?= h($message); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($result)): ?>
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-4">
                    <h2 class="h5">Kết quả tra cứu</h2>
                    <p class="mb-2"><strong>Phương trình đã cân bằng:</strong> <?= h($result['mota']); ?></p>
                    <p class="mb-2"><strong>Sản phẩm:</strong> <?= h($result['sanpham']); ?></p>
                    <p class="mb-0"><strong>Điều kiện:</strong> <?= h($result['madieukien'] ?? 'Không rõ'); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
