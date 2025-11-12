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
2. Đảm bảo PHP đã cài Composer và cài phụ thuộc bằng `composer install` (đã bao gồm `bramus/router`).
3. Nếu dùng Apache/XAMPP, bật `mod_rewrite` bằng cách mở `xampp\apache\conf\httpd.conf`, bỏ dấu `#` trước dòng `LoadModule rewrite_module modules/mod_rewrite.so`, sau đó khởi động lại Apache.
4. Trỏ DocumentRoot đến thư mục `chemlearn/public` (ví dụ cập nhật VirtualHost trong XAMPP). Nếu không thể thay đổi, có thể truy cập trực tiếp `http://localhost/chemlearn/public/`.
5. Truy cập `http://localhost/chemlearn/` (hoặc `/public/` tùy cấu hình) để kiểm tra router. Các route mẫu:
   - `GET /chemlearn/` → Trang chủ kiểm tra router
   - `GET /chemlearn/health` → kiểm tra nhanh tình trạng server (`OK`)
   - `GET /chemlearn/periodic-table` → trang bảng tuần hoàn demo
   - `POST /chemlearn/ai/ask` với JSON `{ "question": "Liên kết ion là gì?" }`
6. Cập nhật thông tin kết nối cơ sở dữ liệu trong `config/config.php` hoặc thiết lập biến môi trường `CHEMLEARN_DB_*`.
7. Tạo database `chemlearn` và chạy file `chemlearn.sql` để khởi tạo bảng.

## 🧪 Gợi ý dữ liệu mẫu
- Thêm bài giảng vào bảng `baigiang` để hiển thị ở chuyên đề.
- Cập nhật bảng `cauhoi` với câu hỏi trắc nghiệm và đáp án đúng.
- Lưu ý cột `mota` của bảng `phanung` nên chứa phương trình đã cân bằng (ví dụ: `2H2 + O2 -> 2H2O`).
- Bổ sung dữ liệu bảng `nguyento` nếu muốn hoàn thiện bảng tuần hoàn.
- Thêm đề thi mới vào `de_thi` và `cau_hoi_de_thi` để mở rộng ngân hàng đề.

Chúc bạn học tốt cùng ChemLearn! 💙
