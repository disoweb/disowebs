# Disowebs Ops Module v0.1.0

> Delivery engine, scope control, and proof vault for Perfex CRM projects.

## Overview

Disowebs Ops extends Perfex CRM with operational tools designed for agency/consultancy workflows:

- **Delivery Engine**: Phases + Milestones tracking per project
- **Scope Control**: Lock scope from accepted estimates/proposals + manage Change Requests
- **Proof Vault**: Capture project wins (problem→solution→outcome) for case studies
- **Role Dashboards**: CEO, CRM Manager, TSA widgets for daily ops visibility

---

## Requirements

- Perfex CRM 3.4.x or higher
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+

---

## Installation

### Method 1: Upload via Admin Panel

1. Download the `disowebs_ops.zip` module package
2. Go to **Setup → Modules** in Perfex admin
3. Click **Upload Module** and select the zip file
4. Click **Activate** on the Disowebs Ops module

### Method 2: Manual Installation

1. Extract the module to `/modules/disowebs_ops/`
2. Go to **Setup → Modules** in Perfex admin
3. Find "Disowebs Ops" and click **Activate**

### Post-Installation

1. Go to **Setup → Roles** and assign permissions:
   - `Disowebs Ops - View`: See dashboards and project tabs
   - `Disowebs Ops - Manage`: Create/edit phases, milestones, proof entries
   - `Disowebs Ops - Approve Change Requests`: CEO-only permission

2. Configure default phases (optional):
   - Go to **Disowebs Ops → Delivery Engine**
   - Enable "Auto-create phases on project" if desired

---

## Database Tables

The module creates these tables on activation:

Table names use your Perfex `db_prefix()` and are listed without the prefix:

| Table | Purpose |
|-------|---------|
| `dw_project_phases` | Project phase definitions |
| `dw_project_milestones` | Weekly milestones per phase |
| `dw_scope_snapshots` | Locked scope from accepted estimates/proposals |
| `dw_change_requests` | Scope change requests with approval workflow |
| `dw_proof_entries` | Proof vault entries (problem/solution/outcome) |
| `dw_proof_files` | File attachments for proof entries |

---

## Features

### 1. Delivery Engine

Navigate to **Disowebs Ops → Delivery Engine** or use the **Delivery** tab on any project.

- **Phases**: Define project phases (Discovery, Design, Development, etc.)
- **Milestones**: Weekly deliverables within each phase
- **Status Tracking**: Not Started → In Progress → Complete
- **At-Risk Alerts**: Overdue milestones highlighted automatically

### 2. Scope Control

Available on the **Scope** tab of each project.

- **Scope Snapshot**: Auto-captured when estimate/proposal is accepted
- **Change Requests**: Formal CR workflow with impact tracking
- **Approval Gate**: Only staff with `approve_change_requests` permission can approve
- **Task Generation**: Approved CRs can auto-create tasks with `[CR#ID]` prefix

### 3. Proof Vault

Use the **Proof** tab on any project.

- **Proof Entries**: Structured format (Problem → Solution → Outcome)
- **File Uploads**: Screenshots, before/after, metrics
- **Case Study Export**: Generate HTML draft for marketing use

### 4. Dashboard Widgets

Role-specific widgets appear on the admin dashboard:

| Widget | Role | Shows |
|--------|------|-------|
| CEO Weekly Execution | CEO/Admin | Milestones done this week, payments received, proof entries this week |
| CEO Capacity & Risk | CEO/Admin | Active projects vs limit, overdue milestones |
| CRM Manager Priorities | CRM Manager | Follow-ups due, overdue invoices, CRs pending approval |
| TSA Support Queue | Support Staff | Open tickets, tickets over 48h, projects without proof |

---

## Permissions

| Permission | Description |
|------------|-------------|
| `view` | View dashboards, project tabs, reports |
| `manage` | Create/edit phases, milestones, proof entries, CRs |
| `approve_change_requests` | Approve/reject change requests (CEO-only) |

---

## Hooks & Filters

### Actions

```php
// Fired when a new proof entry is created
hooks()->do_action('disowebs_ops_proof_created', $proof_id, $project_id);

// Fired when a change request is approved
hooks()->do_action('disowebs_ops_cr_approved', $cr_id, $project_id);

// Fired when a phase status changes
hooks()->do_action('disowebs_ops_phase_status_changed', $phase_id, $old_status, $new_status);
```

### Filters

```php
// Modify default phases created on new projects
hooks()->apply_filters('disowebs_ops_default_phases', $phases);

// Modify case study export HTML
hooks()->apply_filters('disowebs_ops_case_study_html', $html, $project_id);
```

---

## Uninstallation

1. Go to **Setup → Modules**
2. Click **Deactivate** on Disowebs Ops
3. Optionally click **Uninstall** to remove database tables

> ⚠️ **Warning**: Uninstalling removes all module data (phases, milestones, CRs, proof entries).

---

## Troubleshooting

### Module not appearing in sidebar
- Check that the module is **Activated** in Setup → Modules
- Verify user has `Disowebs Ops - View` permission

### Project tabs not showing
- Clear Perfex cache: Setup → Settings → Clear Cache
- Ensure project exists and user has project access

### Dashboard widgets missing
- Widgets are role-filtered; check user role assignments
- CEO widget requires admin or specific permission

---

## Changelog

### v0.1.0 (Week 4 Release)
- Initial release
- Delivery engine (phases + milestones)
- Scope snapshots + change request workflow
- Proof vault with file uploads
- Case study export (HTML)
- CEO/CRM/TSA dashboard widgets

---

## Support

For issues or feature requests:
- Internal: Create a task in Perfex with tag `[disowebs_ops]`
- Email: support@disowebs.com

---

## License

Proprietary - Disowebs Ltd. Internal use only.
