<?php
use function htmlspecialchars as h;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Chuyên đề Hóa học</h1>
        <p class="text-muted mb-0">Khám phá các bài giảng trọng tâm cho môn Hóa học.</p>
    </div>
    <a href="index.php" class="btn btn-outline-primary">← Về trang chủ</a>
</div>

<?php if (empty($lessons)): ?>
    <div class="alert alert-warning">Chưa có chuyên đề trong cơ sở dữ liệu. Hãy thêm dữ liệu vào bảng <code>baigiang</code>.</div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($lessons as $lesson): ?>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <a href="chuyende_chitiet.php?id=<?= (int)$lesson['ma_baigiang']; ?>" class="text-decoration-none">
                                <?= h($lesson['ten_baigiang']); ?>
                            </a>
                        </h5>
                        <p class="card-text text-muted"><?= h(mb_strimwidth(strip_tags($lesson['noidung'] ?? ''), 0, 160, '...')); ?></p>
                        <a href="chuyende_chitiet.php?id=<?= (int)$lesson['ma_baigiang']; ?>" class="btn btn-primary btn-sm">Đọc chi tiết</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
