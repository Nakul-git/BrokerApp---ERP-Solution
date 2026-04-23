(function(){
    function initSelectionMode(){
        const leftPanel = document.querySelector('.left-panel');
        const filterInput = document.getElementById('filterInput');
        const rightPanel = document.querySelector('.right-panel');
        if (!leftPanel || !filterInput) return;
        if (document.body) {
            document.body.dataset.selectionMode = "1";
        }

        // Toggle a class on the left panel while search input is focused
        filterInput.addEventListener('focus', function(){
            leftPanel.classList.remove('keyboard-active');
            leftPanel.classList.add('search-active');
        });
        filterInput.addEventListener('blur', function(){
            setTimeout(function(){ leftPanel.classList.remove('search-active'); }, 0);
        });

        // Keyboard activation helper
        let keyboardTimer = null;
        function activateKeyboardPanel(timeout){
            if (leftPanel.classList.contains('add-inactive')) return;
            leftPanel.classList.add('keyboard-active');
            try { filterInput.blur(); } catch (e) {}
            if (keyboardTimer) clearTimeout(keyboardTimer);
            keyboardTimer = setTimeout(function(){
                leftPanel.classList.remove('keyboard-active');
                keyboardTimer = null;
            }, timeout || 1500);
        }


        // Sync add-mode inactivity from right-panel class
        function syncAddInactive(){
            if (!rightPanel) return;
            const isAdd = rightPanel.classList.contains('add-mode');
            if (isAdd) {
                leftPanel.classList.add('add-inactive');
                leftPanel.classList.remove('search-active', 'keyboard-active');
                try { filterInput.blur(); } catch (e) {}
            } else {
                leftPanel.classList.remove('add-inactive');
            }
        }

        if (rightPanel) {
            const obs = new MutationObserver(syncAddInactive);
            obs.observe(rightPanel, { attributes: true, attributeFilter: ['class'] });
            syncAddInactive();
        }

        // Centralized selection filtering (search input)
        const listWrap = leftPanel.querySelector('.list-wrap');
        const listContainer = listWrap ? listWrap.querySelector('[id]') : null;
        let emptyRow = null;

        function ensureEmptyRow(){
            if (!listContainer) return null;
            if (emptyRow && emptyRow.parentElement === listContainer) return emptyRow;
            emptyRow = document.createElement('div');
            emptyRow.className = 'selection-empty';
            emptyRow.textContent = 'No records found';
            emptyRow.style.display = 'none';
            listContainer.appendChild(emptyRow);
            return emptyRow;
        }

        function applyFilter(){
            if (!listContainer || !filterInput) return;
            const keyword = (filterInput.value || '').trim().toLowerCase();
            const rows = Array.from(listContainer.querySelectorAll('[data-id]'));

            if (!rows.length) {
                if (emptyRow) emptyRow.style.display = 'none';
                return;
            }

            let visibleCount = 0;
            rows.forEach(function(row){
                const text = (row.textContent || '').trim().toLowerCase();
                const match = !keyword || text.indexOf(keyword) !== -1;
                row.style.display = match ? '' : 'none';
                if (match) visibleCount += 1;
            });

            const placeholder = ensureEmptyRow();
            if (placeholder) {
                placeholder.style.display = (keyword && visibleCount === 0) ? '' : 'none';
            }
        }

        if (filterInput) {
            filterInput.addEventListener('input', applyFilter);
        }

        if (listContainer) {
            const listObserver = new MutationObserver(function(){
                applyFilter();
            });
            listObserver.observe(listContainer, { childList: true, subtree: true });
        }

        // Type-ahead search (Selection Mode) inspired by state.html
        let typeBuffer = '';
        let typeTimer = null;
        let lastMatchedIndex = -1;

        function resetTypeBuffer(){
            typeBuffer = '';
            lastMatchedIndex = -1;
            if (typeTimer) {
                clearTimeout(typeTimer);
                typeTimer = null;
            }
        }

        function isDisplayMode(){
            return !!(rightPanel && rightPanel.classList.contains('display-mode'));
        }

        function getSelectionRows(){
            const listWrap = leftPanel.querySelector('.list-wrap');
            if (!listWrap) return [];
            return Array.from(listWrap.querySelectorAll('[data-id]')).filter(function(row){
                if (!row.dataset || !row.dataset.id) return false;
                if (row.offsetParent === null) return false;
                return (row.textContent || '').trim().length > 0;
            });
        }

        function activateRow(row){
            if (!row) return;
            if (typeof row.click === 'function') {
                row.click();
                return;
            }
            try {
                row.dispatchEvent(new MouseEvent('click', { bubbles: true }));
            } catch (e) {}
        }

        function scrollActiveRow(){
            const rows = getSelectionRows();
            if (!rows.length) return;
            const activeRow = rows.find(function(row){
                return row.classList.contains('active');
            });
            if (activeRow && typeof activeRow.scrollIntoView === 'function') {
                activeRow.scrollIntoView({ block: 'nearest' });
            }
        }

        filterInput.addEventListener('focus', resetTypeBuffer);

        document.addEventListener('keydown', function(e){
            if (!isDisplayMode()) return;
            if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp') return;

            activateKeyboardPanel();
            if (e.defaultPrevented) return;

            const activeEl = document.activeElement;
            const isSearchBox = activeEl && activeEl === filterInput;
            if (!isSearchBox && activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.tagName === 'SELECT')) return;

            const rows = getSelectionRows();
            if (!rows.length) return;

            let currentIndex = rows.findIndex(function(row){
                return row.classList.contains('active');
            });
            if (currentIndex === -1) currentIndex = 0;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex < rows.length - 1) currentIndex += 1;
                activateRow(rows[currentIndex]);
                setTimeout(scrollActiveRow, 0);
            }

            if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) currentIndex -= 1;
                activateRow(rows[currentIndex]);
                setTimeout(scrollActiveRow, 0);
            }
        });

        document.addEventListener('keydown', function(e){
            if (!isDisplayMode()) return;
            if (e.ctrlKey || e.altKey || e.metaKey) return;

            const activeEl = document.activeElement;
            const isSearchBox = activeEl && activeEl === filterInput;
            if (!isSearchBox && activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.tagName === 'SELECT')) return;

            if (!(e.key.length === 1 && /^[a-zA-Z]$/.test(e.key))) return;

            const rows = getSelectionRows();
            if (!rows.length) return;

            if (isSearchBox) return;

            e.preventDefault();

            const letter = e.key.toLowerCase();
            if (typeTimer) clearTimeout(typeTimer);
            typeBuffer += letter;
            typeTimer = setTimeout(resetTypeBuffer, 700);

            let matchIndex = -1;
            if (typeBuffer.length === 1) {
                const startIndex = lastMatchedIndex + 1;
                matchIndex = rows.findIndex(function(row, index){
                    return index >= startIndex && row.textContent.trim().toLowerCase().startsWith(letter);
                });
                if (matchIndex === -1) {
                    matchIndex = rows.findIndex(function(row){
                        return row.textContent.trim().toLowerCase().startsWith(letter);
                    });
                }
            } else {
                matchIndex = rows.findIndex(function(row){
                    return row.textContent.trim().toLowerCase().startsWith(typeBuffer);
                });
            }

            if (matchIndex !== -1) {
                lastMatchedIndex = matchIndex;
                activateRow(rows[matchIndex]);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSelectionMode);
    } else {
        initSelectionMode();
    }
})();
