<?php
use function htmlspecialchars as h;
?>
<a href="<?= app_url('chuyende.php'); ?>" class="btn btn-link ps-0">← Quay lại danh sách chuyên đề</a>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h1 class="h3 mb-3"><?= h($lesson['ten_baigiang']); ?></h1>
        <div class="text-muted mb-3">Mã bài giảng: <?= (int)$lesson['ma_baigiang']; ?></div>
        <div class="lesson-content lh-lg">
            <?= nl2br(h($lesson['noidung'] ?? 'Nội dung đang cập nhật.')); ?>
        </div>
    </div>
</div>
