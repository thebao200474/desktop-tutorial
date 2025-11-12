<?php
use function htmlspecialchars as h;
?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h4 mb-1">Ngân hàng đề thi</h1>
                <p class="text-muted mb-0">Chọn đề thi trắc nghiệm, luyện tập và nhận đáp án ngay sau khi nộp bài.</p>
            </div>
            <a href="<?= app_url(); ?>" class="btn btn-outline-secondary">← Trang chủ</a>
        </div>
        <div class="row g-4">
            <?php if (empty($exams)): ?>
                <p class="text-muted">Hiện chưa có đề thi nào. Thêm dữ liệu vào bảng <code>de_thi</code> để bắt đầu.</p>
            <?php else: ?>
                <?php foreach ($exams as $exam): ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5 mb-2"><?= h($exam['ten_de']); ?></h2>
                                <p class="small text-muted mb-2">Nguồn: <?= h($exam['nguon'] ?? 'Chưa rõ'); ?> · Mã đề <?= h($exam['ma_de'] ?? ''); ?></p>
                                <?php if (!empty($exam['mo_ta'])): ?>
                                    <p class="mb-3"><?= h($exam['mo_ta']); ?></p>
                                <?php endif; ?>
                                <a href="<?= app_url('de_thi.php?id=' . (int)$exam['id']); ?>" class="btn btn-primary btn-sm">Bắt đầu làm đề</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
