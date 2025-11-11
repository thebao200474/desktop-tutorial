<?php
use function htmlspecialchars as h;
?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Tiến độ học tập</h1>
                <p class="text-muted">Theo dõi số câu làm đúng, sai và các lần luyện tập gần đây.</p>

                <div class="row text-center g-3">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3">
                            <div class="text-muted">Điểm rank</div>
                            <div class="display-6 fw-bold text-warning"><?= (int)$rank; ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3">
                            <div class="text-muted">Tổng câu đúng</div>
                            <div class="display-6 fw-bold text-success"><?= (int)$tongDung; ?></div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-3 mt-md-0">
                        <div class="p-3 bg-light rounded-3">
                            <div class="text-muted">Tổng câu sai</div>
                            <div class="display-6 fw-bold text-danger"><?= (int)$tongSai; ?></div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-3 mt-md-0">
                        <div class="p-3 bg-light rounded-3">
                            <div class="text-muted">Số lần luyện tập</div>
                            <div class="display-6 fw-bold text-primary"><?= count($records); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h2 class="h5 mb-0">Lịch sử làm bài</h2>
            </div>
            <div class="card-body p-0">
                <?php if (empty($records)): ?>
                    <p class="text-muted p-4 mb-0">Bạn chưa có dữ liệu tiến độ. Hãy làm bài trắc nghiệm để ghi nhận kết quả đầu tiên!</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ngày làm</th>
                                    <th>Bài giảng</th>
                                    <th>Câu đúng</th>
                                    <th>Câu sai</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <td><?= h($record['ngay_lam']); ?></td>
                                        <td><?= h($record['ten_baigiang'] ?? 'Bài trắc nghiệm tổng hợp'); ?></td>
                                        <td class="text-success fw-semibold"><?= (int)$record['so_cau_dung']; ?></td>
                                        <td class="text-danger fw-semibold"><?= (int)$record['so_cau_sai']; ?></td>
                                        <td><?= h($record['ghi_chu'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
