<?php
use function htmlspecialchars as h;

$sortOptions = [
    'newest' => 'Mới nhất',
    'views' => 'Nhiều lượt xem',
    'answers' => 'Nhiều trả lời',
];
$sortLabel = $sortOptions[$sort] ?? $sortOptions['newest'];

$buildSortUrl = function (string $key) use ($search) {
    $params = [];
    if ($search !== '') {
        $params['q'] = $search;
    }
    $params['sort'] = $key;

    return app_url('hoi-dap') . '?' . http_build_query($params);
};

$buildPageUrl = function (int $pageNumber) use ($search, $sort) {
    $params = [];
    if ($search !== '') {
        $params['q'] = $search;
    }
    if ($sort !== '') {
        $params['sort'] = $sort;
    }
    $params['page'] = $pageNumber;

    return app_url('hoi-dap') . '?' . http_build_query($params);
};
?>
<section class="mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
        <div>
            <p class="text-uppercase text-primary fw-semibold mb-1">ChemLearn Q&A</p>
            <h1 class="display-6 fw-bold">Hỏi – Đáp Hóa học</h1>
            <p class="text-muted mb-0">Tìm kiếm câu trả lời nhanh từ cộng đồng học sinh yêu Hóa.</p>
        </div>
        <div>
            <a href="<?= app_url('hoi-dap/hoi'); ?>" class="btn btn-primary btn-lg shadow-sm">
                💡 Hỏi tại đây
            </a>
        </div>
    </div>
</section>

<section class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form class="row gy-3 gx-2 align-items-center" method="get" action="<?= app_url('hoi-dap'); ?>">
            <div class="col-12 col-lg-6">
                <label class="form-label text-muted small mb-1" for="searchQuestion">Tìm trong hỏi đáp…</label>
                <input id="searchQuestion" type="text" name="q" class="form-control form-control-lg"
                       placeholder="Nhập từ khóa (H₂, dung dịch, …)" value="<?= h($search); ?>">
            </div>
            <div class="col-12 col-lg-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">Tìm kiếm</button>
            </div>
            <div class="col-12 col-lg-3 d-flex align-items-end">
                <div class="dropdown w-100">
                    <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                        Sắp xếp: <?= h($sortLabel); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end w-100">
                        <?php foreach ($sortOptions as $key => $label): ?>
                            <li><a class="dropdown-item" href="<?= h($buildSortUrl($key)); ?>"><?= h($label); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</section>

<section class="question-list">
    <?php if ($questions === []): ?>
        <div class="alert alert-light border shadow-sm">Không tìm thấy câu hỏi phù hợp.</div>
    <?php endif; ?>

    <?php foreach ($questions as $question): ?>
        <?php
        $status = ($question['trang_thai'] ?? 'open') === 'solved' ? 'Đã giải' : 'Chưa giải';
        $statusClass = ($question['trang_thai'] ?? 'open') === 'solved' ? 'text-success' : 'text-warning';
        $createdAt = !empty($question['created_at']) ? date('d/m/Y H:i', strtotime((string) $question['created_at'])) : '';
        $author = $question['nguoi_hoi'] ?? 'Ẩn danh';
        ?>
        <article class="card border-0 shadow-sm mb-3 question-card">
            <div class="card-body d-flex flex-column flex-md-row align-items-start gap-3">
                <div class="flex-grow-1">
                    <a class="question-title" href="<?= app_url('hoi-dap/' . $question['id']); ?>">
                        <?= h($question['tieu_de']); ?>
                    </a>
                    <p class="mb-2 text-muted small">
                        <span class="fw-semibold <?= $statusClass; ?>"><?= $status; ?></span>
                        • <?= (int) ($question['luot_xem'] ?? 0); ?> lượt xem
                        • <?= h($author); ?>
                        <?= $createdAt !== '' ? '• ' . h($createdAt) : ''; ?>
                    </p>
                </div>
                <div class="text-center border rounded-3 px-3 py-2 bg-light-subtle shadow-sm">
                    <div class="fs-4 fw-bold text-primary"><?= (int) ($question['so_cau_tra_loi'] ?? 0); ?></div>
                    <div class="text-uppercase text-muted small">trả lời</div>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<?php if ($totalPages > 1): ?>
    <nav aria-label="Phân trang hỏi đáp" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?= $page <= 1 ? '#' : h($buildPageUrl($page - 1)); ?>">&laquo;</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="<?= h($buildPageUrl($i)); ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?= $page >= $totalPages ? '#' : h($buildPageUrl($page + 1)); ?>">&raquo;</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
