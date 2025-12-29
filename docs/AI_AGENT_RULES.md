# AI_AGENT_RULES.md
# Disowebs Ops Module — Codex Agent Rules (Perfex CRM)

## 0) Mission
Build a NEW Perfex CRM module named `disowebs_ops` that turns each Perfex Project into a Disowebs Execution Workspace:
- Phases
- Weekly Milestones
- Scope Snapshot
- Change Requests
- Proof Vault
- Role dashboards/widgets

## 1) Non-Negotiable Constraints (Hard Rules)
1. **NO Perfex core edits.**  
   - All changes must live under `/modules/disowebs_ops/*`.
   - Use Perfex hooks/actions/filters only.

2. **No data duplication.**  
   - Customers, contacts, leads, invoices, projects remain Perfex records.
   - Disowebs tables only store Disowebs-specific data and reference Perfex IDs.

3. **DB changes are module-managed.**
   - Create new tables prefixed `dw_` via module install/activation migration script.
   - Include indexes; keep schema minimal.

4. **Security is mandatory.**
   - Permission checks on every action.
   - CSRF for forms.
   - Validate all inputs server-side.
   - Escape outputs in views.

5. **Upgrade safe.**
   - No monkey patches, no overwriting Perfex internals.
   - Prefer additive UI via hooks.

6. **Role-aware UX.**
   - CEO, CRM Manager, Tech Support Assistant (TSA) have distinct capabilities and dashboards.

7. **Stop conditions (must stop & report).**
   - Missing/unclear hook points for: Project tabs, proposal/estimate accepted, dashboard widgets
   - Unexpected table names/relationships
   - Repo isn’t a standard Perfex module structure

## 2) Definition of Done
A feature is DONE only when:
- Install script runs cleanly
- Module enable/disable works
- Project tabs render
- CRUD works with validation + permissions
- Dashboard widgets show correct counts
- No core files were modified

## 3) Output Requirements (Every Response)
At end of every output include:
1) Files added/changed list
2) Confirmation: “No core files modified”
3) Next step (per Build Protocol) and why
