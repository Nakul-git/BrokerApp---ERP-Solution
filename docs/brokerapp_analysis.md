# BrokerApp Analysis

Last updated: 2026-04-09

## Current App Shape

BrokerApp is a PHP + HTML + vanilla JavaScript ERP web app under `C:\xampp\htdocs\BrokerApp`.

Current high-level counts:

- `68` HTML files
- `175` PHP API files
- `10` JS files at repo/global level

Top-level app areas:

- `masters/`
- `transactions/`
- `screening/`
- `printing/`
- `bill_section/`
- `accounting/`
- `utilities/`
- `api/`

## Shared Foundation

### Auth and session

- Shared PHP bootstrap is [`api/session.php`](C:/xampp/htdocs/BrokerApp/api/session.php)
- It currently connects only:
  - `master` database
  - `company` database
- Default `$con` points to `master`
- There is no third transaction database bootstrap file in the current clean workspace

### Navigation and permissions

- Shared navigation is driven by [`global-nav.js`](C:/xampp/htdocs/BrokerApp/global-nav.js)
- It injects:
  - top ERP menu
  - quick-link row
  - section/page permission enforcement
  - company/division session checks
- Page permissions are route-name based through:
  - [`api/get_logged_user_permissions.php`](C:/xampp/htdocs/BrokerApp/api/get_logged_user_permissions.php)
  - [`api/get_logged_user_page_permissions.php`](C:/xampp/htdocs/BrokerApp/api/get_logged_user_page_permissions.php)

### Shared UI pattern

The strongest implemented house pattern is the master-screen layout used in pages like:

- [`masters/misc_master/state/state.html`](C:/xampp/htdocs/BrokerApp/masters/misc_master/state/state.html)
- [`masters/misc_master/division_master/division_master.html`](C:/xampp/htdocs/BrokerApp/masters/misc_master/division_master/division_master.html)

That pattern includes:

- injected ERP nav
- `content > feature-box`
- `master-layout`
- left selection list
- right detail/edit pane
- `Display Mode` / `Add Mode` / `Edit Mode`
- function-key style action rows
- `selection-mode.js`, `add-mode.js`, `edit-mode.js`, `delete-mode.js`

## Databases

### `master`

Defined in [`master.sql`](C:/xampp/htdocs/BrokerApp/master.sql).

This currently holds most operational master data, including examples like:

- `party`
- `product`
- `brand`
- `state`
- `city`
- `district`
- `area`
- `transport`
- `roles`
- `role_permissions`
- `users`
- party-related child tables
- account-related tables

### `company`

Defined in [`company.sql`](C:/xampp/htdocs/BrokerApp/company.sql).

This currently holds company/division/session-context style data, including:

- `company_master`
- `division_master`

### Transaction foundation

The old desktop software effectively uses a third transaction/posting layer.

BrokerApp now has an initial bootstrap at:

- [`api/transaction_bootstrap.php`](C:/xampp/htdocs/BrokerApp/api/transaction_bootstrap.php)

Current bootstrap responsibilities:

- create/connect transaction DB `brokerage_txn`
- derive financial year code
- expose transaction context from selected company/division
- initialize first-pass tables:
  - `voucher_series`
  - `etrans1`
  - `etrans2`
  - `trans`
  - `outstanding`
  - `user_log`

This is the first structural bridge toward faithful transaction migration.

## Module Status

### Stronger areas

These look substantially implemented in BrokerApp style:

- most misc masters
- party master
- product master
- brand master
- division master
- role/user security plumbing

### Weaker / placeholder areas

These are currently not built in old-ERP style:

- [`sales.html`](C:/xampp/htdocs/BrokerApp/sales.html)
  - still a simplified sales-entry style page
  - saves to `sales_master` / `sales_items`
  - not true old `Daily Sauda`
- [`transactions/loading_entry/loading_entry.html`](C:/xampp/htdocs/BrokerApp/transactions/loading_entry/loading_entry.html)
  - currently placeholder/info page
- [`transactions/payment_entry/payment_entry.html`](C:/xampp/htdocs/BrokerApp/transactions/payment_entry/payment_entry.html)
  - currently placeholder/info page
- [`sales_registry.html`](C:/xampp/htdocs/BrokerApp/sales_registry.html)
  - simple registry viewer, not old register workflow

## Current Transaction Approach

Current `sales` module APIs under [`api/sales`](C:/xampp/htdocs/BrokerApp/api/sales) use a simplified model:

- `sales_master`
- `sales_items`

This is not equivalent to the old SoftBrokerage transaction architecture.

## Build Direction

To make BrokerApp faithful to the original software, the next architecture step should be:

1. expand the third transaction database bootstrap where original modules need more fields
2. port transaction screens screen-by-screen starting with:
   - Daily Sauda
   - Loading Entry
   - Sauda Register
   - Loading Register
   - Payment Entry

## Practical Rule For Future Work

When adding or rebuilding modules:

- keep BrokerApp navigation and permission model
- keep BrokerApp page routing and folder structure
- keep BrokerApp visual house style unless the original transaction screen needs a closer one-off form layout
- do not build important transaction modules on top of the simplified `sales_master` model if the original screen uses `etrans1` / `etrans2` style logic
