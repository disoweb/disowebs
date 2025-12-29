<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Weekly Demos Model
 * 
 * Tracks weekly demo requirements per the Disowebs OS V2 Delivery Engine spec.
 */
class Dw_weekly_demos_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_weekly_demos';
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_by_project($project_id)
    {
        $this->db->where('project_id', (int) $project_id);
        $this->db->order_by('week_start', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    public function get_current_week_demo($project_id)
    {
        $range = disowebs_ops_get_current_week_range();
        
        $this->db->where('project_id', (int) $project_id);
        $this->db->where('week_start', $range['week_start']);
        return $this->db->get($this->table)->row();
    }

    public function add($data)
    {
        if (empty($data['project_id']) || empty($data['week_start']) || empty($data['week_end']) || empty($data['created_by'])) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

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

    public function schedule_demo($project_id, $demo_date, $staff_id, $milestone_id = null)
    {
        $range = disowebs_ops_get_week_range(date('Y-m-d', strtotime($demo_date)));
        
        // Check if demo already exists for this week
        $existing = $this->get_demo_for_week($project_id, $range['week_start']);
        if ($existing) {
            return $this->update($existing->id, [
                'milestone_id' => $milestone_id ? (int) $milestone_id : null,
                'demo_date' => $demo_date,
                'demo_scheduled' => 1,
            ]);
        }

        return $this->add([
            'project_id' => (int) $project_id,
            'milestone_id' => $milestone_id ? (int) $milestone_id : null,
            'week_start' => $range['week_start'],
            'week_end' => $range['week_end'],
            'demo_scheduled' => 1,
            'demo_date' => $demo_date,
            'created_by' => (int) $staff_id,
        ]);
    }

    public function get_demo_for_week($project_id, $week_start)
    {
        $this->db->where('project_id', (int) $project_id);
        $this->db->where('week_start', $week_start);
        return $this->db->get($this->table)->row();
    }

    public function mark_completed($id, $notes = '', $feedback = '', $attendees = '')
    {
        return $this->update($id, [
            'demo_completed' => 1,
            'demo_completed_at' => date('Y-m-d H:i:s'),
            'notes' => $notes,
            'feedback' => $feedback,
            'attendees' => $attendees,
        ]);
    }

    /**
     * Get active projects without a demo scheduled for current week
     */
    public function get_projects_missing_demo()
    {
        $range = disowebs_ops_get_current_week_range();
        
        $sql = 'SELECT p.id as project_id, p.name as project_name, p.clientid, c.company as client_name,
                       ? as week_start, ? as week_end
                FROM ' . db_prefix() . 'projects p
                LEFT JOIN ' . db_prefix() . 'clients c ON c.userid = p.clientid
                LEFT JOIN ' . $this->table . ' d ON d.project_id = p.id AND d.week_start = ?
                WHERE p.status = 2
                AND d.id IS NULL
                ORDER BY p.name ASC';

        return $this->db->query($sql, [$range['week_start'], $range['week_end'], $range['week_start']])->result_array();
    }

    /**
     * Get demos scheduled but not completed for current week
     */
    public function get_pending_demos()
    {
        $range = disowebs_ops_get_current_week_range();

        $this->db->select('d.*, p.name as project_name');
        $this->db->from($this->table . ' as d');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = d.project_id', 'inner');
        $this->db->where('d.week_start', $range['week_start']);
        $this->db->where('d.demo_scheduled', 1);
        $this->db->where('d.demo_completed', 0);
        $this->db->order_by('d.demo_date', 'asc');

        return $this->db->get()->result_array();
    }

    /**
     * Get demo stats for dashboard
     */
    public function get_demo_stats()
    {
        $range = disowebs_ops_get_current_week_range();

        // Count active projects
        $this->db->from(db_prefix() . 'projects');
        $this->db->where('status', 2);
        $active_projects = (int) $this->db->count_all_results();

        // Count demos scheduled this week
        $this->db->from($this->table);
        $this->db->where('week_start', $range['week_start']);
        $this->db->where('demo_scheduled', 1);
        $demos_scheduled = (int) $this->db->count_all_results();

        // Count demos completed this week
        $this->db->from($this->table);
        $this->db->where('week_start', $range['week_start']);
        $this->db->where('demo_completed', 1);
        $demos_completed = (int) $this->db->count_all_results();

        return [
            'active_projects' => $active_projects,
            'demos_scheduled' => $demos_scheduled,
            'demos_completed' => $demos_completed,
            'missing_demos' => count($this->get_projects_missing_demo()),
        ];
    }

    /**
     * Get upcoming demos within the next X days
     * 
     * @param int $days Number of days to look ahead
     * @param int $limit Maximum number of demos to return
     * @return array
     */
    public function get_upcoming_demos($days = 7, $limit = 10)
    {
        $today = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime("+{$days} days"));

        $this->db->select($this->table . '.*, p.name as project_name');
        $this->db->from($this->table);
        $this->db->join(db_prefix() . 'projects as p', 'p.id = ' . $this->table . '.project_id', 'inner');
        $this->db->where($this->table . '.demo_scheduled', 1);
        $this->db->where($this->table . '.week_start >=', $today);
        $this->db->where($this->table . '.week_start <=', $end_date);
        $this->db->order_by($this->table . '.demo_date', 'asc');
        $this->db->order_by($this->table . '.week_start', 'asc');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Get recent completed demos
     * 
     * @param int $days Number of days to look back
     * @param int $limit Maximum number of demos to return
     * @return array
     */
    public function get_recent_demos($days = 30, $limit = 20)
    {
        $today = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime("-{$days} days"));

        $this->db->select($this->table . '.*, p.name as project_name, c.company as client_name');
        $this->db->from($this->table);
        $this->db->join(db_prefix() . 'projects as p', 'p.id = ' . $this->table . '.project_id', 'inner');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = p.clientid', 'left');
        $this->db->where($this->table . '.demo_completed', 1);
        $this->db->where($this->table . '.demo_date >=', $start_date);
        $this->db->where($this->table . '.demo_date <=', $today);
        $this->db->order_by($this->table . '.demo_date', 'desc');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }
}
