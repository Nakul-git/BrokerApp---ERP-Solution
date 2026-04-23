
/* ==========================
   GLOBAL AUTH PROTECTION
========================== */

(async function(){

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
    const appRoot = getAppRoot();

    // pages allowed without login
    const publicPages = ["login.html","register.html"];

    const current = location.pathname.split("/").pop();

    // skip login/register pages
    if(publicPages.includes(current)) return;

    try{

        const res = await fetch(appRoot + "api/check_auth.php");
        const data = await res.json();

        if(!data.logged_in){
            // not logged in → go login
            window.location.href = appRoot + "login.html";
        }

    }catch(e){
        window.location.href = appRoot + "login.html";
    }

})();





// Toggle dropdown menus
function toggleDropdown(el){
    document.querySelectorAll('.dropdown')
    .forEach(d=>d!==el.parentElement && d.classList.remove('open'));
    el.parentElement.classList.toggle('open');
}

// Load HTML view dynamically
function loadFeature(type){
    const c = document.getElementById("content");

    fetch(`views/${type}.html`)
        .then(res => {
            if (!res.ok) throw new Error("View not found");
            return res.text();
        })
        .then(html => {
            c.innerHTML = html;

            // Close dropdowns
            document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('open'));

            // Load states ONLY for State Master
            if (type === "state"|| type === "city") {
                setTimeout(loadStates, 0);
            }
        })
        .catch(err => {
            c.innerHTML = `<div class="feature-box"><p>Error loading ${type}</p></div>`;
            console.error(err);
        });
}



// Load view from URL params if any
const params = new URLSearchParams(window.location.search);
const view = params.get("view");
if (view) {
    loadFeature(view);
}

