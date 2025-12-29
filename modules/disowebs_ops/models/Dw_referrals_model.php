<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Referrals Model
 * 
 * Tracks referral program for Growth Flywheel
 * per the Disowebs OS V2 spec.
 */
class Dw_referrals_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_referrals';
    }

    /**
     * Get referral by ID
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Get referrals by referring client
     */
    public function get_by_referrer($client_id)
    {
        $this->db->where('referrer_client_id', (int) $client_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get referral by referred lead/client
     */
    public function get_by_referred($referred_type, $referred_id)
    {
        $this->db->where('referred_type', $referred_type);
        $this->db->where('referred_id', (int) $referred_id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Add new referral
     */
    public function add($data)
    {
        if (empty($data['referrer_client_id'])) {
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

    /**
     * Update referral
     */
    public function update($id, $data)
    {
        if (!$id) {
            return false;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete referral
     */
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
     * Record a new referral from a client
     */
    public function record_referral($referrer_client_id, $referred_type, $referred_id, $notes = '')
    {
        // Check if referral already exists
        $existing = $this->get_by_referred($referred_type, $referred_id);
        if ($existing) {
            return $existing->id;
        }

        return $this->add([
            'referrer_client_id' => (int) $referrer_client_id,
            'referred_type'      => $referred_type, // 'lead' or 'client'
            'referred_id'        => (int) $referred_id,
            'status'             => 'pending',
            'notes'              => $notes,
        ]);
    }

    /**
     * Mark referral as converted (lead became client)
     */
    public function mark_converted($id, $project_id = null, $revenue = 0)
    {
        return $this->update($id, [
            'status'       => 'converted',
            'converted_at' => date('Y-m-d H:i:s'),
            'project_id'   => $project_id,
            'revenue'      => $revenue,
        ]);
    }

    /**
     * Mark referral reward as paid
     */
    public function mark_reward_paid($id, $reward_amount, $reward_type = 'cash')
    {
        return $this->update($id, [
            'status'         => 'rewarded',
            'reward_paid_at' => date('Y-m-d H:i:s'),
            'reward_amount'  => $reward_amount,
            'reward_type'    => $reward_type,
        ]);
    }

    /**
     * Get referral statistics
     */
    public function get_stats($days = 90)
    {
        $threshold = date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days'));

        // Total referrals
        $this->db->where('created_at >=', $threshold);
        $total = (int) $this->db->count_all_results($this->table);

        // Converted referrals
        $this->db->where('status', 'converted');
        $this->db->or_where('status', 'rewarded');
        $this->db->where('created_at >=', $threshold);
        $converted = (int) $this->db->count_all_results($this->table);

        // Pending referrals
        $this->db->where('status', 'pending');
        $pending = (int) $this->db->count_all_results($this->table);

        // Total revenue from referrals
        $this->db->select('COALESCE(SUM(revenue), 0) as total');
        $this->db->from($this->table);
        $this->db->where('created_at >=', $threshold);
        $this->db->where_in('status', ['converted', 'rewarded']);
        $row = $this->db->get()->row();
        $revenue = $row ? (float) $row->total : 0;

        // Conversion rate
        $conversion_rate = $total > 0 ? round(($converted / $total) * 100, 1) : 0;

        return [
            'total'           => $total,
            'converted'       => $converted,
            'pending'         => $pending,
            'revenue'         => $revenue,
            'conversion_rate' => $conversion_rate,
        ];
    }

    /**
     * Get top referrers (clients who refer most)
     */
    public function get_top_referrers($limit = 10)
    {
        $this->db->select('r.referrer_client_id, c.company as client_name, COUNT(r.id) as referral_count, SUM(r.revenue) as total_revenue');
        $this->db->from($this->table . ' as r');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = r.referrer_client_id', 'left');
        $this->db->group_by('r.referrer_client_id');
        $this->db->order_by('referral_count', 'desc');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Get clients eligible for referral program
     * (completed projects with good testimonials)
     */
    public function get_eligible_referrers()
    {
        $sql = 'SELECT DISTINCT c.userid, c.company 
                FROM ' . db_prefix() . 'clients c
                INNER JOIN ' . db_prefix() . 'projects p ON p.clientid = c.userid
                LEFT JOIN ' . db_prefix() . 'dw_testimonials t ON t.client_id = c.userid
                WHERE p.status = 4 
                AND (t.status = "received" OR t.id IS NULL)
                ORDER BY c.company ASC';

        return $this->db->query($sql)->result_array();
    }

    /**
     * Get all referrals with details
     */
    public function get_all_with_details($limit = 50)
    {
        $this->db->select("r.*, c.company as referrer_name, 
                          CASE WHEN r.referred_type = 'lead' THEN l.name 
                               WHEN r.referred_type = 'client' THEN rc.company 
                          END as referred_name", FALSE);
        $this->db->from($this->table . ' as r');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = r.referrer_client_id', 'left');
        $this->db->join(db_prefix() . 'leads as l', "l.id = r.referred_id AND r.referred_type = 'lead'", 'left');
        $this->db->join(db_prefix() . 'clients as rc', "rc.userid = r.referred_id AND r.referred_type = 'client'", 'left');
        $this->db->order_by('r.created_at', 'desc');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }
}
