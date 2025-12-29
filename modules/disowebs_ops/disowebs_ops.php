<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Disowebs Ops
Description: Disowebs operations workspace for Perfex projects
Version: 0.1.0
Requires at least: 3.4.*
*/

define('DISOWEBS_OPS_MODULE_NAME', 'disowebs_ops');
define('DISOWEBS_OPS_MODULE_VERSION', '0.1.0');

$CI = &get_instance();
$CI->load->helper(DISOWEBS_OPS_MODULE_NAME . '/disowebs_ops');

register_language_files(DISOWEBS_OPS_MODULE_NAME, [DISOWEBS_OPS_MODULE_NAME]);

hooks()->add_action('admin_init', 'disowebs_ops_init_menu');
hooks()->add_action('admin_init', 'disowebs_ops_register_permissions');
hooks()->add_action('admin_init', 'disowebs_ops_register_project_tabs');
hooks()->add_action('admin_init', 'disowebs_ops_ensure_options');
hooks()->add_action('after_add_project', 'disowebs_ops_handle_project_created');
hooks()->add_action('estimate_accepted', 'disowebs_ops_handle_estimate_accepted');
hooks()->add_action('proposal_accepted', 'disowebs_ops_handle_proposal_accepted');
hooks()->add_filter('get_dashboard_widgets', 'disowebs_ops_register_dashboard_widgets');
hooks()->add_action('project_status_changed', 'disowebs_ops_handle_project_status_changed');
hooks()->add_action('after_invoice_payment_recorded', 'disowebs_ops_handle_payment_recorded');
hooks()->add_action('after_change_request_approved', 'disowebs_ops_handle_cr_approved');

// Cron jobs
hooks()->add_action('cron_job', 'disowebs_ops_cron_handler');

// Lead qualification hooks
hooks()->add_filter('before_lead_converted', 'disowebs_ops_validate_lead_qualification');
hooks()->add_filter('lead_convert_to_customer_modal_content', 'disowebs_ops_lead_qualification_fields');

register_activation_hook(DISOWEBS_OPS_MODULE_NAME, 'disowebs_ops_activation_hook');
function disowebs_ops_activation_hook()
{
    require_once __DIR__ . '/install.php';
}

register_deactivation_hook(DISOWEBS_OPS_MODULE_NAME, 'disowebs_ops_deactivation_hook');
function disowebs_ops_deactivation_hook()
{
    require_once __DIR__ . '/deactivate.php';
}

register_uninstall_hook(DISOWEBS_OPS_MODULE_NAME, 'disowebs_ops_uninstall_hook');
function disowebs_ops_uninstall_hook()
{
    require_once __DIR__ . '/uninstall.php';
}

function disowebs_ops_init_menu()
{
    if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
        return;
    }

    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('disowebs_ops', [
        'name'     => _l('disowebs_ops'),
        'icon'     => 'fa fa-cubes',
        'href'     => '',
        'collapse' => true,
        'position' => 2,
    ]);

    // Dev Dashboard - Admin/Lead Developer only (top priority)
    // SDLC Tools are integrated as tabs within Dev Dashboard
    if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('disowebs_ops', [
            'slug'     => 'disowebs_ops_dev_dashboard',
            'name'     => _l('disowebs_ops_dev_dashboard'),
            'href'     => admin_url('disowebs_ops/dev_dashboard'),
            'position' => 0,
            'icon'     => 'fa fa-code',
        ]);
    }

    // CEO Dashboard - Admin only
    if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('disowebs_ops', [
            'slug'     => 'disowebs_ops_ceo_dashboard',
            'name'     => _l('disowebs_ops_ceo_dashboard'),
            'href'     => admin_url('disowebs_ops/ceo_dashboard'),
            'position' => 1,
            'icon'     => 'fa fa-tachometer',
        ]);
    }

    // CRM Manager Dashboard
    $CI->app_menu->add_sidebar_children_item('disowebs_ops', [
        'slug'     => 'disowebs_ops_crm_dashboard',
        'name'     => _l('disowebs_ops_crm_dashboard'),
        'href'     => admin_url('disowebs_ops/crm_dashboard'),
        'position' => 2,
        'icon'     => 'fa fa-users',
    ]);

    // TSA Dashboard
    $CI->app_menu->add_sidebar_children_item('disowebs_ops', [
        'slug'     => 'disowebs_ops_tsa_dashboard',
        'name'     => _l('disowebs_ops_tsa_dashboard'),
        'href'     => admin_url('disowebs_ops/tsa_dashboard'),
        'position' => 3,
        'icon'     => 'fa fa-headset',
    ]);

    $CI->app_menu->add_sidebar_children_item('disowebs_ops', [
        'slug'     => 'disowebs_ops_delivery',
        'name'     => _l('disowebs_ops_delivery_engine'),
        'href'     => admin_url('disowebs_ops/delivery'),
        'position' => 5,
        'icon'     => 'fa fa-rocket',
    ]);

    $CI->app_menu->add_sidebar_children_item('disowebs_ops', [
        'slug'     => 'disowebs_ops_scope',
        'name'     => _l('disowebs_ops_scope_change_requests'),
        'href'     => admin_url('disowebs_ops/scope'),
        'position' => 10,
        'icon'     => 'fa fa-code-branch',
    ]);

    $CI->app_menu->add_sidebar_children_item('disowebs_ops', [
        'slug'     => 'disowebs_ops_proof',
        'name'     => _l('disowebs_ops_proof_vault'),
        'href'     => admin_url('disowebs_ops/proof'),
        'position' => 15,
        'icon'     => 'fa fa-shield-alt',
    ]);

    $CI->app_menu->add_sidebar_children_item('disowebs_ops', [
        'slug'     => 'disowebs_ops_reports',
        'name'     => _l('disowebs_ops_reports'),
        'href'     => admin_url('disowebs_ops/reports'),
        'position' => 20,
        'icon'     => 'fa fa-chart-bar',
    ]);

    // Settings menu item - admin only
    if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('disowebs_ops', [
            'slug'     => 'disowebs_ops_settings',
            'name'     => _l('settings'),
            'href'     => admin_url('disowebs_ops/settings'),
            'position' => 99,
            'icon'     => 'fa fa-cog',
        ]);
    }
}

function disowebs_ops_register_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        // Core permissions
        'view' => _l('disowebs_ops_permission_view'),
        'manage' => _l('disowebs_ops_permission_manage'),
        'approve_change_requests' => _l('disowebs_ops_permission_approve_change_requests'),
        
        // Dashboard access permissions
        'access_ceo_dashboard' => _l('disowebs_ops_permission_access_ceo_dashboard'),
        'access_crm_dashboard' => _l('disowebs_ops_permission_access_crm_dashboard'),
        'access_tsa_dashboard' => _l('disowebs_ops_permission_access_tsa_dashboard'),
        
        // Granular CRM Manager permissions
        'manage_pipeline' => _l('disowebs_ops_permission_manage_pipeline'),
        'manage_milestones' => _l('disowebs_ops_permission_manage_milestones'),
        'create_change_requests' => _l('disowebs_ops_permission_create_change_requests'),
        
        // Granular TSA permissions
        'manage_proof' => _l('disowebs_ops_permission_manage_proof'),
        'manage_support' => _l('disowebs_ops_permission_manage_support'),
        'manage_qa' => _l('disowebs_ops_permission_manage_qa'),
        
        // CEO-only permissions
        'manage_phases' => _l('disowebs_ops_permission_manage_phases'),
        'manage_scope' => _l('disowebs_ops_permission_manage_scope'),
        'manage_gates' => _l('disowebs_ops_permission_manage_gates'),
        'view_profit' => _l('disowebs_ops_permission_view_profit'),
    ];

    register_staff_capabilities(DISOWEBS_OPS_MODULE_NAME, $capabilities, _l('disowebs_ops'));
}

function disowebs_ops_register_project_tabs()
{
    if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
        return;
    }

    $CI = &get_instance();

    $CI->app_tabs->add_project_tab('disowebs_ops_phases', [
        'name'     => _l('disowebs_ops_tab_phases'),
        'icon'     => 'fa fa-layer-group',
        'view'     => 'disowebs_ops/admin/project_tabs/phases',
        'position' => 70,
        'visible'  => has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view'),
    ]);

    $CI->app_tabs->add_project_tab('disowebs_ops_scope', [
        'name'     => _l('disowebs_ops_tab_scope'),
        'icon'     => 'fa fa-clipboard',
        'view'     => 'disowebs_ops/admin/project_tabs/scope',
        'position' => 75,
        'visible'  => has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view'),
    ]);

    $CI->app_tabs->add_project_tab('disowebs_ops_change_requests', [
        'name'     => _l('disowebs_ops_tab_change_requests'),
        'icon'     => 'fa fa-random',
        'view'     => 'disowebs_ops/admin/project_tabs/change_requests',
        'position' => 80,
        'visible'  => has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view'),
    ]);

    $CI->app_tabs->add_project_tab('disowebs_ops_proof', [
        'name'     => _l('disowebs_ops_tab_proof'),
        'icon'     => 'fa fa-check-circle',
        'view'     => 'disowebs_ops/admin/project_tabs/proof',
        'position' => 85,
        'visible'  => has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view'),
    ]);

    // V2 Tabs - Gates & Blockers
    $CI->app_tabs->add_project_tab('disowebs_ops_gates', [
        'name'     => _l('disowebs_ops_tab_gates'),
        'icon'     => 'fa fa-shield-alt',
        'view'     => 'disowebs_ops/admin/project_tabs/gates',
        'position' => 90,
        'visible'  => has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view'),
    ]);

    // V2 Tabs - Profit Tracking (CEO only or with view_profit permission)
    $CI->app_tabs->add_project_tab('disowebs_ops_profit', [
        'name'     => _l('disowebs_ops_tab_profit'),
        'icon'     => 'fa fa-chart-line',
        'view'     => 'disowebs_ops/admin/project_tabs/profit',
        'position' => 95,
        'visible'  => disowebs_ops_can_view_profit(),
    ]);
}

function disowebs_ops_ensure_options()
{
    if (!option_exists('disowebs_ops_auto_create_phases')) {
        add_option('disowebs_ops_auto_create_phases', 1);
    }

    if (!option_exists('disowebs_ops_auto_create_milestones')) {
        add_option('disowebs_ops_auto_create_milestones', 0);
    }

    if (!option_exists('disowebs_ops_default_phases')) {
        add_option('disowebs_ops_default_phases', json_encode(disowebs_ops_default_phases_list()));
    }

    if (!option_exists('disowebs_ops_active_project_limit')) {
        add_option('disowebs_ops_active_project_limit', 4);
    }

    // V2 Options - Proof enforcement (mandatory by default)
    if (!option_exists('disowebs_ops_block_project_closure')) {
        add_option('disowebs_ops_block_project_closure', 1);
    } else {
        // Enforce mandatory proof policy - update existing option to 1
        update_option('disowebs_ops_block_project_closure', 1);
    }

    // V2 Options - Lead qualification
    if (!option_exists('disowebs_ops_require_lead_qualification')) {
        add_option('disowebs_ops_require_lead_qualification', 0);
    }

    if (!option_exists('disowebs_ops_required_lead_fields')) {
        add_option('disowebs_ops_required_lead_fields', json_encode(['name', 'email', 'phonenumber', 'company']));
    }

    // V2 Options - Testimonial auto-request
    if (!option_exists('disowebs_ops_auto_testimonial_request')) {
        add_option('disowebs_ops_auto_testimonial_request', 1);
    }
}

function disowebs_ops_handle_project_created($project_id)
{
    if ((int) get_option('disowebs_ops_auto_create_phases') !== 1) {
        return;
    }

    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_project_phases_model');
    $CI->load->model('disowebs_ops/dw_project_milestones_model');

    $existing = $CI->dw_project_phases_model->get_by_project($project_id);
    if (!empty($existing)) {
        return;
    }

    $auto_milestones = (int) get_option('disowebs_ops_auto_create_milestones') === 1;
    $milestone_offset = 0;

    $position = 1;
    foreach (disowebs_ops_get_default_phases() as $phase_name) {
        $phase_name = trim($phase_name);
        if ($phase_name === '') {
            continue;
        }

        $phase_id = $CI->dw_project_phases_model->add([
            'project_id' => (int) $project_id,
            'name'       => $phase_name,
            'position'   => $position,
            'status'     => 'not_started',
        ]);
        if ($auto_milestones && $phase_id) {
            $milestone_offset = disowebs_ops_create_phase_milestones($project_id, $phase_id, $phase_name, $milestone_offset);
        }
        $position++;
    }
}

function disowebs_ops_handle_estimate_accepted($estimate_id)
{
    if (!is_numeric($estimate_id)) {
        return;
    }

    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_scope_snapshots_model');

    if ($CI->dw_scope_snapshots_model->exists_by_source('estimate', (int) $estimate_id)) {
        return;
    }

    $snapshot = disowebs_ops_build_estimate_snapshot((int) $estimate_id);
    if (!$snapshot) {
        return;
    }

    $CI->dw_scope_snapshots_model->add([
        'project_id' => (int) $snapshot['project_id'],
        'source_type' => $snapshot['source_type'],
        'source_id' => $snapshot['source_id'],
        'snapshot_json' => json_encode($snapshot),
    ]);
}

function disowebs_ops_handle_proposal_accepted($proposal_id)
{
    if (!is_numeric($proposal_id)) {
        return;
    }

    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_scope_snapshots_model');

    if ($CI->dw_scope_snapshots_model->exists_by_source('proposal', (int) $proposal_id)) {
        return;
    }

    $snapshot = disowebs_ops_build_proposal_snapshot((int) $proposal_id);
    if (!$snapshot) {
        return;
    }

    $CI->dw_scope_snapshots_model->add([
        'project_id' => (int) $snapshot['project_id'],
        'source_type' => $snapshot['source_type'],
        'source_id' => $snapshot['source_id'],
        'snapshot_json' => json_encode($snapshot),
    ]);
}

function disowebs_ops_register_dashboard_widgets($widgets)
{
    if (!is_staff_member() || !has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
        return $widgets;
    }

    $role_key = disowebs_ops_get_dashboard_role_key(get_staff_user_id());
    if ($role_key === 'ceo') {
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/ceo_weekly_execution',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/ceo_revenue_cashflow',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/ceo_pipeline_forecast',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/ceo_capacity_risk',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/ceo_risk_alerts',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/ceo_growth_flywheel',
            'container' => 'top-12',
        ];
        // V2 Widgets
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/ceo_delivery_v2',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/ceo_profit_engine',
            'container' => 'top-12',
        ];
    } elseif ($role_key === 'crm_manager') {
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/crm_manager_priorities',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/crm_pipeline_hygiene',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/crm_active_projects_overview',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/crm_retainer_tracker',
            'container' => 'top-12',
        ];
        // V2 Widget
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/crm_retention_v2',
            'container' => 'top-12',
        ];
    } elseif ($role_key === 'tsa') {
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/tsa_support_queue',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/tsa_qa_readiness',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/tsa_documentation',
            'container' => 'top-12',
        ];
        $widgets[] = [
            'path'      => 'disowebs_ops/widgets/tsa_proof_backlog',
            'container' => 'top-12',
        ];
    }

    return $widgets;
}

function disowebs_ops_handle_project_status_changed($data)
{
    if (!is_array($data)) {
        return;
    }

    $status = isset($data['status']) ? (int) $data['status'] : 0;
    $project_id = isset($data['project_id']) ? (int) $data['project_id'] : 0;
    if ($status !== 4 || $project_id <= 0) {
        return;
    }

    $CI = &get_instance();

    // Proof enforcement - block project closure without proof entry
    $CI->load->model('disowebs_ops/dw_proof_entries_model');
    $entries = $CI->dw_proof_entries_model->get_by_project($project_id);
    $proof_enforcement_enabled = (int) get_option('disowebs_ops_block_project_closure') === 1;

    if (empty($entries)) {
        if ($proof_enforcement_enabled) {
            // Revert the status change - cannot close without proof
            $CI->db->where('id', $project_id);
            $CI->db->update(db_prefix() . 'projects', [
                'status'        => 2, // Back to In Progress
                'date_finished' => null,
            ]);
            set_alert('danger', _l('disowebs_ops_proof_required_before_close'));
            log_activity('Project Closure Blocked - No Proof Entry [Project ID: ' . $project_id . ']');
            return; // Exit early, don't proceed with other checks
        } else {
            set_alert('warning', _l('disowebs_ops_proof_required_before_close'));
        }
    }

    // Enforce final payment gate for project closure
    $CI->load->model('disowebs_ops/dw_project_gates_model');
    $CI->dw_project_gates_model->calculate_final_payment_status($project_id);
    $gate = $CI->dw_project_gates_model->get_by_project($project_id);
    if ($gate && (int) $gate->final_payment_cleared === 0) {
        // Block project closure - cannot finish without full payment
        $CI->db->where('id', $project_id);
        $CI->db->update(db_prefix() . 'projects', [
            'status'        => 2, // Back to In Progress
            'date_finished' => null,
        ]);
        set_alert('danger', _l('disowebs_ops_final_payment_required_before_close'));
        log_activity('Project Closure Blocked - Final Payment Not Cleared [Project ID: ' . $project_id . ']');
        return; // Exit early, don't proceed with other checks
    }

    // Check training completed
    if ($gate && (int) $gate->training_completed === 0) {
        set_alert('info', _l('disowebs_ops_training_reminder'));
    }

    // Schedule retainer offer check (day 21-30 post-launch)
    disowebs_ops_schedule_retainer_check($project_id);

    // Auto-request testimonial for Growth Flywheel
    disowebs_ops_auto_request_testimonial($project_id);
}

/**
 * Auto-request testimonial when project is completed
 */
function disowebs_ops_auto_request_testimonial($project_id)
{
    $CI = &get_instance();
    
    // Check if auto testimonial is enabled
    if ((int) get_option('disowebs_ops_auto_testimonial_request') !== 1) {
        return;
    }

    $CI->load->model('disowebs_ops/dw_testimonials_model');
    
    // Check if testimonial already requested
    $existing = $CI->dw_testimonials_model->get_by_project($project_id);
    if (!empty($existing)) {
        return;
    }

    // Get project details
    $CI->load->model('projects_model');
    $project = $CI->projects_model->get($project_id);
    if (!$project || empty($project->clientid)) {
        return;
    }

    // Get primary contact
    $CI->db->where('userid', $project->clientid);
    $CI->db->where('is_primary', 1);
    $contact = $CI->db->get(db_prefix() . 'contacts')->row();
    $contact_id = $contact ? $contact->id : null;

    // Create testimonial request
    $testimonial_id = $CI->dw_testimonials_model->request_testimonial(
        $project_id,
        $project->clientid,
        $contact_id,
        get_staff_user_id() ?: 1
    );

    if ($testimonial_id) {
        // Create follow-up task for testimonial
        $task_data = [
            'name'        => _l('disowebs_ops_testimonial_followup_task', $project->name),
            'startdate'   => date('Y-m-d'),
            'duedate'     => date('Y-m-d', strtotime('+7 days')),
            'priority'    => 2,
            'rel_type'    => 'project',
            'rel_id'      => $project_id,
            'description' => _l('disowebs_ops_testimonial_followup_desc'),
            'billable'    => 0,
        ];

        $CI->load->model('tasks_model');
        $task_id = $CI->tasks_model->add($task_data);

        // Assign to project manager or first member
        if ($task_id) {
            $CI->load->model('projects_model');
            $members = $CI->projects_model->get_project_members($project_id);
            if (!empty($members)) {
                $CI->tasks_model->add_task_assignees([
                    'taskid'   => $task_id,
                    'assignee' => $members[0]['staff_id'],
                ]);
            }
        }

        log_activity('Testimonial Auto-Requested [Project ID: ' . $project_id . ', Testimonial ID: ' . $testimonial_id . ']');
    }
}

/**
 * Handle payment recorded - update deposit/final payment gates
 */
function disowebs_ops_handle_payment_recorded($data)
{
    if (!is_array($data) || empty($data['invoiceid'])) {
        return;
    }

    $CI = &get_instance();
    $CI->load->model('invoices_model');

    $invoice = $CI->invoices_model->get($data['invoiceid']);
    if (!$invoice || empty($invoice->project_id)) {
        return;
    }

    $project_id = (int) $invoice->project_id;

    $CI->load->model('disowebs_ops/dw_project_gates_model');
    $CI->dw_project_gates_model->calculate_deposit_status($project_id);
    $CI->dw_project_gates_model->calculate_final_payment_status($project_id);

    // Update profit tracking
    $CI->load->model('disowebs_ops/dw_project_profit_model');
    $CI->dw_project_profit_model->recalculate($project_id);
}

/**
 * Handle CR approved - check for margin alerts
 */
function disowebs_ops_handle_cr_approved($change_request_id)
{
    if (!$change_request_id) {
        return;
    }

    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_change_requests_model');
    $CI->load->model('disowebs_ops/dw_margin_alerts_model');
    $CI->load->model('disowebs_ops/dw_project_profit_model');

    $cr = $CI->dw_change_requests_model->get($change_request_id);
    if (!$cr) {
        return;
    }

    // Alert if CR approved with zero impact
    if ((int) $cr->impact_days === 0 && (float) $cr->impact_cost <= 0.01) {
        $CI->dw_margin_alerts_model->alert_cr_no_impact(
            $cr->project_id,
            $cr->id,
            $cr->title
        );
    }

    // Recalculate project profit
    $CI->dw_project_profit_model->recalculate($cr->project_id);

    // Check for margin erosion
    $profit = $CI->dw_project_profit_model->get_by_project($cr->project_id);
    if ($profit && (float) $profit->expected_margin > 0) {
        $CI->dw_margin_alerts_model->alert_margin_erosion(
            $cr->project_id,
            (float) $profit->cr_impact_total,
            (float) $profit->expected_margin
        );
    }
}

/**
 * Schedule retainer check for newly finished projects
 */
function disowebs_ops_schedule_retainer_check($project_id)
{
    // This creates a pending retainer offer record
    // A cron job or manual check will process these at day 21-30
    $CI = &get_instance();
    $CI->load->model('projects_model');
    $CI->load->model('disowebs_ops/dw_retainer_offers_model');

    $project = $CI->projects_model->get($project_id);
    if (!$project || empty($project->clientid)) {
        return;
    }

    // Check if offer already exists
    $existing = $CI->dw_retainer_offers_model->get_latest_by_project($project_id);
    if ($existing) {
        return;
    }

    // Create pending offer (will be activated at day 21)
    $CI->dw_retainer_offers_model->create_offer(
        $project_id,
        $project->clientid,
        null,
        null,
        null,
        true // auto-generated
    );
}

/**
 * Cron handler for Disowebs Ops module
 * Runs daily via Perfex CRM cron system
 */
function disowebs_ops_cron_handler()
{
    $CI = &get_instance();
    
    // Process retainer offers (day 21-30 post-launch)
    disowebs_ops_process_retainer_offers();
    
    // Process follow-up reminders
    disowebs_ops_process_followup_reminders();
    
    // Check for projects needing profit recalculation
    disowebs_ops_recalculate_stale_profits();
    
    // Log cron run
    log_activity('Disowebs Ops cron job executed [' . date('Y-m-d H:i:s') . ']');
}

/**
 * Process retainer offers at day 21-30 post-launch
 */
function disowebs_ops_process_retainer_offers()
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_retainer_offers_model');
    $CI->load->model('projects_model');
    
    // Get projects finished 21-30 days ago without offers
    $offers = $CI->dw_retainer_offers_model->get_pending_for_activation();
    
    foreach ($offers as $offer) {
        // Skip if already offered
        if ($offer['status'] !== 'pending') {
            continue;
        }
        
        $days_since = $offer['days_since_launch'];
        
        // Day 21: Mark as ready to offer
        if ($days_since >= 21 && $days_since <= 30 && !$offer['reminder_sent']) {
            $CI->dw_retainer_offers_model->mark_ready_to_offer($offer['id']);
            
            // Create task/reminder for CRM manager to send retainer offer
            disowebs_ops_create_retainer_reminder_task($offer['project_id'], $offer['client_id']);
        }
        
        // Day 30+: Mark as expired if no action taken
        if ($days_since > 30 && $offer['status'] === 'pending') {
            $CI->dw_retainer_offers_model->mark_expired($offer['id']);
        }
    }
}

/**
 * Create a task reminder for CRM to send retainer offer
 */
function disowebs_ops_create_retainer_reminder_task($project_id, $client_id)
{
    $CI = &get_instance();
    $CI->load->model('projects_model');
    $CI->load->model('tasks_model');
    
    $project = $CI->projects_model->get($project_id);
    if (!$project) {
        return;
    }
    
    // Check if task already exists
    $CI->db->where('rel_type', 'project');
    $CI->db->where('rel_id', $project_id);
    $CI->db->like('name', '[Retainer]');
    $existing = $CI->db->get(db_prefix() . 'tasks')->row();
    if ($existing) {
        return;
    }
    
    // Find CRM manager staff
    $crm_staff = disowebs_ops_get_crm_manager_staff_id();
    
    $task_data = [
        'name' => '[Retainer] Send retainer offer to ' . $project->name,
        'description' => 'Project launched 21+ days ago. Time to reach out about retainer services.',
        'rel_type' => 'project',
        'rel_id' => $project_id,
        'priority' => 2, // Medium
        'startdate' => date('Y-m-d'),
        'duedate' => date('Y-m-d', strtotime('+7 days')),
        'is_public' => 0,
        'billable' => 0,
        'hourly_rate' => 0,
    ];
    
    $task_id = $CI->tasks_model->add($task_data, true);
    
    if ($task_id && $crm_staff) {
        $CI->tasks_model->add_task_assignees(['assignees' => [$crm_staff]], $task_id);
    }
}

/**
 * Process follow-up reminders (Day 1/3/7/14 cadence)
 */
function disowebs_ops_process_followup_reminders()
{
    $CI = &get_instance();
    
    // Get proposals awaiting response without recent follow-up
    $followup_days = [1, 3, 7, 14];
    
    $CI->db->select('p.id, p.subject, p.project_id, p.datecreated, p.date, c.company as client_name, c.userid as client_id');
    $CI->db->from(db_prefix() . 'proposals p');
    $CI->db->join(db_prefix() . 'clients c', 'c.userid = p.rel_id AND p.rel_type = "customer"', 'left');
    $CI->db->where('p.status', 4); // Sent status
    $CI->db->where('p.date >=', date('Y-m-d', strtotime('-30 days'))); // Within last 30 days
    $proposals = $CI->db->get()->result_array();
    
    foreach ($proposals as $proposal) {
        $days_since_sent = (int) floor((time() - strtotime($proposal['date'])) / 86400);
        
        // Check if we should send a follow-up today
        if (in_array($days_since_sent, $followup_days)) {
            disowebs_ops_create_followup_task($proposal, $days_since_sent);
        }
    }
    
    // Same for estimates
    $CI->db->select('e.id, e.number, e.clientid, e.datecreated, e.date, c.company as client_name');
    $CI->db->from(db_prefix() . 'estimates e');
    $CI->db->join(db_prefix() . 'clients c', 'c.userid = e.clientid', 'left');
    $CI->db->where('e.status', 2); // Sent status
    $CI->db->where('e.date >=', date('Y-m-d', strtotime('-30 days')));
    $estimates = $CI->db->get()->result_array();
    
    foreach ($estimates as $estimate) {
        $days_since_sent = (int) floor((time() - strtotime($estimate['date'])) / 86400);
        
        if (in_array($days_since_sent, $followup_days)) {
            disowebs_ops_create_estimate_followup_task($estimate, $days_since_sent);
        }
    }
}

/**
 * Create follow-up task for proposal
 */
function disowebs_ops_create_followup_task($proposal, $days)
{
    $CI = &get_instance();
    $CI->load->model('tasks_model');
    
    $task_name = "[Follow-up Day {$days}] Proposal: " . $proposal['subject'];
    
    // Check if task already exists
    $CI->db->where('name', $task_name);
    $CI->db->where('rel_type', 'customer');
    $CI->db->where('rel_id', $proposal['client_id']);
    $existing = $CI->db->get(db_prefix() . 'tasks')->row();
    if ($existing) {
        return;
    }
    
    $crm_staff = disowebs_ops_get_crm_manager_staff_id();
    
    $task_data = [
        'name' => $task_name,
        'description' => "Day {$days} follow-up reminder for proposal sent to " . ($proposal['client_name'] ?: 'client') . ".\n\nProposal ID: {$proposal['id']}",
        'rel_type' => 'customer',
        'rel_id' => $proposal['client_id'] ?: 0,
        'priority' => $days >= 7 ? 3 : 2, // High priority after day 7
        'startdate' => date('Y-m-d'),
        'duedate' => date('Y-m-d'),
        'is_public' => 0,
        'billable' => 0,
        'hourly_rate' => 0,
    ];
    
    $task_id = $CI->tasks_model->add($task_data, true);
    
    if ($task_id && $crm_staff) {
        $CI->tasks_model->add_task_assignees(['assignees' => [$crm_staff]], $task_id);
    }
}

/**
 * Create follow-up task for estimate
 */
function disowebs_ops_create_estimate_followup_task($estimate, $days)
{
    $CI = &get_instance();
    $CI->load->model('tasks_model');
    
    $task_name = "[Follow-up Day {$days}] Estimate #" . format_estimate_number($estimate['id']);
    
    // Check if task already exists
    $CI->db->where('name', $task_name);
    $CI->db->where('rel_type', 'customer');
    $CI->db->where('rel_id', $estimate['clientid']);
    $existing = $CI->db->get(db_prefix() . 'tasks')->row();
    if ($existing) {
        return;
    }
    
    $crm_staff = disowebs_ops_get_crm_manager_staff_id();
    
    $task_data = [
        'name' => $task_name,
        'description' => "Day {$days} follow-up reminder for estimate sent to " . ($estimate['client_name'] ?: 'client') . ".",
        'rel_type' => 'customer',
        'rel_id' => $estimate['clientid'] ?: 0,
        'priority' => $days >= 7 ? 3 : 2,
        'startdate' => date('Y-m-d'),
        'duedate' => date('Y-m-d'),
        'is_public' => 0,
        'billable' => 0,
        'hourly_rate' => 0,
    ];
    
    $task_id = $CI->tasks_model->add($task_data, true);
    
    if ($task_id && $crm_staff) {
        $CI->tasks_model->add_task_assignees(['assignees' => [$crm_staff]], $task_id);
    }
}

/**
 * Recalculate profits for projects with stale data
 */
function disowebs_ops_recalculate_stale_profits()
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_project_profit_model');
    
    $stale_projects = $CI->dw_project_profit_model->get_projects_needing_recalculation();
    
    foreach ($stale_projects as $project) {
        $CI->dw_project_profit_model->recalculate($project['project_id']);
    }
}

/**
 * Get CRM manager staff ID
 */
function disowebs_ops_get_crm_manager_staff_id()
{
    $CI = &get_instance();
    
    // Try to find staff with CRM manager role
    $CI->db->select('staffid');
    $CI->db->from(db_prefix() . 'staff');
    $CI->db->where('role', (int) get_option('disowebs_ops_crm_manager_role'));
    $CI->db->where('active', 1);
    $staff = $CI->db->get()->row();
    
    if ($staff) {
        return (int) $staff->staffid;
    }
    
    // Fallback: return first active admin
    $CI->db->select('staffid');
    $CI->db->from(db_prefix() . 'staff');
    $CI->db->where('admin', 1);
    $CI->db->where('active', 1);
    $CI->db->limit(1);
    $admin = $CI->db->get()->row();
    
    return $admin ? (int) $admin->staffid : null;
}

/**
 * Validate lead qualification before conversion
 */
function disowebs_ops_validate_lead_qualification($data)
{
    $CI = &get_instance();
    
    // Check if lead qualification is enabled
    if ((int) get_option('disowebs_ops_require_lead_qualification') !== 1) {
        return $data;
    }
    
    $lead_id = isset($data['leadid']) ? (int) $data['leadid'] : 0;
    if (!$lead_id) {
        return $data;
    }
    
    // Get lead
    $CI->load->model('leads_model');
    $lead = $CI->leads_model->get($lead_id);
    
    if (!$lead) {
        return $data;
    }
    
    $required_fields = disowebs_ops_get_required_qualification_fields();
    $missing_fields = [];
    
    foreach ($required_fields as $field => $label) {
        $value = isset($lead->{$field}) ? trim($lead->{$field}) : '';
        if (empty($value)) {
            $missing_fields[] = $label;
        }
    }
    
    // Check custom fields if configured
    $required_custom_fields = get_option('disowebs_ops_required_lead_custom_fields');
    if ($required_custom_fields) {
        $custom_field_ids = array_filter(array_map('trim', explode(',', $required_custom_fields)));
        foreach ($custom_field_ids as $cf_id) {
            $cf_value = get_custom_field_value($lead_id, $cf_id, 'leads');
            if (empty(trim($cf_value))) {
                $missing_fields[] = get_custom_field_name($cf_id);
            }
        }
    }
    
    if (!empty($missing_fields)) {
        // Block conversion with error
        $data['qualification_error'] = _l('disowebs_ops_lead_missing_fields') . ': ' . implode(', ', $missing_fields);
    }
    
    return $data;
}

/**
 * Get required qualification fields for leads
 */
function disowebs_ops_get_required_qualification_fields()
{
    $default_fields = [
        'name' => _l('lead_name'),
        'email' => _l('lead_email'),
        'phonenumber' => _l('lead_phone'),
    ];
    
    $custom_required = get_option('disowebs_ops_required_lead_fields');
    if ($custom_required) {
        $fields = json_decode($custom_required, true);
        if (is_array($fields)) {
            return $fields;
        }
    }
    
    return $default_fields;
}

/**
 * Add qualification fields hint to lead conversion modal
 */
function disowebs_ops_lead_qualification_fields($content)
{
    if ((int) get_option('disowebs_ops_require_lead_qualification') !== 1) {
        return $content;
    }
    
    $required_fields = disowebs_ops_get_required_qualification_fields();
    
    $hint = '<div class="alert alert-info mtop10">';
    $hint .= '<strong><i class="fa fa-info-circle"></i> ' . _l('disowebs_ops_qualification_gate') . '</strong><br>';
    $hint .= _l('disowebs_ops_qualification_required_fields') . ': ';
    $hint .= '<em>' . implode(', ', $required_fields) . '</em>';
    $hint .= '</div>';
    
    return $content . $hint;
}
