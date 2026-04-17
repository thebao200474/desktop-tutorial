const state = {
  token: localStorage.getItem('adminToken') || '',
  books: [],
  query: '',
  currentPage: 1,
  pageSize: 5,
  editingId: null,
  category: 'all',
  status: 'all'
};

if (!state.token) {
  window.location.href = '/admin-login.html';
}

const booksTable = document.getElementById('books-table');
const paginationEl = document.getElementById('books-pagination');
const searchInput = document.getElementById('book-search');
const voiceBtn = document.getElementById('book-voice-btn');
const voiceStatus = document.getElementById('voice-status');
const bookForm = document.getElementById('book-form');
const bookModalEl = document.getElementById('book-modal');
const bookModal = new bootstrap.Modal(bookModalEl);
const modalTitle = document.getElementById('book-modal-title');
const submitBtn = document.getElementById('book-submit-btn');
const categoryFilter = document.getElementById('book-category-filter');
const statusFilter = document.getElementById('book-status-filter');
const chipsContainer = document.getElementById('book-filter-chips');
const countLabel = document.getElementById('books-count');

function getField(id) {
  return document.getElementById(id);
}

function bookStatus(book) {
  const qty = Number(book.SoQuyen || 0);
  const hot = /hot/i.test(book.TheLoai || '') || qty >= 20;

  if (qty <= 0) return { key: 'out', label: 'Hết sách', klass: 'badge-soft-danger' };
  if (hot) return { key: 'hot', label: 'Hot', klass: 'badge-soft-hot' };
  if (qty <= 5) return { key: 'low', label: 'Còn ít', klass: 'badge-soft-warning' };
  return { key: 'available', label: 'Còn sách', klass: 'badge-soft-success' };
}

function updateStats() {
  const total = state.books.length;
  const available = state.books.filter((book) => bookStatus(book).key === 'available').length;
  const out = state.books.filter((book) => bookStatus(book).key === 'out').length;
  const hot = state.books.filter((book) => bookStatus(book).key === 'hot').length;

  document.getElementById('stat-total').textContent = total;
  document.getElementById('stat-available').textContent = available;
  document.getElementById('stat-out').textContent = out;
  document.getElementById('stat-hot').textContent = hot;
}

async function api(path, options = {}) {
  const response = await fetch(path, {
    ...options,
    headers: {
      ...(options.headers || {}),
      Authorization: `Bearer ${state.token}`,
      'Content-Type': 'application/json'
    }
  });

  if (response.status === 401) {
    localStorage.removeItem('adminToken');
    window.location.href = '/admin-login.html';
    return {};
  }

  return response.json();
}

function logout() {
  localStorage.removeItem('adminToken');
  window.location.href = '/admin-login.html';
}

document.getElementById('admin-logout').addEventListener('click', logout);

function normalizeText(value) {
  return String(value || '')
    .toLowerCase()
    .normalize('NFD')
    .replace(/\p{Diacritic}/gu, '');
}

function getFilteredBooks() {
  const q = normalizeText(state.query.trim());

  return state.books.filter((book) => {
    const byCategory = state.category === 'all' || (book.TheLoai || 'Chưa phân loại') === state.category;
    const status = bookStatus(book).key;
    const byStatus = state.status === 'all' || state.status === status;

    const byQuery =
      !q ||
      [book.MaSach, book.TenSach, book.NguonGoc, book.TheLoai, book.MoTa, book.MaNXB, book.NamXuatBan, book.SoQuyen, book.DonGia].some((field) =>
        normalizeText(field).includes(q)
      );

    return byCategory && byStatus && byQuery;
  });
}

function getPageItems(items) {
  const start = (state.currentPage - 1) * state.pageSize;
  return items.slice(start, start + state.pageSize);
}

function renderPagination(totalItems) {
  const totalPages = Math.max(1, Math.ceil(totalItems / state.pageSize));
  if (state.currentPage > totalPages) state.currentPage = totalPages;

  paginationEl.innerHTML = '';

  const createPageItem = (label, page, disabled = false, active = false) => {
    const li = document.createElement('li');
    li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;

    const btn = document.createElement('button');
    btn.className = 'page-link';
    btn.type = 'button';
    btn.textContent = label;
    btn.disabled = disabled;
    btn.addEventListener('click', () => {
      state.currentPage = page;
      renderTable();
    });

    li.appendChild(btn);
    paginationEl.appendChild(li);
  };

  createPageItem('‹', Math.max(1, state.currentPage - 1), state.currentPage === 1);
  for (let page = 1; page <= totalPages; page += 1) {
    createPageItem(String(page), page, false, page === state.currentPage);
  }
  createPageItem('›', Math.min(totalPages, state.currentPage + 1), state.currentPage === totalPages);
}

function renderFilterChips() {
  const chips = [];
  if (state.category !== 'all') chips.push({ label: state.category, clear: () => (state.category = 'all') });
  if (state.status !== 'all') chips.push({ label: statusFilter.options[statusFilter.selectedIndex]?.textContent || 'Trạng thái', clear: () => (state.status = 'all') });
  if (state.query.trim()) chips.push({ label: `Từ khóa: ${state.query}`, clear: () => (state.query = '') });

  chipsContainer.innerHTML = '';
  chips.forEach((chip) => {
    const button = document.createElement('button');
    button.className = 'chip-btn';
    button.type = 'button';
    button.innerHTML = `${chip.label} <i class="bi bi-x"></i>`;
    button.addEventListener('click', () => {
      chip.clear();
      searchInput.value = state.query;
      categoryFilter.value = state.category;
      statusFilter.value = state.status;
      state.currentPage = 1;
      renderTable();
    });
    chipsContainer.appendChild(button);
  });
}

function renderTable() {
  const filtered = getFilteredBooks();
  const pageItems = getPageItems(filtered);

  booksTable.innerHTML = '';

  if (!pageItems.length) {
    booksTable.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Không tìm thấy sách phù hợp.</td></tr>';
    countLabel.textContent = `Hiển thị 0 / ${filtered.length} sách`;
    renderPagination(0);
    renderFilterChips();
    return;
  }

  pageItems.forEach((book, index) => {
    const tr = document.createElement('tr');
    const cover = book.AnhBia || '/images/books/placeholder-book.svg';
    const status = bookStatus(book);
    const stt = (state.currentPage - 1) * state.pageSize + index + 1;

    tr.innerHTML = `
      <td>${stt}</td>
      <td><img src="${cover}" class="admin-book-thumb" alt="${book.TenSach}" onerror="this.src='/images/books/placeholder-book.svg'" /></td>
      <td><div class="fw-semibold">${book.TenSach}</div></td>
      <td>${book.NguonGoc || '-'}</td>
      <td>${book.TheLoai || '-'}</td>
      <td>${book.SoQuyen ?? 0}</td>
      <td><span class="badge ${status.klass}">${status.label}</span></td>
      <td>
        <button class="btn action-icon-btn view" title="Xem"><i class="bi bi-eye"></i></button>
        <button class="btn action-icon-btn edit" title="Sửa"><i class="bi bi-pencil"></i></button>
        <button class="btn action-icon-btn delete" title="Xóa"><i class="bi bi-trash"></i></button>
      </td>
    `;

    tr.querySelector('.view').addEventListener('click', () => alert(`${book.TenSach}\nTác giả: ${book.NguonGoc || '-'}`));
    tr.querySelector('.edit').addEventListener('click', () => openEditModal(book));
    tr.querySelector('.delete').addEventListener('click', async () => {
      if (!window.confirm(`Xóa sách ${book.TenSach}?`)) return;
      await api(`/api/admin/books/${book.MaSach}`, { method: 'DELETE' });
      await loadBooks();
    });

    booksTable.appendChild(tr);
  });

  countLabel.textContent = `Hiển thị ${(state.currentPage - 1) * state.pageSize + 1}-${Math.min(
    state.currentPage * state.pageSize,
    filtered.length
  )} / ${filtered.length} sách`;

  renderPagination(filtered.length);
  renderFilterChips();
}

function fillForm(book = null) {
  getField('book-id').value = book?.MaSach || '';
  getField('book-id').disabled = Boolean(book);
  getField('book-name').value = book?.TenSach || '';
  getField('book-author').value = book?.NguonGoc || '';
  getField('book-category').value = book?.TheLoai || '';
  getField('book-qty').value = book?.SoQuyen ?? 0;
  getField('book-price').value = book?.DonGia ?? 0;
  getField('book-year').value = book?.NamXuatBan ?? 2024;
  getField('book-publisher').value = book?.MaNXB || 'NXB01';
  getField('book-cover').value = book?.AnhBia || '/images/books/placeholder-book.svg';
  getField('book-description').value = book?.MoTa || '';
}

function openAddModal() {
  state.editingId = null;
  modalTitle.textContent = 'Thêm sách';
  submitBtn.textContent = 'Lưu sách';
  fillForm();
}

function openEditModal(book) {
  state.editingId = book.MaSach;
  modalTitle.textContent = `Sửa sách ${book.MaSach}`;
  submitBtn.textContent = 'Cập nhật';
  fillForm(book);
  bookModal.show();
}

bookModalEl.addEventListener('show.bs.modal', () => {
  if (!state.editingId) openAddModal();
});

bookForm.addEventListener('submit', async (event) => {
  event.preventDefault();

  const payload = {
    MaSach: getField('book-id').value.trim(),
    TenSach: getField('book-name').value.trim(),
    NguonGoc: getField('book-author').value.trim(),
    TheLoai: getField('book-category').value.trim(),
    SoQuyen: Number(getField('book-qty').value || 0),
    DonGia: Number(getField('book-price').value || 0),
    NamXuatBan: Number(getField('book-year').value || 2024),
    MaNXB: getField('book-publisher').value.trim() || 'NXB01',
    AnhBia: getField('book-cover').value.trim() || '/images/books/placeholder-book.svg',
    MoTa: getField('book-description').value.trim()
  };

  if (!payload.MaSach || !payload.TenSach) {
    alert('Vui lòng nhập mã sách và tên sách.');
    return;
  }

  if (state.editingId) {
    await api(`/api/admin/books/${state.editingId}`, {
      method: 'PUT',
      body: JSON.stringify(payload)
    });
  } else {
    await api('/api/admin/books', {
      method: 'POST',
      body: JSON.stringify(payload)
    });
  }

  bookModal.hide();
  await loadBooks();
});

async function loadBooks() {
  const data = await api('/api/admin/books');
  state.books = data.books || [];
  updateStats();

  const categories = Array.from(new Set(state.books.map((book) => book.TheLoai || 'Chưa phân loại'))).sort((a, b) => a.localeCompare(b, 'vi'));
  categoryFilter.innerHTML = '<option value="all">Tất cả danh mục</option>';
  categories.forEach((category) => {
    const option = document.createElement('option');
    option.value = category;
    option.textContent = category;
    categoryFilter.appendChild(option);
  });

  renderTable();
}

searchInput.addEventListener('input', () => {
  state.query = searchInput.value;
  state.currentPage = 1;
  renderTable();
});

categoryFilter.addEventListener('change', () => {
  state.category = categoryFilter.value;
  state.currentPage = 1;
  renderTable();
});

statusFilter.addEventListener('change', () => {
  state.status = statusFilter.value;
  state.currentPage = 1;
  renderTable();
});

document.getElementById('clear-book-filters').addEventListener('click', () => {
  state.query = '';
  state.category = 'all';
  state.status = 'all';
  state.currentPage = 1;
  searchInput.value = '';
  categoryFilter.value = 'all';
  statusFilter.value = 'all';
  renderTable();
});

document.getElementById('advanced-filter-btn').addEventListener('click', () => {
  alert('Bạn có thể kết hợp tìm kiếm văn bản + giọng nói + lọc danh mục + trạng thái.');
});

voiceBtn.addEventListener('click', () => {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    alert('Trình duyệt không hỗ trợ tìm kiếm giọng nói.');
    return;
  }

  const recognition = new SpeechRecognition();
  recognition.lang = 'vi-VN';
  recognition.interimResults = false;
  recognition.maxAlternatives = 1;

  voiceStatus.textContent = 'Đang nghe...';
  recognition.start();

  recognition.onresult = (event) => {
    const transcript = event.results?.[0]?.[0]?.transcript || '';
    searchInput.value = transcript;
    state.query = transcript;
    state.currentPage = 1;
    renderTable();
    voiceStatus.textContent = `Đã nhận: "${transcript}"`;
  };

  recognition.onerror = () => {
    voiceStatus.textContent = 'Không nhận diện được giọng nói. Vui lòng thử lại.';
  };

  recognition.onend = () => {
    if (!voiceStatus.textContent.includes('Đã nhận')) {
      voiceStatus.textContent = '';
    }
  };
});

loadBooks();
