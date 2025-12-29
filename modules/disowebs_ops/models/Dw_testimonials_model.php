<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Testimonials Model
 * 
 * Tracks testimonial requests and responses for Growth Flywheel
 * per the Disowebs OS V2 spec.
 */
class Dw_testimonials_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_testimonials';
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

    public function get_by_client($client_id)
    {
        $this->db->where('client_id', (int) $client_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    public function add($data)
    {
        if (empty($data['project_id'])) {
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

    public function request_testimonial($project_id, $client_id, $contact_id, $staff_id)
    {
        return $this->add([
            'project_id' => (int) $project_id,
            'client_id' => (int) $client_id,
            'contact_id' => $contact_id ? (int) $contact_id : null,
            'status' => 'requested',
            'requested_at' => date('Y-m-d H:i:s'),
            'requested_by' => (int) $staff_id,
        ]);
    }

    public function mark_received($id, $testimonial_text, $rating = null, $permission = false)
    {
        return $this->update($id, [
            'status' => 'received',
            'received_at' => date('Y-m-d H:i:s'),
            'testimonial_text' => $testimonial_text,
            'rating' => $rating,
            'permission_to_publish' => $permission ? 1 : 0,
        ]);
    }

    public function mark_published($id)
    {
        return $this->update($id, [
            'published' => 1,
            'published_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Approve a testimonial for publishing
     */
    public function approve($id, $staff_id, $notes = '')
    {
        $data = [
            'status' => 'approved',
            'approved_by' => (int) $staff_id,
            'approved_at' => date('Y-m-d H:i:s'),
            'permission_to_publish' => 1,
        ];
        if (!empty($notes)) {
            $data['approval_notes'] = $notes;
        }
        return $this->update($id, $data);
    }

    /**
     * Reject a testimonial
     */
    public function reject($id, $staff_id, $notes = '')
    {
        $data = [
            'status' => 'rejected',
            'approved_by' => (int) $staff_id,
            'approved_at' => date('Y-m-d H:i:s'),
        ];
        if (!empty($notes)) {
            $data['approval_notes'] = $notes;
        }
        return $this->update($id, $data);
    }

    /**
     * Get testimonials pending approval (received but not approved/rejected)
     */
    public function get_pending_approval()
    {
        $this->db->select('t.*, p.name as project_name, c.company as client_name');
        $this->db->from($this->table . ' as t');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = t.project_id', 'left');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = t.client_id', 'left');
        $this->db->where('t.status', 'received');
        $this->db->order_by('t.received_at', 'asc');
        return $this->db->get()->result_array();
    }

    /**
     * Get all testimonials with details
     */
    public function get_all_with_details()
    {
        $this->db->select('t.*, p.name as project_name, c.company as client_name, s.firstname, s.lastname');
        $this->db->from($this->table . ' as t');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = t.project_id', 'left');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = t.client_id', 'left');
        $this->db->join(db_prefix() . 'staff as s', 's.staffid = t.approved_by', 'left');
        $this->db->order_by('t.created_at', 'desc');
        return $this->db->get()->result_array();
    }

    /**
     * Count approved testimonials
     */
    public function count_approved()
    {
        $this->db->where('status', 'approved');
        return (int) $this->db->count_all_results($this->table);
    }

    /**
     * Count testimonials by status
     */
    public function count_by_status($status)
    {
        $this->db->where('status', $status);
        return (int) $this->db->count_all_results($this->table);
    }

    /**
     * Count requested this month
     */
    public function count_requested_this_month()
    {
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-t 23:59:59');

        $this->db->where('requested_at >=', $start);
        $this->db->where('requested_at <=', $end);
        return (int) $this->db->count_all_results($this->table);
    }

    /**
     * Count received this month
     */
    public function count_received_this_month()
    {
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-t 23:59:59');

        $this->db->where('status', 'received');
        $this->db->where('received_at >=', $start);
        $this->db->where('received_at <=', $end);
        return (int) $this->db->count_all_results($this->table);
    }

    /**
     * Get Growth Flywheel stats
     */
    public function get_growth_stats()
    {
        return [
            'requested_count' => $this->count_requested_this_month(),
            'received_count' => $this->count_received_this_month(),
            'pending_count' => $this->count_by_status('requested'),
            'publishable_count' => $this->count_approved(),
        ];
    }

    /**
     * Count testimonials with permission to publish
     */
    public function count_publishable()
    {
        $this->db->where('status', 'received');
        $this->db->where('permission_to_publish', 1);
        return (int) $this->db->count_all_results($this->table);
    }

    /**
     * Get projects finished in last N days without testimonial request
     */
    public function get_projects_needing_testimonial_request($days = 30)
    {
        $threshold = date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days'));
        
        $sql = 'SELECT p.id, p.name, p.clientid, p.date_finished 
                FROM ' . db_prefix() . 'projects p
                LEFT JOIN ' . $this->table . ' t ON t.project_id = p.id
                WHERE p.status = 4 
                AND p.date_finished >= ?
                AND t.id IS NULL
                ORDER BY p.date_finished DESC';

        return $this->db->query($sql, [$threshold])->result_array();
    }

    /**
     * Get all received testimonials ready for case studies
     */
    public function get_for_case_studies()
    {
        $this->db->select('t.*, p.name as project_name, c.company as client_name');
        $this->db->from($this->table . ' as t');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = t.project_id', 'left');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = t.client_id', 'left');
        $this->db->where('t.status', 'received');
        $this->db->where('t.permission_to_publish', 1);
        $this->db->order_by('t.received_at', 'desc');

        return $this->db->get()->result_array();
    }
}
