# Hệ thống Quản Lý Mượn Sách (Nâng cao)

Dự án demo full-stack theo yêu cầu:

- Giao diện người dùng + quản trị dùng **Bootstrap 5**.
- **Tìm kiếm giọng nói** với Web Speech API.
- **AI Chatbot** hỗ trợ gợi ý, trả lời câu hỏi nhanh.
- **Đăng nhập/đăng ký OTP qua Brevo** bằng API key.
- **Tách riêng trang đăng nhập và đăng ký** để UX gọn gàng, dễ dùng.
- **Dashboard quản trị** + quản lý sách/độc giả/lượt mượn.
- **Trang chi tiết sách** có đánh giá sao, nhận xét và nút mượn sách.

## Công nghệ

- Node.js + Express
- SQLite (better-sqlite3)
- JWT
- Brevo Transactional Email API (`sib-api-v3-sdk`)

## Cơ sở dữ liệu

Các bảng chính theo đề bài:

- `Docgia(MaDocGia, HoLot, Ten, NgaySinh, Phai, DiaChi, DienThoai, Email, Password)`
- `Sach(MaSach, TenSach, DonGia, SoQuyen, NamXuatBan, MaNXB, NguonGoc, MoTa, TheLoai, AnhBia)`
- `NhaXuatBan(MaNXB, TenNXB, DiaChi)`
- `TheoDoiMuonSach(MaDocGia, MaSach, NgayMuon, NgayTra)` (mở rộng thêm `HanTra`, `TrangThai`, `MaMuon`)
- `NhanVien(MSNV, HoTenNV, Password, ChucVu, DiaChi, SoDienThoai, Email)`

Bảng bổ sung:

- `OTPToken`: lưu OTP đăng nhập tạm thời.
- `DanhGiaSach`: lưu đánh giá sách.

## Chạy dự án

```bash
npm install
npm start
```

Mở:

- Trang người dùng: `http://localhost:3000/`
- Trang khám phá sách: `http://localhost:3000/explore.html`
- Trang giá sách cá nhân: `http://localhost:3000/my-library.html`
- Trang đăng nhập: `http://localhost:3000/login.html`
- Trang đăng ký: `http://localhost:3000/register.html`
- Trang chi tiết sách: `http://localhost:3000/book-detail.html?id=S001`
- Trang đăng nhập quản trị: `http://localhost:3000/admin-login.html`
- Trang quản trị: `http://localhost:3000/admin.html` (yêu cầu đăng nhập admin)

## Tài khoản mẫu (seed)

- Độc giả: `docgia1@example.com` / `reader123`
- Admin: `admin@example.com`

## Cấu hình Brevo OTP (tuỳ chọn)

Tạo `.env`:

```bash
PORT=3000
JWT_SECRET=__BAT_BUOC_TU_DIEN__
BREVO_API_KEY=__BAT_BUOC_TU_DIEN__
BREVO_SENDER_EMAIL=__BAT_BUOC_TU_DIEN__
BREVO_SENDER_NAME=BookHub  # optional, mặc định BookHub
OTP_EXPIRE_MINUTES=5
```

Nếu thiếu `BREVO_API_KEY` hoặc `BREVO_SENDER_EMAIL`, API gửi OTP sẽ trả lỗi rõ ràng để tránh báo thành công giả.



## ⚠️ Chỗ bạn PHẢI điền thủ công (in đỏ)

- <span style="color:red"><b>JWT_SECRET=... (bắt buộc)</b></span>
- <span style="color:red"><b>BREVO_API_KEY=... (bắt buộc)</b></span>
- <span style="color:red"><b>BREVO_SENDER_EMAIL=... (bắt buộc, email đã verify trên Brevo)</b></span>
- <span style="color:#b45309"><b>BREVO_SENDER_NAME=... (tùy chọn, mặc định BookHub)</b></span>
- <span style="color:#b45309"><b>TEST_RECEIVER_EMAIL=... (tùy chọn cho route test)</b></span>

## API đăng ký OTP

- `POST /api/auth/send-otp-register`: gửi OTP đăng ký qua Brevo; nếu thiếu cấu hình sẽ trả lỗi rõ ràng.
- `POST /api/auth/register`: đăng ký độc giả với OTP + thông tin bảng `Docgia`.


## API test gửi mail

- `GET /api/test-send-mail?email=your_email@gmail.com`: gửi mail test nhanh qua Brevo.
- Hoặc đặt `TEST_RECEIVER_EMAIL` trong `.env` rồi gọi `GET /api/test-send-mail`.


## Xử lý lỗi OTP thường gặp

- Nếu thấy lỗi `Unauthorized` hoặc `Key not found`:
  - API key Brevo sai/hết hạn. Tạo key mới ở Brevo rồi cập nhật `BREVO_API_KEY` trong `.env`.
- Nếu lỗi liên quan `sender`:
  - `BREVO_SENDER_EMAIL` chưa verify. Vào Brevo > Senders để verify email gửi.
- Sau khi sửa `.env`, nhớ restart server (`Ctrl + C` rồi `npm start`).


- `POST /api/admin/login`: đăng nhập quản trị viên bằng email + mật khẩu.


## Thông tin đăng nhập admin (bạn tự điền)

- Email admin mặc định: <span style="color:red"><b>admin@gmail.com</b></span>
- Mật khẩu admin mặc định: <span style="color:red"><b>admin</b></span>

> Bạn tự thay 2 giá trị này theo tài khoản admin bạn đặt.


- Có thể đổi mặc định bằng `.env`: `ADMIN_DEFAULT_EMAIL`, `ADMIN_DEFAULT_PASSWORD` (server sẽ đồng bộ tài khoản admin mặc định khi khởi động).
