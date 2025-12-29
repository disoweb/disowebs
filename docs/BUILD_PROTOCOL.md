# BUILD_PROTOCOL.md
# Disowebs Ops Module — Build Protocol (Daily Milestones + Weekly Summary)

## Principle
Codex must follow phases in order and stop at every gate. No skipping.

---

## WEEK 1 — Repo Discovery + Skeleton + DB (Goal: module activates cleanly)
### Day 1 — Repo Discovery (MANDATORY)
- Identify Perfex version/structure; confirm CodeIgniter module layout
- Inspect at least 2 existing modules to mirror conventions
- Locate hook points for:
  - Project view tabs injection
  - Dashboard widgets
  - Estimate/Proposal accepted event (or nearest)
- Map exact table names for:
  - projects
  - estimates/proposals
  - invoices/payments
Deliverable: `docs/REPO_DISCOVERY.md`

### Day 2 — Module Skeleton
- Create `modules/disowebs_ops/` skeleton matching repo conventions
- Register module, add language file scaffolding, permissions stubs
- Add sidebar menu group “Disowebs Ops”
Deliverable: skeleton loads without errors

### Day 3 — DB Install/Migrations
- Implement install/activate script to create all `dw_*` tables + indexes
- Register capabilities:
  - disowebs_ops_view
  - disowebs_ops_manage
  - disowebs_ops_approve_change_requests
Deliverable: install/enable works, tables created

### Day 4 — Base Models + Repos (Lightweight)
- Create models for `dw_project_phases`, `dw_project_milestones`
- Add shared helpers (date ranges, current week Mon–Sun, staff id)
Deliverable: basic CRUD callable

### Day 5 — Project Tab Injection (Empty Tabs)
- Add project tabs (placeholders) via supported hook:
  - Phases & Milestones
  - Scope
  - Change Requests
  - Proof
Deliverable: tabs visible in project view

### Weekly Summary (Week 1)
- What hooks were used (and where)
- Tables created + indexes
- Tabs showing correctly
- Any blockers

STOP GATE: Do not proceed if tabs/hook points aren’t stable.

---

## WEEK 2 — Delivery Engine (Phases + Milestones)
### Day 6 — Phases UI + CRUD
- Implement phases list, add/edit, reorder, start/complete
- Phase status: not_started / in_progress / done
Deliverable: phases working per project

### Day 7 — Milestones UI + CRUD
- Implement week-based milestones
- Group by phase + week
- Actions: create, edit, mark done
Deliverable: milestones working per project

### Day 8 — Auto-init Defaults
- Setting: auto-create phases on project create
- Optional: auto-create milestone template
Deliverable: on new project, defaults are created (toggle)

### Day 9 — Delivery Indicators
- Completion % for phases/milestones
- “At risk” detection: overdue milestones
Deliverable: risk badges appear

### Day 10 — QA + Permissions pass
- Ensure all actions check permissions and validate inputs
- Confirm no N+1 loops in lists
Deliverable: secure delivery engine

### Weekly Summary (Week 2)
- Screens working
- Settings working
- Permission matrix verified

STOP GATE: Delivery engine must be stable before scope/CR.

---

## WEEK 3 — Scope Snapshot + Change Requests
### Day 11 — Scope Snapshot UI
- Add Scope tab:
  - view latest snapshot
  - history list
  - button to create snapshot from accepted estimate/proposal
Deliverable: manual snapshot works

### Day 12 — Accepted Estimate/Proposal Hook (If possible)
- Implement hook to auto-create snapshot when accepted
- If mapping to project isn’t possible, implement “Create/Link project” action
Deliverable: snapshot automation OR safe manual workflow

### Day 13 — Change Requests CRUD + Approval
- Implement CR flow:
  draft → submitted → approved/rejected → implemented
- Approval requires `disowebs_ops_approve_change_requests`
Deliverable: CRs work end-to-end

### Day 14 — Generate Tasks from Approved CR
- Create Perfex tasks with prefix `[CR#ID]`
- Link tasks to project
Deliverable: task generation works

### Day 15 — Profit Hooks (Light)
- On CR approval, require impact_days and impact_cost
- Show simple “impact summary” on project
Deliverable: CR impact enforced

### Weekly Summary (Week 3)
- Scope snapshot reliability
- CR workflow and permissions
- Any constraints with Perfex hooks

STOP GATE: CR + scope must be enforced before Proof/Dashboards.

---

## WEEK 4 — Proof Vault + Dashboards + QA
### Day 16 — Proof Vault CRUD + Upload
- Proof entries per project
- Multiple file uploads
- Safe storage + validation
Deliverable: proof entries & files

### Day 17 — Export Case Study Draft (HTML)
- Export per project combining proof entries
Deliverable: export view works

### Day 18 — Dashboard Widgets (Role-aware)
- CEO widget: Weekly Execution (Delivery/Revenue/Proof)
- CRM Manager: follow-ups due, invoices overdue, CRs pending approval
- TSA: tickets open, tickets >48h, projects awaiting QA/proof backlog
Deliverable: widgets visible & correct

### Day 19 — WIP/Capacity Flags (Light)
- Active projects count vs limit
- Overdue milestone list
Deliverable: at-risk list

### Day 20 — QA Checklist + Release Notes
- Provide `docs/QA_CHECKLIST.md`
- Provide install/enable instructions
- Confirm “no core files edited”
Deliverable: ready-to-run module package

### Weekly Summary (Week 4)
- All features done
- Known limitations
- Next iteration backlog
