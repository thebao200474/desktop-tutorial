// Khai báo hằng số cho các nhóm nguyên tố và màu sắc tương ứng
const CATEGORY_INFO = {
  "alkali-metal": { labelVi: "Kim loại kiềm", labelEn: "Alkali metal", colorClass: "cat-alkali-metal" },
  "alkaline-earth-metal": { labelVi: "Kim loại kiềm thổ", labelEn: "Alkaline earth metal", colorClass: "cat-alkaline-earth-metal" },
  "transition-metal": { labelVi: "Kim loại chuyển tiếp", labelEn: "Transition metal", colorClass: "cat-transition-metal" },
  "post-transition-metal": { labelVi: "Kim loại hậu chuyển tiếp", labelEn: "Post-transition metal", colorClass: "cat-post-transition-metal" },
  "metalloid": { labelVi: "Á kim", labelEn: "Metalloid", colorClass: "cat-metalloid" },
  "nonmetal": { labelVi: "Phi kim", labelEn: "Nonmetal", colorClass: "cat-nonmetal" },
  "halogen": { labelVi: "Halogen", labelEn: "Halogen", colorClass: "cat-halogen" },
  "noble-gas": { labelVi: "Khí hiếm", labelEn: "Noble gas", colorClass: "cat-noble-gas" },
  "lanthanide": { labelVi: "Lantan", labelEn: "Lanthanide", colorClass: "cat-lanthanide" },
  "actinide": { labelVi: "Actini", labelEn: "Actinide", colorClass: "cat-actinide" }
};

// Khai báo fallback ảnh dạng SVG base64 để dùng khi ảnh bị lỗi
const FALLBACK_IMAGE =
  "data:image/svg+xml;base64," +
  btoa(
    `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 150" fill="none">` +
      `<rect width="200" height="150" rx="16" fill="#1f2937"/>` +
      `<text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" fill="#9ca3af" font-family="Inter, sans-serif" font-size="16">Không có ảnh</text>` +
    `</svg>`
  );

// Đọc các phần tử DOM cần thao tác
const tableEl = document.getElementById("periodic-table");
const legendEl = document.getElementById("legend");
const searchInput = document.getElementById("search");
const categorySelect = document.getElementById("category");
const toggleLanguageBtn = document.getElementById("toggle-language");
const detailPanel = document.getElementById("detail-panel");
const panelOverlay = document.getElementById("panel-overlay");
const closePanelBtn = document.getElementById("close-panel");
const detailName = document.getElementById("detail-name");
const detailSymbol = document.getElementById("detail-symbol");
const detailNumber = document.getElementById("detail-number");
const detailWeight = document.getElementById("detail-weight");
const detailConfig = document.getElementById("detail-config");
const detailDescription = document.getElementById("detail-description");
const detailImage = document.getElementById("detail-image");
const detailApplications = document.getElementById("detail-applications");

// Biến toàn cục lưu dữ liệu nguyên tố và trạng thái hiện tại
let elementsData = [];
let currentLanguage = localStorage.getItem("pt-language") || "vi";
let currentCategory = localStorage.getItem("pt-category") || "";
let searchTerm = "";
let activeElementId = null;

// Hàm tiện ích debounce để tối ưu nhập liệu tìm kiếm
function debounce(fn, delay = 300) {
  let timeoutId;
  return (...args) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn(...args), delay);
  };
}

// Hàm tạo chú giải màu sắc từ CATEGORY_INFO
function renderLegend() {
  legendEl.innerHTML = "";
  Object.entries(CATEGORY_INFO).forEach(([key, info]) => {
    const item = document.createElement("div");
    item.className = "legend-item";

    const swatch = document.createElement("span");
    swatch.className = `legend-swatch ${info.colorClass}`;
    item.appendChild(swatch);

    const label = document.createElement("span");
    label.textContent = currentLanguage === "vi" ? info.labelVi : info.labelEn;
    item.appendChild(label);

    legendEl.appendChild(item);
  });
}

// Hàm bổ sung các lựa chọn lọc loại nguyên tố vào select
function populateCategoryOptions() {
  categorySelect.innerHTML = "";
  const allOption = document.createElement("option");
  allOption.value = "";
  allOption.textContent = "Tất cả loại nguyên tố";
  categorySelect.appendChild(allOption);

  Object.entries(CATEGORY_INFO).forEach(([key, info]) => {
    const option = document.createElement("option");
    option.value = key;
    option.textContent = info.labelVi;
    categorySelect.appendChild(option);
  });

  if (currentCategory && CATEGORY_INFO[currentCategory]) {
    categorySelect.value = currentCategory;
  }
}

// Hàm tạo một thẻ nguyên tố trong grid
function createElementCard(element) {
  const card = document.createElement("button");
  card.type = "button";
  card.className = `element ${CATEGORY_INFO[element.category]?.colorClass || ""}`;
  card.style.gridColumn = String(element.group || 1);
  card.style.gridRow = String(element.period || 1);
  card.dataset.number = element.number;
  card.dataset.category = element.category || "";
  card.dataset.symbol = element.symbol || "";
  card.dataset.nameVi = element.name_vi || "";
  card.dataset.nameEn = element.name_en || "";

  card.innerHTML = `
    <div class="number">${element.number ?? ""}</div>
    <div class="symbol">${element.symbol ?? ""}</div>
    <div class="name">${currentLanguage === "vi" ? element.name_vi ?? "" : element.name_en ?? ""}</div>
  `;

  card.addEventListener("click", () => openDetail(element));
  card.addEventListener("keydown", (evt) => {
    if (evt.key === "Enter" || evt.key === " ") {
      evt.preventDefault();
      openDetail(element);
    }
  });

  return card;
}

// Hàm dựng toàn bộ bảng tuần hoàn dựa trên dữ liệu hiện tại
function renderElements() {
  tableEl.innerHTML = "";
  elementsData.forEach((element) => {
    if (!element || !element.group || !element.period) return;
    const card = createElementCard(element);
    tableEl.appendChild(card);
  });
  applyFilters();
}

// Hàm hiển thị tên theo ngôn ngữ khi chuyển đổi VI/EN
function updateElementNames() {
  tableEl.querySelectorAll(".element").forEach((card) => {
    const nameField = card.querySelector(".name");
    if (!nameField) return;
    nameField.textContent = currentLanguage === "vi" ? card.dataset.nameVi : card.dataset.nameEn;
  });
  renderLegend();
  if (activeElementId) {
    const element = elementsData.find((el) => String(el.number) === String(activeElementId));
    if (element) {
      openDetail(element, false);
    }
  }
}

// Hàm áp dụng tìm kiếm và lọc
function applyFilters() {
  const normalizedSearch = searchTerm.trim().toLowerCase();
  tableEl.querySelectorAll(".element").forEach((card) => {
    const matchesCategory = !currentCategory || card.dataset.category === currentCategory;
    const nameMatchSource = currentLanguage === "vi" ? card.dataset.nameVi : card.dataset.nameEn;
    const matchesSearch =
      !normalizedSearch ||
      card.dataset.symbol.toLowerCase().includes(normalizedSearch) ||
      nameMatchSource.toLowerCase().includes(normalizedSearch);

    if (matchesCategory && matchesSearch) {
      card.classList.remove("hidden");
    } else {
      card.classList.add("hidden");
    }
  });
}

// Hàm mở panel chi tiết
function openDetail(element, focusPanel = true) {
  activeElementId = element.number;
  detailPanel.classList.add("active");
  detailPanel.setAttribute("aria-hidden", "false");
  panelOverlay.hidden = false;

  detailName.textContent = currentLanguage === "vi" ? (element.name_vi || "") : (element.name_en || "");
  detailSymbol.textContent = element.symbol ? `Ký hiệu: ${element.symbol}` : "";
  detailNumber.textContent = element.number ? `Số nguyên tử: ${element.number}` : "";
  detailWeight.textContent = element.atomic_weight ? `Nguyên tử khối: ${element.atomic_weight}` : "";
  detailConfig.textContent = element.electron_configuration ? `Cấu hình electron: ${element.electron_configuration}` : "";
  detailDescription.textContent = currentLanguage === "vi"
    ? (element.description_vi || "Đang cập nhật")
    : (element.description_en || "Updating soon");

  detailApplications.innerHTML = "";
  if (Array.isArray(element.applications) && element.applications.length > 0) {
    element.applications.forEach((app) => {
      const pill = document.createElement("span");
      pill.textContent = app;
      detailApplications.appendChild(pill);
    });
  } else {
    const pill = document.createElement("span");
    pill.textContent = currentLanguage === "vi" ? "Ứng dụng đang cập nhật" : "Applications coming soon";
    detailApplications.appendChild(pill);
  }

  detailImage.src = element.image || FALLBACK_IMAGE;
  detailImage.alt = element.symbol ? `Minh họa ${element.symbol}` : "Minh họa nguyên tố";
  detailImage.onerror = () => {
    detailImage.onerror = null;
    detailImage.src = FALLBACK_IMAGE;
  };

  if (focusPanel) {
    detailPanel.focus({ preventScroll: false });
  }
}

// Hàm đóng panel chi tiết
function closeDetail() {
  activeElementId = null;
  detailPanel.classList.remove("active");
  detailPanel.setAttribute("aria-hidden", "true");
  panelOverlay.hidden = true;
}

// Hàm xử lý chuyển đổi ngôn ngữ VI/EN
function toggleLanguage() {
  currentLanguage = currentLanguage === "vi" ? "en" : "vi";
  localStorage.setItem("pt-language", currentLanguage);
  toggleLanguageBtn.setAttribute("aria-pressed", currentLanguage === "en");
  updateElementNames();
}

// Hàm xử lý thay đổi bộ lọc theo danh mục
function onCategoryChange(value) {
  currentCategory = value;
  localStorage.setItem("pt-category", currentCategory);
  applyFilters();
}

// Khởi động ứng dụng sau khi DOM sẵn sàng
async function init() {
  toggleLanguageBtn.setAttribute("aria-pressed", currentLanguage === "en");
  renderLegend();
  populateCategoryOptions();

  try {
    const response = await fetch("data/elements.json", { cache: "no-store" });
    if (!response.ok) {
      throw new Error(`Không thể tải dữ liệu (${response.status})`);
    }
    const data = await response.json();
    if (!Array.isArray(data)) {
      throw new Error("Dữ liệu không hợp lệ");
    }
    elementsData = data;
    renderElements();
    applyFilters();
  } catch (error) {
    console.error("Lỗi tải dữ liệu:", error);
    tableEl.innerHTML = `<p>Không thể tải dữ liệu nguyên tố. Vui lòng kiểm tra lại tệp JSON.</p>`;
  }
}

// Gắn sự kiện cho thanh tìm kiếm với debounce
const onSearchInput = debounce((value) => {
  searchTerm = value;
  applyFilters();
});

searchInput.addEventListener("input", (event) => onSearchInput(event.target.value));
categorySelect.addEventListener("change", (event) => onCategoryChange(event.target.value));
toggleLanguageBtn.addEventListener("click", toggleLanguage);
closePanelBtn.addEventListener("click", closeDetail);
panelOverlay.addEventListener("click", closeDetail);

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    closeDetail();
  }
});

document.addEventListener("click", (event) => {
  if (!detailPanel.contains(event.target) && !tableEl.contains(event.target)) {
    closeDetail();
  }
});

// Khởi chạy
init();
