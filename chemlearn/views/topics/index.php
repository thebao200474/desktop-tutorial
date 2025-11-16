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

    <!-- Khối nội dung mô tả sâu từng nhóm chuyên đề và kiến thức liên quan -->
    <section class="mt-5">
        <h3 class="fw-bold mb-3">📚 Tổng hợp 10 chuyên đề trọng tâm</h3>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">1. Cấu tạo nguyên tử</h5>
                        <ul class="text-muted small mb-0">
                            <li>Thành phần proton, neutron, electron; số hiệu Z, số khối A.</li>
                            <li>Cấu hình electron (quy tắc Hund/Aufbau) và ion, đồng vị.</li>
                            <li>Ví dụ: O (Z = 8) &rarr; 1s² 2s² 2p⁴.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">2. Bảng tuần hoàn</h5>
                        <ul class="text-muted small mb-0">
                            <li>Gồm 118 nguyên tố, 7 chu kỳ, 18 nhóm với xu hướng rõ ràng.</li>
                            <li>Bán kính: tăng khi xuống, giảm khi sang phải; độ âm điện ngược lại.</li>
                            <li>Tính kim loại mạnh nhất ở góc trái dưới, phi kim mạnh nhất: F.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">3. Liên kết hóa học</h5>
                        <ul class="text-muted small mb-0">
                            <li>Liên kết ion, cộng hóa trị (phân cực/không phân cực) và kim loại.</li>
                            <li>Lai hóa sp, sp², sp³ để dự đoán hình học phân tử.</li>
                            <li>Ví dụ: NaCl (ion), HCl (cộng hóa trị phân cực).</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">4. Phản ứng &amp; phương trình</h5>
                        <ul class="text-muted small mb-0">
                            <li>Các dạng: thế, cộng, tách, oxi hóa - khử.</li>
                            <li>Áp dụng bảo toàn khối lượng, nguyên tố, electron để cân bằng.</li>
                            <li>Ví dụ: Fe + CuSO₄ &rarr; FeSO₄ + Cu.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">5. Axit – bazơ – muối</h5>
                        <ul class="text-muted small mb-0">
                            <li>Phân loại mạnh/yếu, nhận biết pH bằng chỉ thị.</li>
                            <li>Nghiên cứu độ tan, thủy phân của muối, ứng dụng tính pH.</li>
                            <li>Ví dụ: HCl 0,001 M &rarr; pH = 3.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">6. Oxi hóa – khử</h5>
                        <ul class="text-muted small mb-0">
                            <li>Xác định số oxi hóa, vai trò chất khử/oxi hóa.</li>
                            <li>Cân bằng phản ứng bằng phương pháp ion-electron.</li>
                            <li>Ví dụ: Cu + 2Ag⁺ &rarr; Cu²⁺ + 2Ag.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">7. Dung dịch – nồng độ</h5>
                        <ul class="text-muted small mb-0">
                            <li>Các dạng nồng độ: C%, CM, molan.</li>
                            <li>Bài toán pha loãng, cô đặc và trộn dung dịch (C₁V₁ = C₂V₂).</li>
                            <li>Ứng dụng trong tính lượng chất tham gia phản ứng.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">8. Hóa hữu cơ đại cương</h5>
                        <ul class="text-muted small mb-0">
                            <li>Nghiên cứu đồng phân, danh pháp, gốc hydrocarbon.</li>
                            <li>Hiệu ứng cảm ứng (+I, -I) và liên hợp (+M, -M).</li>
                            <li>Ví dụ: C₄H₁₀ có 2 đồng phân cấu tạo.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">9. Hidrocacbon &amp; dẫn xuất</h5>
                        <ul class="text-muted small mb-0">
                            <li>Ankan, anken, ankin, aren cùng các dẫn xuất (ancol, phenol...).</li>
                            <li>Các dạng phản ứng đặc trưng: thế, cộng, tách, tráng bạc.</li>
                            <li>Ví dụ: CH₃CHO + Ag⁺ &rarr; gương bạc.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">10. Amin – amino axit – protein</h5>
                        <ul class="text-muted small mb-0">
                            <li>Đặc điểm bazơ của amin và phản ứng với axit mạnh.</li>
                            <li>Cấu trúc protein từ bậc 1 đến 4, liên kết peptit.</li>
                            <li>Ví dụ: –NH₂ + –COOH &rarr; –CO–NH– + H₂O.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mục riêng tổng hợp các định luật hóa học cơ bản -->
    <section class="mt-5">
        <h3 class="fw-bold mb-3">⚖️ Định luật hóa học quan trọng</h3>
        <div class="row g-3">
            <?php
            $laws = [
                'Định luật bảo toàn khối lượng' => 'Khối lượng chất tham gia luôn bằng khối lượng sản phẩm.',
                'Định luật bảo toàn nguyên tố' => 'Số nguyên tử của mỗi nguyên tố được giữ nguyên trong phản ứng.',
                'Định luật bảo toàn điện tích' => 'Tổng điện tích dương bằng tổng điện tích âm.',
                'Định luật bảo toàn electron' => 'Số electron cho luôn bằng số electron nhận.',
                'Định luật tuần hoàn Mendeleev' => 'Tính chất nguyên tố biến đổi tuần hoàn theo Z.',
                'Định luật khí lý tưởng' => 'pV = nRT mô tả trạng thái khí lý tưởng.',
                'Định luật Henry' => 'Độ tan của khí tỉ lệ thuận với áp suất riêng phần.',
                'Định luật Beer–Lambert' => 'Độ hấp thụ A = εlc tỉ lệ với nồng độ dung dịch.',
                'Định luật Avogadro' => 'Cùng nhiệt độ và áp suất, thể tích khí bằng nhau chứa số phân tử như nhau.',
                'Định luật Hess' => 'ΔH phản ứng bằng tổng entanpi các bước trung gian.'
            ];
            ?>
            <?php foreach ($laws as $law => $desc) : ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($law); ?></h6>
                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($desc); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Mục riêng cho công thức hóa học thường gặp -->
    <section class="mt-5">
        <h3 class="fw-bold mb-3">🧮 Công thức hóa học cần ghi nhớ</h3>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold">1. Công thức cơ bản</h6>
                        <ul class="text-muted small mb-0">
                            <li>n = m / M, n = V / 22,4 (ở đktc).</li>
                            <li>C% = (m chất tan / m dung dịch) × 100%.</li>
                            <li>CM = n / V; C₁V₁ = C₂V₂.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold">2. pH – pOH</h6>
                        <ul class="text-muted small mb-0">
                            <li>pH = –log[H⁺], pOH = –log[OH⁻].</li>
                            <li>pH + pOH = 14 (ở 25°C).</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold">3. Số oxi hóa – electron</h6>
                        <ul class="text-muted small mb-0">
                            <li>Tổng số oxi hóa của phân tử trung hòa bằng 0.</li>
                            <li>Cân bằng phản ứng dựa trên nguyên tắc e cho = e nhận.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold">4. Công thức hữu cơ</h6>
                        <ul class="text-muted small mb-0">
                            <li>Ankan: C<sub>n</sub>H<sub>2n+2</sub>; anken: C<sub>n</sub>H<sub>2n</sub>.</li>
                            <li>Ankin: C<sub>n</sub>H<sub>2n-2</sub>; aren: C<sub>n</sub>H<sub>2n-6</sub>.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold">5. Phản ứng đặc trưng</h6>
                        <ul class="text-muted small mb-0">
                            <li>Tráng bạc: R–CHO + 2Ag⁺ → R–COO⁻ + 2Ag.</li>
                            <li>Este hóa: Axit + Ancol ⇄ Este + H₂O.</li>
                            <li>Xà phòng hóa: Este + NaOH → Muối + Ancol.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold">6. Điện hóa &amp; nhiệt hóa</h6>
                        <ul class="text-muted small mb-0">
                            <li>Điện phân: m = (A · I · t) / (nF).</li>
                            <li>Liên hệ năng lượng: ΔG = –nFE.</li>
                            <li>Nhiệt hóa: ΔH = ΣH(sản phẩm) – ΣH(tham gia).</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>
