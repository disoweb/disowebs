<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Disowebs_ops extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if (!defined('DISOWEBS_OPS_MODULE_NAME')) {
            show_404();
        }
    }

    public function index()
    {
        $this->delivery();
    }

    public function delivery()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        // Load models
        $this->load->model('disowebs_ops/dw_dashboard_model');
        $this->load->model('disowebs_ops/dw_milestone_blockers_model');
        $this->load->model('disowebs_ops/dw_weekly_demos_model');

        // Get delivery stats
        $data['delivery_stats'] = $this->dw_dashboard_model->get_delivery_engine_stats();
        $data['blockers'] = $this->dw_milestone_blockers_model->get_unresolved(null, 10);
        $data['upcoming_demos'] = $this->dw_weekly_demos_model->get_upcoming_demos(7);

        // Get active projects count
        $this->db->where('status', 2);
        $data['active_count'] = (int) $this->db->count_all_results(db_prefix() . 'projects');
        $data['project_limit'] = (int) get_option('disowebs_ops_active_project_limit') ?: 4;

        $data['title'] = _l('disowebs_ops_delivery_engine');

        $this->load->view('disowebs_ops/admin/delivery_engine', $data);
    }

    public function scope()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $data['title'] = _l('disowebs_ops_scope_change_requests');
        $data['change_requests'] = $this->get_scope_change_requests();
        $data['snapshots'] = $this->get_scope_snapshots();

        $this->load->view('disowebs_ops/admin/scope', $data);
    }

    public function proof()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $data['title'] = _l('disowebs_ops_proof_vault');
        $data['proof_entries'] = $this->get_recent_proof_entries();
        $data['proof_totals'] = $this->get_proof_totals();

        $this->load->view('disowebs_ops/admin/proof', $data);
    }

    public function reports()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_dashboard_model');
        $this->load->model('disowebs_ops/dw_project_profit_model');
        $this->load->model('disowebs_ops/dw_retainer_offers_model');
        $this->load->model('disowebs_ops/dw_testimonials_model');

        // Date range filter
        $period = $this->input->get('period') ?: 'month';
        $range = disowebs_ops_get_date_range_for_key($period);

        // KPI Summary
        $data['kpi'] = $this->get_kpi_summary($range);

        // Delivery metrics
        $data['delivery'] = $this->get_delivery_report($range);

        // Revenue metrics
        $data['revenue'] = $this->get_revenue_report($range);

        // Proof & Growth metrics
        $data['growth'] = $this->get_growth_report($range);

        // Retention metrics
        $data['retention'] = $this->dw_retainer_offers_model->get_retention_stats(90);

        // Profit summary
        $data['profit'] = $this->dw_project_profit_model->get_profit_summary();

        // Period options
        $data['period'] = $period;
        $data['period_options'] = [
            'week' => _l('this_week'),
            'month' => _l('this_month'),
            'quarter' => _l('this_quarter'),
            'year' => _l('this_year'),
        ];
        $data['range'] = $range;

        $data['title'] = _l('disowebs_ops_reports');
        $this->load->view('disowebs_ops/admin/reports', $data);
    }

    private function get_kpi_summary($range)
    {
        $start = $range['start'] . ' 00:00:00';
        $end = $range['end'] . ' 23:59:59';

        // Projects delivered (status = 4 = finished) - use date_finished
        $this->db->where('status', 4);
        $this->db->where('date_finished >=', $range['start']);
        $this->db->where('date_finished <=', $range['end']);
        $projects_delivered = (int) $this->db->count_all_results(db_prefix() . 'projects');

        // Milestones completed
        $this->db->where('done_at >=', $start);
        $this->db->where('done_at <=', $end);
        $milestones_done = (int) $this->db->count_all_results(db_prefix() . 'dw_project_milestones');

        // Proof entries
        $this->db->where('created_at >=', $start);
        $this->db->where('created_at <=', $end);
        $proof_entries = (int) $this->db->count_all_results(db_prefix() . 'dw_proof_entries');

        // Revenue collected
        $this->db->select('COALESCE(SUM(amount), 0) as total');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->where('date >=', $range['start']);
        $this->db->where('date <=', $range['end']);
        $row = $this->db->get()->row();
        $revenue = $row ? (float) $row->total : 0;

        // Active projects
        $this->db->where('status', 2);
        $active_projects = (int) $this->db->count_all_results(db_prefix() . 'projects');

        // Pending change requests
        $this->db->where_in('status', ['draft', 'submitted']);
        $pending_crs = (int) $this->db->count_all_results(db_prefix() . 'dw_change_requests');

        return [
            'projects_delivered' => $projects_delivered,
            'milestones_done' => $milestones_done,
            'proof_entries' => $proof_entries,
            'revenue' => $revenue,
            'active_projects' => $active_projects,
            'pending_crs' => $pending_crs,
        ];
    }

    private function get_delivery_report($range)
    {
        $start = $range['start'] . ' 00:00:00';
        $end = $range['end'] . ' 23:59:59';

        // Phases by status
        $this->db->select('status, COUNT(*) as count');
        $this->db->from(db_prefix() . 'dw_project_phases');
        $this->db->group_by('status');
        $phases = $this->db->get()->result_array();
        $phases_by_status = [];
        foreach ($phases as $p) {
            $phases_by_status[$p['status']] = (int) $p['count'];
        }

        // Blockers summary
        $this->db->where('resolved', 0);
        $active_blockers = (int) $this->db->count_all_results(db_prefix() . 'dw_milestone_blockers');

        $this->db->where('resolved', 0);
        $this->db->where('next_action_date <', date('Y-m-d'));
        $overdue_blockers = (int) $this->db->count_all_results(db_prefix() . 'dw_milestone_blockers');

        // Demos this period
        $this->db->where('demo_completed', 1);
        $this->db->where('demo_completed_at >=', $start);
        $this->db->where('demo_completed_at <=', $end);
        $demos_completed = (int) $this->db->count_all_results(db_prefix() . 'dw_weekly_demos');

        return [
            'phases_by_status' => $phases_by_status,
            'active_blockers' => $active_blockers,
            'overdue_blockers' => $overdue_blockers,
            'demos_completed' => $demos_completed,
        ];
    }

    private function get_revenue_report($range)
    {
        $start = $range['start'];
        $end = $range['end'];

        // Payments by week/day
        $this->db->select('DATE(date) as pay_date, SUM(amount) as total');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->where('date >=', $start);
        $this->db->where('date <=', $end);
        $this->db->group_by('DATE(date)');
        $this->db->order_by('date', 'asc');
        $payments_by_date = $this->db->get()->result_array();

        // Outstanding invoices
        $this->db->select('COALESCE(SUM(total - (SELECT COALESCE(SUM(amount), 0) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid = ' . db_prefix() . 'invoices.id)), 0) as outstanding');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->where_not_in('status', [5, 6]); // not cancelled/draft
        $row = $this->db->get()->row();
        $outstanding = $row ? (float) $row->outstanding : 0;

        // Overdue invoices count
        $this->db->where('status', 1); // unpaid
        $this->db->where('duedate <', date('Y-m-d'));
        $overdue_count = (int) $this->db->count_all_results(db_prefix() . 'invoices');

        return [
            'payments_by_date' => $payments_by_date,
            'outstanding' => $outstanding,
            'overdue_count' => $overdue_count,
        ];
    }

    private function get_growth_report($range)
    {
        $start = $range['start'] . ' 00:00:00';
        $end = $range['end'] . ' 23:59:59';

        // Proof entries this period
        $this->db->where('created_at >=', $start);
        $this->db->where('created_at <=', $end);
        $proof_count = (int) $this->db->count_all_results(db_prefix() . 'dw_proof_entries');

        // Testimonials stats
        $this->db->where('status', 'requested');
        $testimonials_pending = (int) $this->db->count_all_results(db_prefix() . 'dw_testimonials');

        $this->db->where('status', 'received');
        $testimonials_received = (int) $this->db->count_all_results(db_prefix() . 'dw_testimonials');

        // Projects without proof (closed but no proof entry)
        $proof_backlog = 0;
        try {
            $sql = 'SELECT COUNT(*) as cnt FROM ' . db_prefix() . 'projects p 
                    WHERE p.status = 4 
                    AND NOT EXISTS (SELECT 1 FROM ' . db_prefix() . 'dw_proof_entries pe WHERE pe.project_id = p.id)';
            $result = $this->db->query($sql)->row();
            $proof_backlog = $result ? (int) $result->cnt : 0;
        } catch (Exception $e) {
            log_activity('disowebs_ops proof_backlog query error: ' . $e->getMessage());
        }

        return [
            'proof_entries' => $proof_count,
            'testimonials_pending' => $testimonials_pending,
            'testimonials_received' => $testimonials_received,
            'proof_backlog' => $proof_backlog,
        ];
    }

    public function just_execution_widget()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->view('disowebs_ops/widgets/ceo_weekly_execution');
    }

    private function render_section($title, $section_key)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $data['title'] = $title;
        $data['section_key'] = $section_key;

        $this->load->view('disowebs_ops/admin/section', $data);
    }

    private function apply_project_permission_filter($projects_table = 'projects')
    {
        if (!staff_cant('view', 'projects')) {
            return;
        }

        $projects_table = $projects_table ?: db_prefix() . 'projects';
        $staff_id = (int) get_staff_user_id();
        $this->db->where($projects_table . '.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . $staff_id . ')', null, false);
    }

    private function get_scope_change_requests()
    {
        $change_requests_table = db_prefix() . 'dw_change_requests';
        $projects_table = db_prefix() . 'projects';
        $staff_table = db_prefix() . 'staff';

        $this->db->select('cr.id, cr.project_id, cr.title, cr.status, cr.impact_days, cr.impact_cost, cr.created_at, cr.created_by');
        $this->db->select('projects.name as project_name');
        $this->db->select('staff.firstname, staff.lastname');
        $this->db->from($change_requests_table . ' as cr');
        $this->db->join($projects_table . ' as projects', 'projects.id = cr.project_id', 'left');
        $this->db->join($staff_table . ' as staff', 'staff.staffid = cr.created_by', 'left');
        $this->apply_project_permission_filter('projects');
        $this->db->order_by('cr.created_at', 'desc');
        $this->db->limit(50);

        return $this->db->get()->result_array();
    }

    private function get_scope_snapshots()
    {
        $snapshots_table = db_prefix() . 'dw_scope_snapshots';
        $projects_table = db_prefix() . 'projects';

        $this->db->select('snapshots.id, snapshots.project_id, snapshots.source_type, snapshots.source_id, snapshots.created_at');
        $this->db->select('projects.name as project_name');
        $this->db->from($snapshots_table . ' as snapshots');
        $this->db->join($projects_table . ' as projects', 'projects.id = snapshots.project_id', 'left');
        $this->apply_project_permission_filter('projects');
        $this->db->order_by('snapshots.created_at', 'desc');
        $this->db->limit(50);

        return $this->db->get()->result_array();
    }

    private function get_recent_proof_entries()
    {
        $proof_entries_table = db_prefix() . 'dw_proof_entries';
        $projects_table = db_prefix() . 'projects';
        $staff_table = db_prefix() . 'staff';

        $this->db->select('proof.id, proof.project_id, proof.title, proof.created_at, proof.created_by');
        $this->db->select('projects.name as project_name');
        $this->db->select('staff.firstname, staff.lastname');
        $this->db->from($proof_entries_table . ' as proof');
        $this->db->join($projects_table . ' as projects', 'projects.id = proof.project_id', 'left');
        $this->db->join($staff_table . ' as staff', 'staff.staffid = proof.created_by', 'left');
        $this->apply_project_permission_filter('projects');
        $this->db->order_by('proof.created_at', 'desc');
        $this->db->limit(50);

        $entries = $this->db->get()->result_array();
        if (empty($entries)) {
            return [];
        }

        $ids = array_map(function ($row) {
            return (int) $row['id'];
        }, $entries);
        $ids = array_filter(array_unique($ids));

        $files_count = [];
        if (!empty($ids)) {
            $this->db->select('proof_entry_id, COUNT(*) as total');
            $this->db->from(db_prefix() . 'dw_proof_files');
            $this->db->where_in('proof_entry_id', $ids);
            $this->db->group_by('proof_entry_id');
            $rows = $this->db->get()->result_array();
            foreach ($rows as $row) {
                $files_count[(int) $row['proof_entry_id']] = (int) $row['total'];
            }
        }

        foreach ($entries as &$entry) {
            $entry['files_count'] = $files_count[(int) $entry['id']] ?? 0;
        }
        unset($entry);

        return $entries;
    }

    private function get_proof_totals()
    {
        $projects_table = db_prefix() . 'projects';
        $proof_entries_table = db_prefix() . 'dw_proof_entries';
        $proof_files_table = db_prefix() . 'dw_proof_files';

        $this->db->from($proof_entries_table . ' as proof');
        $this->db->join($projects_table . ' as projects', 'projects.id = proof.project_id', 'inner');
        $this->apply_project_permission_filter('projects');
        $total_entries = (int) $this->db->count_all_results();

        $this->db->select('COUNT(DISTINCT proof.id) as total');
        $this->db->from($proof_entries_table . ' as proof');
        $this->db->join($proof_files_table . ' as files', 'files.proof_entry_id = proof.id', 'inner');
        $this->db->join($projects_table . ' as projects', 'projects.id = proof.project_id', 'inner');
        $this->apply_project_permission_filter('projects');
        $row = $this->db->get()->row();
        $entries_with_files = $row ? (int) $row->total : 0;

        $this->db->select('COUNT(files.id) as total');
        $this->db->from($proof_files_table . ' as files');
        $this->db->join($proof_entries_table . ' as proof', 'proof.id = files.proof_entry_id', 'inner');
        $this->db->join($projects_table . ' as projects', 'projects.id = proof.project_id', 'inner');
        $this->apply_project_permission_filter('projects');
        $row = $this->db->get()->row();
        $total_files = $row ? (int) $row->total : 0;

        return [
            'entries' => $total_entries,
            'entries_with_files' => $entries_with_files,
            'files' => $total_files,
        ];
    }

    // =====================================================
    // KPI DETAIL PAGES
    // =====================================================

    /**
     * Projects with missing demos this week
     */
    public function demo_missing()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_weekly_demos_model');
        
        $data['title'] = _l('disowebs_ops_demo_missing_projects');
        $data['projects'] = $this->dw_weekly_demos_model->get_projects_missing_demo();
        
        $this->load->view('disowebs_ops/admin/kpi/demo_missing', $data);
    }

    /**
     * Active blockers list
     */
    public function blockers($filter = 'active')
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_milestone_blockers_model');
        
        $data['title'] = _l('disowebs_ops_blockers');
        $data['filter'] = $filter;
        
        if ($filter === 'overdue') {
            $data['blockers'] = $this->dw_milestone_blockers_model->get_overdue_blockers();
        } else {
            $data['blockers'] = $this->dw_milestone_blockers_model->get_unresolved(null, 100);
        }
        
        $data['staff_members'] = get_staff('', ['active' => 1]);
        
        $this->load->view('disowebs_ops/admin/kpi/blockers', $data);
    }

    /**
     * Payment gates - deposit pending
     */
    public function deposit_pending()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_project_gates_model');
        
        $data['title'] = _l('disowebs_ops_deposit_pending');
        $data['projects'] = $this->dw_project_gates_model->get_projects_with_pending_deposit();
        
        $this->load->view('disowebs_ops/admin/kpi/deposit_pending', $data);
    }

    /**
     * Payment gates - final payment pending
     */
    public function final_pending()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_project_gates_model');
        
        $data['title'] = _l('disowebs_ops_final_payment_pending');
        $data['projects'] = $this->dw_project_gates_model->get_projects_with_pending_final();
        
        $this->load->view('disowebs_ops/admin/kpi/final_pending', $data);
    }

    /**
     * Training pending projects
     */
    public function training_pending()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_project_gates_model');
        
        $data['title'] = _l('disowebs_ops_training_pending');
        $data['projects'] = $this->dw_project_gates_model->get_projects_with_pending_training();
        
        $this->load->view('disowebs_ops/admin/kpi/training_pending', $data);
    }

    /**
     * Milestones delivered in period
     */
    public function milestones_delivered()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $period = $this->input->get('period') ?: 'week';
        $range = disowebs_ops_get_date_range_for_key($period);
        
        $this->load->model('disowebs_ops/dw_project_milestones_model');
        
        $data['title'] = _l('disowebs_ops_milestones_delivered');
        $data['milestones'] = $this->dw_project_milestones_model->get_completed_in_range($range['start'], $range['end']);
        $data['period'] = $period;
        $data['range'] = $range;
        
        $this->load->view('disowebs_ops/admin/kpi/milestones_delivered', $data);
    }

    /**
     * Testimonials management page
     */
    public function testimonials($filter = 'all')
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_testimonials_model');
        
        $data['title'] = _l('disowebs_ops_testimonials');
        $data['filter'] = $filter;
        
        if ($filter === 'pending') {
            $data['testimonials'] = $this->dw_testimonials_model->get_pending_approval();
        } elseif ($filter === 'approved') {
            $data['testimonials'] = $this->dw_testimonials_model->get_all_with_details();
            $data['testimonials'] = array_filter($data['testimonials'], function($t) {
                return $t['status'] === 'approved';
            });
        } elseif ($filter === 'requested') {
            $data['testimonials'] = $this->dw_testimonials_model->get_all_with_details();
            $data['testimonials'] = array_filter($data['testimonials'], function($t) {
                return $t['status'] === 'requested';
            });
        } else {
            $data['testimonials'] = $this->dw_testimonials_model->get_all_with_details();
        }
        
        $data['stats'] = $this->dw_testimonials_model->get_growth_stats();
        
        $this->load->view('disowebs_ops/admin/kpi/testimonials', $data);
    }

    /**
     * Referrals management page
     */
    public function referrals()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_referrals_model');
        
        $data['title'] = _l('disowebs_ops_referrals');
        $data['referrals'] = $this->dw_referrals_model->get_all_with_details();
        $data['stats'] = $this->dw_referrals_model->get_stats(90);
        $data['top_referrers'] = $this->dw_referrals_model->get_top_referrers(10);
        
        $this->load->view('disowebs_ops/admin/kpi/referrals', $data);
    }

    /**
     * Upcoming demos list
     */
    public function demos()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_weekly_demos_model');
        
        $data['title'] = _l('disowebs_ops_demos_title');
        $data['upcoming_demos'] = $this->dw_weekly_demos_model->get_upcoming_demos(30);
        $data['recent_demos'] = $this->dw_weekly_demos_model->get_recent_demos(30);
        $data['stats'] = $this->dw_weekly_demos_model->get_demo_stats();
        
        $this->load->view('disowebs_ops/admin/kpi/demos', $data);
    }

    /**
     * Qualified Leads KPI Page
     */
    public function qualified_leads()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_dashboard_model');
        
        $data['title'] = _l('disowebs_ops_qualified_leads_title');
        $data['leads'] = $this->dw_dashboard_model->get_qualified_leads_list();
        $data['stats'] = $this->dw_dashboard_model->get_pipeline_forecast_stats();
        
        $this->load->view('disowebs_ops/admin/kpi/qualified_leads', $data);
    }

    /**
     * Proposals Sent KPI Page
     */
    public function proposals_sent()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_dashboard_model');
        
        $data['title'] = _l('disowebs_ops_proposals_sent_title');
        $data['proposals'] = $this->dw_dashboard_model->get_proposals_sent_list();
        $data['stats'] = $this->dw_dashboard_model->get_pipeline_forecast_stats();
        
        $this->load->view('disowebs_ops/admin/kpi/proposals_sent', $data);
    }

    /**
     * Pipeline Forecast KPI Page
     */
    public function pipeline_forecast($days = 30)
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_dashboard_model');
        
        $days = (int) $days;
        if (!in_array($days, [30, 60])) {
            $days = 30;
        }
        
        $data['title'] = _l('disowebs_ops_forecast_title', $days);
        $data['days'] = $days;
        $data['proposals'] = $this->dw_dashboard_model->get_proposals_forecast_list(null, $days);
        $data['stats'] = $this->dw_dashboard_model->get_pipeline_forecast_stats();
        
        $currency = get_base_currency();
        $data['currency_name'] = $currency ? $currency->name : '';
        
        $this->load->view('disowebs_ops/admin/kpi/pipeline_forecast', $data);
    }

    /**
     * Stale Leads KPI Page
     */
    public function stale_leads()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_dashboard_model');
        
        $data['title'] = _l('disowebs_ops_stale_leads_title');
        $data['leads'] = $this->dw_dashboard_model->get_stale_leads_list(null, 7);
        $data['stats'] = $this->dw_dashboard_model->get_crm_pipeline_hygiene_stats();
        
        $this->load->view('disowebs_ops/admin/kpi/stale_leads', $data);
    }

    /**
     * Followups Overdue KPI Page
     */
    public function followups_overdue()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_dashboard_model');
        
        $data['title'] = _l('disowebs_ops_followups_overdue_title');
        $data['leads'] = $this->dw_dashboard_model->get_followups_overdue_list();
        $data['stats'] = $this->dw_dashboard_model->get_crm_pipeline_hygiene_stats();
        
        $this->load->view('disowebs_ops/admin/kpi/followups_overdue', $data);
    }

    /**
     * Documentation KPI Page - Projects missing documentation
     */
    public function docs($filter = 'pending')
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_dashboard_model');
        
        $data['title'] = _l('disowebs_ops_docs_pending_title');
        $data['projects'] = $this->dw_dashboard_model->get_projects_missing_docs_list();
        $data['stats'] = $this->dw_dashboard_model->get_documentation_stats();
        $data['filter'] = $filter;
        
        $this->load->view('disowebs_ops/admin/kpi/docs_pending', $data);
    }

    /**
     * Stale Proposals KPI Page
     */
    public function stale_proposals()
    {
        if (!has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'view')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_dashboard_model');
        
        $data['title'] = _l('disowebs_ops_stale_proposals_title');
        $data['proposals'] = $this->dw_dashboard_model->get_stale_proposals_list(null, 7);
        $data['stats'] = $this->dw_dashboard_model->get_crm_pipeline_hygiene_stats();
        
        $this->load->view('disowebs_ops/admin/kpi/stale_proposals', $data);
    }

    /**
     * Settings page for Disowebs Ops module
     */
    public function settings()
    {
        if (!is_admin()) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        if ($this->input->post()) {
            $settings = $this->input->post('settings');

            if (is_array($settings)) {
                foreach ($settings as $key => $value) {
                    if ($key === 'disowebs_ops_required_lead_fields') {
                        // Handle array of lead fields
                        $value = is_array($value) ? json_encode($value) : json_encode(['name', 'email']);
                    }
                    update_option($key, $value);
                }
            }

            set_alert('success', _l('settings_updated'));
            redirect(admin_url('disowebs_ops/settings'));
        }

        $data['title'] = _l('disowebs_ops') . ' - ' . _l('settings');
        $this->load->view('disowebs_ops/admin/settings', $data);
    }

    /**
     * CEO Dashboard - Executive overview of all operations
     */
    public function ceo_dashboard()
    {
        if (!is_admin() && !has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'access_ceo_dashboard')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_dashboard_model');
        $this->load->model('disowebs_ops/dw_referrals_model');
        $this->load->model('disowebs_ops/dw_margin_alerts_model');

        $currency = get_base_currency();
        $currency_name = $currency ? $currency->name : '';

        // Pipeline & Forecast
        $data['pipeline_stats'] = $this->dw_dashboard_model->get_pipeline_forecast_stats();
        $data['pipeline_hygiene'] = $this->dw_dashboard_model->get_crm_pipeline_hygiene_stats();

        // Delivery Engine
        $data['delivery_stats'] = $this->dw_dashboard_model->get_delivery_engine_stats();
        $data['margin_alerts'] = $this->dw_margin_alerts_model->get_dashboard_alerts(5);

        // Revenue & Cashflow
        $data['revenue_stats'] = $this->dw_dashboard_model->get_revenue_cashflow_stats();

        // Risk Alerts
        $data['risk_stats'] = $this->dw_dashboard_model->get_risk_alerts_stats();

        // Growth Flywheel
        $data['growth_stats'] = $this->dw_dashboard_model->get_growth_flywheel_stats();
        $data['referral_stats'] = $this->dw_referrals_model->get_stats(90);

        // Weekly Execution
        $range = disowebs_ops_get_date_range_for_key('week');
        $data['execution_stats'] = $this->dw_dashboard_model->get_weekly_execution_stats($range);

        // Capacity
        $this->db->where('status', 2);
        $data['active_projects'] = (int) $this->db->count_all_results(db_prefix() . 'projects');
        $data['project_limit'] = (int) get_option('disowebs_ops_active_project_limit') ?: 4;

        $data['currency_name'] = $currency_name;
        $data['title'] = _l('disowebs_ops_ceo_dashboard');

        $this->load->view('disowebs_ops/admin/dashboards/ceo_dashboard', $data);
    }

    /**
     * CRM Manager Dashboard - Sales and client relationship overview
     */
    public function crm_dashboard()
    {
        if (!is_admin() && !has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'access_crm_dashboard')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_dashboard_model');
        $this->load->model('disowebs_ops/dw_referrals_model');

        $currency = get_base_currency();
        $currency_name = $currency ? $currency->name : '';

        // Pipeline Stats
        $data['pipeline_stats'] = $this->dw_dashboard_model->get_pipeline_forecast_stats();
        $data['pipeline_hygiene'] = $this->dw_dashboard_model->get_crm_pipeline_hygiene_stats();

        // CRM Manager Priorities
        $data['crm_stats'] = $this->dw_dashboard_model->get_crm_manager_stats();

        // Active Projects Overview
        $data['active_projects'] = $this->dw_dashboard_model->get_crm_active_projects_overview();

        // Retention Stats
        $data['retention_stats'] = $this->dw_dashboard_model->get_retention_stats();

        // Retainer Tracker
        $data['retainer_stats'] = $this->dw_dashboard_model->get_retainer_tracker_stats();

        // Growth - Testimonials & Referrals
        $data['growth_stats'] = $this->dw_dashboard_model->get_growth_flywheel_stats();
        $data['referral_stats'] = $this->dw_referrals_model->get_stats(90);

        $data['currency_name'] = $currency_name;
        $data['title'] = _l('disowebs_ops_crm_dashboard');

        $this->load->view('disowebs_ops/admin/dashboards/crm_dashboard', $data);
    }

    /**
     * TSA Dashboard - Technical Support Agent operations
     */
    public function tsa_dashboard()
    {
        if (!is_admin() && !has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'access_tsa_dashboard')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->model('disowebs_ops/dw_dashboard_model');

        // Support Queue Stats
        $data['support_stats'] = $this->dw_dashboard_model->get_support_queue_stats();

        // QA Readiness
        $data['qa_stats'] = $this->dw_dashboard_model->get_qa_readiness_stats();

        // Proof Backlog
        $data['proof_stats'] = $this->dw_dashboard_model->get_proof_backlog_stats();

        // Documentation Status
        $data['docs_stats'] = $this->dw_dashboard_model->get_documentation_stats();

        // Active Tasks for Staff - use task_assigned table
        $staff_id = get_staff_user_id();
        $this->db->select('COUNT(DISTINCT t.id) as total', FALSE);
        $this->db->from(db_prefix() . 'tasks as t');
        $this->db->join(db_prefix() . 'task_assigned as ta', 'ta.taskid = t.id', 'left');
        $this->db->where('t.status !=', 5); // Not completed
        $this->db->where('ta.staffid', $staff_id);
        $result = $this->db->get()->row();
        $data['my_tasks_count'] = $result ? (int) $result->total : 0;

        // Projects needing attention
        $data['projects_needing_proof'] = $this->dw_dashboard_model->get_projects_needing_proof();
        $data['projects_needing_docs'] = $this->dw_dashboard_model->get_projects_missing_documentation();

        $data['title'] = _l('disowebs_ops_tsa_dashboard');

        $this->load->view('disowebs_ops/admin/dashboards/tsa_dashboard', $data);
    }

    /**
     * Developer Dashboard - Lead Developer Operations Center
     * Comprehensive view for build, deploy, and maintain workflows
     */
    public function dev_dashboard()
    {
        // CEO/Admin only - this is a privileged dashboard
        if (!is_admin() && !has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'access_ceo_dashboard')) {
            access_denied(DISOWEBS_OPS_MODULE_NAME);
        }

        $this->load->helper('text');
        $this->load->model('disowebs_ops/dw_dev_dashboard_model');

        // System Health
        $data['system_health'] = $this->dw_dev_dashboard_model->get_system_health();
        $data['performance'] = $this->dw_dev_dashboard_model->get_performance_metrics();

        // Project Pipeline
        $data['pipeline'] = $this->dw_dev_dashboard_model->get_project_pipeline();
        $data['active_projects'] = $this->dw_dev_dashboard_model->get_active_projects_detailed(10);

        // Task Metrics
        $data['task_metrics'] = $this->dw_dev_dashboard_model->get_dev_task_metrics();
        $data['urgent_tasks'] = $this->dw_dev_dashboard_model->get_my_urgent_tasks(10);

        // Deployment Pipeline
        $data['deployment'] = $this->dw_dev_dashboard_model->get_deployment_stats();
        $data['deploy_ready'] = $this->dw_dev_dashboard_model->get_projects_ready_for_deploy(5);

        // Support Metrics
        $data['support_metrics'] = $this->dw_dev_dashboard_model->get_support_metrics();
        $data['dev_tickets'] = $this->dw_dev_dashboard_model->get_tickets_needing_dev(10);

        // Technical Debt / Change Requests
        $data['cr_metrics'] = $this->dw_dev_dashboard_model->get_change_request_metrics();

        // Backup Status
        $data['backup_status'] = $this->dw_dev_dashboard_model->get_backup_status();

        // Recent Activity
        $data['recent_activity'] = $this->dw_dev_dashboard_model->get_recent_activity(15);

        // Quick Actions
        $data['quick_actions'] = $this->dw_dev_dashboard_model->get_quick_action_stats();

        // Weekly Summary
        $data['weekly_summary'] = $this->dw_dev_dashboard_model->get_weekly_summary();

        // SDLC Tools Data
        $this->load->model('disowebs_ops/dw_sdlc_model');
        $this->load->model('disowebs_ops/dw_dev_tools_model');
        
        // Get active tab
        $data['active_tab'] = $this->input->get('tab') ?: 'overview';
        
        // SDLC Stats for tool cards
        $data['sdlc_stats'] = $this->dw_dev_tools_model->get_dev_tools_stats();
        
        // Load tab-specific data based on active tab
        switch ($data['active_tab']) {
            case 'requirements':
                $data['requirements'] = $this->dw_sdlc_model->get_all_requirements();
                $data['requirements_stats'] = $this->dw_sdlc_model->get_requirements_stats();
                break;
            case 'scope':
                $data['scopes'] = $this->dw_sdlc_model->get_all_scopes();
                break;
            case 'snippets':
                $data['snippets'] = $this->dw_dev_tools_model->get_snippets(['limit' => 20]);
                $data['snippet_categories'] = $this->dw_dev_tools_model->get_snippet_categories();
                $data['snippet_languages'] = $this->dw_dev_tools_model->get_snippet_languages();
                break;
            case 'checklists':
                $data['checklist_templates'] = $this->dw_dev_tools_model->get_checklist_templates();
                $data['active_checklists'] = $this->dw_dev_tools_model->get_project_checklists(null);
                break;
            case 'notes':
                $data['dev_notes'] = $this->dw_sdlc_model->get_dev_notes(['limit' => 20, 'pinned_first' => true]);
                break;
            case 'estimates':
                $data['estimates'] = $this->dw_sdlc_model->get_all_estimates();
                break;
            case 'documentation':
                $data['project_docs'] = $this->dw_sdlc_model->get_all_docs();
                $data['docs_stats'] = $this->dw_sdlc_model->get_docs_stats();
                break;
            case 'planning':
                $data['planning_stats'] = $this->dw_sdlc_model->get_planning_stats();
                $data['projects_with_phases'] = $this->dw_sdlc_model->get_projects_with_phases();
                $data['upcoming_milestones'] = $this->dw_sdlc_model->get_upcoming_milestones(10);
                break;
            case 'technical':
                $data['technical_specs'] = $this->dw_sdlc_model->get_all_tech_specs();
                $data['tech_specs_counts'] = $this->dw_sdlc_model->get_tech_specs_counts();
                break;
        }
        
        // Get all projects for forms
        $this->db->select('id, name, clientid');
        $this->db->where_in('status', [1, 2, 3]);
        $this->db->order_by('name', 'ASC');
        $data['projects'] = $this->db->get(db_prefix() . 'projects')->result_array();

        $data['title'] = _l('disowebs_ops_dev_dashboard');

        $this->load->view('disowebs_ops/admin/dashboards/dev_dashboard', $data);
    }

    // =====================================================
    // SDLC TOOLS - AJAX ENDPOINTS
    // =====================================================

    /**
     * Save a code snippet (AJAX)
     */
    public function save_snippet()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_dev_tools_model');
        
        $data = $this->input->post();
        $id = $this->input->post('id');
        
        if ($id) {
            $success = $this->dw_dev_tools_model->update_snippet($id, $data);
        } else {
            $id = $this->dw_dev_tools_model->add_snippet($data);
            $success = $id ? true : false;
        }

        echo json_encode(['success' => $success, 'id' => $id]);
    }

    /**
     * Delete a code snippet (AJAX)
     */
    public function delete_snippet($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_dev_tools_model');
        $success = $this->dw_dev_tools_model->delete_snippet($id);
        
        echo json_encode(['success' => $success]);
    }

    /**
     * Get snippet by ID (AJAX)
     */
    public function get_snippet($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_dev_tools_model');
        $snippet = $this->dw_dev_tools_model->get_snippet($id);
        
        echo json_encode($snippet);
    }

    /**
     * Save a dev note (AJAX)
     */
    public function save_dev_note()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        
        $data = $this->input->post();
        $id = $this->input->post('id');
        
        if ($id) {
            $success = $this->dw_sdlc_model->update_dev_note($id, $data);
        } else {
            $id = $this->dw_sdlc_model->add_dev_note($data);
            $success = $id ? true : false;
        }

        echo json_encode(['success' => $success, 'id' => $id]);
    }

    /**
     * Toggle note pin status (AJAX)
     */
    public function toggle_note_pin($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $success = $this->dw_sdlc_model->toggle_note_pin($id);
        
        echo json_encode(['success' => $success]);
    }

    /**
     * Delete dev note (AJAX)
     */
    public function delete_dev_note($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $success = $this->dw_sdlc_model->delete_dev_note($id);
        
        echo json_encode(['success' => $success]);
    }

    /**
     * Save requirement (AJAX)
     */
    public function save_requirement()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        
        $data = $this->input->post();
        $id = $this->input->post('id');
        
        if ($id) {
            $success = $this->dw_sdlc_model->update_requirement($id, $data);
        } else {
            $id = $this->dw_sdlc_model->add_requirement($data);
            $success = $id ? true : false;
        }

        echo json_encode(['success' => $success, 'id' => $id]);
    }

    /**
     * Get requirement by ID (AJAX)
     */
    public function get_requirement($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $requirement = $this->dw_sdlc_model->get_requirement($id);
        
        echo json_encode($requirement);
    }

    /**
     * Delete requirement (AJAX)
     */
    public function delete_requirement($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $success = $this->dw_sdlc_model->delete_requirement($id);
        
        echo json_encode(['success' => $success]);
    }

    /**
     * Save scope document (AJAX)
     */
    public function save_scope()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        
        $data = $this->input->post();
        $id = $this->input->post('id');
        
        if ($id) {
            $success = $this->dw_sdlc_model->update_scope($id, $data);
        } else {
            $id = $this->dw_sdlc_model->add_scope($data);
            $success = $id ? true : false;
        }

        echo json_encode(['success' => $success, 'id' => $id]);
    }

    /**
     * Calculate PERT estimate (AJAX)
     */
    public function calculate_pert()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $optimistic = floatval($this->input->post('optimistic'));
        $most_likely = floatval($this->input->post('most_likely'));
        $pessimistic = floatval($this->input->post('pessimistic'));
        
        // PERT formula: (O + 4M + P) / 6
        $estimate = ($optimistic + (4 * $most_likely) + $pessimistic) / 6;
        $std_dev = ($pessimistic - $optimistic) / 6;
        
        echo json_encode([
            'estimate' => round($estimate, 2),
            'std_deviation' => round($std_dev, 2),
            'confidence_range' => [
                'low' => round($estimate - $std_dev, 2),
                'high' => round($estimate + $std_dev, 2),
            ]
        ]);
    }

    /**
     * Save time estimate (AJAX)
     */
    public function save_estimate()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        
        $data = $this->input->post();
        $id = $this->input->post('id');
        
        if ($id) {
            $success = $this->dw_sdlc_model->update_time_estimate($id, $data);
        } else {
            $id = $this->dw_sdlc_model->add_time_estimate($data);
            $success = $id ? true : false;
        }

        echo json_encode(['success' => $success, 'id' => $id]);
    }

    /**
     * Toggle checklist item (AJAX)
     */
    public function toggle_checklist_item($item_id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_dev_tools_model');
        $result = $this->dw_dev_tools_model->toggle_checklist_item($item_id);
        
        echo json_encode($result);
    }

    /**
     * Create checklist from template (AJAX)
     */
    public function create_checklist_from_template()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_dev_tools_model');
        
        $template_id = $this->input->post('template_id');
        $project_id = $this->input->post('project_id');
        $name = $this->input->post('name');
        
        $id = $this->dw_dev_tools_model->create_checklist_from_template($template_id, $project_id, $name);
        
        echo json_encode(['success' => $id ? true : false, 'id' => $id]);
    }

    // =====================================================
    // DOCUMENTATION ENDPOINTS
    // =====================================================

    /**
     * Save document (AJAX)
     */
    public function save_document()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        
        $data = $this->input->post();
        $id = $this->input->post('id');
        
        if ($id) {
            $success = $this->dw_sdlc_model->update_document($id, $data);
        } else {
            $id = $this->dw_sdlc_model->add_document($data);
            $success = $id ? true : false;
        }

        echo json_encode(['success' => $success, 'id' => $id]);
    }

    /**
     * Get document (AJAX)
     */
    public function get_document($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $doc = $this->dw_sdlc_model->get_document($id);
        
        echo json_encode($doc);
    }

    /**
     * Delete document (AJAX)
     */
    public function delete_document($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $success = $this->dw_sdlc_model->delete_document($id);
        
        echo json_encode(['success' => $success]);
    }

    /**
     * Duplicate document (AJAX)
     */
    public function duplicate_document($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $new_id = $this->dw_sdlc_model->duplicate_document($id);
        
        echo json_encode(['success' => $new_id ? true : false, 'id' => $new_id]);
    }

    /**
     * View document
     */
    public function view_document($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $data['doc'] = $this->dw_sdlc_model->get_document($id);
        
        if (!$data['doc']) {
            show_404();
        }

        // Increment views
        $this->dw_sdlc_model->increment_doc_views($id);
        
        $data['title'] = $data['doc']->title;
        $this->load->view('disowebs_ops/admin/sdlc/view_document', $data);
    }

    /**
     * Export document
     */
    public function export_document($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $doc = $this->dw_sdlc_model->get_document($id);
        
        if (!$doc) {
            show_404();
        }

        $filename = slug_it($doc->title) . '.md';
        
        header('Content-Type: text/markdown');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $doc->content;
    }

    // =====================================================
    // TECHNICAL SPECS ENDPOINTS
    // =====================================================

    /**
     * Save tech spec (AJAX)
     */
    public function save_tech_spec()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        
        $data = $this->input->post();
        $id = $this->input->post('id');
        
        if ($id) {
            $success = $this->dw_sdlc_model->update_tech_spec($id, $data);
        } else {
            $id = $this->dw_sdlc_model->add_tech_spec($data);
            $success = $id ? true : false;
        }

        echo json_encode(['success' => $success, 'id' => $id]);
    }

    /**
     * Get tech spec (AJAX)
     */
    public function get_tech_spec($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $spec = $this->dw_sdlc_model->get_tech_spec($id);
        
        echo json_encode($spec);
    }

    /**
     * Delete tech spec (AJAX)
     */
    public function delete_tech_spec($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $success = $this->dw_sdlc_model->delete_tech_spec($id);
        
        echo json_encode(['success' => $success]);
    }

    /**
     * View tech spec
     */
    public function view_tech_spec($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $data['spec'] = $this->dw_sdlc_model->get_tech_spec($id);
        
        if (!$data['spec']) {
            show_404();
        }
        
        $data['title'] = $data['spec']->title;
        $this->load->view('disowebs_ops/admin/sdlc/view_tech_spec', $data);
    }

    /**
     * Export tech spec
     */
    public function export_tech_spec($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->load->model('disowebs_ops/dw_sdlc_model');
        $spec = $this->dw_sdlc_model->get_tech_spec($id);
        
        if (!$spec) {
            show_404();
        }

        $filename = slug_it($spec->title) . '_v' . $spec->version . '.md';
        
        header('Content-Type: text/markdown');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $spec->content;
    }

    // =====================================================
    // PROJECT PLANNING ENDPOINTS
    // =====================================================

    /**
     * Get project phases (AJAX)
     */
    public function get_project_phases($project_id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_project_phases_model');
        $phases = $this->dw_project_phases_model->get_by_project($project_id);
        
        echo json_encode($phases);
    }

    /**
     * Save milestone (AJAX)
     */
    public function save_milestone()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_project_milestones_model');
        
        $data = $this->input->post();
        $id = $this->input->post('id');
        
        // Set status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'planned';
        }
        
        if ($id) {
            $success = $this->dw_project_milestones_model->update($id, $data);
        } else {
            $id = $this->dw_project_milestones_model->add($data);
            $success = $id ? true : false;
        }

        echo json_encode(['success' => $success, 'id' => $id]);
    }

    /**
     * Save a checklist (AJAX)
     */
    public function save_checklist()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $this->load->model('disowebs_ops/dw_dev_tools_model');
        
        $data = $this->input->post();
        $id = $this->input->post('id');
        
        // Remove empty id
        if (empty($id)) {
            unset($data['id']);
        }
        
        if ($id) {
            $success = $this->dw_dev_tools_model->update_checklist($id, $data);
        } else {
            $id = $this->dw_dev_tools_model->add_checklist($data);
            $success = $id ? true : false;
        }

        echo json_encode(['success' => $success, 'id' => $id]);
    }

    /**
     * Create phases from template (AJAX)
     */
    public function create_phases_from_template()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $project_id = $this->input->post('project_id');
        $template = $this->input->post('template');
        $create_milestones = $this->input->post('create_milestones') == '1';

        if (!$project_id) {
            echo json_encode(['success' => false, 'message' => 'Project ID required']);
            return;
        }

        $this->load->model('disowebs_ops/dw_project_phases_model');
        $this->load->model('disowebs_ops/dw_project_milestones_model');

        // Check if project already has phases
        $existing = $this->dw_project_phases_model->get_by_project($project_id);
        if (!empty($existing)) {
            echo json_encode(['success' => false, 'message' => 'Project already has phases']);
            return;
        }

        // Get phases based on template
        $phases = $this->get_template_phases($template);
        
        $position = 1;
        $milestone_offset = 0;
        
        foreach ($phases as $phase_name) {
            $phase_id = $this->dw_project_phases_model->add([
                'project_id' => (int) $project_id,
                'name'       => $phase_name,
                'position'   => $position,
                'status'     => 'not_started',
            ]);
            
            if ($create_milestones && $phase_id) {
                $milestone_offset = disowebs_ops_create_phase_milestones($project_id, $phase_id, $phase_name, $milestone_offset);
            }
            
            $position++;
        }

        echo json_encode(['success' => true]);
    }

    /**
     * Get template phases
     */
    private function get_template_phases($template)
    {
        $templates = [
            'default' => ['Discovery', 'Planning', 'Design', 'Development', 'Testing', 'Deployment', 'Support'],
            'agile' => ['Sprint Planning', 'Sprint 1', 'Sprint 2', 'Sprint 3', 'Sprint Review', 'Retrospective', 'Release'],
            'waterfall' => ['Requirements', 'System Design', 'Implementation', 'Integration', 'Testing', 'Deployment', 'Maintenance'],
        ];

        return $templates[$template] ?? $templates['default'];
    }
}
