const state = { token: localStorage.getItem('adminToken') || '', page: 1, pageSize: 10, search: '', status: 'all', period: 'all' };
if (!state.token) window.location.href = '/admin-login.html';

const qs = (id) => document.getElementById(id);
const borrowBody = qs('borrow-table-body');
const pagination = qs('borrow-pagination');
const emptyBox = qs('borrow-empty');
const countText = qs('borrow-count');
const detailModal = new bootstrap.Modal(qs('borrowDetailModal'));
const createModal = new bootstrap.Modal(qs('createBorrowModal'));

const statusMap = { dang_muon: 'Đang mượn', qua_han: 'Quá hạn', da_tra: 'Đã trả', cho_duyet: 'Chờ duyệt', da_huy: 'Đã hủy' };

async function api(path, options = {}) {
  const res = await fetch(path, { ...options, headers: { ...(options.headers || {}), Authorization: `Bearer ${state.token}`, 'Content-Type': 'application/json' } });
  if (res.status === 401) { localStorage.removeItem('adminToken'); window.location.href = '/admin-login.html'; return {}; }
  return res.json();
}

function fmtDate(v) { return v ? new Date(v).toLocaleDateString('vi-VN') : '--'; }

function renderPagination(totalPages, page) {
  pagination.innerHTML = '';
  for (let i = 1; i <= totalPages; i += 1) {
    const li = document.createElement('li'); li.className = `page-item ${i === page ? 'active' : ''}`;
    li.innerHTML = `<button class="page-link">${i}</button>`;
    li.addEventListener('click', () => { state.page = i; loadBorrows(); });
    pagination.appendChild(li);
  }
}

function actionButton(icon, title, onClick) {
  const btn = document.createElement('button');
  btn.className = 'action-btn'; btn.title = title; btn.innerHTML = `<i class="bi ${icon}"></i>`; btn.addEventListener('click', onClick);
  return btn;
}

async function openDetail(id) {
  const data = await api(`/api/admin/borrows/${id}`);
  const x = data.item; if (!x) return;
  qs('borrow-detail-content').innerHTML = `
    <div class="row g-2 small">
      <div class="col-6"><b>Mã mượn:</b> PM${x.MaMuon}</div>
      <div class="col-6"><b>Trạng thái:</b> ${statusMap[x.TrangThai] || x.TrangThai}</div>
      <div class="col-6"><b>Độc giả:</b> ${x.DocGia}</div>
      <div class="col-6"><b>Email:</b> ${x.Email || '--'}</div>
      <div class="col-6"><b>Sách:</b> ${x.TenSach}</div>
      <div class="col-6"><b>Tác giả:</b> ${x.TacGia || '--'}</div>
      <div class="col-6"><b>Ngày mượn:</b> ${fmtDate(x.NgayMuon)}</div>
      <div class="col-6"><b>Hạn trả:</b> ${fmtDate(x.HanTra)}</div>
      <div class="col-6"><b>Ngày trả:</b> ${fmtDate(x.NgayTra)}</div>
      <div class="col-6"><b>SĐT:</b> ${x.DienThoai || '--'}</div>
    </div>`;
  detailModal.show();
}

async function mutate(id, action, body = {}) { await api(`/api/admin/borrows/${id}/${action}`, { method: 'PUT', body: JSON.stringify(body) }); loadBorrows(); }

async function loadBorrows() {
  const p = new URLSearchParams({ page: state.page, pageSize: state.pageSize, search: state.search, status: state.status, period: state.period });
  const data = await api(`/api/admin/borrows?${p.toString()}`);
  const items = data.items || [];

  qs('stat-total').textContent = data.stats?.total || 0;
  qs('stat-borrowing').textContent = data.stats?.dangMuon || 0;
  qs('stat-overdue').textContent = data.stats?.quaHan || 0;
  qs('stat-returned').textContent = data.stats?.daTra || 0;

  borrowBody.innerHTML = '';
  if (!items.length) {
    emptyBox.classList.remove('d-none');
  } else {
    emptyBox.classList.add('d-none');
    items.forEach((x, idx) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${(state.page - 1) * state.pageSize + idx + 1}</td><td>PM${x.MaMuon}</td><td>${x.DocGia}</td><td>${x.TenSach}</td><td>${fmtDate(x.NgayMuon)}</td><td>${fmtDate(x.HanTra)}</td><td>${fmtDate(x.NgayTra)}</td><td><span class="status-badge status-${x.TrangThai}">${statusMap[x.TrangThai] || x.TrangThai}</span></td><td class="action-cell"></td>`;
      const cell = tr.querySelector('.action-cell');
      cell.append(
        actionButton('bi-eye', 'Xem chi tiết', () => openDetail(x.MaMuon)),
        actionButton('bi-check2-circle', 'Duyệt phiếu', () => mutate(x.MaMuon, 'approve')),
        actionButton('bi-check2-square', 'Xác nhận trả', () => mutate(x.MaMuon, 'return')),
        actionButton('bi-calendar-plus', 'Gia hạn +7 ngày', () => {
          const d = new Date(x.HanTra || Date.now()); d.setDate(d.getDate() + 7); mutate(x.MaMuon, 'extend', { HanTra: d.toISOString() });
        }),
        actionButton('bi-trash', 'Hủy phiếu', async () => { await api(`/api/admin/borrows/${x.MaMuon}`, { method: 'DELETE' }); loadBorrows(); })
      );
      borrowBody.appendChild(tr);
    });
  }

  const pg = data.pagination || { totalPages: 1, total: 0, page: 1 };
  countText.textContent = `Hiển thị ${(pg.page - 1) * state.pageSize + 1}-${Math.min(pg.page * state.pageSize, pg.total)} / ${pg.total} phiếu`;
  renderPagination(pg.totalPages || 1, pg.page || 1);
}

async function preloadCreateModal() {
  const [users, books] = await Promise.all([api('/api/admin/users'), api('/api/admin/books')]);
  qs('create-reader').innerHTML = (users.users || []).map((u) => `<option value="${u.MaDocGia}">${u.HoLot} ${u.Ten} (${u.MaDocGia})</option>`).join('');
  qs('create-book').innerHTML = (books.books || []).map((b) => `<option value="${b.MaSach}">${b.TenSach} (${b.MaSach})</option>`).join('');
  qs('create-date').value = new Date().toISOString().slice(0, 10);
  const d = new Date(); d.setDate(d.getDate() + 7); qs('create-due').value = d.toISOString().slice(0, 10);
}

qs('create-borrow-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  await api('/api/admin/borrows', {
    method: 'POST',
    body: JSON.stringify({ MaDocGia: qs('create-reader').value, MaSach: qs('create-book').value, NgayMuon: qs('create-date').value, HanTra: qs('create-due').value, TrangThai: qs('create-status').value })
  });
  createModal.hide();
  loadBorrows();
});

qs('create-borrow-btn').addEventListener('click', async () => { await preloadCreateModal(); createModal.show(); });
qs('empty-create-btn').addEventListener('click', async () => { await preloadCreateModal(); createModal.show(); });
qs('refresh-borrows').addEventListener('click', () => loadBorrows());
qs('export-borrows').addEventListener('click', () => window.print());
qs('borrow-search').addEventListener('input', (e) => { state.search = e.target.value.trim(); state.page = 1; loadBorrows(); });
qs('borrow-status').addEventListener('change', (e) => { state.status = e.target.value; state.page = 1; loadBorrows(); });
qs('borrow-period').addEventListener('change', (e) => { state.period = e.target.value; state.page = 1; loadBorrows(); });
qs('admin-greeting').textContent = 'Xin chào, Admin';

loadBorrows();
