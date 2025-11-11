# ChemLearn

Dự án web học Hóa học trực tuyến – CT275 Công nghệ Web  
Trường Đại học Cần Thơ

## 🎯 Tính năng
- Xem bài học & ví dụ Hóa học
- Bảng tuần hoàn trực quan, tìm kiếm theo tên/ký hiệu
- Làm trắc nghiệm luyện tập và thi thử THPT, hiển thị đáp án
- Ghi tiến độ học, hệ thống điểm rank tăng khi luyện tập/thi đề
- Hỏi đáp AI (mô phỏng) và chatbot ChemTutor ngay trên trang chủ
- Trang trí giao diện với icon Hóa học kéo thả

## ⚙️ Công nghệ
PHP 8, MySQL, Bootstrap 5, PDO, Composer Autoload (PSR-4)

## 🚀 Khởi chạy nhanh
1. Sao chép thư mục `chemlearn` vào `htdocs` (XAMPP) hoặc máy chủ PHP phù hợp.
2. Cập nhật thông tin kết nối cơ sở dữ liệu trong `config/config.php` hoặc thiết lập biến môi trường `CHEMLEARN_DB_*`.
3. Tạo database `chemlearn` và chạy file `chemlearn.sql` để khởi tạo bảng.
4. Truy cập `http://localhost/chemlearn/` để trải nghiệm trang web.

## 🧪 Gợi ý dữ liệu mẫu
- Thêm bài giảng vào bảng `baigiang` để hiển thị ở chuyên đề.
- Cập nhật bảng `cauhoi` với câu hỏi trắc nghiệm và đáp án đúng.
- Lưu ý cột `mota` của bảng `phanung` nên chứa phương trình đã cân bằng (ví dụ: `2H2 + O2 -> 2H2O`).
- Bổ sung dữ liệu bảng `nguyento` nếu muốn hoàn thiện bảng tuần hoàn.
- Thêm đề thi mới vào `de_thi` và `cau_hoi_de_thi` để mở rộng ngân hàng đề.

Chúc bạn học tốt cùng ChemLearn! 💙
