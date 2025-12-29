# REPO_DISCOVERY.md

## Perfex/CodeIgniter version + module conventions
- Perfex DB migration version is 340 (3.4.0) in `application/config/migration.php`.
- Modules live under `modules/<module>/` with a root bootstrap file named `<module>.php` containing the module header, constants, hook registrations, and activation/deactivation/uninstall hooks (examples: `modules/custom_links/custom_links.php`, `modules/project_roadmap/project_roadmap.php`).
- Common module layout mirrors `modules/custom_links/` and `modules/project_roadmap/`: `assets/`, `controllers/`, `models/`, `views/`, `helpers/`, `language/`, `migrations/`, plus `install.php`/`deactivate.php`/`uninstall.php` and an `index.html`.
- Module controllers extend `AdminController` and gate access via permissions (example: `modules/custom_links/controllers/Custom_links.php`).

## Hook points
### Project tabs injection
- Core registers default project tabs via `hooks()->add_action('admin_init', 'app_init_project_tabs')` in `application/helpers/core_hooks_helper.php`, implemented in `application/helpers/projects_helper.php`.
- Modules can add project tabs by hooking `admin_init` and calling `$CI->app_tabs->add_project_tab()` (see `application/libraries/App_tabs.php`; example usage in `modules/domain_manager/domain_manager.php`).

### Dashboard widgets
- Dashboard widgets are collected in `application/helpers/widgets_helper.php` and extended via `hooks()->apply_filters('get_dashboard_widgets', $widgets)`.
- Modules append widgets by adding the `get_dashboard_widgets` filter (examples: `modules/project_roadmap/project_roadmap.php`, `modules/goals/goals.php`).

### Estimate/Proposal accepted
- `hooks()->do_action('estimate_accepted', $id)` fires when a client accepts an estimate in `application/models/Estimates_model.php`.
- `hooks()->do_action('proposal_accepted', $id)` fires when a client accepts a proposal in `application/models/Proposals_model.php`.
- Related nearby hooks: `after_proposal_converted_to_invoice` in `application/controllers/admin/Proposals.php`, plus `after_estimate_updated` in `application/models/Estimates_model.php`.

## Core DB tables + fields (confirmed from repo)
### Projects
- Table: `tblprojects` (`db_prefix().'projects'`) created in `application/migrations/109_version_109.php`.
- Fields: id, name, description, status, clientid, billing_type, start_date, deadline, project_created, project_cost, project_rate_per_hour, addedfrom.

### Estimates
- Table: `tblestimates` created in `application/migrations/102_version_102.php`.
- Fields: id, sent, datesend, clientid, number, year, datecreated, date, expirydate, currency, subtotal, total, adjustment, addedfrom, status, clientnote, adminnote, discount_percent, discount_total, discount_type, invoiceid, invoiced_date, terms, reference_no.
- Additional: total_tax added in `application/migrations/109_version_109.php`.

### Proposals
- Table: `tblproposals` created in `application/migrations/107_version_107.php`.
- Fields: id, subject, content, addedfrom, datecreated, total, currency, open_till, date, rel_id, rel_type, assigned, hash, proposal_to, address, email, phone, allow_comments, status, estimate_id, invoice_id, date_converted.

### Invoices (base CREATE not present in migrations)
- Table: `tblinvoices` referenced throughout core. The initial CREATE TABLE statement is not in this repo, so the full schema cannot be confirmed from migrations alone.
- Fields confirmed by migrations: discount_percent, discount_type, discount_total, terms, sale_agent, billing_street, billing_city, billing_state, billing_zip, billing_country, shipping_street, shipping_city, shipping_state, shipping_zip, shipping_country, include_shipping, show_shipping_on_invoice, total_tax, show_quantity_as, project_id, prefix, number_format, recurring, recurring_type, custom_recurring, last_recurring_date, allowed_payment_modes, token, is_recurring_from, cycles, total_cycles, subscription_id, cancel_overdue_reminders, deleted_customer_name (`application/migrations/101_version_101.php`, `application/migrations/102_version_102.php`, `application/migrations/104_version_104.php`, `application/migrations/106_version_106.php`, `application/migrations/109_version_109.php`, `application/migrations/120_version_120.php`, `application/migrations/122_version_122.php`, `application/migrations/124_version_124.php`, `application/migrations/127_version_127.php`, `application/migrations/200_version_200.php`).
- Fields confirmed by core queries/views: id, number, date, duedate, clientid, currency, subtotal, total, total_tax, status, hash, project_id (`application/views/admin/tables/invoices.php`, `application/models/Invoices_model.php`).
- Note: full field list requires DB introspection; repository lacks the base CREATE statement.

### Payments
- Table: `tblinvoicepaymentrecords` (invoice payments). Base CREATE not found in repo.
- Fields confirmed by code/migrations: id, invoiceid, amount, date, daterecorded, paymentmode (varchar), paymentmethod, transactionid, note, addedfrom (nullable), plus indexes on invoiceid/paymentmethod (`application/migrations/101_version_101.php`, `application/migrations/126_version_126.php`, `application/migrations/129_version_129.php`, `application/models/Payments_model.php`, `application/views/admin/tables/payments.php`).
- Payment modes table: `tblpayment_modes` (renamed from `tblinvoicepaymentsmodes` in `application/migrations/231_version_231.php`) used for joins in payments listings (`application/models/Payments_model.php`, `application/views/admin/tables/payments.php`).

## Recommended implementation approach (based on findings)
- Create `modules/disowebs_ops/` using the standard module bootstrap + layout from `modules/custom_links/` and `modules/project_roadmap/`.
- Register hooks in `modules/disowebs_ops/disowebs_ops.php`:
  - `admin_init` to add project tabs via `$CI->app_tabs->add_project_tab()` with views in `modules/disowebs_ops/views/...`.
  - `get_dashboard_widgets` filter to inject role-aware widgets.
  - `estimate_accepted` / `proposal_accepted` actions for auto-snapshot, falling back to manual linking if project mapping is unavailable.
- Use `db_prefix()` for all core table references; create only new `dw_*` tables via module install/migrations.
- Enforce permissions + CSRF in all module controllers, mirroring access checks in `modules/custom_links/controllers/Custom_links.php`.
- For invoices/payments, prefer model APIs (`Invoices_model`, `Payments_model`) instead of raw SQL to avoid relying on schema assumptions.
