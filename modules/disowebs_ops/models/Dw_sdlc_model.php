<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SDLC Tools Model
 * 
 * Comprehensive model for Software Development Lifecycle tools:
 * - Requirements Gathering
 * - Scope Management
 * - Change Requests
 * - User Stories
 * - Time Estimation
 * - Technical Specs
 * - Dev Notes / Knowledge Base
 */
class Dw_sdlc_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // =====================================================
    // REQUIREMENTS MANAGEMENT
    // =====================================================

    /**
     * Get requirements for a project
     */
    public function get_requirements($project_id, $filters = [])
    {
        $this->db->select([
            'r.*',
            'CONCAT(s.firstname, " ", s.lastname) as assigned_name',
            'CONCAT(c.firstname, " ", c.lastname) as created_by_name',
        ]);
        $this->db->from(db_prefix() . 'dw_project_requirements r');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = r.assigned_to', 'left');
        $this->db->join(db_prefix() . 'staff c', 'c.staffid = r.created_by', 'left');
        $this->db->where('r.project_id', $project_id);

        if (!empty($filters['status'])) {
            $this->db->where('r.status', $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $this->db->where('r.priority', $filters['priority']);
        }
        if (!empty($filters['category'])) {
            $this->db->where('r.category', $filters['category']);
        }

        $this->db->order_by('r.priority', 'ASC');
        $this->db->order_by('r.sort_order', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get single requirement
     */
    public function get_requirement($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'dw_project_requirements')->row();
    }

    /**
     * Add requirement
     */
    public function add_requirement($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix() . 'dw_project_requirements', $data);
        $id = $this->db->insert_id();

        if ($id) {
            log_activity('Requirement Added [ID: ' . $id . ', Project: ' . $data['project_id'] . ']');
            return $id;
        }
        return false;
    }

    /**
     * Update requirement
     */
    public function update_requirement($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_project_requirements', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Requirement Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Get all requirements (optionally filtered by project)
     */
    public function get_all_requirements($project_id = null, $filters = [])
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_project_requirements')) {
            return [];
        }

        $this->db->select([
            'r.*',
            'p.name as project_name',
            'CONCAT(s.firstname, " ", s.lastname) as assigned_name',
            'CONCAT(c.firstname, " ", c.lastname) as created_by_name',
        ]);
        $this->db->from(db_prefix() . 'dw_project_requirements r');
        $this->db->join(db_prefix() . 'projects p', 'p.id = r.project_id', 'left');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = r.assigned_to', 'left');
        $this->db->join(db_prefix() . 'staff c', 'c.staffid = r.created_by', 'left');

        if ($project_id) {
            $this->db->where('r.project_id', $project_id);
        }

        if (!empty($filters['status'])) {
            $this->db->where('r.status', $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $this->db->where('r.priority', $filters['priority']);
        }
        if (!empty($filters['category']) || !empty($filters['type'])) {
            $this->db->where('r.requirement_type', $filters['category'] ?? $filters['type']);
        }

        $this->db->order_by('r.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get requirements stats
     */
    public function get_requirements_stats($project_id = null)
    {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'draft' => 0,
            'approved' => 0,
            'in_progress' => 0,
            'in_development' => 0,
            'completed' => 0,
            'rejected' => 0,
            'critical' => 0,
        ];

        if (!$this->db->table_exists(db_prefix() . 'dw_project_requirements')) {
            return $stats;
        }

        $this->db->select('status, priority, COUNT(*) as count');
        $this->db->from(db_prefix() . 'dw_project_requirements');
        if ($project_id) {
            $this->db->where('project_id', $project_id);
        }
        $this->db->group_by('status, priority');
        $results = $this->db->get()->result();

        foreach ($results as $row) {
            $stats['total'] += $row->count;
            if (isset($stats[$row->status])) {
                $stats[$row->status] += $row->count;
            }
            if ($row->priority === 'critical') {
                $stats['critical'] += $row->count;
            }
        }

        return $stats;
    }

    /**
     * Delete requirement
     */
    public function delete_requirement($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dw_project_requirements');

        if ($this->db->affected_rows() > 0) {
            log_activity('Requirement Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Approve requirement (client or internal)
     */
    public function approve_requirement($id, $type = 'client')
    {
        $update = [];
        if ($type === 'client') {
            $update['client_approved'] = 1;
            $update['client_approved_date'] = date('Y-m-d H:i:s');
        }
        $update['status'] = 'approved';
        $update['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_project_requirements', $update);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Convert requirement to task
     */
    public function requirement_to_task($requirement_id, $additional_data = [])
    {
        $req = $this->get_requirement($requirement_id);
        if (!$req) return false;

        $task_data = [
            'name' => $req->title,
            'description' => $req->description . "\n\n**Acceptance Criteria:**\n" . $req->acceptance_criteria,
            'rel_type' => 'project',
            'rel_id' => $req->project_id,
            'priority' => $this->map_priority_to_task($req->priority),
            'startdate' => date('Y-m-d'),
        ];

        if ($req->estimated_hours) {
            // You could set billable or other fields here
        }

        $task_data = array_merge($task_data, $additional_data);

        $this->load->model('tasks_model');
        $task_id = $this->tasks_model->add($task_data);

        if ($task_id) {
            // Update requirement status
            $this->update_requirement($requirement_id, [
                'status' => 'in_progress',
            ]);
            return $task_id;
        }
        return false;
    }

    private function map_priority_to_task($priority)
    {
        $map = [
            'critical' => 1,
            'high' => 2,
            'medium' => 3,
            'low' => 4,
        ];
        return $map[$priority] ?? 3;
    }

    /**
     * Get requirement categories
     */
    public function get_requirement_categories()
    {
        return [
            'functional' => 'Functional',
            'non_functional' => 'Non-Functional',
            'technical' => 'Technical',
            'ui_ux' => 'UI/UX',
            'integration' => 'Integration',
            'security' => 'Security',
            'performance' => 'Performance',
            'data' => 'Data/Content',
        ];
    }

    // =====================================================
    // SCOPE MANAGEMENT
    // =====================================================

    /**
     * Get all scope documents (optionally filtered by project)
     */
    public function get_all_scopes($project_id = null)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_scope_documents')) {
            return [];
        }

        $this->db->select([
            's.*',
            'p.name as project_name',
            'cl.company as client_name',
            'CONCAT(c.firstname, " ", c.lastname) as created_by_name',
            'CONCAT(a.firstname, " ", a.lastname) as approved_by_name',
            '(SELECT COUNT(*) FROM ' . db_prefix() . 'dw_scope_items WHERE scope_id = s.id) as item_count',
        ]);
        $this->db->from(db_prefix() . 'dw_scope_documents s');
        $this->db->join(db_prefix() . 'projects p', 'p.id = s.project_id', 'left');
        $this->db->join(db_prefix() . 'clients cl', 'cl.userid = p.clientid', 'left');
        $this->db->join(db_prefix() . 'staff c', 'c.staffid = s.created_by', 'left');
        $this->db->join(db_prefix() . 'staff a', 'a.staffid = s.internal_approved_by', 'left');

        if ($project_id) {
            $this->db->where('s.project_id', $project_id);
        }

        $this->db->order_by('s.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get scope documents for project
     */
    public function get_scope_documents($project_id)
    {
        $this->db->select([
            's.*',
            'CONCAT(c.firstname, " ", c.lastname) as created_by_name',
            'CONCAT(a.firstname, " ", a.lastname) as approved_by_name',
        ]);
        $this->db->from(db_prefix() . 'dw_scope_documents s');
        $this->db->join(db_prefix() . 'staff c', 'c.staffid = s.created_by', 'left');
        $this->db->join(db_prefix() . 'staff a', 'a.staffid = s.internal_approved_by', 'left');
        $this->db->where('s.project_id', $project_id);
        $this->db->order_by('s.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get single scope document
     */
    public function get_scope($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'dw_scope_documents')->row();
    }

    /**
     * Get scope with all items
     */
    public function get_scope_full($id)
    {
        $scope = $this->get_scope($id);
        if (!$scope) return null;

        $scope->items = $this->get_scope_items($id);
        $scope->total_hours = $this->calculate_scope_hours($id);
        $scope->total_cost = $this->calculate_scope_cost($id);

        return $scope;
    }

    /**
     * Create scope document
     */
    public function add_scope($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['version'] = '1.0';

        $this->db->insert(db_prefix() . 'dw_scope_documents', $data);
        $id = $this->db->insert_id();

        if ($id) {
            // Generate hash for client signing
            $hash = hash('sha256', $id . time() . uniqid());
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'dw_scope_documents', ['hash' => $hash]);

            log_activity('Scope Document Created [ID: ' . $id . ']');
            return $id;
        }
        return false;
    }

    /**
     * Update scope document
     */
    public function update_scope($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_scope_documents', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Scope Document Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Create new version of scope document
     */
    public function create_scope_version($id)
    {
        $scope = $this->get_scope($id);
        if (!$scope) return false;

        // Mark old as superseded
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_scope_documents', ['status' => 'superseded']);

        // Calculate new version
        $version_parts = explode('.', $scope->version);
        $new_version = $version_parts[0] . '.' . (intval($version_parts[1] ?? 0) + 1);

        // Create new scope
        $new_data = [
            'project_id' => $scope->project_id,
            'version' => $new_version,
            'title' => $scope->title,
            'overview' => $scope->overview,
            'objectives' => $scope->objectives,
            'deliverables' => $scope->deliverables,
            'out_of_scope' => $scope->out_of_scope,
            'assumptions' => $scope->assumptions,
            'constraints' => $scope->constraints,
            'acceptance_criteria' => $scope->acceptance_criteria,
            'timeline_summary' => $scope->timeline_summary,
            'budget_summary' => $scope->budget_summary,
            'risks' => $scope->risks,
            'status' => 'draft',
        ];

        $new_id = $this->add_scope($new_data);

        // Copy scope items
        if ($new_id) {
            $items = $this->get_scope_items($id);
            foreach ($items as $item) {
                $this->add_scope_item([
                    'scope_id' => $new_id,
                    'parent_id' => null, // Would need mapping for nested items
                    'type' => $item->type,
                    'title' => $item->title,
                    'description' => $item->description,
                    'estimated_hours' => $item->estimated_hours,
                    'estimated_cost' => $item->estimated_cost,
                    'included' => $item->included,
                    'sort_order' => $item->sort_order,
                ]);
            }
        }

        return $new_id;
    }

    /**
     * Approve scope document
     */
    public function approve_scope($id)
    {
        $update = [
            'status' => 'approved',
            'internal_approved_by' => get_staff_user_id(),
            'internal_approved_date' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_scope_documents', $update);

        log_activity('Scope Document Approved [ID: ' . $id . ']');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Client sign scope (via public hash)
     */
    public function client_sign_scope($hash, $client_name)
    {
        $this->db->where('hash', $hash);
        $scope = $this->db->get(db_prefix() . 'dw_scope_documents')->row();

        if (!$scope) return false;

        $update = [
            'client_signed' => 1,
            'client_signed_date' => date('Y-m-d H:i:s'),
            'client_signed_by' => $client_name,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('id', $scope->id);
        $this->db->update(db_prefix() . 'dw_scope_documents', $update);

        log_activity('Scope Signed by Client [ID: ' . $scope->id . ', Client: ' . $client_name . ']');
        return $this->db->affected_rows() > 0;
    }

    // =====================================================
    // SCOPE ITEMS
    // =====================================================

    /**
     * Get scope items
     */
    public function get_scope_items($scope_id, $parent_id = null)
    {
        $this->db->where('scope_id', $scope_id);
        if ($parent_id === null) {
            $this->db->where('parent_id IS NULL');
        } else {
            $this->db->where('parent_id', $parent_id);
        }
        $this->db->order_by('sort_order', 'ASC');
        $items = $this->db->get(db_prefix() . 'dw_scope_items')->result();

        // Get children recursively
        foreach ($items as &$item) {
            $item->children = $this->get_scope_items($scope_id, $item->id);
        }

        return $items;
    }

    /**
     * Add scope item
     */
    public function add_scope_item($data)
    {
        $this->db->insert(db_prefix() . 'dw_scope_items', $data);
        return $this->db->insert_id();
    }

    /**
     * Update scope item
     */
    public function update_scope_item($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_scope_items', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete scope item
     */
    public function delete_scope_item($id)
    {
        // Delete children first
        $this->db->where('parent_id', $id);
        $this->db->delete(db_prefix() . 'dw_scope_items');

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dw_scope_items');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Calculate total scope hours
     */
    public function calculate_scope_hours($scope_id)
    {
        $this->db->select_sum('estimated_hours');
        $this->db->where('scope_id', $scope_id);
        $this->db->where('included', 1);
        $result = $this->db->get(db_prefix() . 'dw_scope_items')->row();
        return $result->estimated_hours ?? 0;
    }

    /**
     * Calculate total scope cost
     */
    public function calculate_scope_cost($scope_id)
    {
        $this->db->select_sum('estimated_cost');
        $this->db->where('scope_id', $scope_id);
        $this->db->where('included', 1);
        $result = $this->db->get(db_prefix() . 'dw_scope_items')->row();
        return $result->estimated_cost ?? 0;
    }

    /**
     * Generate scope from requirements
     */
    public function generate_scope_from_requirements($project_id, $scope_id)
    {
        $requirements = $this->get_requirements($project_id, ['status' => 'approved']);

        $sort = 0;
        foreach ($requirements as $req) {
            $this->add_scope_item([
                'scope_id' => $scope_id,
                'type' => 'deliverable',
                'title' => $req->title,
                'description' => $req->description,
                'estimated_hours' => $req->estimated_hours,
                'included' => 1,
                'sort_order' => $sort++,
            ]);
        }

        return $sort;
    }

    // =====================================================
    // CHANGE REQUESTS
    // =====================================================

    /**
     * Get change request stats
     */
    public function get_change_request_stats($project_id = null)
    {
        $stats = [
            'pending' => 0,
            'under_review' => 0,
            'approved' => 0,
            'rejected' => 0,
            'implemented' => 0,
        ];

        $this->db->select('status, COUNT(*) as count');
        $this->db->from(db_prefix() . 'dw_change_requests');
        if ($project_id) {
            $this->db->where('project_id', $project_id);
        }
        $this->db->group_by('status');
        $results = $this->db->get()->result();

        foreach ($results as $row) {
            if (isset($stats[$row->status])) {
                $stats[$row->status] = $row->count;
            }
        }

        return $stats;
    }

    /**
     * Get change requests for project
     */
    public function get_change_requests($project_id, $status = null)
    {
        $this->db->select([
            'cr.*',
            'CONCAT(r.firstname, " ", r.lastname) as reviewed_by_name',
            'CONCAT(a.firstname, " ", a.lastname) as approved_by_name',
        ]);
        $this->db->from(db_prefix() . 'dw_change_requests cr');
        $this->db->join(db_prefix() . 'staff r', 'r.staffid = cr.reviewed_by', 'left');
        $this->db->join(db_prefix() . 'staff a', 'a.staffid = cr.approved_by', 'left');
        $this->db->where('cr.project_id', $project_id);

        if ($status) {
            $this->db->where('cr.status', $status);
        }

        $this->db->order_by('cr.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get single change request
     */
    public function get_change_request($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'dw_change_requests')->row();
    }

    /**
     * Add change request
     */
    public function add_change_request($data)
    {
        // Generate CR number
        $data['cr_number'] = $this->generate_cr_number($data['project_id']);
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['requested_date'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix() . 'dw_change_requests', $data);
        $id = $this->db->insert_id();

        if ($id) {
            log_activity('Change Request Created [' . $data['cr_number'] . ']');
            return $id;
        }
        return false;
    }

    /**
     * Generate CR number
     */
    private function generate_cr_number($project_id)
    {
        $this->db->where('project_id', $project_id);
        $count = $this->db->count_all_results(db_prefix() . 'dw_change_requests');
        return 'CR-' . $project_id . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Update change request
     */
    public function update_change_request($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_change_requests', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Change Request Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Approve change request
     */
    public function approve_change_request($id, $notes = '')
    {
        $update = [
            'status' => 'approved',
            'approved_by' => get_staff_user_id(),
            'approved_date' => date('Y-m-d H:i:s'),
            'notes' => $notes,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_change_requests', $update);

        log_activity('Change Request Approved [ID: ' . $id . ']');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Reject change request
     */
    public function reject_change_request($id, $reason = '')
    {
        $update = [
            'status' => 'rejected',
            'reviewed_by' => get_staff_user_id(),
            'reviewed_date' => date('Y-m-d H:i:s'),
            'notes' => $reason,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_change_requests', $update);

        log_activity('Change Request Rejected [ID: ' . $id . ']');
        return $this->db->affected_rows() > 0;
    }

    // =====================================================
    // USER STORIES
    // =====================================================

    /**
     * Get user stories for project
     */
    public function get_user_stories($project_id, $filters = [])
    {
        $this->db->select([
            'us.*',
            'CONCAT(s.firstname, " ", s.lastname) as assigned_name',
            'r.title as requirement_title',
        ]);
        $this->db->from(db_prefix() . 'dw_user_stories us');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = us.assigned_to', 'left');
        $this->db->join(db_prefix() . 'dw_project_requirements r', 'r.id = us.requirement_id', 'left');
        $this->db->where('us.project_id', $project_id);

        if (!empty($filters['status'])) {
            $this->db->where('us.status', $filters['status']);
        }
        if (!empty($filters['sprint'])) {
            $this->db->where('us.sprint', $filters['sprint']);
        }
        if (!empty($filters['epic'])) {
            $this->db->where('us.epic', $filters['epic']);
        }

        $this->db->order_by('us.priority', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get single user story
     */
    public function get_user_story($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'dw_user_stories')->row();
    }

    /**
     * Add user story
     */
    public function add_user_story($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix() . 'dw_user_stories', $data);
        $id = $this->db->insert_id();

        if ($id) {
            log_activity('User Story Created [ID: ' . $id . ']');
            return $id;
        }
        return false;
    }

    /**
     * Update user story
     */
    public function update_user_story($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_user_stories', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('User Story Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete user story
     */
    public function delete_user_story($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dw_user_stories');

        if ($this->db->affected_rows() > 0) {
            log_activity('User Story Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Convert user story to task
     */
    public function story_to_task($story_id)
    {
        $story = $this->get_user_story($story_id);
        if (!$story) return false;

        $description = "**As a** " . $story->as_a . "\n";
        $description .= "**I want** " . $story->i_want . "\n";
        $description .= "**So that** " . $story->so_that . "\n\n";
        $description .= "**Acceptance Criteria:**\n" . $story->acceptance_criteria;

        $task_data = [
            'name' => 'User Story: ' . substr($story->i_want, 0, 100),
            'description' => $description,
            'rel_type' => 'project',
            'rel_id' => $story->project_id,
            'priority' => $this->map_priority_to_task($story->priority),
            'startdate' => date('Y-m-d'),
        ];

        if ($story->assigned_to) {
            $task_data['assignees'] = [$story->assigned_to];
        }

        $this->load->model('tasks_model');
        $task_id = $this->tasks_model->add($task_data);

        if ($task_id) {
            $this->update_user_story($story_id, [
                'task_id' => $task_id,
                'status' => 'in_progress',
            ]);
            return $task_id;
        }
        return false;
    }

    /**
     * Get story epics for project
     */
    public function get_epics($project_id)
    {
        $this->db->distinct();
        $this->db->select('epic');
        $this->db->where('project_id', $project_id);
        $this->db->where('epic IS NOT NULL');
        $this->db->where('epic !=', '');
        return $this->db->get(db_prefix() . 'dw_user_stories')->result();
    }

    /**
     * Get story sprints for project
     */
    public function get_sprints($project_id)
    {
        $this->db->distinct();
        $this->db->select('sprint');
        $this->db->where('project_id', $project_id);
        $this->db->where('sprint IS NOT NULL');
        $this->db->where('sprint !=', '');
        return $this->db->get(db_prefix() . 'dw_user_stories')->result();
    }

    // =====================================================
    // TIME ESTIMATES
    // =====================================================

    /**
     * Get all estimates (optionally filtered by project)
     */
    public function get_all_estimates($project_id = null)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_time_estimates')) {
            return [];
        }

        $this->db->select([
            'e.*',
            'p.name as project_name',
        ]);
        $this->db->from(db_prefix() . 'dw_time_estimates e');
        $this->db->join(db_prefix() . 'projects p', 'p.id = e.project_id', 'left');
        
        if ($project_id) {
            $this->db->where('e.project_id', $project_id);
        }
        
        $this->db->order_by('e.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get estimates for project
     */
    public function get_estimates($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get(db_prefix() . 'dw_time_estimates')->result();
    }

    /**
     * Add estimate (PERT)
     */
    public function add_estimate($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');

        // Calculate final estimate with buffer
        $calculated = ($data['optimistic_hours'] + 4 * $data['likely_hours'] + $data['pessimistic_hours']) / 6;
        $buffer = $data['buffer_percent'] ?? 20;
        $data['final_estimate'] = round($calculated * (1 + $buffer / 100), 2);

        $this->db->insert(db_prefix() . 'dw_time_estimates', $data);
        return $this->db->insert_id();
    }

    /**
     * Update estimate with actual hours
     */
    public function update_estimate_actual($id, $actual_hours)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_time_estimates', [
            'actual_hours' => $actual_hours,
        ]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get estimate accuracy report
     */
    public function get_estimate_accuracy($project_id = null)
    {
        $this->db->select([
            'COUNT(*) as total_estimates',
            'AVG(variance) as avg_variance',
            'AVG(ABS(variance)) as avg_absolute_variance',
            'SUM(CASE WHEN variance <= 0 THEN 1 ELSE 0 END) as under_estimates',
            'SUM(CASE WHEN variance > 0 THEN 1 ELSE 0 END) as over_estimates',
        ]);
        $this->db->where('actual_hours IS NOT NULL');

        if ($project_id) {
            $this->db->where('project_id', $project_id);
        }

        return $this->db->get(db_prefix() . 'dw_time_estimates')->row();
    }

    // =====================================================
    // TECHNICAL SPECS
    // =====================================================

    /**
     * Get technical specs for project
     */
    public function get_technical_specs($project_id, $type = null)
    {
        $this->db->select([
            'ts.*',
            'CONCAT(s.firstname, " ", s.lastname) as created_by_name',
        ]);
        $this->db->from(db_prefix() . 'dw_technical_specs ts');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = ts.created_by', 'left');
        $this->db->where('ts.project_id', $project_id);

        if ($type) {
            $this->db->where('ts.type', $type);
        }

        $this->db->order_by('ts.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get single technical spec
     */
    public function get_technical_spec($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'dw_technical_specs')->row();
    }

    /**
     * Add technical spec
     */
    public function add_technical_spec($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['version'] = '1.0';

        $this->db->insert(db_prefix() . 'dw_technical_specs', $data);
        $id = $this->db->insert_id();

        if ($id) {
            log_activity('Technical Spec Created [ID: ' . $id . ']');
            return $id;
        }
        return false;
    }

    /**
     * Update technical spec
     */
    public function update_technical_spec($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_technical_specs', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Technical Spec Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Get spec types
     */
    public function get_spec_types()
    {
        return [
            'architecture' => 'Architecture',
            'api' => 'API Specification',
            'database' => 'Database Design',
            'integration' => 'Integration',
            'security' => 'Security',
            'performance' => 'Performance',
        ];
    }

    // =====================================================
    // DEV NOTES / KNOWLEDGE BASE
    // =====================================================

    /**
     * Get dev notes
     */
    public function get_dev_notes($filters = [])
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_dev_notes')) {
            return [];
        }

        $this->db->select([
            'n.*',
            'CONCAT(s.firstname, " ", s.lastname) as created_by_name',
            'p.name as project_name',
        ]);
        $this->db->from(db_prefix() . 'dw_dev_notes n');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = n.created_by', 'left');
        $this->db->join(db_prefix() . 'projects p', 'p.id = n.project_id', 'left');

        if (!empty($filters['project_id'])) {
            $this->db->where('n.project_id', $filters['project_id']);
        }
        if (!empty($filters['category'])) {
            $this->db->where('n.category', $filters['category']);
        }
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('n.title', $filters['search']);
            $this->db->or_like('n.content', $filters['search']);
            $this->db->or_like('n.tags', $filters['search']);
            $this->db->group_end();
        }

        if (!empty($filters['pinned_first'])) {
            $this->db->order_by('n.is_pinned', 'DESC');
        }
        $this->db->order_by('n.created_at', 'DESC');

        if (!empty($filters['limit'])) {
            $this->db->limit($filters['limit']);
        }

        return $this->db->get()->result();
    }

    /**
     * Get single note
     */
    public function get_dev_note($id, $increment_view = false)
    {
        $this->db->where('id', $id);
        $note = $this->db->get(db_prefix() . 'dw_dev_notes')->row();

        if ($note && $increment_view) {
            $this->db->where('id', $id);
            $this->db->set('views', 'views + 1', false);
            $this->db->update(db_prefix() . 'dw_dev_notes');
        }

        return $note;
    }

    /**
     * Add dev note
     */
    public function add_dev_note($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix() . 'dw_dev_notes', $data);
        $id = $this->db->insert_id();

        if ($id) {
            log_activity('Dev Note Created [ID: ' . $id . ']');
            return $id;
        }
        return false;
    }

    /**
     * Update dev note
     */
    public function update_dev_note($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_dev_notes', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Dev Note Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete dev note
     */
    public function delete_dev_note($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dw_dev_notes');

        if ($this->db->affected_rows() > 0) {
            log_activity('Dev Note Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Toggle note pin
     */
    public function toggle_note_pin($id)
    {
        $note = $this->get_dev_note($id);
        if (!$note) return false;

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_dev_notes', [
            'is_pinned' => $note->is_pinned ? 0 : 1,
        ]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get note categories
     */
    public function get_note_categories()
    {
        return [
            'general' => 'General',
            'troubleshooting' => 'Troubleshooting',
            'how_to' => 'How-To Guide',
            'architecture' => 'Architecture Decision',
            'api' => 'API Documentation',
            'deployment' => 'Deployment',
            'security' => 'Security',
            'performance' => 'Performance',
            'client' => 'Client Specific',
        ];
    }

    // =====================================================
    // STATISTICS & REPORTING
    // =====================================================

    /**
     * Get project SDLC statistics
     */
    public function get_project_sdlc_stats($project_id)
    {
        $stats = [];

        // Requirements stats
        $this->db->where('project_id', $project_id);
        $stats['requirements_total'] = $this->db->count_all_results(db_prefix() . 'dw_project_requirements');

        $this->db->where('project_id', $project_id);
        $this->db->where('status', 'implemented');
        $stats['requirements_done'] = $this->db->count_all_results(db_prefix() . 'dw_project_requirements');

        // Change requests
        $this->db->where('project_id', $project_id);
        $stats['change_requests_total'] = $this->db->count_all_results(db_prefix() . 'dw_change_requests');

        $this->db->where('project_id', $project_id);
        $this->db->where_in('status', ['submitted', 'under_review']);
        $stats['change_requests_pending'] = $this->db->count_all_results(db_prefix() . 'dw_change_requests');

        // User stories
        $this->db->where('project_id', $project_id);
        $stats['stories_total'] = $this->db->count_all_results(db_prefix() . 'dw_user_stories');

        $this->db->where('project_id', $project_id);
        $this->db->where('status', 'done');
        $stats['stories_done'] = $this->db->count_all_results(db_prefix() . 'dw_user_stories');

        // Story points
        $this->db->select_sum('story_points');
        $this->db->where('project_id', $project_id);
        $result = $this->db->get(db_prefix() . 'dw_user_stories')->row();
        $stats['total_story_points'] = $result->story_points ?? 0;

        $this->db->select_sum('story_points');
        $this->db->where('project_id', $project_id);
        $this->db->where('status', 'done');
        $result = $this->db->get(db_prefix() . 'dw_user_stories')->row();
        $stats['completed_story_points'] = $result->story_points ?? 0;

        // Scope status
        $this->db->where('project_id', $project_id);
        $this->db->where('status', 'approved');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $stats['current_scope'] = $this->db->get(db_prefix() . 'dw_scope_documents')->row();

        return $stats;
    }

    // =====================================================
    // PROJECT DOCUMENTATION
    // =====================================================

    /**
     * Get all documents
     */
    public function get_all_docs($filters = [])
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_project_docs')) {
            return [];
        }

        $this->db->select([
            'd.*',
            'p.name as project_name',
        ]);
        $this->db->from(db_prefix() . 'dw_project_docs d');
        $this->db->join(db_prefix() . 'projects p', 'p.id = d.project_id', 'left');

        if (!empty($filters['project_id'])) {
            $this->db->where('d.project_id', $filters['project_id']);
        }
        if (!empty($filters['doc_type'])) {
            $this->db->where('d.doc_type', $filters['doc_type']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('d.status', $filters['status']);
        }

        $this->db->order_by('d.updated_at', 'DESC');

        if (!empty($filters['limit'])) {
            $this->db->limit($filters['limit']);
        }

        return $this->db->get()->result();
    }

    /**
     * Get document
     */
    public function get_document($id)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_project_docs')) {
            return null;
        }

        $this->db->select([
            'd.*',
            'p.name as project_name',
        ]);
        $this->db->from(db_prefix() . 'dw_project_docs d');
        $this->db->join(db_prefix() . 'projects p', 'p.id = d.project_id', 'left');
        $this->db->where('d.id', $id);
        return $this->db->get()->row();
    }

    /**
     * Add document
     */
    public function add_document($data)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_project_docs')) {
            return false;
        }

        $insert_data = [
            'project_id' => !empty($data['project_id']) ? $data['project_id'] : null,
            'title' => $data['title'],
            'doc_type' => $data['doc_type'] ?? 'other',
            'content' => $data['content'],
            'status' => $data['status'] ?? 'draft',
            'version' => $data['version'] ?? '1.0',
            'tags' => $data['tags'] ?? null,
            'created_by' => get_staff_user_id(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . 'dw_project_docs', $insert_data);
        $id = $this->db->insert_id();

        if ($id) {
            log_activity('Document Added [ID: ' . $id . ']');
            return $id;
        }
        return false;
    }

    /**
     * Update document
     */
    public function update_document($id, $data)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_project_docs')) {
            return false;
        }

        $update_data = [
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (isset($data['title'])) $update_data['title'] = $data['title'];
        if (isset($data['project_id'])) $update_data['project_id'] = !empty($data['project_id']) ? $data['project_id'] : null;
        if (isset($data['doc_type'])) $update_data['doc_type'] = $data['doc_type'];
        if (isset($data['content'])) $update_data['content'] = $data['content'];
        if (isset($data['status'])) $update_data['status'] = $data['status'];
        if (isset($data['version'])) $update_data['version'] = $data['version'];
        if (isset($data['tags'])) $update_data['tags'] = $data['tags'];

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_project_docs', $update_data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Document Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete document
     */
    public function delete_document($id)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_project_docs')) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dw_project_docs');

        if ($this->db->affected_rows() > 0) {
            log_activity('Document Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Duplicate document
     */
    public function duplicate_document($id)
    {
        $doc = $this->get_document($id);
        if (!$doc) return false;

        return $this->add_document([
            'project_id' => $doc->project_id,
            'title' => $doc->title . ' (Copy)',
            'doc_type' => $doc->doc_type,
            'content' => $doc->content,
            'status' => 'draft',
            'tags' => $doc->tags,
        ]);
    }

    /**
     * Increment document views
     */
    public function increment_doc_views($id)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_project_docs')) {
            return false;
        }

        $this->db->set('views', 'views + 1', false);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_project_docs');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get documentation stats
     */
    public function get_docs_stats()
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_project_docs')) {
            return ['total' => 0, 'draft' => 0, 'published' => 0];
        }

        $stats = [];

        $stats['total'] = $this->db->count_all_results(db_prefix() . 'dw_project_docs');

        $this->db->where('status', 'draft');
        $stats['draft'] = $this->db->count_all_results(db_prefix() . 'dw_project_docs');

        $this->db->where('status', 'published');
        $stats['published'] = $this->db->count_all_results(db_prefix() . 'dw_project_docs');

        return $stats;
    }

    // =====================================================
    // TECHNICAL SPECIFICATIONS
    // =====================================================

    /**
     * Get all tech specs
     */
    public function get_all_tech_specs($filters = [])
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_technical_specs')) {
            return [];
        }

        $this->db->select([
            't.*',
            'p.name as project_name',
        ]);
        $this->db->from(db_prefix() . 'dw_technical_specs t');
        $this->db->join(db_prefix() . 'projects p', 'p.id = t.project_id', 'left');

        if (!empty($filters['project_id'])) {
            $this->db->where('t.project_id', $filters['project_id']);
        }
        if (!empty($filters['type'])) {
            $this->db->where('t.type', $filters['type']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('t.status', $filters['status']);
        }

        $this->db->order_by('t.updated_at', 'DESC');

        if (!empty($filters['limit'])) {
            $this->db->limit($filters['limit']);
        }

        return $this->db->get()->result();
    }

    /**
     * Get tech spec
     */
    public function get_tech_spec($id)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_technical_specs')) {
            return null;
        }

        $this->db->select([
            't.*',
            'p.name as project_name',
        ]);
        $this->db->from(db_prefix() . 'dw_technical_specs t');
        $this->db->join(db_prefix() . 'projects p', 'p.id = t.project_id', 'left');
        $this->db->where('t.id', $id);
        return $this->db->get()->row();
    }

    /**
     * Add tech spec
     */
    public function add_tech_spec($data)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_technical_specs')) {
            return false;
        }

        $insert_data = [
            'project_id' => !empty($data['project_id']) ? $data['project_id'] : null,
            'type' => $data['type'] ?? 'architecture',
            'title' => $data['title'],
            'content' => $data['content'],
            'version' => $data['version'] ?? '1.0',
            'status' => $data['status'] ?? 'draft',
            'created_by' => get_staff_user_id(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . 'dw_technical_specs', $insert_data);
        $id = $this->db->insert_id();

        if ($id) {
            log_activity('Tech Spec Added [ID: ' . $id . ']');
            return $id;
        }
        return false;
    }

    /**
     * Update tech spec
     */
    public function update_tech_spec($id, $data)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_technical_specs')) {
            return false;
        }

        $update_data = [
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (isset($data['title'])) $update_data['title'] = $data['title'];
        if (isset($data['project_id'])) $update_data['project_id'] = !empty($data['project_id']) ? $data['project_id'] : null;
        if (isset($data['type'])) $update_data['type'] = $data['type'];
        if (isset($data['content'])) $update_data['content'] = $data['content'];
        if (isset($data['status'])) $update_data['status'] = $data['status'];
        if (isset($data['version'])) $update_data['version'] = $data['version'];

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_technical_specs', $update_data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Tech Spec Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete tech spec
     */
    public function delete_tech_spec($id)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_technical_specs')) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dw_technical_specs');

        if ($this->db->affected_rows() > 0) {
            log_activity('Tech Spec Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Get tech specs counts by type
     */
    public function get_tech_specs_counts()
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_technical_specs')) {
            return [
                'architecture' => 0,
                'api' => 0,
                'database' => 0,
                'integration' => 0,
                'security' => 0,
                'performance' => 0,
            ];
        }

        $counts = [];
        $types = ['architecture', 'api', 'database', 'integration', 'security', 'performance'];
        
        foreach ($types as $type) {
            $this->db->where('type', $type);
            $counts[$type] = $this->db->count_all_results(db_prefix() . 'dw_technical_specs');
        }

        return $counts;
    }

    // =====================================================
    // PROJECT PLANNING
    // =====================================================

    /**
     * Get planning statistics
     */
    public function get_planning_stats()
    {
        $stats = [];

        // Active projects (status 2 = In Progress)
        $this->db->where('status', 2);
        $stats['active_projects'] = $this->db->count_all_results(db_prefix() . 'projects');

        // Total phases
        if ($this->db->table_exists(db_prefix() . 'dw_project_phases')) {
            $stats['total_phases'] = $this->db->count_all_results(db_prefix() . 'dw_project_phases');
        } else {
            $stats['total_phases'] = 0;
        }

        // Total milestones
        if ($this->db->table_exists(db_prefix() . 'dw_project_milestones')) {
            $stats['total_milestones'] = $this->db->count_all_results(db_prefix() . 'dw_project_milestones');
            
            // Overdue milestones
            $this->db->where('status !=', 'done');
            $this->db->where('week_end <', date('Y-m-d'));
            $stats['overdue_milestones'] = $this->db->count_all_results(db_prefix() . 'dw_project_milestones');
        } else {
            $stats['total_milestones'] = 0;
            $stats['overdue_milestones'] = 0;
        }

        return $stats;
    }

    /**
     * Get projects with phases data
     */
    public function get_projects_with_phases()
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_project_phases')) {
            return [];
        }

        // Get active projects
        $this->db->select('id, name, status');
        $this->db->where('status', 2); // In Progress
        $this->db->order_by('name', 'ASC');
        $this->db->limit(10);
        $projects = $this->db->get(db_prefix() . 'projects')->result_array();

        $status_colors = [
            1 => 'default',  // Not Started
            2 => 'info',     // In Progress
            3 => 'warning',  // On Hold
            4 => 'success',  // Finished
            5 => 'danger',   // Cancelled
        ];

        $status_names = [
            1 => _l('project_status_1'),
            2 => _l('project_status_2'),
            3 => _l('project_status_3'),
            4 => _l('project_status_4'),
            5 => _l('project_status_5'),
        ];

        foreach ($projects as &$project) {
            // Get phases
            $this->db->where('project_id', $project['id']);
            $this->db->order_by('position', 'ASC');
            $phases = $this->db->get(db_prefix() . 'dw_project_phases')->result_array();
            
            $project['phases'] = $phases;
            $project['phases_total'] = count($phases);
            $project['phases_completed'] = count(array_filter($phases, function($p) { return $p['status'] === 'done'; }));
            $project['status_color'] = $status_colors[$project['status']] ?? 'default';
            $project['status_name'] = $status_names[$project['status']] ?? '';

            // Get upcoming milestones
            if ($this->db->table_exists(db_prefix() . 'dw_project_milestones')) {
                $this->db->where('project_id', $project['id']);
                $this->db->where('status !=', 'done');
                $this->db->where('week_end >=', date('Y-m-d'));
                $this->db->order_by('week_end', 'ASC');
                $this->db->limit(3);
                $project['upcoming_milestones'] = $this->db->get(db_prefix() . 'dw_project_milestones')->result_array();
            } else {
                $project['upcoming_milestones'] = [];
            }
        }

        return $projects;
    }

    /**
     * Get upcoming milestones
     */
    public function get_upcoming_milestones($limit = 10)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_project_milestones')) {
            return [];
        }

        $today = date('Y-m-d');
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $week_end = date('Y-m-d', strtotime('sunday this week'));

        $this->db->select([
            'm.*',
            'p.name as project_name',
        ]);
        $this->db->from(db_prefix() . 'dw_project_milestones m');
        $this->db->join(db_prefix() . 'projects p', 'p.id = m.project_id', 'left');
        $this->db->where('m.status !=', 'done');
        $this->db->order_by('m.week_end', 'ASC');
        $this->db->limit($limit);
        
        $milestones = $this->db->get()->result_array();

        foreach ($milestones as &$ms) {
            $ms['is_overdue'] = $ms['week_end'] < $today;
            $ms['is_this_week'] = ($ms['week_start'] <= $week_end && $ms['week_end'] >= $week_start);
        }

        return $milestones;
    }
}
