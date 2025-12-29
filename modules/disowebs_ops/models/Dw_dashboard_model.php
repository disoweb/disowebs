<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dw_dashboard_model extends App_Model
{
    private $milestones_table;
    private $proof_entries_table;
    private $change_requests_table;
    private $scope_snapshots_table;

    public function __construct()
    {
        parent::__construct();
        $this->milestones_table = db_prefix() . 'dw_project_milestones';
        $this->proof_entries_table = db_prefix() . 'dw_proof_entries';
        $this->change_requests_table = db_prefix() . 'dw_change_requests';
        $this->scope_snapshots_table = db_prefix() . 'dw_scope_snapshots';
    }

    public function get_weekly_execution_stats($range = null)
    {
        $range = (is_array($range) && isset($range['start'], $range['end']))
            ? $range
            : disowebs_ops_get_date_range_for_key('week');
        $start = $range['start'] ?? date('Y-m-d');
        $end = $range['end'] ?? $start;
        $start_dt = $start . ' 00:00:00';
        $end_dt = $end . ' 23:59:59';

        return [
            'range_start' => $start,
            'range_end' => $end,
            'milestones_done' => $this->count_milestones_done($start_dt, $end_dt),
            'proof_entries' => $this->count_proof_entries($start_dt, $end_dt),
        ];
    }

    public function get_crm_manager_stats($staff_id = null)
    {
        $staff_id = $staff_id ?: get_staff_user_id();

        return [
            'followups_due' => $this->count_followups_due($staff_id),
            'clients_weekly_updates' => $this->count_clients_weekly_updates_due($staff_id),
            'invoices_followup' => $this->count_invoices_followup_due($staff_id),
            'proposals_waiting' => $this->count_proposals_awaiting_response($staff_id),
            'overdue_invoices' => $this->count_overdue_invoices($staff_id),
            'pending_change_requests' => $this->count_pending_change_requests(),
        ];
    }

    public function get_tsa_stats($staff_id = null)
    {
        $staff_id = $staff_id ?: get_staff_user_id();

        return [
            'open_tickets' => $this->count_open_tickets($staff_id),
            'open_tickets_48h' => $this->count_open_tickets($staff_id, 48),
            'high_priority_tickets' => $this->count_high_priority_tickets($staff_id),
            'projects_awaiting_qa' => $this->count_projects_in_status($staff_id, 6),
            'proof_backlog' => $this->count_projects_without_proof($staff_id),
        ];
    }

    public function get_capacity_stats($staff_id = null, $limit = 5)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        $project_limit = (int) get_option('disowebs_ops_active_project_limit');
        if ($project_limit <= 0) {
            $project_limit = 4;
        }

        return [
            'active_projects' => $this->count_active_projects($staff_id),
            'queued_projects' => $this->count_projects_in_status($staff_id, 1),
            'active_project_limit' => $project_limit,
            'overdue_milestones' => $this->get_overdue_milestones($staff_id, $limit),
            'overdue_milestones_total' => $this->count_overdue_milestones($staff_id),
            'blocked_milestones' => $this->get_overdue_milestones($staff_id, $limit),
            'blocked_milestones_total' => $this->count_overdue_milestones($staff_id),
        ];
    }

    public function get_revenue_cashflow_stats()
    {
        $month_start = date('Y-m-01');
        $month_end = date('Y-m-t');

        return [
            'month_start' => $month_start,
            'month_end' => $month_end,
            'revenue' => $this->get_payments_received($month_start, $month_end),
            'outstanding' => $this->get_outstanding_invoices_summary(get_staff_user_id()),
            'retainers' => $this->get_active_retainers_stats(),
        ];
    }

    public function get_pipeline_forecast_stats($staff_id = null)
    {
        $staff_id = $staff_id ?: get_staff_user_id();

        return [
            'qualified_leads' => $this->count_qualified_leads($staff_id),
            'proposals_sent' => $this->count_proposals_sent($staff_id),
            'forecast_30' => $this->get_weighted_proposal_forecast($staff_id, 30),
            'forecast_60' => $this->get_weighted_proposal_forecast($staff_id, 60),
        ];
    }

    public function get_risk_alerts_stats($staff_id = null)
    {
        $staff_id = $staff_id ?: get_staff_user_id();

        return [
            'overdue_milestones' => $this->count_overdue_milestones($staff_id),
            'overdue_invoices' => $this->count_overdue_invoices($staff_id),
            'projects_without_scope' => $this->count_projects_without_scope_snapshot($staff_id),
            'margin_alerts' => $this->count_margin_alerts(),
            'deposit_warnings' => $this->count_deposit_warnings(),
            'blockers_overdue' => $this->count_overdue_blockers(),
        ];
    }

    public function get_growth_flywheel_stats()
    {
        $this->load->model('disowebs_ops/dw_testimonials_model');
        $testimonial_stats = $this->dw_testimonials_model->get_growth_stats();

        return [
            'proof_entries_month' => $this->count_proof_entries_this_month(),
            'testimonials_requested' => $testimonial_stats['requested_count'],
            'testimonials_received' => $testimonial_stats['received_count'],
            'testimonials_pending' => $testimonial_stats['pending_count'],
            'testimonials_approved' => $testimonial_stats['publishable_count'],
            'case_studies_ready' => $this->count_projects_with_proof(),
        ];
    }

    public function get_delivery_engine_stats($staff_id = null)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        
        $this->load->model('disowebs_ops/dw_weekly_demos_model');
        $this->load->model('disowebs_ops/dw_milestone_blockers_model');
        $this->load->model('disowebs_ops/dw_project_gates_model');
        
        $demo_stats = $this->dw_weekly_demos_model->get_demo_stats();
        $gate_stats = $this->get_gate_status_stats();
        
        return [
            'active_projects' => $demo_stats['active_projects'],
            'demos_scheduled' => $demo_stats['demos_scheduled'],
            'demos_completed' => $demo_stats['demos_completed'],
            'demo_missing' => $demo_stats['missing_demos'],
            'active_blockers' => $this->dw_milestone_blockers_model->count_all_unresolved($staff_id),
            'overdue_blockers' => count($this->dw_milestone_blockers_model->get_overdue_blockers($staff_id)),
            'deposit_pending' => $gate_stats['deposit_warnings'],
            'final_pending' => $gate_stats['final_payment_warnings'],
            'training_pending' => $gate_stats['missing_training'],
        ];
    }

    public function get_retention_stats($staff_id = null)
    {
        $this->load->model('disowebs_ops/dw_retainer_offers_model');
        $stats = $this->dw_retainer_offers_model->get_retention_stats(30);
        
        // Get eligible projects (launched recently without retainer offer)
        $eligible_projects = $this->dw_retainer_offers_model->get_eligible_projects();
        
        // Map to expected keys for dashboard widget
        return [
            'eligible_for_retainer' => count($eligible_projects),
            'retainers_offered' => $stats['offered'] ?? 0,
            'retainers_accepted' => $stats['accepted'] ?? 0,
            'conversion_rate' => $stats['conversion_rate'] ?? 0,
            'monthly_retainer_value' => $stats['monthly_retainer_value'] ?? 0,
            'eligible_projects' => $eligible_projects,
            // Also pass original keys for backwards compatibility
            'launched' => $stats['launched'] ?? 0,
            'offered' => $stats['offered'] ?? 0,
            'accepted' => $stats['accepted'] ?? 0,
            'declined' => $stats['declined'] ?? 0,
        ];
    }

    /**
     * Get retainer tracker stats for CRM dashboard
     * Returns active retainer count and MRR
     */
    public function get_retainer_tracker_stats()
    {
        $this->load->model('disowebs_ops/dw_retainer_offers_model');
        
        // Get accepted retainer offers
        $this->db->select('id, project_id, offer_amount');
        $this->db->from(db_prefix() . 'dw_retainer_offers');
        $this->db->where('status', 'accepted');
        $active_retainers = $this->db->get()->result_array();
        
        $mrr = 0;
        foreach ($active_retainers as $retainer) {
            $mrr += (float) ($retainer['offer_amount'] ?? 0);
        }
        
        return [
            'active' => count($active_retainers),
            'mrr' => $mrr,
        ];
    }

    public function get_profit_engine_stats($staff_id = null)
    {
        $this->load->model('disowebs_ops/dw_project_profit_model');
        $this->load->model('disowebs_ops/dw_margin_alerts_model');
        
        $profit_summary = $this->dw_project_profit_model->get_profit_summary();
        $alert_stats = $this->dw_margin_alerts_model->get_stats();
        $low_margin_projects = $this->dw_project_profit_model->get_low_margin_projects(20);
        $top_margin_projects = $this->dw_project_profit_model->get_top_margin_projects(5);
        
        return [
            'total_revenue' => $profit_summary['total_actual_revenue'] ?? 0,
            'total_cost' => $profit_summary['total_actual_cost'] ?? 0,
            'total_profit' => ($profit_summary['total_actual_revenue'] ?? 0) - ($profit_summary['total_actual_cost'] ?? 0),
            'avg_margin' => $profit_summary['avg_margin_percent'] ?? 0,
            'low_margin_count' => count($low_margin_projects),
            'low_margin_projects' => $low_margin_projects,
            'top_margin_projects' => $top_margin_projects,
            'cr_impact_total' => $profit_summary['total_cr_impact'] ?? 0,
            'alerts_total' => $alert_stats['total'],
            'alerts_critical' => $alert_stats['critical'],
        ];
    }

    public function get_gate_status_stats()
    {
        $this->load->model('disowebs_ops/dw_project_gates_model');
        
        $deposit_warnings = $this->dw_project_gates_model->get_deposit_warnings();
        $final_warnings = $this->dw_project_gates_model->get_final_payment_warnings();
        $missing_training = $this->dw_project_gates_model->count_missing_training();
        
        return [
            'deposit_warnings' => count($deposit_warnings),
            'final_payment_warnings' => count($final_warnings),
            'missing_training' => $missing_training,
        ];
    }

    private function count_margin_alerts()
    {
        $this->load->model('disowebs_ops/dw_margin_alerts_model');
        return $this->dw_margin_alerts_model->count_unacknowledged();
    }

    private function count_deposit_warnings()
    {
        $this->load->model('disowebs_ops/dw_project_gates_model');
        return count($this->dw_project_gates_model->get_deposit_warnings());
    }

    private function count_overdue_blockers()
    {
        $this->load->model('disowebs_ops/dw_milestone_blockers_model');
        return count($this->dw_milestone_blockers_model->get_overdue_blockers());
    }

    public function get_crm_pipeline_hygiene_stats($staff_id = null)
    {
        $staff_id = $staff_id ?: get_staff_user_id();

        return [
            'followups_overdue' => $this->count_followups_overdue($staff_id),
            'stale_leads' => $this->count_stale_leads($staff_id, 7),
            'proposals_pending' => $this->count_proposals_pending_days($staff_id, 7),
        ];
    }

    public function get_crm_active_projects_overview($staff_id = null, $limit = 5)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        $projects_table = db_prefix() . 'projects';

        $this->db->select('id, name, clientid');
        $this->db->from($projects_table);
        $this->db->where('status', 2);
        $this->apply_project_permission_filter($staff_id, $projects_table);
        $this->db->order_by('start_date', 'desc');
        $this->db->limit((int) $limit);
        $projects = $this->db->get()->result_array();

        if (empty($projects)) {
            return [];
        }

        $project_ids = array_column($projects, 'id');
        $this->db->select('project_id, title, week_end');
        $this->db->from($this->milestones_table);
        $this->db->where_in('project_id', $project_ids);
        $this->db->where('status !=', 'done');
        $this->db->order_by('week_end', 'asc');
        $milestones = $this->db->get()->result_array();

        $next_milestones = [];
        foreach ($milestones as $milestone) {
            $project_id = (int) $milestone['project_id'];
            if (!isset($next_milestones[$project_id])) {
                $next_milestones[$project_id] = $milestone;
            }
        }

        $overview = [];
        foreach ($projects as $project) {
            $project_id = (int) $project['id'];
            $milestone = $next_milestones[$project_id] ?? null;
            $invoice_summary = disowebs_ops_get_project_invoice_summary($project_id);

            $overview[] = [
                'project_id' => $project_id,
                'project_name' => $project['name'],
                'milestone_title' => $milestone['title'] ?? '',
                'milestone_due' => $milestone['week_end'] ?? null,
                'payment_outstanding' => $invoice_summary['outstanding'] ?? 0.0,
            ];
        }

        return $overview;
    }

    public function get_crm_retainer_tracker_stats($staff_id = null, $days = 30)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        $recent_projects = $this->get_recent_finished_projects($staff_id, $days);
        $launched = count($recent_projects);

        if ($launched === 0) {
            return [
                'launched' => 0,
                'offered' => 0,
                'accepted' => 0,
                'declined' => 0,
            ];
        }

        $project_ids = array_column($recent_projects, 'id');
        $client_ids = array_values(array_unique(array_filter(array_column($recent_projects, 'clientid'))));

        $retainer_map = $this->get_retainer_offer_map($recent_projects, $project_ids, $client_ids);
        $offered = count($retainer_map['offered']);
        $accepted = count($retainer_map['accepted']);
        $declined = max(0, $offered - $accepted);

        return [
            'launched' => $launched,
            'offered' => $offered,
            'accepted' => $accepted,
            'declined' => $declined,
        ];
    }

    public function get_qa_readiness_stats($staff_id = null)
    {
        $staff_id = $staff_id ?: get_staff_user_id();

        return [
            'pending' => $this->count_projects_in_status($staff_id, 6),
            'passed_this_week' => 0,
            'failed' => 0,
        ];
    }

    public function get_documentation_stats($staff_id = null)
    {
        $staff_id = $staff_id ?: get_staff_user_id();

        return [
            'total' => $this->count_docs_updated_this_month($staff_id),
            'written_this_week' => 0,
            'coverage_pct' => 0,
        ];
    }

    public function get_proof_backlog_stats($staff_id = null)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        $range = disowebs_ops_get_current_week_range();
        $start_dt = ($range['week_start'] ?? date('Y-m-d')) . ' 00:00:00';
        $end_dt = ($range['week_end'] ?? date('Y-m-d')) . ' 23:59:59';

        return [
            'total' => $this->count_projects_without_proof($staff_id),
            'recorded_this_week' => $this->count_proof_entries($start_dt, $end_dt),
        ];
    }

    /**
     * Get support queue stats for TSA dashboard
     */
    public function get_support_queue_stats()
    {
        // Count open tickets
        $this->db->where('status !=', 5); // Not closed
        $open_tickets = (int) $this->db->count_all_results(db_prefix() . 'tickets');

        // Count high priority tickets
        $this->db->where('status !=', 5);
        $this->db->where('priority', 1); // High priority
        $high_priority = (int) $this->db->count_all_results(db_prefix() . 'tickets');

        // Count overdue tickets (no response in 24h)
        $this->db->where('status !=', 5);
        $this->db->where('lastreply <', date('Y-m-d H:i:s', strtotime('-24 hours')));
        $this->db->or_where('lastreply IS NULL', null, false);
        $this->db->where('status !=', 5);
        $overdue = (int) $this->db->count_all_results(db_prefix() . 'tickets');

        return [
            'open_tickets' => $open_tickets,
            'high_priority' => $high_priority,
            'overdue' => $overdue,
            'avg_response_time' => '2h', // Placeholder
        ];
    }

    /**
     * Get count of projects needing proof/demo
     */
    public function get_projects_needing_proof()
    {
        return $this->count_projects_without_proof(get_staff_user_id());
    }

    /**
     * Get count of projects missing documentation
     */
    public function get_projects_missing_documentation()
    {
        return $this->count_projects_missing_docs(get_staff_user_id());
    }

    private function count_milestones_done($start_dt, $end_dt)
    {
        $this->db->where('status', 'done');
        $this->db->where('done_at >=', $start_dt);
        $this->db->where('done_at <=', $end_dt);

        return (int) $this->db->count_all_results($this->milestones_table);
    }

    private function get_payments_received($start, $end)
    {
        if (!staff_can('view', 'payments') && !staff_can('view_own', 'payments') && !staff_can('view', 'invoices') && !staff_can('view_own', 'invoices') && !is_admin()) {
            return [
                'total' => 0.0,
                'count' => 0,
            ];
        }

        $row = $this->db
            ->select('COUNT(*) as total_count, COALESCE(SUM(amount), 0) as total_amount')
            ->from(db_prefix() . 'invoicepaymentrecords')
            ->where('date >=', $start)
            ->where('date <=', $end)
            ->get()
            ->row();

        return [
            'total' => $row ? (float) $row->total_amount : 0.0,
            'count' => $row ? (int) $row->total_count : 0,
        ];
    }

    private function count_proof_entries($start_dt, $end_dt)
    {
        $this->db->where('created_at >=', $start_dt);
        $this->db->where('created_at <=', $end_dt);

        return (int) $this->db->count_all_results($this->proof_entries_table);
    }

    private function count_proof_entries_this_month()
    {
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-t 23:59:59');

        return $this->count_proof_entries($start, $end);
    }

    private function count_projects_with_proof()
    {
        $row = $this->db
            ->select('COUNT(DISTINCT project_id) as total')
            ->from($this->proof_entries_table)
            ->get()
            ->row();

        return $row ? (int) $row->total : 0;
    }

    private function count_followups_due($staff_id)
    {
        $today_end = date('Y-m-d 23:59:59');

        $this->db->from(db_prefix() . 'reminders');
        $this->db->where('isnotified', 0);
        $this->db->where('date <=', $today_end);
        $this->db->where_in('rel_type', ['lead', 'customer']);
        if (!is_admin()) {
            $this->db->where('staff', (int) $staff_id);
        }

        return (int) $this->db->count_all_results();
    }

    private function count_clients_weekly_updates_due($staff_id)
    {
        $today_end = date('Y-m-d 23:59:59');

        $this->db->from(db_prefix() . 'reminders');
        $this->db->where('isnotified', 0);
        $this->db->where('date <=', $today_end);
        $this->db->where('rel_type', 'project');
        if (!is_admin()) {
            $this->db->where('staff', (int) $staff_id);
        }

        return (int) $this->db->count_all_results();
    }

    private function count_invoices_followup_due($staff_id)
    {
        if (!class_exists('Invoices_model', false)) {
            $this->load->model('invoices_model');
        }

        if (!staff_can('view', 'invoices') && !staff_can('view_own', 'invoices') && !is_admin()) {
            return 0;
        }

        $today = date('Y-m-d');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->where_in('status', [Invoices_model::STATUS_UNPAID, Invoices_model::STATUS_PARTIALLY, Invoices_model::STATUS_OVERDUE]);
        $this->db->where('duedate <=', $today);
        if (staff_cant('view', 'invoices')) {
            $this->db->where(get_invoices_where_sql_for_staff((int) $staff_id));
        }

        return (int) $this->db->count_all_results();
    }

    private function count_followups_overdue($staff_id)
    {
        $today_start = date('Y-m-d 00:00:00');

        $this->db->from(db_prefix() . 'reminders');
        $this->db->where('isnotified', 0);
        $this->db->where('date <', $today_start);
        $this->db->where_in('rel_type', ['lead', 'customer']);
        if (!is_admin()) {
            $this->db->where('staff', (int) $staff_id);
        }

        return (int) $this->db->count_all_results();
    }

    private function count_overdue_invoices($staff_id)
    {
        if (!class_exists('Invoices_model', false)) {
            $this->load->model('invoices_model');
        }

        if (!staff_can('view', 'invoices') && !staff_can('view_own', 'invoices') && !is_admin()) {
            return 0;
        }

        $this->db->from(db_prefix() . 'invoices');
        $this->db->where('status', Invoices_model::STATUS_OVERDUE);
        if (staff_cant('view', 'invoices')) {
            $this->db->where(get_invoices_where_sql_for_staff((int) $staff_id));
        }

        return (int) $this->db->count_all_results();
    }

    private function get_outstanding_invoices_summary($staff_id)
    {
        if (!class_exists('Invoices_model', false)) {
            $this->load->model('invoices_model');
        }

        if (!staff_can('view', 'invoices') && !staff_can('view_own', 'invoices') && !is_admin()) {
            return [
                'count' => 0,
                'total' => 0.0,
            ];
        }

        $this->db->select('COUNT(*) as total_count, COALESCE(SUM(total), 0) as total_amount');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->where_in('status', [Invoices_model::STATUS_UNPAID, Invoices_model::STATUS_PARTIALLY, Invoices_model::STATUS_OVERDUE]);
        if (staff_cant('view', 'invoices')) {
            $this->db->where(get_invoices_where_sql_for_staff((int) $staff_id));
        }

        $row = $this->db->get()->row();

        return [
            'count' => $row ? (int) $row->total_count : 0,
            'total' => $row ? (float) $row->total_amount : 0.0,
        ];
    }

    private function count_proposals_awaiting_response($staff_id)
    {
        if (
            staff_cant('view', 'proposals')
            && staff_cant('view_own', 'proposals')
            && (int) get_option('allow_staff_view_proposals_assigned') !== 1
        ) {
            return 0;
        }

        if (!function_exists('get_proposals_sql_where_staff')) {
            $this->load->helper('proposals');
        }

        $this->db->from(db_prefix() . 'proposals');
        $this->db->where_in('status', [4, 5]);

        if (staff_cant('view', 'proposals')) {
            $where = get_proposals_sql_where_staff((int) $staff_id);
            if ($where) {
                $this->db->where($where, null, false);
            }
        }

        return (int) $this->db->count_all_results();
    }

    private function count_proposals_sent($staff_id)
    {
        if (
            staff_cant('view', 'proposals')
            && staff_cant('view_own', 'proposals')
            && (int) get_option('allow_staff_view_proposals_assigned') !== 1
        ) {
            return 0;
        }

        $this->db->from(db_prefix() . 'proposals');
        $this->db->where_in('status', [4, 5]);
        $this->apply_proposals_permission_filter($staff_id);

        return (int) $this->db->count_all_results();
    }

    private function count_proposals_pending_days($staff_id, $days)
    {
        if (
            staff_cant('view', 'proposals')
            && staff_cant('view_own', 'proposals')
            && (int) get_option('allow_staff_view_proposals_assigned') !== 1
        ) {
            return 0;
        }

        $threshold = date('Y-m-d', strtotime('-' . (int) $days . ' days'));
        $this->db->from(db_prefix() . 'proposals');
        $this->db->where_in('status', [4, 5]);
        $this->db->where('date <', $threshold);
        $this->apply_proposals_permission_filter($staff_id);

        return (int) $this->db->count_all_results();
    }

    private function get_weighted_proposal_forecast($staff_id, $days)
    {
        if (
            staff_cant('view', 'proposals')
            && staff_cant('view_own', 'proposals')
            && (int) get_option('allow_staff_view_proposals_assigned') !== 1
        ) {
            return 0.0;
        }

        $target_date = date('Y-m-d', strtotime('+' . (int) $days . ' days'));
        $this->db->select('COALESCE(SUM(total * CASE WHEN status = 4 THEN 0.4 WHEN status = 5 THEN 0.6 ELSE 0 END), 0) as weighted_total');
        $this->db->from(db_prefix() . 'proposals');
        $this->db->where_in('status', [4, 5]);
        $this->db->where('DATE(COALESCE(open_till, date)) <= ' . $this->db->escape($target_date), null, false);
        $this->apply_proposals_permission_filter($staff_id);

        $row = $this->db->get()->row();

        return $row ? (float) $row->weighted_total : 0.0;
    }

    private function count_pending_change_requests()
    {
        $this->db->where('status', 'submitted');

        return (int) $this->db->count_all_results($this->change_requests_table);
    }

    private function count_qualified_leads($staff_id)
    {
        if (!staff_can('view', 'leads') && !staff_can('view_own', 'leads') && !is_admin()) {
            return 0;
        }

        $this->db->from(db_prefix() . 'leads');
        $this->db->where('lost', 0);
        $this->db->where('junk', 0);
        if (staff_cant('view', 'leads')) {
            $this->db->where('assigned', (int) $staff_id);
        }

        return (int) $this->db->count_all_results();
    }

    private function count_stale_leads($staff_id, $days)
    {
        if (!staff_can('view', 'leads') && !staff_can('view_own', 'leads') && !is_admin()) {
            return 0;
        }

        $threshold = date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days'));
        $this->db->from(db_prefix() . 'leads');
        $this->db->where('lost', 0);
        $this->db->where('junk', 0);
        $this->db->where('COALESCE(lastcontact, dateadded) < ' . $this->db->escape($threshold), null, false);
        if (staff_cant('view', 'leads')) {
            $this->db->where('assigned', (int) $staff_id);
        }

        return (int) $this->db->count_all_results();
    }

    private function count_open_tickets($staff_id, $older_than_hours = null)
    {
        if (!is_staff_member() && (int) get_option('access_tickets_to_none_staff_members') !== 1) {
            return 0;
        }

        $closed_status_ids = $this->get_closed_ticket_status_ids();

        $this->db->from(db_prefix() . 'tickets');
        $this->db->where('merged_ticket_id IS NULL', null, false);
        if (!empty($closed_status_ids)) {
            $this->db->where_not_in('status', $closed_status_ids);
        }
        if ($older_than_hours !== null) {
            $threshold = date('Y-m-d H:i:s', strtotime('-' . (int) $older_than_hours . ' hours'));
            $this->db->where('date <=', $threshold);
        }

        $this->apply_ticket_department_filter($staff_id);

        return (int) $this->db->count_all_results();
    }

    private function count_high_priority_tickets($staff_id)
    {
        if (!is_staff_member() && (int) get_option('access_tickets_to_none_staff_members') !== 1) {
            return 0;
        }

        $priority_ids = $this->get_high_priority_ticket_ids();
        if (empty($priority_ids)) {
            return 0;
        }

        $closed_status_ids = $this->get_closed_ticket_status_ids();

        $this->db->from(db_prefix() . 'tickets');
        $this->db->where('merged_ticket_id IS NULL', null, false);
        if (!empty($closed_status_ids)) {
            $this->db->where_not_in('status', $closed_status_ids);
        }
        $this->db->where_in('priority', $priority_ids);

        $this->apply_ticket_department_filter($staff_id);

        return (int) $this->db->count_all_results();
    }

    private function apply_ticket_department_filter($staff_id)
    {
        if (is_admin()) {
            return;
        }

        $this->load->model('departments_model');
        $staff_departments_ids = $this->departments_model->get_staff_departments($staff_id, true);

        if (get_option('staff_access_only_assigned_departments') == 1) {
            $departments_ids = [];
            if (count($staff_departments_ids) === 0) {
                $departments = $this->departments_model->get();
                foreach ($departments as $department) {
                    $departments_ids[] = (int) $department['departmentid'];
                }
            } else {
                foreach ($staff_departments_ids as $department_id) {
                    $departments_ids[] = (int) $department_id;
                }
            }

            $departments_ids = array_values(array_filter(array_unique($departments_ids)));
            if (count($departments_ids) > 0) {
                $this->db->where('department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . (int) $staff_id . '")', null, false);
            }
        }
    }

    private function get_closed_ticket_status_ids()
    {
        $ids = [];
        $results = $this->db
            ->select('ticketstatusid')
            ->from(db_prefix() . 'tickets_status')
            ->where_in('name', ['Closed', 'closed'])
            ->get()
            ->result_array();

        foreach ($results as $row) {
            $ids[] = (int) $row['ticketstatusid'];
        }

        if (count($ids) === 0) {
            $ids[] = 5;
        }

        return array_values(array_unique($ids));
    }

    private function get_high_priority_ticket_ids()
    {
        $ids = [];
        $results = $this->db
            ->select('ticketpriorityid')
            ->from(db_prefix() . 'tickets_priorities')
            ->group_start()
            ->like('name', 'high')
            ->or_like('name', 'urgent')
            ->group_end()
            ->get()
            ->result_array();

        foreach ($results as $row) {
            $ids[] = (int) $row['ticketpriorityid'];
        }

        if (count($ids) === 0) {
            $fallback = $this->db
                ->select('ticketpriorityid')
                ->from(db_prefix() . 'tickets_priorities')
                ->order_by('ticketpriorityid', 'desc')
                ->limit(1)
                ->get()
                ->row_array();
            if ($fallback) {
                $ids[] = (int) $fallback['ticketpriorityid'];
            }
        }

        return array_values(array_unique($ids));
    }

    private function count_projects_without_proof($staff_id)
    {
        $projects_table = db_prefix() . 'projects';

        $this->db->select('COUNT(DISTINCT ' . $projects_table . '.id) as total');
        $this->db->from($projects_table);
        $this->db->join($this->proof_entries_table . ' as proof', 'proof.project_id = ' . $projects_table . '.id', 'left');
        $this->db->where($projects_table . '.status', 4);
        $this->db->where('proof.id IS NULL', null, false);

        if (staff_cant('view', 'projects')) {
            $this->db->where($projects_table . '.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . (int) $staff_id . ')', null, false);
        }

        $row = $this->db->get()->row();

        return $row ? (int) $row->total : 0;
    }

    private function count_projects_without_scope_snapshot($staff_id)
    {
        $projects_table = db_prefix() . 'projects';

        $this->db->select('COUNT(DISTINCT ' . $projects_table . '.id) as total');
        $this->db->from($projects_table);
        $this->db->join($this->scope_snapshots_table . ' as snapshots', 'snapshots.project_id = ' . $projects_table . '.id', 'left');
        $this->db->where($projects_table . '.status', 2);
        $this->db->where('snapshots.id IS NULL', null, false);
        $this->apply_project_permission_filter($staff_id, $projects_table);

        $row = $this->db->get()->row();

        return $row ? (int) $row->total : 0;
    }

    private function count_projects_missing_docs($staff_id)
    {
        $projects_table = db_prefix() . 'projects';
        $files_table = db_prefix() . 'project_files';

        $this->db->select('COUNT(DISTINCT ' . $projects_table . '.id) as total');
        $this->db->from($projects_table);
        $this->db->join($files_table . ' as files', 'files.project_id = ' . $projects_table . '.id', 'left');
        $this->db->where($projects_table . '.status', 2);
        $this->db->where('files.id IS NULL', null, false);
        $this->apply_project_permission_filter($staff_id, $projects_table);

        $row = $this->db->get()->row();

        return $row ? (int) $row->total : 0;
    }

    private function count_docs_updated_this_month($staff_id)
    {
        $files_table = db_prefix() . 'project_files';
        $projects_table = db_prefix() . 'projects';
        $month_start = date('Y-m-01 00:00:00');
        $month_end = date('Y-m-t 23:59:59');

        $this->db->select('COUNT(*) as total');
        $this->db->from($files_table . ' as files');
        $this->db->join($projects_table, $projects_table . '.id = files.project_id', 'inner');
        $this->db->where('files.dateadded >=', $month_start);
        $this->db->where('files.dateadded <=', $month_end);
        $this->apply_project_permission_filter($staff_id, $projects_table);

        $row = $this->db->get()->row();

        return $row ? (int) $row->total : 0;
    }

    private function get_active_retainers_stats()
    {
        $stats = [
            'count' => 0,
            'mrr' => 0.0,
        ];

        $subscriptions_table = db_prefix() . 'subscriptions';
        if ($this->db->table_exists($subscriptions_table)) {
            $this->db->from($subscriptions_table);
            $this->db->where('status', 'active');
            $this->db->group_start();
            $this->db->like('name', 'retainer');
            $this->db->or_like('description', 'retainer');
            $this->db->group_end();
            $stats['count'] = (int) $this->db->count_all_results();

            $amount_field = $this->get_subscription_amount_field();
            if ($amount_field) {
                $quantity_expr = $this->db->field_exists('quantity', $subscriptions_table) ? 'COALESCE(quantity, 1)' : '1';
                $this->db->select('COALESCE(SUM(' . $amount_field . ' * ' . $quantity_expr . '), 0) as total_amount');
                $this->db->from($subscriptions_table);
                $this->db->where('status', 'active');
                $this->db->group_start();
                $this->db->like('name', 'retainer');
                $this->db->or_like('description', 'retainer');
                $this->db->group_end();
                $row = $this->db->get()->row();
                $stats['mrr'] = $row ? (float) $row->total_amount : 0.0;
            }
        }

        if ($stats['count'] === 0) {
            $stats['count'] = $this->count_retainer_contracts();
        }

        return $stats;
    }

    private function get_subscription_amount_field()
    {
        $table = db_prefix() . 'subscriptions';
        $candidates = [
            'amount',
            'amount_per_cycle',
            'stripe_plan_amount',
            'billing_amount',
            'rate',
            'price',
        ];

        foreach ($candidates as $candidate) {
            if ($this->db->field_exists($candidate, $table)) {
                return $candidate;
            }
        }

        return null;
    }

    private function count_retainer_contracts()
    {
        $contracts_table = db_prefix() . 'contracts';
        $types_table = db_prefix() . 'contracts_types';

        if (!$this->db->table_exists($contracts_table) || !$this->db->table_exists($types_table)) {
            return 0;
        }

        $this->db->from($contracts_table . ' as contracts');
        $this->db->join($types_table . ' as types', 'types.id = contracts.contract_type', 'left');
        $this->db->where('contracts.trash', 0);
        $this->db->group_start();
        $this->db->like('types.name', 'retainer');
        $this->db->or_like('contracts.subject', 'retainer');
        $this->db->group_end();
        $this->db->group_start();
        $this->db->where('contracts.signed', 1);
        $this->db->or_where('contracts.marked_as_signed', 1);
        $this->db->group_end();

        return (int) $this->db->count_all_results();
    }

    private function get_recent_finished_projects($staff_id, $days)
    {
        $projects_table = db_prefix() . 'projects';
        $threshold = date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days'));

        $this->db->select('id, clientid');
        $this->db->from($projects_table);
        $this->db->where('status', 4);
        $this->db->where('date_finished >=', $threshold);
        $this->apply_project_permission_filter($staff_id, $projects_table);

        return $this->db->get()->result_array();
    }

    private function get_retainer_offer_map($projects, $project_ids, $client_ids)
    {
        $offered = [];
        $accepted = [];
        $client_projects = [];

        foreach ($projects as $project) {
            $client_id = (int) $project['clientid'];
            if ($client_id > 0) {
                $client_projects[$client_id][] = (int) $project['id'];
            }
        }

        $subscriptions_table = db_prefix() . 'subscriptions';
        if ($this->db->table_exists($subscriptions_table) && (!empty($project_ids) || !empty($client_ids))) {
            $this->db->select('project_id, clientid, status');
            $this->db->from($subscriptions_table);
            $this->db->group_start();
            $this->db->like('name', 'retainer');
            $this->db->or_like('description', 'retainer');
            $this->db->group_end();
            $this->db->group_start();
            if (!empty($project_ids)) {
                $this->db->where_in('project_id', $project_ids);
            }
            if (!empty($client_ids)) {
                if (!empty($project_ids)) {
                    $this->db->or_where_in('clientid', $client_ids);
                } else {
                    $this->db->where_in('clientid', $client_ids);
                }
            }
            $this->db->group_end();
            $subscriptions = $this->db->get()->result_array();

            foreach ($subscriptions as $subscription) {
                $targets = [];
                $project_id = (int) $subscription['project_id'];
                $client_id = (int) $subscription['clientid'];
                if ($project_id > 0 && in_array($project_id, $project_ids, true)) {
                    $targets = [$project_id];
                } elseif ($client_id > 0 && isset($client_projects[$client_id])) {
                    $targets = $client_projects[$client_id];
                }

                foreach ($targets as $target_id) {
                    $offered[$target_id] = true;
                    if ($subscription['status'] === 'active') {
                        $accepted[$target_id] = true;
                    }
                }
            }
        }

        $contracts_table = db_prefix() . 'contracts';
        $types_table = db_prefix() . 'contracts_types';
        if ($this->db->table_exists($contracts_table) && $this->db->table_exists($types_table) && !empty($client_ids)) {
            $this->db->select('contracts.client, contracts.signed, contracts.marked_as_signed');
            $this->db->from($contracts_table . ' as contracts');
            $this->db->join($types_table . ' as types', 'types.id = contracts.contract_type', 'left');
            $this->db->where_in('contracts.client', $client_ids);
            $this->db->where('contracts.trash', 0);
            $this->db->group_start();
            $this->db->like('types.name', 'retainer');
            $this->db->or_like('contracts.subject', 'retainer');
            $this->db->group_end();
            $contracts = $this->db->get()->result_array();

            foreach ($contracts as $contract) {
                $client_id = (int) $contract['client'];
                if (!isset($client_projects[$client_id])) {
                    continue;
                }

                foreach ($client_projects[$client_id] as $project_id) {
                    $offered[$project_id] = true;
                    if ((int) $contract['signed'] === 1 || (int) $contract['marked_as_signed'] === 1) {
                        $accepted[$project_id] = true;
                    }
                }
            }
        }

        return [
            'offered' => array_keys($offered),
            'accepted' => array_keys($accepted),
        ];
    }

    private function count_projects_in_status($staff_id, $status_id)
    {
        $projects_table = db_prefix() . 'projects';
        $this->db->from($projects_table);
        $this->db->where('status', (int) $status_id);
        $this->apply_project_permission_filter($staff_id, $projects_table);

        return (int) $this->db->count_all_results();
    }

    private function count_active_projects($staff_id)
    {
        $projects_table = db_prefix() . 'projects';
        $this->db->from($projects_table);
        $this->db->where('status', 2);
        $this->apply_project_permission_filter($staff_id, $projects_table);

        return (int) $this->db->count_all_results();
    }

    private function get_overdue_milestones($staff_id, $limit = 5)
    {
        $milestones_table = $this->milestones_table . ' as milestones';
        $projects_table = db_prefix() . 'projects';
        $today = date('Y-m-d');

        $this->db->select('milestones.id, milestones.project_id, milestones.title, milestones.week_end, ' . $projects_table . '.name as project_name');
        $this->db->from($milestones_table);
        $this->db->join($projects_table, $projects_table . '.id = milestones.project_id', 'inner');
        $this->db->where('milestones.status !=', 'done');
        $this->db->where('milestones.week_end <', $today);
        $this->apply_project_permission_filter($staff_id, $projects_table);
        $this->db->order_by('milestones.week_end', 'asc');
        if ($limit) {
            $this->db->limit((int) $limit);
        }

        return $this->db->get()->result_array();
    }

    private function count_overdue_milestones($staff_id)
    {
        $projects_table = db_prefix() . 'projects';
        $today = date('Y-m-d');

        $this->db->from($this->milestones_table . ' as milestones');
        $this->db->join($projects_table, $projects_table . '.id = milestones.project_id', 'inner');
        $this->db->where('milestones.status !=', 'done');
        $this->db->where('milestones.week_end <', $today);
        $this->apply_project_permission_filter($staff_id, $projects_table);

        return (int) $this->db->count_all_results();
    }

    private function apply_proposals_permission_filter($staff_id)
    {
        if (staff_cant('view', 'proposals')) {
            if (!function_exists('get_proposals_sql_where_staff')) {
                $this->load->helper('proposals');
            }

            $where = get_proposals_sql_where_staff((int) $staff_id);
            if ($where) {
                $this->db->where($where, null, false);
            }
        }
    }

    private function apply_project_permission_filter($staff_id, $projects_table = null)
    {
        if (!staff_cant('view', 'projects')) {
            return;
        }

        $projects_table = $projects_table ?: db_prefix() . 'projects';
        $this->db->where($projects_table . '.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . (int) $staff_id . ')', null, false);
    }

    /**
     * Get qualified leads with details for KPI page
     */
    public function get_qualified_leads_list($staff_id = null, $limit = 100)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        
        if (!staff_can('view', 'leads') && !staff_can('view_own', 'leads') && !is_admin()) {
            return [];
        }

        $this->db->select('l.id, l.name, l.email, l.phonenumber, l.company, l.dateadded, l.lastcontact, l.assigned');
        $this->db->select('ls.name as status_name, ls.color as status_color');
        $this->db->select('lsrc.name as source_name');
        $this->db->select('CONCAT(s.firstname, \' \', s.lastname) as assigned_name', FALSE);
        $this->db->from(db_prefix() . 'leads as l');
        $this->db->join(db_prefix() . 'leads_status as ls', 'ls.id = l.status', 'left');
        $this->db->join(db_prefix() . 'leads_sources as lsrc', 'lsrc.id = l.source', 'left');
        $this->db->join(db_prefix() . 'staff as s', 's.staffid = l.assigned', 'left');
        $this->db->where('l.lost', 0);
        $this->db->where('l.junk', 0);
        
        if (staff_cant('view', 'leads')) {
            $this->db->where('l.assigned', (int) $staff_id);
        }
        
        $this->db->order_by('l.dateadded', 'desc');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Get proposals sent (open/revised) with details for KPI page
     */
    public function get_proposals_sent_list($staff_id = null, $limit = 100)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        
        if (staff_cant('view', 'proposals') && staff_cant('view_own', 'proposals') && (int) get_option('allow_staff_view_proposals_assigned') !== 1) {
            return [];
        }

        $this->db->select('p.id, p.subject, p.date, p.open_till, p.total, p.currency, p.status, p.rel_type, p.rel_id');
        $this->db->select('l.name as lead_name', FALSE);
        $this->db->select('c.company as client_name', FALSE);
        $this->db->from(db_prefix() . 'proposals as p');
        $this->db->join(db_prefix() . 'leads as l', 'l.id = p.rel_id AND p.rel_type = \'lead\'', 'left');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = p.rel_id AND p.rel_type = \'customer\'', 'left');
        $this->db->where_in('p.status', [4, 5]); // 4 = Sent, 5 = Revised
        
        $this->apply_proposals_permission_filter($staff_id);
        
        $this->db->order_by('p.date', 'desc');
        $this->db->limit($limit);

        $proposals = $this->db->get()->result_array();
        
        // Build related_name in PHP
        foreach ($proposals as &$p) {
            $p['related_name'] = ($p['rel_type'] == 'lead') ? $p['lead_name'] : $p['client_name'];
        }
        
        return $proposals;
    }

    /**
     * Get proposals for forecast (30 or 60 days) with details
     */
    public function get_proposals_forecast_list($staff_id = null, $days = 30, $limit = 100)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        
        if (staff_cant('view', 'proposals') && staff_cant('view_own', 'proposals') && (int) get_option('allow_staff_view_proposals_assigned') !== 1) {
            return [];
        }

        $target_date = date('Y-m-d', strtotime('+' . (int) $days . ' days'));
        
        $this->db->select('p.id, p.subject, p.date, p.open_till, p.total, p.currency, p.status, p.rel_type, p.rel_id');
        $this->db->select('l.name as lead_name', FALSE);
        $this->db->select('c.company as client_name', FALSE);
        $this->db->from(db_prefix() . 'proposals as p');
        $this->db->join(db_prefix() . 'leads as l', 'l.id = p.rel_id AND p.rel_type = \'lead\'', 'left');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = p.rel_id AND p.rel_type = \'customer\'', 'left');
        $this->db->where_in('p.status', [4, 5]);
        $this->db->where('DATE(COALESCE(p.open_till, p.date)) <= ' . $this->db->escape($target_date), null, false);
        
        $this->apply_proposals_permission_filter($staff_id);
        
        $this->db->order_by('p.total', 'desc');
        $this->db->limit($limit);

        $proposals = $this->db->get()->result_array();
        
        // Calculate probability and weighted value in PHP
        foreach ($proposals as &$p) {
            $p['probability'] = ($p['status'] == 4) ? 40 : (($p['status'] == 5) ? 60 : 0);
            $p['weighted_value'] = $p['total'] * ($p['probability'] / 100);
            $p['related_name'] = ($p['rel_type'] == 'lead') ? $p['lead_name'] : $p['client_name'];
        }
        
        return $proposals;
    }

    /**
     * Get stale leads with details for KPI page
     */
    public function get_stale_leads_list($staff_id = null, $days = 7, $limit = 100)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        
        if (!staff_can('view', 'leads') && !staff_can('view_own', 'leads') && !is_admin()) {
            return [];
        }

        $threshold = date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days'));
        
        $this->db->select('l.id, l.name, l.email, l.phonenumber, l.company, l.dateadded, l.lastcontact, l.assigned');
        $this->db->select('ls.name as status_name, ls.color as status_color');
        $this->db->select('lsrc.name as source_name');
        $this->db->select('CONCAT(s.firstname, \' \', s.lastname) as assigned_name', FALSE);
        $this->db->select('DATEDIFF(NOW(), COALESCE(l.lastcontact, l.dateadded)) as days_since_contact', FALSE);
        $this->db->from(db_prefix() . 'leads as l');
        $this->db->join(db_prefix() . 'leads_status as ls', 'ls.id = l.status', 'left');
        $this->db->join(db_prefix() . 'leads_sources as lsrc', 'lsrc.id = l.source', 'left');
        $this->db->join(db_prefix() . 'staff as s', 's.staffid = l.assigned', 'left');
        $this->db->where('l.lost', 0);
        $this->db->where('l.junk', 0);
        $this->db->where('COALESCE(l.lastcontact, l.dateadded) < ' . $this->db->escape($threshold), null, false);
        
        if (staff_cant('view', 'leads')) {
            $this->db->where('l.assigned', (int) $staff_id);
        }
        
        $this->db->order_by('days_since_contact', 'desc');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Get stale proposals with details for KPI page
     */
    public function get_stale_proposals_list($staff_id = null, $days = 7, $limit = 100)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        
        if (staff_cant('view', 'proposals') && staff_cant('view_own', 'proposals') && (int) get_option('allow_staff_view_proposals_assigned') !== 1) {
            return [];
        }

        $threshold = date('Y-m-d', strtotime('-' . (int) $days . ' days'));
        
        $this->db->select('p.id, p.subject, p.date, p.open_till, p.total, p.currency, p.status, p.rel_type, p.rel_id');
        $this->db->select('DATEDIFF(NOW(), p.date) as days_pending', FALSE);
        $this->db->select('l.name as lead_name', FALSE);
        $this->db->select('c.company as client_name', FALSE);
        $this->db->from(db_prefix() . 'proposals as p');
        $this->db->join(db_prefix() . 'leads as l', 'l.id = p.rel_id AND p.rel_type = \'lead\'', 'left');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = p.rel_id AND p.rel_type = \'customer\'', 'left');
        $this->db->where_in('p.status', [4, 5]);
        $this->db->where('p.date <', $threshold);
        
        $this->apply_proposals_permission_filter($staff_id);
        
        $this->db->order_by('days_pending', 'desc');
        $this->db->limit($limit);

        $proposals = $this->db->get()->result_array();
        
        // Build related_name in PHP
        foreach ($proposals as &$p) {
            $p['related_name'] = ($p['rel_type'] == 'lead') ? $p['lead_name'] : $p['client_name'];
        }
        
        return $proposals;
    }

    /**
     * Get followups overdue with details for KPI page
     * Uses tblreminders table for lead reminders
     */
    public function get_followups_overdue_list($staff_id = null, $limit = 100)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        
        if (!staff_can('view', 'leads') && !staff_can('view_own', 'leads') && !is_admin()) {
            return [];
        }

        $today = date('Y-m-d');
        
        // Get overdue lead reminders from tblreminders
        $this->db->select('l.id, l.name, l.email, l.phonenumber, l.company, l.dateadded, l.lastcontact, l.assigned');
        $this->db->select('r.date as followup_date, r.description as reminder_note');
        $this->db->select('ls.name as status_name, ls.color as status_color');
        $this->db->select('CONCAT(s.firstname, \' \', s.lastname) as assigned_name', FALSE);
        $this->db->select('DATEDIFF(NOW(), r.date) as days_overdue', FALSE);
        $this->db->from(db_prefix() . 'reminders as r');
        $this->db->join(db_prefix() . 'leads as l', 'l.id = r.rel_id', 'inner');
        $this->db->join(db_prefix() . 'leads_status as ls', 'ls.id = l.status', 'left');
        $this->db->join(db_prefix() . 'staff as s', 's.staffid = l.assigned', 'left');
        $this->db->where('r.rel_type', 'lead');
        $this->db->where('r.isnotified', 0);
        $this->db->where('l.lost', 0);
        $this->db->where('l.junk', 0);
        $this->db->where('DATE(r.date) <', $today);
        
        if (staff_cant('view', 'leads')) {
            $this->db->where('l.assigned', (int) $staff_id);
        }
        
        $this->db->order_by('days_overdue', 'desc');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Get list of active projects missing documentation (files)
     */
    public function get_projects_missing_docs_list($staff_id = null)
    {
        $staff_id = $staff_id ?: get_staff_user_id();
        $projects_table = db_prefix() . 'projects';
        $files_table = db_prefix() . 'project_files';
        $clients_table = db_prefix() . 'clients';

        $this->db->select($projects_table . '.id as project_id, ' . $projects_table . '.name as project_name');
        $this->db->select($projects_table . '.start_date, ' . $projects_table . '.deadline');
        $this->db->select($clients_table . '.company as client_name');
        $this->db->from($projects_table);
        $this->db->join($clients_table, $clients_table . '.userid = ' . $projects_table . '.clientid', 'left');
        $this->db->join($files_table . ' as files', 'files.project_id = ' . $projects_table . '.id', 'left');
        $this->db->where($projects_table . '.status', 2); // Active projects
        $this->db->where('files.id IS NULL', null, false);
        $this->db->group_by($projects_table . '.id');
        $this->apply_project_permission_filter($staff_id, $projects_table);
        $this->db->order_by($projects_table . '.start_date', 'desc');

        return $this->db->get()->result_array();
    }
}
