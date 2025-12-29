<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dw_sdlc_checklists_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_sdlc_checklists';
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_by_project($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->order_by('phase', 'asc');
        $this->db->order_by('created_at', 'asc');
        return $this->db->get($this->table)->result_array();
    }

    public function get_templates()
    {
        $this->db->where('is_template', 1);
        $this->db->order_by('title', 'asc');
        return $this->db->get($this->table)->result_array();
    }

    public function add($data)
    {
        if (empty($data['project_id']) && empty($data['is_template'])) {
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

        // Delete checklist items first
        $this->db->where('checklist_id', $id);
        $this->db->delete(db_prefix() . 'dw_checklist_items');

        $this->db->where('id', $id);
        $this->db->delete($this->table);

        return $this->db->affected_rows() > 0;
    }
}
