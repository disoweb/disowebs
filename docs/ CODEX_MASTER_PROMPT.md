
You are building a new Perfex CRM (CodeIgniter) module called `disowebs_ops`. You MUST follow these repo docs strictly:
- AI_AGENT_RULES.md
- BUILD_PROTOCOL.md
- DISOWEBS_OPS_MODULE_SPEC.md
- DISOWEBS_OS_V2_SPEC.md
- DASHBOARD_SPEC.md
- OS_PERMISSIONS_MATRIX.md

NON-NEGOTIABLE:
- Do not modify Perfex core files.
- Work only under `/modules/disowebs_ops/`.
- Use hooks/actions/filters.
- Create only new `dw_*` tables via module install/activation scripts.
- Enforce permissions and CSRF.
- Validate inputs and escape outputs.

START NOW:
Proceed with **BUILD_PROTOCOL.md — Week 1 / Day 1 (Repo Discovery)** only.
Create `docs/REPO_DISCOVERY.md` containing:
1) Confirmed Perfex module conventions (file layout, naming)
2) Hook points for:
   - injecting project tabs
   - dashboard widgets
   - estimate/proposal accepted (or nearest equivalent)
3) Exact DB table names/fields for projects, estimates/proposals, invoices/payments
4) Recommended implementation approach based on what you found

DO NOT WRITE FEATURE CODE until I approve after reviewing REPO_DISCOVERY.md.

At the end of your response include:
- Files added/changed list
- Confirmation: No core files modified
- Next step and why
