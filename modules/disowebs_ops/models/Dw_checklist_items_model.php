<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dw_checklist_items_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_checklist_items';
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_by_checklist($checklist_id)
    {
        $this->db->where('checklist_id', $checklist_id);
        $this->db->order_by('position', 'asc');
        return $this->db->get($this->table)->result_array();
    }

    public function add($data)
    {
        if (empty($data['checklist_id'])) {
            return false;
        }

        if (!isset($data['position'])) {
            $this->db->select_max('position');
            $this->db->where('checklist_id', $data['checklist_id']);
            $row = $this->db->get($this->table)->row();
            $data['position'] = $row && $row->position !== null ? ((int) $row->position + 1) : 1;
        }

        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;

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

    public function toggle_completed($id)
    {
        $item = $this->get($id);
        if (!$item) {
            return false;
        }

        $data = [
            'is_completed' => $item->is_completed ? 0 : 1,
            'completed_at' => $item->is_completed ? null : date('Y-m-d H:i:s'),
            'completed_by' => $item->is_completed ? null : get_staff_user_id()
        ];

        return $this->update($id, $data);
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
