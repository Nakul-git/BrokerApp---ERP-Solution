# Original SoftBrokerage Notes

Last updated: 2026-04-09

This file stores what has been learned from the decoded desktop software so later work does not depend on memory.

Source root:

- [`C:\ERP_Analyzer\decompiled\SoftBrokerage decompiled`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled)

## Overall Architecture

The original software is a decompiled .NET WinForms MDI ERP application.

Core shape:

- `Program.cs` starts the app
- `mdiform1.cs` is the MDI shell
- `frmlogin.cs` handles login
- `Common.cs` is the main global utility/business/data helper
- many `frm*.cs` files are module screens
- many `upd_*.cs` files are posting/update helpers
- reporting is heavily used

Important architectural reality:

- it is not cleanly layered
- UI, business logic, SQL, reporting, and global state are tightly mixed
- forms directly query and save data

## Daily Sauda

Main source files:

- [`frmsauda.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsauda.cs)
- [`frmsauda.Designer.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsauda.Designer.cs)

### Form identity

- `base.Tag == "SD"` means `Daily Sauda`
- same form class also appears to support related transaction modes like sales return via different tags

### Core UI shape

The original `Daily Sauda` screen is not built like the current BrokerApp master pages.

Main characteristics:

- large white workspace inside red outer border
- centered green title `Daily Sauda`
- pink `Display Mode` block top-right
- top menu and quick action strip
- fields for:
  - entry date
  - division
  - ref no / book / character / voucher no
  - loading required
  - buyer
  - seller
  - sub broker
- central cyan/orange item grid
- small right-side list showing recent date/book/no rows
- totals and remarks near the bottom
- function-key buttons like `F2 Add`, `F3 Modify`, `F5 Delete`, `F7 Print`, `Esc Exit`

### Validation behavior seen in code

`valid()` in [`frmsauda.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsauda.cs) checks at least:

- entry no must be non-zero
- at least one product row must exist
- division must be valid
- duplicate voucher checks in `etrans1`
- voucher/book rules from `vouctyp_mast`
- buyer must be valid
- seller must be valid
- buyer and seller must be different
- date must fall inside financial year
- book must be valid for current division
- voucher number must be within allowed range
- product rows must have valid product and amount
- linked loading date and vehicle-required constraints

### Transaction storage behavior

Daily Sauda in the original software is not a simple single-header invoice.

Observed model:

- header saved in `etrans1`
- detail rows saved in `etrans2`
- supporting postings also involve other transaction tables/helpers

Important header concepts:

- `main_bk = 'SD'`
- `c_j_s_p` is book code
- `vouc_code` is voucher number
- `vouc_chr` is voucher character
- buyer and seller are separate fields
- sub-broker and loading flags are stored on header/detail paths

Important detail concepts:

- paired buyer-side and seller-side rows are created
- buyer-side details use `main_bk = 'SDBYR'`
- seller-side details use `main_bk = 'SDSLR'`
- rows are linked through opposite-code style references

### Migration implication

BrokerApp should not model `Daily Sauda` as only:

- one `sales_master` row
- many `sales_items` rows

That simplified model is not enough for faithful migration.

The correct web migration should use a transaction schema closer to:

- `etrans1`
- `etrans2`
- later related posting/outstanding tables as needed

## Migration Principle

For transaction modules, use this rule:

- derive workflow and data shape from decoded source first
- then adapt it into BrokerApp’s web stack
- do not approximate important transaction logic from screenshots alone
