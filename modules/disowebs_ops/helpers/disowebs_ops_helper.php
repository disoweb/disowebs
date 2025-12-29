<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Format number in compact form (1k, 500k, 1.2m, etc)
 * @param float $number The number to format
 * @param mixed $currency Currency symbol string or currency object from get_base_currency()
 * @return string Formatted number
 */
function disowebs_ops_format_compact_money($number, $currency = '')
{
    $number = (float) $number;
    
    // Handle currency object or string
    $prefix = '';
    if (is_object($currency) && isset($currency->symbol)) {
        $prefix = $currency->symbol;
    } elseif (is_string($currency)) {
        $prefix = $currency;
    }
    
    if ($number < 0) {
        $prefix = '-' . $prefix;
        $number = abs($number);
    }
    
    if ($number >= 1000000000) {
        return $prefix . number_format($number / 1000000000, 2) . 'B';
    } elseif ($number >= 1000000) {
        return $prefix . number_format($number / 1000000, 2) . 'M';
    } elseif ($number >= 1000) {
        return $prefix . number_format($number / 1000, 0) . 'K';
    } else {
        return $prefix . number_format($number, 0);
    }
}

function disowebs_ops_get_staff_id()
{
    return is_staff_logged_in() ? get_staff_user_id() : 0;
}

// ============================================
// V2 WORKFLOW HELPER FUNCTIONS
// ============================================

/**
 * Check if project can proceed to Build phase (deposit gate)
 * Policy: 60-70% deposit required before Build
 */
function disowebs_ops_can_proceed_to_build($project_id)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_project_gates_model');
    return $CI->dw_project_gates_model->can_proceed_to_build($project_id);
}

/**
 * Check if handover credentials can be released (final payment gate)
 * Policy: Full payment required before handover
 */
function disowebs_ops_can_release_handover($project_id)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_project_gates_model');
    return $CI->dw_project_gates_model->can_release_handover($project_id);
}

/**
 * Get project gate status summary
 */
function disowebs_ops_get_project_gate_status($project_id)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_project_gates_model');
    
    $gate = $CI->dw_project_gates_model->get_by_project($project_id);
    if (!$gate) {
        return [
            'deposit_cleared' => false,
            'deposit_percent' => 0,
            'final_payment_cleared' => false,
            'final_payment_percent' => 0,
            'handover_released' => false,
            'training_completed' => false,
        ];
    }

    $deposit_percent = (float) $gate->deposit_required > 0 
        ? min(100, ((float) $gate->deposit_paid / (float) $gate->deposit_required) * 100) 
        : 0;
    
    $final_percent = (float) $gate->final_payment_required > 0 
        ? min(100, ((float) $gate->final_payment_paid / (float) $gate->final_payment_required) * 100) 
        : 0;

    return [
        'deposit_cleared' => (int) $gate->deposit_cleared === 1,
        'deposit_percent' => round($deposit_percent, 1),
        'deposit_required' => (float) $gate->deposit_required,
        'deposit_paid' => (float) $gate->deposit_paid,
        'final_payment_cleared' => (int) $gate->final_payment_cleared === 1,
        'final_payment_percent' => round($final_percent, 1),
        'final_payment_required' => (float) $gate->final_payment_required,
        'final_payment_paid' => (float) $gate->final_payment_paid,
        'handover_released' => (int) $gate->handover_released === 1,
        'training_completed' => (int) $gate->training_completed === 1,
    ];
}

/**
 * Get blocker count for a milestone
 */
function disowebs_ops_get_milestone_blocker_count($milestone_id)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_milestone_blockers_model');
    return $CI->dw_milestone_blockers_model->count_unresolved_by_milestone($milestone_id);
}

/**
 * Get unresolved blockers for a project
 */
function disowebs_ops_get_project_blockers($project_id)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_milestone_blockers_model');
    return $CI->dw_milestone_blockers_model->get_unresolved_by_project($project_id);
}

/**
 * Check if project has pending blockers
 */
function disowebs_ops_project_has_blockers($project_id)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_milestone_blockers_model');
    return $CI->dw_milestone_blockers_model->count_unresolved_by_project($project_id) > 0;
}

/**
 * Get project profit summary with all required fields for views
 */
function disowebs_ops_get_project_profit_summary($project_id)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_project_profit_model');
    
    $profit = $CI->dw_project_profit_model->get_by_project($project_id);
    if (!$profit) {
        // Return default structure for views
        return [
            'expected_revenue' => 0.0,
            'actual_revenue' => 0.0,
            'expected_cost' => 0.0,
            'actual_cost' => 0.0,
            'expected_margin' => 0.0,
            'actual_margin' => 0.0,
            'net_profit' => 0.0,
            'margin_percent' => 0.0,
            'cr_impact_total' => 0.0,
            'cr_revenue_impact' => 0.0,
            'cr_cost_impact' => 0.0,
            'effort_hours' => 0.0,
            'estimated_hours' => 0.0,
            'actual_hours' => 0.0,
        ];
    }

    // Calculate net profit (actual revenue - actual cost)
    $net_profit = (float) $profit->actual_revenue - (float) $profit->actual_cost;

    return [
        'expected_revenue' => (float) $profit->expected_revenue,
        'actual_revenue' => (float) $profit->actual_revenue,
        'expected_cost' => (float) $profit->expected_cost,
        'actual_cost' => (float) $profit->actual_cost,
        'expected_margin' => (float) $profit->expected_margin,
        'actual_margin' => (float) $profit->actual_margin,
        'net_profit' => $net_profit,
        'margin_percent' => (float) $profit->margin_percent,
        'cr_impact_total' => (float) $profit->cr_impact_total,
        'cr_revenue_impact' => 0.0, // CRs typically add cost, not revenue
        'cr_cost_impact' => (float) $profit->cr_impact_total,
        'effort_hours' => (float) $profit->estimated_hours,
        'estimated_hours' => (float) $profit->estimated_hours,
        'actual_hours' => (float) $profit->actual_hours,
    ];
}

/**
 * Get current week demo status for project
 */
function disowebs_ops_get_project_demo_status($project_id)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_weekly_demos_model');
    
    $demo = $CI->dw_weekly_demos_model->get_current_week_demo($project_id);
    if (!$demo) {
        return [
            'scheduled' => false,
            'completed' => false,
            'demo_date' => null,
        ];
    }

    return [
        'scheduled' => (int) $demo->demo_scheduled === 1,
        'completed' => (int) $demo->demo_completed === 1,
        'demo_date' => $demo->demo_date,
    ];
}

/**
 * Get retainer offer status for project
 */
function disowebs_ops_get_project_retainer_status($project_id)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_retainer_offers_model');
    
    $offer = $CI->dw_retainer_offers_model->get_latest_by_project($project_id);
    if (!$offer) {
        return [
            'offered' => false,
            'accepted' => false,
            'declined' => false,
            'days_since_launch' => 0,
        ];
    }

    return [
        'offered' => in_array($offer->status, ['offered', 'accepted', 'declined']),
        'accepted' => (int) $offer->accepted === 1,
        'declined' => (int) $offer->declined === 1,
        'days_since_launch' => (int) $offer->days_since_launch,
        'offer_date' => $offer->offer_date,
    ];
}

/**
 * Get active margin alerts for project
 */
function disowebs_ops_get_project_margin_alerts($project_id)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_margin_alerts_model');
    return $CI->dw_margin_alerts_model->get_unacknowledged($project_id);
}

/**
 * Get testimonial status for project
 */
function disowebs_ops_get_project_testimonial_status($project_id)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_testimonials_model');
    
    $testimonials = $CI->dw_testimonials_model->get_by_project($project_id);
    if (empty($testimonials)) {
        return [
            'requested' => false,
            'received' => false,
            'approved' => false,
            'count' => 0,
            'testimonials' => [],
        ];
    }

    $received = 0;
    $approved = 0;
    $pending_approval = 0;
    foreach ($testimonials as $t) {
        if ($t['status'] === 'received') {
            $received++;
            $pending_approval++;
        }
        if ($t['status'] === 'approved') {
            $approved++;
        }
    }

    return [
        'requested' => count($testimonials) > 0,
        'received' => $received > 0,
        'approved' => $approved > 0,
        'count' => count($testimonials),
        'received_count' => $received,
        'approved_count' => $approved,
        'pending_approval_count' => $pending_approval,
        'testimonials' => $testimonials,
    ];
}

// ============================================
// ORIGINAL HELPER FUNCTIONS
// ============================================

function disowebs_ops_get_week_range($date = null)
{
    $date = $date ?: date('Y-m-d');

    $dt = new DateTime($date);
    $dt->setTime(0, 0, 0);

    $start = clone $dt;
    $start->modify('monday this week');

    $end = clone $start;
    $end->modify('sunday this week');

    return [
        'week_start' => $start->format('Y-m-d'),
        'week_end' => $end->format('Y-m-d'),
    ];
}

function disowebs_ops_get_current_week_range()
{
    return disowebs_ops_get_week_range();
}

function disowebs_ops_get_date_range_for_key($key = 'week')
{
    $key = strtolower(trim((string) $key));
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    $end = clone $today;

    switch ($key) {
        case 'day':
            $start = clone $end;
            break;
        case 'month':
            $start = clone $end;
            $start->modify('-29 days');
            break;
        case 'quarter':
            $start = clone $end;
            $start->modify('-89 days');
            break;
        case 'year':
            $start = clone $end;
            $start->modify('-364 days');
            break;
        case 'week':
        default:
            $key = 'week';
            $start = clone $end;
            $start->modify('-6 days');
            break;
    }

    return [
        'key' => $key,
        'start' => $start->format('Y-m-d'),
        'end' => $end->format('Y-m-d'),
    ];
}

function disowebs_ops_default_phases_list()
{
    return ['Discovery', 'Build', 'Deploy', 'Support'];
}

function disowebs_ops_get_default_phases()
{
    $stored = get_option('disowebs_ops_default_phases');
    if (!$stored) {
        return disowebs_ops_default_phases_list();
    }

    $decoded = json_decode($stored, true);
    if (is_array($decoded) && count($decoded) > 0) {
        return $decoded;
    }

    $lines = preg_split('/\\r\\n|\\r|\\n/', (string) $stored);
    $lines = array_filter(array_map('trim', $lines));

    return count($lines) > 0 ? array_values($lines) : disowebs_ops_default_phases_list();
}

function disowebs_ops_default_phases_text()
{
    return implode(PHP_EOL, disowebs_ops_get_default_phases());
}

function disowebs_ops_normalize_phase_lines($raw)
{
    $lines = preg_split('/\\r\\n|\\r|\\n/', (string) $raw);
    $lines = array_values(array_filter(array_map('trim', $lines)));

    return count($lines) > 0 ? $lines : disowebs_ops_default_phases_list();
}

function disowebs_ops_default_phase_milestone_templates()
{
    return [
        'discovery' => [
            'Kickoff & requirements',
            'Sitemap & scope confirmation',
            'Design direction approved',
        ],
        'build' => [
            'Dev environment setup',
            'Core build complete',
            'Internal QA pass',
        ],
        'deploy' => [
            'Staging deployment',
            'Production launch',
            'Handover checklist',
        ],
        'support' => [
            'Post-launch monitoring',
            'Bugfix round',
            'Training & handoff',
        ],
    ];
}

function disowebs_ops_get_phase_milestone_titles($phase_name)
{
    $phase_name = trim((string) $phase_name);
    if ($phase_name === '') {
        return [];
    }

    $phase_key = strtolower($phase_name);
    $templates = disowebs_ops_default_phase_milestone_templates();

    if (strpos($phase_key, 'discover') !== false) {
        return $templates['discovery'];
    }
    if (strpos($phase_key, 'build') !== false) {
        return $templates['build'];
    }
    if (strpos($phase_key, 'deploy') !== false || strpos($phase_key, 'launch') !== false) {
        return $templates['deploy'];
    }
    if (strpos($phase_key, 'support') !== false || strpos($phase_key, 'maint') !== false) {
        return $templates['support'];
    }

    return ['Complete ' . $phase_name . ' deliverables'];
}

function disowebs_ops_week_range_for_offset($offset_weeks = 0)
{
    $offset_weeks = (int) $offset_weeks;
    $date = new DateTime();
    if ($offset_weeks !== 0) {
        $date->modify('+' . ($offset_weeks * 7) . ' days');
    }

    return disowebs_ops_get_week_range($date->format('Y-m-d'));
}

function disowebs_ops_create_phase_milestones($project_id, $phase_id, $phase_name, $offset_weeks = 0)
{
    $CI = &get_instance();
    $CI->load->model('disowebs_ops/dw_project_milestones_model');

    if (!$project_id || !$phase_id) {
        return (int) $offset_weeks;
    }

    if ($CI->dw_project_milestones_model->exists_for_phase($project_id, $phase_id)) {
        return (int) $offset_weeks;
    }

    $offset_weeks = (int) $offset_weeks;
    $titles = disowebs_ops_get_phase_milestone_titles($phase_name);

    foreach ($titles as $title) {
        $range = disowebs_ops_week_range_for_offset($offset_weeks);
        $insert_id = $CI->dw_project_milestones_model->add([
            'project_id' => (int) $project_id,
            'phase_id' => (int) $phase_id,
            'title' => $title,
            'description' => null,
            'week_start' => $range['week_start'],
            'week_end' => $range['week_end'],
            'status' => 'planned',
        ]);
        if ($insert_id) {
            $offset_weeks++;
        }
    }

    return $offset_weeks;
}

function disowebs_ops_proof_upload_path($proof_entry_id = null)
{
    $suffix = $proof_entry_id ? ((int) $proof_entry_id . '/') : '';
    return FCPATH . 'uploads/disowebs_ops/proof/' . $suffix;
}

function disowebs_ops_proof_upload_url($proof_entry_id, $filename)
{
    return base_url('uploads/disowebs_ops/proof/' . (int) $proof_entry_id . '/' . $filename);
}

function disowebs_ops_ensure_proof_upload_base()
{
    $base_path = rtrim(FCPATH, '/') . '/uploads/disowebs_ops/proof/';
    if (!is_dir($base_path)) {
        mkdir($base_path, 0755, true);
        fopen($base_path . 'index.html', 'w');
    }
}

function disowebs_ops_handle_proof_uploads($proof_entry_id, $index_name = 'proof_files')
{
    $uploads = [
        'files' => [],
        'errors' => [],
    ];

    $CI = &get_instance();
    if (!function_exists('_upload_extension_allowed')) {
        $CI->load->helper('upload');
    }

    if (!isset($_FILES[$index_name])) {
        return $uploads;
    }

    _file_attachments_index_fix($index_name);

    $path = disowebs_ops_proof_upload_path($proof_entry_id);
    disowebs_ops_ensure_proof_upload_base();
    $max_size = file_upload_max_size();

    for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
        if (isset($_FILES[$index_name]['error'][$i]) && _perfex_upload_error($_FILES[$index_name]['error'][$i])) {
            $uploads['errors'][] = _perfex_upload_error($_FILES[$index_name]['error'][$i]);
            continue;
        }

        if (!isset($_FILES[$index_name]['name'][$i]) || $_FILES[$index_name]['name'][$i] === '') {
            continue;
        }

        if (!$tmpFilePath = $_FILES[$index_name]['tmp_name'][$i]) {
            continue;
        }

        if (!$tmpFilePath || $tmpFilePath === '') {
            continue;
        }

        if (!empty($_FILES[$index_name]['size'][$i]) && $max_size > 0 && $_FILES[$index_name]['size'][$i] > $max_size) {
            $uploads['errors'][] = _l('file_exceeds_max_filesize');
            continue;
        }

        if (!_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
            $uploads['errors'][] = _l('disowebs_ops_proof_file_type_invalid');
            continue;
        }

        _maybe_create_upload_path($path);

        $filename = unique_filename($path, $_FILES[$index_name]['name'][$i]);
        $newFilePath = $path . $filename;

        if (move_uploaded_file($tmpFilePath, $newFilePath)) {
            $uploads['files'][] = [
                'file_path' => $filename,
                'file_type' => $_FILES[$index_name]['type'][$i],
            ];
        }
    }

    return $uploads;
}

function disowebs_ops_build_estimate_snapshot($estimate_id, $project_id = null)
{
    $CI = &get_instance();
    $CI->load->model('estimates_model');

    $estimate = $CI->estimates_model->get($estimate_id);
    if (!$estimate || (int) $estimate->status !== 4) {
        return null;
    }

    $estimate_project_id = isset($estimate->project_id) ? (int) $estimate->project_id : 0;
    if ($project_id !== null && (int) $project_id !== $estimate_project_id) {
        return null;
    }

    if ($estimate_project_id <= 0) {
        return null;
    }

    $client_name = isset($estimate->client->company) ? $estimate->client->company : '';
    $items = disowebs_ops_normalize_items($estimate->items ?? []);

    return [
        'source_type' => 'estimate',
        'source_id' => (int) $estimate_id,
        'number' => format_estimate_number($estimate_id),
        'subject' => $estimate->number,
        'captured_at' => date('Y-m-d H:i:s'),
        'project_id' => $estimate_project_id,
        'client_id' => (int) $estimate->clientid,
        'client_name' => $client_name,
        'total' => $estimate->total,
        'subtotal' => $estimate->subtotal,
        'total_tax' => $estimate->total_tax ?? null,
        'discount_total' => $estimate->discount_total ?? null,
        'discount_percent' => $estimate->discount_percent ?? null,
        'discount_type' => $estimate->discount_type ?? null,
        'currency_id' => $estimate->currency,
        'currency_name' => $estimate->currency_name ?? '',
        'terms' => $estimate->terms ?? '',
        'client_note' => $estimate->clientnote ?? '',
        'admin_note' => $estimate->adminnote ?? '',
        'items' => $items,
    ];
}

function disowebs_ops_build_proposal_snapshot($proposal_id, $project_id = null)
{
    $CI = &get_instance();
    $CI->load->model('proposals_model');

    $proposal = $CI->proposals_model->get($proposal_id);
    if (!$proposal || (int) $proposal->status !== 3) {
        return null;
    }

    $proposal_project_id = isset($proposal->project_id) ? (int) $proposal->project_id : 0;
    if ($project_id !== null && (int) $project_id !== $proposal_project_id) {
        return null;
    }

    if ($proposal_project_id <= 0) {
        return null;
    }

    $items = disowebs_ops_normalize_items($proposal->items ?? []);

    return [
        'source_type' => 'proposal',
        'source_id' => (int) $proposal_id,
        'number' => format_proposal_number($proposal_id),
        'subject' => $proposal->subject ?? '',
        'captured_at' => date('Y-m-d H:i:s'),
        'project_id' => $proposal_project_id,
        'client_id' => (int) $proposal->rel_id,
        'client_name' => $proposal->proposal_to ?? '',
        'total' => $proposal->total ?? null,
        'currency_id' => $proposal->currency,
        'currency_name' => $proposal->currency_name ?? '',
        'terms' => $proposal->content ?? '',
        'items' => $items,
    ];
}

function disowebs_ops_normalize_items($items)
{
    $normalized = [];

    foreach ($items as $item) {
        $normalized[] = [
            'description' => $item['description'] ?? '',
            'long_description' => $item['long_description'] ?? '',
            'qty' => $item['qty'] ?? null,
            'rate' => $item['rate'] ?? null,
            'unit' => $item['unit'] ?? '',
            'taxname' => $item['taxname'] ?? '',
            'taxrate' => $item['taxrate'] ?? '',
        ];
    }

    return $normalized;
}

function disowebs_ops_get_staff_role_name($staff_id = null)
{
    $staff_id = $staff_id ?: get_staff_user_id();
    if (!$staff_id) {
        return '';
    }

    $staff = get_staff($staff_id);
    if (!$staff || !isset($staff->role)) {
        return '';
    }

    $CI = &get_instance();
    if (!class_exists('roles_model', false)) {
        $CI->load->model('roles_model');
    }

    $role = $CI->roles_model->get($staff->role);
    return $role && isset($role->name) ? (string) $role->name : '';
}

function disowebs_ops_get_dashboard_role_key($staff_id = null)
{
    if (!is_staff_logged_in()) {
        return '';
    }

    if (has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'approve_change_requests')) {
        return 'ceo';
    }

    $role_name = strtolower(trim(disowebs_ops_get_staff_role_name($staff_id)));
    if ($role_name === '') {
        return is_admin() ? 'ceo' : '';
    }

    $role_map = [
        'ceo' => ['ceo', 'owner', 'founder', 'principal', 'admin', 'administrator'],
        'crm_manager' => ['crm', 'client manager', 'account manager', 'ops manager', 'operations manager'],
        'tsa' => ['tsa', 'support', 'technical support', 'assistant', 'qa'],
    ];

    foreach ($role_map as $key => $needles) {
        foreach ($needles as $needle) {
            if (strpos($role_name, $needle) !== false) {
                return $key;
            }
        }
    }

    return is_admin() ? 'ceo' : '';
}

function disowebs_ops_is_ceo($staff_id = null)
{
    if (!is_staff_logged_in()) {
        return false;
    }

    if (has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'approve_change_requests')) {
        return true;
    }

    $staff_id = $staff_id ?: get_staff_user_id();
    return $staff_id ? is_admin($staff_id) : false;
}

function disowebs_ops_can_manage_phases($staff_id = null)
{
    // CEO-only permission - requires explicit manage_phases capability
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage_phases');
}

function disowebs_ops_can_manage_milestones($staff_id = null)
{
    // CEO/CRM/TSA - requires manage_milestones capability
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage_milestones');
}

function disowebs_ops_can_update_milestone_status($staff_id = null)
{
    return disowebs_ops_can_manage_milestones($staff_id);
}

function disowebs_ops_can_delete_milestones($staff_id = null)
{
    // CEO/CRM only - admins can always delete, others need manage_milestones + manage
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    // CRM can delete but TSA cannot
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage_milestones') 
        && has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage');
}

function disowebs_ops_can_manage_change_requests($staff_id = null)
{
    // CEO/CRM only - requires create_change_requests capability
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create_change_requests');
}

function disowebs_ops_can_approve_change_requests($staff_id = null)
{
    return disowebs_ops_is_ceo($staff_id);
}

function disowebs_ops_can_delete_change_requests($staff_id = null)
{
    // CEO only - requires manage_scope capability
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage_scope');
}

function disowebs_ops_can_create_change_request_task($staff_id = null)
{
    // CEO/CRM only - requires create_change_requests capability
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'create_change_requests');
}

function disowebs_ops_can_mark_change_request_implemented($staff_id = null)
{
    // All roles can mark implemented - just needs manage permission
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage');
}

function disowebs_ops_can_manage_proof_entries($staff_id = null)
{
    // TSA primary, but all roles can create - requires manage_proof capability
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage_proof');
}

function disowebs_ops_can_delete_proof_entries($staff_id = null)
{
    // CEO only - requires manage_scope capability (same as delete CR)
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage_scope');
}

function disowebs_ops_can_delete_proof_files($staff_id = null)
{
    return disowebs_ops_can_manage_proof_entries($staff_id);
}

/**
 * Check if staff can manage gates (deposit/final payment clearing)
 * CEO-only permission - requires manage_gates capability
 */
function disowebs_ops_can_manage_gates($staff_id = null)
{
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage_gates');
}

/**
 * Check if staff can view profit data
 * CEO-only permission - requires view_profit capability
 */
function disowebs_ops_can_view_profit($staff_id = null)
{
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view_profit');
}

/**
 * Check if staff can manage pipeline (CRM Manager capability)
 */
function disowebs_ops_can_manage_pipeline($staff_id = null)
{
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage_pipeline');
}

/**
 * Check if staff can manage support tickets (TSA capability)
 */
function disowebs_ops_can_manage_support($staff_id = null)
{
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage_support');
}

/**
 * Check if staff can manage QA processes (TSA capability)
 */
function disowebs_ops_can_manage_qa($staff_id = null)
{
    if (is_admin($staff_id ?: get_staff_user_id())) {
        return true;
    }
    return has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage_qa');
}

function disowebs_ops_get_project_invoice_summary($project_id)
{
    $project_id = (int) $project_id;
    if ($project_id <= 0) {
        return [
            'total' => 0.0,
            'paid' => 0.0,
            'outstanding' => 0.0,
            'paid_ratio' => 0.0,
        ];
    }

    $CI = &get_instance();
    if (!class_exists('Invoices_model', false)) {
        $CI->load->model('invoices_model');
    }

    $invoice_table = db_prefix() . 'invoices';

    $CI->db->select('COALESCE(SUM(total), 0) as total_amount');
    $CI->db->from($invoice_table);
    $CI->db->where('project_id', $project_id);
    $CI->db->where_not_in('status', [Invoices_model::STATUS_CANCELLED, Invoices_model::STATUS_DRAFT]);
    $total_row = $CI->db->get()->row();

    $CI->db->select('COALESCE(SUM(payments.amount), 0) as paid_amount');
    $CI->db->from(db_prefix() . 'invoicepaymentrecords as payments');
    $CI->db->join($invoice_table . ' as invoices', 'invoices.id = payments.invoiceid', 'inner');
    $CI->db->where('invoices.project_id', $project_id);
    $CI->db->where_not_in('invoices.status', [Invoices_model::STATUS_CANCELLED, Invoices_model::STATUS_DRAFT]);
    $paid_row = $CI->db->get()->row();

    $total = $total_row ? (float) $total_row->total_amount : 0.0;
    $paid = $paid_row ? (float) $paid_row->paid_amount : 0.0;
    $outstanding = max(0.0, $total - $paid);
    $ratio = $total > 0.0 ? $paid / $total : 0.0;

    return [
        'total' => $total,
        'paid' => $paid,
        'outstanding' => $outstanding,
        'paid_ratio' => $ratio,
    ];
}
