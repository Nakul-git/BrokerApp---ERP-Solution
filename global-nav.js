function normalizePageKey(pathOrHref) {
    try {
        const base = String(pathOrHref || "").split("/").pop().split("?")[0].split("#")[0];
        return base.toUpperCase().replace(/[^A-Z0-9]+/g, "_");
    } catch (e) {
        return "";
    }
}

function getAppRoot() {
    const path = String(location.pathname || "").replace(/\\/g, "/");
    const lower = path.toLowerCase();
    const marker = "/brokerapp/";
    const idx = lower.indexOf(marker);
    if (idx !== -1) {
        return location.origin + path.slice(0, idx + marker.length);
    }
    return location.origin + "/";
}

function resolveAppHref(href) {
    const value = String(href || "");
    if (!value || value.startsWith("#") || value.startsWith("javascript:")) return value;
    if (/^[a-z]+:/i.test(value)) return value;
    if (value.startsWith("/")) return location.origin + value;
    return getAppRoot() + value;
}

(function () {
    const current = (location.pathname.split('/').pop() || '').toLowerCase();
    if (current === 'login.html' || current === 'register.html') return;

    const ROUTE_SECTION_MAP = {
        "account_group.html": "MASTERS",
        "add_less_parameter.html": "MISC. MASTERS",
        "area.html": "MISC. MASTERS",
        "bank_master.html": "MISC. MASTERS",
        "brand_master.html": "MASTERS",
        "city.html": "MISC. MASTERS",
        "company_group_master.html": "MISC. MASTERS",
        "condition_master.html": "MISC. MASTERS",
        "courier_master.html": "MISC. MASTERS",
        "deals_in_master.html": "MISC. MASTERS",
        "district_master.html": "MISC. MASTERS",
        "division_master.html": "MISC. MASTERS",
        "form_wise_book_setup.html": "MISC. MASTERS",
        "group_master.html": "ACCOUNT GROUP",
        "group_setup.html": "ACCOUNT GROUP",
        "group_transfer.html": "ACCOUNT GROUP",
        "length_master.html": "MISC. MASTERS",
        "line_master.html": "MISC. MASTERS",
        "master_data.html": "MASTER DATA",
        "master_data_entry.html": "MASTER DATA",
        "master_data_print.html": "MASTER DATA",
        "narration_master.html": "MISC. MASTERS",
        "note_master.html": "MISC. MASTERS",
        "party.html": "MASTERS",
        "party_category_master.html": "MISC. MASTERS",
        "party_type_master.html": "MISC. MASTERS",
        "party_wise_brokerage_rate_setup.html": "MASTERS",
        "product.html": "MASTERS",
        "product_group_master.html": "MISC. MASTERS",
        "product_type_master.html": "MISC. MASTERS",
        "shift_division.html": "MASTERS",
        "sku_unit_master.html": "MISC. MASTERS",
        "state.html": "MISC. MASTERS",
        "term_type_master.html": "MISC. MASTERS",
        "transport_master.html": "MASTERS",
        "loading_entry.html": "TRANSACTIONS",
        "loading_register.html": "TRANSACTIONS",
        "payment_entry.html": "TRANSACTIONS",
        "sales.html": "TRANSACTIONS",
        "sales_registry.html": "TRANSACTIONS",
        "transactions.html": "TRANSACTIONS",
        "screening.html": "SCREENING",
        "printing.html": "PRINTING",
        "bill_section.html": "BILL SECTION",
        "account_master.html": "ACCOUNTING",
        "role_master.html": "USER SECURITY",
        "user_log.html": "USER SECURITY",
        "backup.html": "UTILITIES",
        "bf_bal_from_prv_year.html": "UTILITIES",
        "calculator.html": "UTILITIES",
        "change_fin_yr_div.html": "UTILITIES",
        "company_details.html": "UTILITIES",
        "confirm.html": "UTILITIES",
        "envelope_printing.html": "UTILITIES",
        "exit_software.html": "UTILITIES",
        "find_tb_difference.html": "UTILITIES",
        "merge_account.html": "UTILITIES",
        "merge_city.html": "UTILITIES",
        "merge_delete_data.html": "UTILITIES",
        "missing_voucher_sr_no.html": "UTILITIES",
        "re_numbering.html": "UTILITIES",
        "refresh_payment_outstanding_loading.html": "UTILITIES",
        "rtgs_form_setup.html": "UTILITIES",
        "search.html": "UTILITIES",
        "send_auto_sms.html": "UTILITIES",
        "send_manual_sms.html": "UTILITIES",
        "send_outstanding_sms.html": "UTILITIES",
        "sms_setup.html": "UTILITIES",
        "unit_setup.html": "UTILITIES",
        "user_master.html": "UTILITIES",
        "user_security.html": "USER SECURITY"
    };

    function getRequiredSection(pageName) {
        return ROUTE_SECTION_MAP[pageName] || null;
    }

    async function enforceGlobalAccess() {
        try {
            const res = await fetch(resolveAppHref("api/get_logged_user_permissions.php"), { cache: "no-store" });
            const data = await res.json();

            if (data.status !== "success") {
                window.location.replace(resolveAppHref("login.html"));
                return null;
            }

            const allowedSections = (data.allowed_sections || []).map(s => String(s).toUpperCase());
            const allowedPages = (data.allowed_pages || []).map(p => String(p).toUpperCase());
            const isAdmin = Number(data.is_admin || 0) === 1;
            const hasSelectedCompany = !!data.selected_company_id;
            const hasSelectedDivision = !!data.selected_division_id;
            const required = getRequiredSection(current);
            document.body.dataset.currentPage = current;
            document.body.dataset.requiredSection = required || "";
            document.body.dataset.loggedIsAdmin = isAdmin ? "1" : "0";
            document.body.dataset.selectedCompanyId = data.selected_company_id || "";
            document.body.dataset.selectedCompanyCode = data.selected_company_code || "";
            document.body.dataset.selectedCompanyName = data.selected_company_name || "";
            document.body.dataset.selectedDivisionId = data.selected_division_id || "";
            document.body.dataset.selectedDivisionCode = data.selected_division_code || "";
            document.body.dataset.selectedDivisionName = data.selected_division_name || "";

            if (current !== "company.html" && !hasSelectedCompany) {
                window.location.replace(resolveAppHref("company.html"));
                return null;
            }

            // Check division selection (exempt: company.html, index.html)
            const exemptPagesDivision = new Set(["company.html", "index.html"]);
            if (!exemptPagesDivision.has(current) && !hasSelectedDivision) {
                window.location.replace(resolveAppHref("company.html"));
                return null;
            }

            if (!isAdmin && required && !allowedSections.includes(required)) {
                window.location.replace(resolveAppHref("index.html"));
                return null;
            }

            const pageParams = new URLSearchParams({
                page: current,
                section: required || ""
            });

            const pageRes = await fetch(resolveAppHref("api/get_logged_user_page_permissions.php?" + pageParams.toString()), {
                cache: "no-store"
            });
            const pageData = await pageRes.json();
            const pagePerm = pageData.permissions || {};

            const exemptPages = new Set(["index.html", "company.html"]);
            if (!isAdmin && !exemptPages.has(current) && Number(pagePerm.v || 0) !== 1) {
                window.location.replace(resolveAppHref("index.html"));
                return null;
            }

            return {
                is_admin: isAdmin,
                sections: allowedSections,
                pages: allowedPages
            };
        } catch (e) {
            window.location.replace(resolveAppHref("login.html"));
            return null;
        }
    }

    function buildNav() {
        return `
<nav class="navbar global-erp-nav" aria-label="Primary">
    <div class="erp-top-links" role="menubar">

        <div class="dropdown" data-section="MASTERS">
            <a href="#" data-toggle="dropdown" role="menuitem">Masters</a>
            <div class="dropdown-menu">
                <a href="masters/party_master/party.html">Party Master</a>
                <a href="masters/product_master/product.html">Product Master</a>
                <a href="masters/brand_master/brand_master.html">Brand Master</a>
                <a href="masters/party_wise_brokerage_rate_setup/party_wise_brokerage_rate_setup.html">Party Wise Brokerage Rate Setup</a>
                <a href="masters/transport_master/transport_master.html">Transport Master</a>

                <div class="erp-submenu">
                    <a href="#" data-toggle="submenu">Account Group ></a>
                    <div class="submenu-menu">
                        <a href="masters/account_group/group_master/group_master.html">Group Master</a>
                        <a href="masters/account_group/group_transfer/group_transfer.html">Group Transfer</a>
                        <a href="masters/account_group/group_setup/group_setup.html">Group Setup</a>
                    </div>
                </div>

                <div class="erp-submenu misc-masters">
                    <a href="#" data-toggle="submenu">Misc. Masters ></a>
                    <div class="submenu-menu">
                        <a href="masters/misc_master/state/state.html">State Master</a>
                        <a href="masters/misc_master/city/city.html">City Master</a>
                        <a href="masters/misc_master/district/district_master.html">District Master</a>
                        <a href="masters/misc_master/area/area.html">Zone / Area Master</a>
                        <a href="masters/misc_master/line_master/line_master.html">Line Master</a>
                        <a href="masters/misc_master/length_master/length_master.html">Length Master</a>
                        <a href="masters/misc_master/deals_in_master/deals_in_master.html">Deals In Master</a>
                        <a href="masters/misc_master/term_type_master/term_type_master.html">Term Type Master</a>
                        <a href="masters/misc_master/courier_master/courier_master.html">Courier Master</a>
                        <a href="masters/misc_master/narration_master/narration_master.html">Narration Master</a>
                        <a href="masters/misc_master/sku_unit_master/sku_unit_master.html">SKU(Unit) Master</a>
                        <a href="masters/misc_master/party_type_master/party_type_master.html">Party Type Master</a>
                        <a href="masters/misc_master/party_category_master/party_category_master.html">Party Category Master</a>
                        <a href="masters/misc_master/bank_master/bank_master.html">Bank Master</a>
                        <a href="masters/misc_master/condition_master/condition_master.html">Condition Master</a>
                        <a href="masters/misc_master/note_master/note_master.html">Note Master</a>
                        <a href="masters/misc_master/company_group_master/company_group_master.html">Company Group Master</a>
                        <a href="masters/misc_master/product_type_master/product_type_master.html">Product Type Master</a>
                        <a href="masters/misc_master/product_group_master/product_group_master.html">Product Group Master</a>
                        <a href="masters/misc_master/form_wise_book_setup/form_wise_book_setup.html">Form Wise Book Setup</a>
                        <a href="masters/misc_master/add_less_parameter/add_less_parameter.html">Add Less Parameter</a>
                        <a href="masters/misc_master/division_master/division_master.html">Division Master</a>
                    </div>
                </div>

                <div class="erp-submenu">
                    <a href="#" data-toggle="submenu">Master Data ></a>
                    <div class="submenu-menu">
                        <a href="masters/master_data/master_data_entry/master_data_entry.html">Master Data Entry</a>
                        <a href="masters/master_data/master_data_print/master_data_print.html">Master Data Print</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="dropdown" data-section="TRANSACTIONS">
            <a href="#" data-toggle="dropdown">Transactions</a>
            <div class="dropdown-menu tx-menu">
                <a href="sales.html"><span>Daily Sauda</span><span class="menu-shortcut">Ctrl+S</span></a>
                <a href="transactions/loading_entry/loading_entry.html"><span>Loading Entry</span><span class="menu-shortcut">Ctrl+L</span></a>
                <a href="transactions/payment_entry/payment_entry.html"><span>Payment Entry</span></a>
            </div>
        </div>

        <a href="screening/screening.html" data-section="SCREENING">Screening</a>
        <a href="printing/printing.html" data-section="PRINTING">Printing</a>
        <a href="bill_section/bill_section.html" data-section="BILL SECTION">Bill Section</a>
        <div class="dropdown" data-section="ACCOUNTING">
            <a href="#" data-toggle="dropdown">Accounting</a>
            <div class="dropdown-menu">
                <a href="accounting/account_master/account_master.html">Account Master (General)</a>
            </div>
        </div>

        <div class="dropdown" data-section="UTILITIES">
            <a href="#" data-toggle="dropdown">Utilities</a>
            <div class="dropdown-menu utilities-menu">
                <a href="utilities/backup/backup.html">Backup</a>
                <a href="utilities/user_master/user_master.html">User Master</a>
                <a href="utilities/sms_setup/sms_setup.html">SMS Setup</a>
                <a href="utilities/send_auto_sms/send_auto_sms.html">Send Auto SMS</a>
                <a href="utilities/send_manual_sms/send_manual_sms.html">Send Manual SMS</a>
                <a href="utilities/send_outstanding_sms/send_outstanding_sms.html">Send Outstanding SMS</a>
                <a href="utilities/envelope_printing/envelope_printing.html">Envelope Printing</a>
                <a href="utilities/refresh_payment_outstanding_loading/refresh_payment_outstanding_loading.html">Refresh Payment Outstanding (Loading)</a>
                <a href="utilities/change_fin_yr_div/change_fin_yr_div.html">Change Fin. Yr (Div)</a>
                <div class="erp-submenu" data-section="USER SECURITY">
                    <a href="#" data-toggle="submenu">User Security ></a>
                    <div class="submenu-menu">
                        <a href="utilities/user_security/role_master/role_master.html">Role Master</a>
                        <a href="utilities/user_security/user_log/user_log.html">User Log</a>
                    </div>
                </div>
                <a href="utilities/company_details/company_details.html">Company Details</a>
                <a href="utilities/merge_account/merge_account.html">Merge Account</a>
                <a href="utilities/merge_city/merge_city.html">Merge City</a>
                <a href="utilities/merge_delete_data/merge_delete_data.html">Merge & Delete Data</a>
                <a href="utilities/find_tb_difference/find_tb_difference.html">Find T.B. Difference</a>
                <a href="utilities/re_numbering/re_numbering.html">Re-Numbering</a>
                <a href="utilities/missing_voucher_sr_no/missing_voucher_sr_no.html">Missing Voucher Sr No</a>
                <a href="utilities/rtgs_form_setup/rtgs_form_setup.html">RTGS Form Setup</a>
                <a href="utilities/unit_setup/unit_setup.html">Unit Setup</a>
                <a href="utilities/confirm/confirm.html">Confirm</a>
                <a href="utilities/bf_bal_from_prv_year/bf_bal_from_prv_year.html">B/F Bal from Prv Year</a>
                <a href="utilities/calculator/calculator.html">Calculator</a>
            </div>
        </div>
    </div>

    <a href="index.html" class="logo" style="text-decoration:none;color:inherit;">SoftBrokerage</a>

    <div style="margin-left:auto;display:flex;align-items:center;">
        <a href="auth/logout.php"
           class="nav-logout-btn"
           data-logout-btn="1"
           title="Logout"
           aria-label="Logout"
           style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:rgba(0,0,0,0.06);color:#111;text-decoration:none;transition:.2s;">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</nav>

<div class="erp-quick-bar global-erp-quick">
    <a href="sales.html" data-section="TRANSACTIONS">Daily Sauda</a>
    <a href="transactions/loading_entry/loading_entry.html" data-section="TRANSACTIONS">Loading Entry</a>
    <a href="sales_registry.html" data-section="TRANSACTIONS">Sauda Register</a>
    <a href="loading_register.html" data-section="TRANSACTIONS">Loading Register</a>
    <a href="company.html" data-section="MASTERS">Shift Division</a>
    <a href="index.html">Exit</a>
    <a href="auth/logout.php">Chng Password</a>
    <a href="search.html">Search</a>
</div>`;
    }

    function closeAll(navRoot) {
        navRoot.querySelectorAll('.dropdown').forEach(d => d.classList.remove('open'));
        navRoot.querySelectorAll('.erp-submenu').forEach(d => d.classList.remove('open'));
        navRoot.querySelectorAll('.erp-submenu .submenu-menu').forEach(m => m.style.top = '');
    }

    function closeOpenSubmenuOnly(navRoot) {
        const openSub = navRoot.querySelector('.erp-submenu.open');
        if (!openSub) return false;
        openSub.classList.remove('open');
        const menu = openSub.querySelector(':scope > .submenu-menu');
        if (menu) menu.style.top = '';
        return true;
    }

    function adjustMiscMastersPosition(parent) {
        if (!parent || !parent.classList.contains('misc-masters')) return;

        const menu = parent.querySelector(':scope > .submenu-menu');
        if (!menu) return;

        menu.style.top = '';
        const rect = menu.getBoundingClientRect();
        const vpH = window.innerHeight;

        let delta = 0;
        if (rect.bottom > vpH - 8) delta = rect.bottom - (vpH - 8);
        if (rect.top - delta < 8) delta = rect.top - 8;
        if (delta > 0) menu.style.top = `${-delta}px`;
    }

    function wireNavHandlers(navRoot) {
        navRoot.querySelectorAll('[data-toggle="dropdown"]').forEach(el => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = this.closest('.dropdown');
                navRoot.querySelectorAll('.dropdown').forEach(d => {
                    if (d !== parent) d.classList.remove('open');
                });
                parent.classList.toggle('open');
            });
        });

        navRoot.querySelectorAll('.erp-submenu').forEach(menu => {
            menu.addEventListener('mouseenter', function () {
                const scope = this.parentElement;
                scope.querySelectorAll('.erp-submenu').forEach(s => {
                    if (s !== this) s.classList.remove('open');
                });
                this.classList.add('open');
                adjustMiscMastersPosition(this);
            });

            menu.addEventListener('mouseleave', function () {
                this.classList.remove('open');
            });
        });

        navRoot.querySelectorAll('[data-toggle="submenu"]').forEach(el => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = this.closest('.erp-submenu');
                if (!parent) return;

                const scope = parent.parentElement;
                scope.querySelectorAll('.erp-submenu').forEach(s => {
                    if (s !== parent) s.classList.remove('open');
                });

                parent.classList.toggle('open');
                adjustMiscMastersPosition(parent);
            });
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.global-erp-nav')) {
                closeAll(navRoot);
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            if (closeOpenSubmenuOnly(navRoot)) {
                // Close only the submenu on Escape and keep main menu open.
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }
            closeAll(navRoot);
            e.preventDefault();
            e.stopImmediatePropagation();
        });
    }

    function ensureFontAwesome() {
        if (document.querySelector('link[data-fa-global="1"]')) return;
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css';
        link.setAttribute('data-fa-global', '1');
        document.head.appendChild(link);
    }

    function ensureGlobalStyle() {
        if (document.querySelector('link[data-global-style="1"]')) return;
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = resolveAppHref('global-style.css?v=2');
        link.setAttribute('data-global-style', '1');
        document.head.appendChild(link);
    }

    function ensureGlobalKeyboard() {
        if (document.querySelector('script[data-global-keyboard="1"]')) return;
        const script = document.createElement('script');
        script.src = resolveAppHref('global-keyboard.js?v=3');
        script.defer = true;
        script.setAttribute('data-global-keyboard', '1');
        document.head.appendChild(script);
    }

    function ensurePwaInstall() {
        if (!document.querySelector('link[rel="manifest"]')) {
            const link = document.createElement('link');
            link.rel = 'manifest';
            link.href = resolveAppHref('manifest.json');
            document.head.appendChild(link);
        }

        if (!document.querySelector('meta[name="theme-color"]')) {
            const meta = document.createElement('meta');
            meta.name = 'theme-color';
            meta.content = '#111111';
            document.head.appendChild(meta);
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register(resolveAppHref('sw.js')).catch(function () { });
            });
        }
    }

    function ensureEditMode() {
        if (document.querySelector('script[data-edit-mode="1"]')) return;
        const script = document.createElement('script');
        script.src = resolveAppHref('edit-mode.js?v=1');
        script.defer = true;
        script.setAttribute('data-edit-mode', '1');
        document.head.appendChild(script);
    }

    function ensureDeleteMode() {
        const existing = document.querySelector('script[data-delete-mode="1"]');
        if (existing) {
            if (window.confirmDelete) return Promise.resolve();
            return new Promise(function (resolve) {
                existing.addEventListener('load', resolve, { once: true });
                existing.addEventListener('error', resolve, { once: true });
                setTimeout(resolve, 1000);
            });
        }
        return new Promise(function (resolve) {
            const script = document.createElement('script');
            script.src = resolveAppHref('delete-mode.js?v=1');
            script.defer = true;
            script.setAttribute('data-delete-mode', '1');
            script.onload = resolve;
            script.onerror = resolve;
            document.head.appendChild(script);
        });
    }

    document.addEventListener('DOMContentLoaded', async function () {
        const permissionPayload = await enforceGlobalAccess();
        if (!permissionPayload) return;

        ensureFontAwesome();
        ensureGlobalStyle();
        ensureGlobalKeyboard();
        ensurePwaInstall();
        ensureEditMode();
        const deleteModeReady = ensureDeleteMode();

        if (document.querySelector('.global-erp-nav')) return;

        const wrapper = document.createElement('div');
        wrapper.innerHTML = buildNav();

        const nav = wrapper.firstElementChild;
        const quick = nav ? nav.nextElementSibling : null;

        if (nav) document.body.insertBefore(nav, document.body.firstChild);
        if (quick) document.body.insertBefore(quick, nav.nextSibling);

        [nav, quick].forEach(function (root) {
            if (!root) return;
            root.querySelectorAll('a[href]').forEach(function (a) {
                a.setAttribute("href", resolveAppHref(a.getAttribute("href")));
            });
        });

        wireNavHandlers(document.body);
        const logoutBtn = document.querySelector('.nav-logout-btn[data-logout-btn="1"]');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async function (e) {
                e.preventDefault();
                if (deleteModeReady) await deleteModeReady;
                const confirmFn = window.confirmDelete || window.confirmAdd;
                if (document.body) document.body.dataset.suppressDeleteLabel = "1";
                const ok = confirmFn ? await confirmFn("Are you sure you want to logout?") : true;
                if (document.body) delete document.body.dataset.suppressDeleteLabel;
                if (ok) window.location.href = logoutBtn.getAttribute('href');
            });
        }
        applyUserPermissions(permissionPayload);
    });

})();

document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        if (document.querySelector(".global-erp-nav .erp-submenu.open")) {
            return;
        }
        if (document.body && (document.body.dataset.kbNavbarActive === "1" || document.body.dataset.kbContentActive === "1")) {
            return;
        }
        const rightPanel = document.querySelector(".right-panel");
        if (rightPanel && rightPanel.classList.contains("display-mode")) {
            return;
        }
        const current = (location.pathname.split('/').pop() || '').toLowerCase();
        if (current === "index.html") return;

        const ref = (document.referrer || "").toLowerCase();
        const isAppRef = ref.includes("/brokerapp/");

        if (isAppRef && window.history.length > 1) {
            window.history.back();
        }
    }
});

function applyUserPermissions(permissionPayload) {
    if (Number(permissionPayload?.is_admin || 0) === 1) {
        document.querySelectorAll("[data-section], .global-erp-nav a[href]:not(.nav-logout-btn), .global-erp-quick a[href], .erp-submenu").forEach(function (el) {
            el.style.display = "";
        });
        return;
    }

    const allowed = Array.isArray(permissionPayload?.sections) ? permissionPayload.sections : [];
    const allowedPages = new Set(Array.isArray(permissionPayload?.pages) ? permissionPayload.pages : []);
    const mastersFamily = ["MASTERS", "ACCOUNT GROUP", "MISC. MASTERS", "MASTER DATA"];
    const canSeeMastersMenu = mastersFamily.some(s => allowed.includes(s));

    document.querySelectorAll("[data-section]").forEach(el => {
        const section = el.getAttribute("data-section");
        if (section === "MASTERS") {
            el.style.display = canSeeMastersMenu ? "" : "none";
            return;
        }

        if (!allowed.includes(section)) {
            el.style.display = "none";
        }
    });

    // Page-level visibility: hide unchecked page links from nav + quick bar.
    document.querySelectorAll(".global-erp-nav a[href]:not(.nav-logout-btn), .global-erp-quick a[href]").forEach(el => {
        const href = el.getAttribute("href") || "";
        if (!href || href === "#" || href.startsWith("javascript:")) return;

        const lower = href.toLowerCase();
        if (lower.endsWith(".php")) return;

        if (lower.endsWith(".html")) {
            const key = normalizePageKey(href);
            // Always keep index visible as safe landing/exit page
            if (key === "INDEX_HTML") return;
            if (!allowedPages.has(key)) {
                el.style.display = "none";
            }
        }
    });

    // Hide submenu wrappers that no longer have visible links.
    document.querySelectorAll(".erp-submenu").forEach(wrapper => {
        const visibleLinks = wrapper.querySelectorAll(".submenu-menu a:not([style*='display: none'])");
        if (!visibleLinks.length) {
            wrapper.style.display = "none";
        }
    });
}
