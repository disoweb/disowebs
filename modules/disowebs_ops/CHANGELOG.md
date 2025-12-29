# Changelog

All notable changes to the Disowebs Ops module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [0.1.0] - 2025-12-27

### Added

#### Core Module
- Module skeleton with Perfex CRM integration
- Sidebar menu group with sub-items
- Permission system (view, manage, approve_change_requests)
- Install/deactivate/uninstall hooks
- English language file with all translations

#### Delivery Engine (Week 2)
- Project phases CRUD with status transitions
- Phase reordering (move up/down)
- Milestones with week-based scheduling
- Milestone status tracking (planned → in_progress → done)
- Overdue milestone detection
- Completion percentage calculations
- Project tab integration

#### Scope Control (Week 3)
- Scope snapshots from accepted estimates/proposals
- Auto-capture on estimate_accepted and proposal_accepted hooks
- Manual scope lock functionality
- Change Requests CRUD with approval workflow
- CEO-only approval permission gate
- Impact tracking (days + cost)
- Task generation from approved CRs with `[CR#ID]` prefix
- Project tab showing scope + CR summary

#### Proof Vault (Week 4)
- Proof entries with structured format (problem/solution/outcome)
- Multi-file upload support
- File type validation
- Case study HTML export per project
- Project tab integration

#### Dashboard Widgets (Week 4)
- CEO Weekly Execution widget
  - Milestones done this week
  - Payments received this week
  - Proof entries this week
- CRM Manager Priorities widget
  - Follow-ups due
  - Overdue invoices
  - Pending change requests
- TSA Support Queue widget
  - Open tickets
  - Tickets over 48h
  - Projects awaiting QA/proof
- Capacity & Risk panel
  - Active projects vs limit
  - Overdue milestones list

#### Documentation (Week 4 Day 20)
- README.md with full installation instructions
- QA_CHECKLIST.md for release testing
- CHANGELOG.md for version tracking

### Database Tables
- `dw_project_phases`
- `dw_project_milestones`
- `dw_scope_snapshots`
- `dw_change_requests`
- `dw_proof_entries`
- `dw_proof_files`

### Options
- `disowebs_ops_auto_create_phases` - Auto-create default phases on new projects
- `disowebs_ops_active_project_limit` - WIP limit for capacity tracking

---

## [Unreleased]

### Added
- Global Scope & Change Requests overview page (recent CRs + snapshots)
- Global Proof Vault overview page (totals + recent entries)
- Delivery Engine setting for active project limit (WIP indicator)
- CRM Manager widget metric for proposals awaiting response
- TSA widget metric for projects awaiting QA/UAT
- Warning when active projects lack a milestone for the current week
- Proof-required warning when a project is closed without proof entries
- Role-based action gating for phases, milestones, CRs, and proof per OS permissions matrix
- CEO dashboard widgets for revenue & cashflow, pipeline forecast, risk alerts, and growth flywheel
- CRM dashboard widgets for pipeline hygiene, active projects overview, and retainer tracker
- TSA dashboard widgets for QA readiness, documentation, and proof backlog
- Capacity widget now shows queued projects and blocked milestone list
- Scope tab policy notice for CR-required scope changes
- Deposit gate and final payment warnings on project phases
- Just Execution widget range selector (day/week/month/quarter/year) and compact card layout

### Fixed
- Proof upload storage uses `FCPATH/uploads/disowebs_ops/proof` with base URL links
- Delete actions accept CSRF-only POST submissions
- Proof backlog now counts finished projects without proof

### Planned for v0.2.0
- Settings page for module configuration
- Default phase templates
- Bulk CR actions
- Proof entry templates
- PDF case study export
- Email notifications for CR approvals
- API endpoints for external integrations

---

## Version History

| Version | Date | Milestone |
|---------|------|-----------|
| 0.1.0 | 2025-12-27 | Week 4 Release - Module v1 |
