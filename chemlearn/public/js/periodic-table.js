// Script bảng tuần hoàn ChemLearn – lọc theo ký hiệu và nhóm
(function () {
    // Hàm trợ giúp loại bỏ dấu tiếng Việt để tìm kiếm nhẹ nhàng
    const normalize = (value) => value
        .toLowerCase()
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '');

    const grid = document.querySelector('[data-periodic-grid]');
    if (!grid) {
        return; // Không có bảng -> không chạy gì thêm
    }

    const cells = Array.from(document.querySelectorAll('.periodic-cell'));
    const searchInput = document.getElementById('periodic-search');
    const filterSelect = document.getElementById('periodic-filter');

    const applyFilter = () => {
        const keyword = searchInput ? normalize(searchInput.value.trim()) : '';
        const filter = filterSelect ? filterSelect.value : '';

        cells.forEach((cell) => {
            const symbol = normalize(cell.dataset.symbol || '');
            const name = normalize(cell.dataset.name || '');
            const category = cell.dataset.category || '';

            const matchKeyword = !keyword || symbol.includes(keyword) || name.includes(keyword);
            const matchCategory = !filter || category === filter;

            cell.classList.toggle('is-hidden', !(matchKeyword && matchCategory));
        });
    };

    if (searchInput) {
        searchInput.addEventListener('input', applyFilter);
    }

    if (filterSelect) {
        filterSelect.addEventListener('change', applyFilter);
    }

    applyFilter();
})();
