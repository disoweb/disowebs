# OS_PERMISSIONS_MATRIX.md
# Disowebs OS — Permissions Matrix (Perfex + Disowebs Ops Module)

## Purpose
Define **exact** role → permissions → screens → actions for Disowebs operations.
This is the source of truth for Codex when implementing role-based access in the `disowebs_ops` module.

> Note: Perfex has its own native permissions. This matrix adds **Disowebs Ops capabilities** and maps them to roles.
> Perfex native permissions should remain intact; Disowebs Ops checks should be layered on top.

---

## Roles
1) **CEO & Lead Developer** (You)
2) **Customer Relationship Manager (CRM Manager)**
3) **Technical Support Assistant (TSA)**

---

## Disowebs Ops Capabilities (Module-Level)
- `disowebs_ops_view`
- `disowebs_ops_manage`
- `disowebs_ops_approve_change_requests`

Optional (Phase 2, if needed):
- `disowebs_ops_manage_scope`
- `disowebs_ops_export_case_study`
- `disowebs_ops_manage_settings`

For v1, keep only the three core capabilities.

---

## Capability → Role Assignment (Module-Level)

| Capability | CEO | CRM Manager | TSA |
|---|---:|---:|---:|
| disowebs_ops_view | ✅ | ✅ | ✅ |
| disowebs_ops_manage | ✅ | ✅ (limited) | ✅ (limited) |
| disowebs_ops_approve_change_requests | ✅ | ❌ | ❌ |

**Meaning of “limited”**
- CRM Manager can manage: milestones planning, communication logs, CR creation/submission, invoices sending/follow-ups (Perfex), but cannot approve CRs or finalize scope.
- TSA can manage: proof entries/files, QA checklist fields (if tracked), ticket workflow (Perfex), but cannot touch pricing/scope/CR approvals.

---

## Screen Access Map (What each role sees)

### A) Perfex Native Screens (Baseline)
> These are the Perfex areas you already have. Use native Perfex permissions + Disowebs role policy.

| Screen | CEO | CRM Manager | TSA | Notes |
|---|---:|---:|---:|---|
| Dashboard | ✅ | ✅ | ✅ | Role dashboards/widgets differ |
| Customers | ✅ | ✅ | ✅ (view) | TSA view-only |
| Leads | ✅ | ✅ | ❌ | TSA should not manage leads |
| Proposals/Estimates | ✅ | ✅ (view/send) | ❌ | CEO writes proposals; CRM sends/follows up |
| Invoices/Payments | ✅ | ✅ (send/follow-up) | ❌ | TSA not involved |
| Projects | ✅ | ✅ | ✅ | All need project access |
| Tasks | ✅ | ✅ | ✅ | TSA mostly for QA/support tasks |
| Support Tickets | ✅ | ✅ (view/escalate) | ✅ (manage) | TSA owns ticket ops |
| Contracts | ✅ | ✅ (view) | ❌ | Optional |
| Reports | ✅ | ✅ (limited) | ✅ (support reports only) | Keep simple |

---

### B) Disowebs Ops Module Screens (New)
| Screen / Area | CEO | CRM Manager | TSA | Capability required |
|---|---:|---:|---:|---|
| Disowebs Ops (menu group) | ✅ | ✅ | ✅ | disowebs_ops_view |
| Delivery Engine (overview) | ✅ | ✅ | ✅ | disowebs_ops_view |
| Scope & Change Requests (overview) | ✅ | ✅ | ✅ (view only) | disowebs_ops_view |
| Proof Vault (overview) | ✅ | ✅ (view) | ✅ (manage) | disowebs_ops_view/manage |
| Settings (module) | ✅ | ❌ | ❌ | (optional) manage_settings |

---

## Project Workspace Tabs (Inside each Project)
These appear inside Perfex project view.

### 1) Tab: Phases & Milestones
| Action | CEO | CRM Manager | TSA | Capability |
|---|---:|---:|---:|---|
| View phases/milestones | ✅ | ✅ | ✅ | disowebs_ops_view |
| Create/edit phases | ✅ | ❌ | ❌ | disowebs_ops_manage |
| Reorder phases | ✅ | ❌ | ❌ | disowebs_ops_manage |
| Start/Complete phase | ✅ | ❌ | ❌ | disowebs_ops_manage |
| Create milestone | ✅ | ✅ | ✅ (QA milestones only) | disowebs_ops_manage |
| Edit milestone (title/date) | ✅ | ✅ | ✅ (QA-only) | disowebs_ops_manage |
| Mark milestone done | ✅ | ✅ | ✅ (QA milestones) | disowebs_ops_manage |
| Delete milestone | ✅ | ✅ (restricted) | ❌ | disowebs_ops_manage |

**Restriction rule**
- CRM Manager may manage milestone planning/dates/status but should not modify phases.
- TSA may only mark “QA/Support” milestones done (tag-based or by category if implemented later).
If tag-based restrictions aren’t implemented in v1, allow TSA to mark done but require CEO visibility via dashboard.

---

### 2) Tab: Scope
| Action | CEO | CRM Manager | TSA | Capability |
|---|---:|---:|---:|---|
| View scope snapshot(s) | ✅ | ✅ | ✅ (view) | disowebs_ops_view |
| Create scope snapshot manually | ✅ | ❌ | ❌ | disowebs_ops_manage |
| Link accepted estimate/proposal to project | ✅ | ✅ (assist) | ❌ | manage (CEO final) |

**Rule**
- CEO owns scope lock. CRM Manager can gather docs but not finalize scope.

---

### 3) Tab: Change Requests
| Action | CEO | CRM Manager | TSA | Capability |
|---|---:|---:|---:|---|
| View CR list/details | ✅ | ✅ | ✅ (view) | disowebs_ops_view |
| Create CR (draft/submitted) | ✅ | ✅ | ❌ | disowebs_ops_manage |
| Edit CR (pre-approval) | ✅ | ✅ | ❌ | disowebs_ops_manage |
| Approve/Reject CR | ✅ | ❌ | ❌ | disowebs_ops_approve_change_requests |
| Require impact_days & impact_cost | ✅ | ✅ (must fill) | ❌ | manage |
| Generate tasks from approved CR | ✅ | ✅ (trigger) | ❌ | manage + approve already done |
| Mark CR implemented | ✅ | ✅ | ✅ (support-related) | disowebs_ops_manage |

**Rule**
- CRM Manager owns logging CRs and ensuring fields are complete.
- CEO owns approval.
- TSA can mark implemented only for support-type CRs (optional refinement later).

---

### 4) Tab: Proof
| Action | CEO | CRM Manager | TSA | Capability |
|---|---:|---:|---:|---|
| View proof entries | ✅ | ✅ | ✅ | disowebs_ops_view |
| Create proof entry | ✅ | ✅ (basic) | ✅ (primary owner) | disowebs_ops_manage |
| Upload proof files | ✅ | ✅ | ✅ | disowebs_ops_manage |
| Edit proof content | ✅ | ✅ (limited) | ✅ | disowebs_ops_manage |
| Delete proof entry | ✅ | ❌ | ❌ | disowebs_ops_manage |
| Export case study draft | ✅ | ✅ | ✅ | disowebs_ops_view (or optional export perm) |

**Rule**
- TSA is primary owner of proof quality and completeness.
- CEO approves anything published publicly (outside OS).

---

## Dashboard Widgets (Role-Aware)
Widgets should display based on role permissions and/or staff role assignment.

### CEO Dashboard Widgets
- Weekly Execution Scoreboard (Delivery/Revenue/Proof)
- Pipeline Forecast (from Perfex CRM)
- Capacity/WIP
- Risk alerts (overdue milestones, overdue invoices, missing scope snapshot)
- Growth flywheel (proof/testimonials/case studies)

**Access:** CEO only (or staff with admin)

---

### CRM Manager Dashboard Widgets
- Today’s priorities: follow-ups due, invoices overdue, CRs pending approval
- Active projects overview: next update due, milestone status, payment status
- Retainer tracker: launched last 30 days, offered/accepted/declined

**Access:** CRM Manager + CEO

---

### TSA Dashboard Widgets
- Support queue: open, >48h, priority
- QA readiness: projects awaiting QA
- Documentation backlog
- Proof backlog (projects without proof)

**Access:** TSA + CEO

---

## Enforcement Notes (Implementation Guidance for Codex)
1. Add server-side checks:
   - `has_permission('disowebs_ops', '', 'view')` style (match repo conventions)
2. Gate **approval endpoints** strictly by `disowebs_ops_approve_change_requests`.
3. For “limited manage” roles (CRM/TSA), use:
   - separate permission checks per controller action
   - OR a per-action allowlist based on role
4. Never rely on UI-only hiding. Always enforce server-side.

---

## Minimal V1 Implementation (Recommended)
To keep v1 fast and stable:
- Implement only 3 module capabilities
- Enforce strongest boundaries:
  - CEO only: phase management, scope snapshot creation, CR approval, deletions
  - CRM: CR creation/submission, milestone mgmt, view scope/proof
  - TSA: proof mgmt, support-related milestones, view CR/scope

Refinements (tag-based restrictions) can be v2.

---
