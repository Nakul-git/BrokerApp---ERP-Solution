(function () {
    function ensureStyles() {
        if (document.querySelector('style[data-edit-mode="1"]')) return;
        const style = document.createElement('style');
        style.setAttribute('data-edit-mode', '1');
        style.textContent = `
.edit-mode-modal{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.4);
  display:flex;
  align-items:center;
  justify-content:center;
  z-index:9999;
}
.edit-mode-modal-box{
  background:#1f2937;
  color:#fff;
  padding:20px;
  width:320px;
  border-radius:10px;
}
.edit-mode-modal-buttons{
  margin-top:15px;
  text-align:right;
}
.edit-mode-modal-buttons button{
  margin-left:10px;
  padding:5px 15px;
  cursor:pointer;
}
`;
        document.head.appendChild(style);
    }

    function escapeHtml(value) {
        return String(value || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    function getLastEditableField() {
        const rightPanel = document.querySelector(".right-panel");
        if (!rightPanel || !rightPanel.classList.contains("modify-mode")) return null;
        const form = rightPanel.querySelector("form");
        if (!form) return null;
        const fields = Array.from(form.querySelectorAll("input, textarea")).filter(function(field){
            if (!isEditableField(field)) return false;
            const type = String(field.type || "").toLowerCase();
            if (type === "checkbox" || type === "radio") return false;
            return true;
        });
        return fields.length ? fields[fields.length - 1] : null;
    }

    function focusLastField() {
        const field = getLastEditableField();
        if (!field) {
            const saveBtn = document.getElementById("saveBtn");
            if (saveBtn) setTimeout(function () { saveBtn.focus(); }, 0);
            return;
        }
        setTimeout(function () { selectField(field); }, 0);
    }

    function editModeConfirm(message, onConfirm, onCancel) {
        ensureStyles();
        const displayMessage = escapeHtml(message);
        const modal = document.createElement('div');
        modal.className = 'edit-mode-modal';
        modal.innerHTML = `
<div class="edit-mode-modal-box">
  <div>${displayMessage}</div>
  <div class="edit-mode-modal-buttons">
    <button id="editModeCancel">Cancel</button>
    <button id="editModeOk">OK</button>
  </div>
</div>`;
        document.body.appendChild(modal);

        const cancelBtn = modal.querySelector('#editModeCancel');
        const okBtn = modal.querySelector('#editModeOk');
        const buttons = [cancelBtn, okBtn];
        let activeIndex = 0;

        cancelBtn.focus();

        function closeModal() {
            document.removeEventListener('keydown', keyHandler, true);
            modal.remove();
        }

        function keyHandler(e) {
            e.stopPropagation();
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown' || e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                e.preventDefault();
                activeIndex = activeIndex === 0 ? 1 : 0;
                buttons[activeIndex].focus();
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                closeModal();
                if (activeIndex === 1 && typeof onConfirm === 'function') onConfirm();
                if (activeIndex === 0 && typeof onCancel === 'function') onCancel();
                if (activeIndex === 0) focusLastField();
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                closeModal();
                if (typeof onCancel === 'function') onCancel();
                focusLastField();
            }
        }

        document.addEventListener('keydown', keyHandler, true);

        cancelBtn.onclick = function () {
            closeModal();
            if (typeof onCancel === 'function') onCancel();
            focusLastField();
        };
        okBtn.onclick = function () {
            closeModal();
            if (typeof onConfirm === 'function') onConfirm();
        };
    }

    function editModeConfirmPromise(message) {
        return new Promise(function (resolve) {
            editModeConfirm(message, function () { resolve(true); }, function () { resolve(false); });
        });
    }

    window.editModeConfirm = editModeConfirm;
    window.editModeConfirmPromise = editModeConfirmPromise;
    window.confirmEdit = async function (message) {
        return await window.editModeConfirmPromise(message);
    };

    function ready(fn) {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", fn);
        } else {
            fn();
        }
    }

    function isEditableField(field) {
        if (!field) return false;
        if (field.disabled || field.readOnly) return false;
        if (field.type === "hidden") return false;
        if (field.type === "checkbox" || field.type === "radio") return false;
        if (field.tagName === "SELECT") return false;
        if (field.offsetParent === null) return false;
        if (field.hasAttribute("data-no-edit-select")) return false;
        return field.tagName === "INPUT" || field.tagName === "TEXTAREA";
    }

    function selectField(field) {
        if (!isEditableField(field)) return;
        try {
            field.focus();
            if (typeof field.select === "function") field.select();
        } catch (e) {}
    }

    function getFirstEditableField(form) {
        if (!form) return null;
        const fields = Array.from(form.querySelectorAll("input, textarea")).filter(isEditableField);
        return fields[0] || null;
    }

    function init() {
        const rightPanel = document.querySelector(".right-panel");
        const form = rightPanel ? rightPanel.querySelector("form") : null;
        if (!rightPanel || !form) return;

        let wasModify = rightPanel.classList.contains("modify-mode");

        function handleEnterModifyMode() {
            const active = document.activeElement;
            if (active && form.contains(active) && isEditableField(active)) {
                selectField(active);
                return;
            }
            const first = getFirstEditableField(form);
            if (first) selectField(first);
        }

        const obs = new MutationObserver(function () {
            const isModify = rightPanel.classList.contains("modify-mode");
            if (isModify && !wasModify) {
                setTimeout(handleEnterModifyMode, 0);
            }
            wasModify = isModify;
        });
        obs.observe(rightPanel, { attributes: true, attributeFilter: ["class"] });

        form.addEventListener("focusin", function (e) {
            if (!rightPanel.classList.contains("modify-mode")) return;
            if (!isEditableField(e.target)) return;
            setTimeout(function () {
                selectField(e.target);
            }, 0);
        });

        if (rightPanel.classList.contains("modify-mode")) {
            setTimeout(handleEnterModifyMode, 0);
        }
    }

    document.addEventListener("click", function (e) {
        const btn = e.target && e.target.closest ? e.target.closest("#saveBtn") : null;
        if (!btn) return;
        const rightPanel = document.querySelector(".right-panel");
        if (!rightPanel || !rightPanel.classList.contains("modify-mode")) return;
        if (btn.dataset.editConfirmBypass === "1") return;
        e.preventDefault();
        e.stopImmediatePropagation();
        const confirmFn = window.confirmEdit || function (msg) { return Promise.resolve(window.confirm(msg)); };
        confirmFn("Do you want to edit this?").then(function (ok) {
            if (!ok) return;
            btn.dataset.editConfirmBypass = "1";
            btn.click();
            setTimeout(function () { delete btn.dataset.editConfirmBypass; }, 0);
        });
    }, true);

    ready(init);
})();
