const adminState = { token: localStorage.getItem('adminToken') || '' };

const adminEmail = document.getElementById('admin-email');
const adminOtp = document.getElementById('admin-otp');
const adminStatus = document.getElementById('admin-status');

const sTotalBooks = document.getElementById('s-total-books');
const sReaders = document.getElementById('s-readers');
const sBorrowed = document.getElementById('s-borrowed');
const sOverdue = document.getElementById('s-overdue');

const booksTable = document.getElementById('books-table');
const usersTable = document.getElementById('users-table');
const loansTable = document.getElementById('loans-table');

document.getElementById('admin-send-otp').addEventListener('click', async () => {
  const res = await fetch('/api/auth/send-otp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: adminEmail.value.trim(), role: 'admin' })
  });
  const data = await res.json();
  adminStatus.textContent = data.message || data.error;
});

document.getElementById('admin-verify').addEventListener('click', async () => {
  const res = await fetch('/api/auth/verify-otp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: adminEmail.value.trim(), otp: adminOtp.value.trim(), role: 'admin' })
  });
  const data = await res.json();
  if (data.token) {
    adminState.token = data.token;
    localStorage.setItem('adminToken', data.token);
    adminStatus.textContent = 'Xác thực admin thành công.';
    loadAll();
  } else {
    adminStatus.textContent = data.message || 'Thất bại';
  }
});

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

async function api(path) {
  const res = await fetch(path, {
    headers: {
      Authorization: `Bearer ${adminState.token}`
    }
  });
  return res.json();
}

async function loadAll() {
  if (!adminState.token) return;

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
    tr.innerHTML = `<td>${b.MaSach}</td><td>${b.TenSach}</td><td>${b.NguonGoc || ''}</td><td>${b.SoQuyen}</td><td><button class="btn btn-sm btn-danger" data-id="${b.MaSach}">Xóa</button></td>`;
    booksTable.appendChild(tr);
  });

  booksTable.querySelectorAll('button').forEach((btn) => {
    btn.addEventListener('click', async () => {
      await fetch(`/api/admin/books/${btn.dataset.id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${adminState.token}` }
      });
      loadAll();
    });
  });

  usersTable.innerHTML = '';
  (users.users || []).forEach((u) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${u.MaDocGia}</td><td>${u.HoLot} ${u.Ten}</td><td>${u.Email || ''}</td><td>${u.DienThoai || ''}</td>`;
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
