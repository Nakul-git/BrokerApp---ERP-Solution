(function () {
    const page = (location.pathname.split("/").pop() || "").toLowerCase();
    if (!page || page === "login.html" || page === "register.html") return;

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

    async function fetchPagePermissions() {
        const section = (document.body.dataset.requiredSection || "").toUpperCase();
        const query = new URLSearchParams({ page, section }).toString();
        const res = await fetch(resolveAppHref("api/get_logged_user_page_permissions.php?" + query), { cache: "no-store" });
        return res.json();
    }

    function cleanLabelText(text) {
        return String(text || "")
            .replace(/[\u{1F300}-\u{1FAFF}]/gu, "")
            .replace(/\s+/g, " ")
            .trim()
            .toUpperCase();
    }

    function getActionButtons(action) {
        const byId = {
            add: "addBtn",
            edit: "modifyBtn",
            delete: "deleteBtn",
            print: "printBtn",
            exit: "exitBtn",
            save: "saveBtn",
            cancel: "cancelBtn"
        };

        const textPattern = {
            add: /\bF2\s*-\s*ADD\b|\bF2ADD\b|\bADD\b/,
            edit: /\bF3\s*-\s*MODIFY\b|\bF3MODIFY\b|\bMODIFY\b/,
            delete: /\bF5\s*-\s*DELETE\b|\bF5DELETE\b|\bDELETE\b/,
            print: /\bF7\s*-\s*PRINT\b|\bF7PRINT\b|\bPRINT\b/,
            exit: /\bESC\s*-\s*EXIT\b|\bESCEXIT\b|\bEXIT\b/,
            save: /\bF6\s*-\s*SAVE\b|\bF6SAVE\b|\bSAVE\b/,
            cancel: /\bESC\s*-\s*CANCEL\b|\bESCCANCEL\b|\bCANCEL\b/
        };

        const matched = [];
        const seen = new Set();

        const explicitSelectors = [
            `#${byId[action]}`,
            `[data-action="${action}"]`
        ];

        explicitSelectors.forEach(function (selector) {
            document.querySelectorAll(selector).forEach(function (el) {
                if (!seen.has(el)) {
                    seen.add(el);
                    matched.push(el);
                }
            });
        });

        document.querySelectorAll("button, a.btn, input[type='button'], input[type='submit']").forEach(function (el) {
            if (seen.has(el)) return;
            const label = cleanLabelText(el.textContent || el.value || "");
            if (!label) return;
            if (textPattern[action].test(label)) {
                seen.add(el);
                matched.push(el);
            }
        });

        return matched;
    }

    function disableButtons(action, allowed) {
        getActionButtons(action).forEach(function (el) {
            if ("disabled" in el) {
                el.disabled = !allowed;
            }
            el.setAttribute("aria-disabled", allowed ? "false" : "true");
        });
    }

    function toggleButtons(action, visible) {
        getActionButtons(action).forEach(function (el) {
            el.classList.toggle("permission-hidden", !visible);
            if (!visible) {
                el.style.setProperty("display", "none", "important");
            } else {
                el.style.removeProperty("display");
            }
        });
    }

    function applyButtonPermissions(p) {
        const canSave = p.a === 1 || p.e === 1;

        toggleButtons("add", p.a === 1);
        toggleButtons("edit", p.e === 1);
        toggleButtons("delete", p.d === 1);
        toggleButtons("print", p.p === 1);
        toggleButtons("save", canSave);
        toggleButtons("cancel", canSave);
        toggleButtons("exit", p.v === 1);

        disableButtons("add", p.a === 1);
        disableButtons("edit", p.e === 1);
        disableButtons("delete", p.d === 1);
        disableButtons("print", p.p === 1);
        disableButtons("save", canSave);
        disableButtons("cancel", canSave);
        disableButtons("exit", p.v === 1);
    }

    function blockForbiddenShortcuts(p) {
        const map = {
            F2: "a",
            F3: "e",
            F5: "d",
            F7: "p"
        };

        document.addEventListener("keydown", function (e) {
            const need = map[e.key];
            if (need && p[need] !== 1) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }

            if (e.key === "F6" && !(p.a === 1 || p.e === 1)) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        }, true);
    }

    function isElementVisible(el) {
        if (!el) return false;
        if (!el.isConnected) return false;
        if (el.classList && el.classList.contains("permission-hidden")) return false;
        const style = window.getComputedStyle(el);
        if (style.display === "none" || style.visibility === "hidden") return false;
        if (style.opacity === "0") return false;
        if (el.getClientRects().length === 0) return false;
        return true;
    }

    function isElementUsable(el) {
        if (!isElementVisible(el)) return false;
        if ("disabled" in el && el.disabled) return false;
        if (el.getAttribute("aria-disabled") === "true") return false;
        if (el.dataset && el.dataset.saveInFlight === "1") return false;
        return true;
    }

    const kbNavState = {
        navbarActive: false,
        contentActive: false,
        activeMenuIndex: null,
        activeContentIndex: 0,
        activeContentEl: null
    };
    window.__kbNavState = kbNavState;

    const kbKeyState = {
        ctrl: false,
        alt: false,
        altGraph: false,
        lastToggleAt: 0
    };

    function isModalOpen() {
        return !!document.querySelector(".simple-modal, .add-mode-modal, .edit-mode-modal");
    }

    function getNavRoot() {
        return document.querySelector(".global-erp-nav");
    }

    function closeAllNavMenus() {
        const navRoot = getNavRoot();
        if (!navRoot) return;
        navRoot.querySelectorAll(".dropdown.open").forEach(d => d.classList.remove("open"));
        navRoot.querySelectorAll(".erp-submenu.open").forEach(d => d.classList.remove("open"));
        navRoot.querySelectorAll(".erp-submenu .submenu-menu").forEach(m => m.style.top = "");
    }

    function getTopNavItems() {
        const navRoot = getNavRoot();
        if (!navRoot) return [];
        const top = navRoot.querySelector(".erp-top-links");
        if (!top) return [];
        const items = [];
        Array.from(top.children).forEach(child => {
            if (child.matches(".dropdown")) {
                const anchor = child.querySelector(":scope > a[data-toggle='dropdown']");
                if (anchor && isElementVisible(child) && isElementVisible(anchor)) {
                    items.push({ type: "dropdown", anchor, dropdown: child });
                }
            } else if (child.matches("a")) {
                if (isElementVisible(child)) {
                    items.push({ type: "link", anchor: child, dropdown: null });
                }
            }
        });
        return items;
    }

    function clearActiveNavItem() {
        const navRoot = getNavRoot();
        if (!navRoot) return;
        navRoot.querySelectorAll(".kb-nav-item-active").forEach(el => {
            el.classList.remove("kb-nav-item-active");
            el.removeAttribute("aria-current");
        });
    }

    function setActiveMenuIndex(index) {
        const items = getTopNavItems();
        if (!items.length) return;
        const normalized = ((index % items.length) + items.length) % items.length;
        kbNavState.activeMenuIndex = normalized;
        clearActiveNavItem();
        const target = items[normalized];
        target.anchor.classList.add("kb-nav-item-active");
        target.anchor.setAttribute("aria-current", "true");
    }

    function guessInitialMenuIndex() {
        const items = getTopNavItems();
        if (!items.length) return 0;
        const required = String(document.body.dataset.requiredSection || "").toUpperCase();
        if (required) {
            const found = items.findIndex(item => {
                const section = (item.dropdown && item.dropdown.dataset && item.dropdown.dataset.section) || item.anchor.dataset.section || "";
                return String(section).toUpperCase() === required || (required === "MASTER DATA" && String(section).toUpperCase() === "MASTERS");
            });
            if (found >= 0) return found;
        }
        const current = (location.pathname.split("/").pop() || "").toLowerCase();
        const foundByHref = items.findIndex(item => {
            const href = (item.anchor.getAttribute("href") || "").toLowerCase();
            return href && href.indexOf(current) !== -1;
        });
        return foundByHref >= 0 ? foundByHref : 0;
    }

    function setNavbarActive(active) {
        kbNavState.navbarActive = !!active;
        document.body.classList.toggle("kb-navbar-active", kbNavState.navbarActive);
        document.body.dataset.kbNavbarActive = kbNavState.navbarActive ? "1" : "0";
        if (kbNavState.navbarActive) {
            if (!getTopNavItems().length) return;
            if (kbNavState.activeMenuIndex == null) {
                kbNavState.activeMenuIndex = guessInitialMenuIndex();
            }
            setActiveMenuIndex(kbNavState.activeMenuIndex);
        }
    }

    function setContentActive(active) {
        kbNavState.contentActive = !!active;
        document.body.classList.toggle("kb-content-active", kbNavState.contentActive);
        document.body.dataset.kbContentActive = kbNavState.contentActive ? "1" : "0";
        if (!kbNavState.contentActive) {
            if (kbNavState.activeContentEl) {
                kbNavState.activeContentEl.classList.remove("kb-content-item-active");
                kbNavState.activeContentEl = null;
            }
        }
    }

    function activateNavbar() {
        setContentActive(false);
        setNavbarActive(true);
    }

    function deactivateNavbar() {
        setNavbarActive(false);
        clearActiveNavItem();
    }

    function activateContent() {
        setNavbarActive(false);
        setContentActive(true);
        setActiveContentIndex(kbNavState.activeContentIndex || 0);
    }

    function deactivateContent() {
        setContentActive(false);
    }

    function getOpenDropdown() {
        return document.querySelector(".global-erp-nav .dropdown.open");
    }

    let lastHoverMenuItem = null;

    function isMenuItem(el) {
        if (!el) return false;
        return !!el.closest(".global-erp-nav .dropdown-menu, .global-erp-nav .submenu-menu, .global-erp-nav .erp-submenu");
    }

    function closeOpenSubmenuOnly() {
        const openDropdown = getOpenDropdown();
        if (!openDropdown) return false;
        const openSub = openDropdown.querySelector(".erp-submenu.open");
        if (!openSub) return false;
        openSub.classList.remove("open");
        const toggle = openSub.querySelector(":scope > a");
        if (toggle) {
            const items = getContentItems();
            const idx = items.indexOf(toggle);
            if (idx >= 0) {
                setActiveContentIndex(idx);
            }
        }
        return true;
    }

    function getContentItems() {
        const openDropdown = getOpenDropdown();
        if (openDropdown) {
            const menu = openDropdown.querySelector(".dropdown-menu");
            if (!menu) return [];
            return Array.from(menu.querySelectorAll("a")).filter(isElementVisible);
        }

        if (!(document.body && document.body.dataset && document.body.dataset.selectionMode === "1")) {
            const leftRows = Array.from(document.querySelectorAll(".left-panel .list-wrap [data-id]")).filter(isElementVisible);
            if (leftRows.length) return leftRows;
        }

        const tableRows = Array.from(document.querySelectorAll(".right-panel table tbody tr, main table tbody tr, table tbody tr")).filter(row => {
            if (!isElementVisible(row)) return false;
            return (row.textContent || "").trim().length > 0;
        });
        if (tableRows.length) return tableRows;

        const focusSelectors = "input, select, textarea, button, a, [tabindex]";
        const focusable = Array.from(document.querySelectorAll(".right-panel " + focusSelectors + ", main " + focusSelectors)).filter(el => {
            if (!isElementVisible(el)) return false;
            if (el.closest(".global-erp-nav, .global-erp-quick")) return false;
            if ("disabled" in el && el.disabled) return false;
            return true;
        });
        if (focusable.length) return focusable;

        return Array.from(document.querySelectorAll("body " + focusSelectors)).filter(el => {
            if (!isElementVisible(el)) return false;
            if (el.closest(".global-erp-nav, .global-erp-quick")) return false;
            if ("disabled" in el && el.disabled) return false;
            return true;
        });
    }

    function setActiveContentIndex(index) {
        const items = getContentItems();
        if (!items.length) return;
        const normalized = ((index % items.length) + items.length) % items.length;
        kbNavState.activeContentIndex = normalized;

        const target = items[normalized];
        if (kbNavState.activeContentEl && kbNavState.activeContentEl !== target) {
            kbNavState.activeContentEl.classList.remove("kb-content-item-active");
        }
        kbNavState.activeContentEl = target;
        target.classList.add("kb-content-item-active");

        if (typeof target.focus === "function" && target.tabIndex >= 0) {
            target.focus({ preventScroll: true });
        }
        if (typeof target.scrollIntoView === "function") {
            target.scrollIntoView({ block: "nearest" });
        }
    }

    function isSamePageHref(href) {
        const resolved = resolveAppHref(href).split("#")[0].split("?")[0].toLowerCase();
        const current = location.pathname.toLowerCase();
        return resolved.endsWith(current);
    }

    function activateContentItem(item) {
        if (!item) return false;
        const submenuToggle = item.closest(".erp-submenu") && item.matches(".erp-submenu > a");
        if (submenuToggle) {
            const submenu = item.closest(".erp-submenu");
            if (submenu) {
                const scope = submenu.parentElement;
                if (scope) {
                    scope.querySelectorAll(".erp-submenu").forEach(s => {
                        if (s !== submenu) s.classList.remove("open");
                    });
                }
                submenu.classList.toggle("open");
                if (submenu.classList.contains("open")) {
                    const submenuItems = Array.from(submenu.querySelectorAll(".submenu-menu a")).filter(isElementVisible);
                    if (submenuItems.length) {
                        const allItems = getContentItems();
                        const firstIndex = allItems.indexOf(submenuItems[0]);
                        if (firstIndex >= 0) {
                            setActiveContentIndex(firstIndex);
                        }
                    }
                }
            }
            return true;
        }

        if (item.tagName === "A") {
            const href = item.getAttribute("href") || "";
            if (href && !href.startsWith("#")) {
                if (isSamePageHref(href)) {
                    closeAllNavMenus();
                    activateContent();
                    return true;
                }
                item.click();
                return true;
            }
        }

        if (item.matches("input, select, textarea")) {
            item.focus();
            return true;
        }

        if (typeof item.click === "function") {
            item.click();
            return true;
        }

        try {
            item.dispatchEvent(new MouseEvent("click", { bubbles: true }));
            return true;
        } catch (e) {
            return false;
        }
    }

    function handleNavKeyboard(e) {
        if (e.defaultPrevented) return;
        if (isModalOpen()) return;

        if (e.key === "Control") kbKeyState.ctrl = true;
        if (e.key === "Alt") kbKeyState.alt = true;
        if (e.key === "AltGraph") kbKeyState.altGraph = true;

        const ctrlAltDown = kbKeyState.ctrl && (kbKeyState.alt || kbKeyState.altGraph);
        const isToggle = !e.repeat && !e.metaKey && (e.key === "Control" || e.key === "Alt" || e.key === "AltGraph") && ctrlAltDown;
        if (isToggle) {
            const now = Date.now();
            if (now - kbKeyState.lastToggleAt < 300) return;
            kbKeyState.lastToggleAt = now;
            if (kbNavState.contentActive) {
                closeAllNavMenus();
                activateNavbar();
            } else if (kbNavState.navbarActive) {
                deactivateNavbar();
            } else {
                activateNavbar();
            }
            e.preventDefault();
            e.stopImmediatePropagation();
            return;
        }

        if (!kbNavState.navbarActive && !kbNavState.contentActive) return;

        if (kbNavState.navbarActive) {
            if (e.key === "ArrowLeft" || e.key === "ArrowRight") {
                const delta = e.key === "ArrowRight" ? 1 : -1;
                setActiveMenuIndex((kbNavState.activeMenuIndex || 0) + delta);
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }
            if (e.key === "Enter") {
                const items = getTopNavItems();
                const current = items[kbNavState.activeMenuIndex || 0];
                if (current) {
                    if (current.type === "dropdown" && current.dropdown) {
                        closeAllNavMenus();
                        current.dropdown.classList.add("open");
                        activateContent();
                    } else if (current.anchor) {
                        const href = current.anchor.getAttribute("href") || "";
                        if (href && isSamePageHref(href)) {
                            activateContent();
                        } else {
                            current.anchor.click();
                        }
                    }
                }
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }
            if (e.key === "Escape") {
                deactivateNavbar();
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }
            return;
        }

        if (kbNavState.contentActive) {
            if (e.key === "ArrowUp" || e.key === "ArrowDown") {
                const delta = e.key === "ArrowDown" ? 1 : -1;
                setActiveContentIndex((kbNavState.activeContentIndex || 0) + delta);
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }
            if (e.key === "Enter") {
                const items = getContentItems();
                const target = items[kbNavState.activeContentIndex || 0];
                if (activateContentItem(target)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return;
                }
            }
            if (e.key === "Escape") {
                if (closeOpenSubmenuOnly()) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return;
                }
                closeAllNavMenus();
                activateNavbar();
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        }
    }

    document.addEventListener("keydown", handleNavKeyboard, true);
    document.addEventListener("keyup", function (e) {
        if (e.key === "Control") kbKeyState.ctrl = false;
        if (e.key === "Alt") kbKeyState.alt = false;
        if (e.key === "AltGraph") kbKeyState.altGraph = false;
    }, true);

    document.addEventListener("mouseover", function (e) {
        const target = e.target.closest(".global-erp-nav .dropdown-menu a, .global-erp-nav .erp-submenu > a, .global-erp-nav .submenu-menu a");
        if (target) {
            lastHoverMenuItem = target;
        }
    }, true);

    document.addEventListener("focusin", function (e) {
        const target = e.target.closest(".global-erp-nav .dropdown-menu a, .global-erp-nav .erp-submenu > a, .global-erp-nav .submenu-menu a");
        if (target) {
            lastHoverMenuItem = target;
        }
    }, true);

    document.addEventListener("keydown", function (e) {
        if (e.defaultPrevented) return;
        if (e.key !== "Enter") return;
        if (kbNavState.navbarActive || kbNavState.contentActive) return;
        if (isModalOpen()) return;

        const openDropdown = getOpenDropdown();
        if (!openDropdown) return;

        let target = null;
        const active = document.activeElement;
        if (active && isMenuItem(active)) {
            target = active;
        } else if (lastHoverMenuItem && lastHoverMenuItem.isConnected) {
            target = lastHoverMenuItem;
        }

        if (target && activateContentItem(target)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    }, true);

    function triggerAction(action) {
        const buttons = getActionButtons(action);
        const target = buttons.find(isElementUsable);
        if (!target) return false;
        if (target.dataset && target.dataset.globalHandled === "1") return false;
        if (target.dataset) target.dataset.globalHandled = "1";
        target.click();
        setTimeout(function () {
            if (target.dataset) delete target.dataset.globalHandled;
        }, 0);
        return true;
    }

    let lastShortcutKey = null;
    let lastShortcutAt = 0;

    function handleGlobalShortcuts(e) {
        if (e.defaultPrevented) return;
        if (kbNavState.navbarActive || kbNavState.contentActive) return;
        if (e.ctrlKey || e.altKey || e.metaKey) return;
        if (e.repeat) return;

        const now = Date.now();
        if (e.key === lastShortcutKey && now - lastShortcutAt < 200) {
            return;
        }

        const keyMap = {
            F2: "add",
            F3: "edit",
            F5: "delete",
            F6: "save",
            F7: "print"
        };

        const action = keyMap[e.key];
        if (action) {
            if (triggerAction(action)) {
                lastShortcutKey = e.key;
                lastShortcutAt = now;
                e.preventDefault();
                e.stopImmediatePropagation();
            }
            return;
        }

        if (e.key === "Escape") {
            const rightPanel = document.querySelector(".right-panel");
            const inEditMode = rightPanel
                ? rightPanel.classList.contains("add-mode") || rightPanel.classList.contains("modify-mode")
                : false;

            if (inEditMode) {
                if (triggerAction("cancel")) {
                    lastShortcutKey = e.key;
                    lastShortcutAt = now;
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return;
                }
                // In edit mode, never allow Escape to trigger exit navigation.
                lastShortcutKey = e.key;
                lastShortcutAt = now;
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }

            const addMode = document.getElementById("addModeButtons");
            if (isElementVisible(addMode)) {
                if (triggerAction("cancel")) {
                    lastShortcutKey = e.key;
                    lastShortcutAt = now;
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return;
                }
            }

            const openDropdown = getOpenDropdown();
            if (openDropdown) {
                if (closeOpenSubmenuOnly()) {
                    lastShortcutKey = e.key;
                    lastShortcutAt = now;
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return;
                }
                closeAllNavMenus();
                activateNavbar();
                lastShortcutKey = e.key;
                lastShortcutAt = now;
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }

            const inDisplayMode = rightPanel ? rightPanel.classList.contains("display-mode") : false;
            if (inDisplayMode) {
                if (window.history.length > 1) {
                    window.history.back();
                }
                lastShortcutKey = e.key;
                lastShortcutAt = now;
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }

            if (triggerAction("cancel") || triggerAction("exit")) {
                lastShortcutKey = e.key;
                lastShortcutAt = now;
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        }
    }

    async function initPermissionGuard() {
        try {
            const data = await fetchPagePermissions();
            if (data.status !== "success") return;
            const p = data.permissions || { v: 0, a: 0, e: 0, d: 0, p: 0 };
            window.__pagePermissions = p;
            applyButtonPermissions(p);
            blockForbiddenShortcuts(p);

            const observer = new MutationObserver(function () {
                applyButtonPermissions(p);
            });
            observer.observe(document.body, { childList: true, subtree: true });
        } catch (e) {
            // ignore fetch failures to avoid blocking usage by mistake
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initPermissionGuard);
    } else {
        initPermissionGuard();
    }

    document.addEventListener("keydown", function (e) {
        if (e.defaultPrevented) return;
        if (e.key !== "Escape") return;
        if (e.ctrlKey || e.altKey || e.metaKey) return;
        if (document.querySelector(".simple-modal")) return;

        const rightPanel = document.querySelector(".right-panel");
        const inEditMode = rightPanel
            ? rightPanel.classList.contains("add-mode") || rightPanel.classList.contains("modify-mode")
            : false;

        if (!inEditMode) return;

        if (triggerAction("cancel")) {
            lastShortcutKey = e.key;
            lastShortcutAt = Date.now();
        }
        e.preventDefault();
        e.stopImmediatePropagation();
    }, true);

    document.addEventListener("keydown", handleGlobalShortcuts, true);
})();
