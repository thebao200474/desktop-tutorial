/*
 * Gợi ý phương trình hóa học theo từ khóa người dùng nhập.
 * Toàn bộ chú thích sử dụng tiếng Việt để phù hợp tài liệu dự án.
 */
(function () {
    'use strict';

    // Lấy dữ liệu phương trình được truyền từ view (server-side)
    const equations = Array.isArray(window.ChemLearnEquationData)
        ? window.ChemLearnEquationData
        : [];

    const keywordPreset = typeof window.ChemLearnEquationKeyword === 'string'
        ? window.ChemLearnEquationKeyword
        : '';

    const input = document.querySelector('[data-equation-search]');
    const suggestionBox = document.querySelector('[data-equation-suggestions]');
    const searchForm = input ? input.closest('form') : null;

    if (!input || !suggestionBox || equations.length === 0) {
        return;
    }

    // Bảng chuyển đổi chỉ số dưới về dạng số thường và ngược lại
    const subToAscii = {
        '₀': '0', '₁': '1', '₂': '2', '₃': '3', '₄': '4',
        '₅': '5', '₆': '6', '₇': '7', '₈': '8', '₉': '9',
    };

    const asciiToSub = {
        '0': '₀', '1': '₁', '2': '₂', '3': '₃', '4': '₄',
        '5': '₅', '6': '₆', '7': '₇', '8': '₈', '9': '₉',
    };

    const normalise = (value) => value
        .toString()
        .trim()
        .toLocaleLowerCase('vi');

    const replaceDigits = (value, map) => value.replace(/[0-9₀-₉]/g, (char) => map[char] ?? char);

    // Kiểm tra xem phương trình có chứa từ khóa hay không
    const matchEquation = (equation, query) => {
        const haystack = [
            equation.phuong_trinh,
            equation.loai_phan_ung,
            equation.giai_thich,
            equation.nhom_phan_ung,
        ].map((text) => normalise(text || ''));

        const convertedHaystack = haystack.map((text) => replaceDigits(text, subToAscii));

        const queries = [
            query,
            replaceDigits(query, asciiToSub),
            replaceDigits(query, subToAscii),
        ].map((text) => normalise(text));

        return haystack.some((text) => queries.some((q) => q && text.includes(q)))
            || convertedHaystack.some((text) => queries.some((q) => q && text.includes(q)));
    };

    const buildSuggestions = (query) => {
        const trimmed = query.trim();
        if (!trimmed) {
            return [];
        }

        return equations
            .filter((equation) => matchEquation(equation, trimmed))
            .slice(0, 6);
    };

    const clearSuggestions = () => {
        suggestionBox.innerHTML = '';
        suggestionBox.classList.add('d-none');
    };

    const renderSuggestions = (items) => {
        if (!items.length) {
            clearSuggestions();
            return;
        }

        suggestionBox.innerHTML = '';
        items.forEach((item) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'list-group-item list-group-item-action';
            button.textContent = `${item.phuong_trinh} — ${item.loai_phan_ung}`;

            button.addEventListener('click', () => {
                input.value = item.phuong_trinh;
                clearSuggestions();
                if (searchForm) {
                    searchForm.submit();
                }
            });

            suggestionBox.appendChild(button);
        });

        suggestionBox.classList.remove('d-none');
    };

    const debounce = (fn, delay = 200) => {
        let timer = null;
        return (...args) => {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => fn.apply(null, args), delay);
        };
    };

    const updateSuggestions = debounce(() => {
        renderSuggestions(buildSuggestions(input.value));
    }, 150);

    input.addEventListener('input', updateSuggestions);
    input.addEventListener('focus', () => {
        if (input.value.trim() !== '') {
            renderSuggestions(buildSuggestions(input.value));
        }
    });

    document.addEventListener('click', (event) => {
        if (!suggestionBox.contains(event.target) && event.target !== input) {
            clearSuggestions();
        }
    });

    if (keywordPreset && keywordPreset.trim() !== '') {
        renderSuggestions(buildSuggestions(keywordPreset));
    }
})();
