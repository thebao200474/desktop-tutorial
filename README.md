# Hệ thống Quản Lý Mượn Sách (Nâng cao)

Dự án demo full-stack theo yêu cầu:

- Giao diện người dùng + quản trị dùng **Bootstrap 5**.
- **Tìm kiếm giọng nói** với Web Speech API.
- **AI Chatbot** (luật đơn giản, có thể mở rộng Dialogflow/Rasa).
- **Đăng nhập OTP qua Gmail** bằng Nodemailer.
- **Dashboard quản trị** + quản lý sách/độc giả/lượt mượn.

## Công nghệ

- Node.js + Express
- SQLite (better-sqlite3)
- JWT
- Nodemailer

## Cơ sở dữ liệu

Các bảng chính theo đề bài:

- `Docgia(MaDocGia, HoLot, Ten, NgaySinh, Phai, DiaChi, DienThoai, Email)`
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
- Trang quản trị: `http://localhost:3000/admin.html`

## Tài khoản mẫu (seed)

- Độc giả: `docgia1@example.com`
- Admin: `admin@example.com`

## Cấu hình Gmail OTP (tuỳ chọn)

Tạo `.env`:

```bash
PORT=3000
JWT_SECRET=replace_me
GMAIL_USER=your_email@gmail.com
GMAIL_APP_PASSWORD=your_gmail_app_password
```

Nếu chưa cấu hình Gmail, hệ thống chạy ở chế độ demo mail nội bộ (không gửi ra ngoài).
