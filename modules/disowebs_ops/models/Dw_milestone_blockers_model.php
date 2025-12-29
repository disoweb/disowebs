<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Milestone Blockers Model
 * 
 * Tracks blockers for milestones with owner and next action tracking
 * per the Disowebs OS V2 Delivery Engine spec.
 */
class Dw_milestone_blockers_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_milestone_blockers';
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_by_milestone($milestone_id)
    {
        $this->db->where('milestone_id', (int) $milestone_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    public function get_by_project($project_id)
    {
        $this->db->where('project_id', (int) $project_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    public function get_unresolved_by_project($project_id)
    {
        $this->db->where('project_id', (int) $project_id);
        $this->db->where('resolved', 0);
        $this->db->order_by('next_action_date', 'asc');
        return $this->db->get($this->table)->result_array();
    }

    public function get_unresolved_by_milestone($milestone_id)
    {
        $this->db->where('milestone_id', (int) $milestone_id);
        $this->db->where('resolved', 0);
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get all unresolved blockers across all projects
     * 
     * @param int|null $project_id Optional project filter
     * @param int $limit Number of blockers to return
     * @return array
     */
    public function get_unresolved($project_id = null, $limit = 20)
    {
        $this->db->select($this->table . '.*, p.name as project_name, m.title as milestone_title');
        $this->db->join(db_prefix() . 'projects p', 'p.id = ' . $this->table . '.project_id', 'left');
        $this->db->join(db_prefix() . 'dw_project_milestones m', 'm.id = ' . $this->table . '.milestone_id', 'left');
        $this->db->where($this->table . '.resolved', 0);
        if ($project_id) {
            $this->db->where($this->table . '.project_id', (int) $project_id);
        }
        $this->db->order_by($this->table . '.next_action_date', 'asc');
        $this->db->limit($limit);
        return $this->db->get($this->table)->result_array();
    }

    public function add($data)
    {
        if (empty($data['milestone_id']) || empty($data['project_id']) || empty($data['description']) || empty($data['created_by'])) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        $data['resolved'] = 0;

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

    public function resolve($id, $staff_id)
    {
        return $this->update($id, [
            'resolved' => 1,
            'resolved_at' => date('Y-m-d H:i:s'),
            'resolved_by' => (int) $staff_id,
        ]);
    }

    public function unresolve($id)
    {
        return $this->update($id, [
            'resolved' => 0,
            'resolved_at' => null,
            'resolved_by' => null,
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

    public function count_unresolved_by_project($project_id)
    {
        $this->db->where('project_id', (int) $project_id);
        $this->db->where('resolved', 0);
        return (int) $this->db->count_all_results($this->table);
    }

    public function count_unresolved_by_milestone($milestone_id)
    {
        $this->db->where('milestone_id', (int) $milestone_id);
        $this->db->where('resolved', 0);
        return (int) $this->db->count_all_results($this->table);
    }

    /**
     * Get all unresolved blockers with overdue next_action_date
     */
    public function get_overdue_blockers($staff_id = null)
    {
        $today = date('Y-m-d');
        
        $this->db->select('b.*, m.title as milestone_title, p.name as project_name, s.firstname, s.lastname');
        $this->db->from($this->table . ' as b');
        $this->db->join(db_prefix() . 'dw_project_milestones as m', 'm.id = b.milestone_id', 'left');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = b.project_id', 'inner');
        $this->db->join(db_prefix() . 'staff as s', 's.staffid = b.owner_staff_id', 'left');
        $this->db->where('b.resolved', 0);
        $this->db->where('b.next_action_date <', $today);
        $this->db->where('b.next_action_date IS NOT NULL', null, false);

        if ($staff_id && !is_admin($staff_id)) {
            $this->db->where('b.owner_staff_id', (int) $staff_id);
        }

        $this->db->order_by('b.next_action_date', 'asc');
        return $this->db->get()->result_array();
    }

    /**
     * Get blockers with upcoming action dates (next 7 days)
     */
    public function get_upcoming_actions($days = 7, $staff_id = null)
    {
        $today = date('Y-m-d');
        $future = date('Y-m-d', strtotime('+' . (int) $days . ' days'));

        $this->db->select('b.*, m.title as milestone_title, p.name as project_name');
        $this->db->from($this->table . ' as b');
        $this->db->join(db_prefix() . 'dw_project_milestones as m', 'm.id = b.milestone_id', 'left');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = b.project_id', 'inner');
        $this->db->where('b.resolved', 0);
        $this->db->where('b.next_action_date >=', $today);
        $this->db->where('b.next_action_date <=', $future);

        if ($staff_id && !is_admin($staff_id)) {
            $this->db->where('b.owner_staff_id', (int) $staff_id);
        }

        $this->db->order_by('b.next_action_date', 'asc');
        return $this->db->get()->result_array();
    }

    /**
     * Count total unresolved blockers
     */
    public function count_all_unresolved($staff_id = null)
    {
        $this->db->from($this->table);
        $this->db->where('resolved', 0);

        if ($staff_id && !is_admin($staff_id)) {
            $this->db->where('owner_staff_id', (int) $staff_id);
        }

        return (int) $this->db->count_all_results();
    }
}
