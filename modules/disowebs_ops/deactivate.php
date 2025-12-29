<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Disowebs Ops Module Deactivation Script
 * 
 * This script runs when the module is deactivated.
 * It unregisters menus, tabs, and widgets but preserves data.
 */

$CI = &get_instance();

// Log deactivation
log_activity('Disowebs Ops Module Deactivated');

// Note: Menu items, tabs, and widgets are registered via hooks that only run
// when the module is active, so they are automatically unregistered when the
// module is deactivated. No explicit cleanup needed for:
// - Sidebar menu items (registered in disowebs_ops_init_menu)
// - Project tabs (registered via filters)
// - Dashboard widgets (registered via hooks)
// - Settings options (preserved for potential reactivation)

// Clear any cached data related to the module
if (function_exists('clear_staff_sessions')) {
    // Force staff to re-login to refresh menu
}

// Optionally disable scheduled tasks
// Note: Cron jobs will simply not execute if module is inactive
