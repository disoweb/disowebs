<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SDLC Tools Controller
 * 
 * Handles all SDLC productivity tools:
 * - Requirements Management
 * - Scope Documents
 * - Change Requests
 * - User Stories
 * - Code Snippets
 * - Dev Templates
 * - Checklists
 * - Dev Notes
 */
class Disowebs_ops_sdlc extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if (!defined('DISOWEBS_OPS_MODULE_NAME')) {
            show_404();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $this->load->model('disowebs_ops/dw_dev_tools_model');
    }

    // =====================================================
    // SDLC TOOLS HUB
    // =====================================================

    /**
     * SDLC Tools Hub - Main landing page
     */
    public function index()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        // Get stats for all tools
        $data['stats'] = $this->dw_dev_tools_model->get_dev_tools_stats();
        $data['quick_actions'] = $this->dw_dev_tools_model->get_quick_actions();

        // Recent items
        $data['recent_snippets'] = $this->dw_dev_tools_model->get_snippets(['limit' => 5]);
        $data['recent_notes'] = $this->dw_sdlc_model->get_dev_notes(['limit' => 5, 'pinned_first' => true]);
        $data['active_checklists'] = $this->dw_dev_tools_model->get_project_checklists(null);

        $data['title'] = _l('disowebs_ops_sdlc_tools');
        $this->load->view('disowebs_ops/admin/sdlc/tools_hub', $data);
    }

    // =====================================================
    // REQUIREMENTS
    // =====================================================

    /**
     * List requirements for a project
     */
    public function requirements($project_id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        // Get filters
        $filters = [
            'status' => $this->input->get('status'),
            'priority' => $this->input->get('priority'),
            'category' => $this->input->get('category'),
        ];

        // Get requirements (all or for specific project)
        $data['requirements'] = $this->dw_sdlc_model->get_all_requirements($project_id, $filters);
        
        // Get stats
        $data['stats'] = $this->dw_sdlc_model->get_requirements_stats($project_id);
        
        if ($project_id) {
            $this->load->model('projects_model');
            $data['project'] = $this->projects_model->get($project_id);
        } else {
            $data['project'] = null;
        }

        // Get all projects for selector
        $this->db->select('id, name, clientid');
        $this->db->where_in('status', [1, 2, 3]); // Not finished or cancelled
        $this->db->order_by('name', 'ASC');
        $data['projects'] = $this->db->get(db_prefix() . 'projects')->result_array();
        
        // Get clients for form
        $this->db->select('userid, company');
        $this->db->order_by('company', 'ASC');
        $data['clients'] = $this->db->get(db_prefix() . 'clients')->result_array();

        $data['categories'] = $this->dw_sdlc_model->get_requirement_categories();
        $data['project_id'] = $project_id;
        $data['filters'] = $filters;
        $data['title'] = _l('disowebs_ops_requirements');

        $this->load->view('disowebs_ops/admin/sdlc/requirements', $data);
    }

    /**
     * Add/Edit requirement
     */
    public function requirement($id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if ($id) {
                $success = $this->dw_sdlc_model->update_requirement($id, $data);
                $message = $success ? _l('updated_successfully', _l('disowebs_ops_requirement')) : _l('error_updating');
            } else {
                $id = $this->dw_sdlc_model->add_requirement($data);
                $success = $id ? true : false;
                $message = $success ? _l('added_successfully', _l('disowebs_ops_requirement')) : _l('error_adding');
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => $success, 'message' => $message, 'id' => $id]);
                return;
            }

            set_alert($success ? 'success' : 'danger', $message);
            redirect(admin_url('disowebs_ops/sdlc/requirements'));
        }

        if ($id) {
            $data['requirement'] = $this->dw_sdlc_model->get_requirement($id);
        } else {
            $data['requirement'] = null;
        }

        // Get all projects for selector
        $this->db->select('id, name, clientid');
        $this->db->where_in('status', [1, 2, 3]);
        $this->db->order_by('name', 'ASC');
        $data['projects'] = $this->db->get(db_prefix() . 'projects')->result_array();
        
        // Get clients for form
        $this->db->select('userid, company');
        $this->db->order_by('company', 'ASC');
        $data['clients'] = $this->db->get(db_prefix() . 'clients')->result_array();

        $data['categories'] = $this->dw_sdlc_model->get_requirement_categories();
        $data['project_id'] = $this->input->get('project_id');
        $data['title'] = $id ? _l('edit') : _l('add_new');

        $this->load->view('disowebs_ops/admin/sdlc/requirement_form', $data);
    }

    /**
     * Delete requirement
     */
    public function delete_requirement($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'delete')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $req = $this->dw_sdlc_model->get_requirement($id);
        $success = $this->dw_sdlc_model->delete_requirement($id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $success]);
            return;
        }

        set_alert($success ? 'success' : 'danger', $success ? _l('deleted') : _l('error_deleting'));
        redirect(admin_url('disowebs_ops/sdlc/requirements/' . ($req ? $req->project_id : '')));
    }

    /**
     * Convert requirement to task
     */
    public function requirement_to_task($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $task_id = $this->dw_sdlc_model->requirement_to_task($id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $task_id ? true : false, 'task_id' => $task_id]);
            return;
        }

        if ($task_id) {
            set_alert('success', _l('disowebs_ops_requirement_converted_to_task'));
            redirect(admin_url('tasks/view/' . $task_id));
        } else {
            set_alert('danger', _l('error_converting'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    // =====================================================
    // SCOPE DOCUMENTS
    // =====================================================

    /**
     * List scope documents for a project
     */
    public function scope($project_id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        // Get scopes (all or for specific project)
        $data['scopes'] = $this->dw_sdlc_model->get_all_scopes($project_id);
        
        if ($project_id) {
            $this->load->model('projects_model');
            $data['project'] = $this->projects_model->get($project_id);
        } else {
            $data['project'] = null;
        }

        // Get all projects
        $this->db->select('id, name, clientid');
        $this->db->where_in('status', [1, 2, 3]);
        $this->db->order_by('name', 'ASC');
        $data['projects'] = $this->db->get(db_prefix() . 'projects')->result_array();
        
        // Get clients
        $this->db->select('userid, company');
        $this->db->order_by('company', 'ASC');
        $data['clients'] = $this->db->get(db_prefix() . 'clients')->result_array();

        $data['project_id'] = $project_id;
        $data['title'] = _l('disowebs_ops_scope_documents');

        $this->load->view('disowebs_ops/admin/sdlc/scope_list', $data);
    }

    /**
     * View/Edit scope document
     */
    public function scope_document($id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        if ($this->input->post() && has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'edit')) {
            $post_data = $this->input->post();

            if ($id) {
                $success = $this->dw_sdlc_model->update_scope($id, $post_data);
            } else {
                $id = $this->dw_sdlc_model->add_scope($post_data);
                $success = $id ? true : false;
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => $success, 'id' => $id]);
                return;
            }

            set_alert($success ? 'success' : 'danger', $success ? _l('updated_successfully', _l('disowebs_ops_scope_document')) : _l('error_updating'));
            redirect(admin_url('disowebs_ops/sdlc/scope_document/' . $id));
        }

        if ($id) {
            $data['scope'] = $this->dw_sdlc_model->get_scope_full($id);
            if (!$data['scope']) {
                show_404();
            }
            $data['project_id'] = $data['scope']->project_id;
        } else {
            $data['scope'] = null;
            $data['project_id'] = $this->input->get('project_id');
        }

        // Get all projects
        $this->db->select('id, name, clientid');
        $this->db->where_in('status', [1, 2, 3]);
        $this->db->order_by('name', 'ASC');
        $data['projects'] = $this->db->get(db_prefix() . 'projects')->result_array();
        
        // Get clients
        $this->db->select('userid, company');
        $this->db->order_by('company', 'ASC');
        $data['clients'] = $this->db->get(db_prefix() . 'clients')->result_array();

        // Get templates
        $data['templates'] = $this->dw_dev_tools_model->get_templates('scope');

        $data['title'] = $id ? _l('disowebs_ops_scope_document') : _l('disowebs_ops_new_scope');
        $this->load->view('disowebs_ops/admin/sdlc/scope_form', $data);
    }

    /**
     * Approve scope document
     */
    public function approve_scope($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'edit')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $success = $this->dw_sdlc_model->approve_scope($id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $success]);
            return;
        }

        set_alert($success ? 'success' : 'danger', $success ? _l('disowebs_ops_scope_approved') : _l('error'));
        redirect(admin_url('disowebs_ops/sdlc/scope_document/' . $id));
    }

    /**
     * Create new version of scope
     */
    public function new_scope_version($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $new_id = $this->dw_sdlc_model->create_scope_version($id);

        if ($new_id) {
            set_alert('success', _l('disowebs_ops_new_version_created'));
            redirect(admin_url('disowebs_ops/sdlc/scope_document/' . $new_id));
        } else {
            set_alert('danger', _l('error'));
            redirect(admin_url('disowebs_ops/sdlc/scope_document/' . $id));
        }
    }

    /**
     * Generate scope from requirements
     */
    public function generate_scope_from_requirements($project_id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        // Create new scope
        $this->load->model('projects_model');
        $project = $this->projects_model->get($project_id);

        $scope_id = $this->dw_sdlc_model->add_scope([
            'project_id' => $project_id,
            'title' => 'Scope Document - ' . $project->name,
            'overview' => 'Auto-generated from approved requirements.',
        ]);

        if ($scope_id) {
            $count = $this->dw_sdlc_model->generate_scope_from_requirements($project_id, $scope_id);
            set_alert('success', sprintf(_l('disowebs_ops_scope_generated'), $count));
            redirect(admin_url('disowebs_ops/sdlc/scope_document/' . $scope_id));
        } else {
            set_alert('danger', _l('error'));
            redirect(admin_url('disowebs_ops/sdlc/scope/' . $project_id));
        }
    }

    // =====================================================
    // CHANGE REQUESTS
    // =====================================================

    /**
     * List change requests
     */
    public function change_requests($project_id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $status = $this->input->get('status');

        // Get all change requests with project info
        $this->db->select([
            'cr.*',
            'p.name as project_name',
        ]);
        $this->db->from(db_prefix() . 'dw_change_requests cr');
        $this->db->join(db_prefix() . 'projects p', 'p.id = cr.project_id', 'left');
        
        if ($project_id) {
            $this->db->where('cr.project_id', $project_id);
        }
        if ($status) {
            $this->db->where('cr.status', $status);
        }
        $this->db->order_by('cr.created_at', 'DESC');
        $data['change_requests'] = $this->db->get()->result();
        
        // Get stats
        $data['stats'] = $this->dw_sdlc_model->get_change_request_stats($project_id);
        
        if ($project_id) {
            $this->load->model('projects_model');
            $data['project'] = $this->projects_model->get($project_id);
        } else {
            $data['project'] = null;
        }

        // Get all projects
        $this->db->select('id, name, clientid');
        $this->db->where_in('status', [1, 2, 3]);
        $this->db->order_by('name', 'ASC');
        $data['projects'] = $this->db->get(db_prefix() . 'projects')->result_array();
        
        // Get scopes for linking
        $this->db->select('id, title, project_id');
        $this->db->order_by('title', 'ASC');
        $data['scopes'] = $this->db->get(db_prefix() . 'dw_scope_documents')->result();

        $data['project_id'] = $project_id;
        $data['status_filter'] = $status;
        $data['title'] = _l('disowebs_ops_change_requests');

        $this->load->view('disowebs_ops/admin/sdlc/change_requests', $data);
    }

    /**
     * Add/Edit change request
     */
    public function change_request($id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if ($id) {
                $success = $this->dw_sdlc_model->update_change_request($id, $data);
            } else {
                $id = $this->dw_sdlc_model->add_change_request($data);
                $success = $id ? true : false;
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => $success, 'id' => $id]);
                return;
            }

            set_alert($success ? 'success' : 'danger', $success ? _l('saved') : _l('error'));
            redirect(admin_url('disowebs_ops/sdlc/change_requests/' . $data['project_id']));
        }

        if ($id) {
            $data['change_request'] = $this->dw_sdlc_model->get_change_request($id);
        } else {
            $data['change_request'] = null;
        }

        $data['project_id'] = $this->input->get('project_id');
        $data['templates'] = $this->dw_dev_tools_model->get_templates('document', 'change_request');
        $data['title'] = $id ? _l('edit') : _l('add_new');

        $this->load->view('disowebs_ops/admin/sdlc/change_request_form', $data);
    }

    /**
     * Approve/Reject change request
     */
    public function process_change_request($id, $action)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'edit')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $notes = $this->input->post('notes');

        if ($action === 'approve') {
            $success = $this->dw_sdlc_model->approve_change_request($id, $notes);
        } else {
            $success = $this->dw_sdlc_model->reject_change_request($id, $notes);
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $success]);
            return;
        }

        $cr = $this->dw_sdlc_model->get_change_request($id);
        set_alert($success ? 'success' : 'danger', $success ? _l('saved') : _l('error'));
        redirect(admin_url('disowebs_ops/sdlc/change_requests/' . ($cr ? $cr->project_id : '')));
    }

    // =====================================================
    // USER STORIES
    // =====================================================

    /**
     * List user stories
     */
    public function stories($project_id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $filters = [
            'status' => $this->input->get('status'),
            'sprint' => $this->input->get('sprint'),
            'epic' => $this->input->get('epic'),
        ];

        if ($project_id) {
            $data['stories'] = $this->dw_sdlc_model->get_user_stories($project_id, $filters);
            $data['epics'] = $this->dw_sdlc_model->get_epics($project_id);
            $data['sprints'] = $this->dw_sdlc_model->get_sprints($project_id);
            
            $this->load->model('projects_model');
            $data['project'] = $this->projects_model->get($project_id);
        } else {
            $data['stories'] = [];
            $data['epics'] = [];
            $data['sprints'] = [];
            $data['project'] = null;
        }

        // Get all projects
        $this->db->select('id, name');
        $this->db->order_by('name', 'ASC');
        $data['projects'] = $this->db->get(db_prefix() . 'projects')->result();

        $data['project_id'] = $project_id;
        $data['filters'] = $filters;
        $data['title'] = _l('disowebs_ops_user_stories');

        $this->load->view('disowebs_ops/admin/sdlc/user_stories', $data);
    }

    /**
     * Add/Edit user story
     */
    public function story($id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if ($id) {
                $success = $this->dw_sdlc_model->update_user_story($id, $data);
            } else {
                $id = $this->dw_sdlc_model->add_user_story($data);
                $success = $id ? true : false;
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => $success, 'id' => $id]);
                return;
            }

            set_alert($success ? 'success' : 'danger', $success ? _l('saved') : _l('error'));
            redirect(admin_url('disowebs_ops/sdlc/stories/' . $data['project_id']));
        }

        if ($id) {
            $data['story'] = $this->dw_sdlc_model->get_user_story($id);
        } else {
            $data['story'] = null;
        }

        $data['project_id'] = $this->input->get('project_id');
        $data['templates'] = $this->dw_dev_tools_model->get_templates('requirement', 'user_story');
        $data['title'] = $id ? _l('edit') : _l('add_new');

        $this->load->view('disowebs_ops/admin/sdlc/story_form', $data);
    }

    /**
     * Convert user story to task
     */
    public function story_to_task($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $task_id = $this->dw_sdlc_model->story_to_task($id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $task_id ? true : false, 'task_id' => $task_id]);
            return;
        }

        if ($task_id) {
            set_alert('success', _l('disowebs_ops_story_converted_to_task'));
            redirect(admin_url('tasks/view/' . $task_id));
        } else {
            set_alert('danger', _l('error'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    // =====================================================
    // CODE SNIPPETS
    // =====================================================

    /**
     * Code snippets library
     */
    public function snippets()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $filters = [
            'category' => $this->input->get('category'),
            'language' => $this->input->get('language'),
            'search' => $this->input->get('search'),
        ];

        $data['snippets'] = $this->dw_dev_tools_model->get_snippets($filters);
        $data['categories'] = $this->dw_dev_tools_model->get_snippet_categories();
        $data['languages'] = $this->dw_dev_tools_model->get_snippet_languages();
        $data['filters'] = $filters;
        $data['title'] = _l('disowebs_ops_code_snippets');

        $this->load->view('disowebs_ops/admin/sdlc/snippets', $data);
    }

    /**
     * Add/Edit snippet
     */
    public function snippet($id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if ($id) {
                $success = $this->dw_dev_tools_model->update_snippet($id, $data);
            } else {
                $id = $this->dw_dev_tools_model->add_snippet($data);
                $success = $id ? true : false;
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => $success, 'id' => $id]);
                return;
            }

            set_alert($success ? 'success' : 'danger', $success ? _l('saved') : _l('error'));
            redirect(admin_url('disowebs_ops/sdlc/snippets'));
        }

        if ($id) {
            $data['snippet'] = $this->dw_dev_tools_model->get_snippet($id);
        } else {
            $data['snippet'] = null;
        }

        $data['title'] = $id ? _l('edit') : _l('add_new');
        $this->load->view('disowebs_ops/admin/sdlc/snippet_form', $data);
    }

    /**
     * Get snippet (AJAX - for copy)
     */
    public function get_snippet($id)
    {
        $snippet = $this->dw_dev_tools_model->get_snippet($id, true); // Increment usage
        echo json_encode($snippet);
    }

    /**
     * Delete snippet
     */
    public function delete_snippet($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'delete')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $success = $this->dw_dev_tools_model->delete_snippet($id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $success]);
            return;
        }

        set_alert($success ? 'success' : 'danger', $success ? _l('deleted') : _l('error'));
        redirect(admin_url('disowebs_ops/sdlc/snippets'));
    }

    // =====================================================
    // DEV TEMPLATES
    // =====================================================

    /**
     * Templates library
     */
    public function templates()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $type = $this->input->get('type');
        $category = $this->input->get('category');

        $data['templates'] = $this->dw_dev_tools_model->get_templates($type, $category);
        $data['template_types'] = $this->dw_dev_tools_model->get_template_types();
        $data['type_filter'] = $type;
        $data['title'] = _l('disowebs_ops_templates');

        $this->load->view('disowebs_ops/admin/sdlc/templates', $data);
    }

    /**
     * Add/Edit template
     */
    public function template($id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if ($id) {
                $success = $this->dw_dev_tools_model->update_template($id, $data);
            } else {
                $id = $this->dw_dev_tools_model->add_template($data);
                $success = $id ? true : false;
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => $success, 'id' => $id]);
                return;
            }

            set_alert($success ? 'success' : 'danger', $success ? _l('saved') : _l('error'));
            redirect(admin_url('disowebs_ops/sdlc/templates'));
        }

        if ($id) {
            $data['template'] = $this->dw_dev_tools_model->get_template($id);
        } else {
            $data['template'] = null;
        }

        $data['template_types'] = $this->dw_dev_tools_model->get_template_types();
        $data['title'] = $id ? _l('edit') : _l('add_new');
        $this->load->view('disowebs_ops/admin/sdlc/template_form', $data);
    }

    /**
     * Get rendered template
     */
    public function render_template($id)
    {
        $variables = $this->input->post('variables') ?: [];
        $content = $this->dw_dev_tools_model->render_template($id, $variables);
        echo json_encode(['content' => $content]);
    }

    // =====================================================
    // CHECKLISTS
    // =====================================================

    /**
     * Checklists management
     */
    public function checklists($project_id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $data['templates'] = $this->dw_dev_tools_model->get_checklist_templates();
        
        // Get all active checklists with project info
        $this->db->select([
            'c.*',
            'p.name as project_name',
        ]);
        $this->db->from(db_prefix() . 'dw_sdlc_checklists c');
        $this->db->join(db_prefix() . 'projects p', 'p.id = c.project_id', 'left');
        $this->db->where('c.is_template', 0);
        
        if ($project_id) {
            $this->db->where('c.project_id', $project_id);
        } else {
            $this->db->where('c.status !=', 'completed');
        }
        
        $this->db->order_by('c.created_at', 'DESC');
        $data['checklists'] = $this->db->get()->result();
        
        if ($project_id) {
            $this->load->model('projects_model');
            $data['project'] = $this->projects_model->get($project_id);
        } else {
            $data['project'] = null;
        }

        // Get all projects
        $this->db->select('id, name');
        $this->db->where_in('status', [1, 2, 3]);
        $this->db->order_by('name', 'ASC');
        $data['projects'] = $this->db->get(db_prefix() . 'projects')->result_array();

        $data['project_id'] = $project_id;
        $data['checklist_types'] = $this->dw_dev_tools_model->get_checklist_types();
        $data['title'] = _l('disowebs_ops_checklists');

        $this->load->view('disowebs_ops/admin/sdlc/checklists', $data);
    }

    /**
     * View/Edit checklist
     */
    public function checklist($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $data['checklist'] = $this->dw_dev_tools_model->get_checklist($id);
        if (!$data['checklist']) {
            show_404();
        }

        $data['title'] = $data['checklist']->name;
        $this->load->view('disowebs_ops/admin/sdlc/checklist_view', $data);
    }

    /**
     * Create checklist from template
     */
    public function create_checklist_from_template()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $template_id = $this->input->post('template_id');
        $project_id = $this->input->post('project_id');
        $milestone_id = $this->input->post('milestone_id');

        $id = $this->dw_dev_tools_model->create_checklist_from_template($template_id, $project_id, $milestone_id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $id ? true : false, 'id' => $id]);
            return;
        }

        if ($id) {
            set_alert('success', _l('disowebs_ops_checklist_created'));
            redirect(admin_url('disowebs_ops/sdlc/checklist/' . $id));
        } else {
            set_alert('danger', _l('error'));
            redirect(admin_url('disowebs_ops/sdlc/checklists/' . $project_id));
        }
    }

    /**
     * Toggle checklist item
     */
    public function toggle_checklist_item($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $new_state = $this->dw_dev_tools_model->toggle_checklist_item($id);

        // Get updated progress
        $this->db->select('checklist_id');
        $this->db->where('id', $id);
        $item = $this->db->get(db_prefix() . 'dw_checklist_items')->row();
        $progress = 0;
        if ($item) {
            $progress = $this->dw_dev_tools_model->calculate_checklist_progress($item->checklist_id);
        }

        echo json_encode([
            'success' => true,
            'checked' => $new_state,
            'progress' => $progress,
        ]);
    }

    /**
     * Add checklist item
     */
    public function add_checklist_item()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'edit')) {
            echo json_encode(['success' => false]);
            return;
        }

        $data = $this->input->post();
        $id = $this->dw_dev_tools_model->add_checklist_item($data);

        echo json_encode(['success' => $id ? true : false, 'id' => $id]);
    }

    /**
     * Delete checklist item
     */
    public function delete_checklist_item($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'edit')) {
            echo json_encode(['success' => false]);
            return;
        }

        $success = $this->dw_dev_tools_model->delete_checklist_item($id);
        echo json_encode(['success' => $success]);
    }

    // =====================================================
    // DEV NOTES / KNOWLEDGE BASE
    // =====================================================

    /**
     * Dev notes / Knowledge base
     */
    public function notes()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $filters = [
            'project_id' => $this->input->get('project_id'),
            'category' => $this->input->get('category'),
            'search' => $this->input->get('search'),
            'pinned_first' => true,
        ];

        $data['notes'] = $this->dw_sdlc_model->get_dev_notes($filters);
        $data['categories'] = $this->dw_sdlc_model->get_note_categories();
        $data['filters'] = $filters;

        // Get all projects
        $this->db->select('id, name');
        $this->db->where_in('status', [1, 2, 3]);
        $this->db->order_by('name', 'ASC');
        $data['projects'] = $this->db->get(db_prefix() . 'projects')->result_array();

        $data['title'] = _l('disowebs_ops_dev_notes');
        $this->load->view('disowebs_ops/admin/sdlc/notes', $data);
    }

    /**
     * View note
     */
    public function note($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $data['note'] = $this->dw_sdlc_model->get_dev_note($id, true);
        if (!$data['note']) {
            show_404();
        }

        $data['title'] = $data['note']->title;
        $this->load->view('disowebs_ops/admin/sdlc/note_view', $data);
    }

    /**
     * Add/Edit note
     */
    public function edit_note($id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if ($id) {
                $success = $this->dw_sdlc_model->update_dev_note($id, $data);
            } else {
                $id = $this->dw_sdlc_model->add_dev_note($data);
                $success = $id ? true : false;
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => $success, 'id' => $id]);
                return;
            }

            set_alert($success ? 'success' : 'danger', $success ? _l('saved') : _l('error'));
            redirect(admin_url('disowebs_ops/sdlc/note/' . $id));
        }

        if ($id) {
            $data['note'] = $this->dw_sdlc_model->get_dev_note($id);
        } else {
            $data['note'] = null;
        }

        $data['categories'] = $this->dw_sdlc_model->get_note_categories();

        // Get all projects
        $this->db->select('id, name');
        $this->db->order_by('name', 'ASC');
        $data['projects'] = $this->db->get(db_prefix() . 'projects')->result();

        $data['title'] = $id ? _l('edit') : _l('add_new');
        $this->load->view('disowebs_ops/admin/sdlc/note_form', $data);
    }

    /**
     * Delete note
     */
    public function delete_note($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'delete')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $success = $this->dw_sdlc_model->delete_dev_note($id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $success]);
            return;
        }

        set_alert($success ? 'success' : 'danger', $success ? _l('deleted') : _l('error'));
        redirect(admin_url('disowebs_ops/sdlc/notes'));
    }

    /**
     * Toggle note pin
     */
    public function toggle_note_pin($id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'edit')) {
            echo json_encode(['success' => false]);
            return;
        }

        $success = $this->dw_sdlc_model->toggle_note_pin($id);
        echo json_encode(['success' => $success]);
    }

    // =====================================================
    // TECHNICAL SPECS
    // =====================================================

    /**
     * Technical specs for project
     */
    public function specs($project_id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $type = $this->input->get('type');
        $data['specs'] = $this->dw_sdlc_model->get_technical_specs($project_id, $type);
        $data['spec_types'] = $this->dw_sdlc_model->get_spec_types();

        $this->load->model('projects_model');
        $data['project'] = $this->projects_model->get($project_id);

        $data['project_id'] = $project_id;
        $data['type_filter'] = $type;
        $data['title'] = _l('disowebs_ops_technical_specs');

        $this->load->view('disowebs_ops/admin/sdlc/specs', $data);
    }

    /**
     * Add/Edit technical spec
     */
    public function spec($id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if ($id) {
                $success = $this->dw_sdlc_model->update_technical_spec($id, $data);
            } else {
                $id = $this->dw_sdlc_model->add_technical_spec($data);
                $success = $id ? true : false;
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => $success, 'id' => $id]);
                return;
            }

            set_alert($success ? 'success' : 'danger', $success ? _l('saved') : _l('error'));
            redirect(admin_url('disowebs_ops/sdlc/specs/' . $data['project_id']));
        }

        if ($id) {
            $data['spec'] = $this->dw_sdlc_model->get_technical_spec($id);
            $data['project_id'] = $data['spec']->project_id;
        } else {
            $data['spec'] = null;
            $data['project_id'] = $this->input->get('project_id');
        }

        $data['spec_types'] = $this->dw_sdlc_model->get_spec_types();
        $data['templates'] = $this->dw_dev_tools_model->get_templates('document', 'technical');
        $data['title'] = $id ? _l('edit') : _l('add_new');

        $this->load->view('disowebs_ops/admin/sdlc/spec_form', $data);
    }

    // =====================================================
    // TIME ESTIMATION
    // =====================================================

    /**
     * Time estimation tool (PERT)
     */
    public function estimates($project_id = null)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        // Get all estimates with project info
        $data['estimates'] = $this->dw_sdlc_model->get_all_estimates($project_id);
        $data['accuracy_stats'] = $this->dw_sdlc_model->get_estimate_accuracy($project_id);
        
        if ($project_id) {
            $this->load->model('projects_model');
            $data['project'] = $this->projects_model->get($project_id);
        } else {
            $data['project'] = null;
        }

        // Get all projects
        $this->db->select('id, name');
        $this->db->where_in('status', [1, 2, 3]);
        $this->db->order_by('name', 'ASC');
        $data['projects'] = $this->db->get(db_prefix() . 'projects')->result_array();

        $data['project_id'] = $project_id;
        $data['title'] = _l('disowebs_ops_time_estimates');

        $this->load->view('disowebs_ops/admin/sdlc/estimates', $data);
    }

    /**
     * Add estimate
     */
    public function add_estimate()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create')) {
            echo json_encode(['success' => false]);
            return;
        }

        $data = $this->input->post();
        $id = $this->dw_sdlc_model->add_estimate($data);

        echo json_encode(['success' => $id ? true : false, 'id' => $id]);
    }

    // =====================================================
    // PROJECT SDLC OVERVIEW
    // =====================================================

    /**
     * Project SDLC overview - all tools for a project
     */
    public function project_overview($project_id)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('projects_model');
        $data['project'] = $this->projects_model->get($project_id);
        if (!$data['project']) {
            show_404();
        }

        // Get all SDLC stats for this project
        $data['sdlc_stats'] = $this->dw_sdlc_model->get_project_sdlc_stats($project_id);

        // Recent items
        $data['requirements'] = $this->dw_sdlc_model->get_requirements($project_id);
        $data['current_scope'] = $data['sdlc_stats']['current_scope'];
        $data['change_requests'] = $this->dw_sdlc_model->get_change_requests($project_id);
        $data['stories'] = $this->dw_sdlc_model->get_user_stories($project_id);
        $data['checklists'] = $this->dw_dev_tools_model->get_project_checklists($project_id);
        $data['specs'] = $this->dw_sdlc_model->get_technical_specs($project_id);

        $data['project_id'] = $project_id;
        $data['title'] = $data['project']->name . ' - ' . _l('disowebs_ops_sdlc_overview');

        $this->load->view('disowebs_ops/admin/sdlc/project_overview', $data);
    }
}
