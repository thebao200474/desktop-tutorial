const BOOK_DETAIL_MOCK = {
  S001: {
    id: 'S001',
    title: 'Đắc nhân tâm',
    author: 'Dale Carnegie',
    rating: 4.6,
    reviewCount: 2345,
    categories: ['Kỹ năng sống', 'Kinh tế'],
    publisher: 'Tổng hợp TP.HCM',
    publishYear: 2019,
    pages: 336,
    description:
      'Đắc nhân tâm là quyển sách nổi tiếng của Dale Carnegie, mang đến những nguyên tắc vàng giúp bạn giao tiếp hiệu quả, tạo thiện cảm và xây dựng mối quan hệ bền vững.',
    cover: '/images/books/dac-nhan-tam.svg',
    ratingBreakdown: { 5: 78, 4: 14, 3: 5, 2: 2, 1: 1 },
    reviews: [
      {
        user: 'Nguyễn Văn A',
        avatar: '/images/users/user-1.svg',
        rating: 5,
        comment: 'Rất hay và ý nghĩa!',
        date: '20/04/2024'
      }
    ]
  }
};

const params = new URLSearchParams(window.location.search);
const bookId = params.get('id') || 'S001';

const wrap = document.getElementById('book-detail-wrap');
const reviewList = document.getElementById('review-list');
const reviewForm = document.getElementById('review-form');
const intro = document.getElementById('book-intro');
const avgRating = document.getElementById('avg-rating');
const totalRatingText = document.getElementById('total-rating-text');
const ratingBreakdown = document.getElementById('rating-breakdown');
const featuredReview = document.getElementById('featured-review');
const breadcrumbTitle = document.getElementById('breadcrumb-title');

let currentBook = null;

const formatCount = (n) => Number(n || 0).toLocaleString('vi-VN');

async function loadBook() {
  const mock = BOOK_DETAIL_MOCK[bookId] || BOOK_DETAIL_MOCK.S001;
  try {
    const res = await fetch(`/api/books/${encodeURIComponent(bookId)}`);
    const data = await res.json();
    const b = data.book;
    if (b) {
      currentBook = {
        ...mock,
        id: b.MaSach,
        title: b.TenSach,
        author: b.NguonGoc || mock.author,
        rating: Number(mock.rating || b.avgRating || 0),
        reviewCount: Number(mock.reviewCount || b.totalReviews || 0),
        categories: b.TheLoai ? [b.TheLoai] : mock.categories,
        publisher: b.TenNXB || mock.publisher,
        publishYear: b.NamXuatBan || mock.publishYear,
        description: b.MoTa || mock.description,
        cover: b.AnhBia || mock.cover
      };
    } else {
      currentBook = mock;
    }
  } catch {
    currentBook = mock;
  }

  breadcrumbTitle.textContent = currentBook.title;
  intro.textContent = currentBook.description;

  wrap.innerHTML = `
    <div class="col-lg-4 text-center">
      <img src="${currentBook.cover}" class="img-fluid detail-cover" alt="${currentBook.title}" onerror="this.src='/images/books/placeholder-book.svg'">
    </div>
    <div class="col-lg-8">
      <h2 class="fw-bold mb-1">${currentBook.title}</h2>
      <p class="text-muted mb-2">${currentBook.author}</p>
      <p class="mb-2"><strong>${currentBook.rating.toFixed(1)}</strong> <span class="text-warning">★★★★★</span> <span class="text-muted">(${formatCount(currentBook.reviewCount)} đánh giá)</span></p>
      <p class="mb-1"><strong>Thể loại:</strong> ${currentBook.categories.join(', ')}</p>
      <p class="mb-1"><strong>Nhà xuất bản:</strong> ${currentBook.publisher}</p>
      <p class="mb-1"><strong>Năm xuất bản:</strong> ${currentBook.publishYear}</p>
      <p class="mb-3"><strong>Số trang:</strong> ${currentBook.pages}</p>

      <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-brown" id="read-now">📖 Đọc ngay</button>
        <button class="btn btn-outline-secondary" id="add-shelf">➕ Thêm vào giá sách</button>
        <button class="btn btn-outline-secondary" id="go-review">⭐ Đánh giá</button>
      </div>
    </div>
  `;

  document.getElementById('read-now').addEventListener('click', () => alert('Chức năng đọc thử đang được phát triển.'));
  document.getElementById('add-shelf').addEventListener('click', (e) => {
    e.target.textContent = '✓ Đã thêm vào giá sách';
    e.target.classList.remove('btn-outline-secondary');
    e.target.classList.add('btn-outline-success');
  });
  document.getElementById('go-review').addEventListener('click', () => reviewForm.scrollIntoView({ behavior: 'smooth' }));

  renderCommunity();
  loadReviews();
}

function renderCommunity() {
  avgRating.textContent = currentBook.rating.toFixed(1);
  totalRatingText.textContent = `${formatCount(currentBook.reviewCount)} đánh giá`;

  const breakdown = currentBook.ratingBreakdown || { 5: 70, 4: 18, 3: 7, 2: 3, 1: 2 };
  ratingBreakdown.innerHTML = [5, 4, 3, 2, 1]
    .map((star) => {
      const percent = breakdown[star] || 0;
      return `
      <div class="d-flex align-items-center gap-2 mb-2 small">
        <span style="width:48px">${star} sao</span>
        <div class="progress flex-grow-1" style="height:8px"><div class="progress-bar bg-warning" style="width:${percent}%"></div></div>
        <span class="text-muted" style="width:38px">${percent}%</span>
      </div>`;
    })
    .join('');

  const first = (currentBook.reviews && currentBook.reviews[0]) || {
    user: 'Nguyễn Văn A',
    avatar: '/images/users/user-1.svg',
    rating: 5,
    comment: 'Rất hay và ý nghĩa!',
    date: '20/04/2024'
  };

  featuredReview.innerHTML = `
    <img src="${first.avatar}" class="review-avatar" alt="${first.user}" onerror="this.src='/images/users/user-1.svg'">
    <div class="flex-grow-1">
      <div class="fw-semibold">${first.user} <span class="text-warning small">${'★'.repeat(first.rating)}</span></div>
      <div class="text-secondary">${first.comment}</div>
    </div>
    <div class="text-muted small">${first.date}</div>
  `;
}

async function loadReviews() {
  try {
    const res = await fetch(`/api/books/${encodeURIComponent(bookId)}/reviews`);
    const data = await res.json();
    if (data.reviews?.length) {
      reviewList.innerHTML = '';
      data.reviews.forEach((r) => {
        const li = document.createElement('li');
        li.className = 'list-group-item';
        li.innerHTML = `<strong>${r.DocGia || 'Ẩn danh'}</strong> - ${'⭐'.repeat(r.Diem)}<div class="small text-muted">${r.BinhLuan || ''}</div>`;
        reviewList.appendChild(li);
      });
      return;
    }
  } catch {
    // keep mock
  }

  reviewList.innerHTML = '';
  (currentBook.reviews || []).forEach((r) => {
    const li = document.createElement('li');
    li.className = 'list-group-item';
    li.innerHTML = `<strong>${r.user}</strong> - ${'⭐'.repeat(r.rating)}<div class="small text-muted">${r.comment}</div>`;
    reviewList.appendChild(li);
  });
}

reviewForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const token = localStorage.getItem('readerToken');
  if (!token) {
    alert('Vui lòng đăng nhập trước khi đánh giá.');
    return;
  }

  const payload = { Diem: Number(document.getElementById('rating').value), BinhLuan: document.getElementById('comment').value.trim() };
  const res = await fetch(`/api/books/${encodeURIComponent(bookId)}/reviews`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
    body: JSON.stringify(payload)
  });

  const data = await res.json();
  if (!res.ok) {
    alert(data.message || 'Không thể gửi đánh giá');
    return;
  }

  reviewForm.reset();
  loadReviews();
});

loadBook();
