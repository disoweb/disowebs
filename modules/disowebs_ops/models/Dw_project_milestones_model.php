<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dw_project_milestones_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_project_milestones';
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_by_project($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->order_by('week_start', 'asc');
        $this->db->order_by('id', 'asc');
        return $this->db->get($this->table)->result_array();
    }

    public function count_by_project($project_id)
    {
        $this->db->where('project_id', $project_id);
        return (int) $this->db->count_all_results($this->table);
    }

    public function exists_for_phase($project_id, $phase_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->where('phase_id', $phase_id);
        $this->db->limit(1);
        return (bool) $this->db->get($this->table)->row_array();
    }

    public function add($data)
    {
        if (empty($data['project_id']) || empty($data['title']) || empty($data['week_start']) || empty($data['week_end'])) {
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

    /**
     * Get milestones completed within a date range
     */
    public function get_completed_in_range($start_date, $end_date)
    {
        $this->db->select('m.*, p.name as project_name, p.clientid, c.company as client_name');
        $this->db->from($this->table . ' as m');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = m.project_id', 'inner');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = p.clientid', 'left');
        $this->db->where('m.status', 'done');
        $this->db->where('m.done_at >=', $start_date . ' 00:00:00');
        $this->db->where('m.done_at <=', $end_date . ' 23:59:59');
        $this->db->order_by('m.done_at', 'desc');

        return $this->db->get()->result_array();
    }
}
