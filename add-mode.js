(function(){
    function ensureStyles(){
        if(document.querySelector('style[data-add-mode="1"]')) return;
        const style = document.createElement('style');
        style.setAttribute('data-add-mode', '1');
        style.textContent = `
.add-mode-modal{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.4);
  display:flex;
  align-items:center;
  justify-content:center;
  z-index:9999;
}
.add-mode-modal-box{
  background:#1f2937;
  color:#fff;
  padding:20px;
  width:340px;
  border-radius:10px;
}
.add-mode-modal-buttons{
  margin-top:15px;
  text-align:right;
}
.add-mode-modal-buttons button{
  margin-left:10px;
  padding:5px 15px;
  cursor:pointer;
}
.add-mode-label{
  margin-top:10px;
  font-weight:700;
  word-break:break-word;
}
`;
        document.head.appendChild(style);
    }

    function getActiveFormLabel(){
        const rightPanel = document.querySelector('.right-panel');
        if(!rightPanel) return "";
        const blacklist = new Set(["NAME", "CODE", "TITLE", "DESCRIPTION", "TYPE"]);
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
        for(let i = 0; i < fieldSelectors.length; i += 1){
            const field = rightPanel.querySelector(fieldSelectors[i]);
            if(!field) continue;
            const value = (field.value || "").trim();
            if(value && !blacklist.has(value.toUpperCase())) return value;
        }
        const select = rightPanel.querySelector("select");
        if(select && select.selectedOptions && select.selectedOptions.length){
            const optionText = (select.selectedOptions[0].textContent || "").trim();
            if(optionText && !blacklist.has(optionText.toUpperCase())) return optionText;
        }
        return "";
    }

    function findInlineErrorSpan(field){
        if(!field) return null;
        const group = field.closest('.form-group') || field.closest('label') || field.parentElement;
        if(group){
            const err = group.querySelector('.error-text');
            if(err) return err;
        }
        if(field.id){
            const label = document.querySelector('label[for="' + field.id + '"]');
            if(label){
                const err = label.querySelector('.error-text');
                if(err) return err;
            }
        }
        return null;
    }

    function showInlineRequired(field, message){
        if(!field) return;
        const msg = message || 'Required';
        field.classList.add('field-error');
        const err = findInlineErrorSpan(field);
        if(err){
            err.textContent = msg;
            err.style.display = 'inline';
            return;
        }
        const parent = field.parentElement;
        if(!parent) return;
        let inline = parent.querySelector('span[data-inline-required="1"]');
        if(!inline){
            inline = document.createElement('span');
            inline.dataset.inlineRequired = '1';
            inline.className = 'error-text';
            inline.style.marginLeft = '8px';
            inline.style.color = '#c40000';
            inline.style.fontSize = '12px';
            inline.style.fontWeight = '700';
            inline.style.whiteSpace = 'nowrap';
            parent.appendChild(inline);
        }
        inline.textContent = msg;
        inline.style.display = 'inline';
    }

    window.showInlineRequired = showInlineRequired;

    function isSkippableCheckbox(field){
        if(!field) return false;
        const type = String(field.type || '').toLowerCase();
        if(type !== 'checkbox' && type !== 'radio') return false;
        return !field.hasAttribute('data-enter-include');
    }

    function getLastFocusableField(){
        const rightPanel = document.querySelector('.right-panel');
        if(!rightPanel) return null;
        if(!rightPanel.classList.contains('add-mode') && !rightPanel.classList.contains('modify-mode')) return null;
        const form = rightPanel.querySelector('form');
        if(!form) return null;
        const fields = Array.from(form.querySelectorAll('input, select, textarea')).filter(function(field){
            if(field.disabled) return false;
            if(field.type === 'hidden') return false;
            if(isSkippableCheckbox(field)) return false;
            if(field.offsetParent === null) return false;
            return true;
        });
        return fields.length ? fields[fields.length - 1] : null;
    }

    function focusLastField(){
        const field = getLastFocusableField();
        if(!field){
            const saveBtn = document.getElementById('saveBtn');
            if(saveBtn) setTimeout(function(){ saveBtn.focus(); }, 0);
            return;
        }
        setTimeout(function(){
            field.focus();
            if(typeof field.select === 'function' && field.value) field.select();
        }, 0);
    }

    function escapeHtml(value){
        return String(value || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    function formatMessage(message){
        const base = String(message || "");
        const label = getActiveFormLabel();
        const baseSafe = escapeHtml(base).replace(/\r?\n/g, "<br>");
        if(!label) return baseSafe;
        const labelLower = label.toLowerCase();
        const baseLower = base.toLowerCase();
        const escapedLabel = labelLower.replace(/[.*+?^${}()|[\\]\\\\]/g, "\\$&");
        const lineMatch = new RegExp("(^|\\n)\\s*" + escapedLabel + "\\s*(\\n|$)", "i");
        if(lineMatch.test(baseLower)) return baseSafe;
        if(label.length > 2){
            const wordMatch = new RegExp("\\b" + escapedLabel + "\\b", "i");
            if(wordMatch.test(baseLower)) return baseSafe;
        }
        return baseSafe + "<div class=\"add-mode-label\">" + escapeHtml(label) + "</div>";
    }

    function addModeConfirm(message, onConfirm, onCancel){
        ensureStyles();
        const displayMessage = formatMessage(message);
        const modal = document.createElement('div');
        modal.className = 'add-mode-modal';
        modal.innerHTML = `
<div class="add-mode-modal-box">
  <div>${displayMessage}</div>
  <div class="add-mode-modal-buttons">
    <button id="addModeOk">OK</button>
    <button id="addModeCancel">Cancel</button>
  </div>
</div>`;
        document.body.appendChild(modal);

        const okBtn = modal.querySelector('#addModeOk');
        const cancelBtn = modal.querySelector('#addModeCancel');
        const buttons = [okBtn, cancelBtn];
        let activeIndex = 0;

        okBtn.focus();

        function closeModal(){
            document.removeEventListener('keydown', keyHandler, true);
            modal.remove();
        }

        function keyHandler(e){
            e.stopPropagation();
            if(e.key === 'ArrowUp' || e.key === 'ArrowDown' || e.key === 'ArrowLeft' || e.key === 'ArrowRight'){
                e.preventDefault();
                activeIndex = activeIndex === 0 ? 1 : 0;
                buttons[activeIndex].focus();
            }
            if(e.key === 'Enter'){
                e.preventDefault();
                closeModal();
                if(activeIndex === 0 && typeof onConfirm === 'function') onConfirm();
                if(activeIndex === 1 && typeof onCancel === 'function') onCancel();
                if(activeIndex === 1) focusLastField();
            }
            if(e.key === 'Escape'){
                e.preventDefault();
                closeModal();
                if(typeof onCancel === 'function') onCancel();
                focusLastField();
            }
        }

        document.addEventListener('keydown', keyHandler, true);

        okBtn.onclick = function(){
            closeModal();
            if(typeof onConfirm === 'function') onConfirm();
        };
        cancelBtn.onclick = function(){
            closeModal();
            if(typeof onCancel === 'function') onCancel();
            focusLastField();
        };
    }

    function addModeConfirmPromise(message){
        return new Promise(function(resolve){
            addModeConfirm(message, function(){ resolve(true); }, function(){ resolve(false); });
        });
    }

    window.addModeConfirm = addModeConfirm;
    window.addModeConfirmPromise = addModeConfirmPromise;
    window.confirmAdd = async function(message){
        return await window.addModeConfirmPromise(message);
    };

    function ready(fn){
        if(document.readyState === 'loading'){
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    function init(){
        const rightPanel = document.querySelector('.right-panel');
        const form = rightPanel ? rightPanel.querySelector('form') : null;
        if(!rightPanel || !form) return;

        const saveBtn = document.getElementById('saveBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const addBtn = document.getElementById('addBtn');

        function isAddMode(){
            return rightPanel.classList.contains('add-mode');
        }
        function isModifyMode(){
            return rightPanel.classList.contains('modify-mode');
        }
        function isAddOrModify(){
            return isAddMode() || isModifyMode();
        }

        function hasVisibleError(field){
            const group = field.closest('.form-group') || field.closest('label') || field.parentElement;
            if(!group) return false;
            const err = group.querySelector('.error-text');
            if(!err) return false;
            if(err.style && err.style.display === 'none') return false;
            return (err.textContent || '').trim().length > 0;
        }

        function runCheck(field, attrName){
            if(!field) return false;
            const fnName = field.getAttribute(attrName);
            if(!fnName) return false;
            const fn = window[fnName];
            if(typeof fn !== 'function') return false;
            try { return !!fn(); } catch (e) { return false; }
        }

        function focusField(field){
            if(!field) return;
            field.focus();
            if(typeof field.select === 'function' && field.value) field.select();
        }

        let suppressAutoAddUntil = 0;
        let manualAddLock = false;
        let ignoreAutoAddUntil = 0;

        function cancelEdit(){
            // Prevent auto re-entering add mode right after a user cancel.
            const now = Date.now();
            suppressAutoAddUntil = now + 2000;
            ignoreAutoAddUntil = now + 2000;
            stopEnforceAddMode();
            manualAddLock = true;
            addSavePending = false;
            setSaveInFlight(false);
            if(cancelBtn){
                cancelBtn.click();
                return;
            }
            if(typeof window.cancelEditMode === 'function'){
                window.cancelEditMode();
            }
        }

        let saveInFlight = false;
        function setSaveInFlight(value){
            saveInFlight = value;
            if(!saveBtn) return;
            if(value){
                saveBtn.dataset.saveInFlight = '1';
                if('disabled' in saveBtn) saveBtn.disabled = true;
            } else {
                delete saveBtn.dataset.saveInFlight;
                if('disabled' in saveBtn) saveBtn.disabled = false;
            }
        }
        window.clearAddModeSaveInFlight = function(){
            setSaveInFlight(false);
        };

        function triggerAddMode(){
            if(Date.now() < suppressAutoAddUntil) return;
            if(Date.now() < ignoreAutoAddUntil) return;
            if(manualAddLock) return;
            if(typeof window.handleAddAction === 'function'){
                window.handleAddAction();
                return;
            }
            if(typeof window.startAddMode === 'function'){
                window.startAddMode();
                return;
            }
            if(addBtn){
                addBtn.click();
            }
        }

        if(addBtn){
            addBtn.addEventListener('click', function(){
                suppressAutoAddUntil = 0;
                manualAddLock = false;
            });
        }
        if(cancelBtn){
            cancelBtn.addEventListener('click', function(){
                const now = Date.now();
                suppressAutoAddUntil = now + 2000;
                ignoreAutoAddUntil = now + 2000;
                manualAddLock = true;
                stopEnforceAddMode();
                addSavePending = false;
                setSaveInFlight(false);
            });
        }

        let enforceUntil = 0;
        let enforceObserver = null;
        function stopEnforceAddMode(){
            enforceUntil = 0;
            if(enforceObserver){
                try { enforceObserver.disconnect(); } catch(e) {}
                enforceObserver = null;
            }
        }
        function startEnforceAddMode(duration){
            if(!rightPanel) return;
            if(Date.now() < suppressAutoAddUntil) return;
            if(Date.now() < ignoreAutoAddUntil) return;
            if(manualAddLock) return;
            const ttl = Math.max(300, duration || 1200);
            enforceUntil = Date.now() + ttl;
            if(!enforceObserver){
                enforceObserver = new MutationObserver(function(){
                    if(Date.now() > enforceUntil) return;
                    if(rightPanel.classList.contains('display-mode')){
                        triggerAddMode();
                    }
                });
                enforceObserver.observe(rightPanel, { attributes: true, attributeFilter: ['class'] });
            }
            setTimeout(function(){
                if(Date.now() > enforceUntil){
                    enforceUntil = 0;
                }
            }, ttl + 50);
        }

        function getFirstField(){
            return form.querySelector('[data-add-first]') || form.querySelector('input, select, textarea');
        }

        function getFocusableFields(){
            return Array.from(form.querySelectorAll('input, select, textarea')).filter(function(field){
                if(field.disabled) return false;
                if(field.type === 'hidden') return false;
                if((field.type === 'checkbox' || field.type === 'radio') && !field.hasAttribute('data-enter-include')) return false;
                if(shouldSkipField(field)) return false;
                if(field.offsetParent === null) return false;
                return true;
            });
        }

        function shouldSkipField(field){
            if(!field) return false;
            const rule = field.getAttribute('data-enter-skip-when');
            if(!rule) return false;
            const trimmed = String(rule).trim();
            if(!trimmed) return false;
            if(trimmed.endsWith(':checked')){
                const selector = trimmed.slice(0, -8).trim();
                if(!selector) return false;
                const el = document.querySelector(selector);
                return !!(el && el.checked);
            }
            const el = document.querySelector(trimmed);
            if(!el) return false;
            if('checked' in el) return !!el.checked;
            return false;
        }

        function handleEnter(e){
            if(e.__addModeHandled) return;
            if(e.key !== 'Enter') return;
            if(!isAddOrModify()) return;

            const activeField = document.activeElement;
            if(!activeField || !form.contains(activeField)) return;
            const hasForceHandle = activeField.hasAttribute('data-required-check')
                || activeField.hasAttribute('data-duplicate-check')
                || activeField.hasAttribute('data-enter-next');
            if(e.defaultPrevented && !hasForceHandle) return;

            if(isAddMode()){
                if(runCheck(activeField, 'data-duplicate-check')){ e.preventDefault(); focusField(activeField); return; }
                if(runCheck(activeField, 'data-required-check')){ e.preventDefault(); focusField(activeField); return; }
            }

            const firstField = getFirstField();
            if(isAddMode() && firstField && activeField === firstField){
                const val = (activeField.value || '').trim();
                if(!val){
                    e.preventDefault();
                    cancelEdit();
                    return;
                }
            }

            if(isAddMode()){
                if(activeField.classList.contains('field-error') || hasVisibleError(activeField)){
                    e.preventDefault();
                    focusField(activeField);
                    return;
                }
            }

            e.preventDefault();
            e.__addModeHandled = true;
            if(typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
            const fields = getFocusableFields();
            const index = fields.indexOf(activeField);
            if(index === -1) return;
            if(index > -1 && index < fields.length - 1){
                fields[index + 1].focus();
            } else if(saveBtn){
                // Match party_wise_brokerage_rate_setup flow:
                // move focus to Save, require another Enter to trigger.
                saveBtn.focus();
            }
        }

        form.addEventListener('keydown', handleEnter);

        function handleInput(e){
            const field = e.target;
            if(!isAddOrModify()) return;
            if(!field || !form.contains(field)) return;
            runCheck(field, 'data-duplicate-check');
            runCheck(field, 'data-required-check');
        }

        form.addEventListener('input', handleInput);

        function handleUppercase(e){
            if(!isAddOrModify()) return;
            const field = e.target;
            if(!field || !form.contains(field)) return;
            if(field.hasAttribute('data-no-uppercase')) return;
            if(field.tagName === 'INPUT'){
                const type = String(field.type || '').toLowerCase();
                if(type !== 'text') return;
                if(field.readOnly || field.disabled) return;
                const val = field.value || '';
                const upper = val.toUpperCase();
                if(val === upper) return;
                const start = field.selectionStart;
                const end = field.selectionEnd;
                field.value = upper;
                try { if(start !== null && end !== null) field.setSelectionRange(start, end); } catch (e) {}
                return;
            }
            if(field.tagName === 'TEXTAREA'){
                if(field.readOnly || field.disabled) return;
                const val = field.value || '';
                const upper = val.toUpperCase();
                if(val === upper) return;
                const start = field.selectionStart;
                const end = field.selectionEnd;
                field.value = upper;
                try { if(start !== null && end !== null) field.setSelectionRange(start, end); } catch (e) {}
            }
        }

        form.addEventListener('input', handleUppercase);

        // Stay in add mode after successful add save
        let addSavePending = false;
        if(saveBtn){
            saveBtn.addEventListener('click', function(e){
                if(saveInFlight){ e.preventDefault(); return; }
                setSaveInFlight(true);
                if(isAddMode()) addSavePending = true;
            });
        }
        document.addEventListener('keydown', function(e){
            if(e.key === 'F6' && isAddMode() && !saveInFlight) addSavePending = true;
        });

        const originalFetch = window.fetch;
        if(typeof originalFetch === 'function'){
            window.fetch = function(){
                const args = arguments;
                const shouldCheck = addSavePending && isAddMode();
                const shouldReset = saveInFlight;
                return originalFetch.apply(this, args).then(function(resp){
                    if(shouldReset){
                        setTimeout(function(){ setSaveInFlight(false); }, 0);
                    }
                    if(shouldCheck){
                        addSavePending = false;
                        try {
                            const clone = resp.clone();
                            clone.text().then(function(text){
                                if(Date.now() < ignoreAutoAddUntil) return;
                                let data = null;
                                try {
                                    data = JSON.parse(text);
                                } catch (e) {
                                    data = null;
                                }

                                const status = data && (data.status || data.result);
                                const ok = (status && String(status).toLowerCase() === 'success')
                                    || (data && data.success === true);

                                if(ok){
                                    startEnforceAddMode();
                                    setTimeout(triggerAddMode, 0);
                                    return;
                                }

                                const plain = String(text || '').toLowerCase();
                                if(plain.indexOf('success') !== -1 && plain.indexOf('error') === -1){
                                    startEnforceAddMode();
                                    setTimeout(triggerAddMode, 0);
                                }
                            }).catch(function(){});
                        } catch (e) {}
                    }
                    return resp;
                }).catch(function(err){
                    if(shouldReset){
                        setTimeout(function(){ setSaveInFlight(false); }, 0);
                    }
                    throw err;
                });
            };
        }

        document.addEventListener('keydown', function(e){
            if(e.key !== 'Escape') return;
            if(!isAddOrModify()) return;
            if(e.ctrlKey || e.altKey || e.metaKey) return;
            if(document.querySelector('.add-mode-modal')) return;
            const now = Date.now();
            suppressAutoAddUntil = now + 2000;
            ignoreAutoAddUntil = now + 2000;
            manualAddLock = true;
            stopEnforceAddMode();
            addSavePending = false;
            setSaveInFlight(false);
            cancelEdit();
        }, true);
    }

    ready(init);
})();
