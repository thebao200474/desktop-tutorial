<?php

namespace ChemLearn\Controllers;

/**
 * HomeController chịu trách nhiệm xử lý yêu cầu đến trang chủ ChemLearn.
 */
class HomeController
{
    /**
     * Phương thức index() chuẩn bị dữ liệu và render giao diện trang chủ.
     */
    public function index(): void
    {
        // Khai báo tiêu đề trang để truyền xuống layout.
        $pageTitle = 'ChemLearn - Học Hóa học Trực tuyến';

        // Nội dung giới thiệu ngắn gọn về dự án để hiển thị ở phần intro.
        $intro = 'ChemLearn giúp bạn học Hóa học dễ dàng hơn qua bảng tuần hoàn, phản ứng, công thức, trắc nghiệm và hỏi đáp.';

        // Danh sách 6 thẻ chức năng hiển thị trên trang chủ (icon, tiêu đề, mô tả, liên kết).
        $featureCards = [
            [
                'icon' => '⚗️',
                'title' => 'Bảng tuần hoàn',
                'description' => 'Khám phá đầy đủ thông tin về các nguyên tố hóa học.',
                'link' => '/periodic',
            ],
            [
                'icon' => '🧪',
                'title' => 'Phản ứng hóa học',
                'description' => 'Tra cứu và cân bằng các phương trình phản ứng.',
                'link' => '/equations',
            ],
            [
                'icon' => '📘',
                'title' => 'Công thức & Định luật',
                'description' => 'Ôn tập nhanh những công thức và định luật quan trọng.',
                'link' => '/formulas',
            ],
            [
                'icon' => '💬',
                'title' => 'Hỏi đáp',
                'description' => 'Đặt câu hỏi và trao đổi kiến thức cùng cộng đồng.',
                'link' => '/qa',
            ],
            [
                'icon' => '🧩',
                'title' => 'Trắc nghiệm',
                'description' => 'Làm bài kiểm tra trắc nghiệm để củng cố kiến thức.',
                'link' => '/quiz',
            ],
            [
                'icon' => '📈',
                'title' => 'Bảng xếp hạng',
                'description' => 'Theo dõi thành tích học tập và cạnh tranh cùng bạn bè.',
                'link' => '/ranking',
            ],
        ];

        // Xác định đường dẫn tuyệt đối đến file view để nạp giao diện trang chủ.
        $viewPath = __DIR__ . '/../views/home/index.php';

        // Bắt đầu bộ đệm đầu ra để lấy nội dung HTML của view.
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // Xác định đường dẫn đến layout để bọc nội dung trang chủ trong khung giao diện chung.
        $layoutPath = __DIR__ . '/../views/layouts/default.php';

        // Nạp layout và hiển thị toàn bộ trang cho người dùng.
        require $layoutPath;
    }
}
