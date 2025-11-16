<?php
// View trang chủ sử dụng biến $intro và $featureCards do HomeController truyền xuống.
// Nội dung HTML sẽ được gom vào biến $content thông qua output buffering ở controller.
?>

<!-- Khối tiêu đề trang hiển thị logo, khẩu hiệu và nút bắt đầu học -->
<section class="text-center mb-5">
    <!-- Logo minh họa cho ChemLearn (có thể thay bằng hình thật trong tương lai) -->
    <div class="mb-3">
        <span class="display-4">⚗️</span>
    </div>
    <!-- Tiêu đề chính của trang -->
    <h1 class="fw-bold text-primary">Chào mừng đến với ChemLearn</h1>
    <!-- Đoạn giới thiệu ngắn được escape đảm bảo an toàn -->
    <p class="text-muted lead"><?php echo htmlspecialchars($intro ?? ''); ?></p>
    <!-- Nút kêu gọi hành động đưa người dùng đến module bảng tuần hoàn -->
    <a href="/periodic" class="btn btn-success btn-lg px-4 rounded-pill shadow-sm">Bắt đầu học ngay</a>
</section>

<!-- Khối form tìm kiếm để người dùng tra cứu nội dung hóa học -->
<section class="mb-5">
    <form class="input-group input-group-lg shadow-sm" method="GET" action="/search">
        <!-- Ô nhập từ khóa tìm kiếm -->
        <input type="text" class="form-control" name="q" placeholder="Tìm theo phản ứng, công thức, nguyên tố..." aria-label="Tìm kiếm ChemLearn">
        <!-- Nút kích hoạt tìm kiếm (mặc dù backend chưa xử lý) -->
        <button class="btn btn-primary" type="submit">🔍 Tìm kiếm</button>
    </form>
</section>

<!-- Khối card chức năng giới thiệu các module chính của ChemLearn -->
<section class="mb-5">
    <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
        <?php if (!empty($featureCards) && is_array($featureCards)) : ?>
            <?php foreach ($featureCards as $card) : ?>
                <div class="col">
                    <!-- Card hiển thị thông tin từng module, có hiệu ứng hover -->
                    <div class="card border-0 h-100 shadow-sm feature-card text-center p-4">
                        <div class="fs-1 mb-3"><?php echo htmlspecialchars($card['icon']); ?></div>
                        <h5 class="fw-semibold mb-2"><?php echo htmlspecialchars($card['title']); ?></h5>
                        <p class="text-muted mb-4"><?php echo htmlspecialchars($card['description']); ?></p>
                        <a href="<?php echo htmlspecialchars($card['link']); ?>" class="btn btn-outline-primary rounded-pill">Truy cập</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Script JS nhẹ tạo hiệu ứng đổi màu nền khi hover card -->
<script>
    document.querySelectorAll('.feature-card').forEach((card) => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-4px) scale(1.02)';
            card.style.backgroundColor = '#f8f9ff';
            card.style.boxShadow = '0 1rem 3rem rgba(13, 110, 253, 0.15)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'none';
            card.style.backgroundColor = '#ffffff';
            card.style.boxShadow = '';
        });
    });
</script>
