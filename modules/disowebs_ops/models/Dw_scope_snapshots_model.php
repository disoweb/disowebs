<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dw_scope_snapshots_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_scope_snapshots';
    }

    public function add($data)
    {
        if (empty($data['project_id']) || empty($data['source_type']) || empty($data['source_id']) || empty($data['snapshot_json'])) {
            return false;
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get_by_project($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->result_array();
    }

    public function get_latest_by_project($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->order_by('created_at', 'desc');
        return $this->db->get($this->table)->row_array();
    }

    public function exists_by_source($source_type, $source_id)
    {
        $this->db->where('source_type', $source_type);
        $this->db->where('source_id', $source_id);
        $this->db->limit(1);
        return (bool) $this->db->get($this->table)->row_array();
    }
}
