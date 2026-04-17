require('dotenv').config();
const fs = require('fs');
const path = require('path');
const express = require('express');
const Database = require('better-sqlite3');
const nodemailer = require('nodemailer');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');

const app = express();
const PORT = process.env.PORT || 3000;
const JWT_SECRET = process.env.JWT_SECRET || 'dev-secret-key';

app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

const dbPath = path.join(__dirname, 'db', 'library.db');
const db = new Database(dbPath);

db.pragma('journal_mode = WAL');
db.exec(fs.readFileSync(path.join(__dirname, 'db', 'init.sql'), 'utf-8'));

function ensureDocgiaPasswordColumn() {
  const cols = db.prepare("PRAGMA table_info(Docgia)").all();
  const hasPassword = cols.some((x) => x.name === 'Password');
  if (!hasPassword) {
    db.exec('ALTER TABLE Docgia ADD COLUMN Password TEXT');
  }
}

ensureDocgiaPasswordColumn();

function seedData() {
  const hasPublisher = db.prepare('SELECT COUNT(*) AS count FROM NhaXuatBan').get().count;
  if (hasPublisher > 0) return;

  const adminPassword = bcrypt.hashSync('admin123', 10);
  const readerPassword = bcrypt.hashSync('reader123', 10);

  db.prepare('INSERT INTO NhaXuatBan(MaNXB, TenNXB, DiaChi) VALUES (?, ?, ?)').run('NXB01', 'NXB Tre', 'TP.HCM');
  db.prepare('INSERT INTO NhaXuatBan(MaNXB, TenNXB, DiaChi) VALUES (?, ?, ?)').run('NXB02', 'Nha Nam', 'Ha Noi');

  const books = [
    ['S001', 'Đắc nhân tâm', 90000, 8, 2019, 'NXB01', 'Dale Carnegie', 'Sách kỹ năng sống kinh điển.', 'Kỹ năng sống', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400'],
    ['S002', 'Nhà giả kim', 79000, 6, 2021, 'NXB02', 'Paulo Coelho', 'Tiểu thuyết truyền cảm hứng nổi tiếng.', 'Tiểu thuyết', 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=400'],
    ['S003', 'Tư duy nhanh và chậm', 120000, 4, 2020, 'NXB01', 'Daniel Kahneman', 'Phân tích tâm lý học hành vi và ra quyết định.', 'Khoa học', 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=400']
  ];

  const insertBook = db.prepare(`
    INSERT INTO Sach(MaSach, TenSach, DonGia, SoQuyen, NamXuatBan, MaNXB, NguonGoc, MoTa, TheLoai, AnhBia)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  `);

  books.forEach((book) => insertBook.run(...book));

  db.prepare(`
    INSERT INTO Docgia(MaDocGia, HoLot, Ten, NgaySinh, Phai, DiaChi, DienThoai, Email, Password)
    VALUES ('DG001', 'Nguyen Van', 'An', '2001-05-10', 'Nam', 'Can Tho', '0900000001', 'docgia1@example.com', ?)
  `).run(readerPassword);

  db.prepare(`
    INSERT INTO NhanVien(MSNV, HoTenNV, Password, ChucVu, DiaChi, SoDienThoai, Email)
    VALUES ('NV001', 'Tran Thi Admin', ?, 'Quan tri', 'TP.HCM', '0900000099', 'admin@example.com')
  `).run(adminPassword);
}

seedData();

function authMiddleware(req, res, next) {
  const header = req.headers.authorization;
  if (!header?.startsWith('Bearer ')) {
    return res.status(401).json({ message: 'Thiếu token.' });
  }

  try {
    const token = header.replace('Bearer ', '');
    req.user = jwt.verify(token, JWT_SECRET);
    return next();
  } catch (error) {
    return res.status(401).json({ message: 'Token không hợp lệ.' });
  }
}

function adminOnly(req, res, next) {
  if (req.user.role !== 'admin') {
    return res.status(403).json({ message: 'Chỉ quản trị viên được truy cập.' });
  }
  return next();
}

function buildMailer() {
  if (process.env.GMAIL_USER && process.env.GMAIL_APP_PASSWORD) {
    return nodemailer.createTransport({
      service: 'gmail',
      auth: {
        user: process.env.GMAIL_USER,
        pass: process.env.GMAIL_APP_PASSWORD
      }
    });
  }

  return nodemailer.createTransport({
    streamTransport: true,
    newline: 'unix',
    buffer: true
  });
}

function generateDocGiaId() {
  const last = db.prepare("SELECT MaDocGia FROM Docgia ORDER BY MaDocGia DESC LIMIT 1").get();
  const num = last ? Number(String(last.MaDocGia).replace('DG', '')) + 1 : 1;
  return `DG${String(num).padStart(3, '0')}`;
}

app.post('/api/auth/register', (req, res) => {
  const { HoLot = '', Ten = '', Email, Password, DienThoai = '', DiaChi = '', NgaySinh = null, Phai = null } = req.body;
  if (!Email || !Password || !Ten) {
    return res.status(400).json({ message: 'Tên, email và mật khẩu là bắt buộc.' });
  }

  const exists = db.prepare('SELECT 1 FROM Docgia WHERE Email = ?').get(Email);
  if (exists) {
    return res.status(409).json({ message: 'Email đã tồn tại.' });
  }

  const MaDocGia = generateDocGiaId();
  const passwordHash = bcrypt.hashSync(Password, 10);
  db.prepare(
    `INSERT INTO Docgia(MaDocGia, HoLot, Ten, NgaySinh, Phai, DiaChi, DienThoai, Email, Password)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`
  ).run(MaDocGia, HoLot, Ten, NgaySinh, Phai, DiaChi, DienThoai, Email, passwordHash);

  return res.json({ message: 'Đăng ký thành công. Vui lòng đăng nhập.' });
});

app.post('/api/auth/login', (req, res) => {
  const { email, password } = req.body;
  const user = db.prepare('SELECT MaDocGia, HoLot, Ten, Email, Password FROM Docgia WHERE Email = ?').get(email);
  if (!user || !user.Password || !bcrypt.compareSync(password, user.Password)) {
    return res.status(401).json({ message: 'Email hoặc mật khẩu không đúng.' });
  }

  const token = jwt.sign({ sub: user.MaDocGia, role: 'reader', email: user.Email }, JWT_SECRET, { expiresIn: '8h' });
  return res.json({
    message: 'Đăng nhập thành công.',
    token,
    profile: { id: user.MaDocGia, name: `${user.HoLot} ${user.Ten}`.trim(), Email: user.Email, role: 'reader' }
  });
});

app.post('/api/auth/send-otp', (req, res) => {
  const { email, role = 'reader' } = req.body;
  if (!email) {
    return res.status(400).json({ message: 'Email là bắt buộc.' });
  }

  const userExists =
    role === 'admin'
      ? db.prepare('SELECT 1 FROM NhanVien WHERE Email = ?').get(email)
      : db.prepare('SELECT 1 FROM Docgia WHERE Email = ?').get(email);

  if (!userExists) {
    return res.status(404).json({ message: 'Không tìm thấy tài khoản với email này.' });
  }

  const otp = `${Math.floor(100000 + Math.random() * 900000)}`;
  const expiresAt = Date.now() + 5 * 60 * 1000;

  db.prepare('DELETE FROM OTPToken WHERE email = ? AND role = ?').run(email, role);
  db.prepare('INSERT INTO OTPToken(email, otp, role, expiresAt) VALUES (?, ?, ?, ?)').run(email, otp, role, expiresAt);

  const transporter = buildMailer();
  transporter.sendMail(
    {
      from: process.env.GMAIL_USER || 'no-reply@bookhub.local',
      to: email,
      subject: 'Mã OTP đăng nhập BookHub',
      text: `Mã OTP của bạn là ${otp}. Mã có hiệu lực trong 5 phút.`
    },
    (error, info) => {
      if (error) {
        return res.status(500).json({ message: 'Gửi OTP thất bại.', error: error.message });
      }

      return res.json({
        message: 'Đã gửi OTP thành công.',
        mode: process.env.GMAIL_USER ? 'gmail' : 'demo-console',
        preview: process.env.GMAIL_USER ? undefined : info.message.toString()
      });
    }
  );
});

app.post('/api/auth/verify-otp', (req, res) => {
  const { email, otp, role = 'reader' } = req.body;
  const tokenRow = db
    .prepare('SELECT * FROM OTPToken WHERE email = ? AND role = ? ORDER BY id DESC LIMIT 1')
    .get(email, role);

  if (!tokenRow) {
    return res.status(400).json({ message: 'Không tìm thấy OTP, vui lòng yêu cầu lại.' });
  }

  if (tokenRow.otp !== otp || tokenRow.expiresAt < Date.now()) {
    return res.status(400).json({ message: 'OTP không đúng hoặc đã hết hạn.' });
  }

  const profile =
    role === 'admin'
      ? db.prepare('SELECT MSNV AS id, HoTenNV AS name, Email FROM NhanVien WHERE Email = ?').get(email)
      : db.prepare("SELECT MaDocGia AS id, HoLot || ' ' || Ten AS name, Email FROM Docgia WHERE Email = ?").get(email);

  db.prepare('DELETE FROM OTPToken WHERE id = ?').run(tokenRow.id);

  const token = jwt.sign({ sub: profile.id, role, email }, JWT_SECRET, { expiresIn: '8h' });
  return res.json({ message: 'Xác thực thành công.', token, profile: { ...profile, role } });
});

app.get('/api/search-books', (req, res) => {
  const q = req.query.q || '';
  const books = db
    .prepare(
      `SELECT s.*, n.TenNXB
       FROM Sach s
       LEFT JOIN NhaXuatBan n ON n.MaNXB = s.MaNXB
       WHERE s.TenSach LIKE ? OR s.NguonGoc LIKE ? OR s.TheLoai LIKE ?
       ORDER BY s.TenSach`
    )
    .all(`%${q}%`, `%${q}%`, `%${q}%`);
  res.json({ books });
});


app.get('/api/books', (req, res) => {
  const {
    search = '',
    genre = 'all',
    author = 'all',
    minRating = 0,
    yearFrom = 0,
    sort = 'newest',
    page = 1,
    pageSize = 10
  } = req.query;

  const filters = ['1=1'];
  const params = [];

  if (search) {
    filters.push('(s.TenSach LIKE ? OR s.NguonGoc LIKE ? OR s.TheLoai LIKE ?)');
    params.push(`%${search}%`, `%${search}%`, `%${search}%`);
  }

  if (genre !== 'all') {
    filters.push('s.TheLoai = ?');
    params.push(genre);
  }

  if (author !== 'all') {
    filters.push('s.NguonGoc = ?');
    params.push(author);
  }

  if (Number(yearFrom) > 0) {
    filters.push('s.NamXuatBan >= ?');
    params.push(Number(yearFrom));
  }

  if (Number(minRating) > 0) {
    filters.push('COALESCE(avgRating, 0) >= ?');
    params.push(Number(minRating));
  }

  const sortMap = {
    newest: 's.NamXuatBan DESC',
    featured: 's.SoQuyen DESC',
    rating: 'avgRating DESC',
    priceAsc: 's.DonGia ASC',
    priceDesc: 's.DonGia DESC'
  };

  const orderBy = sortMap[sort] || sortMap.newest;
  const limit = Math.max(1, Number(pageSize) || 10);
  const offset = (Math.max(1, Number(page) || 1) - 1) * limit;

  const baseQuery = `
    FROM (
      SELECT s.*,
             COALESCE(AVG(r.Diem), 0) AS avgRating,
             COUNT(r.id) AS ratingCount
      FROM Sach s
      LEFT JOIN DanhGiaSach r ON r.MaSach = s.MaSach
      GROUP BY s.MaSach
    ) s
    WHERE ${filters.join(' AND ')}
  `;

  const countRow = db.prepare(`SELECT COUNT(*) AS total ${baseQuery}`).get(...params);
  const books = db
    .prepare(`
      SELECT s.*
      ${baseQuery}
      ORDER BY ${orderBy}
      LIMIT ? OFFSET ?
    `)
    .all(...params, limit, offset);

  res.json({
    books,
    pagination: {
      total: countRow.total,
      page: Math.max(1, Number(page) || 1),
      pageSize: limit,
      totalPages: Math.ceil(countRow.total / limit)
    }
  });
});

app.get('/api/books/:id', (req, res) => {
  const book = db
    .prepare(
      `SELECT s.*, n.TenNXB,
              COALESCE(AVG(r.Diem), 0) AS avgRating,
              COUNT(r.id) AS totalReviews
       FROM Sach s
       LEFT JOIN NhaXuatBan n ON n.MaNXB = s.MaNXB
       LEFT JOIN DanhGiaSach r ON r.MaSach = s.MaSach
       WHERE s.MaSach = ?`
    )
    .get(req.params.id);

  if (!book) return res.status(404).json({ message: 'Không tìm thấy sách.' });
  return res.json({ book });
});

app.get('/api/books/:id/reviews', (req, res) => {
  const reviews = db
    .prepare(
      `SELECT r.*, d.HoLot || ' ' || d.Ten AS DocGia
       FROM DanhGiaSach r
       LEFT JOIN Docgia d ON d.MaDocGia = r.MaDocGia
       WHERE r.MaSach = ?
       ORDER BY r.id DESC`
    )
    .all(req.params.id);
  res.json({ reviews });
});

app.post('/api/books/:id/reviews', authMiddleware, (req, res) => {
  const { Diem, BinhLuan = '' } = req.body;
  if (!Diem || Diem < 1 || Diem > 5) {
    return res.status(400).json({ message: 'Điểm đánh giá phải từ 1-5.' });
  }

  db.prepare('INSERT INTO DanhGiaSach(MaDocGia, MaSach, Diem, BinhLuan) VALUES (?, ?, ?, ?)').run(req.user.sub, req.params.id, Diem, BinhLuan);
  return res.json({ message: 'Đã gửi đánh giá.' });
});

app.post('/api/borrow-book', authMiddleware, (req, res) => {
  const { MaSach } = req.body;
  const MaDocGia = req.user.sub;

  const book = db.prepare('SELECT * FROM Sach WHERE MaSach = ?').get(MaSach);
  if (!book || book.SoQuyen <= 0) {
    return res.status(400).json({ message: 'Sách không còn sẵn để mượn.' });
  }

  const now = new Date();
  const dueDate = new Date(now);
  dueDate.setDate(dueDate.getDate() + 14);

  const borrowTx = db.transaction(() => {
    db.prepare('UPDATE Sach SET SoQuyen = SoQuyen - 1 WHERE MaSach = ?').run(MaSach);
    db.prepare(
      `INSERT INTO TheoDoiMuonSach(MaDocGia, MaSach, NgayMuon, HanTra, TrangThai)
       VALUES (?, ?, ?, ?, 'dang_muon')`
    ).run(MaDocGia, MaSach, now.toISOString(), dueDate.toISOString());
  });

  borrowTx();
  return res.json({ message: 'Mượn sách thành công.', dueDate });
});

app.get('/api/my-loans', authMiddleware, (req, res) => {
  const loans = db
    .prepare(
      `SELECT l.*, s.TenSach, s.AnhBia
       FROM TheoDoiMuonSach l
       JOIN Sach s ON s.MaSach = l.MaSach
       WHERE l.MaDocGia = ?
       ORDER BY l.MaMuon DESC`
    )
    .all(req.user.sub);
  res.json({ loans });
});

app.post('/api/chatbot', (req, res) => {
  const { message = '' } = req.body;
  const text = message.toLowerCase();

  if (text.includes('gợi ý') || text.includes('de xuat')) {
    const picks = db.prepare('SELECT TenSach, NguonGoc FROM Sach ORDER BY SoQuyen DESC LIMIT 3').all();
    return res.json({
      reply: `Bạn có thể đọc: ${picks.map((x) => `${x.TenSach} (${x.NguonGoc})`).join(', ')}.`
    });
  }

  if (text.includes('sẵn') || text.includes('co san')) {
    const count = db.prepare('SELECT COUNT(*) AS c FROM Sach WHERE SoQuyen > 0').get().c;
    return res.json({ reply: `Hiện có ${count} đầu sách đang còn để mượn.` });
  }

  return res.json({
    reply:
      'Mình có thể hỗ trợ: tìm sách, gợi ý sách, kiểm tra tình trạng còn sách, hoặc hướng dẫn mượn sách bằng OTP.'
  });
});

app.get('/api/admin/dashboard', authMiddleware, adminOnly, (req, res) => {
  const totalBooks = db.prepare('SELECT COUNT(*) AS c FROM Sach').get().c;
  const totalReaders = db.prepare('SELECT COUNT(*) AS c FROM Docgia').get().c;
  const borrowed = db.prepare("SELECT COUNT(*) AS c FROM TheoDoiMuonSach WHERE TrangThai = 'dang_muon'").get().c;
  const overdue = db
    .prepare("SELECT COUNT(*) AS c FROM TheoDoiMuonSach WHERE TrangThai = 'dang_muon' AND datetime(HanTra) < datetime('now')")
    .get().c;

  res.json({ totalBooks, totalReaders, borrowed, overdue });
});

app.get('/api/admin/books', authMiddleware, adminOnly, (req, res) => {
  const books = db.prepare('SELECT * FROM Sach ORDER BY TenSach').all();
  res.json({ books });
});

app.post('/api/admin/books', authMiddleware, adminOnly, (req, res) => {
  const { MaSach, TenSach, DonGia = 0, SoQuyen = 0, NamXuatBan, MaNXB, NguonGoc, MoTa = '', TheLoai = '', AnhBia = '' } = req.body;
  db.prepare(
    `INSERT INTO Sach(MaSach, TenSach, DonGia, SoQuyen, NamXuatBan, MaNXB, NguonGoc, MoTa, TheLoai, AnhBia)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`
  ).run(MaSach, TenSach, DonGia, SoQuyen, NamXuatBan, MaNXB, NguonGoc, MoTa, TheLoai, AnhBia);
  res.json({ message: 'Đã thêm sách.' });
});

app.put('/api/admin/books/:id', authMiddleware, adminOnly, (req, res) => {
  const { TenSach, DonGia = 0, SoQuyen = 0, NamXuatBan, MaNXB, NguonGoc = '', MoTa = '', TheLoai = '', AnhBia = '' } = req.body;
  db.prepare(
    `UPDATE Sach
     SET TenSach = ?, DonGia = ?, SoQuyen = ?, NamXuatBan = ?, MaNXB = ?, NguonGoc = ?, MoTa = ?, TheLoai = ?, AnhBia = ?
     WHERE MaSach = ?`
  ).run(TenSach, DonGia, SoQuyen, NamXuatBan, MaNXB, NguonGoc, MoTa, TheLoai, AnhBia, req.params.id);
  res.json({ message: 'Đã cập nhật sách.' });
});

app.delete('/api/admin/books/:id', authMiddleware, adminOnly, (req, res) => {
  db.prepare('DELETE FROM Sach WHERE MaSach = ?').run(req.params.id);
  res.json({ message: 'Đã xóa sách.' });
});

app.get('/api/admin/users', authMiddleware, adminOnly, (req, res) => {
  const users = db
    .prepare(
      `SELECT d.MaDocGia, d.HoLot, d.Ten, d.Email, d.DienThoai, d.DiaChi,
              CASE
                WHEN EXISTS (
                  SELECT 1 FROM TheoDoiMuonSach l
                  WHERE l.MaDocGia = d.MaDocGia AND l.TrangThai = 'dang_muon'
                ) THEN 'Đang mượn'
                ELSE 'Bình thường'
              END AS TrangThai
       FROM Docgia d
       ORDER BY d.MaDocGia`
    )
    .all();
  res.json({ users });
});

app.get('/api/admin/loans', authMiddleware, adminOnly, (req, res) => {
  const loans = db
    .prepare(
      `SELECT l.MaMuon, d.HoLot || ' ' || d.Ten AS DocGia, s.TenSach, l.NgayMuon, l.HanTra, l.NgayTra, l.TrangThai
       FROM TheoDoiMuonSach l
       JOIN Docgia d ON d.MaDocGia = l.MaDocGia
       JOIN Sach s ON s.MaSach = l.MaSach
       ORDER BY l.MaMuon DESC`
    )
    .all();
  res.json({ loans });
});

app.get('/health', (req, res) => res.json({ ok: true }));

app.listen(PORT, () => {
  console.log(`Server running at http://localhost:${PORT}`);
});
