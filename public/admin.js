const adminState = { token: localStorage.getItem('adminToken') || '', profile: JSON.parse(localStorage.getItem('adminProfile') || 'null') };
if (!adminState.token) window.location.href = '/admin-login.html';

const qs = (id) => document.getElementById(id);
const adminGreeting = qs('admin-greeting');
let trafficChart = null;

async function api(path, options = {}) {
  const res = await fetch(path, { ...options, headers: { ...(options.headers || {}), Authorization: `Bearer ${adminState.token}` } });
  if (res.status === 401) { localStorage.removeItem('adminToken'); localStorage.removeItem('adminProfile'); window.location.href = '/admin-login.html'; return {}; }
  return res.json();
}

qs('admin-logout').addEventListener('click', () => {
  localStorage.removeItem('adminToken');
  localStorage.removeItem('adminProfile');
  window.location.href = '/admin-login.html';
});

function renderTrafficChart(labels = [], values = []) {
  const canvas = qs('traffic-chart');
  if (!canvas) return;
  if (trafficChart) trafficChart.destroy();

  trafficChart = new Chart(canvas, {
    type: 'line',
    data: { labels, datasets: [{ data: values, borderColor: '#94a3b8', backgroundColor: 'rgba(148,163,184,0.18)', fill: true, tension: 0, pointRadius: 2 }] },
    options: { responsive: true, maintainAspectRatio: false, animation: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
  });
}

async function loadSummary() {
  const data = await api('/api/admin/summary');
  qs('sum-books').textContent = Number(data.totalBooks || 0).toLocaleString('vi-VN');
  qs('sum-users').textContent = Number(data.totalUsers || 0).toLocaleString('vi-VN');
  qs('sum-reviews').textContent = Number(data.totalReviews || 0).toLocaleString('vi-VN');
  qs('sum-borrows').textContent = Number(data.totalBorrows || 0).toLocaleString('vi-VN');
  qs('sum-books-sub').textContent = data?.deltas?.books || '+52 so với tháng trước';
  qs('sum-users-sub').textContent = data?.deltas?.users || '+220 người đăng ký mới';
  qs('sum-reviews-sub').textContent = data?.deltas?.reviews || '+45 đánh giá mới';
  qs('sum-borrows-sub').textContent = data?.deltas?.borrows || '+12 cập nhật mới';
}

async function loadTraffic(days = 7) {
  const data = await api(`/api/admin/traffic?days=${days}`);
  renderTrafficChart(data.labels || [], data.values || []);
}

async function loadTopBooks() {
  const data = await api('/api/admin/top-books');
  qs('top-books-table').innerHTML = (data.items || []).map((x) => `<tr><td>${x.stt}</td><td>${x.tenSach}</td><td>${x.tacGia}</td><td>${x.ngayThem}</td></tr>`).join('');
}

qs('traffic-range').addEventListener('change', (e) => loadTraffic(Number(e.target.value || 7)));
adminGreeting.textContent = adminState.profile?.name ? `Xin chào, ${adminState.profile.name}` : 'Xin chào, Admin';
loadSummary();
loadTraffic(7);
loadTopBooks();
