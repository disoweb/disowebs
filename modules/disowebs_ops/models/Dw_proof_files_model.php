<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dw_proof_files_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_proof_files';
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_by_entry($proof_entry_id)
    {
        $this->db->where('proof_entry_id', $proof_entry_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    public function get_by_project($project_id)
    {
        $this->db->select($this->table . '.*');
        $this->db->from($this->table);
        $this->db->join(db_prefix() . 'dw_proof_entries', db_prefix() . 'dw_proof_entries.id = ' . $this->table . '.proof_entry_id');
        $this->db->where(db_prefix() . 'dw_proof_entries.project_id', $project_id);
        $this->db->order_by($this->table . '.created_at', 'desc');
        return $this->db->get()->result_array();
    }

    public function add($data)
    {
        if (empty($data['proof_entry_id']) || empty($data['file_path']) || empty($data['file_type'])) {
            return false;
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
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
