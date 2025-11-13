<?php
// Layout mặc định chịu trách nhiệm bọc nội dung từng trang trong khung HTML chung.
// Biến $pageTitle và $content sẽ được controller truyền xuống trước khi nạp layout này.
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <!-- Thiết lập mã hóa và meta viewport để trang responsive trên mọi thiết bị -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tiêu đề trang được escape để đảm bảo an toàn XSS -->
    <title><?php echo htmlspecialchars($pageTitle ?? 'ChemLearn'); ?></title>
    <!-- Nạp font chữ Roboto từ Google Fonts để sử dụng cho toàn bộ giao diện -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Nạp Bootstrap 5.3 qua CDN để dùng các thành phần giao diện có sẵn -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Nạp Font Awesome để sử dụng icon trong toàn bộ giao diện -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Tùy chỉnh CSS nhẹ giúp giao diện sáng sủa, có hiệu ứng hover -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(180deg, #f5f9ff 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-brand {
            font-weight: 700;
            color: #0d6efd !important;
        }
        .nav-link {
            color: #0d6efd !important;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .nav-link:hover {
            color: #0b5ed7 !important;
        }
        main {
            flex: 1 0 auto;
            padding-top: 5rem;
            padding-bottom: 3rem;
        }
        footer {
            flex-shrink: 0;
            background-color: #0d6efd;
            color: #ffffff;
        }
        .feature-card {
            border-radius: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
        }
        .breadcrumb a {
            color: #0d6efd;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .card.shadow-sm {
            border-radius: 0.85rem;
        }
    </style>
</head>
<body>
    <!-- Navbar sticky-top hiển thị logo ChemLearn và các liên kết điều hướng chính -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <!-- Logo/Brand của website -->
            <a class="navbar-brand" href="/">ChemLearn</a>
            <!-- Nút toggle hiển thị menu trên thiết bị nhỏ -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Danh sách liên kết điều hướng chính -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="/periodic">Bảng tuần hoàn</a></li>
                    <li class="nav-item"><a class="nav-link" href="/equations">Phản ứng hóa học</a></li>
                    <li class="nav-item"><a class="nav-link" href="/formulas">Công thức &amp; Định luật</a></li>
                    <li class="nav-item"><a class="nav-link" href="/qa">Hỏi đáp</a></li>
                    <li class="nav-item"><a class="nav-link" href="/quiz">Trắc nghiệm</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ranking">Bảng xếp hạng</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Khu vực nội dung chính, controller sẽ truyền biến $content vào để render -->
    <main>
        <div class="container">
            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <!-- Footer hiển thị thông tin bản quyền cuối trang -->
    <footer class="py-3">
        <div class="container text-center small">
            &copy; <?php echo htmlspecialchars('2025 ChemLearn | Trương Nguyễn Thế Bảo'); ?>
        </div>
    </footer>

    <!-- Nạp JS của Bootstrap để hỗ trợ các component tương tác -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
