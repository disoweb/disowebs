<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dw_proof_entries_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_proof_entries';
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_by_project($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    public function add($data)
    {
        if (empty($data['project_id']) || empty($data['title']) || empty($data['problem']) || empty($data['solution']) || empty($data['outcome']) || empty($data['created_by'])) {
            return false;
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (!$id) {
            return false;
        }

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
}
