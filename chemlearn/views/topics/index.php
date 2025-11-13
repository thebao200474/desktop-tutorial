<?php
// View hiển thị danh sách tất cả chủ đề học tập cùng bộ đếm bài.
// Các biến $topics và $q được TopicController truyền sang khi render trang.
?>
<section class="my-4">
    <!-- Breadcrumb giúp người dùng định vị vị trí hiện tại trên website -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chuyên đề</li>
        </ol>
    </nav>

    <!-- Tiêu đề trang cùng mô tả ngắn gọn -->
    <header class="mb-4">
        <h2 class="fw-bold">
            <i class="fa-solid fa-flask me-2"></i>
            Danh sách chuyên đề Hóa học
        </h2>
        <p class="text-muted mb-0">Chọn một chuyên đề để khám phá các bài giảng chi tiết và ví dụ minh họa.</p>
    </header>

    <!-- Form tìm kiếm chủ đề theo tên, giữ lại giá trị đã nhập -->
    <form class="row g-2 mb-4" method="get">
        <div class="col-sm-8 col-md-6">
            <input
                class="form-control"
                type="text"
                name="q"
                placeholder="Tìm chuyên đề (vd: Hữu cơ, Vô cơ...)"
                value="<?php echo htmlspecialchars($q ?? ''); ?>"
            >
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-magnifying-glass me-1"></i> Tìm
            </button>
            <?php if (!empty($q)) : ?>
                <a class="btn btn-outline-secondary" href="/topics">Xóa lọc</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Lưới card hiển thị từng chủ đề cùng tổng số bài học -->
    <div class="row g-3">
        <?php if (!empty($topics)) : ?>
            <?php foreach ($topics as $topic) : ?>
                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 py-3">
                        <div class="card-body text-center d-flex flex-column">
                            <i class="fa-solid <?php echo htmlspecialchars($topic['icon'] ?? 'fa-flask'); ?> fa-2x text-primary mb-3"></i>
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($topic['ten_chude'] ?? ''); ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo htmlspecialchars(mb_strimwidth((string) ($topic['mota'] ?? ''), 0, 70, '...')); ?>
                            </p>
                            <span class="badge bg-light text-dark mb-2 align-self-center">
                                <i class="fa-solid fa-book-open me-1"></i>
                                <?php echo htmlspecialchars((string) (int) ($topic['total_lessons'] ?? 0)); ?> bài
                            </span>
                            <div>
                                <a href="/topics/<?php echo htmlspecialchars((string) (int) ($topic['ma_chude'] ?? 0)); ?>" class="btn btn-outline-primary btn-sm">
                                    Xem chuyên đề
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    Không tìm thấy chuyên đề phù hợp với từ khóa.
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
