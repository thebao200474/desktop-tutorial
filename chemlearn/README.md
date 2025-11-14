# ChemLearn

Dự án web học Hóa học trực tuyến – CT275 Công nghệ Web
Trường Đại học Cần Thơ

## 🎯 Tính năng
- Xem bài học & ví dụ Hóa học
- Bảng tuần hoàn trực quan, tìm kiếm theo tên/ký hiệu
- Làm trắc nghiệm luyện tập và thi thử THPT, hiển thị đáp án
- Ghi tiến độ học, hệ thống điểm rank tăng khi luyện tập/thi đề
- Chatbot Hóa học offline dạng bong bóng trên mọi trang, lưu lịch sử tại trình duyệt
- Trang trí giao diện với icon Hóa học kéo thả

## ⚙️ Công nghệ
PHP 8, MySQL, Bootstrap 5, PDO, Composer Autoload (PSR-4)

## 🚀 Khởi chạy nhanh
1. Sao chép thư mục `chemlearn` vào `htdocs` (XAMPP) hoặc máy chủ PHP phù hợp.
2. Đảm bảo PHP đã cài Composer và cài phụ thuộc bằng `composer install` (đã bao gồm `bramus/router`).
3. Nếu dùng Apache/XAMPP, bật `mod_rewrite` bằng cách mở `xampp\apache\conf\httpd.conf`, bỏ dấu `#` trước dòng `LoadModule rewrite_module modules/mod_rewrite.so`, sau đó khởi động lại Apache.
4. Trỏ DocumentRoot đến thư mục `chemlearn/public` (ví dụ cập nhật VirtualHost trong XAMPP). Nếu không thể thay đổi, có thể truy cập trực tiếp `http://localhost/chemlearn/public/`.
5. Truy cập `http://localhost/chemlearn/` (hoặc `/public/` tùy cấu hình) để kiểm tra router. Các route mẫu:
   - `GET /chemlearn/` → Trang chủ ChemLearn
   - `GET /chemlearn/index.php` → Trang chủ (bổ sung cho XAMPP truy cập trực tiếp)
   - `GET /chemlearn/periodic-table` → Bảng tuần hoàn offline 118 nguyên tố
   - `POST /chemlearn/chatbot/ask` → API chatbot bong bóng (gửi FormData `message`, `csrf`)
6. Cập nhật thông tin kết nối cơ sở dữ liệu trong `config/config.php` hoặc thiết lập biến môi trường `CHEMLEARN_DB_*`.
7. Tạo database `chemlearn` và chạy file `chemlearn.sql` để khởi tạo bảng.

## 🧪 Gợi ý dữ liệu mẫu
- Thêm bài giảng vào bảng `baigiang` để hiển thị ở chuyên đề.
- Cập nhật bảng `cauhoi` với câu hỏi trắc nghiệm và đáp án đúng.
- Lưu ý cột `mota` của bảng `phanung` nên chứa phương trình đã cân bằng (ví dụ: `2H2 + O2 -> 2H2O`).
- Bổ sung dữ liệu bảng `nguyento` nếu muốn hoàn thiện bảng tuần hoàn.
- Import file `database/chatbot.sql` để thêm bảng `faq_hoa` và dữ liệu trả lời mẫu cho chatbot.
- Thêm đề thi mới vào `de_thi` và `cau_hoi_de_thi` để mở rộng ngân hàng đề.

Chúc bạn học tốt cùng ChemLearn! 💙
