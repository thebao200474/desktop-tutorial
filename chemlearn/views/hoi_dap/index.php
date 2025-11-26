<?php
use function htmlspecialchars as h;
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Hỏi đáp cùng ChemLearn AI</h1>
                <p class="text-muted">Đặt câu hỏi về Hóa học và nhận câu trả lời mô phỏng từ hệ thống. Nội dung chỉ mang tính tham khảo.</p>
                <form method="post" class="mb-3">
                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken); ?>">
                    <div class="mb-3">
                        <label for="question" class="form-label">Câu hỏi của bạn</label>
                        <textarea name="question" id="question" rows="4" class="form-control" placeholder="Ví dụ: Cách phân biệt axit mạnh và axit yếu?"><?= h($questionText); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi</button>
                </form>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-warning"><?= h($message); ?></div>
                <?php endif; ?>

                <?php if (!empty($answer)): ?>
                    <div class="alert alert-success">
                        <h2 class="h6">ChemLearn AI trả lời:</h2>
                        <p class="mb-0"><?= h($answer); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h2 class="h5 mb-0">Lịch sử trao đổi gần đây</h2>
            </div>
            <div class="card-body">
                <?php if (empty($history)): ?>
                    <p class="text-muted mb-0">Chưa có dữ liệu lịch sử. Hãy đặt câu hỏi đầu tiên của bạn!</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($history as $item): ?>
                            <div class="list-group-item">
                                <div class="fw-semibold">Câu hỏi:</div>
                                <p class="mb-1"><?= h($item['cau_hoi']); ?></p>
                                <div class="fw-semibold">Trả lời:</div>
                                <p class="mb-1 text-success"><?= h($item['cau_tra_loi']); ?></p>
                                <small class="text-muted">Thời gian: <?= h($item['thoigian']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
