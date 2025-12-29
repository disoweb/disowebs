# DISOWEBS_OPS_MODULE_SPEC.md
# Disowebs Ops Module (Perfex) — Full Spec (Build Target)

## Goal
Extend Perfex Projects into a Disowebs Execution Workspace without modifying core.

## Navigation
- Sidebar menu group: **Disowebs Ops**
  - Delivery Engine
  - Scope & Change Requests
  - Proof Vault
  - Reports (later)

## Project View Tabs (must)
1) Phases & Milestones
2) Scope
3) Change Requests
4) Proof

---

## Data Model (new tables only)
### dw_project_phases
- id PK
- project_id (Perfex projects.id)
- name
- position
- status: not_started | in_progress | done
- started_at nullable
- completed_at nullable
- created_at, updated_at
Indexes: (project_id), (project_id, status)

### dw_project_milestones
- id PK
- project_id
- phase_id nullable
- title
- description nullable
- week_start date
- week_end date
- status: planned | in_progress | done
- done_at nullable
- created_at, updated_at
Indexes: (project_id), (project_id, status), (week_start, week_end)

### dw_scope_snapshots
- id PK
- project_id
- source_type: estimate | proposal
- source_id
- snapshot_json (longtext)
- created_at
Indexes: (project_id), (source_type, source_id)

### dw_change_requests
- id PK
- project_id
- title
- description longtext
- impact_days int default 0
- impact_cost decimal(15,2) default 0
- status: draft | submitted | approved | rejected | implemented
- approved_by staff_id nullable
- approved_at nullable
- created_by staff_id
- created_at, updated_at
Indexes: (project_id), (status), (approved_by)

### dw_proof_entries
- id PK
- project_id
- title
- problem longtext
- solution longtext
- outcome longtext
- created_by staff_id
- created_at
Indexes: (project_id), (created_at)

### dw_proof_files
- id PK
- proof_entry_id
- file_path
- file_type
- created_at
Indexes: (proof_entry_id)

---

## Core Workflows
### A) Delivery (Phases & Milestones)
- Each project has 4 default phases (configurable):
  - Discovery / Build / Deploy / Support
- Each active project should have at least 1 milestone in the current week
- Marking a milestone “done” records done_at

### B) Scope Snapshot
- Manual: “Create snapshot from accepted estimate/proposal”
- Automated (best effort): when estimate/proposal accepted create snapshot

Snapshot stores read-only contract:
- included/excluded items
- totals
- timeline notes

### C) Change Requests
- Logged by CRM Manager
- Approved only by CEO (permission)
- Approval requires impact_days and impact_cost
- Approved CR can generate Perfex tasks `[DWSID] …`

### D) Proof Vault
- Proof entry required before project closure (policy)
- Export Case Study Draft HTML for quick publishing

---

## Dashboard Widgets (role-aware)
### CEO Widget — Weekly Execution
- Delivery = milestones done this week
- Revenue = invoices paid (or payments recorded) this week
- Proof = proof entries created this week
- Show warning if any == 0

### CRM Manager Widget — Flow Control
- Follow-ups due today
- Proposals awaiting response
- Overdue invoices
- CRs pending CEO approval

### TSA Widget — Stability
- Open tickets
- Tickets older than 48h
- Projects awaiting QA/UAT (if tracked)
- Proof backlog (projects closed without proof)

---

## Permissions
- disowebs_ops_view
- disowebs_ops_manage
- disowebs_ops_approve_change_requests

Mapping:
- CEO: all permissions
- CRM Manager: view + manage (no approve)
- TSA: view + manage (limited to proof/support views where applicable)

---

## Settings
- Auto-create default phases on new project (on/off)
- Default phases list (editable order)
- Default milestone template (optional; can be V2)

---

## Definition of Done
- Module installs and enables
- Tabs appear in project view
- CRUD works with validation + permissions
- Widgets display correct counts
- No Perfex core changes
