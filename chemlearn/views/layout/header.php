<?php
use function htmlspecialchars as h;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? h($title) : 'ChemLearn'; ?></title>
    <?php $csrfToken = h($_SESSION['csrf_token'] ?? $_SESSION['csrf'] ?? ''); ?>
    <meta name="csrf-token" content="<?= $csrfToken; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?= asset_url('css/periodic-table.css'); ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?= app_url(); ?>">
            <span>⚗️ ChemLearn</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= app_url(); ?>">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= app_url('chuyende.php'); ?>">Chuyên đề</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= app_url('phuongtrinh'); ?>">Phương trình</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= app_url('hoi-dap'); ?>">Hỏi đáp</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= app_url('periodic-table'); ?>">Bảng tuần hoàn</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-open-chat>Chatbot Hóa học</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= app_url('de_thi.php'); ?>">Đề thi</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (!empty($currentUser)): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="accountMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= h($currentUser['hoten'] ?? $currentUser['tendangnhap']); ?>
                            <span class="badge text-bg-warning ms-2">Rank <?= (int)($currentUser['diem_rank'] ?? 0); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountMenu">
                            <li><a class="dropdown-item" href="<?= app_url('tien_do.php'); ?>">Tiến độ học</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= app_url('dangxuat.php'); ?>">Đăng xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= app_url('dangnhap.php'); ?>">Đăng nhập</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= app_url('dangky.php'); ?>">Đăng ký</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-5">
    <?php if (!empty($_SESSION['flash_message'])): ?>
        <div class="alert alert-info alert-custom shadow-sm" role="alert">
            <?= h($_SESSION['flash_message']); ?>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>
