<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Adoption Baseline Model
 * 
 * Tracks client adoption metrics post-launch per the Disowebs OS V2 Retention Engine spec.
 * Policy: Capture adoption baseline to identify at-risk clients.
 */
class Dw_adoption_baseline_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_adoption_baseline';
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

    public function get_by_client($client_id)
    {
        $this->db->where('client_id', (int) $client_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    public function add($data)
    {
        if (empty($data['project_id']) || empty($data['client_id'])) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        if (!isset($data['baseline_date'])) {
            $data['baseline_date'] = date('Y-m-d');
        }

        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        // Calculate adoption score
        $data['adoption_score'] = $this->calculate_score($data);

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (!$id) {
            return false;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        // Recalculate score if metrics changed
        if (isset($data['logins_count']) || isset($data['training_sessions']) || isset($data['support_tickets'])) {
            $existing = $this->get($id);
            if ($existing) {
                $merged = array_merge((array) $existing, $data);
                $data['adoption_score'] = $this->calculate_score($merged);
            }
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function update_by_project($project_id, $data)
    {
        $existing = $this->get_by_project($project_id);
        if ($existing) {
            return $this->update($existing->id, $data);
        }
        return false;
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
     * Ensure adoption baseline exists for project
     */
    public function ensure_exists($project_id, $client_id)
    {
        $existing = $this->get_by_project($project_id);
        if ($existing) {
            return $existing;
        }

        $this->add([
            'project_id' => (int) $project_id,
            'client_id' => (int) $client_id,
            'status' => 'pending',
        ]);

        return $this->get_by_project($project_id);
    }

    /**
     * Calculate adoption score (0-100)
     * Score factors:
     * - Logins: 0-30 points (10+ logins = 30)
     * - Training: 0-30 points (3+ sessions = 30)
     * - Low tickets: 0-20 points (fewer tickets = better)
     * - Features used: 0-20 points
     */
    private function calculate_score($data)
    {
        $score = 0;

        // Login score (max 30)
        $logins = isset($data['logins_count']) ? (int) $data['logins_count'] : 0;
        $score += min(30, $logins * 3);

        // Training score (max 30)
        $training = isset($data['training_sessions']) ? (int) $data['training_sessions'] : 0;
        $score += min(30, $training * 10);

        // Support tickets score (max 20, inverse - fewer is better)
        $tickets = isset($data['support_tickets']) ? (int) $data['support_tickets'] : 0;
        if ($tickets === 0) {
            $score += 20;
        } elseif ($tickets <= 2) {
            $score += 15;
        } elseif ($tickets <= 5) {
            $score += 10;
        } elseif ($tickets <= 10) {
            $score += 5;
        }

        // Features used score (max 20)
        $features = isset($data['features_used']) ? $data['features_used'] : '';
        if (is_string($features) && !empty($features)) {
            $feature_count = count(array_filter(explode(',', $features)));
            $score += min(20, $feature_count * 4);
        }

        return min(100, $score);
    }

    /**
     * Get adoption status label based on score
     */
    public function get_status_label($score)
    {
        if ($score >= 70) {
            return 'healthy';
        } elseif ($score >= 40) {
            return 'at_risk';
        } else {
            return 'critical';
        }
    }

    /**
     * Get adoption status class for UI
     */
    public function get_status_class($score)
    {
        if ($score >= 70) {
            return 'success';
        } elseif ($score >= 40) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    /**
     * Update status based on score
     */
    public function update_status($id)
    {
        $baseline = $this->get($id);
        if (!$baseline) {
            return false;
        }

        $status = $this->get_status_label($baseline->adoption_score);
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Get at-risk clients (score < 70)
     */
    public function get_at_risk_clients($limit = 10)
    {
        $this->db->select('a.*, p.name as project_name, c.company as client_name');
        $this->db->from($this->table . ' as a');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = a.project_id', 'inner');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = a.client_id', 'left');
        $this->db->where('a.adoption_score <', 70);
        $this->db->order_by('a.adoption_score', 'asc');
        $this->db->limit((int) $limit);

        return $this->db->get()->result_array();
    }

    /**
     * Get critical clients (score < 40)
     */
    public function get_critical_clients($limit = 10)
    {
        $this->db->select('a.*, p.name as project_name, c.company as client_name');
        $this->db->from($this->table . ' as a');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = a.project_id', 'inner');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = a.client_id', 'left');
        $this->db->where('a.adoption_score <', 40);
        $this->db->order_by('a.adoption_score', 'asc');
        $this->db->limit((int) $limit);

        return $this->db->get()->result_array();
    }

    /**
     * Get adoption stats for dashboard
     */
    public function get_stats()
    {
        // Total tracked
        $this->db->from($this->table);
        $total = (int) $this->db->count_all_results();

        // Healthy (score >= 70)
        $this->db->from($this->table);
        $this->db->where('adoption_score >=', 70);
        $healthy = (int) $this->db->count_all_results();

        // At risk (40-69)
        $this->db->from($this->table);
        $this->db->where('adoption_score >=', 40);
        $this->db->where('adoption_score <', 70);
        $at_risk = (int) $this->db->count_all_results();

        // Critical (< 40)
        $this->db->from($this->table);
        $this->db->where('adoption_score <', 40);
        $critical = (int) $this->db->count_all_results();

        // Average score
        $this->db->select('COALESCE(AVG(adoption_score), 0) as avg_score');
        $this->db->from($this->table);
        $row = $this->db->get()->row();
        $avg_score = $row ? (float) $row->avg_score : 0;

        return [
            'total' => $total,
            'healthy' => $healthy,
            'at_risk' => $at_risk,
            'critical' => $critical,
            'avg_score' => round($avg_score, 1),
        ];
    }

    /**
     * Capture baseline for project (auto-calculate metrics)
     */
    public function capture_baseline($project_id, $client_id, $staff_id = null)
    {
        // Get support tickets count for client
        $this->db->where('userid', (int) $client_id);
        $tickets_count = (int) $this->db->count_all_results(db_prefix() . 'tickets');

        $data = [
            'project_id' => (int) $project_id,
            'client_id' => (int) $client_id,
            'baseline_date' => date('Y-m-d'),
            'logins_count' => 0, // Would need client portal login tracking
            'training_sessions' => 0,
            'support_tickets' => $tickets_count,
            'features_used' => '',
            'status' => 'captured',
            'captured_by' => $staff_id ? (int) $staff_id : null,
        ];

        return $this->add($data);
    }
}
