<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Developer Tools Model
 * 
 * Tools for developer productivity:
 * - Code Snippets Library
 * - Dev Templates
 * - SDLC Checklists
 */
class Dw_dev_tools_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // =====================================================
    // CODE SNIPPETS LIBRARY
    // =====================================================

    /**
     * Get snippets with filters
     */
    public function get_snippets($filters = [])
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_code_snippets')) {
            return [];
        }

        $this->db->select([
            's.*',
            'CONCAT(st.firstname, " ", st.lastname) as created_by_name',
        ]);
        $this->db->from(db_prefix() . 'dw_code_snippets s');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = s.created_by', 'left');

        if (!empty($filters['category'])) {
            $this->db->where('s.category', $filters['category']);
        }
        if (!empty($filters['language'])) {
            $this->db->where('s.language', $filters['language']);
        }
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('s.title', $filters['search']);
            $this->db->or_like('s.description', $filters['search']);
            $this->db->or_like('s.tags', $filters['search']);
            $this->db->or_like('s.code', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by('s.usage_count', 'DESC');
        $this->db->order_by('s.title', 'ASC');

        if (!empty($filters['limit'])) {
            $this->db->limit($filters['limit']);
        }

        return $this->db->get()->result();
    }

    /**
     * Get single snippet
     */
    public function get_snippet($id, $increment_usage = false)
    {
        $this->db->where('id', $id);
        $snippet = $this->db->get(db_prefix() . 'dw_code_snippets')->row();

        if ($snippet && $increment_usage) {
            $this->db->where('id', $id);
            $this->db->set('usage_count', 'usage_count + 1', false);
            $this->db->update(db_prefix() . 'dw_code_snippets');
        }

        return $snippet;
    }

    /**
     * Add snippet
     */
    public function add_snippet($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['usage_count'] = 0;

        $this->db->insert(db_prefix() . 'dw_code_snippets', $data);
        $id = $this->db->insert_id();

        if ($id) {
            log_activity('Code Snippet Created [ID: ' . $id . ']');
            return $id;
        }
        return false;
    }

    /**
     * Update snippet
     */
    public function update_snippet($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_code_snippets', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Code Snippet Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete snippet
     */
    public function delete_snippet($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dw_code_snippets');

        if ($this->db->affected_rows() > 0) {
            log_activity('Code Snippet Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Get snippet categories
     */
    public function get_snippet_categories()
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_code_snippets')) {
            return [];
        }
        $this->db->distinct();
        $this->db->select('category');
        $this->db->order_by('category', 'ASC');
        return $this->db->get(db_prefix() . 'dw_code_snippets')->result();
    }

    /**
     * Get snippet languages
     */
    public function get_snippet_languages()
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_code_snippets')) {
            return [];
        }
        $this->db->distinct();
        $this->db->select('language');
        $this->db->order_by('language', 'ASC');
        return $this->db->get(db_prefix() . 'dw_code_snippets')->result();
    }

    /**
     * Get most used snippets
     */
    public function get_popular_snippets($limit = 10)
    {
        $this->db->order_by('usage_count', 'DESC');
        $this->db->limit($limit);
        return $this->db->get(db_prefix() . 'dw_code_snippets')->result();
    }

    /**
     * Search snippets (fulltext)
     */
    public function search_snippets($query)
    {
        // Simple LIKE search as fallback
        $this->db->group_start();
        $this->db->like('title', $query);
        $this->db->or_like('description', $query);
        $this->db->or_like('tags', $query);
        $this->db->or_like('code', $query);
        $this->db->group_end();
        $this->db->order_by('usage_count', 'DESC');
        $this->db->limit(20);
        return $this->db->get(db_prefix() . 'dw_code_snippets')->result();
    }

    // =====================================================
    // DEV TEMPLATES
    // =====================================================

    /**
     * Get templates
     */
    public function get_templates($type = null, $category = null)
    {
        $this->db->select([
            't.*',
            'CONCAT(s.firstname, " ", s.lastname) as created_by_name',
        ]);
        $this->db->from(db_prefix() . 'dw_dev_templates t');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = t.created_by', 'left');

        if ($type) {
            $this->db->where('t.type', $type);
        }
        if ($category) {
            $this->db->where('t.category', $category);
        }

        $this->db->order_by('t.is_default', 'DESC');
        $this->db->order_by('t.usage_count', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get single template
     */
    public function get_template($id, $increment_usage = false)
    {
        $this->db->where('id', $id);
        $template = $this->db->get(db_prefix() . 'dw_dev_templates')->row();

        if ($template && $increment_usage) {
            $this->db->where('id', $id);
            $this->db->set('usage_count', 'usage_count + 1', false);
            $this->db->update(db_prefix() . 'dw_dev_templates');
        }

        return $template;
    }

    /**
     * Add template
     */
    public function add_template($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['usage_count'] = 0;

        $this->db->insert(db_prefix() . 'dw_dev_templates', $data);
        $id = $this->db->insert_id();

        if ($id) {
            log_activity('Dev Template Created [ID: ' . $id . ']');
            return $id;
        }
        return false;
    }

    /**
     * Update template
     */
    public function update_template($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_dev_templates', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Dev Template Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete template
     */
    public function delete_template($id)
    {
        $template = $this->get_template($id);
        if ($template && $template->is_default) {
            return false; // Don't delete default templates
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dw_dev_templates');

        if ($this->db->affected_rows() > 0) {
            log_activity('Dev Template Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Render template with variables
     */
    public function render_template($id, $variables = [])
    {
        $template = $this->get_template($id, true);
        if (!$template) return null;

        $content = $template->content;

        // Replace variables
        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return $content;
    }

    /**
     * Get template types
     */
    public function get_template_types()
    {
        return [
            'document' => 'Document',
            'code' => 'Code',
            'email' => 'Email',
            'checklist' => 'Checklist',
            'requirement' => 'Requirement',
            'scope' => 'Scope',
        ];
    }

    // =====================================================
    // SDLC CHECKLISTS
    // =====================================================

    /**
     * Get checklist templates
     */
    public function get_checklist_templates($type = null)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_sdlc_checklists')) {
            return [];
        }

        $this->db->where('is_template', 1);
        if ($type) {
            $this->db->where('type', $type);
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get(db_prefix() . 'dw_sdlc_checklists')->result();
    }

    /**
     * Get project checklists
     */
    public function get_project_checklists($project_id)
    {
        if (!$this->db->table_exists(db_prefix() . 'dw_sdlc_checklists')) {
            return [];
        }

        $this->db->select('c.*, (SELECT COUNT(*) FROM ' . db_prefix() . 'dw_checklist_items WHERE checklist_id = c.id) as total_items, (SELECT COUNT(*) FROM ' . db_prefix() . 'dw_checklist_items WHERE checklist_id = c.id AND is_checked = 1) as completed_items, p.name as project_name');
        $this->db->from(db_prefix() . 'dw_sdlc_checklists c');
        $this->db->join(db_prefix() . 'projects p', 'p.id = c.project_id', 'left');
        if ($project_id) {
            $this->db->where('c.project_id', $project_id);
        }
        $this->db->where('c.is_template', 0);
        $this->db->order_by('c.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get single checklist with items
     */
    public function get_checklist($id)
    {
        $this->db->where('id', $id);
        $checklist = $this->db->get(db_prefix() . 'dw_sdlc_checklists')->row();

        if ($checklist) {
            $checklist->items = $this->get_checklist_items($id);
            $checklist->progress = $this->calculate_checklist_progress($id);
        }

        return $checklist;
    }

    /**
     * Get checklist items
     */
    public function get_checklist_items($checklist_id, $parent_id = null)
    {
        $this->db->where('checklist_id', $checklist_id);
        if ($parent_id === null) {
            $this->db->where('parent_id IS NULL');
        } else {
            $this->db->where('parent_id', $parent_id);
        }
        $this->db->order_by('sort_order', 'ASC');
        $items = $this->db->get(db_prefix() . 'dw_checklist_items')->result();

        foreach ($items as &$item) {
            $item->children = $this->get_checklist_items($checklist_id, $item->id);
        }

        return $items;
    }

    /**
     * Create checklist from template
     */
    public function create_checklist_from_template($template_id, $project_id, $milestone_id = null)
    {
        $template = $this->get_checklist($template_id);
        if (!$template || !$template->is_template) return false;

        // Create checklist
        $checklist_data = [
            'type' => $template->type,
            'name' => $template->name,
            'description' => $template->description,
            'is_template' => 0,
            'project_id' => $project_id,
            'milestone_id' => $milestone_id,
            'status' => 'not_started',
            'progress' => 0,
            'created_by' => get_staff_user_id(),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . 'dw_sdlc_checklists', $checklist_data);
        $new_id = $this->db->insert_id();

        if ($new_id) {
            // Copy items
            $this->copy_checklist_items($template_id, $new_id);
            log_activity('Checklist Created from Template [ID: ' . $new_id . ', Project: ' . $project_id . ']');
            return $new_id;
        }

        return false;
    }

    /**
     * Copy checklist items from one checklist to another
     */
    private function copy_checklist_items($from_checklist_id, $to_checklist_id, $from_parent_id = null, $to_parent_id = null)
    {
        $this->db->where('checklist_id', $from_checklist_id);
        if ($from_parent_id === null) {
            $this->db->where('parent_id IS NULL');
        } else {
            $this->db->where('parent_id', $from_parent_id);
        }
        $items = $this->db->get(db_prefix() . 'dw_checklist_items')->result();

        foreach ($items as $item) {
            $item_data = [
                'checklist_id' => $to_checklist_id,
                'parent_id' => $to_parent_id,
                'title' => $item->title,
                'description' => $item->description,
                'is_required' => $item->is_required,
                'is_checked' => 0,
                'sort_order' => $item->sort_order,
            ];

            $this->db->insert(db_prefix() . 'dw_checklist_items', $item_data);
            $new_item_id = $this->db->insert_id();

            // Copy children recursively
            $this->copy_checklist_items($from_checklist_id, $to_checklist_id, $item->id, $new_item_id);
        }
    }

    /**
     * Add checklist
     */
    public function add_checklist($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['progress'] = 0;
        $data['status'] = 'not_started';

        $this->db->insert(db_prefix() . 'dw_sdlc_checklists', $data);
        $id = $this->db->insert_id();

        if ($id) {
            log_activity('Checklist Created [ID: ' . $id . ']');
            return $id;
        }
        return false;
    }

    /**
     * Update checklist
     */
    public function update_checklist($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_sdlc_checklists', $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete checklist
     */
    public function delete_checklist($id)
    {
        // Delete items first
        $this->db->where('checklist_id', $id);
        $this->db->delete(db_prefix() . 'dw_checklist_items');

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dw_sdlc_checklists');

        if ($this->db->affected_rows() > 0) {
            log_activity('Checklist Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Add checklist item
     */
    public function add_checklist_item($data)
    {
        $this->db->insert(db_prefix() . 'dw_checklist_items', $data);
        $id = $this->db->insert_id();

        if ($id) {
            $this->update_checklist_progress($data['checklist_id']);
            return $id;
        }
        return false;
    }

    /**
     * Update checklist item
     */
    public function update_checklist_item($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_checklist_items', $data);

        if ($this->db->affected_rows() > 0) {
            // Get checklist_id and update progress
            $this->db->select('checklist_id');
            $this->db->where('id', $id);
            $item = $this->db->get(db_prefix() . 'dw_checklist_items')->row();
            if ($item) {
                $this->update_checklist_progress($item->checklist_id);
            }
            return true;
        }
        return false;
    }

    /**
     * Delete checklist item
     */
    public function delete_checklist_item($id)
    {
        // Get checklist_id first
        $this->db->select('checklist_id');
        $this->db->where('id', $id);
        $item = $this->db->get(db_prefix() . 'dw_checklist_items')->row();

        // Delete children first
        $this->db->where('parent_id', $id);
        $this->db->delete(db_prefix() . 'dw_checklist_items');

        // Delete item
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dw_checklist_items');

        if ($this->db->affected_rows() > 0 && $item) {
            $this->update_checklist_progress($item->checklist_id);
            return true;
        }
        return false;
    }

    /**
     * Toggle checklist item
     */
    public function toggle_checklist_item($id)
    {
        $this->db->select('is_checked, checklist_id');
        $this->db->where('id', $id);
        $item = $this->db->get(db_prefix() . 'dw_checklist_items')->row();

        if (!$item) return false;

        $update = [
            'is_checked' => $item->is_checked ? 0 : 1,
        ];

        if (!$item->is_checked) {
            $update['checked_by'] = get_staff_user_id();
            $update['checked_at'] = date('Y-m-d H:i:s');
        } else {
            $update['checked_by'] = null;
            $update['checked_at'] = null;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dw_checklist_items', $update);

        $this->update_checklist_progress($item->checklist_id);
        return !$item->is_checked; // Return new state
    }

    /**
     * Calculate and update checklist progress
     */
    public function update_checklist_progress($checklist_id)
    {
        $progress = $this->calculate_checklist_progress($checklist_id);

        $status = 'not_started';
        if ($progress == 100) {
            $status = 'completed';
        } elseif ($progress > 0) {
            $status = 'in_progress';
        }

        $update = [
            'progress' => $progress,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($status == 'completed') {
            $update['completed_date'] = date('Y-m-d H:i:s');
        }

        $this->db->where('id', $checklist_id);
        $this->db->update(db_prefix() . 'dw_sdlc_checklists', $update);

        return $progress;
    }

    /**
     * Calculate checklist progress
     */
    public function calculate_checklist_progress($checklist_id)
    {
        $this->db->where('checklist_id', $checklist_id);
        $total = $this->db->count_all_results(db_prefix() . 'dw_checklist_items');

        if ($total == 0) return 0;

        $this->db->where('checklist_id', $checklist_id);
        $this->db->where('is_checked', 1);
        $checked = $this->db->count_all_results(db_prefix() . 'dw_checklist_items');

        return round(($checked / $total) * 100);
    }

    /**
     * Get checklist types
     */
    public function get_checklist_types()
    {
        return [
            'kickoff' => 'Project Kickoff',
            'requirements' => 'Requirements',
            'design' => 'Design',
            'development' => 'Development',
            'code_review' => 'Code Review',
            'qa' => 'QA Testing',
            'deployment' => 'Deployment',
            'handover' => 'Client Handover',
            'maintenance' => 'Maintenance',
            'custom' => 'Custom',
        ];
    }

    // =====================================================
    // QUICK ACTIONS
    // =====================================================

    /**
     * Get available quick actions for dashboard
     */
    public function get_quick_actions()
    {
        return [
            [
                'id' => 'new_requirement',
                'label' => 'Add Requirement',
                'icon' => 'fa fa-list-alt',
                'color' => 'primary',
                'url' => 'disowebs_ops/requirements/add',
            ],
            [
                'id' => 'new_scope',
                'label' => 'Create Scope',
                'icon' => 'fa fa-file-contract',
                'color' => 'success',
                'url' => 'disowebs_ops/scope/add',
            ],
            [
                'id' => 'new_story',
                'label' => 'Add User Story',
                'icon' => 'fa fa-book',
                'color' => 'info',
                'url' => 'disowebs_ops/stories/add',
            ],
            [
                'id' => 'new_snippet',
                'label' => 'Save Snippet',
                'icon' => 'fa fa-code',
                'color' => 'warning',
                'url' => 'disowebs_ops/snippets/add',
            ],
            [
                'id' => 'new_checklist',
                'label' => 'Start Checklist',
                'icon' => 'fa fa-tasks',
                'color' => 'default',
                'url' => 'disowebs_ops/checklists',
            ],
            [
                'id' => 'new_note',
                'label' => 'Add Dev Note',
                'icon' => 'fa fa-sticky-note',
                'color' => 'default',
                'url' => 'disowebs_ops/notes/add',
            ],
        ];
    }

    // =====================================================
    // STATISTICS
    // =====================================================

    /**
     * Check if SDLC tables exist
     */
    private function sdlc_tables_exist()
    {
        return $this->db->table_exists(db_prefix() . 'dw_code_snippets');
    }

    /**
     * Get overall dev tools stats
     */
    public function get_dev_tools_stats()
    {
        $stats = [
            'total_snippets' => 0,
            'total_templates' => 0,
            'active_checklists' => 0,
            'pending_change_requests' => 0,
            'pending_requirements' => 0,
            'total_notes' => 0,
        ];

        // Return empty stats if tables don't exist yet
        if (!$this->sdlc_tables_exist()) {
            return $stats;
        }

        try {
            // Snippets
            $stats['total_snippets'] = $this->db->count_all(db_prefix() . 'dw_code_snippets');

            // Templates
            if ($this->db->table_exists(db_prefix() . 'dw_dev_templates')) {
                $stats['total_templates'] = $this->db->count_all(db_prefix() . 'dw_dev_templates');
            }

            // Active checklists
            if ($this->db->table_exists(db_prefix() . 'dw_sdlc_checklists')) {
                $this->db->where('is_template', 0);
                $this->db->where('status !=', 'completed');
                $stats['active_checklists'] = $this->db->count_all_results(db_prefix() . 'dw_sdlc_checklists');
            }

            // Pending CRs
            if ($this->db->table_exists(db_prefix() . 'dw_change_requests')) {
                $this->db->where_in('status', ['submitted', 'under_review']);
                $stats['pending_change_requests'] = $this->db->count_all_results(db_prefix() . 'dw_change_requests');
            }

            // Pending Requirements
            if ($this->db->table_exists(db_prefix() . 'dw_project_requirements')) {
                $this->db->where('status', 'pending');
                $stats['pending_requirements'] = $this->db->count_all_results(db_prefix() . 'dw_project_requirements');
            }

            // Dev notes
            if ($this->db->table_exists(db_prefix() . 'dw_dev_notes')) {
                $stats['total_notes'] = $this->db->count_all(db_prefix() . 'dw_dev_notes');
            }
        } catch (Exception $e) {
            // Return empty stats on error
        }

        return $stats;
    }
}
