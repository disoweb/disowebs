<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Margin Alerts Model
 * 
 * Tracks margin alerts for CRs approved without proper impact consideration
 * per the Disowebs OS V2 Profit Engine spec.
 */
class Dw_margin_alerts_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_margin_alerts';
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_by_project($project_id)
    {
        $this->db->where('project_id', (int) $project_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    public function get_unacknowledged($project_id = null)
    {
        $this->db->where('acknowledged', 0);
        if ($project_id) {
            $this->db->where('project_id', (int) $project_id);
        }
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    public function add($data)
    {
        if (empty($data['project_id']) || empty($data['alert_type']) || empty($data['message'])) {
            return false;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['acknowledged'] = 0;

        if (!isset($data['severity'])) {
            $data['severity'] = 'warning';
        }

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function acknowledge($id, $staff_id)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, [
            'acknowledged' => 1,
            'acknowledged_by' => (int) $staff_id,
            'acknowledged_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete($id)
    {
        if (!$id) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Create CR without impact alert
     */
    public function alert_cr_no_impact($project_id, $change_request_id, $cr_title)
    {
        // Check if alert already exists
        $this->db->where('project_id', (int) $project_id);
        $this->db->where('change_request_id', (int) $change_request_id);
        $this->db->where('alert_type', 'cr_no_impact');
        if ($this->db->count_all_results($this->table) > 0) {
            return false;
        }

        return $this->add([
            'project_id' => (int) $project_id,
            'change_request_id' => (int) $change_request_id,
            'alert_type' => 'cr_no_impact',
            'message' => sprintf('Change Request "%s" was approved without impact cost or days specified.', $cr_title),
            'severity' => 'warning',
        ]);
    }

    /**
     * Create low margin alert
     */
    public function alert_low_margin($project_id, $margin_percent, $threshold = 20)
    {
        // Check if recent alert exists (last 24h)
        $recent = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $this->db->where('project_id', (int) $project_id);
        $this->db->where('alert_type', 'low_margin');
        $this->db->where('created_at >', $recent);
        if ($this->db->count_all_results($this->table) > 0) {
            return false;
        }

        $severity = $margin_percent < 10 ? 'critical' : 'warning';

        return $this->add([
            'project_id' => (int) $project_id,
            'alert_type' => 'low_margin',
            'message' => sprintf('Project margin is at %.1f%%, below the %.0f%% threshold.', $margin_percent, $threshold),
            'severity' => $severity,
        ]);
    }

    /**
     * Create margin erosion alert (CR impact eating into margin)
     */
    public function alert_margin_erosion($project_id, $cr_impact_total, $original_margin)
    {
        if ($original_margin <= 0) {
            return false;
        }

        $erosion_percent = ($cr_impact_total / $original_margin) * 100;
        if ($erosion_percent < 25) {
            return false; // Only alert if CRs have eroded 25%+ of margin
        }

        // Check if recent alert exists
        $recent = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $this->db->where('project_id', (int) $project_id);
        $this->db->where('alert_type', 'margin_erosion');
        $this->db->where('created_at >', $recent);
        if ($this->db->count_all_results($this->table) > 0) {
            return false;
        }

        return $this->add([
            'project_id' => (int) $project_id,
            'alert_type' => 'margin_erosion',
            'message' => sprintf('Change Requests have eroded %.1f%% of the original project margin.', $erosion_percent),
            'severity' => $erosion_percent > 50 ? 'critical' : 'warning',
        ]);
    }

    /**
     * Get count of unacknowledged alerts
     */
    public function count_unacknowledged($project_id = null)
    {
        $this->db->where('acknowledged', 0);
        if ($project_id) {
            $this->db->where('project_id', (int) $project_id);
        }
        return (int) $this->db->count_all_results($this->table);
    }

    /**
     * Get alerts for dashboard display
     */
    public function get_dashboard_alerts($limit = 10)
    {
        $this->db->select('a.*, p.name as project_name');
        $this->db->from($this->table . ' as a');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = a.project_id', 'inner');
        $this->db->where('a.acknowledged', 0);
        $this->db->order_by('a.created_at', 'desc');
        $this->db->limit((int) $limit);

        return $this->db->get()->result_array();
    }

    /**
     * Get alert stats by severity
     */
    public function get_stats()
    {
        $stats = [
            'total' => 0,
            'critical' => 0,
            'warning' => 0,
            'info' => 0,
        ];

        $results = $this->db
            ->select('severity, COUNT(*) as count')
            ->from($this->table)
            ->where('acknowledged', 0)
            ->group_by('severity')
            ->get()
            ->result_array();

        foreach ($results as $row) {
            $stats[$row['severity']] = (int) $row['count'];
            $stats['total'] += (int) $row['count'];
        }

        return $stats;
    }
}
