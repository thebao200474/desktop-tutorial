<?php

namespace ChemLearn\Controllers;

/**
 * HomeController chịu trách nhiệm xử lý yêu cầu đến trang chủ ChemLearn.
 */
class HomeController extends BaseController
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

        // Sử dụng tiện ích view() của BaseController để render view và layout.
        $this->view('home/index', [
            'pageTitle' => $pageTitle,
            'intro' => $intro,
            'featureCards' => $featureCards,
        ]);
    }
}
