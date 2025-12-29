<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Disowebs Ops Module Uninstall Script
 * 
 * This script runs when the module is uninstalled.
 * It drops all dw_* tables created by the module.
 * WARNING: This will permanently delete all module data!
 */

$CI = &get_instance();

// Log uninstallation
log_activity('Disowebs Ops Module Uninstalled - All module data will be removed');

// ==============================================
// DROP ALL dw_* TABLES
// ==============================================

$tables_to_drop = [
    'dw_project_phases',
    'dw_project_milestones',
    'dw_scope_snapshots',
    'dw_change_requests',
    'dw_proof_entries',
    'dw_proof_files',
    'dw_project_gates',
    'dw_milestone_blockers',
    'dw_testimonials',
    'dw_project_profit',
    'dw_weekly_demos',
    'dw_retainer_offers',
    'dw_margin_alerts',
    'dw_adoption_baseline',
    'dw_followup_log',
    'dw_referrals',
];

foreach ($tables_to_drop as $table) {
    $full_table_name = db_prefix() . $table;
    if ($CI->db->table_exists($full_table_name)) {
        $CI->db->query('DROP TABLE IF EXISTS `' . $full_table_name . '`');
    }
}

// ==============================================
// REMOVE MODULE OPTIONS
// ==============================================

$options_to_remove = [
    'disowebs_ops_auto_create_phases',
    'disowebs_ops_auto_create_milestones',
    'disowebs_ops_default_phases',
    'disowebs_ops_active_project_limit',
    'disowebs_ops_block_project_closure',
    'disowebs_ops_require_lead_qualification',
    'disowebs_ops_required_lead_fields',
    'disowebs_ops_auto_testimonial_request',
];

foreach ($options_to_remove as $option) {
    delete_option($option);
}

// ==============================================
// CLEAN UP UPLOADED FILES
// ==============================================

// Remove proof files directory
$proof_files_dir = FCPATH . 'uploads/disowebs_ops/proof_files/';
if (is_dir($proof_files_dir)) {
    // Recursively delete directory contents
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($proof_files_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $file) {
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    rmdir($proof_files_dir);
}

// Remove module uploads directory
$module_uploads_dir = FCPATH . 'uploads/disowebs_ops/';
if (is_dir($module_uploads_dir)) {
    @rmdir($module_uploads_dir); // Will only remove if empty
}

// ==============================================
// REMOVE STAFF PERMISSIONS
// ==============================================

// Delete module-specific permissions from staff_permissions table
$CI->db->where('feature', DISOWEBS_OPS_MODULE_NAME);
$CI->db->delete(db_prefix() . 'staff_permissions');

log_activity('Disowebs Ops Module Uninstall Complete - All tables and data removed');
