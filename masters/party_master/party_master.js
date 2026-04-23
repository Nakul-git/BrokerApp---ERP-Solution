let allParties = [];
let allCities = [];
let allGroups = [];
let allConditions = [];
let allProducts = [];
let allBrands = [];
let allDeals = [];
let allBanks = [];
let allCompanies = [];
let allDistricts = [];
let allZoneAreas = [];
let activePartyId = null;
let bankRows = [];
let divisionBalanceRows = [];
let sellerRows = [];
let buyerRows = [];
let preEditPartyId = null;
let activeBankRowIndex = 0;
let activeChildSection = "";
let activePBRowIndex = { seller: 0, buyer: 0 };
let bankEdit = { row: -1, field: "" };
let pbEdit = { side: "", row: -1, field: "" };

const filterInput = document.getElementById("filterInput");
const partyList = document.getElementById("partyList");
const partyForm = document.getElementById("partyForm");
const modeTag = document.getElementById("modeTag");
const rightPanel = document.querySelector(".right-panel");
const displayButtons = document.getElementById("displayButtons");
const addModeButtons = document.getElementById("addModeButtons");
const partyNameInput = document.getElementById("party_name");
const partyNameError = document.getElementById("partyNameError");
const divisionBalanceBody = document.getElementById("divisionBalanceBody");
const cityError = document.getElementById("cityError");
const citySelect = document.getElementById("city_id");
const bankBody = document.getElementById("bankBody");
const paymentConditionBody = document.getElementById("paymentConditionBody");
const packingConditionBody = document.getElementById("packingConditionBody");
const sellerBody = document.getElementById("sellerBody");
const buyerBody = document.getElementById("buyerBody");

function setActiveChildSection(section) {
    activeChildSection = section || "";
}

function closeAllSelectDropdowns(exceptWrapper) {
    document.querySelectorAll(".dropdown-list.open").forEach(function (list) {
        if (exceptWrapper && list.closest(".dropdown-wrapper") === exceptWrapper) return;
        list.classList.remove("open");
    });
}

function focusNextField(fromEl) {
    const scope = partyForm || document;
    const selectors = [
        "input:not([type='hidden']):not([tabindex='-1'])",
        "textarea:not([tabindex='-1'])",
        "select:not([tabindex='-1'])",
        "button:not([tabindex='-1'])"
    ].join(",");
    const list = Array.from(scope.querySelectorAll(selectors)).filter(function (el) {
        if (el.disabled) return false;
        if (el.offsetParent === null) return false;
        return true;
    });
    if (!list.length) return;
    const idx = list.indexOf(fromEl);
    if (idx === -1) return;
    const next = list[idx + 1];
    if (next) next.focus();
}

function getSelectOptionLabel(selectEl, opt) {
    if (!opt) return "";
    if (selectEl === citySelect) {
        const city = allCities.find(function (c) { return String(c.city_id) === String(opt.value || ""); });
        if (city) return (city.city_name || "").trim();
    }
    return (opt.textContent || "").trim();
}

function buildSelectDropdownList(selectEl, listEl, filterText) {
    const query = (filterText || "").trim().toLowerCase();
    const options = Array.from(selectEl.options || []);
    const matched = options.filter(function (opt) {
        const text = getSelectOptionLabel(selectEl, opt);
        return !query || text.toLowerCase().includes(query);
    });
    listEl.innerHTML = "";
    if (!matched.length) {
        const empty = document.createElement("div");
        empty.className = "dropdown-empty";
        empty.textContent = "No options";
        listEl.appendChild(empty);
        return;
    }
    matched.forEach(function (opt, idx) {
        const item = document.createElement("div");
        item.className = "dropdown-item";
        item.dataset.value = opt.value;
        item.dataset.index = String(idx);
        item.textContent = getSelectOptionLabel(selectEl, opt);
        if (opt.value === selectEl.value) {
            item.classList.add("active");
        }
        item.addEventListener("mousedown", function (e) {
            e.preventDefault();
            selectEl.value = opt.value;
            syncSelectDropdown(selectEl);
            listEl.classList.remove("open");
            const ev = new Event("change", { bubbles: true });
            selectEl.dispatchEvent(ev);
        });
        listEl.appendChild(item);
    });
}

function moveSelectDropdownActive(listEl, dir) {
    const items = Array.from(listEl.querySelectorAll(".dropdown-item"));
    if (!items.length) return;
    let idx = items.findIndex(function (el) { return el.classList.contains("active"); });
    if (idx === -1) idx = 0;
    idx = Math.max(0, Math.min(items.length - 1, idx + dir));
    items.forEach(function (el) { el.classList.remove("active"); });
    items[idx].classList.add("active");
    items[idx].scrollIntoView({ block: "nearest" });
}

function syncSelectDropdown(selectEl) {
    const inputId = selectEl.dataset.dropdownInputId;
    if (!inputId) return;
    const inputEl = document.getElementById(inputId);
    if (!inputEl) return;
    const opt = selectEl.options[selectEl.selectedIndex];
    inputEl.value = getSelectOptionLabel(selectEl, opt);
}

function enhanceSelectToDropdown(selectEl, opts) {
    if (!selectEl || selectEl.dataset.dropdownInputId) return;
    const config = opts || {};
    const wrapper = document.createElement("div");
    wrapper.className = "dropdown-wrapper";
    const inputEl = document.createElement("input");
    inputEl.type = "text";
    inputEl.autocomplete = "off";
    inputEl.className = "dropdown-input";
    inputEl.id = selectEl.id + "_input_" + Math.random().toString(36).slice(2, 8);
    inputEl.placeholder = config.placeholder || "Select...";
    if (config.readOnly) inputEl.readOnly = true;

    const listEl = document.createElement("div");
    listEl.className = "dropdown-list";

    selectEl.parentNode.insertBefore(wrapper, selectEl);
    wrapper.appendChild(inputEl);
    wrapper.appendChild(listEl);
    wrapper.appendChild(selectEl);
    selectEl.classList.add("dropdown-hidden");
    selectEl.dataset.dropdownInputId = inputEl.id;

    syncSelectDropdown(selectEl);

    function openDropdown() {
        if (rightPanel.classList.contains("display-mode")) return;
        closeAllSelectDropdowns(wrapper);
        const filterText = inputEl.dataset.hasTyped === "1" ? inputEl.value : "";
        buildSelectDropdownList(selectEl, listEl, filterText);
        listEl.classList.add("open");
    }

    inputEl.addEventListener("focus", function () { openDropdown(); });
    inputEl.addEventListener("click", function () { openDropdown(); });
    inputEl.addEventListener("input", function () {
        if (rightPanel.classList.contains("display-mode")) return;
        inputEl.dataset.hasTyped = "1";
        buildSelectDropdownList(selectEl, listEl, inputEl.value);
        listEl.classList.add("open");
    });

    inputEl.addEventListener("keydown", function (e) {
        if (rightPanel.classList.contains("display-mode")) return;
        if (e.key === "ArrowDown") {
            e.preventDefault();
            if (!listEl.classList.contains("open")) openDropdown();
            moveSelectDropdownActive(listEl, 1);
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            if (!listEl.classList.contains("open")) openDropdown();
            moveSelectDropdownActive(listEl, -1);
        } else if (e.key === "Enter") {
            e.preventDefault();
            const active = listEl.querySelector(".dropdown-item.active");
            if (active) {
                selectEl.value = active.dataset.value;
            }
            syncSelectDropdown(selectEl);
            inputEl.dataset.hasTyped = "0";
            listEl.classList.remove("open");
            const ev = new Event("change", { bubbles: true });
            selectEl.dispatchEvent(ev);
            focusNextField(inputEl);
        } else if (e.key === "Escape") {
            listEl.classList.remove("open");
        }
    });

    inputEl.addEventListener("blur", function () {
        setTimeout(function () {
            if (!wrapper.contains(document.activeElement)) {
                listEl.classList.remove("open");
                syncSelectDropdown(selectEl);
                inputEl.dataset.hasTyped = "0";
            }
        }, 120);
    });

    selectEl.addEventListener("change", function () { syncSelectDropdown(selectEl); });
}

function enhanceSelectsIn(container) {
    if (!container) return;
    container.querySelectorAll("select").forEach(function (sel) {
        enhanceSelectToDropdown(sel);
    });
}

function syncAllSelectDropdowns() {
    document.querySelectorAll("select[data-dropdown-input-id]").forEach(function (el) {
        syncSelectDropdown(el);
    });
}

function esc(v) {
    return (v ?? "").toString().replace(/[&<>"']/g, function (m) {
        return ({ "&":"&amp;", "<":"&lt;", ">":"&gt;", "\"":"&quot;", "'":"&#39;" })[m];
    });
}

function setMode(mode) {
    rightPanel.classList.remove("add-mode", "modify-mode", "display-mode");
    if (mode === "add") {
        modeTag.textContent = "Add Mode";
        rightPanel.classList.add("add-mode");
    } else if (mode === "modify") {
        modeTag.textContent = "Edit Mode";
        rightPanel.classList.add("modify-mode");
    } else {
        modeTag.textContent = "Display Mode";
        rightPanel.classList.add("display-mode");
    }
    const isDisplay = mode === "display";
    displayButtons.style.display = isDisplay ? "flex" : "none";
    addModeButtons.style.display = isDisplay ? "none" : "flex";
}

function setFormEditable(editable) {
    const fields = partyForm.querySelectorAll("input, select, textarea");
    fields.forEach(function (field) {
        if (field.type === "hidden") return;
        if (field.id === "state_view" || field.id === "office_city" || field.id === "office_state") {
            field.readOnly = true;
            field.setAttribute("tabindex", "-1");
            return;
        }
        if (field.type === "checkbox") {
            field.disabled = !editable;
            return;
        }
        field.readOnly = !editable;
        field.disabled = !editable && field.tagName === "SELECT";
        if (!editable) field.setAttribute("tabindex", "-1");
        else field.removeAttribute("tabindex");
    });
}

function showPartyNameError() {
    partyNameInput.classList.add("field-error");
    partyNameError.textContent = "Required";
    partyNameError.style.display = "inline";
}

function showPartyNameErrorMessage(message) {
    partyNameInput.classList.add("field-error");
    partyNameError.textContent = message || "Required";
    partyNameError.style.display = "inline";
}

function clearPartyNameError() {
    partyNameInput.classList.remove("field-error");
    partyNameError.textContent = "";
    partyNameError.style.display = "none";
}

function checkDuplicatePartyName() {
    const name = (partyNameInput.value || "").trim().toLowerCase();
    if (!name) { clearPartyNameError(); return false; }
    const currentId = Number(document.getElementById("party_id").value || 0);
    const duplicate = allParties.some(function (p) {
        const sameName = (p.party_name || "").trim().toLowerCase() === name;
        const sameId = Number(p.party_id) === currentId;
        return sameName && !sameId;
    });
    if (duplicate) {
        showPartyNameErrorMessage("Already exists");
        return true;
    }
    clearPartyNameError();
    return false;
}

function showCityError() {
    citySelect.classList.add("field-error");
    if (citySelect.dataset.dropdownInputId) {
        const inputEl = document.getElementById(citySelect.dataset.dropdownInputId);
        if (inputEl) inputEl.classList.add("field-error");
    }
    cityError.textContent = "Required";
    cityError.style.display = "inline";
}

function clearCityError() {
    citySelect.classList.remove("field-error");
    if (citySelect.dataset.dropdownInputId) {
        const inputEl = document.getElementById(citySelect.dataset.dropdownInputId);
        if (inputEl) inputEl.classList.remove("field-error");
    }
    cityError.textContent = "";
    cityError.style.display = "none";
}

function hasAnyOtherFieldValue() {
    const fields = partyForm.querySelectorAll("input, select, textarea");
    for (let i = 0; i < fields.length; i += 1) {
        const field = fields[i];
        if (field.type === "hidden") continue;
        if (field.id === "party_name" || field.id === "city_id") continue;
        if (field.type === "checkbox") {
            if (field.checked) return true;
            continue;
        }
        if ((field.value || "").trim()) return true;
    }
    return false;
}

function buildBankRow() { return { ac_holder:"", ac_number:"", bank_id:0, bank_name:"", ifsc_code:"", pin_code:"" }; }
function buildPBRow() { return { product_id:"", brand_id:"", pack:"" }; }
function buildDivisionRow() {
    return { division_id:"", opening_balance:"", dc:"DB", hb_opening_balance:"", hb_dc:"DB" };
}

function setActiveBankRow(index, shouldFocus) {
    const rows = Array.from(document.querySelectorAll("#bankBody .bank-row"));
    if (!rows.length) {
        activeBankRowIndex = 0;
        return;
    }
    setActiveChildSection("bank");
    activeBankRowIndex = Math.max(0, Math.min(rows.length - 1, Number(index) || 0));
    rows.forEach(function (row, rowIndex) {
        row.classList.toggle("is-active", rowIndex === activeBankRowIndex);
    });
    const row = rows[activeBankRowIndex];
    if (!row) return;
    row.scrollIntoView({ block: "nearest" });
    if (shouldFocus) row.focus();
}

function focusBankField(selector) {
    const fieldName = selector === ".bank-ac-number" ? "ac_number" : selector === ".bank-bank-name" ? "bank_name" : "ac_holder";
    openBankEditor(activeBankRowIndex, fieldName);
}

function addBankRowFromShortcut() {
    if (!bankRows.length) bankRows = [buildBankRow()];
    const last = bankRows[bankRows.length - 1];
    if (last && !(last.ac_holder || last.ac_number || last.bank_name)) {
        activeBankRowIndex = bankRows.length - 1;
    } else {
        bankRows.push(buildBankRow());
        activeBankRowIndex = bankRows.length - 1;
    }
    renderBankRows(bankRows);
    openBankEditor(activeBankRowIndex, "ac_holder");
}

function deleteBankRowFromShortcut() {
    const baseRows = bankRows.length ? bankRows.slice() : [buildBankRow()];
    const removeIndex = Math.max(0, Math.min(baseRows.length - 1, activeBankRowIndex));
    baseRows.splice(removeIndex, 1);
    bankRows = baseRows.length ? baseRows : [buildBankRow()];
    activeBankRowIndex = Math.max(0, Math.min(bankRows.length - 1, removeIndex));
    bankEdit = { row: -1, field: "" };
    renderBankRows(bankRows);
}

function focusMoveBackFromBank() {
    bankBody.focus();
    setActiveChildSection("bank");
}

function bankDisplayName(bank) {
    const name = (bank && bank.bank_name) ? bank.bank_name : "";
    const branch = (bank && bank.branch) ? bank.branch : "";
    return branch ? (name + "," + branch) : name;
}

function bankMatch(value) {
    const n = (value || "").trim().toLowerCase();
    if (!n) return null;
    return allBanks.find(function (b) {
        return (b.bank_name || "").trim().toLowerCase() === n
            || bankDisplayName(b).trim().toLowerCase() === n
            || String(b.bank_id || "") === n;
    }) || null;
}

function productMatch(value) {
    const n = (value || "").trim().toLowerCase();
    if (!n) return null;
    return allProducts.find(function (p) {
        return String(p.product_id || "") === n || (p.product_name || "").trim().toLowerCase() === n;
    }) || null;
}

function brandMatch(value) {
    const n = (value || "").trim().toLowerCase();
    if (!n) return null;
    return allBrands.find(function (b) {
        return String(b.brand_id || "") === n || (b.brand_name || "").trim().toLowerCase() === n;
    }) || null;
}

function ensurePartyMasterLookups() {
    const shell = document.getElementById("partyForm") || document.body;
    const defs = [
        { id: "partyMasterBankList", values: allBanks.map(function (b) { return bankDisplayName(b); }) },
        { id: "partyMasterProductList", values: allProducts.map(function (p) { return p.product_name || ""; }) },
        { id: "partyMasterBrandList", values: allBrands.map(function (b) { return b.brand_name || ""; }) }
    ];
    defs.forEach(function (def) {
        let list = document.getElementById(def.id);
        if (!list) {
            list = document.createElement("datalist");
            list.id = def.id;
            shell.appendChild(list);
        }
        list.innerHTML = Array.from(new Set(def.values.filter(Boolean))).map(function (value) {
            return "<option value='" + esc(value) + "'></option>";
        }).join("");
    });
}

function renderBankRows(rows) {
    const body = bankBody;
    const list = rows.length ? rows : [buildBankRow()];
    activeBankRowIndex = Math.max(0, Math.min(list.length - 1, activeBankRowIndex));
    body.innerHTML = list.map(function (r, i) {
        const activeClass = i === activeBankRowIndex ? " is-active" : "";
        const bank = bankMatch(r.bank_id || r.bank_name || "");
        const bankText = bank ? bankDisplayName(bank) : (r.bank_name || "");
        const ifsc = bank ? (bank.ifsc_code || "") : (r.ifsc_code || "");
        const pin = bank ? (bank.pin || "") : (r.pin_code || "");
        return "<div class='grid-row bank-row" + activeClass + "' data-bank-index='" + i + "' tabindex='-1'>"
            + "<div style='padding-top:10px;text-align:center;font-weight:700;'>" + (i + 1) + "</div>"
            + "<div>" + (bankEdit.row === i && bankEdit.field === "ac_holder"
                ? "<input class='bank-editor bank-editor-ac-holder' data-row='" + i + "' data-field='ac_holder' value='" + esc(r.ac_holder || "") + "'>"
                : "<span class='cell-text'>" + esc(r.ac_holder || "") + "</span>") + "</div>"
            + "<div>" + (bankEdit.row === i && bankEdit.field === "ac_number"
                ? "<input class='bank-editor bank-editor-ac-number' data-row='" + i + "' data-field='ac_number' value='" + esc(r.ac_number || "") + "'>"
                : "<span class='cell-text'>" + esc(r.ac_number || "") + "</span>") + "</div>"
            + "<div>" + (bankEdit.row === i && bankEdit.field === "bank_name"
                ? "<input class='bank-editor bank-editor-bank-name' data-row='" + i + "' data-field='bank_name' list='partyMasterBankList' value='" + esc(bankText) + "'>"
                : "<span class='cell-text'>" + esc(bankText) + "</span>") + "</div>"
            + "<div><span class='cell-text'>" + esc(ifsc) + "</span></div>"
            + "<div><span class='cell-text'>" + esc(pin) + "</span></div>"
            + "</div>";
    }).join("");
    setActiveBankRow(activeBankRowIndex, false);
    if (bankEdit.row >= 0) {
        const editor = body.querySelector(".bank-editor[data-row='" + bankEdit.row + "'][data-field='" + bankEdit.field + "']");
        if (editor) {
            setTimeout(function () {
                editor.focus();
                if (editor.select) editor.select();
            }, 0);
        }
    }
}

function getSelectedCompanyId() {
    const bodyId = Number(document.body?.dataset?.selectedCompanyId || 0);
    if (bodyId > 0) return bodyId;
    const sessionId = Number(window.selectedCompanyId || 0);
    if (sessionId > 0) return sessionId;
    return 0;
}

function getSelectedCompanyLabel() {
    const name = (document.body?.dataset?.selectedCompanyName || window.selectedCompanyName || "").trim();
    const code = (document.body?.dataset?.selectedCompanyCode || window.selectedCompanyCode || "").trim();
    if (code && name) return code + " - " + name;
    return name || code || "Selected company";
}

function renderDivisionRows(rows) {
    const body = divisionBalanceBody;
    if (!body) return;
    const list = rows.length ? rows : [buildDivisionRow()];
    const companyId = getSelectedCompanyId();
    const companyLabel = getSelectedCompanyLabel();
    body.innerHTML = list.map(function (r, i) {
        return "<div class='grid-row division-grid-row'>"
            + "<div style='padding-top:5px;text-align:center;font-weight:700;'>" + (i + 1) + "</div>"
            + "<div>"
            + "<input type='hidden' class='division-id' value='" + esc(companyId || r.division_id || "") + "'>"
            + "<label class='division-display-cell division-company'>" + esc(companyLabel) + "</label>"
            + "</div>"
            + "<div><input type='hidden' class='division-opening-value' value='" + esc(r.opening_balance || "") + "'><label class='division-display-cell'>" + esc(r.opening_balance || "") + "</label></div>"
            + "<div><input type='hidden' class='division-dc' value='DB'><label class='division-display-cell'>Debit</label></div>"
            + "<div><input type='hidden' class='division-hb-opening-value' value='" + esc(r.hb_opening_balance || "") + "'><label class='division-display-cell'>" + esc(r.hb_opening_balance || "") + "</label></div>"
            + "<div><input type='hidden' class='division-hb-dc' value='CR'><label class='division-display-cell'>Credit</label></div>"
            + "</div>";
    }).join("");
    enhanceSelectsIn(body);
}

function readDivisionRows() {
    if (!divisionBalanceBody) return [];
    return Array.from(divisionBalanceBody.querySelectorAll(".division-grid-row")).map(function (row) {
        const divisionIdEl = row.querySelector(".division-id");
        const dcEl = row.querySelector(".division-dc");
        const hbDcEl = row.querySelector(".division-hb-dc");
        const openingValueEl = row.querySelector(".division-opening-value");
        const hbOpeningValueEl = row.querySelector(".division-hb-opening-value");
        return {
            division_id: Number(divisionIdEl ? divisionIdEl.value : 0),
            opening_balance: openingValueEl ? openingValueEl.value.trim() : "",
            dc: dcEl ? (dcEl.value || "DB") : "DB",
            hb_opening_balance: hbOpeningValueEl ? hbOpeningValueEl.value.trim() : "",
            hb_dc: hbDcEl ? (hbDcEl.value || "CR") : "CR"
        };
    }).filter(function (r) {
        return r.division_id > 0 || r.opening_balance || r.hb_opening_balance;
    });
}

function readBankRows() {
    return bankRows.filter(function (r) {
        return r.ac_holder || r.ac_number || r.bank_name || r.ifsc_code || r.pin_code;
    }).map(function (r) {
        return {
            ac_holder: r.ac_holder || "",
            ac_number: r.ac_number || "",
            bank_name: r.bank_name || "",
            ifsc_code: r.ifsc_code || "",
            pin_code: r.pin_code || ""
        };
    });
}

function renderPBRows(bodyId, rows) {
    const body = document.getElementById(bodyId);
    const list = rows.length ? rows : [buildPBRow()];
    const side = bodyId === "sellerBody" ? "seller" : "buyer";
    activePBRowIndex[side] = Math.max(0, Math.min(list.length - 1, activePBRowIndex[side] || 0));
    body.innerHTML = list.map(function (r, i) {
        const activeClass = i === activePBRowIndex[side] ? " is-active" : "";
        const product = allProducts.find(function (p) { return Number(p.product_id) === Number(r.product_id); });
        const brand = allBrands.find(function (b) { return Number(b.brand_id) === Number(r.brand_id); });
        return "<div class='grid-row pb-row" + activeClass + "' data-pb-index='" + i + "' data-pb-side='" + side + "' tabindex='-1'>"
            + "<div style='padding-top:10px;text-align:center;font-weight:700;'>" + (i + 1) + "</div>"
            + "<div>" + (pbEdit.side === side && pbEdit.row === i && pbEdit.field === "product_id"
                ? "<input class='pb-editor pb-editor-product' data-side='" + side + "' data-row='" + i + "' data-field='product_id' list='partyMasterProductList' value='" + esc((product || {}).product_name || "") + "'>"
                : "<span class='cell-text'>" + esc((product || {}).product_name || "") + "</span>") + "</div>"
            + "<div>" + (pbEdit.side === side && pbEdit.row === i && pbEdit.field === "brand_id"
                ? "<input class='pb-editor pb-editor-brand' data-side='" + side + "' data-row='" + i + "' data-field='brand_id' list='partyMasterBrandList' value='" + esc((brand || {}).brand_name || "") + "'>"
                : "<span class='cell-text'>" + esc((brand || {}).brand_name || "") + "</span>") + "</div>"
            + "<div>" + (pbEdit.side === side && pbEdit.row === i && pbEdit.field === "pack"
                ? "<input class='pb-editor' data-side='" + side + "' data-row='" + i + "' data-field='pack' value='" + esc(r.pack || "") + "'>"
                : "<span class='cell-text'>" + esc(r.pack || "") + "</span>") + "</div>"
            + "</div>";
    }).join("");
    setActivePBRow(side, activePBRowIndex[side], false);
    if (pbEdit.side === side && pbEdit.row >= 0) {
        const editor = body.querySelector(".pb-editor[data-side='" + side + "'][data-row='" + pbEdit.row + "'][data-field='" + pbEdit.field + "']");
        if (editor) {
            setTimeout(function () {
                editor.focus();
                if (editor.select) editor.select();
            }, 0);
        }
    }
}

function readPBRows(bodyId) {
    const rows = bodyId === "sellerBody" ? sellerRows : buyerRows;
    return rows.filter(function (r) {
        return r.product_id > 0 || r.brand_id > 0 || r.pack;
    }).map(function (r) {
        return {
            product_id: Number(r.product_id || 0),
            brand_id: Number(r.brand_id || 0),
            pack: r.pack || ""
        };
    });
}

function renderConditionChecks(containerId, rows, checkedSet) {
    const box = document.getElementById(containerId);
    if (!rows.length) {
        box.innerHTML = "<div class='condition-empty'>No condition available</div>";
        return;
    }
    box.innerHTML = rows.map(function (r, i) {
        const ck = checkedSet.has(Number(r.condition_id)) ? " checked" : "";
        return "<div class='grid-row condition-row' data-condition-index='" + i + "' tabindex='-1'>"
            + "<div class='condition-sno'>" + (i + 1) + "</div>"
            + "<div class='condition-sel'><input type='checkbox' class='condition-check' value='" + esc(r.condition_id) + "'" + ck + "></div>"
            + "<div class='condition-text'>" + esc(r.term_description || "") + "</div>"
            + "</div>";
    }).join("");
}

function checkedValues(containerId) {
    return Array.from(document.querySelectorAll("#" + containerId + " .condition-check:checked")).map(function (e) {
        return Number(e.value || 0);
    }).filter(function (v) { return v > 0; });
}

function setMultiSelectValues(id, values) {
    const set = new Set((values || []).map(function (v) { return Number(v); }));
    const el = document.getElementById(id);
    if (!el) return;
    Array.from(el.options).forEach(function (o) { o.selected = set.has(Number(o.value)); });
}

function getMultiSelectValues(id) {
    const el = document.getElementById(id);
    if (!el) return [];
    return Array.from(el.selectedOptions).map(function (o) {
        return Number(o.value || 0);
    }).filter(function (v) { return v > 0; });
}

function syncCityStateFields() {
    const selectedId = Number(citySelect.value || 0);
    const city = allCities.find(function (c) { return Number(c.city_id) === selectedId; });
    const cityName = city ? (city.city_name || "") : "";
    const stateName = city ? (city.state_name || "") : "";
    document.getElementById("city").value = cityName;
    document.getElementById("state").value = stateName;
    document.getElementById("state_view").value = stateName;
    syncOfficeLocationFields(cityName, stateName);
}

function syncOfficeLocationFields(cityName, stateName) {
    const officeCityEl = document.getElementById("office_city");
    const officeStateEl = document.getElementById("office_state");
    if (officeCityEl) officeCityEl.value = cityName || "";
    if (officeStateEl) officeStateEl.value = stateName || "";
}

function renderCityOptions(selectedId) {
    if (!allCities.length) {
        citySelect.innerHTML = "<option value=''>No cities available</option>";
        syncCityStateFields();
        syncSelectDropdown(citySelect);
        return;
    }
    citySelect.innerHTML = "<option value=''>Select city</option>" + allCities.map(function (c) {
        const selected = Number(c.city_id) === Number(selectedId) ? " selected" : "";
        return "<option value='" + esc(c.city_id) + "'" + selected + ">" + esc(c.city_name || "") + "</option>";
    }).join("");
    syncCityStateFields();
    syncSelectDropdown(citySelect);
}

function renderAreaOptions(selectedValue) {
    const areaSelect = document.getElementById("area");
    if (!areaSelect) return;
    const value = (selectedValue || "").trim();
    const matched = allDistricts.find(function (d) {
        return (d.district_name || "").trim().toLowerCase() === value.toLowerCase();
    });
    areaSelect.innerHTML = "<option value=''>Select area</option>" + allDistricts.map(function (d) {
        const name = (d.district_name || "").trim();
        const selected = matched && Number(d.district_id) === Number(matched.district_id) ? " selected" : "";
        return "<option value='" + esc(name) + "'" + selected + ">" + esc(name) + "</option>";
    }).join("");
    if (value && !matched) {
        areaSelect.innerHTML += "<option value='" + esc(value) + "' selected>" + esc(value) + "</option>";
    }
    syncSelectDropdown(areaSelect);
}

function renderZoneAreaOptions(selectedValue) {
    const zoneAreaSelect = document.getElementById("zone_area");
    if (!zoneAreaSelect) return;
    const value = (selectedValue || "").trim();
    const matched = allZoneAreas.find(function (a) {
        return (a.name || "").trim().toLowerCase() === value.toLowerCase();
    });
    zoneAreaSelect.innerHTML = "<option value=''>Select zone / area</option>" + allZoneAreas.map(function (a) {
        const name = (a.name || "").trim();
        const selected = matched && Number(a.area_id) === Number(matched.area_id) ? " selected" : "";
        return "<option value='" + esc(name) + "'" + selected + ">" + esc(name) + "</option>";
    }).join("");
    if (value && !matched) {
        zoneAreaSelect.innerHTML += "<option value='" + esc(value) + "' selected>" + esc(value) + "</option>";
    }
    syncSelectDropdown(zoneAreaSelect);
}

function renderGroupOptions(value) {
    const groupSelect = document.getElementById("group_name");
    groupSelect.innerHTML = "<option value=''>Select group</option>" + allGroups.map(function (g) {
        const name = g.group_name || "";
        const selected = name === value ? " selected" : "";
        return "<option value='" + esc(name) + "'" + selected + ">" + esc(name) + "</option>";
    }).join("");
    syncSelectDropdown(groupSelect);
}

function renderDefaultProductOptions(selectedId) {
    const el = document.getElementById("default_product_id");
    el.innerHTML = "<option value=''>Select product</option>" + allProducts.map(function (p) {
        const s = Number(p.product_id) === Number(selectedId) ? " selected" : "";
        return "<option value='" + esc(p.product_id) + "'" + s + ">" + esc(p.product_name || "") + "</option>";
    }).join("");
    syncSelectDropdown(el);
}

function renderDefaultBrandOptions(selectedId) {
    const el = document.getElementById("default_brand_id");
    el.innerHTML = "<option value=''>Select brand</option>" + allBrands.map(function (b) {
        const s = Number(b.brand_id) === Number(selectedId) ? " selected" : "";
        return "<option value='" + esc(b.brand_id) + "'" + s + ">" + esc(b.brand_name || "") + "</option>";
    }).join("");
    syncSelectDropdown(el);
}

function renderDealsTable(selectedIds) {
    const body = document.getElementById("dealsBody");
    if (!body) return;
    const selectedSet = new Set((selectedIds || []).map(function (v) { return Number(v); }));
    if (!Array.isArray(allDeals) || !allDeals.length) {
        body.innerHTML = "<div class='deals-row'><div class='deals-sno'>-</div><div class='deals-sel'></div><div class='deals-text'>No deals found</div></div>";
        return;
    }
    body.innerHTML = allDeals.map(function (d, i) {
        const id = Number(d.deals_id || 0);
        const ck = selectedSet.has(id) ? " checked" : "";
        return "<div class='deals-row'>"
            + "<div class='deals-sno'>" + (i + 1) + "</div>"
            + "<div class='deals-sel'><input type='checkbox' value='" + esc(id) + "'" + ck + "></div>"
            + "<div class='deals-text'>" + esc(d.deals_name || "") + "</div>"
            + "</div>";
    }).join("");
}

function getDealsSelected() {
    return Array.from(document.querySelectorAll("#dealsBody input[type='checkbox']:checked")).map(function (cb) {
        return Number(cb.value || 0);
    }).filter(function (v) { return v > 0; });
}

function boolSet(id, value) {
    const el = document.getElementById(id);
    if (el) el.checked = Number(value) === 1 || value === true || value === "1";
}

function trimTrailingBlankRows(rows, kind) {
    const source = Array.isArray(rows) ? rows.slice() : [];
    while (source.length) {
        const last = source[source.length - 1];
        const isBlank = kind === "bank"
            ? !(last && (last.ac_holder || last.ac_number || last.bank_name || last.ifsc_code || last.pin_code))
            : !(last && (Number(last.product_id) > 0 || Number(last.brand_id) > 0 || String(last.pack || "").trim()));
        if (!isBlank) break;
        source.pop();
    }
    return source.length ? source : [kind === "bank" ? buildBankRow() : buildPBRow()];
}

function ensureWorkingRowsForEdit() {
    bankRows = trimTrailingBlankRows(bankRows, "bank");
    sellerRows = trimTrailingBlankRows(sellerRows, "pb");
    buyerRows = trimTrailingBlankRows(buyerRows, "pb");
    bankRows.push(buildBankRow());
    sellerRows.push(buildPBRow());
    buyerRows.push(buildPBRow());
    bankEdit = { row: -1, field: "" };
    pbEdit = { side: "", row: -1, field: "" };
    renderBankRows(bankRows);
    renderPBRows("sellerBody", sellerRows);
    renderPBRows("buyerBody", buyerRows);
}

function resetForm() {
    partyForm.reset();
    document.getElementById("party_id").value = "";
    activePartyId = null;
    activeBankRowIndex = 0;
    clearPartyNameError();
    clearCityError();
    boolSet("party_role_byr", 1);
    boolSet("party_role_slr", 1);
    boolSet("party_role_sb", 0);
    boolSet("party_role_bb", 0);
    boolSet("is_active", 1);
    renderCityOptions("");
    renderAreaOptions("");
    renderZoneAreaOptions("");
    renderGroupOptions("");
    renderDefaultProductOptions("");
    renderDefaultBrandOptions("");
    renderDealsTable([]);
    divisionBalanceRows = [buildDivisionRow()];
    renderDivisionRows(divisionBalanceRows);
    renderConditionChecks("paymentConditionBody", allConditions.filter(function (c) { return Number(c.payment_condition) === 1; }), new Set());
    renderConditionChecks("packingConditionBody", allConditions.filter(function (c) { return Number(c.packing_condition) === 1; }), new Set());
    bankRows = [buildBankRow()];
    sellerRows = [buildPBRow()];
    buyerRows = [buildPBRow()];
    bankEdit = { row: -1, field: "" };
    pbEdit = { side: "", row: -1, field: "" };
    renderBankRows(bankRows);
    renderPBRows("sellerBody", sellerRows);
    renderPBRows("buyerBody", buyerRows);
    setActiveChildSection("");
    renderParties();
    syncAllSelectDropdowns();
    setMode("add");
    setFormEditable(true);
}

function fillForm(base, details) {
    function setVal(id, val, fallback) {
        const el = document.getElementById(id);
        if (!el) return;
        let nextVal = (val ?? fallback ?? "");
        if (typeof nextVal === "string") {
            const trimmed = nextVal.trim();
            if (trimmed === "0000-00-00" || trimmed.startsWith("0000-00-00")) {
                nextVal = "";
            }
        }
        if (el.type === "date") {
            const s = (nextVal || "").toString().trim();
            if (s && !/^\d{4}-\d{2}-\d{2}$/.test(s)) {
                nextVal = "";
            }
        }
        el.value = nextVal;
        if (el.tagName === "SELECT" && el.dataset.dropdownInputId) {
            syncSelectDropdown(el);
        }
    }
    setVal("party_id", base.party_id, "");
    setVal("party_name", base.party_name, "");
    const matchedCity = allCities.find(function (c) { return (c.city_name || "").toLowerCase() === (base.city || "").toLowerCase(); });
    renderCityOptions(matchedCity ? matchedCity.city_id : "");
    renderAreaOptions(base.area || "");
    renderZoneAreaOptions(base.zone_area || "");
    setVal("city", base.city, "");
    setVal("state", base.state, "");
    setVal("state_view", base.state, "");
    ["pin_code","contact_no","gst_no","pan_no","email","opening_balance","balance_type","category","address1","address2","address3","address4","group_name","sms_ac","mobile_no","sms_ow","trans","proprietor","fssai_no","lock_date","party_type","cr_day","comp_group","co_name","remarks","office_address1","office_address2","office_address3","office_pin","office_phone","office_mobile","wp1","wp2","wp3","wp4","default_product_id","default_brand_id"].forEach(function (k) { setVal(k, base[k], ""); });
    setVal("area", base.area, "");
    setVal("zone_area", base.zone_area, "");
    syncOfficeLocationFields(base.city || "", base.state || "");
    boolSet("party_role_byr", base.party_role_byr);
    boolSet("party_role_slr", base.party_role_slr);
    boolSet("party_role_sb", base.party_role_sb);
    boolSet("party_role_bb", base.party_role_bb);
    boolSet("is_active", base.is_active);
    boolSet("multiple_sms_session", base.multiple_sms_session);
    boolSet("sms_reg", base.sms_reg);
    boolSet("wp_reg", base.wp_reg);
    boolSet("email_reg", base.email_reg);
    renderGroupOptions(base.group_name || "");
    renderDefaultProductOptions(base.default_product_id || "");
    renderDefaultBrandOptions(base.default_brand_id || "");
    renderDealsTable(details.deals_ids || []);
    divisionBalanceRows = (details.division_balances && details.division_balances.length) ? details.division_balances : [buildDivisionRow()];
    renderDivisionRows(divisionBalanceRows);
    renderConditionChecks("paymentConditionBody", allConditions.filter(function (c) { return Number(c.payment_condition) === 1; }), new Set((details.payment_condition_ids || []).map(Number)));
    renderConditionChecks("packingConditionBody", allConditions.filter(function (c) { return Number(c.packing_condition) === 1; }), new Set((details.packing_condition_ids || []).map(Number)));
    activeBankRowIndex = 0;
    bankRows = (details.bank_rows && details.bank_rows.length) ? details.bank_rows.slice() : [buildBankRow()];
    sellerRows = (details.seller_rows && details.seller_rows.length) ? details.seller_rows.slice() : [buildPBRow()];
    buyerRows = (details.buyer_rows && details.buyer_rows.length) ? details.buyer_rows.slice() : [buildPBRow()];
    bankEdit = { row: -1, field: "" };
    pbEdit = { side: "", row: -1, field: "" };
    renderBankRows(bankRows);
    renderPBRows("sellerBody", sellerRows);
    renderPBRows("buyerBody", buyerRows);
    setActiveChildSection("");
    activePartyId = Number(base.party_id);
    clearPartyNameError();
    clearCityError();
    setMode("display");
    setFormEditable(false);
    renderParties();
}

function renderParties() {
    const keyword = filterInput.value.trim().toLowerCase();
    const rows = allParties.filter(function (p) {
        return !keyword || (p.party_name || "").toLowerCase().includes(keyword) || (p.city || "").toLowerCase().includes(keyword);
    });
    if (!rows.length) {
        partyList.innerHTML = "<div class='party-row'><div class='party-row-name'>No parties found</div></div>";
        return;
    }
    partyList.innerHTML = rows.map(function (p) {
        const active = Number(p.party_id) === activePartyId ? "active" : "";
        return "<div class='party-row " + active + "' data-id='" + esc(p.party_id) + "'>"
            + "<div class='party-row-name'>" + esc(p.party_name || "") + "</div>"
            + "<div class='party-row-sub'>" + esc((p.city || "") + ((p.state || "") ? (" | " + p.state) : "")) + "</div>"
            + "</div>";
    }).join("");

    const activeRow = partyList.querySelector(".party-row.active");
    if (activeRow) {
        activeRow.scrollIntoView({ block: "nearest" });
    }

    partyList.querySelectorAll(".party-row").forEach(function (row) {
        row.addEventListener("click", async function () {
            const id = Number(this.dataset.id);
            const base = allParties.find(function (p) { return Number(p.party_id) === id; });
            if (!base) return;
            const body = new URLSearchParams();
            body.set("party_id", String(id));
            const res = await fetch("../../api/party/get_party_details.php", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: body.toString() });
            fillForm(base, await res.json());
        });
    });
}

async function loadMeta() {
    const res = await Promise.all([
        fetch("../../api/party/get_cities.php"),
        fetch("../../api/group/get_group.php"),
        fetch("../../api/condition/get_condition.php"),
        fetch("../../api/product/get_product.php"),
        fetch("../../api/brand/get_brand.php"),
        fetch("../../api/deals_in/get_deals_in.php"),
        fetch("../../api/bank/get_bank.php"),
        fetch("../../api/company/get_company.php"),
        fetch("../../api/district/get_district.php"),
        fetch("../../api/area/get_area.php")
    ]);
    allCities = await res[0].json();
    allGroups = await res[1].json();
    allConditions = await res[2].json();
    allProducts = await res[3].json();
    allBrands = await res[4].json();
    allDeals = await res[5].json();
    allBanks = await res[6].json();
    allCompanies = await res[7].json();
    allDistricts = await res[8].json();
    allZoneAreas = await res[9].json();
    if (!Array.isArray(allBanks)) allBanks = [];
    if (!Array.isArray(allDeals)) allDeals = [];
    if (!Array.isArray(allCompanies)) allCompanies = [];
    if (!Array.isArray(allDistricts)) allDistricts = [];
    if (!Array.isArray(allZoneAreas)) allZoneAreas = [];
    ensurePartyMasterLookups();
}

async function loadParties() {
    const res = await fetch("../../api/party/get_party.php");
    allParties = await res.json();
    renderParties();
}

async function saveParty(isModify) {
    syncCityStateFields();
    const fd = new FormData(partyForm);
    const partyId = Number(fd.get("party_id") || 0);
    const partyName = (fd.get("party_name") || "").trim();
    const cityName = (fd.get("city") || "").trim();
    if (checkDuplicatePartyName()) {
        partyNameInput.focus();
        partyNameInput.select();
        return;
    }
    if (!partyName || !cityName) {
        if (hasAnyOtherFieldValue()) {
            if (!partyName) showPartyNameErrorMessage("Required");
            if (!cityName) showCityError();
        }
        return;
    }
    clearPartyNameError();
    clearCityError();
    if (isModify && partyId <= 0) { alert("Select a party to modify"); return; }
    fd.set("bank_rows_json", JSON.stringify(readBankRows()));
    fd.set("seller_rows_json", JSON.stringify(readPBRows("sellerBody")));
    fd.set("buyer_rows_json", JSON.stringify(readPBRows("buyerBody")));
    fd.set("division_balances_json", JSON.stringify(readDivisionRows()));
    fd.set("payment_condition_ids_json", JSON.stringify(checkedValues("paymentConditionBody")));
    fd.set("packing_condition_ids_json", JSON.stringify(checkedValues("packingConditionBody")));
    fd.set("deals_ids_json", JSON.stringify(getDealsSelected()));
    if (!isModify && typeof window.confirmAdd === "function") {
        const ok = await window.confirmAdd("Are you sure you want to add this party?\n\n" + partyName);
        if (!ok) return;
    }

    const res = await fetch(isModify ? "../../api/party/update_party.php" : "../../api/party/add_party.php", { method: "POST", body: fd });
    const data = await res.json();
    if (!data || data.status !== "success") { alert((data && data.message) || "Request failed"); }
    if (data.status === "success") {
        await loadParties();
        if (!isModify) {
            resetForm();
            return;
        }
        const updated = allParties.find(function (p) { return Number(p.party_id) === Number(partyId); });
        if (updated) {
            const body = new URLSearchParams();
            body.set("party_id", String(partyId));
            const res2 = await fetch("../../api/party/get_party_details.php", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: body.toString() });
            fillForm(updated, await res2.json());
        }
    }
}

function handleAddAction() {
    preEditPartyId = activePartyId;
    resetForm();
    setMode("add");
    setFormEditable(true);
    setTimeout(function () { partyNameInput.focus(); }, 0);
}

async function deleteParty() {
    const partyId = Number(document.getElementById("party_id").value || 0);
    if (partyId <= 0) { alert("Select a party to delete"); return; }
    const ok = await window.confirmDelete("Delete this party?");
    if (!ok) return;
    const body = new URLSearchParams();
    body.set("party_id", String(partyId));
    const res = await fetch("../../api/party/delete_party.php", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: body.toString() });
    const data = await res.json();
    if (!data || data.status !== "success") { alert((data && data.message) || "Request failed"); }
    if (data.status === "success") {
        await loadParties();
        if (allParties.length) {
            const first = allParties[0];
            const body2 = new URLSearchParams();
            body2.set("party_id", String(first.party_id));
            const res2 = await fetch("../../api/party/get_party_details.php", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: body2.toString() });
            fillForm(first, await res2.json());
        } else {
            resetForm();
            setMode("display");
            setFormEditable(false);
        }
    }
}

function switchTab(name) {
    document.querySelectorAll(".tab-btn").forEach(function (b) { b.classList.toggle("active", b.dataset.tab === name); });
    document.querySelectorAll(".tab-pane").forEach(function (p) { p.classList.toggle("active", p.id === ("tab-" + name)); });
    setActiveChildSection("");
}

function setActivePBRow(side, index, shouldFocus) {
    const body = side === "seller" ? sellerBody : buyerBody;
    const rows = Array.from(body.querySelectorAll(".pb-row"));
    if (!rows.length) {
        activePBRowIndex[side] = 0;
        return;
    }
    setActiveChildSection(side);
    activePBRowIndex[side] = Math.max(0, Math.min(rows.length - 1, Number(index) || 0));
    rows.forEach(function (row, rowIndex) {
        row.classList.toggle("is-active", rowIndex === activePBRowIndex[side]);
    });
    const row = rows[activePBRowIndex[side]];
    if (!row) return;
    row.scrollIntoView({ block: "nearest" });
    if (shouldFocus) row.focus();
}

function focusPBField(side, selector) {
    const fieldName = selector === ".pb-brand" ? "brand_id" : selector === ".pb-pack" ? "pack" : "product_id";
    openPBEditor(side, activePBRowIndex[side], fieldName);
}

function addPBRowFromShortcut(side) {
    const targetRows = side === "seller" ? sellerRows : buyerRows;
    if (!targetRows.length) targetRows.push(buildPBRow());
    const last = targetRows[targetRows.length - 1];
    if (last && !(Number(last.product_id) > 0 || Number(last.brand_id) > 0 || String(last.pack || "").trim())) {
        activePBRowIndex[side] = targetRows.length - 1;
    } else {
        targetRows.push(buildPBRow());
        activePBRowIndex[side] = targetRows.length - 1;
    }
    renderPBRows(side === "seller" ? "sellerBody" : "buyerBody", targetRows);
    openPBEditor(side, activePBRowIndex[side], "product_id");
}

function deletePBRowFromShortcut(side) {
    const rows = side === "seller" ? sellerRows : buyerRows;
    const baseRows = rows.length ? rows.slice() : [buildPBRow()];
    const removeIndex = Math.max(0, Math.min(baseRows.length - 1, activePBRowIndex[side] || 0));
    baseRows.splice(removeIndex, 1);
    const nextRows = baseRows.length ? baseRows : [buildPBRow()];
    if (side === "seller") {
        sellerRows = nextRows;
    } else {
        buyerRows = nextRows;
    }
    activePBRowIndex[side] = Math.max(0, Math.min(nextRows.length - 1, removeIndex));
    pbEdit = { side: "", row: -1, field: "" };
    renderPBRows(side === "seller" ? "sellerBody" : "buyerBody", nextRows);
}

function moveBackFromPB(side) {
    const body = side === "buyer" ? buyerBody : sellerBody;
    body.focus();
    setActiveChildSection(side);
}

function openBankEditor(rowIndex, field) {
    bankEdit = { row: rowIndex, field: field };
    renderBankRows(bankRows);
}

function closeBankEditor() {
    bankEdit = { row: -1, field: "" };
    renderBankRows(bankRows);
}

function openPBEditor(side, rowIndex, field) {
    pbEdit = { side: side, row: rowIndex, field: field };
    renderPBRows(side === "seller" ? "sellerBody" : "buyerBody", side === "seller" ? sellerRows : buyerRows);
}

function closePBEditor(side) {
    pbEdit = { side: "", row: -1, field: "" };
    renderPBRows(side === "seller" ? "sellerBody" : "buyerBody", side === "seller" ? sellerRows : buyerRows);
}

function commitBankField(rowIndex, field, value) {
    if (!bankRows[rowIndex]) bankRows[rowIndex] = buildBankRow();
    if (field === "ac_holder" || field === "ac_number") {
        bankRows[rowIndex][field] = String(value || "").trim();
        return bankRows[rowIndex][field].length > 0;
    }
    if (field === "bank_name") {
        const bank = bankMatch(value);
        if (!bank) return false;
        bankRows[rowIndex].bank_id = Number(bank.bank_id || 0);
        bankRows[rowIndex].bank_name = bankDisplayName(bank);
        bankRows[rowIndex].ifsc_code = bank.ifsc_code || "";
        bankRows[rowIndex].pin_code = bank.pin || "";
        return true;
    }
    return false;
}

function finishBankRow(rowIndex) {
    const row = bankRows[rowIndex] || buildBankRow();
    if (!(row.ac_holder || row.ac_number || row.bank_name)) {
        if (bankRows.length > 1) bankRows.splice(rowIndex, 1);
        activeBankRowIndex = Math.max(0, Math.min(bankRows.length - 1, rowIndex - 1));
        bankEdit = { row: -1, field: "" };
        renderBankRows(bankRows);
        return;
    }
    const isLast = rowIndex === bankRows.length - 1;
    if (isLast) {
        bankRows.push(buildBankRow());
        activeBankRowIndex = rowIndex + 1;
        bankEdit = { row: activeBankRowIndex, field: "ac_holder" };
        renderBankRows(bankRows);
        return;
    }
    activeBankRowIndex = rowIndex;
    bankEdit = { row: -1, field: "" };
    renderBankRows(bankRows);
}

function commitPBField(side, rowIndex, field, value) {
    const rows = side === "seller" ? sellerRows : buyerRows;
    if (!rows[rowIndex]) rows[rowIndex] = buildPBRow();
    if (field === "product_id") {
        const product = productMatch(value);
        if (!product) return false;
        rows[rowIndex].product_id = Number(product.product_id || 0);
        return rows[rowIndex].product_id > 0;
    }
    if (field === "brand_id") {
        const brand = brandMatch(value);
        if (!brand) return false;
        rows[rowIndex].brand_id = Number(brand.brand_id || 0);
        return rows[rowIndex].brand_id > 0;
    }
    rows[rowIndex][field] = String(value || "").trim() || "0";
    return true;
}

async function commitPBBrand(side, rowIndex, value) {
    const ok = commitPBField(side, rowIndex, "brand_id", value);
    if (ok) return true;
    const brandName = String(value || "").trim();
    if (!brandName) {
        const rows = side === "seller" ? sellerRows : buyerRows;
        rows[rowIndex].brand_id = 0;
        return true;
    }
    const create = window.confirm("Brand Not Found....!!,Are you sure to Save This Brand");
    if (!create) return false;
    const body = new URLSearchParams();
    body.set("brand_name", brandName);
    body.set("sort_order", "100");
    const res = await fetch("../../api/brand/add_brand.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body.toString()
    });
    const data = await res.json();
    if (!data || (data.status !== "success" && !/already exists/i.test(data.message || ""))) {
        alert((data && data.message) || "Failed to add brand");
        return false;
    }
    const brandRes = await fetch("../../api/brand/get_brand.php");
    allBrands = await brandRes.json();
    ensurePartyMasterLookups();
    return commitPBField(side, rowIndex, "brand_id", brandName);
}

function finishPBRow(side, rowIndex) {
    const rows = side === "seller" ? sellerRows : buyerRows;
    const row = rows[rowIndex] || buildPBRow();
    if (!(Number(row.product_id) > 0 || Number(row.brand_id) > 0 || String(row.pack || "").trim())) {
        if (rows.length > 1) rows.splice(rowIndex, 1);
        activePBRowIndex[side] = Math.max(0, Math.min(rows.length - 1, rowIndex - 1));
        pbEdit = { side: "", row: -1, field: "" };
        renderPBRows(side === "seller" ? "sellerBody" : "buyerBody", rows);
        return;
    }
    const isLast = rowIndex === rows.length - 1;
    if (isLast) {
        rows.push(buildPBRow());
        activePBRowIndex[side] = rowIndex + 1;
        pbEdit = { side: side, row: activePBRowIndex[side], field: "product_id" };
        renderPBRows(side === "seller" ? "sellerBody" : "buyerBody", rows);
        return;
    }
    activePBRowIndex[side] = rowIndex;
    pbEdit = { side: "", row: -1, field: "" };
    renderPBRows(side === "seller" ? "sellerBody" : "buyerBody", rows);
}

document.querySelectorAll(".tab-btn").forEach(function (btn) {
    btn.addEventListener("click", function () { switchTab(btn.dataset.tab); });
});

bankBody.addEventListener("click", function (e) {
    const row = e.target.closest(".bank-row");
    if (!row) return;
    setActiveBankRow(Number(row.dataset.bankIndex || 0), false);
});
bankBody.addEventListener("focusin", function (e) {
    const row = e.target.closest(".bank-row");
    if (!row) return;
    setActiveBankRow(Number(row.dataset.bankIndex || 0), false);
});

bankBody.addEventListener("input", function (e) {
    const editor = e.target.closest(".bank-editor");
    if (!editor) return;
    const rowIndex = Number(editor.dataset.row || -1);
    const field = editor.dataset.field || "";
    if (rowIndex < 0 || !field) return;
    if (field === "ac_holder" || field === "ac_number") {
        commitBankField(rowIndex, field, editor.value);
    }
});

bankBody.addEventListener("keydown", function (e) {
    const editor = e.target.closest(".bank-editor");
    if (!editor) return;
    const rowIndex = Number(editor.dataset.row || -1);
    const field = editor.dataset.field || "";
    if (rowIndex < 0 || !field) return;
    if (e.key === "Enter") {
        e.preventDefault();
        if (field === "ac_holder") {
            if (!commitBankField(rowIndex, field, editor.value)) {
                if (bankRows[rowIndex] && !bankRows[rowIndex].ac_number && !bankRows[rowIndex].bank_name && bankRows.length > 1) {
                    bankRows.splice(rowIndex, 1);
                    activeBankRowIndex = Math.max(0, rowIndex - 1);
                }
                closeBankEditor();
                return;
            }
            openBankEditor(rowIndex, "ac_number");
            return;
        }
        if (field === "ac_number") {
            if (!commitBankField(rowIndex, field, editor.value)) return;
            openBankEditor(rowIndex, "bank_name");
            return;
        }
        if (field === "bank_name") {
            const ok = commitBankField(rowIndex, field, editor.value);
            if (!ok) return;
            finishBankRow(rowIndex);
        }
    }
    if (e.key === "F1") {
        e.preventDefault();
        if (field === "bank_name") {
            openBankEditor(rowIndex, "ac_number");
            return;
        }
        if (field === "ac_number") {
            openBankEditor(rowIndex, "ac_holder");
            return;
        }
        if (field === "ac_holder") {
            closeBankEditor();
            return;
        }
    }
    if (e.key === "Escape") {
        e.preventDefault();
        if (!bankRows[rowIndex].ac_holder && !bankRows[rowIndex].ac_number && !bankRows[rowIndex].bank_name && bankRows.length > 1) {
            bankRows.splice(rowIndex, 1);
            activeBankRowIndex = Math.max(0, rowIndex - 1);
        }
        closeBankEditor();
    }
});

sellerBody.addEventListener("click", function (e) {
    const row = e.target.closest(".pb-row");
    if (!row) return;
    setActivePBRow("seller", Number(row.dataset.pbIndex || 0), false);
});

sellerBody.addEventListener("focusin", function (e) {
    const row = e.target.closest(".pb-row");
    if (!row) return;
    setActivePBRow("seller", Number(row.dataset.pbIndex || 0), false);
});

buyerBody.addEventListener("click", function (e) {
    const row = e.target.closest(".pb-row");
    if (!row) return;
    setActivePBRow("buyer", Number(row.dataset.pbIndex || 0), false);
});

buyerBody.addEventListener("focusin", function (e) {
    const row = e.target.closest(".pb-row");
    if (!row) return;
    setActivePBRow("buyer", Number(row.dataset.pbIndex || 0), false);
});

function bindPBBody(body, side) {
    body.addEventListener("change", async function (e) {
        const editor = e.target.closest(".pb-editor");
        if (!editor) return;
        const rowIndex = Number(editor.dataset.row || -1);
        const field = editor.dataset.field || "";
        if (rowIndex < 0 || !field) return;
        if (field === "product_id") {
            if (!commitPBField(side, rowIndex, field, editor.value)) return;
            openPBEditor(side, rowIndex, "brand_id");
            return;
        }
        if (field === "brand_id") {
            const ok = await commitPBBrand(side, rowIndex, editor.value);
            if (!ok) return;
            openPBEditor(side, rowIndex, "pack");
        }
    });

    body.addEventListener("keydown", async function (e) {
        const editor = e.target.closest(".pb-editor");
        if (!editor) return;
        const rowIndex = Number(editor.dataset.row || -1);
        const field = editor.dataset.field || "";
        if (rowIndex < 0 || !field) return;
        if (e.key === "Enter") {
            e.preventDefault();
            if (field === "product_id") {
                if (!commitPBField(side, rowIndex, field, editor.value)) return;
                openPBEditor(side, rowIndex, "brand_id");
                return;
            }
            if (field === "brand_id") {
                const ok = await commitPBBrand(side, rowIndex, editor.value);
                if (!ok) return;
                openPBEditor(side, rowIndex, "pack");
                return;
            }
            if (field === "pack") {
                commitPBField(side, rowIndex, field, editor.value);
                finishPBRow(side, rowIndex);
            }
        }
        if (e.key === "F1") {
            e.preventDefault();
            if (field === "pack") {
                openPBEditor(side, rowIndex, "brand_id");
                return;
            }
            if (field === "brand_id") {
                openPBEditor(side, rowIndex, "product_id");
                return;
            }
            if (field === "product_id") {
                closePBEditor(side);
                return;
            }
        }
        if (e.key === "Escape") {
            e.preventDefault();
            const rows = side === "seller" ? sellerRows : buyerRows;
            const row = rows[rowIndex] || buildPBRow();
            if (!(Number(row.product_id) > 0 || Number(row.brand_id) > 0 || String(row.pack || "").trim()) && rows.length > 1) {
                rows.splice(rowIndex, 1);
                activePBRowIndex[side] = Math.max(0, rowIndex - 1);
            }
            closePBEditor(side);
        }
    });
}

bindPBBody(sellerBody, "seller");
bindPBBody(buyerBody, "buyer");

citySelect.addEventListener("change", function () {
    syncCityStateFields();
    if (citySelect.value) clearCityError();
});
if (divisionBalanceBody) {
    divisionBalanceBody.addEventListener("click", function (e) {
        if (!e.target.closest(".division-del")) return;
        const rows = readDivisionRows();
        const idx = Array.from(divisionBalanceBody.querySelectorAll(".division-grid-row")).indexOf(e.target.closest(".division-grid-row"));
        if (idx >= 0) rows.splice(idx, 1);
        divisionBalanceRows = rows.length ? rows : [buildDivisionRow()];
        renderDivisionRows(divisionBalanceRows);
    });
}
filterInput.addEventListener("input", renderParties);
document.getElementById("addBtn").addEventListener("click", handleAddAction);
document.getElementById("modifyBtn").addEventListener("click", function () {
    const partyId = Number(document.getElementById("party_id").value || 0);
    if (!partyId) { alert("Select a party to modify"); return; }
    preEditPartyId = partyId;
    setMode("modify");
    setFormEditable(true);
    ensureWorkingRowsForEdit();
    setTimeout(function () { partyNameInput.focus(); }, 0);
});
document.getElementById("deleteBtn").addEventListener("click", deleteParty);
document.getElementById("printBtn").addEventListener("click", function () { window.print(); });
document.getElementById("exitBtn").addEventListener("click", function () { window.location.href = "../../index.html"; });
document.getElementById("saveBtn").addEventListener("click", function () {
    saveParty(rightPanel.classList.contains("modify-mode"));
});
document.getElementById("cancelBtn").addEventListener("click", function () {
    if (preEditPartyId) {
        const prev = allParties.find(function (p) { return Number(p.party_id) === Number(preEditPartyId); });
        if (prev) {
            const body = new URLSearchParams();
            body.set("party_id", String(prev.party_id));
            fetch("../../api/party/get_party_details.php", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: body.toString() })
                .then(res => res.json())
                .then(data => fillForm(prev, data));
            return;
        }
    }
    if (allParties.length) {
        const first = allParties[0];
        const body = new URLSearchParams();
        body.set("party_id", String(first.party_id));
        fetch("../../api/party/get_party_details.php", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: body.toString() })
            .then(res => res.json())
            .then(data => fillForm(first, data));
        return;
    }
    resetForm();
});

partyNameInput.addEventListener("input", function () {
    if ((partyNameInput.value || "").trim()) {
        checkDuplicatePartyName();
    } else {
        clearPartyNameError();
    }
});

document.addEventListener("keydown", function (e) {
    if (!rightPanel.classList.contains("display-mode")) return;
    if (e.key !== "ArrowDown" && e.key !== "ArrowUp") return;
    const rows = Array.from(partyList.querySelectorAll(".party-row")).filter(function (r) { return r.dataset.id; });
    if (!rows.length) return;
    let currentIndex = rows.findIndex(function (row) { return Number(row.dataset.id) === Number(activePartyId); });
    if (currentIndex === -1) currentIndex = 0;
    if (e.key === "ArrowDown") {
        e.preventDefault();
        if (currentIndex < rows.length - 1) currentIndex += 1;
        rows[currentIndex].click();
    }
    if (e.key === "ArrowUp") {
        e.preventDefault();
        if (currentIndex > 0) currentIndex -= 1;
        rows[currentIndex].click();
    }
});

document.addEventListener("keydown", function (e) {
    const inDisplayMode = rightPanel.classList.contains("display-mode");
    const target = e.target;
    const isTypingField = target && target.matches && target.matches("input:not([type='checkbox']):not([type='radio']), textarea");

    if (!inDisplayMode) {
        if ((e.key === "A" || e.key === "a" || e.key === "E" || e.key === "e" || e.key === "D" || e.key === "d") && isTypingField) {
            return;
        }

        if (activeChildSection === "bank") {
            if (e.key === "ArrowDown" || e.key === "ArrowUp") {
                const rows = Array.from(document.querySelectorAll("#bankBody .bank-row"));
                if (!rows.length) return;
                e.preventDefault();
                setActiveBankRow(activeBankRowIndex + (e.key === "ArrowDown" ? 1 : -1), true);
                return;
            }
            if (e.key === "A" || e.key === "a") {
                e.preventDefault();
                addBankRowFromShortcut();
                return;
            }
            if (e.key === "E" || e.key === "e") {
                e.preventDefault();
                focusBankField(".bank-ac-holder");
                return;
            }
            if (e.key === "D" || e.key === "d") {
                e.preventDefault();
                deleteBankRowFromShortcut();
                return;
            }
            if (e.key === "F1") {
                e.preventDefault();
                focusMoveBackFromBank();
                return;
            }
        }

        if (activeChildSection === "seller" || activeChildSection === "buyer") {
            const side = activeChildSection;
            if (e.key === "ArrowDown" || e.key === "ArrowUp") {
                const rows = Array.from((side === "seller" ? sellerBody : buyerBody).querySelectorAll(".pb-row"));
                if (!rows.length) return;
                e.preventDefault();
                setActivePBRow(side, activePBRowIndex[side] + (e.key === "ArrowDown" ? 1 : -1), true);
                return;
            }
            if (e.key === "A" || e.key === "a") {
                e.preventDefault();
                addPBRowFromShortcut(side);
                return;
            }
            if (e.key === "E" || e.key === "e") {
                e.preventDefault();
                focusPBField(side, ".pb-product");
                return;
            }
            if (e.key === "D" || e.key === "d") {
                e.preventDefault();
                deletePBRowFromShortcut(side);
                return;
            }
            if (e.key === "F1") {
                e.preventDefault();
                moveBackFromPB(side);
                return;
            }
        }

        if (e.key === "F6") {
            e.preventDefault();
            document.getElementById("saveBtn").click();
            return;
        }
        if (e.key === "Escape") {
            e.preventDefault();
            document.getElementById("cancelBtn").click();
            return;
        }
        return;
    }

    if (e.key === "F2") { e.preventDefault(); handleAddAction(); }
    if (e.key === "F3") { e.preventDefault(); document.getElementById("modifyBtn").click(); }
    if (e.key === "F5") { e.preventDefault(); deleteParty(); }
    if (e.key === "F7") { e.preventDefault(); window.print(); }
    if (e.key === "Escape") { e.preventDefault(); document.getElementById("exitBtn").click(); }
});

document.addEventListener("mousedown", function (e) {
    const wrapper = e.target.closest(".dropdown-wrapper");
    if (wrapper) return;
    closeAllSelectDropdowns();
});

(async function init() {
    await loadMeta();
    await loadParties();
    enhanceSelectsIn(partyForm);
    if (allParties.length) {
        const first = allParties[0];
        const body = new URLSearchParams();
        body.set("party_id", String(first.party_id));
        const res = await fetch("../../api/party/get_party_details.php", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: body.toString() });
        fillForm(first, await res.json());
    } else {
        resetForm();
    }
})();
