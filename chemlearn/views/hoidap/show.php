<?php
use function htmlspecialchars as h;

$renderRichText = static function (?string $html): string {
    $allowedTags = '<p><br><strong><em><u><ol><ul><li><a><table><thead><tbody><tr><td><th><blockquote><code><pre>';
    $clean = strip_tags((string) $html, $allowedTags);
    $clean = preg_replace('/on[a-z]+="[^"]*"/i', '', $clean ?? '');
    $clean = preg_replace('/javascript:/i', '', $clean ?? '');

    return $clean !== '' ? $clean : '<p class="text-muted">(Chưa có nội dung)</p>';
};

$questionAuthor = $question['nguoi_hoi'] ?? 'Ẩn danh';
$status = ($question['trang_thai'] ?? 'open') === 'solved' ? 'Đã giải' : 'Chưa giải';
$statusClass = ($question['trang_thai'] ?? 'open') === 'solved' ? 'success' : 'warning';
$createdAt = !empty($question['created_at']) ? date('d/m/Y H:i', strtotime((string) $question['created_at'])) : '';
$canMarkBest = !empty($currentUser['ma_user']) && !empty($question['user_id']) && (int) $currentUser['ma_user'] === (int) $question['user_id'];
$returnUrl = !empty($_GET['from']) && $_GET['from'] === 'mine' ? app_url('hoi-dap?mine=1') : app_url('hoi-dap');
?>
<section class="mb-4">
    <a class="btn btn-link px-0" href="<?= h($returnUrl); ?>">&larr; Quay lại danh sách</a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
        <div>
            <h1 class="fw-bold mb-2"><?= h($question['tieu_de'] ?? ''); ?></h1>
            <p class="text-muted mb-1">
                Đăng bởi <strong><?= h($questionAuthor); ?></strong>
                <?= $createdAt !== '' ? ' • ' . h($createdAt) : ''; ?>
            </p>
            <span class="badge text-bg-<?= $statusClass; ?>"><?= $status; ?></span>
            <span class="badge text-bg-light text-dark"><?= (int) ($question['luot_xem'] ?? 0); ?> lượt xem</span>
        </div>
        <div class="text-end">
            <a href="<?= app_url('hoi-dap/hoi'); ?>" class="btn btn-outline-primary">Hỏi câu khác</a>
            <?php if (!empty($canDelete)): ?>
                <form class="d-inline" action="<?= app_url('hoi-dap/' . $question['id'] . '/xoa'); ?>" method="post" onsubmit="return confirm('Bạn chắc chắn muốn xóa câu hỏi này?');">
                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken); ?>">
                    <input type="hidden" name="redirect" value="<?= h($returnUrl); ?>">
                    <button type="submit" class="btn btn-outline-danger">Xóa câu hỏi</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="question-content rich-text">
            <?= $renderRichText($question['noi_dung_html'] ?? ''); ?>
        </div>

        <?php if (!empty($files)): ?>
            <div class="mt-4">
                <h5 class="fw-semibold">Đính kèm</h5>
                <div class="d-flex flex-wrap gap-3">
                    <?php foreach ($files as $file): ?>
                        <?php
                        $path = $file['duong_dan'] ?? '';
                        $label = $file['ten_goc'] ?? basename((string) $path);
                        $isUrl = filter_var($path, FILTER_VALIDATE_URL);
                        $href = $isUrl ? $path : asset_url('uploads/questions/' . ltrim((string) $path, '/'));
                        $isImage = !$isUrl && preg_match('/\.(png|jpe?g|gif|webp)$/i', (string) $path);
                        ?>
                        <div class="attachment-tile">
                            <?php if ($isImage): ?>
                                <img src="<?= h($href); ?>" alt="<?= h($label); ?>" class="img-fluid rounded mb-2">
                            <?php else: ?>
                                <div class="icon">📎</div>
                            <?php endif; ?>
                            <a class="d-block small" href="<?= h($href); ?>" target="_blank" rel="noopener">
                                <?= h($label); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="answers" class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0"><?= (int) ($question['so_cau_tra_loi'] ?? count($answers)); ?> trả lời</h2>
        <span class="text-muted">Chia sẻ cách giải hoặc kinh nghiệm học tập của bạn.</span>
    </div>

    <?php if ($answers === []): ?>
        <div class="alert alert-light border">Chưa có câu trả lời nào. Hãy là người đầu tiên!</div>
    <?php endif; ?>

    <div class="vstack gap-3">
        <?php foreach ($answers as $answer): ?>
            <?php
            $answerAuthor = $answer['user_id'] ? 'Thành viên #' . (int) $answer['user_id'] : 'Khách ChemLearn';
            $answerTime = !empty($answer['created_at']) ? date('d/m/Y H:i', strtotime((string) $answer['created_at'])) : '';
            ?>
            <article class="card border-0 shadow-sm">
                <div class="card-body">
                    <?php if (!empty($answer['is_best'])): ?>
                        <div class="badge text-bg-success mb-2">Trả lời hay nhất</div>
                    <?php endif; ?>
                    <div class="rich-text mb-3">
                        <?= $renderRichText($answer['noi_dung_html'] ?? ''); ?>
                    </div>
                    <p class="text-muted small mb-0"><?= h($answerAuthor); ?> <?= $answerTime !== '' ? '• ' . h($answerTime) : ''; ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section id="answer-form" class="card border-0 shadow-sm">
    <div class="card-body">
        <h3 class="h5 mb-3">Trả lời câu hỏi này</h3>
        <form method="post" action="<?= app_url('hoi-dap/' . $question['id']); ?>#answers" class="vstack gap-3">
            <input type="hidden" name="csrf_token" value="<?= h($csrfToken); ?>">
            <textarea class="form-control" name="answer_html" rows="5" placeholder="Nhập lời giải hoặc gợi ý của bạn..." required></textarea>
            <?php if ($canMarkBest): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="markBest" name="is_best">
                    <label class="form-check-label" for="markBest">Đánh dấu là câu trả lời hay nhất</label>
                </div>
            <?php endif; ?>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Gửi câu trả lời</button>
            </div>
        </form>
    </div>
</section>
