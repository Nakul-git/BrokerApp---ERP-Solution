(function () {
    function ensureStyles() {
        if (document.querySelector('style[data-delete-mode="1"]')) return;
        const style = document.createElement('style');
        style.setAttribute('data-delete-mode', '1');
        style.textContent = `
.simple-modal{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.4);
  display:flex;
  align-items:center;
  justify-content:center;
  z-index:9999;
}
.simple-modal-box{
  background:#1f2937;
  color:#fff;
  padding:20px;
  width:320px;
  border-radius:10px;
}
.simple-modal-buttons{
  margin-top:15px;
  text-align:right;
}
.simple-modal-buttons button{
  margin-left:10px;
  padding:5px 15px;
  cursor:pointer;
}
.delete-mode-label{
  margin-top:10px;
  font-weight:700;
  word-break:break-word;
}
`;
        document.head.appendChild(style);
    }

    function getActiveSelectionLabel() {
        const blacklist = new Set(["NAME", "CODE", "TITLE", "DESCRIPTION", "TYPE"]);

        function extractLabel(el) {
            if (!el) return "";
            const ds = el.dataset || {};
            const byData = ds.label || ds.name || ds.value || ds.text;
            if (byData) return String(byData).trim();
            let text = (el.textContent || "").trim();
            if (!text) return "";
            const parts = text.split(/\r?\n/).map(t => t.trim()).filter(Boolean);
            for (let i = 0; i < parts.length; i += 1) {
                const part = parts[i];
                if (!blacklist.has(part.toUpperCase())) return part;
            }
            return "";
        }

        function findActive() {
            const selectors = [
                ".left-panel .list-wrap .active",
                ".left-panel .list-wrap .selected",
                ".left-panel .list-wrap [aria-selected='true']",
                ".left-panel .active",
                ".left-panel .selected",
                ".left-panel [aria-selected='true']",
                ".selection-list .active",
                ".selection-list .selected",
                "[data-selection='active']"
            ];
            for (let i = 0; i < selectors.length; i += 1) {
                const el = document.querySelector(selectors[i]);
                if (el) return el;
            }

            const containers = Array.from(document.querySelectorAll(
                ".list-wrap, .selection-list, .listbox, [id$='List'], [id$='list']"
            ));
            for (let i = 0; i < containers.length; i += 1) {
                const container = containers[i];
                const el = container.querySelector(".active, .selected, [aria-selected='true']");
                if (el) return el;
            }
            return null;
        }

        const active = findActive();
        const activeLabel = extractLabel(active);
        if (activeLabel) return activeLabel;

        const anyRow = document.querySelector(".left-panel .list-wrap [data-id], .left-panel .list-wrap [data-key]");
        if (anyRow) {
            const list = anyRow.closest("#areaList, #noteList, .list-wrap, .selection-list, .listbox");
            if (list) {
                const preferred = list.querySelector(".active, .selected, [aria-selected='true']") || list.querySelector("[data-id], [data-key]");
                const listLabel = extractLabel(preferred);
                if (listLabel) return listLabel;
            }
        }

        const rightPanel = document.querySelector(".right-panel");
        if (!rightPanel) return "";
        const fieldSelectors = [
            "input[id$='_name']",
            "input[name$='_name']",
            "input[id$='_description']",
            "input[name$='_description']",
            "textarea[id$='_description']",
            "textarea[name$='_description']",
            "input[type='text']",
            "textarea"
        ];
        for (let i = 0; i < fieldSelectors.length; i += 1) {
            const field = rightPanel.querySelector(fieldSelectors[i]);
            if (!field) continue;
            const value = (field.value || "").trim();
            if (value && !blacklist.has(value.toUpperCase())) return value;
        }
        const select = rightPanel.querySelector("select");
        if (select && select.selectedOptions && select.selectedOptions.length) {
            const optionText = (select.selectedOptions[0].textContent || "").trim();
            if (optionText && !blacklist.has(optionText.toUpperCase())) return optionText;
        }
        return "";
    }

    function escapeHtml(value) {
        return String(value || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    function formatMessage(message) {
        const base = String(message || "");
        if (document.body && document.body.dataset && document.body.dataset.suppressDeleteLabel === "1") {
            return escapeHtml(base);
        }
        if (/logout/i.test(base)) {
            return escapeHtml(base);
        }
        const label = getActiveSelectionLabel();
        if (!label) return escapeHtml(base);
        const labelLower = label.toLowerCase();
        const baseLower = base.toLowerCase();
        if (label.length > 2) {
            const wordMatch = new RegExp("\\b" + labelLower.replace(/[.*+?^${}()|[\\]\\\\]/g, "\\$&") + "\\b", "i");
            if (wordMatch.test(baseLower)) return escapeHtml(base);
        }
        return escapeHtml(base) + "<div class=\"delete-mode-label\">" + escapeHtml(label) + "</div>";
    }

    function deleteModeConfirm(message, onConfirm, onCancel) {
        ensureStyles();
        const displayMessage = formatMessage(message);
        const modal = document.createElement('div');
        modal.className = 'simple-modal';
        modal.innerHTML = `
<div class="simple-modal-box">
  <div>${displayMessage}</div>
  <div class="simple-modal-buttons">
    <button id="modalCancel">Cancel</button>
    <button id="modalOk">OK</button>
  </div>
</div>`;
        document.body.appendChild(modal);

        const cancelBtn = modal.querySelector('#modalCancel');
        const okBtn = modal.querySelector('#modalOk');
        const buttons = [cancelBtn, okBtn];
        let activeIndex = 0;

        cancelBtn.focus();

        function closeModal() {
            document.removeEventListener('keydown', keyHandler, true);
            modal.remove();
        }

        function keyHandler(e) {
            e.stopPropagation();
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = activeIndex === 0 ? 1 : 0;
                buttons[activeIndex].focus();
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                closeModal();
                if (activeIndex === 1 && typeof onConfirm === 'function') onConfirm();
                if (activeIndex === 0 && typeof onCancel === 'function') onCancel();
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                closeModal();
                if (typeof onCancel === 'function') onCancel();
            }
        }

        document.addEventListener('keydown', keyHandler, true);

        cancelBtn.onclick = function () {
            closeModal();
            if (typeof onCancel === 'function') onCancel();
        };
        okBtn.onclick = function () {
            closeModal();
            if (typeof onConfirm === 'function') onConfirm();
        };
    }

    function deleteModeConfirmPromise(message) {
        return new Promise(function (resolve) {
            deleteModeConfirm(message, function () { resolve(true); }, function () { resolve(false); });
        });
    }

    window.deleteModeConfirm = deleteModeConfirm;
    window.deleteModeConfirmPromise = deleteModeConfirmPromise;
    window.confirmDelete = async function (message) {
        return await window.deleteModeConfirmPromise(message);
    };
})();
