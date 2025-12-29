# Disowebs Ops Module - QA Checklist

> Use this checklist before each release to verify module functionality.

---

## Pre-Flight Checks

- [ ] Module version updated in `disowebs_ops.php` header
- [ ] Language file has all required keys (`disowebs_ops_lang.php`)
- [ ] No PHP syntax errors (`php -l` on all files)
- [ ] No undefined function/class errors on activation

---

## Installation Tests

### Fresh Install
- [ ] Upload zip via Setup → Modules succeeds
- [ ] Activation creates all 6 database tables
- [ ] Sidebar menu appears for admin users
- [ ] No PHP errors in application logs

### Upgrade Install
- [ ] Deactivate → Reactivate preserves data
- [ ] Migration scripts run without errors (if any)
- [ ] Version number updated correctly

### Uninstall
- [ ] Deactivation hides menu/tabs (data preserved)
- [ ] Full uninstall removes all `dw_*` tables
- [ ] No orphaned data in other tables

---

## Permissions Tests

- [ ] User with NO permissions: Cannot see sidebar or tabs
- [ ] User with `view` only: Can see dashboards/tabs, cannot edit
- [ ] User with `manage`: Can create/edit phases, milestones, proof
- [ ] User with `approve_change_requests`: Can approve CRs
- [ ] Non-approver cannot approve CRs (button hidden or blocked)

---

## Delivery Engine Tests

### Phases
- [ ] Create phase → appears in list
- [ ] Edit phase name → saves correctly
- [ ] Reorder phases (drag/drop or arrows) → order persists
- [ ] Delete phase → confirms, removes phase
- [ ] Delete phase with milestones → milestones become unassigned (phase cleared)
- [ ] Phase status transitions: Not Started → In Progress → Done

### Milestones
- [ ] Create milestone with week range → saves correctly
- [ ] Milestone appears under correct phase
- [ ] Mark milestone as done → `done_at` timestamp set
- [ ] Overdue milestone (past `week_end`) → flagged in UI
- [ ] Edit milestone → changes persist
- [ ] Delete milestone → confirms, removes

### Project Tab
- [ ] Delivery tab appears on project detail page
- [ ] Shows phases and milestones for that project only
- [ ] Completion percentage calculates correctly

---

## Scope Control Tests

### Scope Snapshot
- [ ] Manual snapshot creates from selected accepted estimate/proposal
- [ ] Snapshot JSON contains line items, totals, dates
- [ ] Snapshot is read-only after creation
- [ ] Multiple snapshots allowed (versioning)

### Auto-Capture (Hooks)
- [ ] Accept estimate → triggers scope snapshot if project linked
- [ ] Accept proposal → triggers scope snapshot if project linked
- [ ] No duplicate snapshots on re-accept

### Change Requests
- [ ] Create CR → appears in list as Draft
- [ ] CR requires: title, description, impact_days, impact_cost
- [ ] Submit CR → status changes to Submitted
- [ ] Approve CR → status changes to Approved, `approved_by` set
- [ ] Reject CR → status changes to Rejected
- [ ] Generate tasks from approved CR → tasks created with `[CR#ID]` prefix
- [ ] CR impact summary shows on project tab

---

## Proof Vault Tests

### Proof Entries
- [ ] Create entry with problem/solution/outcome → saves
- [ ] All three fields are required (validation)
- [ ] Entry appears in project proof tab
- [ ] Edit entry → changes persist
- [ ] Delete entry → confirms, removes entry and files

### File Uploads
- [ ] Upload single image → file saved, thumbnail shown
- [ ] Upload multiple files → all attached to entry
- [ ] Upload non-image (PDF, doc) → handled gracefully
- [ ] Delete file → removes from storage and database
- [ ] Max file size respected (no server error)

### Case Study Export
- [ ] Export button visible on project with proof entries
- [ ] Export generates HTML with all proof entries
- [ ] HTML includes: project name, client, problem/solution/outcome
- [ ] Images embedded or linked correctly
- [ ] Export with no proof entries → shows message or disabled

---

## Dashboard Widget Tests

### CEO Weekly Execution Widget
- [ ] Shows milestones done this week
- [ ] Shows payments received this week
- [ ] Shows proof entries created this week
- [ ] Warning appears if any weekly signal is zero
- [ ] Widget only visible to admin/CEO role

### CEO Capacity & Risk Widget
- [ ] Shows active projects vs limit
- [ ] Shows overdue milestones list (with project links)
- [ ] Shows status badge when over limit
- [ ] Widget only visible to admin/CEO role

### CRM Manager Priorities Widget
- [ ] Shows follow-ups due today/overdue
- [ ] Shows overdue invoices
- [ ] Shows CRs pending approval
- [ ] Widget only visible to CRM Manager role

### TSA Support Queue Widget
- [ ] Shows open ticket count
- [ ] Shows tickets aging >48h
- [ ] Shows projects awaiting QA/proof
- [ ] Widget only visible to TSA/Support role

---

## Edge Cases

- [ ] Project with no phases → shows empty state, can add
- [ ] Phase with no milestones → displays correctly
- [ ] Very long phase/milestone names → truncated or wrapped
- [ ] Special characters in text fields → properly escaped
- [ ] Concurrent edits → no data corruption
- [ ] Large number of proof entries (20+) → list renders without errors

---

## Performance

- [ ] Dashboard widgets load in <2 seconds
- [ ] Project tab loads in <1 second
- [ ] Proof vault with 50+ entries loads reasonably
- [ ] No N+1 query issues (check query log)

---

## Browser Compatibility

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

---

## Final Sign-Off

| Check | Tester | Date |
|-------|--------|------|
| Fresh install | | |
| Permissions | | |
| Delivery engine | | |
| Scope control | | |
| Proof vault | | |
| Dashboard widgets | | |
| Edge cases | | |

**Release Approved**: [ ] Yes / [ ] No

**Notes**:
_______________________________________
_______________________________________
_______________________________________

---

## Quick Regression Test (5-minute smoke test)

For quick verification after minor changes:

1. [ ] Activate module → no errors
2. [ ] Create a phase on any project
3. [ ] Create a milestone in that phase
4. [ ] Create a change request
5. [ ] Create a proof entry with file upload
6. [ ] Check dashboard widgets appear
7. [ ] Export case study

If all pass → safe for deployment.
