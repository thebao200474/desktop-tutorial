<?php
// View hiển thị trang Bảng tuần hoàn. Nhận dữ liệu $elements từ controller.
/** @var array $elements */

$slugify = static function (string $value): string {
    $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if ($normalized === false) {
        $normalized = strtr($value, [
            'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
            'đ' => 'd',
            'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
            'À' => 'A', 'Á' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A',
            'Ă' => 'A', 'Ằ' => 'A', 'Ắ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A',
            'Â' => 'A', 'Ầ' => 'A', 'Ấ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A',
            'Đ' => 'D',
            'È' => 'E', 'É' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E',
            'Ê' => 'E', 'Ề' => 'E', 'Ế' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O',
            'Ô' => 'O', 'Ồ' => 'O', 'Ố' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O',
            'Ơ' => 'O', 'Ờ' => 'O', 'Ớ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U',
            'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U',
            'Ỳ' => 'Y', 'Ý' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y',
        ]);
    }
    $normalized = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $normalized));
    $normalized = trim($normalized, '-');

    return $normalized !== '' ? $normalized : 'khong-xac-dinh';
};

$typeFlags = [];
foreach ($elements as $element) {
    if (!empty($element['type']) && is_string($element['type'])) {
        $typeFlags[$element['type']] = true;
    }
}
$types = array_keys($typeFlags);
sort($types, SORT_LOCALE_STRING);

$elementsJson = json_encode(
    $elements,
    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
);
?>

<section class="container my-4">
    <!-- Breadcrumb giúp người dùng định hướng vị trí hiện tại trong website -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Bảng tuần hoàn</li>
        </ol>
    </nav>

    <!-- Tiêu đề và mô tả ngắn về chuyên mục Bảng tuần hoàn -->
    <header class="text-center mb-4">
        <h2 class="fw-bold">
            <i class="fa-solid fa-table me-2"></i>
            Bảng tuần hoàn các nguyên tố hóa học
        </h2>
        <p class="text-muted mb-0">
            Khám phá thông tin 118 nguyên tố: ký hiệu, số hiệu nguyên tử, nguyên tử khối và phân loại nhóm.
        </p>
    </header>

    <div class="periodic-wrapper">
        <!-- Thanh công cụ chứa ô tìm kiếm và bộ lọc nhóm nguyên tố -->
        <form class="row g-3 align-items-center periodic-toolbar mb-4" method="get" onsubmit="return false;">
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label small text-uppercase text-muted" for="element-search">Tìm kiếm</label>
                <input id="element-search" type="search" class="form-control" placeholder="Nhập ký hiệu hoặc tên nguyên tố...">
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label small text-uppercase text-muted" for="element-group">Phân loại</label>
                <select id="element-group" class="form-select">
                    <option value="all">Tất cả nhóm</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?php echo htmlspecialchars($slugify($type), ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-lg-4">
                <div class="periodic-legend">
                    <?php foreach ($types as $type): ?>
                        <span>
                            <span class="legend-sample group-<?php echo htmlspecialchars($slugify($type), ENT_QUOTES); ?>"></span>
                            <?php echo htmlspecialchars($type); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 text-start text-lg-end">
                    <button type="button" id="element-reset" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-rotate-left me-1"></i>
                        Xóa bộ lọc
                    </button>
                </div>
            </div>
        </form>

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <!-- Lưới hiển thị 118 ô nguyên tố, JS sẽ render nội dung -->
                <div id="periodic-grid" class="periodic-grid" role="grid" aria-live="polite"></div>
                <p class="mt-3 text-muted small">
                    * Hàng Lanthanide (57–71) và Actinide (89–103) được tách riêng phía dưới để giữ bố cục chuẩn 18 cột.
                </p>
            </div>
            <div class="col-12 col-xl-4">
                <!-- Bảng thông tin chi tiết cập nhật khi người dùng chọn một nguyên tố -->
                <aside class="periodic-detail h-100" aria-live="polite">
                    <div id="element-detail-placeholder" class="detail-placeholder">
                        <h3 class="mb-2">Chọn một nguyên tố</h3>
                        <p class="mb-0">Bấm vào một ô trong bảng để xem thông tin chi tiết.</p>
                    </div>
                    <div class="d-none" id="element-detail-content">
                        <h3 id="element-detail-name" class="mb-1"></h3>
                        <p class="mb-2">Ký hiệu: <span class="fw-bold" id="element-detail-symbol"></span></p>
                        <p class="mb-2">Nguyên tử khối trung bình: <span id="element-detail-mass"></span></p>
                        <p class="mb-0">Nhóm phân loại: <span id="element-detail-type"></span></p>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>

<script>
// Truyền dữ liệu từ PHP xuống JS thông qua biến toàn cục.
window.CHEMLEARN_ELEMENTS = <?php echo $elementsJson ?: '[]'; ?>;
</script>
