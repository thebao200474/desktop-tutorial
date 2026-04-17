const form = document.getElementById('admin-login-form');
const emailInput = document.getElementById('admin-email');
const passwordInput = document.getElementById('admin-password');
const alertBox = document.getElementById('admin-login-alert');
const submitBtn = document.getElementById('admin-login-submit');
const togglePasswordBtn = document.getElementById('toggle-password');
const rememberCheckbox = document.getElementById('remember-login');
const passwordError = document.getElementById('password-error');

function showAlert(message) {
  alertBox.textContent = message;
  alertBox.classList.remove('d-none');
}

function clearAlert() {
  alertBox.textContent = '';
  alertBox.classList.add('d-none');
}

function setLoading(loading) {
  submitBtn.disabled = loading;
  submitBtn.textContent = loading ? 'Đang đăng nhập...' : 'Đăng nhập';
}

function validateForm() {
  let valid = true;
  passwordError.textContent = '';

  const email = emailInput.value.trim();
  if (!/^\S+@\S+\.\S+$/.test(email)) {
    emailInput.classList.add('is-invalid');
    valid = false;
  } else {
    emailInput.classList.remove('is-invalid');
  }

  if (!passwordInput.value) {
    passwordError.textContent = 'Vui lòng nhập mật khẩu.';
    passwordInput.classList.add('is-invalid');
    valid = false;
  } else {
    passwordInput.classList.remove('is-invalid');
  }

  return valid;
}

function preloadRememberedEmail() {
  const rememberedEmail = localStorage.getItem('adminRememberEmail') || '';
  if (rememberedEmail) {
    emailInput.value = rememberedEmail;
    rememberCheckbox.checked = true;
  }
}

form.addEventListener('submit', async (event) => {
  event.preventDefault();
  clearAlert();

  if (!validateForm()) return;

  setLoading(true);
  try {
    const res = await fetch('/api/admin/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: emailInput.value.trim(), password: passwordInput.value })
    });

    const data = await res.json();
    if (!res.ok || !data.success || !data.token) {
      showAlert(data.message || 'Đăng nhập admin thất bại.');
      return;
    }

    localStorage.setItem('adminToken', data.token);
    localStorage.setItem('adminProfile', JSON.stringify(data.profile || null));

    if (rememberCheckbox.checked) {
      localStorage.setItem('adminRememberEmail', emailInput.value.trim());
    } else {
      localStorage.removeItem('adminRememberEmail');
    }

    window.location.href = '/admin.html';
  } catch (error) {
    showAlert('Không thể kết nối máy chủ. Vui lòng thử lại.');
  } finally {
    setLoading(false);
  }
});

togglePasswordBtn.addEventListener('click', () => {
  const show = passwordInput.type === 'password';
  passwordInput.type = show ? 'text' : 'password';
  togglePasswordBtn.textContent = show ? '🙈' : '👁';
});

preloadRememberedEmail();
