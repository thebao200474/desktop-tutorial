const adminState = {
  token: localStorage.getItem('adminToken') || '',
  profile: JSON.parse(localStorage.getItem('adminProfile') || 'null')
};

if (!adminState.token) {
  window.location.href = '/admin-login.html';
}

const adminStatus = document.getElementById('admin-status');
const sTotalBooks = document.getElementById('s-total-books');
const sReaders = document.getElementById('s-readers');
const sBorrowed = document.getElementById('s-borrowed');
const sOverdue = document.getElementById('s-overdue');

const booksTable = document.getElementById('books-table');
const usersTable = document.getElementById('users-table');
const loansTable = document.getElementById('loans-table');

const logoutBtn = document.getElementById('admin-logout');
if (logoutBtn) {
  logoutBtn.addEventListener('click', () => {
    localStorage.removeItem('adminToken');
    localStorage.removeItem('adminProfile');
    window.location.href = '/admin-login.html';
  });
}

document.getElementById('add-book-btn').addEventListener('click', async () => {
  if (!adminState.token) return;
  const payload = {
    MaSach: document.getElementById('new-book-id').value.trim(),
    TenSach: document.getElementById('new-book-name').value.trim(),
    NguonGoc: document.getElementById('new-book-author').value.trim(),
    SoQuyen: Number(document.getElementById('new-book-qty').value || 0),
    DonGia: 0,
    MaNXB: 'NXB01',
    NamXuatBan: 2024
  };

  const res = await fetch('/api/admin/books', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${adminState.token}`
    },
    body: JSON.stringify(payload)
  });

  const data = await res.json();
  adminStatus.textContent = data.message || 'Đã thêm';
  loadAll();
});

async function api(path, options = {}) {
  const res = await fetch(path, {
    ...options,
    headers: {
      ...(options.headers || {}),
      Authorization: `Bearer ${adminState.token}`
    }
  });

  if (res.status === 401) {
    localStorage.removeItem('adminToken');
    localStorage.removeItem('adminProfile');
    window.location.href = '/admin-login.html';
    return {};
  }

  return res.json();
}

async function editBook(book) {
  const TenSach = prompt('Tên sách mới:', book.TenSach);
  if (!TenSach) return;
  const NguonGoc = prompt('Tác giả / Nguồn gốc:', book.NguonGoc || '');
  if (NguonGoc === null) return;
  const qtyInput = prompt('Số lượng:', String(book.SoQuyen));
  const SoQuyen = Number(qtyInput);
  if (Number.isNaN(SoQuyen)) return;

  await api(`/api/admin/books/${book.MaSach}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      TenSach,
      NguonGoc,
      SoQuyen,
      DonGia: book.DonGia,
      NamXuatBan: book.NamXuatBan,
      MaNXB: book.MaNXB,
      MoTa: book.MoTa,
      TheLoai: book.TheLoai,
      AnhBia: book.AnhBia
    })
  });

  loadAll();
}

async function loadAll() {
  if (!adminState.token) return;

  adminStatus.textContent = adminState.profile?.name
    ? `Xin chào ${adminState.profile.name}.`
    : 'Đang tải dữ liệu...';

  const [dashboard, books, users, loans] = await Promise.all([
    api('/api/admin/dashboard'),
    api('/api/admin/books'),
    api('/api/admin/users'),
    api('/api/admin/loans')
  ]);

  sTotalBooks.textContent = dashboard.totalBooks ?? 0;
  sReaders.textContent = dashboard.totalReaders ?? 0;
  sBorrowed.textContent = dashboard.borrowed ?? 0;
  sOverdue.textContent = dashboard.overdue ?? 0;

  booksTable.innerHTML = '';
  (books.books || []).forEach((b) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${b.MaSach}</td>
      <td>${b.TenSach}</td>
      <td>${b.NguonGoc || ''}</td>
      <td><span class="badge text-bg-light">${b.SoQuyen}</span></td>
      <td>
        <button class="btn btn-sm btn-warning me-1 edit-btn">Sửa</button>
        <button class="btn btn-sm btn-danger delete-btn">Xóa</button>
      </td>
    `;

    tr.querySelector('.edit-btn').addEventListener('click', () => editBook(b));
    tr.querySelector('.delete-btn').addEventListener('click', async () => {
      await api(`/api/admin/books/${b.MaSach}`, { method: 'DELETE' });
      loadAll();
    });

    booksTable.appendChild(tr);
  });

  usersTable.innerHTML = '';
  (users.users || []).forEach((u) => {
    const badge = u.TrangThai === 'Đang mượn' ? 'text-bg-warning' : 'text-bg-success';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${u.MaDocGia}</td>
      <td>${u.HoLot} ${u.Ten}</td>
      <td>${u.Email || ''}</td>
      <td>${u.DienThoai || ''}</td>
      <td><span class="badge ${badge}">${u.TrangThai}</span></td>
    `;
    usersTable.appendChild(tr);
  });

  loansTable.innerHTML = '';
  (loans.loans || []).forEach((l) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${l.MaMuon}</td><td>${l.DocGia}</td><td>${l.TenSach}</td><td>${new Date(l.NgayMuon).toLocaleDateString('vi-VN')}</td><td>${new Date(l.HanTra).toLocaleDateString('vi-VN')}</td><td>${l.TrangThai}</td>`;
    loansTable.appendChild(tr);
  });
}

loadAll();
