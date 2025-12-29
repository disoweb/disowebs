<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Disowebs_ops_projects extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if (!defined('DISOWEBS_OPS_MODULE_NAME')) {
            show_404();
        }

        $this->load->model('projects_model');
        $this->load->model('disowebs_ops/dw_project_phases_model');
        $this->load->model('disowebs_ops/dw_project_milestones_model');
        $this->load->model('disowebs_ops/dw_change_requests_model');
        $this->load->model('disowebs_ops/dw_scope_snapshots_model');
        $this->load->model('disowebs_ops/dw_proof_entries_model');
        $this->load->model('disowebs_ops/dw_proof_files_model');
        $this->load->model('tasks_model');
    }

    public function add_phase($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_phase_manage_access();
        $this->require_post();

        $name = trim($this->input->post('name', true));
        $status = $this->sanitize_phase_status($this->input->post('status', true));

        if ($name === '') {
            set_alert('danger', _l('disowebs_ops_phase_name_required'));
            return $this->redirect_project($project_id);
        }

        $data = [
            'project_id' => (int) $project_id,
            'name'       => $name,
            'status'     => $status,
        ];

        if ($status === 'in_progress') {
            $data['started_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'done') {
            $data['started_at'] = date('Y-m-d H:i:s');
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        $insert_id = $this->dw_project_phases_model->add($data);

        if ($insert_id) {
            if ((int) get_option('disowebs_ops_auto_create_milestones') === 1) {
                $offset = $this->dw_project_milestones_model->count_by_project($project_id);
                disowebs_ops_create_phase_milestones($project_id, $insert_id, $name, $offset);
            }
            set_alert('success', _l('disowebs_ops_phase_added'));
        } else {
            set_alert('danger', _l('disowebs_ops_phase_add_failed'));
        }

        $this->redirect_project($project_id);
    }

    public function update_phase($project_id, $phase_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_phase_manage_access();
        $this->require_post();

        $phase = $this->dw_project_phases_model->get($phase_id);
        if (!$phase || (int) $phase->project_id !== (int) $project_id) {
            show_404();
        }

        $name = trim($this->input->post('name', true));
        $status = $this->sanitize_phase_status($this->input->post('status', true));

        if ($name === '') {
            set_alert('danger', _l('disowebs_ops_phase_name_required'));
            return $this->redirect_project($project_id);
        }

        // Enforce deposit gate before moving Build/Deploy phases to in_progress
        if ($status === 'in_progress' && $phase->status !== 'in_progress') {
            $phase_name_lower = strtolower($name);
            if (strpos($phase_name_lower, 'build') !== false || strpos($phase_name_lower, 'deploy') !== false || strpos($phase_name_lower, 'development') !== false) {
                $this->load->model('disowebs_ops/dw_project_gates_model');
                $this->dw_project_gates_model->calculate_deposit_status($project_id);
                if (!disowebs_ops_can_proceed_to_build($project_id)) {
                    set_alert('danger', _l('disowebs_ops_deposit_gate_required_for_build'));
                    log_activity('Phase Progress Blocked - Deposit Gate Not Cleared [Project ID: ' . $project_id . ', Phase: ' . $name . ']');
                    return $this->redirect_project($project_id);
                }
            }
        }

        $data = [
            'name'   => $name,
            'status' => $status,
        ];

        if ($status === 'not_started') {
            $data['started_at'] = null;
            $data['completed_at'] = null;
        } elseif ($status === 'in_progress') {
            $data['started_at'] = $phase->started_at ?: date('Y-m-d H:i:s');
            $data['completed_at'] = null;
        } elseif ($status === 'done') {
            $data['started_at'] = $phase->started_at ?: date('Y-m-d H:i:s');
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        $updated = $this->dw_project_phases_model->update($phase_id, $data);

        if ($updated) {
            set_alert('success', _l('disowebs_ops_phase_updated'));
        } else {
            set_alert('warning', _l('disowebs_ops_phase_update_no_change'));
        }

        $this->redirect_project($project_id);
    }

    public function delete_phase($project_id, $phase_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_phase_manage_access();
        $this->require_post();

        $phase = $this->dw_project_phases_model->get($phase_id);
        if (!$phase || (int) $phase->project_id !== (int) $project_id) {
            show_404();
        }

        $this->db->where('phase_id', $phase_id);
        $this->db->update(db_prefix() . 'dw_project_milestones', ['phase_id' => null]);

        $deleted = $this->dw_project_phases_model->delete($phase_id);

        if ($deleted) {
            set_alert('success', _l('disowebs_ops_phase_deleted'));
        } else {
            set_alert('danger', _l('disowebs_ops_phase_delete_failed'));
        }

        $this->redirect_project($project_id);
    }

    public function move_phase($project_id, $phase_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_phase_manage_access();
        $this->require_post();

        $direction = $this->input->post('direction', true);
        if (!in_array($direction, ['up', 'down'], true)) {
            set_alert('danger', _l('disowebs_ops_phase_move_invalid'));
            return $this->redirect_project($project_id);
        }

        $phases = $this->dw_project_phases_model->get_by_project($project_id);
        $index = null;

        foreach ($phases as $key => $phase) {
            if ((int) $phase['id'] === (int) $phase_id) {
                $index = $key;
                break;
            }
        }

        if ($index === null) {
            show_404();
        }

        $swap_index = $direction === 'up' ? $index - 1 : $index + 1;
        if (!isset($phases[$swap_index])) {
            set_alert('warning', _l('disowebs_ops_phase_move_limit'));
            return $this->redirect_project($project_id);
        }

        $current = $phases[$index];
        $target = $phases[$swap_index];

        $this->dw_project_phases_model->update($current['id'], ['position' => $target['position']]);
        $this->dw_project_phases_model->update($target['id'], ['position' => $current['position']]);

        set_alert('success', _l('disowebs_ops_phase_reordered'));
        $this->redirect_project($project_id);
    }

    public function set_phase_status($project_id, $phase_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_phase_manage_access();
        $this->require_post();

        $phase = $this->dw_project_phases_model->get($phase_id);
        if (!$phase || (int) $phase->project_id !== (int) $project_id) {
            show_404();
        }

        $status = $this->sanitize_phase_status($this->input->post('status', true));

        $data = ['status' => $status];

        if ($status === 'not_started') {
            $data['started_at'] = null;
            $data['completed_at'] = null;
        } elseif ($status === 'in_progress') {
            $data['started_at'] = $phase->started_at ?: date('Y-m-d H:i:s');
            $data['completed_at'] = null;
        } elseif ($status === 'done') {
            $data['started_at'] = $phase->started_at ?: date('Y-m-d H:i:s');
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        $this->dw_project_phases_model->update($phase_id, $data);

        set_alert('success', _l('disowebs_ops_phase_status_updated'));
        $this->redirect_project($project_id);
    }

    public function add_milestone($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_milestone_manage_access();
        $this->require_post();

        $title = trim($this->input->post('title', true));
        $description = trim($this->input->post('description', true));
        $phase_id = $this->input->post('phase_id', true);
        $status = $this->sanitize_milestone_status($this->input->post('status', true));

        $week_start = $this->normalize_date($this->input->post('week_start', true));
        $week_end = $this->normalize_date($this->input->post('week_end', true));

        if ($title === '') {
            set_alert('danger', _l('disowebs_ops_milestone_title_required'));
            return $this->redirect_project($project_id);
        }

        if (!$week_start || !$week_end || strtotime($week_end) < strtotime($week_start)) {
            set_alert('danger', _l('disowebs_ops_milestone_week_invalid'));
            return $this->redirect_project($project_id);
        }

        $phase_id = $this->validate_phase_id($project_id, $phase_id);
        if ($phase_id === null && trim((string) $this->input->post('phase_id', true)) !== '') {
            set_alert('danger', _l('disowebs_ops_phase_invalid'));
            return $this->redirect_project($project_id);
        }
        if ($phase_id === null && trim((string) $this->input->post('phase_id', true)) !== '') {
            set_alert('danger', _l('disowebs_ops_phase_invalid'));
            return $this->redirect_project($project_id);
        }

        $data = [
            'project_id' => (int) $project_id,
            'phase_id'   => $phase_id,
            'title'      => $title,
            'description'=> $description === '' ? null : $description,
            'week_start' => $week_start,
            'week_end'   => $week_end,
            'status'     => $status,
        ];

        if ($status === 'done') {
            $data['done_at'] = date('Y-m-d H:i:s');
        }

        $insert_id = $this->dw_project_milestones_model->add($data);

        if ($insert_id) {
            set_alert('success', _l('disowebs_ops_milestone_added'));
        } else {
            set_alert('danger', _l('disowebs_ops_milestone_add_failed'));
        }

        $this->redirect_project($project_id);
    }

    public function update_milestone($project_id, $milestone_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_milestone_manage_access();
        $this->require_post();

        $milestone = $this->dw_project_milestones_model->get($milestone_id);
        if (!$milestone || (int) $milestone->project_id !== (int) $project_id) {
            show_404();
        }

        $title = trim($this->input->post('title', true));
        $description = trim($this->input->post('description', true));
        $phase_id = $this->input->post('phase_id', true);
        $status = $this->sanitize_milestone_status($this->input->post('status', true));

        $week_start = $this->normalize_date($this->input->post('week_start', true));
        $week_end = $this->normalize_date($this->input->post('week_end', true));

        if ($title === '') {
            set_alert('danger', _l('disowebs_ops_milestone_title_required'));
            return $this->redirect_project($project_id);
        }

        if (!$week_start || !$week_end || strtotime($week_end) < strtotime($week_start)) {
            set_alert('danger', _l('disowebs_ops_milestone_week_invalid'));
            return $this->redirect_project($project_id);
        }

        $phase_id = $this->validate_phase_id($project_id, $phase_id);

        $data = [
            'phase_id'   => $phase_id,
            'title'      => $title,
            'description'=> $description === '' ? null : $description,
            'week_start' => $week_start,
            'week_end'   => $week_end,
            'status'     => $status,
        ];

        if ($status === 'done') {
            $data['done_at'] = $milestone->done_at ?: date('Y-m-d H:i:s');
        } else {
            $data['done_at'] = null;
        }

        $updated = $this->dw_project_milestones_model->update($milestone_id, $data);

        if ($updated) {
            set_alert('success', _l('disowebs_ops_milestone_updated'));
        } else {
            set_alert('warning', _l('disowebs_ops_milestone_update_no_change'));
        }

        $this->redirect_project($project_id);
    }

    public function delete_milestone($project_id, $milestone_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_milestone_delete_access();
        $this->require_post();

        $milestone = $this->dw_project_milestones_model->get($milestone_id);
        if (!$milestone || (int) $milestone->project_id !== (int) $project_id) {
            show_404();
        }

        $deleted = $this->dw_project_milestones_model->delete($milestone_id);

        if ($deleted) {
            set_alert('success', _l('disowebs_ops_milestone_deleted'));
        } else {
            set_alert('danger', _l('disowebs_ops_milestone_delete_failed'));
        }

        $this->redirect_project($project_id);
    }

    public function set_milestone_status($project_id, $milestone_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_milestone_status_access();
        $this->require_post();

        $milestone = $this->dw_project_milestones_model->get($milestone_id);
        if (!$milestone || (int) $milestone->project_id !== (int) $project_id) {
            show_404();
        }

        $status = $this->sanitize_milestone_status($this->input->post('status', true));

        $data = ['status' => $status];

        if ($status === 'done') {
            $data['done_at'] = $milestone->done_at ?: date('Y-m-d H:i:s');
        } else {
            $data['done_at'] = null;
        }

        $this->dw_project_milestones_model->update($milestone_id, $data);

        set_alert('success', _l('disowebs_ops_milestone_status_updated'));
        $this->redirect_project($project_id);
    }

    public function add_change_request($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_change_request_manage_access();
        $this->require_post();

        $title = trim($this->input->post('title', true));
        $description = trim($this->input->post('description', true));
        $impact_days = $this->normalize_impact_days($this->input->post('impact_days', true));
        $impact_cost = $this->normalize_impact_cost($this->input->post('impact_cost', true));

        if ($title === '') {
            set_alert('danger', _l('disowebs_ops_cr_title_required'));
            return $this->redirect_project_change_requests($project_id);
        }

        if ($description === '') {
            set_alert('danger', _l('disowebs_ops_cr_description_required'));
            return $this->redirect_project_change_requests($project_id);
        }

        if ($impact_days === null || $impact_cost === null) {
            set_alert('danger', _l('disowebs_ops_cr_impact_invalid'));
            return $this->redirect_project_change_requests($project_id);
        }

        $insert_id = $this->dw_change_requests_model->add([
            'project_id' => (int) $project_id,
            'title' => $title,
            'description' => $description,
            'impact_days' => $impact_days,
            'impact_cost' => $impact_cost,
            'status' => 'draft',
            'created_by' => disowebs_ops_get_staff_id(),
        ]);

        if ($insert_id) {
            set_alert('success', _l('disowebs_ops_cr_added'));
        } else {
            set_alert('danger', _l('disowebs_ops_cr_add_failed'));
        }

        $this->redirect_project_change_requests($project_id);
    }

    public function update_change_request($project_id, $change_request_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_change_request_manage_access();
        $this->require_post();

        $change_request = $this->get_change_request_or_404($project_id, $change_request_id);

        if (in_array($change_request->status, ['approved', 'implemented'], true)) {
            set_alert('warning', _l('disowebs_ops_cr_edit_locked'));
            return $this->redirect_project_change_requests($project_id);
        }

        $title = trim($this->input->post('title', true));
        $description = trim($this->input->post('description', true));
        $impact_days = $this->normalize_impact_days($this->input->post('impact_days', true));
        $impact_cost = $this->normalize_impact_cost($this->input->post('impact_cost', true));

        if ($title === '') {
            set_alert('danger', _l('disowebs_ops_cr_title_required'));
            return $this->redirect_project_change_requests($project_id);
        }

        if ($description === '') {
            set_alert('danger', _l('disowebs_ops_cr_description_required'));
            return $this->redirect_project_change_requests($project_id);
        }

        if ($impact_days === null || $impact_cost === null) {
            set_alert('danger', _l('disowebs_ops_cr_impact_invalid'));
            return $this->redirect_project_change_requests($project_id);
        }

        $updated = $this->dw_change_requests_model->update($change_request_id, [
            'title' => $title,
            'description' => $description,
            'impact_days' => $impact_days,
            'impact_cost' => $impact_cost,
        ]);

        if ($updated) {
            set_alert('success', _l('disowebs_ops_cr_updated'));
        } else {
            set_alert('warning', _l('disowebs_ops_cr_update_no_change'));
        }

        $this->redirect_project_change_requests($project_id);
    }

    public function delete_change_request($project_id, $change_request_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_change_request_delete_access();
        $this->require_post();

        $change_request = $this->get_change_request_or_404($project_id, $change_request_id);

        if (!in_array($change_request->status, ['draft', 'rejected'], true)) {
            set_alert('warning', _l('disowebs_ops_cr_delete_locked'));
            return $this->redirect_project_change_requests($project_id);
        }

        $deleted = $this->dw_change_requests_model->delete($change_request_id);

        if ($deleted) {
            set_alert('success', _l('disowebs_ops_cr_deleted'));
        } else {
            set_alert('danger', _l('disowebs_ops_cr_delete_failed'));
        }

        $this->redirect_project_change_requests($project_id);
    }

    public function submit_change_request($project_id, $change_request_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_change_request_manage_access();
        $this->require_post();

        $change_request = $this->get_change_request_or_404($project_id, $change_request_id);

        if (!in_array($change_request->status, ['draft', 'rejected'], true)) {
            set_alert('warning', _l('disowebs_ops_cr_submit_invalid'));
            return $this->redirect_project_change_requests($project_id);
        }

        $updated = $this->dw_change_requests_model->update($change_request_id, [
            'status' => 'submitted',
        ]);

        if ($updated) {
            set_alert('success', _l('disowebs_ops_cr_submitted'));
        } else {
            set_alert('warning', _l('disowebs_ops_cr_update_no_change'));
        }

        $this->redirect_project_change_requests($project_id);
    }

    public function approve_change_request($project_id, $change_request_id)
    {
        $this->ensure_project_access($project_id, false);
        $this->ensure_change_request_approval_access();
        $this->require_post();

        $change_request = $this->get_change_request_or_404($project_id, $change_request_id);

        if ($change_request->status !== 'submitted') {
            set_alert('warning', _l('disowebs_ops_cr_approve_invalid'));
            return $this->redirect_project_change_requests($project_id);
        }

        if ((int) $change_request->impact_days <= 0 || (float) $change_request->impact_cost <= 0.0) {
            set_alert('danger', _l('disowebs_ops_cr_impact_required'));
            return $this->redirect_project_change_requests($project_id);
        }

        $updated = $this->dw_change_requests_model->update($change_request_id, [
            'status' => 'approved',
            'approved_by' => disowebs_ops_get_staff_id(),
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        if ($updated) {
            set_alert('success', _l('disowebs_ops_cr_approved'));
        } else {
            set_alert('warning', _l('disowebs_ops_cr_update_no_change'));
        }

        $this->redirect_project_change_requests($project_id);
    }

    public function reject_change_request($project_id, $change_request_id)
    {
        $this->ensure_project_access($project_id, false);
        $this->ensure_change_request_approval_access();
        $this->require_post();

        $change_request = $this->get_change_request_or_404($project_id, $change_request_id);

        if ($change_request->status !== 'submitted') {
            set_alert('warning', _l('disowebs_ops_cr_reject_invalid'));
            return $this->redirect_project_change_requests($project_id);
        }

        $updated = $this->dw_change_requests_model->update($change_request_id, [
            'status' => 'rejected',
            'approved_by' => disowebs_ops_get_staff_id(),
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        if ($updated) {
            set_alert('success', _l('disowebs_ops_cr_rejected'));
        } else {
            set_alert('warning', _l('disowebs_ops_cr_update_no_change'));
        }

        $this->redirect_project_change_requests($project_id);
    }

    public function mark_change_request_implemented($project_id, $change_request_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_change_request_implemented_access();
        $this->require_post();

        $change_request = $this->get_change_request_or_404($project_id, $change_request_id);

        if ($change_request->status !== 'approved') {
            set_alert('warning', _l('disowebs_ops_cr_implement_invalid'));
            return $this->redirect_project_change_requests($project_id);
        }

        $updated = $this->dw_change_requests_model->update($change_request_id, [
            'status' => 'implemented',
        ]);

        if ($updated) {
            set_alert('success', _l('disowebs_ops_cr_implemented'));
        } else {
            set_alert('warning', _l('disowebs_ops_cr_update_no_change'));
        }

        $this->redirect_project_change_requests($project_id);
    }

    public function create_change_request_task($project_id, $change_request_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_change_request_task_access();
        $this->require_post();

        $change_request = $this->get_change_request_or_404($project_id, $change_request_id);

        if ($change_request->status !== 'approved') {
            set_alert('warning', _l('disowebs_ops_cr_task_invalid'));
            return $this->redirect_project_change_requests($project_id);
        }

        if ($this->change_request_task_exists($project_id, $change_request_id)) {
            set_alert('warning', _l('disowebs_ops_cr_task_exists'));
            return $this->redirect_project_change_requests($project_id);
        }

        $start_date = date('Y-m-d');
        $impact_days = max(0, (int) $change_request->impact_days);
        $due_date = $impact_days > 0 ? date('Y-m-d', strtotime('+' . $impact_days . ' days')) : $start_date;

        $task_data = [
            'name' => '[DW' . (int) $change_request_id . '] ' . $change_request->title,
            'description' => $change_request->description,
            'startdate' => $start_date,
            'duedate' => $due_date,
            'priority' => 2,
            'rel_type' => 'project',
            'rel_id' => (int) $project_id,
        ];

        $task_id = $this->tasks_model->add($task_data);

        if ($task_id) {
            set_alert('success', _l('disowebs_ops_cr_task_created'));
        } else {
            set_alert('danger', _l('disowebs_ops_cr_task_failed'));
        }

        $this->redirect_project_change_requests($project_id);
    }

    public function add_proof_entry($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_proof_manage_access();
        $this->require_post();

        $title = trim($this->input->post('title', true));
        $problem = trim($this->input->post('problem', true));
        $solution = trim($this->input->post('solution', true));
        $outcome = trim($this->input->post('outcome', true));

        if ($title === '') {
            set_alert('danger', _l('disowebs_ops_proof_title_required'));
            return $this->redirect_project_proof($project_id);
        }

        if ($problem === '' || $solution === '' || $outcome === '') {
            set_alert('danger', _l('disowebs_ops_proof_fields_required'));
            return $this->redirect_project_proof($project_id);
        }

        $insert_id = $this->dw_proof_entries_model->add([
            'project_id' => (int) $project_id,
            'title' => $title,
            'problem' => $problem,
            'solution' => $solution,
            'outcome' => $outcome,
            'created_by' => disowebs_ops_get_staff_id(),
        ]);

        if (!$insert_id) {
            set_alert('danger', _l('disowebs_ops_proof_add_failed'));
            return $this->redirect_project_proof($project_id);
        }

        $uploads = disowebs_ops_handle_proof_uploads($insert_id, 'proof_files');
        if (!empty($uploads['files'])) {
            foreach ($uploads['files'] as $file) {
                $this->dw_proof_files_model->add([
                    'proof_entry_id' => $insert_id,
                    'file_path' => $file['file_path'],
                    'file_type' => $file['file_type'],
                ]);
            }
        }
        if (!empty($uploads['errors'])) {
            set_alert('warning', implode(' ', array_unique($uploads['errors'])));
        }

        set_alert('success', _l('disowebs_ops_proof_added'));
        $this->redirect_project_proof($project_id);
    }

    public function update_proof_entry($project_id, $proof_entry_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_proof_manage_access();
        $this->require_post();

        $proof_entry = $this->get_proof_entry_or_404($project_id, $proof_entry_id);

        $title = trim($this->input->post('title', true));
        $problem = trim($this->input->post('problem', true));
        $solution = trim($this->input->post('solution', true));
        $outcome = trim($this->input->post('outcome', true));

        if ($title === '') {
            set_alert('danger', _l('disowebs_ops_proof_title_required'));
            return $this->redirect_project_proof($project_id);
        }

        if ($problem === '' || $solution === '' || $outcome === '') {
            set_alert('danger', _l('disowebs_ops_proof_fields_required'));
            return $this->redirect_project_proof($project_id);
        }

        $updated = $this->dw_proof_entries_model->update($proof_entry_id, [
            'title' => $title,
            'problem' => $problem,
            'solution' => $solution,
            'outcome' => $outcome,
        ]);

        $uploads = disowebs_ops_handle_proof_uploads($proof_entry_id, 'proof_files');
        if (!empty($uploads['files'])) {
            foreach ($uploads['files'] as $file) {
                $this->dw_proof_files_model->add([
                    'proof_entry_id' => $proof_entry_id,
                    'file_path' => $file['file_path'],
                    'file_type' => $file['file_type'],
                ]);
            }
        }
        if (!empty($uploads['errors'])) {
            set_alert('warning', implode(' ', array_unique($uploads['errors'])));
        }

        if ($updated) {
            set_alert('success', _l('disowebs_ops_proof_updated'));
        } else {
            set_alert('warning', _l('disowebs_ops_proof_update_no_change'));
        }

        $this->redirect_project_proof($project_id);
    }

    public function delete_proof_entry($project_id, $proof_entry_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_proof_delete_access();
        $this->require_post();

        $proof_entry = $this->get_proof_entry_or_404($project_id, $proof_entry_id);
        $files = $this->dw_proof_files_model->get_by_entry($proof_entry_id);

        foreach ($files as $file) {
            $this->remove_proof_file_from_disk($proof_entry_id, $file['file_path']);
            $this->dw_proof_files_model->delete($file['id']);
        }

        $deleted = $this->dw_proof_entries_model->delete($proof_entry_id);

        if ($deleted) {
            $path = disowebs_ops_proof_upload_path($proof_entry_id);
            if (is_dir($path)) {
                delete_dir($path);
            }
            set_alert('success', _l('disowebs_ops_proof_deleted'));
        } else {
            set_alert('danger', _l('disowebs_ops_proof_delete_failed'));
        }

        $this->redirect_project_proof($project_id);
    }

    public function delete_proof_file($project_id, $proof_entry_id, $file_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_proof_file_delete_access();
        $this->require_post();

        $this->get_proof_entry_or_404($project_id, $proof_entry_id);

        $file = $this->dw_proof_files_model->get($file_id);
        if (!$file || (int) $file->proof_entry_id !== (int) $proof_entry_id) {
            show_404();
        }

        $this->remove_proof_file_from_disk($proof_entry_id, $file->file_path);
        $deleted = $this->dw_proof_files_model->delete($file_id);

        if ($deleted) {
            set_alert('success', _l('disowebs_ops_proof_file_deleted'));
        } else {
            set_alert('danger', _l('disowebs_ops_proof_file_delete_failed'));
        }

        $this->redirect_project_proof($project_id);
    }

    public function export_case_study($project_id)
    {
        $this->ensure_project_access($project_id, false);

        $project = $this->projects_model->get($project_id);
        if (!$project) {
            show_404();
        }

        $this->load->model('clients_model');
        $client = null;
        if (!empty($project->clientid)) {
            $client = $this->clients_model->get($project->clientid);
        }

        $proof_entries = $this->dw_proof_entries_model->get_by_project($project_id);
        $proof_files = $this->dw_proof_files_model->get_by_project($project_id);

        $files_by_entry = [];
        foreach ($proof_files as $file) {
            $files_by_entry[$file['proof_entry_id']][] = $file;
        }

        $data = [
            'project' => $project,
            'client' => $client,
            'proof_entries' => $proof_entries,
            'files_by_entry' => $files_by_entry,
        ];

        $this->load->view('disowebs_ops/admin/exports/case_study', $data);
    }

    public function create_scope_snapshot($project_id)
    {
        $this->ensure_project_access($project_id, false);
        $this->ensure_scope_access();
        $this->require_post();

        $source_ref = $this->input->post('source_ref', true);
        $source_ref = trim((string) $source_ref);

        if ($source_ref === '' || strpos($source_ref, ':') === false) {
            set_alert('danger', _l('disowebs_ops_snapshot_source_required'));
            return $this->redirect_project_scope($project_id);
        }

        [$source_type, $source_id] = explode(':', $source_ref, 2);
        $source_type = trim($source_type);
        $source_id = trim($source_id);

        if (!in_array($source_type, ['estimate', 'proposal'], true) || !ctype_digit($source_id)) {
            set_alert('danger', _l('disowebs_ops_snapshot_source_invalid'));
            return $this->redirect_project_scope($project_id);
        }

        $source_id = (int) $source_id;
        $snapshot = $source_type === 'estimate'
            ? disowebs_ops_build_estimate_snapshot($source_id, $project_id)
            : disowebs_ops_build_proposal_snapshot($source_id, $project_id);

        if (!$snapshot) {
            set_alert('danger', _l('disowebs_ops_snapshot_source_invalid'));
            return $this->redirect_project_scope($project_id);
        }

        $insert_id = $this->dw_scope_snapshots_model->add([
            'project_id' => (int) $project_id,
            'source_type' => $source_type,
            'source_id' => $source_id,
            'snapshot_json' => json_encode($snapshot),
        ]);

        if ($insert_id) {
            set_alert('success', _l('disowebs_ops_snapshot_created'));
        } else {
            set_alert('danger', _l('disowebs_ops_snapshot_failed'));
        }

        $this->redirect_project_scope($project_id);
    }

    private function ensure_project_access($project_id, $manage = false)
    {
        if (!is_numeric($project_id)) {
            show_404();
        }

        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        if ($manage && !has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        if (!$this->projects_model->is_member($project_id) && !staff_can('view', 'projects')) {
            access_denied('projects');
        }
    }

    private function ensure_scope_access()
    {
        if (!disowebs_ops_can_approve_change_requests()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_change_request_approval_access()
    {
        if (!disowebs_ops_can_approve_change_requests()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_phase_manage_access()
    {
        if (!disowebs_ops_can_manage_phases()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_milestone_manage_access()
    {
        if (!disowebs_ops_can_manage_milestones()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_milestone_status_access()
    {
        if (!disowebs_ops_can_update_milestone_status()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_milestone_delete_access()
    {
        if (!disowebs_ops_can_delete_milestones()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_change_request_manage_access()
    {
        if (!disowebs_ops_can_manage_change_requests()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_change_request_delete_access()
    {
        if (!disowebs_ops_can_delete_change_requests()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_change_request_task_access()
    {
        if (!disowebs_ops_can_create_change_request_task()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_change_request_implemented_access()
    {
        if (!disowebs_ops_can_mark_change_request_implemented()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_proof_manage_access()
    {
        if (!disowebs_ops_can_manage_proof_entries()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_proof_delete_access()
    {
        if (!disowebs_ops_can_delete_proof_entries()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    private function ensure_proof_file_delete_access()
    {
        if (!disowebs_ops_can_delete_proof_files()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }
    }

    // =====================================================
    // PROJECT GATES & BLOCKERS (V2 Workflow)
    // =====================================================

    public function clear_deposit_gate($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_gates_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_project_gates_model');
        $success = $this->dw_project_gates_model->manual_clear_deposit($project_id, get_staff_user_id());

        if ($success) {
            set_alert('success', _l('disowebs_ops_deposit_gate_cleared'));
        } else {
            set_alert('danger', _l('disowebs_ops_gate_update_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    public function clear_final_gate($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_gates_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_project_gates_model');
        $success = $this->dw_project_gates_model->manual_clear_final_payment($project_id, get_staff_user_id());

        if ($success) {
            set_alert('success', _l('disowebs_ops_final_gate_cleared'));
        } else {
            set_alert('danger', _l('disowebs_ops_gate_update_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    public function release_handover($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_gates_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_project_gates_model');
        
        // Recalculate and enforce final payment gate before handover release
        $this->dw_project_gates_model->calculate_final_payment_status($project_id);
        if (!disowebs_ops_can_release_handover($project_id)) {
            set_alert('danger', _l('disowebs_ops_final_payment_required_for_handover'));
            log_activity('Handover Release Blocked - Final Payment Not Cleared [Project ID: ' . $project_id . ']');
            return $this->redirect_project_gates($project_id);
        }
        
        $success = $this->dw_project_gates_model->release_handover($project_id, get_staff_user_id());

        if ($success) {
            set_alert('success', _l('disowebs_ops_handover_released_success'));
        } else {
            set_alert('danger', _l('disowebs_ops_gate_update_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    public function mark_training_completed($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $notes = $this->input->post('training_notes', true);

        $this->load->model('disowebs_ops/dw_project_gates_model');
        $success = $this->dw_project_gates_model->mark_training_completed($project_id, get_staff_user_id(), $notes);

        if ($success) {
            set_alert('success', _l('disowebs_ops_training_completed_success'));
        } else {
            set_alert('danger', _l('disowebs_ops_gate_update_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    public function add_blocker($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $description = trim($this->input->post('description', true));
        if (empty($description)) {
            set_alert('danger', _l('disowebs_ops_blocker_description_required'));
            return $this->redirect_project_gates($project_id);
        }

        $this->load->model('disowebs_ops/dw_milestone_blockers_model');
        $data = [
            'project_id' => (int) $project_id,
            'milestone_id' => $this->input->post('milestone_id') ?: null,
            'description' => $description,
            'owner_staff_id' => $this->input->post('owner_staff_id') ?: null,
            'next_action' => $this->input->post('next_action', true),
            'next_action_date' => $this->normalize_date($this->input->post('next_action_date')),
            'reported_by' => get_staff_user_id()
        ];

        $id = $this->dw_milestone_blockers_model->add($data);

        if ($id) {
            set_alert('success', _l('disowebs_ops_blocker_added'));
        } else {
            set_alert('danger', _l('disowebs_ops_blocker_add_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    public function resolve_blocker($project_id, $blocker_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_milestone_blockers_model');
        $blocker = $this->dw_milestone_blockers_model->get($blocker_id);
        
        if (!$blocker || (int) $blocker['project_id'] !== (int) $project_id) {
            show_404();
        }

        $resolution = $this->input->post('resolution', true) ?: 'Resolved';
        $success = $this->dw_milestone_blockers_model->resolve($blocker_id, get_staff_user_id(), $resolution);

        if ($success) {
            set_alert('success', _l('disowebs_ops_blocker_resolved'));
        } else {
            set_alert('danger', _l('disowebs_ops_blocker_resolve_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    public function delete_blocker($project_id, $blocker_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_milestone_blockers_model');
        $blocker = $this->dw_milestone_blockers_model->get($blocker_id);
        
        if (!$blocker || (int) $blocker['project_id'] !== (int) $project_id) {
            show_404();
        }

        $success = $this->dw_milestone_blockers_model->delete($blocker_id);

        if ($success) {
            set_alert('success', _l('disowebs_ops_blocker_deleted'));
        } else {
            set_alert('danger', _l('disowebs_ops_blocker_delete_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    // =====================================================
    // WEEKLY DEMOS (V2 Delivery Engine)
    // =====================================================

    public function schedule_demo($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $demo_date = $this->input->post('demo_date');
        if (empty($demo_date)) {
            set_alert('danger', _l('disowebs_ops_demo_date_required'));
            return $this->redirect_project_gates($project_id);
        }

        $this->load->model('disowebs_ops/dw_weekly_demos_model');
        $demo_datetime = to_sql_date($demo_date, true);
        $milestone_id = $this->input->post('milestone_id') ?: null;
        
        $id = $this->dw_weekly_demos_model->schedule_demo($project_id, $demo_datetime, get_staff_user_id(), $milestone_id);

        if ($id) {
            set_alert('success', _l('disowebs_ops_demo_scheduled_success'));
        } else {
            set_alert('danger', _l('disowebs_ops_demo_schedule_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    public function complete_demo($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_weekly_demos_model');
        
        // Find demo for current week
        $week_range = disowebs_ops_get_current_week_range();
        $this->db->where('project_id', $project_id);
        $this->db->where('week_start', $week_range['week_start']);
        $demo = $this->db->get(db_prefix() . 'dw_weekly_demos')->row_array();

        if (!$demo) {
            set_alert('danger', _l('disowebs_ops_demo_not_found'));
            return $this->redirect_project_gates($project_id);
        }

        $attendees = $this->input->post('attendees', true);
        $notes = $this->input->post('notes', true);
        $feedback = $this->input->post('feedback', true);

        $success = $this->dw_weekly_demos_model->mark_completed($demo['id'], $attendees, $notes, $feedback);

        if ($success) {
            set_alert('success', _l('disowebs_ops_demo_completed_success'));
        } else {
            set_alert('danger', _l('disowebs_ops_demo_complete_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    // =====================================================
    // RETAINER OFFERS (V2 Retention Engine)
    // =====================================================

    public function create_retainer_offer($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_retainer_offers_model');
        $id = $this->dw_retainer_offers_model->create_offer($project_id, get_staff_user_id());

        if ($id) {
            set_alert('success', _l('disowebs_ops_retainer_offer_created'));
        } else {
            set_alert('warning', _l('disowebs_ops_retainer_offer_exists'));
        }

        $this->redirect_project_gates($project_id);
    }

    public function mark_retainer_offered($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_retainer_offers_model');
        
        $offer = $this->dw_retainer_offers_model->get_by_project($project_id);
        if (!$offer) {
            set_alert('danger', _l('disowebs_ops_retainer_offer_not_found'));
            return $this->redirect_project_gates($project_id);
        }

        $success = $this->dw_retainer_offers_model->mark_offered($offer['id']);

        if ($success) {
            set_alert('success', _l('disowebs_ops_retainer_offer_sent'));
        } else {
            set_alert('danger', _l('disowebs_ops_retainer_update_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    public function mark_retainer_accepted($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_retainer_offers_model');
        
        $offer = $this->dw_retainer_offers_model->get_by_project($project_id);
        if (!$offer) {
            set_alert('danger', _l('disowebs_ops_retainer_offer_not_found'));
            return $this->redirect_project_gates($project_id);
        }

        $package_type = $this->input->post('package_type', true) ?: null;
        $monthly_value = $this->input->post('monthly_value') ?: null;

        $success = $this->dw_retainer_offers_model->mark_accepted($offer['id'], $package_type, $monthly_value);

        if ($success) {
            set_alert('success', _l('disowebs_ops_retainer_accepted'));
        } else {
            set_alert('danger', _l('disowebs_ops_retainer_update_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    public function mark_retainer_declined($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_retainer_offers_model');
        
        $offer = $this->dw_retainer_offers_model->get_by_project($project_id);
        if (!$offer) {
            set_alert('danger', _l('disowebs_ops_retainer_offer_not_found'));
            return $this->redirect_project_gates($project_id);
        }

        $reason = $this->input->post('decline_reason', true);
        $success = $this->dw_retainer_offers_model->mark_declined($offer['id'], $reason);

        if ($success) {
            set_alert('success', _l('disowebs_ops_retainer_declined'));
        } else {
            set_alert('danger', _l('disowebs_ops_retainer_update_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    // =====================================================
    // TESTIMONIALS (V2 Growth Flywheel)
    // =====================================================

    public function request_testimonial($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $project = $this->projects_model->get($project_id);
        if (!$project || empty($project->clientid)) {
            set_alert('danger', _l('disowebs_ops_no_client_for_testimonial'));
            return $this->redirect_project_gates($project_id);
        }

        $this->load->model('disowebs_ops/dw_testimonials_model');
        $id = $this->dw_testimonials_model->request_testimonial($project_id, $project->clientid, get_staff_user_id());

        if ($id) {
            set_alert('success', _l('disowebs_ops_testimonial_requested'));
        } else {
            set_alert('warning', _l('disowebs_ops_testimonial_already_requested'));
        }

        $this->redirect_project_gates($project_id);
    }

    public function mark_testimonial_received($project_id, $testimonial_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_testimonials_model');
        $testimonial = $this->dw_testimonials_model->get($testimonial_id);
        
        if (!$testimonial || (int) $testimonial['project_id'] !== (int) $project_id) {
            show_404();
        }

        $content = $this->input->post('testimonial_content', true);
        $rating = (int) $this->input->post('rating');
        $success = $this->dw_testimonials_model->mark_received($testimonial_id, $content, $rating);

        if ($success) {
            set_alert('success', _l('disowebs_ops_testimonial_received_success'));
        } else {
            set_alert('danger', _l('disowebs_ops_testimonial_update_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    /**
     * Approve a testimonial for publishing
     */
    public function approve_testimonial($project_id, $testimonial_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_ceo_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_testimonials_model');
        $testimonial = $this->dw_testimonials_model->get($testimonial_id);
        
        if (!$testimonial || (int) $testimonial['project_id'] !== (int) $project_id) {
            show_404();
        }

        $notes = $this->input->post('approval_notes', true);
        $success = $this->dw_testimonials_model->approve($testimonial_id, get_staff_user_id(), $notes);

        if ($success) {
            set_alert('success', _l('disowebs_ops_testimonial_approved'));
        } else {
            set_alert('danger', _l('disowebs_ops_testimonial_update_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    /**
     * Reject a testimonial
     */
    public function reject_testimonial($project_id, $testimonial_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_ceo_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_testimonials_model');
        $testimonial = $this->dw_testimonials_model->get($testimonial_id);
        
        if (!$testimonial || (int) $testimonial['project_id'] !== (int) $project_id) {
            show_404();
        }

        $notes = $this->input->post('rejection_notes', true);
        $success = $this->dw_testimonials_model->reject($testimonial_id, get_staff_user_id(), $notes);

        if ($success) {
            set_alert('success', _l('disowebs_ops_testimonial_rejected'));
        } else {
            set_alert('danger', _l('disowebs_ops_testimonial_update_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    // =====================================================
    // PROJECT PROFIT TRACKING (V2 Profit Engine)
    // =====================================================

    public function update_profit_settings($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_profit_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_project_profit_model');
        
        $expected_revenue = $this->normalize_impact_cost($this->input->post('expected_revenue'));
        $expected_cost = $this->normalize_impact_cost($this->input->post('expected_cost'));
        $effort_hours = floatval($this->input->post('effort_hours'));

        $success = $this->dw_project_profit_model->set_expected_values($project_id, $expected_revenue, $expected_cost, $effort_hours);

        if ($success) {
            set_alert('success', _l('disowebs_ops_profit_settings_updated'));
        } else {
            set_alert('danger', _l('disowebs_ops_profit_update_failed'));
        }

        $this->redirect_project_profit($project_id);
    }

    public function add_cost_entry($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $description = $this->input->post('cost_description', true);
        $amount = $this->normalize_impact_cost($this->input->post('cost_amount'));
        $type = $this->input->post('cost_type', true);

        if (empty($description) || $amount === null || $amount <= 0) {
            set_alert('danger', _l('disowebs_ops_cost_entry_invalid'));
            return $this->redirect_project_profit($project_id);
        }

        // Create expense record in expenses table
        $this->load->model('expenses_model');
        
        $expense_data = [
            'project_id' => $project_id,
            'category' => 1, // Default category, could be made configurable
            'amount' => $amount,
            'expense_name' => $description,
            'note' => 'Added via Disowebs Ops - ' . $type,
            'date' => date('Y-m-d'),
            'addedfrom' => get_staff_user_id(),
            'billable' => 0
        ];

        $expense_id = $this->expenses_model->add($expense_data);

        if ($expense_id) {
            // Recalculate project profit
            $this->load->model('disowebs_ops/dw_project_profit_model');
            $this->dw_project_profit_model->recalculate($project_id);
            
            set_alert('success', _l('disowebs_ops_cost_entry_added'));
        } else {
            set_alert('danger', _l('disowebs_ops_cost_entry_failed'));
        }

        $this->redirect_project_profit($project_id);
    }

    public function acknowledge_alert($project_id, $alert_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_ceo_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_margin_alerts_model');
        $alert = $this->dw_margin_alerts_model->get($alert_id);
        
        if (!$alert || (int) $alert['project_id'] !== (int) $project_id) {
            show_404();
        }

        $success = $this->dw_margin_alerts_model->acknowledge($alert_id, get_staff_user_id());

        if ($success) {
            set_alert('success', _l('disowebs_ops_alert_acknowledged_success'));
        } else {
            set_alert('danger', _l('disowebs_ops_alert_acknowledge_failed'));
        }

        $this->redirect_project_profit($project_id);
    }

    // =====================================================
    // ADOPTION BASELINE
    // =====================================================

    /**
     * Capture initial adoption baseline for a project
     * Called when project launches to establish metrics baseline
     */
    public function capture_adoption_baseline($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_adoption_baseline_model');

        // Check if baseline already exists
        $existing = $this->dw_adoption_baseline_model->get_by_project($project_id);
        if ($existing) {
            set_alert('warning', _l('disowebs_ops_baseline_already_exists'));
            $this->redirect_project_gates($project_id);
            return;
        }

        // Get project client
        $project = $this->projects_model->get($project_id);
        if (!$project || empty($project->clientid)) {
            set_alert('danger', _l('disowebs_ops_project_no_client'));
            $this->redirect_project_gates($project_id);
            return;
        }

        // Capture baseline with initial values
        $data = [
            'logins_count'       => $this->input->post('logins_count') ? (int) $this->input->post('logins_count') : 0,
            'training_sessions'  => $this->input->post('training_sessions') ? (int) $this->input->post('training_sessions') : 0,
            'support_tickets'    => $this->input->post('support_tickets') ? (int) $this->input->post('support_tickets') : 0,
        ];

        $result = $this->dw_adoption_baseline_model->capture_baseline($project_id, $project->clientid, $data);

        if ($result) {
            set_alert('success', _l('disowebs_ops_baseline_captured'));
            log_activity('Adoption Baseline Captured [Project ID: ' . $project_id . ']');
        } else {
            set_alert('danger', _l('disowebs_ops_baseline_capture_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    /**
     * Update adoption baseline metrics
     * Called periodically to track client adoption progress
     */
    public function update_adoption_baseline($project_id)
    {
        $this->ensure_project_access($project_id, true);
        $this->ensure_manage_access();
        $this->require_post();

        $this->load->model('disowebs_ops/dw_adoption_baseline_model');

        // Verify baseline exists
        $baseline = $this->dw_adoption_baseline_model->get_by_project($project_id);
        if (!$baseline) {
            set_alert('danger', _l('disowebs_ops_no_adoption_baseline'));
            $this->redirect_project_gates($project_id);
            return;
        }

        // Update metrics
        $data = [
            'logins_count'       => max(0, (int) $this->input->post('logins_count')),
            'training_sessions'  => max(0, (int) $this->input->post('training_sessions')),
            'support_tickets'    => max(0, (int) $this->input->post('support_tickets')),
        ];

        $result = $this->dw_adoption_baseline_model->update_metrics($baseline['id'], $data);

        if ($result) {
            set_alert('success', _l('disowebs_ops_baseline_updated'));
            log_activity('Adoption Baseline Updated [Project ID: ' . $project_id . ', Baseline ID: ' . $baseline['id'] . ']');
        } else {
            set_alert('danger', _l('disowebs_ops_baseline_update_failed'));
        }

        $this->redirect_project_gates($project_id);
    }

    /**
     * Get adoption baseline data via AJAX
     */
    public function get_adoption_baseline($project_id)
    {
        $this->ensure_project_access($project_id);

        $this->load->model('disowebs_ops/dw_adoption_baseline_model');
        $baseline = $this->dw_adoption_baseline_model->get_by_project($project_id);

        if (!$baseline) {
            echo json_encode(['success' => false, 'message' => _l('disowebs_ops_no_adoption_baseline')]);
            return;
        }

        echo json_encode([
            'success'  => true,
            'baseline' => $baseline,
        ]);
    }

    // =====================================================
    // HELPER ACCESS CHECKS
    // =====================================================

    private function ensure_ceo_access()
    {
        if (!disowebs_ops_is_ceo()) {
            set_alert('danger', _l('disowebs_ops_ceo_only_action'));
            redirect(admin_url());
        }
    }

    private function ensure_gates_access()
    {
        if (!disowebs_ops_can_manage_gates()) {
            set_alert('danger', _l('disowebs_ops_no_permission'));
            redirect(admin_url());
        }
    }

    private function ensure_profit_access()
    {
        if (!disowebs_ops_can_view_profit()) {
            set_alert('danger', _l('disowebs_ops_no_permission'));
            redirect(admin_url());
        }
    }

    private function ensure_manage_access()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage')) {
            set_alert('danger', _l('disowebs_ops_no_permission'));
            redirect(admin_url());
        }
    }

    private function redirect_project_gates($project_id)
    {
        redirect(admin_url('projects/view/' . $project_id . '?group=disowebs_ops_gates'));
    }

    private function redirect_project_profit($project_id)
    {
        redirect(admin_url('projects/view/' . $project_id . '?group=disowebs_ops_profit'));
    }

    private function require_post()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : '';
        if ($method !== 'POST') {
            show_404();
        }
    }

    private function sanitize_phase_status($status)
    {
        $allowed = ['not_started', 'in_progress', 'done'];
        return in_array($status, $allowed, true) ? $status : 'not_started';
    }

    private function sanitize_milestone_status($status)
    {
        $allowed = ['planned', 'in_progress', 'done'];
        return in_array($status, $allowed, true) ? $status : 'planned';
    }

    private function sanitize_change_request_status($status)
    {
        $allowed = ['draft', 'submitted', 'approved', 'rejected', 'implemented'];
        return in_array($status, $allowed, true) ? $status : 'draft';
    }

    private function normalize_impact_days($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return 0;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return max(0, (int) round($value));
    }

    private function normalize_impact_cost($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return 0.0;
        }

        $value = str_replace(',', '', $value);
        if (!is_numeric($value)) {
            return null;
        }

        return max(0, round((float) $value, 2));
    }

    private function normalize_date($date)
    {
        $date = trim((string) $date);
        if ($date === '') {
            return null;
        }

        return to_sql_date($date);
    }

    private function validate_phase_id($project_id, $phase_id)
    {
        if ($phase_id === null || $phase_id === '' || !is_numeric($phase_id)) {
            return null;
        }

        $phase = $this->dw_project_phases_model->get($phase_id);
        if (!$phase || (int) $phase->project_id !== (int) $project_id) {
            return null;
        }

        return (int) $phase_id;
    }

    private function get_change_request_or_404($project_id, $change_request_id)
    {
        $change_request = $this->dw_change_requests_model->get($change_request_id);
        if (!$change_request || (int) $change_request->project_id !== (int) $project_id) {
            show_404();
        }

        return $change_request;
    }

    private function change_request_task_exists($project_id, $change_request_id)
    {
        $this->db->where('rel_type', 'project');
        $this->db->where('rel_id', $project_id);
        $this->db->like('name', '[DW' . (int) $change_request_id . ']', 'after');
        $this->db->limit(1);
        return (bool) $this->db->get(db_prefix() . 'tasks')->row_array();
    }

    private function get_proof_entry_or_404($project_id, $proof_entry_id)
    {
        $proof_entry = $this->dw_proof_entries_model->get($proof_entry_id);
        if (!$proof_entry || (int) $proof_entry->project_id !== (int) $project_id) {
            show_404();
        }

        return $proof_entry;
    }

    private function remove_proof_file_from_disk($proof_entry_id, $file_path)
    {
        $file_path = basename((string) $file_path);
        if ($file_path === '') {
            return;
        }

        $path = disowebs_ops_proof_upload_path($proof_entry_id);
        $full_path = $path . $file_path;
        if (file_exists($full_path)) {
            unlink($full_path);
        }
    }

    private function redirect_project($project_id)
    {
        redirect(admin_url('projects/view/' . $project_id . '?group=disowebs_ops_phases'));
    }

    private function redirect_project_scope($project_id)
    {
        redirect(admin_url('projects/view/' . $project_id . '?group=disowebs_ops_scope'));
    }

    private function redirect_project_change_requests($project_id)
    {
        redirect(admin_url('projects/view/' . $project_id . '?group=disowebs_ops_change_requests'));
    }

    private function redirect_project_proof($project_id)
    {
        redirect(admin_url('projects/view/' . $project_id . '?group=disowebs_ops_proof'));
    }

}
