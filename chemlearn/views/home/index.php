<?php
use function htmlspecialchars as h;
?>
<section class="hero text-center mb-5">
    <div class="container">
        <h1 class="display-5 fw-bold">Học Hóa học dễ hiểu hơn với ChemLearn</h1>
        <p class="lead mt-3">Nền tảng học và ôn tập Hóa học dành cho sinh viên CT275 - Công nghệ Web (Đại học Cần Thơ).</p>
        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="chuyende.php" class="btn btn-light btn-lg">Khám phá chuyên đề</a>
            <a href="cauhoi.php" class="btn btn-outline-light btn-lg">Làm bài trắc nghiệm</a>
        </div>
    </div>
</section>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm card-hover">
            <div class="card-body text-center">
                <div class="display-6 mb-3">📚</div>
                <h5 class="card-title">Học chuyên đề</h5>
                <p class="card-text">Hệ thống bài giảng chất lượng, dễ hiểu, bám sát chương trình.</p>
                <a href="chuyende.php" class="btn btn-primary">Xem chuyên đề</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm card-hover">
            <div class="card-body text-center">
                <div class="display-6 mb-3">⚖️</div>
                <h5 class="card-title">Cân bằng PTHH</h5>
                <p class="card-text">Công cụ gợi ý nhanh kết quả cân bằng phương trình hóa học.</p>
                <a href="canbang.php" class="btn btn-primary">Thử ngay</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm card-hover">
            <div class="card-body text-center">
                <div class="display-6 mb-3">🧪</div>
                <h5 class="card-title">Làm câu hỏi</h5>
                <p class="card-text">Bộ câu hỏi trắc nghiệm giúp bạn tự kiểm tra kiến thức.</p>
                <a href="cauhoi.php" class="btn btn-primary">Làm bài</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm card-hover">
            <div class="card-body text-center">
                <div class="display-6 mb-3">🤖</div>
                <h5 class="card-title">Hỏi đáp AI</h5>
                <p class="card-text">Đặt câu hỏi và nhận phản hồi mô phỏng từ AI ChemLearn.</p>
                <a href="hoi_dap.php" class="btn btn-primary">Đặt câu hỏi</a>
            </div>
        </div>
    </div>
</div>

<section class="mb-5">
    <h2 class="h4 mb-3">Bài giảng mới nhất</h2>
    <?php if (!empty($lessons)): ?>
        <div class="row g-4">
            <?php foreach ($lessons as $lesson): ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= h($lesson['ten_baigiang']); ?></h5>
                            <p class="card-text text-muted"><?= h(mb_strimwidth(strip_tags($lesson['noidung'] ?? ''), 0, 120, '...')); ?></p>
                            <a href="chuyende_chitiet.php?id=<?= (int)$lesson['ma_baigiang']; ?>" class="btn btn-outline-primary btn-sm">Đọc chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary">Chưa có bài giảng nào trong hệ thống. Vui lòng thêm dữ liệu mẫu.</div>
    <?php endif; ?>
</section>

<div class="floating-settings" data-settings>
    <button class="btn btn-primary rounded-circle shadow" type="button" data-settings-toggle aria-expanded="false" aria-controls="quickSettingsPanel">
        <span aria-hidden="true">⚙️</span>
        <span class="visually-hidden">Mở bảng cài đặt nhanh</span>
    </button>
    <div class="card shadow settings-panel d-none" id="quickSettingsPanel" data-settings-panel>
        <div class="card-body p-3">
            <h6 class="card-title h6 mb-3">Cài đặt nhanh</h6>
            <button class="btn btn-outline-secondary btn-sm w-100 mb-2" type="button" disabled>
                Chế độ tối <span class="text-muted">(đang phát triển)</span>
            </button>
            <a class="btn btn-primary btn-sm w-100" href="tien_do.php">Xem tiến độ học tập</a>
        </div>
    </div>
</div>

<div class="chat-bubble" data-chat>
    <button class="chat-bubble__button shadow" type="button" data-chat-toggle aria-expanded="false" aria-controls="chatPreview">
        <span aria-hidden="true">💬</span>
        <span class="visually-hidden">Mở trò chuyện với ChemBot</span>
    </button>
    <div class="chat-bubble__panel shadow d-none" id="chatPreview" data-chat-panel>
        <div class="chat-bubble__header">ChemBot</div>
        <p class="mb-2">Xin chào! Tôi có thể giúp bạn giải đáp các câu hỏi Hóa học.</p>
        <a class="btn btn-success btn-sm w-100" href="hoi_dap.php">Hỏi ChemBot</a>
    </div>
</div>
