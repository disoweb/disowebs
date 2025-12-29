<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Retainer Offers Model
 * 
 * Tracks retainer offers per the Disowebs OS V2 Retention Engine spec.
 * Policy: Retainer offer at day 21-30 post-launch.
 */
class Dw_retainer_offers_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_retainer_offers';
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

    public function get_latest_by_project($project_id)
    {
        $this->db->where('project_id', (int) $project_id);
        $this->db->order_by('created_at', 'desc');
        $this->db->limit(1);
        return $this->db->get($this->table)->row();
    }

    public function add($data)
    {
        if (empty($data['project_id']) || empty($data['client_id'])) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (!$id) {
            return false;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
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
     * Create retainer offer for project
     */
    public function create_offer($project_id, $client_id, $amount = null, $type = null, $staff_id = null, $auto = false)
    {
        // Calculate days since launch
        $days_since = $this->get_days_since_launch($project_id);

        return $this->add([
            'project_id' => (int) $project_id,
            'client_id' => (int) $client_id,
            'status' => 'pending',
            'offer_date' => date('Y-m-d'),
            'offer_amount' => $amount,
            'offer_type' => $type,
            'days_since_launch' => $days_since,
            'auto_generated' => $auto ? 1 : 0,
            'created_by' => $staff_id ? (int) $staff_id : null,
        ]);
    }

    public function mark_offered($id)
    {
        return $this->update($id, [
            'status' => 'offered',
            'offer_date' => date('Y-m-d'),
        ]);
    }

    public function mark_accepted($id, $subscription_id = null, $contract_id = null)
    {
        return $this->update($id, [
            'status' => 'accepted',
            'accepted' => 1,
            'accepted_at' => date('Y-m-d H:i:s'),
            'subscription_id' => $subscription_id,
            'contract_id' => $contract_id,
        ]);
    }

    public function mark_declined($id, $reason = '')
    {
        return $this->update($id, [
            'status' => 'declined',
            'declined' => 1,
            'declined_at' => date('Y-m-d H:i:s'),
            'decline_reason' => $reason,
        ]);
    }

    public function send_reminder($id)
    {
        return $this->update($id, [
            'reminder_sent' => 1,
            'reminder_sent_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get days since project launch (date_finished)
     */
    private function get_days_since_launch($project_id)
    {
        $this->db->select('date_finished');
        $this->db->from(db_prefix() . 'projects');
        $this->db->where('id', (int) $project_id);
        $row = $this->db->get()->row();

        if (!$row || !$row->date_finished) {
            return 0;
        }

        $finished = new DateTime($row->date_finished);
        $now = new DateTime();
        return max(0, $now->diff($finished)->days);
    }

    /**
     * Get projects eligible for retainer offer (day 21-30 post-launch)
     * Policy: Retainer offer at day 21-30 post-launch
     */
    public function get_eligible_projects()
    {
        $day_21 = date('Y-m-d', strtotime('-30 days'));
        $day_30 = date('Y-m-d', strtotime('-21 days'));

        $sql = 'SELECT p.id, p.name, p.clientid, p.date_finished,
                       DATEDIFF(NOW(), p.date_finished) as days_since_launch
                FROM ' . db_prefix() . 'projects p
                LEFT JOIN ' . $this->table . ' r ON r.project_id = p.id
                WHERE p.status = 4
                AND p.date_finished BETWEEN ? AND ?
                AND r.id IS NULL
                ORDER BY p.date_finished ASC';

        return $this->db->query($sql, [$day_21, $day_30])->result_array();
    }

    /**
     * Get projects past 30 days without retainer offer (overdue)
     */
    public function get_overdue_offers()
    {
        $threshold = date('Y-m-d', strtotime('-30 days'));

        $sql = 'SELECT p.id, p.name, p.clientid, p.date_finished,
                       DATEDIFF(NOW(), p.date_finished) as days_since_launch
                FROM ' . db_prefix() . 'projects p
                LEFT JOIN ' . $this->table . ' r ON r.project_id = p.id
                WHERE p.status = 4
                AND p.date_finished < ?
                AND r.id IS NULL
                ORDER BY p.date_finished ASC';

        return $this->db->query($sql, [$threshold])->result_array();
    }

    /**
     * Get pending offers that need follow-up
     */
    public function get_pending_offers($days_old = 7)
    {
        $threshold = date('Y-m-d', strtotime('-' . (int) $days_old . ' days'));

        $this->db->select('r.*, p.name as project_name, c.company as client_name');
        $this->db->from($this->table . ' as r');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = r.project_id', 'inner');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = r.client_id', 'left');
        $this->db->where('r.status', 'offered');
        $this->db->where('r.accepted', 0);
        $this->db->where('r.declined', 0);
        $this->db->where('r.offer_date <', $threshold);
        $this->db->order_by('r.offer_date', 'asc');

        return $this->db->get()->result_array();
    }

    /**
     * Get retention stats for dashboard
     */
    public function get_retention_stats($days = 30)
    {
        $threshold = date('Y-m-d', strtotime('-' . (int) $days . ' days'));

        // Count projects launched in period
        $this->db->from(db_prefix() . 'projects');
        $this->db->where('status', 4);
        $this->db->where('date_finished >=', $threshold);
        $launched = (int) $this->db->count_all_results();

        // Offers made in period
        $this->db->from($this->table);
        $this->db->where('offer_date >=', $threshold);
        $offered = (int) $this->db->count_all_results();

        // Accepted in period
        $this->db->from($this->table);
        $this->db->where('accepted', 1);
        $this->db->where('accepted_at >=', $threshold . ' 00:00:00');
        $accepted = (int) $this->db->count_all_results();

        // Declined in period
        $this->db->from($this->table);
        $this->db->where('declined', 1);
        $this->db->where('declined_at >=', $threshold . ' 00:00:00');
        $declined = (int) $this->db->count_all_results();

        // Total accepted retainers (all time)
        $this->db->from($this->table);
        $this->db->where('accepted', 1);
        $total_accepted = (int) $this->db->count_all_results();

        // Monthly retainer value (from accepted offers)
        $this->db->select('COALESCE(SUM(offer_amount), 0) as total');
        $this->db->from($this->table);
        $this->db->where('accepted', 1);
        $row = $this->db->get()->row();
        $monthly_value = $row ? (float) $row->total : 0.0;

        return [
            'launched' => $launched,
            'offered' => $offered,
            'accepted' => $accepted,
            'declined' => $declined,
            'total_accepted' => $total_accepted,
            'pending' => max(0, $offered - $accepted - $declined),
            'conversion_rate' => $offered > 0 ? round(($accepted / $offered) * 100, 1) : 0,
            'monthly_retainer_value' => $monthly_value,
        ];
    }

    /**
     * Get pending offers ready for activation (cron)
     */
    public function get_pending_for_activation()
    {
        $this->db->select('r.*, p.name as project_name, p.date_finished,
                          DATEDIFF(NOW(), p.date_finished) as days_since_launch');
        $this->db->from($this->table . ' as r');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = r.project_id', 'inner');
        $this->db->where('r.status', 'pending');
        $this->db->where('p.status', 4);
        
        return $this->db->get()->result_array();
    }

    /**
     * Mark offer as ready to send
     */
    public function mark_ready_to_offer($id)
    {
        return $this->update($id, [
            'reminder_sent' => 1,
            'reminder_sent_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Mark offer as expired
     */
    public function mark_expired($id)
    {
        return $this->update($id, [
            'status' => 'expired',
        ]);
    }
}
