<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dw_dev_notes_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_dev_notes';
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_by_project($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->order_by('is_pinned', 'desc');
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    public function get_pinned($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->where('is_pinned', 1);
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

    public function toggle_pinned($id)
    {
        $note = $this->get($id);
        if (!$note) {
            return false;
        }

        return $this->update($id, ['is_pinned' => $note->is_pinned ? 0 : 1]);
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
}
