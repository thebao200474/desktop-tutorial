const adminState = {
  token: localStorage.getItem('adminToken') || '',
  profile: JSON.parse(localStorage.getItem('adminProfile') || 'null')
};

if (!adminState.token) {
  window.location.href = '/admin-login.html';
}

const adminGreeting = document.getElementById('admin-greeting');
const sumBooks = document.getElementById('sum-books');
const sumUsers = document.getElementById('sum-users');
const sumReviews = document.getElementById('sum-reviews');
const sumBorrows = document.getElementById('sum-borrows');
const sumBooksSub = document.getElementById('sum-books-sub');
const sumUsersSub = document.getElementById('sum-users-sub');
const sumReviewsSub = document.getElementById('sum-reviews-sub');
const sumBorrowsSub = document.getElementById('sum-borrows-sub');

const topBooksTable = document.getElementById('top-books-table');
const booksTable = document.getElementById('books-table');
const usersTable = document.getElementById('users-table');
const loansTable = document.getElementById('loans-table');
const trafficRange = document.getElementById('traffic-range');

let trafficChart = null;

const logoutBtn = document.getElementById('admin-logout');
if (logoutBtn) {
  logoutBtn.addEventListener('click', () => {
    localStorage.removeItem('adminToken');
    localStorage.removeItem('adminProfile');
    window.location.href = '/admin-login.html';
  });
}

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

function renderTrafficChart(labels = [], values = []) {
  const ctx = document.getElementById('traffic-chart');
  if (!ctx) return;

  if (trafficChart) trafficChart.destroy();

  trafficChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Lượt truy cập',
          data: values,
          borderColor: '#94a3b8',
          backgroundColor: 'rgba(148, 163, 184, 0.18)',
          tension: 0,
          fill: true,
          pointRadius: 2,
          pointBackgroundColor: '#64748b'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: false,
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0 } }
      },
      plugins: {
        legend: { display: false }
      }
    }
  });
}

async function loadSummary() {
  const data = await api('/api/admin/summary');
  sumBooks.textContent = Number(data.totalBooks || 0).toLocaleString('vi-VN');
  sumUsers.textContent = Number(data.totalUsers || 0).toLocaleString('vi-VN');
  sumReviews.textContent = Number(data.totalReviews || 0).toLocaleString('vi-VN');
  sumBorrows.textContent = Number(data.totalBorrows || 0).toLocaleString('vi-VN');

  sumBooksSub.textContent = data?.deltas?.books || '+52 so với tháng trước';
  sumUsersSub.textContent = data?.deltas?.users || '+220 người đăng ký mới';
  sumReviewsSub.textContent = data?.deltas?.reviews || '+45 đánh giá mới';
  sumBorrowsSub.textContent = data?.deltas?.borrows || '+12 cập nhật mới';
}

async function loadTraffic(days = 7) {
  const data = await api(`/api/admin/traffic?days=${days}`);
  renderTrafficChart(data.labels || [], data.values || []);
}

async function loadTopBooks() {
  const data = await api('/api/admin/top-books');
  topBooksTable.innerHTML = '';
  (data.items || []).forEach((item) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${item.stt}</td>
      <td>${item.tenSach}</td>
      <td>${item.tacGia}</td>
      <td>${item.ngayThem}</td>
    `;
    topBooksTable.appendChild(tr);
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

  await api('/api/admin/books', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });

  loadTables();
  loadSummary();
  loadTopBooks();
});

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

  loadTables();
  loadSummary();
}

async function loadTables() {
  const [books, users, loans] = await Promise.all([
    api('/api/admin/books'),
    api('/api/admin/users'),
    api('/api/admin/loans')
  ]);

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
      loadTables();
      loadSummary();
      loadTopBooks();
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

if (trafficRange) {
  trafficRange.addEventListener('change', () => {
    loadTraffic(Number(trafficRange.value || 7));
  });
}

adminGreeting.textContent = adminState.profile?.name
  ? `Xin chào, ${adminState.profile.name}`
  : 'Xin chào, Admin';

loadSummary();
loadTraffic(7);
loadTopBooks();
loadTables();
