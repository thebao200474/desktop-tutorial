<?php
// View hiển thị chi tiết một chủ đề cùng danh sách bài giảng liên quan.
// Biến $topic, $lessons, $page và $totalPages được TopicController truyền xuống.

$searchTerm = $_GET['q'] ?? '';
$queryParams = $_GET ?? [];
unset($queryParams['page']);
$baseQueryString = http_build_query($queryParams);
$appendQuery = $baseQueryString !== '' ? '&' . $baseQueryString : '';
?>

<section class="my-4">
    <!-- Thanh định hướng breadcrumb giúp người dùng quay lại các trang trước -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/topics">Chủ đề</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo htmlspecialchars($topic['ten_chude'] ?? ''); ?>
            </li>
        </ol>
    </nav>

    <!-- Tiêu đề và mô tả ngắn của chủ đề -->
    <header class="mb-4">
        <h2 class="fw-bold">
            <i class="fa-solid <?php echo htmlspecialchars($topic['icon'] ?? 'fa-flask'); ?> me-2"></i>
            <?php echo htmlspecialchars($topic['ten_chude'] ?? ''); ?>
        </h2>
        <?php if (!empty($topic['mota'])) : ?>
            <p class="text-muted mb-0"><?php echo htmlspecialchars($topic['mota']); ?></p>
        <?php endif; ?>
    </header>

    <!-- Ô tìm kiếm trong phạm vi chủ đề -->
    <form class="row g-2 mb-4" method="get">
        <div class="col-sm-8 col-md-6">
            <input class="form-control" type="text" name="q" placeholder="Tìm bài trong chủ đề..."
                   value="<?php echo htmlspecialchars($searchTerm); ?>">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
            </button>
        </div>
    </form>

    <!-- Danh sách bài giảng theo dạng card -->
    <div class="row g-3">
        <?php if (!empty($lessons)) : ?>
            <?php foreach ($lessons as $lesson) : ?>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2"><?php echo htmlspecialchars($lesson['title']); ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo htmlspecialchars($lesson['excerpt']); ?>...
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <a href="/lesson/<?php echo htmlspecialchars($lesson['slug']); ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-book-open me-1"></i> Xem bài
                                </a>
                                <?php if (!empty($lesson['updated_at'])) : ?>
                                    <?php $updatedAt = strtotime((string) $lesson['updated_at']); ?>
                                    <?php if ($updatedAt !== false) : ?>
                                        <span class="text-muted small">
                                            <i class="fa-solid fa-clock me-1"></i>
                                            <?php echo htmlspecialchars(date('d/m/Y', $updatedAt)); ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    Chưa có bài giảng nào trong chủ đề này.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Phân trang -->
    <nav class="mt-4" aria-label="Điều hướng phân trang bài giảng">
        <ul class="pagination">
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo htmlspecialchars((string) max(1, $page - 1) . $appendQuery); ?>">Trước</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo htmlspecialchars((string) $i . $appendQuery); ?>"><?php echo htmlspecialchars((string) $i); ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo htmlspecialchars((string) min($totalPages, $page + 1) . $appendQuery); ?>">Sau</a>
            </li>
        </ul>
    </nav>
</section>
