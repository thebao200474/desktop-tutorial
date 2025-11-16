/**
 * Script xử lý hiển thị bảng tuần hoàn trên ChemLearn.
 */
(function () {
    'use strict';

    const elementsData = Array.isArray(window.CHEMLEARN_ELEMENTS) ? window.CHEMLEARN_ELEMENTS : [];
    const table = document.getElementById('periodic-grid');
    const detailName = document.getElementById('element-detail-name');
    const detailSymbol = document.getElementById('element-detail-symbol');
    const detailMass = document.getElementById('element-detail-mass');
    const detailType = document.getElementById('element-detail-type');
    const detailPlaceholder = document.getElementById('element-detail-placeholder');
    const detailContent = document.getElementById('element-detail-content');
    const searchInput = document.getElementById('element-search');
    const groupSelect = document.getElementById('element-group');
    const resetButton = document.getElementById('element-reset');

    if (!table) {
        return;
    }

    const positions = new Map();

    const setPosition = (number, row, column) => {
        positions.set(number, { row, column });
    };

    const setSequentialPositions = (start, end, row, startColumn) => {
        for (let atomic = start, column = startColumn; atomic <= end; atomic += 1, column += 1) {
            setPosition(atomic, row, column);
        }
    };

    setPosition(1, 1, 1);
    setPosition(2, 1, 18);

    setPosition(3, 2, 1);
    setPosition(4, 2, 2);
    setSequentialPositions(5, 10, 2, 13);

    setPosition(11, 3, 1);
    setPosition(12, 3, 2);
    setSequentialPositions(13, 18, 3, 13);

    setPosition(19, 4, 1);
    setPosition(20, 4, 2);
    setSequentialPositions(21, 30, 4, 3);
    setSequentialPositions(31, 36, 4, 13);

    setPosition(37, 5, 1);
    setPosition(38, 5, 2);
    setSequentialPositions(39, 48, 5, 3);
    setSequentialPositions(49, 54, 5, 13);

    setPosition(55, 6, 1);
    setPosition(56, 6, 2);
    setSequentialPositions(72, 80, 6, 4);
    setSequentialPositions(81, 86, 6, 13);

    setPosition(87, 7, 1);
    setPosition(88, 7, 2);
    setSequentialPositions(104, 112, 7, 4);
    setSequentialPositions(113, 118, 7, 13);

    setSequentialPositions(57, 71, 8, 4);
    setSequentialPositions(89, 103, 9, 4);

    const slugify = (value) => {
        if (!value) {
            return 'khong-xac-dinh';
        }
        return value
            .toString()
            .normalize('NFD')
            .replace(/\p{Diacritic}/gu, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '') || 'khong-xac-dinh';
    };

    const state = {
        query: '',
        group: 'all',
        selected: null,
    };

    const matchQuery = (element) => {
        if (!state.query) {
            return true;
        }
        const keyword = state.query;
        return element.name.toLowerCase().includes(keyword) || element.symbol.toLowerCase().includes(keyword);
    };

    const matchGroup = (element) => {
        if (state.group === 'all') {
            return true;
        }
        return slugify(element.type) === state.group;
    };

    const updateDetail = (element) => {
        if (!detailName || !detailSymbol || !detailMass || !detailType || !detailPlaceholder) {
            return;
        }

        if (!element) {
            detailName.textContent = '';
            detailSymbol.textContent = '';
            detailMass.textContent = '';
            detailType.textContent = '';
            detailPlaceholder.classList.remove('d-none');
            if (detailContent) {
                detailContent.classList.add('d-none');
            }
            return;
        }

        detailPlaceholder.classList.add('d-none');
        if (detailContent) {
            detailContent.classList.remove('d-none');
        }
        detailName.textContent = `${element.number}. ${element.name}`;
        detailSymbol.textContent = element.symbol;
        detailMass.textContent = element.mass;
        detailType.textContent = element.type;
    };

    const clearDetailIfFilteredOut = () => {
        const highlighted = table.querySelector('.element-tile.is-selected');
        if (!highlighted) {
            return;
        }
        const atomic = Number.parseInt(highlighted.dataset.number, 10);
        const stillVisible = elementsData.some((item) => item.number === atomic && matchQuery(item) && matchGroup(item));
        if (!stillVisible) {
            highlighted.classList.remove('is-selected');
            updateDetail(null);
            state.selected = null;
        }
    };

    const renderTable = () => {
        table.innerHTML = '';

        const fragment = document.createDocumentFragment();
        let selectedElement = null;

        elementsData.forEach((element) => {
            if (!matchQuery(element) || !matchGroup(element)) {
                return;
            }

            const tile = document.createElement('button');
            tile.type = 'button';
            tile.className = `element-tile group-${slugify(element.type)}`;
            tile.setAttribute('data-number', String(element.number));
            tile.setAttribute('title', `${element.name} (Z=${element.number})`);

            const position = positions.get(element.number);
            if (position) {
                tile.style.gridColumn = position.column;
                tile.style.gridRow = position.row;
            }

            tile.innerHTML = `
                <span class="atomic-number">${element.number}</span>
                <span class="symbol">${element.symbol}</span>
                <span class="mass">${element.mass}</span>
                <span class="name">${element.name}</span>
            `;

            tile.addEventListener('click', () => {
                table.querySelectorAll('.element-tile').forEach((node) => node.classList.remove('is-selected'));
                tile.classList.add('is-selected');
                state.selected = element.number;
                updateDetail(element);
            });

            tile.addEventListener('mouseenter', () => {
                if (!table.querySelector('.element-tile.is-selected')) {
                    updateDetail(element);
                }
            });

            if (state.selected === element.number) {
                tile.classList.add('is-selected');
                selectedElement = element;
            }

            fragment.appendChild(tile);
        });

        table.appendChild(fragment);

        if (!table.childElementCount) {
            const empty = document.createElement('div');
            empty.className = 'text-center text-muted fw-semibold py-3';
            empty.textContent = 'Không tìm thấy nguyên tố phù hợp với bộ lọc.';
            empty.style.gridColumn = '1 / -1';
            table.appendChild(empty);
            updateDetail(null);
            state.selected = null;
        } else {
            if (selectedElement) {
                updateDetail(selectedElement);
            } else {
                clearDetailIfFilteredOut();
            }
        }
    };

    if (searchInput) {
        searchInput.addEventListener('input', (event) => {
            state.query = event.target.value.trim().toLowerCase();
            renderTable();
        });
    }

    if (groupSelect) {
        groupSelect.addEventListener('change', (event) => {
            state.group = event.target.value;
            renderTable();
        });
    }

    if (resetButton) {
        resetButton.addEventListener('click', () => {
            state.query = '';
            state.group = 'all';
            state.selected = null;
            if (searchInput) {
                searchInput.value = '';
            }
            if (groupSelect) {
                groupSelect.value = 'all';
            }
            renderTable();
            updateDetail(null);
        });
    }

    renderTable();
})();
