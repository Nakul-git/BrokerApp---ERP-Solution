# Migration Tracker

Last updated: 2026-04-09

## Goal

Rebuild BrokerApp so it follows the original SoftBrokerage workflow and data model, while staying in the current web architecture.

## Current Baseline

- Master modules: many are already present in BrokerApp style
- Transaction modules: only partially implemented
- Third transaction database bootstrap: initial version added in [`api/transaction_bootstrap.php`](C:/xampp/htdocs/BrokerApp/api/transaction_bootstrap.php)

## Priority Order

1. Expand transaction DB bootstrap and schema as source-driven modules demand
2. Rebuild Daily Sauda from original source
3. Rebuild Loading Entry from original source
4. Rebuild Sauda Register
5. Rebuild Loading Register
6. Rebuild Payment Entry
7. Expand reporting / printing / posting behavior

## Daily Sauda Breakdown

- [x] Define transaction DB connection/bootstrap
- [x] Add initial web schema for `etrans1`
- [x] Add initial web schema for `etrans2`
- [x] Add first-pass supporting tables
- [ ] Build Daily Sauda API set
- [ ] Rebuild Daily Sauda frontend to match original workflow
- [ ] Add modify/delete/load cycle
- [ ] Add print/register linkage
- [ ] Add original-style validations

## Notes

- Keep updating this file as each migration step is completed.
- Update [`original_softbrokerage_notes.md`](C:/xampp/htdocs/BrokerApp/docs/original_softbrokerage_notes.md) whenever a new screen or posting rule is decoded.
