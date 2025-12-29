<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Project Profit Model
 * 
 * Tracks project profitability metrics and margin calculations
 * per the Disowebs OS V2 Profit Engine spec.
 */
class Dw_project_profit_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_project_profit';
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_by_project($project_id)
    {
        $this->db->where('project_id', (int) $project_id);
        return $this->db->get($this->table)->row();
    }

    public function ensure_exists($project_id)
    {
        $existing = $this->get_by_project($project_id);
        if ($existing) {
            return $existing;
        }

        $now = date('Y-m-d H:i:s');
        $this->db->insert($this->table, [
            'project_id' => (int) $project_id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $this->get_by_project($project_id);
    }

    public function update_by_project($project_id, $data)
    {
        $this->ensure_exists($project_id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('project_id', (int) $project_id);
        return $this->db->update($this->table, $data);
    }

    public function set_expected_values($project_id, $expected_revenue, $estimated_hours, $hourly_cost = 0)
    {
        $expected_cost = (float) $estimated_hours * (float) $hourly_cost;
        $expected_margin = (float) $expected_revenue - $expected_cost;

        return $this->update_by_project($project_id, [
            'expected_revenue' => (float) $expected_revenue,
            'estimated_hours' => (float) $estimated_hours,
            'hourly_cost_rate' => (float) $hourly_cost,
            'expected_cost' => $expected_cost,
            'expected_margin' => $expected_margin,
        ]);
    }

    /**
     * Recalculate actual values from Perfex data
     */
    public function recalculate($project_id)
    {
        $profit = $this->ensure_exists($project_id);

        // Get actual revenue from invoices
        $invoice_summary = disowebs_ops_get_project_invoice_summary($project_id);
        $actual_revenue = (float) $invoice_summary['paid'];

        // Get actual hours from tasks (if timesheet available)
        $actual_hours = $this->get_project_logged_hours($project_id);

        // Calculate CR impact
        $cr_impact = $this->get_cr_impact_total($project_id);

        // Calculate actuals
        $hourly_cost = (float) $profit->hourly_cost_rate;
        $actual_cost = $actual_hours * $hourly_cost;
        $actual_margin = $actual_revenue - $actual_cost;
        $margin_percent = $actual_revenue > 0 ? ($actual_margin / $actual_revenue) * 100 : 0;

        return $this->update_by_project($project_id, [
            'actual_revenue' => $actual_revenue,
            'actual_hours' => $actual_hours,
            'actual_cost' => $actual_cost,
            'actual_margin' => $actual_margin,
            'margin_percent' => round($margin_percent, 2),
            'cr_impact_total' => $cr_impact,
            'last_calculated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get total CR impact cost for project
     */
    private function get_cr_impact_total($project_id)
    {
        $this->db->select('COALESCE(SUM(impact_cost), 0) as total');
        $this->db->from(db_prefix() . 'dw_change_requests');
        $this->db->where('project_id', (int) $project_id);
        $this->db->where_in('status', ['approved', 'implemented']);
        $row = $this->db->get()->row();
        return $row ? (float) $row->total : 0.0;
    }

    /**
     * Get logged hours from tasks (Perfex timesheet)
     */
    private function get_project_logged_hours($project_id)
    {
        $timesheets_table = db_prefix() . 'taskstimers';
        $tasks_table = db_prefix() . 'tasks';

        if (!$this->db->table_exists($timesheets_table)) {
            return 0.0;
        }

        $this->db->select('COALESCE(SUM(tt.end_time - tt.start_time), 0) as total_seconds');
        $this->db->from($timesheets_table . ' as tt');
        $this->db->join($tasks_table . ' as t', 't.id = tt.task_id', 'inner');
        $this->db->where('t.rel_type', 'project');
        $this->db->where('t.rel_id', (int) $project_id);
        $this->db->where('tt.end_time >', 0);

        $row = $this->db->get()->row();
        $seconds = $row ? (float) $row->total_seconds : 0;

        return round($seconds / 3600, 2); // Convert to hours
    }

    /**
     * Get projects with low margin (< threshold %)
     */
    public function get_low_margin_projects($threshold = 20)
    {
        $this->db->select('pp.*, p.name as project_name');
        $this->db->from($this->table . ' as pp');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = pp.project_id', 'inner');
        $this->db->where('pp.margin_percent <', (float) $threshold);
        $this->db->where('pp.actual_revenue >', 0);
        $this->db->order_by('pp.margin_percent', 'asc');

        return $this->db->get()->result_array();
    }

    /**
     * Get projects where CR impact exists but margin not recalculated
     */
    public function get_projects_needing_recalculation()
    {
        // Projects with CRs that have been approved since last calculation
        $sql = 'SELECT pp.project_id, p.name as project_name, pp.last_calculated_at
                FROM ' . $this->table . ' pp
                INNER JOIN ' . db_prefix() . 'projects p ON p.id = pp.project_id
                WHERE pp.project_id IN (
                    SELECT project_id FROM ' . db_prefix() . 'dw_change_requests 
                    WHERE status IN ("approved", "implemented")
                    AND updated_at > COALESCE(pp.last_calculated_at, "2000-01-01")
                )';

        return $this->db->query($sql)->result_array();
    }

    /**
     * Get overall profit summary
     */
    public function get_profit_summary()
    {
        $this->db->select('
            COUNT(*) as projects_tracked,
            COALESCE(SUM(expected_revenue), 0) as total_expected_revenue,
            COALESCE(SUM(actual_revenue), 0) as total_actual_revenue,
            COALESCE(SUM(expected_cost), 0) as total_expected_cost,
            COALESCE(SUM(actual_cost), 0) as total_actual_cost,
            COALESCE(SUM(expected_margin), 0) as total_expected_margin,
            COALESCE(SUM(actual_margin), 0) as total_actual_margin,
            COALESCE(SUM(cr_impact_total), 0) as total_cr_impact,
            COALESCE(AVG(margin_percent), 0) as avg_margin_percent
        ');
        $this->db->from($this->table);

        $result = $this->db->get()->row_array();
        
        // Ensure all keys exist with defaults
        return [
            'projects_tracked' => (int) ($result['projects_tracked'] ?? 0),
            'total_expected_revenue' => (float) ($result['total_expected_revenue'] ?? 0),
            'total_actual_revenue' => (float) ($result['total_actual_revenue'] ?? 0),
            'total_expected_cost' => (float) ($result['total_expected_cost'] ?? 0),
            'total_actual_cost' => (float) ($result['total_actual_cost'] ?? 0),
            'total_expected_margin' => (float) ($result['total_expected_margin'] ?? 0),
            'total_actual_margin' => (float) ($result['total_actual_margin'] ?? 0),
            'total_cr_impact' => (float) ($result['total_cr_impact'] ?? 0),
            'avg_margin_percent' => (float) ($result['avg_margin_percent'] ?? 0),
        ];
    }

    /**
     * Get top performing projects by margin
     */
    public function get_top_margin_projects($limit = 5)
    {
        $this->db->select('pp.*, p.name as project_name, (pp.actual_revenue - pp.actual_cost) as net_profit');
        $this->db->from($this->table . ' as pp');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = pp.project_id', 'inner');
        $this->db->where('pp.actual_revenue >', 0);
        $this->db->order_by('pp.margin_percent', 'desc');
        $this->db->limit((int) $limit);

        return $this->db->get()->result_array();
    }
}
