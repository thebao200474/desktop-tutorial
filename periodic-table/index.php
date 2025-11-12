<?php
// Tệp giao diện chính của dự án Bảng tuần hoàn
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bảng tuần hoàn - ChemLearn</title>
    <!-- Liên kết font chữ Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Liên kết stylesheet chính -->
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <!-- Bao bọc toàn trang -->
    <div class="page">
        <!-- Thanh công cụ tìm kiếm và lọc -->
        <header class="toolbar">
            <div class="toolbar__group">
                <label for="search" class="sr-only">Tìm kiếm</label>
                <input id="search" type="search" placeholder="Tìm ký hiệu hoặc tên nguyên tố..." />
            </div>
            <div class="toolbar__group">
                <label for="category" class="sr-only">Lọc loại nguyên tố</label>
                <select id="category">
                    <option value="">Tất cả loại nguyên tố</option>
                </select>
            </div>
            <div class="toolbar__group">
                <button id="toggle-language" type="button" aria-pressed="false">VI / EN</button>
            </div>
        </header>

        <!-- Khối chú giải màu sắc -->
        <section id="legend" aria-label="Chú giải loại nguyên tố"></section>

        <main class="content">
            <!-- Khu vực bảng tuần hoàn -->
            <section class="table-wrapper">
                <div id="periodic-table" role="grid" aria-label="Bảng tuần hoàn các nguyên tố"></div>
            </section>
            <!-- Panel chi tiết -->
            <aside id="detail-panel" aria-live="polite" aria-hidden="true" tabindex="-1">
                <button id="close-panel" type="button" aria-label="Đóng chi tiết">×</button>
                <div class="detail__content">
                    <div class="detail__image-wrapper">
                        <img id="detail-image" src="" alt="" />
                    </div>
                    <div class="detail__text">
                        <h2 id="detail-name"></h2>
                        <p id="detail-symbol"></p>
                        <p id="detail-number"></p>
                        <p id="detail-weight"></p>
                        <p id="detail-config"></p>
                        <p id="detail-description"></p>
                        <div id="detail-applications"></div>
                    </div>
                </div>
            </aside>
        </main>

        <!-- Lớp phủ cho di động -->
        <div id="panel-overlay" hidden></div>
    </div>

    <!-- Script chính -->
    <script src="app.js"></script>
</body>
</html>
