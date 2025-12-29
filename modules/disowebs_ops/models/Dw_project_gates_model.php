<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Project Gates Model
 * 
 * Manages deposit gates, final payment gates, handover releases, and training tracking
 * per the Disowebs OS V2 Spec policies.
 */
class Dw_project_gates_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'dw_project_gates';
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_by_project($project_id)
    {
        $this->db->where('project_id', (int) $project_id);
        return $this->db->get($this->table)->row();
    }

    public function ensure_exists($project_id)
    {
        $existing = $this->get_by_project($project_id);
        if ($existing) {
            return $existing;
        }

        $now = date('Y-m-d H:i:s');
        $this->db->insert($this->table, [
            'project_id' => (int) $project_id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $this->get_by_project($project_id);
    }

    public function update_by_project($project_id, $data)
    {
        $this->ensure_exists($project_id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('project_id', (int) $project_id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Calculate deposit status based on project invoices
     * Deposit gate: 60-70% before Build phase
     */
    public function calculate_deposit_status($project_id)
    {
        $gate = $this->ensure_exists($project_id);
        $invoice_summary = disowebs_ops_get_project_invoice_summary($project_id);
        
        $total = (float) $invoice_summary['total'];
        $paid = (float) $invoice_summary['paid'];
        $deposit_threshold = $total * 0.60; // 60% minimum
        
        $update = [
            'deposit_required' => $deposit_threshold,
            'deposit_paid' => $paid,
        ];

        // Auto-clear deposit gate if 60%+ paid
        if ($paid >= $deposit_threshold && (int) $gate->deposit_cleared === 0) {
            $update['deposit_cleared'] = 1;
            $update['deposit_cleared_at'] = date('Y-m-d H:i:s');
        }

        return $this->update_by_project($project_id, $update);
    }

    /**
     * Calculate final payment status
     * Final payment gate: Full payment before handover
     */
    public function calculate_final_payment_status($project_id)
    {
        $gate = $this->ensure_exists($project_id);
        $invoice_summary = disowebs_ops_get_project_invoice_summary($project_id);
        
        $total = (float) $invoice_summary['total'];
        $paid = (float) $invoice_summary['paid'];
        $outstanding = (float) $invoice_summary['outstanding'];
        
        $update = [
            'final_payment_required' => $total,
            'final_payment_paid' => $paid,
        ];

        // Auto-clear final payment gate if fully paid
        if ($outstanding <= 0.01 && $total > 0 && (int) $gate->final_payment_cleared === 0) {
            $update['final_payment_cleared'] = 1;
            $update['final_payment_cleared_at'] = date('Y-m-d H:i:s');
        }

        return $this->update_by_project($project_id, $update);
    }

    public function clear_deposit_gate($project_id, $staff_id)
    {
        return $this->update_by_project($project_id, [
            'deposit_cleared' => 1,
            'deposit_cleared_at' => date('Y-m-d H:i:s'),
            'deposit_cleared_by' => (int) $staff_id,
        ]);
    }

    public function clear_final_payment_gate($project_id, $staff_id)
    {
        return $this->update_by_project($project_id, [
            'final_payment_cleared' => 1,
            'final_payment_cleared_at' => date('Y-m-d H:i:s'),
            'final_payment_cleared_by' => (int) $staff_id,
        ]);
    }

    public function release_handover($project_id, $staff_id)
    {
        return $this->update_by_project($project_id, [
            'handover_released' => 1,
            'handover_released_at' => date('Y-m-d H:i:s'),
            'handover_released_by' => (int) $staff_id,
        ]);
    }

    public function mark_training_completed($project_id, $notes = '')
    {
        return $this->update_by_project($project_id, [
            'training_completed' => 1,
            'training_completed_at' => date('Y-m-d H:i:s'),
            'training_notes' => $notes,
        ]);
    }

    public function can_proceed_to_build($project_id)
    {
        $gate = $this->get_by_project($project_id);
        if (!$gate) {
            return true; // No gate record = permissive
        }
        return (int) $gate->deposit_cleared === 1;
    }

    public function can_release_handover($project_id)
    {
        $gate = $this->get_by_project($project_id);
        if (!$gate) {
            return false;
        }
        return (int) $gate->final_payment_cleared === 1;
    }

    /**
     * Get projects with uncleared deposit gates that are trying to enter Build phase
     */
    public function get_deposit_warnings()
    {
        $CI = &get_instance();
        $CI->load->model('disowebs_ops/dw_project_phases_model');

        $this->db->select('g.*, p.name as project_name');
        $this->db->from($this->table . ' as g');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = g.project_id', 'inner');
        $this->db->where('g.deposit_cleared', 0);
        $this->db->where('p.status', 2); // In progress

        return $this->db->get()->result_array();
    }

    /**
     * Get projects with uncleared final payment that need handover
     */
    public function get_final_payment_warnings()
    {
        $this->db->select('g.*, p.name as project_name');
        $this->db->from($this->table . ' as g');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = g.project_id', 'inner');
        $this->db->where('g.final_payment_cleared', 0);
        $this->db->where('g.handover_released', 0);
        $this->db->where('p.status', 4); // Finished

        return $this->db->get()->result_array();
    }

    /**
     * Count projects without training completed (finished projects)
     */
    public function count_missing_training()
    {
        $this->db->from($this->table . ' as g');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = g.project_id', 'inner');
        $this->db->where('g.training_completed', 0);
        $this->db->where('p.status', 4);

        return (int) $this->db->count_all_results();
    }

    /**
     * Get projects with pending deposit (deposit not cleared)
     */
    public function get_projects_with_pending_deposit()
    {
        $this->db->select('g.*, p.id as project_id, p.name as project_name, p.clientid, c.company as client_name, p.status as project_status');
        $this->db->from($this->table . ' as g');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = g.project_id', 'inner');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = p.clientid', 'left');
        $this->db->where('g.deposit_cleared', 0);
        $this->db->where('p.status', 2); // In progress
        $this->db->order_by('p.name', 'asc');

        return $this->db->get()->result_array();
    }

    /**
     * Get projects with pending final payment
     */
    public function get_projects_with_pending_final()
    {
        $this->db->select('g.*, p.id as project_id, p.name as project_name, p.clientid, c.company as client_name, p.status as project_status');
        $this->db->from($this->table . ' as g');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = g.project_id', 'inner');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = p.clientid', 'left');
        $this->db->where('g.final_payment_cleared', 0);
        $this->db->where_in('p.status', [2, 4]); // In progress or finished
        $this->db->order_by('p.name', 'asc');

        return $this->db->get()->result_array();
    }

    /**
     * Get projects with pending training (finished but training not completed)
     */
    public function get_projects_with_pending_training()
    {
        $this->db->select('g.*, p.id as project_id, p.name as project_name, p.clientid, c.company as client_name, p.date_finished');
        $this->db->from($this->table . ' as g');
        $this->db->join(db_prefix() . 'projects as p', 'p.id = g.project_id', 'inner');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = p.clientid', 'left');
        $this->db->where('g.training_completed', 0);
        $this->db->where('p.status', 4); // Finished
        $this->db->order_by('p.date_finished', 'desc');

        return $this->db->get()->result_array();
    }
}
