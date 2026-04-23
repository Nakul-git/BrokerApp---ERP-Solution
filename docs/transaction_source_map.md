# Transaction Source Map

Last updated: 2026-04-09

This file maps BrokerApp transaction pages to the decoded original SoftBrokerage forms.

## Entry Screens

### Daily Sauda

BrokerApp page:

- [`sales.html`](C:/xampp/htdocs/BrokerApp/sales.html)

Primary original sources:

- [`frmsauda.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsauda.cs)
- [`frmsauda.Designer.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsauda.Designer.cs)
- [`frmsaudaformat2.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsaudaformat2.cs)
- [`frmsaudaformat2.Designer.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsaudaformat2.Designer.cs)

### Loading Entry

BrokerApp page:

- [`transactions/loading_entry/loading_entry.html`](C:/xampp/htdocs/BrokerApp/transactions/loading_entry/loading_entry.html)

Primary original sources:

- [`frmloading.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmloading.cs)
- [`frmloading.Designer.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmloading.Designer.cs)
- [`frmloadingformat2.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmloadingformat2.cs)
- [`frmloadingformat2.Designer.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmloadingformat2.Designer.cs)
- [`frmloadingformat3.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmloadingformat3.cs)
- [`frmloadingformat3.Designer.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmloadingformat3.Designer.cs)

### Payment Entry

BrokerApp page:

- [`transactions/payment_entry/payment_entry.html`](C:/xampp/htdocs/BrokerApp/transactions/payment_entry/payment_entry.html)

Primary original sources:

- [`frmpaymententry.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmpaymententry.cs)
- [`frmpaymententry.Designer.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmpaymententry.Designer.cs)

## Register Screens

### Sauda Register

BrokerApp page:

- [`sales_registry.html`](C:/xampp/htdocs/BrokerApp/sales_registry.html)

Primary original sources:

- [`frmsaudaregister.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsaudaregister.cs)
- [`frmsaudaregister.Designer.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsaudaregister.Designer.cs)
- [`frmsaudaregisterformat2.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsaudaregisterformat2.cs)
- [`frmsaudaregisterformat2.Designer.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsaudaregisterformat2.Designer.cs)

### Loading Register

BrokerApp page:

- [`loading_register.html`](C:/xampp/htdocs/BrokerApp/loading_register.html)

Primary original sources:

- [`frmloadingregisterformat2.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmloadingregisterformat2.cs)
- [`frmloadingregisterformat2.Designer.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmloadingregisterformat2.Designer.cs)

## Related / Supporting Forms

- [`frmbillgeneration.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmbillgeneration.cs)
- [`frmbillregister.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmbillregister.cs)
- [`frmsaudaanalysis.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsaudaanalysis.cs)
- [`frmsaudaledger.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmsaudaledger.cs)
- [`frmpaymentregisterformat2.cs`](C:/ERP_Analyzer/decompiled/SoftBrokerage%20decompiled/frmpaymentregisterformat2.cs)

## Build Rule

For any transaction screen:

1. inspect the original entry/register form source first
2. note the real fields, filters, grids, and action buttons in [`original_softbrokerage_notes.md`](C:/xampp/htdocs/BrokerApp/docs/original_softbrokerage_notes.md)
3. build the web page in BrokerApp’s shared navigation/permission system
4. keep the web UI aligned with existing BrokerApp standards, but keep the workflow and data model aligned with the original form
