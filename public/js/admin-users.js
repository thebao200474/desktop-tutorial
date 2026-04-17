const state = {
  token: localStorage.getItem('adminToken') || '',
  users: [],
  query: '',
  role: 'all',
  status: 'all',
  page: 1,
  pageSize: 5
};

if (!state.token) window.location.href = '/admin-login.html';

const usersTable = document.getElementById('users-table');
const searchInput = document.getElementById('user-search');
const roleFilter = document.getElementById('user-role-filter');
const statusFilter = document.getElementById('user-status-filter');
const chipsContainer = document.getElementById('user-filter-chips');
const paginationEl = document.getElementById('users-pagination');
const countEl = document.getElementById('users-count');
const createUserForm = document.getElementById('user-create-form');
const createUserModalEl = document.getElementById('user-create-modal');
const viewUserModalEl = document.getElementById('user-view-modal');
const createUserModal = new bootstrap.Modal(createUserModalEl);
const viewUserModal = new bootstrap.Modal(viewUserModalEl);

function normalizeText(value) {
  return String(value || '')
    .toLowerCase()
    .normalize('NFD')
    .replace(/\p{Diacritic}/gu, '');
}

async function api(path) {
  const response = await fetch(path, { headers: { Authorization: `Bearer ${state.token}` } });
  if (response.status === 401) {
    localStorage.removeItem('adminToken');
    window.location.href = '/admin-login.html';
    return {};
  }

  return response.json();
}

async function requestJson(path, options = {}) {
  const response = await fetch(path, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${state.token}`,
      ...(options.headers || {})
    }
  });

  if (response.status === 401) {
    localStorage.removeItem('adminToken');
    window.location.href = '/admin-login.html';
    return null;
  }

  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw new Error(data.message || 'Có lỗi xảy ra.');
  }
  return data;
}

document.getElementById('admin-logout').addEventListener('click', () => {
  localStorage.removeItem('adminToken');
  window.location.href = '/admin-login.html';
});

document.getElementById('add-user-btn').addEventListener('click', () => {
  createUserForm.reset();
});

document.getElementById('user-advanced-filter').addEventListener('click', () => {
  alert('Bạn có thể lọc theo vai trò + trạng thái + từ khóa cùng lúc.');
});

function userRole(user) {
  return /admin/i.test(user.Email || '') ? 'admin' : 'user';
}

function userStatus(user) {
  if (user.TrangThai === 'locked' || user.TrangThai === 'Bị khóa') return 'locked';
  if (user.TrangThai === 'inactive' || user.TrangThai === 'Không hoạt động') return 'inactive';
  return 'active';
}

function statusBadge(status) {
  if (status === 'locked') return '<span class="badge badge-soft-danger">Bị khóa</span>';
  if (status === 'inactive') return '<span class="badge badge-soft-secondary">Không hoạt động</span>';
  return '<span class="badge badge-soft-success">Hoạt động</span>';
}

function statusText(status) {
  if (status === 'locked') return 'Bị khóa';
  if (status === 'inactive') return 'Không hoạt động';
  return 'Hoạt động';
}

function roleLabel(role) {
  return role === 'admin' ? 'Quản trị viên' : 'Người dùng';
}

function filteredUsers() {
  const q = normalizeText(state.query);
  return state.users.filter((user) => {
    const role = userRole(user);
    const status = userStatus(user);
    const byRole = state.role === 'all' || state.role === role;
    const byStatus = state.status === 'all' || state.status === status;
    const byQuery =
      !q || [user.MaDocGia, `${user.HoLot} ${user.Ten}`, user.Email, user.DienThoai].some((value) => normalizeText(value).includes(q));

    return byRole && byStatus && byQuery;
  });
}

function renderStats() {
  const total = state.users.length;
  const active = state.users.filter((user) => userStatus(user) === 'active').length;
  const locked = state.users.filter((user) => userStatus(user) === 'locked').length;
  const monthlyNew = Math.min(total, Math.max(1, Math.floor(total * 0.08)));

  document.getElementById('user-stat-total').textContent = total;
  document.getElementById('user-stat-active').textContent = active;
  document.getElementById('user-stat-locked').textContent = locked;
  document.getElementById('user-stat-new').textContent = monthlyNew;
}

function renderChips() {
  const chips = [];
  if (state.query.trim()) chips.push({ label: `Từ khóa: ${state.query}`, clear: () => (state.query = '') });
  if (state.role !== 'all') chips.push({ label: roleFilter.options[roleFilter.selectedIndex].textContent, clear: () => (state.role = 'all') });
  if (state.status !== 'all') chips.push({ label: statusFilter.options[statusFilter.selectedIndex].textContent, clear: () => (state.status = 'all') });

  chipsContainer.innerHTML = '';
  chips.forEach((chip) => {
    const button = document.createElement('button');
    button.className = 'chip-btn';
    button.innerHTML = `${chip.label} <i class="bi bi-x"></i>`;
    button.addEventListener('click', () => {
      chip.clear();
      syncFilterInputs();
      state.page = 1;
      renderUsers();
    });
    chipsContainer.appendChild(button);
  });
}

function renderPagination(total) {
  const totalPages = Math.max(1, Math.ceil(total / state.pageSize));
  if (state.page > totalPages) state.page = totalPages;
  paginationEl.innerHTML = '';

  for (let i = 1; i <= totalPages; i += 1) {
    const li = document.createElement('li');
    li.className = `page-item ${i === state.page ? 'active' : ''}`;
    li.innerHTML = `<button class="page-link" type="button">${i}</button>`;
    li.addEventListener('click', () => {
      state.page = i;
      renderUsers();
    });
    paginationEl.appendChild(li);
  }
}

function renderUsers() {
  const users = filteredUsers();
  const start = (state.page - 1) * state.pageSize;
  const pageItems = users.slice(start, start + state.pageSize);

  usersTable.innerHTML = '';

  if (!pageItems.length) {
    usersTable.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Không có người dùng phù hợp.</td></tr>';
    countEl.textContent = `Hiển thị 0 / ${users.length} người dùng`;
    renderPagination(0);
    renderChips();
    return;
  }

  pageItems.forEach((user, index) => {
    const role = userRole(user);
    const status = userStatus(user);

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${start + index + 1}</td>
      <td><img src="/images/users/user-1.svg" alt="avatar" class="user-avatar-sm" /></td>
      <td class="fw-semibold">${user.HoLot} ${user.Ten}</td>
      <td>${user.Email || '-'}</td>
      <td>${roleLabel(role)}</td>
      <td>${statusBadge(status)}</td>
      <td>
        <button class="btn action-icon-btn" title="Xem chi tiết" data-action="view" data-id="${user.MaDocGia}"><i class="bi bi-eye"></i></button>
        <button class="btn action-icon-btn" title="Chỉnh sửa" data-action="edit" data-id="${user.MaDocGia}"><i class="bi bi-pencil"></i></button>
        <button class="btn action-icon-btn" title="Khóa/Mở khóa" data-action="toggle-lock" data-id="${user.MaDocGia}">
          <i class="bi ${status === 'locked' ? 'bi-unlock' : 'bi-lock'}"></i>
        </button>
        <button class="btn action-icon-btn" title="Xóa" data-action="delete" data-id="${user.MaDocGia}"><i class="bi bi-trash"></i></button>
      </td>
    `;

    usersTable.appendChild(tr);
  });

  countEl.textContent = `Hiển thị ${start + 1}-${Math.min(start + state.pageSize, users.length)} / ${users.length} người dùng`;
  renderPagination(users.length);
  renderChips();
}

function refreshUsersKeepPage() {
  return loadUsers(true);
}

function renderUserDetailModal(user) {
  document.getElementById('view-ma-doc-gia').textContent = user.MaDocGia || '-';
  document.getElementById('view-fullname').textContent = `${user.HoLot || ''} ${user.Ten || ''}`.trim() || '-';
  document.getElementById('view-email').textContent = user.Email || '-';
  document.getElementById('view-phone').textContent = user.DienThoai || '-';
  document.getElementById('view-gender').textContent = user.Phai || '-';
  document.getElementById('view-birthday').textContent = user.NgaySinh ? String(user.NgaySinh).slice(0, 10) : '-';
  document.getElementById('view-address').textContent = user.DiaChi || '-';
  document.getElementById('view-status').textContent = statusText(userStatus(user));
  viewUserModal.show();
}

async function handleUserAction(action, id) {
  const user = state.users.find((item) => item.MaDocGia === id);
  if (!user) return;

  if (action === 'view') {
    renderUserDetailModal(user);
    return;
  }

  if (action === 'edit') {
    const hoLot = prompt('Họ lót:', user.HoLot || '');
    if (hoLot === null) return;
    const ten = prompt('Tên:', user.Ten || '');
    if (ten === null) return;
    const dienThoai = prompt('Số điện thoại:', user.DienThoai || '');
    if (dienThoai === null) return;
    const diaChi = prompt('Địa chỉ:', user.DiaChi || '');
    if (diaChi === null) return;
    const phai = prompt('Phái (Nam/Nữ/Khác):', user.Phai || 'Nam');
    if (phai === null) return;
    const ngaySinh = prompt('Ngày sinh (YYYY-MM-DD):', user.NgaySinh || '2000-01-01');
    if (ngaySinh === null) return;

    try {
      await requestJson(`/api/admin/users/${id}`, {
        method: 'PUT',
        body: JSON.stringify({ hoLot, ten, dienThoai, diaChi, phai, ngaySinh })
      });
      alert('Đã cập nhật người dùng.');
      await refreshUsersKeepPage();
    } catch (error) {
      alert(error.message);
    }
    return;
  }

  if (action === 'toggle-lock') {
    try {
      const response = await requestJson(`/api/admin/users/${id}/toggle-lock`, { method: 'PUT' });
      if (response?.message) alert(response.message);
      await refreshUsersKeepPage();
    } catch (error) {
      alert(error.message);
    }
    return;
  }

  if (action === 'delete') {
    const ok = confirm(`Bạn có chắc muốn xóa độc giả ${user.HoLot} ${user.Ten}?`);
    if (!ok) return;
    try {
      await requestJson(`/api/admin/users/${id}`, { method: 'DELETE' });
      alert('Đã xóa người dùng.');
      await refreshUsersKeepPage();
    } catch (error) {
      alert(error.message);
    }
  }
}

function syncFilterInputs() {
  searchInput.value = state.query;
  roleFilter.value = state.role;
  statusFilter.value = state.status;
}

searchInput.addEventListener('input', () => {
  state.query = searchInput.value;
  state.page = 1;
  renderUsers();
});

roleFilter.addEventListener('change', () => {
  state.role = roleFilter.value;
  state.page = 1;
  renderUsers();
});

statusFilter.addEventListener('change', () => {
  state.status = statusFilter.value;
  state.page = 1;
  renderUsers();
});

document.getElementById('clear-user-filters').addEventListener('click', () => {
  state.query = '';
  state.role = 'all';
  state.status = 'all';
  state.page = 1;
  syncFilterInputs();
  renderUsers();
});

usersTable.addEventListener('click', (event) => {
  const button = event.target.closest('button[data-action]');
  if (!button) return;
  const { action, id } = button.dataset;
  handleUserAction(action, id);
});

function startUserVoiceSearch() {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    alert('Trình duyệt chưa hỗ trợ tìm kiếm giọng nói.');
    return;
  }
  const recognition = new SpeechRecognition();
  recognition.lang = 'vi-VN';
  recognition.interimResults = false;
  recognition.maxAlternatives = 1;
  recognition.onresult = (event) => {
    const transcript = event.results[0][0].transcript || '';
    state.query = transcript.trim();
    searchInput.value = state.query;
    state.page = 1;
    renderUsers();
  };
  recognition.onerror = () => alert('Không nhận diện được giọng nói. Vui lòng thử lại.');
  recognition.start();
}

document.getElementById('user-voice-search').addEventListener('click', startUserVoiceSearch);

createUserForm.addEventListener('submit', async (event) => {
  event.preventDefault();
  const payload = {
    hoLot: document.getElementById('create-ho-lot').value.trim(),
    ten: document.getElementById('create-ten').value.trim(),
    email: document.getElementById('create-email').value.trim(),
    dienThoai: document.getElementById('create-phone').value.trim(),
    diaChi: document.getElementById('create-address').value.trim(),
    phai: document.getElementById('create-gender').value,
    ngaySinh: document.getElementById('create-birthday').value || null,
    password: document.getElementById('create-password').value
  };
  try {
    await requestJson('/api/admin/users', { method: 'POST', body: JSON.stringify(payload) });
    createUserModal.hide();
    createUserForm.reset();
    alert('Đã thêm người dùng mới.');
    await refreshUsersKeepPage();
  } catch (error) {
    alert(error.message);
  }
});

async function loadUsers(keepPage = false) {
  const prevPage = state.page;
  const data = await api('/api/admin/users');
  state.users = data.users || [];
  if (keepPage) state.page = prevPage;
  renderStats();
  renderUsers();
}

loadUsers();
