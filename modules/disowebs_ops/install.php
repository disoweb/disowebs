<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// ============================================
// SDLC DEVELOPMENT TOOLS TABLES
// ============================================

// Project Requirements
if (!$CI->db->table_exists(db_prefix() . 'dw_project_requirements')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_project_requirements` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) DEFAULT NULL,
        `title` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `priority` varchar(20) NOT NULL DEFAULT 'medium',
        `status` varchar(20) NOT NULL DEFAULT 'pending',
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `status` (`status`),
        KEY `priority` (`priority`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Scope Documents
if (!$CI->db->table_exists(db_prefix() . 'dw_scope_documents')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_scope_documents` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) DEFAULT NULL,
        `title` varchar(255) NOT NULL,
        `version` varchar(20) NOT NULL DEFAULT '1.0',
        `overview` text DEFAULT NULL,
        `objectives` text DEFAULT NULL,
        `deliverables` text DEFAULT NULL,
        `exclusions` text DEFAULT NULL,
        `assumptions` text DEFAULT NULL,
        `constraints` text DEFAULT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'draft',
        `hash` varchar(64) DEFAULT NULL,
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Scope Items
if (!$CI->db->table_exists(db_prefix() . 'dw_scope_items')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_scope_items` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `scope_id` int(11) NOT NULL,
        `item_type` varchar(20) NOT NULL,
        `content` text NOT NULL,
        `position` int(11) NOT NULL DEFAULT '0',
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `scope_id` (`scope_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Code Snippets
if (!$CI->db->table_exists(db_prefix() . 'dw_code_snippets')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_code_snippets` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `language` varchar(50) NOT NULL,
        `code` longtext NOT NULL,
        `description` text DEFAULT NULL,
        `category` varchar(100) DEFAULT NULL,
        `tags` varchar(255) DEFAULT NULL,
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `language` (`language`),
        KEY `category` (`category`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// SDLC Checklists
if (!$CI->db->table_exists(db_prefix() . 'dw_sdlc_checklists')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_sdlc_checklists` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `project_id` int(11) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `is_template` tinyint(1) NOT NULL DEFAULT '0',
        `total_items` int(11) NOT NULL DEFAULT '0',
        `completed_items` int(11) NOT NULL DEFAULT '0',
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `is_template` (`is_template`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Checklist Items
if (!$CI->db->table_exists(db_prefix() . 'dw_checklist_items')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_checklist_items` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `checklist_id` int(11) NOT NULL,
        `description` varchar(500) NOT NULL,
        `is_completed` tinyint(1) NOT NULL DEFAULT '0',
        `completed_by` int(11) DEFAULT NULL,
        `completed_at` datetime DEFAULT NULL,
        `position` int(11) NOT NULL DEFAULT '0',
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `checklist_id` (`checklist_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Dev Notes
if (!$CI->db->table_exists(db_prefix() . 'dw_dev_notes')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_dev_notes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) DEFAULT NULL,
        `content` text NOT NULL,
        `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
        `color` varchar(20) DEFAULT NULL,
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `is_pinned` (`is_pinned`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Dev Templates
if (!$CI->db->table_exists(db_prefix() . 'dw_dev_templates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_dev_templates` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `type` varchar(50) NOT NULL,
        `content` longtext NOT NULL,
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `type` (`type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Project Documents
if (!$CI->db->table_exists(db_prefix() . 'dw_project_docs')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_project_docs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) DEFAULT NULL,
        `title` varchar(255) NOT NULL,
        `doc_type` varchar(50) NOT NULL DEFAULT 'general',
        `content` longtext NOT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'draft',
        `version` varchar(20) NOT NULL DEFAULT '1.0',
        `views` int(11) NOT NULL DEFAULT '0',
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `doc_type` (`doc_type`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Technical Specs
if (!$CI->db->table_exists(db_prefix() . 'dw_technical_specs')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_technical_specs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) DEFAULT NULL,
        `title` varchar(255) NOT NULL,
        `type` varchar(50) NOT NULL DEFAULT 'general',
        `version` varchar(20) NOT NULL DEFAULT '1.0',
        `content` longtext NOT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'draft',
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `type` (`type`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Time Estimates
if (!$CI->db->table_exists(db_prefix() . 'dw_time_estimates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_time_estimates` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) DEFAULT NULL,
        `task_id` int(11) DEFAULT NULL,
        `title` varchar(255) NOT NULL,
        `optimistic` decimal(10,2) NOT NULL DEFAULT '0.00',
        `most_likely` decimal(10,2) NOT NULL DEFAULT '0.00',
        `pessimistic` decimal(10,2) NOT NULL DEFAULT '0.00',
        `estimate` decimal(10,2) NOT NULL DEFAULT '0.00',
        `actual_hours` decimal(10,2) DEFAULT NULL,
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `task_id` (`task_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// User Stories
if (!$CI->db->table_exists(db_prefix() . 'dw_user_stories')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_user_stories` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) DEFAULT NULL,
        `title` varchar(255) NOT NULL,
        `as_a` varchar(255) NOT NULL,
        `i_want` text NOT NULL,
        `so_that` text DEFAULT NULL,
        `acceptance_criteria` text DEFAULT NULL,
        `story_points` int(11) DEFAULT NULL,
        `priority` varchar(20) NOT NULL DEFAULT 'medium',
        `status` varchar(20) NOT NULL DEFAULT 'backlog',
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `status` (`status`),
        KEY `priority` (`priority`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// ============================================
// ORIGINAL TABLES
// ============================================

if (!$CI->db->table_exists(db_prefix() . 'dw_project_phases')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_project_phases` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `name` varchar(255) NOT NULL,
        `position` int(11) NOT NULL DEFAULT '0',
        `status` varchar(20) NOT NULL DEFAULT 'not_started',
        `started_at` datetime DEFAULT NULL,
        `completed_at` datetime DEFAULT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `project_status` (`project_id`, `status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$CI->db->table_exists(db_prefix() . 'dw_project_milestones')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_project_milestones` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `phase_id` int(11) DEFAULT NULL,
        `title` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `week_start` date NOT NULL,
        `week_end` date NOT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'planned',
        `done_at` datetime DEFAULT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `project_status` (`project_id`, `status`),
        KEY `week_range` (`week_start`, `week_end`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$CI->db->table_exists(db_prefix() . 'dw_scope_snapshots')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_scope_snapshots` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `source_type` varchar(20) NOT NULL,
        `source_id` int(11) NOT NULL,
        `snapshot_json` longtext NOT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `source_lookup` (`source_type`, `source_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$CI->db->table_exists(db_prefix() . 'dw_change_requests')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_change_requests` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` longtext NOT NULL,
        `impact_days` int(11) NOT NULL DEFAULT '0',
        `impact_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
        `status` varchar(20) NOT NULL DEFAULT 'draft',
        `approved_by` int(11) DEFAULT NULL,
        `approved_at` datetime DEFAULT NULL,
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `status` (`status`),
        KEY `approved_by` (`approved_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$CI->db->table_exists(db_prefix() . 'dw_proof_entries')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_proof_entries` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `title` varchar(255) NOT NULL,
        `problem` longtext NOT NULL,
        `solution` longtext NOT NULL,
        `outcome` longtext NOT NULL,
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$CI->db->table_exists(db_prefix() . 'dw_proof_files')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_proof_files` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `proof_entry_id` int(11) NOT NULL,
        `file_path` varchar(255) NOT NULL,
        `file_type` varchar(50) NOT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `proof_entry_id` (`proof_entry_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// ============================================
// NEW TABLES FOR WORKFLOW GAPS (V2 Spec)
// ============================================

// Project delivery gates (deposit, final payment, training)
if (!$CI->db->table_exists(db_prefix() . 'dw_project_gates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_project_gates` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `deposit_required` decimal(15,2) NOT NULL DEFAULT '0.00',
        `deposit_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
        `deposit_cleared` tinyint(1) NOT NULL DEFAULT '0',
        `deposit_cleared_at` datetime DEFAULT NULL,
        `deposit_cleared_by` int(11) DEFAULT NULL,
        `final_payment_required` decimal(15,2) NOT NULL DEFAULT '0.00',
        `final_payment_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
        `final_payment_cleared` tinyint(1) NOT NULL DEFAULT '0',
        `final_payment_cleared_at` datetime DEFAULT NULL,
        `final_payment_cleared_by` int(11) DEFAULT NULL,
        `handover_released` tinyint(1) NOT NULL DEFAULT '0',
        `handover_released_at` datetime DEFAULT NULL,
        `handover_released_by` int(11) DEFAULT NULL,
        `training_completed` tinyint(1) NOT NULL DEFAULT '0',
        `training_completed_at` datetime DEFAULT NULL,
        `training_notes` text DEFAULT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `project_id` (`project_id`),
        KEY `deposit_cleared` (`deposit_cleared`),
        KEY `final_payment_cleared` (`final_payment_cleared`),
        KEY `handover_released` (`handover_released`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Milestone blockers (blocker tracking with owner and next action)
if (!$CI->db->table_exists(db_prefix() . 'dw_milestone_blockers')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_milestone_blockers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `milestone_id` int(11) NOT NULL,
        `project_id` int(11) NOT NULL,
        `description` text NOT NULL,
        `owner_staff_id` int(11) DEFAULT NULL,
        `next_action` varchar(255) DEFAULT NULL,
        `next_action_date` date DEFAULT NULL,
        `resolved` tinyint(1) NOT NULL DEFAULT '0',
        `resolved_at` datetime DEFAULT NULL,
        `resolved_by` int(11) DEFAULT NULL,
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `milestone_id` (`milestone_id`),
        KEY `project_id` (`project_id`),
        KEY `resolved` (`resolved`),
        KEY `owner_staff_id` (`owner_staff_id`),
        KEY `next_action_date` (`next_action_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Testimonial tracking (Growth Flywheel)
if (!$CI->db->table_exists(db_prefix() . 'dw_testimonials')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_testimonials` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `client_id` int(11) DEFAULT NULL,
        `contact_id` int(11) DEFAULT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'requested',
        `requested_at` datetime DEFAULT NULL,
        `requested_by` int(11) DEFAULT NULL,
        `received_at` datetime DEFAULT NULL,
        `testimonial_text` text DEFAULT NULL,
        `rating` int(11) DEFAULT NULL,
        `permission_to_publish` tinyint(1) NOT NULL DEFAULT '0',
        `approved_by` int(11) DEFAULT NULL,
        `approved_at` datetime DEFAULT NULL,
        `approval_notes` text DEFAULT NULL,
        `published` tinyint(1) NOT NULL DEFAULT '0',
        `published_at` datetime DEFAULT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `client_id` (`client_id`),
        KEY `status` (`status`),
        KEY `requested_at` (`requested_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Add approval columns to existing testimonials table
if ($CI->db->table_exists(db_prefix() . 'dw_testimonials')) {
    if (!$CI->db->field_exists('approved_by', db_prefix() . 'dw_testimonials')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'dw_testimonials` ADD `approved_by` int(11) DEFAULT NULL AFTER `permission_to_publish`');
    }
    if (!$CI->db->field_exists('approved_at', db_prefix() . 'dw_testimonials')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'dw_testimonials` ADD `approved_at` datetime DEFAULT NULL AFTER `approved_by`');
    }
    if (!$CI->db->field_exists('approval_notes', db_prefix() . 'dw_testimonials')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'dw_testimonials` ADD `approval_notes` text DEFAULT NULL AFTER `approved_at`');
    }
}

// Project profit tracking (Profit Engine)
if (!$CI->db->table_exists(db_prefix() . 'dw_project_profit')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_project_profit` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `expected_revenue` decimal(15,2) NOT NULL DEFAULT '0.00',
        `actual_revenue` decimal(15,2) NOT NULL DEFAULT '0.00',
        `estimated_hours` decimal(10,2) NOT NULL DEFAULT '0.00',
        `actual_hours` decimal(10,2) NOT NULL DEFAULT '0.00',
        `hourly_cost_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
        `expected_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
        `actual_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
        `expected_margin` decimal(15,2) NOT NULL DEFAULT '0.00',
        `actual_margin` decimal(15,2) NOT NULL DEFAULT '0.00',
        `margin_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
        `cr_impact_total` decimal(15,2) NOT NULL DEFAULT '0.00',
        `notes` text DEFAULT NULL,
        `last_calculated_at` datetime DEFAULT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `project_id` (`project_id`),
        KEY `margin_percent` (`margin_percent`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Weekly demo tracking (Delivery Engine)
if (!$CI->db->table_exists(db_prefix() . 'dw_weekly_demos')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_weekly_demos` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `milestone_id` int(11) DEFAULT NULL,
        `week_start` date NOT NULL,
        `week_end` date NOT NULL,
        `demo_scheduled` tinyint(1) NOT NULL DEFAULT '0',
        `demo_date` datetime DEFAULT NULL,
        `demo_completed` tinyint(1) NOT NULL DEFAULT '0',
        `demo_completed_at` datetime DEFAULT NULL,
        `attendees` text DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `feedback` text DEFAULT NULL,
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `milestone_id` (`milestone_id`),
        KEY `week_range` (`week_start`, `week_end`),
        KEY `demo_completed` (`demo_completed`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Retainer offers tracking (Retention Engine)
if (!$CI->db->table_exists(db_prefix() . 'dw_retainer_offers')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_retainer_offers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `client_id` int(11) NOT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'pending',
        `offer_date` date DEFAULT NULL,
        `offer_amount` decimal(15,2) DEFAULT NULL,
        `offer_type` varchar(50) DEFAULT NULL,
        `accepted` tinyint(1) NOT NULL DEFAULT '0',
        `accepted_at` datetime DEFAULT NULL,
        `declined` tinyint(1) NOT NULL DEFAULT '0',
        `declined_at` datetime DEFAULT NULL,
        `decline_reason` text DEFAULT NULL,
        `subscription_id` int(11) DEFAULT NULL,
        `contract_id` int(11) DEFAULT NULL,
        `days_since_launch` int(11) NOT NULL DEFAULT '0',
        `auto_generated` tinyint(1) NOT NULL DEFAULT '0',
        `reminder_sent` tinyint(1) NOT NULL DEFAULT '0',
        `reminder_sent_at` datetime DEFAULT NULL,
        `created_by` int(11) DEFAULT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `client_id` (`client_id`),
        KEY `status` (`status`),
        KEY `offer_date` (`offer_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Margin alerts log (Profit Engine)
if (!$CI->db->table_exists(db_prefix() . 'dw_margin_alerts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_margin_alerts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `change_request_id` int(11) DEFAULT NULL,
        `alert_type` varchar(50) NOT NULL,
        `message` text NOT NULL,
        `severity` varchar(20) NOT NULL DEFAULT 'warning',
        `acknowledged` tinyint(1) NOT NULL DEFAULT '0',
        `acknowledged_by` int(11) DEFAULT NULL,
        `acknowledged_at` datetime DEFAULT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `project_id` (`project_id`),
        KEY `change_request_id` (`change_request_id`),
        KEY `alert_type` (`alert_type`),
        KEY `acknowledged` (`acknowledged`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Adoption baseline tracking (Retention Engine)
if (!$CI->db->table_exists(db_prefix() . 'dw_adoption_baseline')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_adoption_baseline` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_id` int(11) NOT NULL,
        `client_id` int(11) NOT NULL,
        `baseline_date` date NOT NULL,
        `logins_count` int(11) NOT NULL DEFAULT '0',
        `features_used` text DEFAULT NULL,
        `training_sessions` int(11) NOT NULL DEFAULT '0',
        `support_tickets` int(11) NOT NULL DEFAULT '0',
        `adoption_score` int(11) NOT NULL DEFAULT '0',
        `status` varchar(20) NOT NULL DEFAULT 'pending',
        `notes` text DEFAULT NULL,
        `captured_by` int(11) DEFAULT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `project_client` (`project_id`, `client_id`),
        KEY `project_id` (`project_id`),
        KEY `client_id` (`client_id`),
        KEY `adoption_score` (`adoption_score`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Follow-up tracking for proposals/estimates (Sales Engine)
if (!$CI->db->table_exists(db_prefix() . 'dw_followup_log')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_followup_log` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `rel_type` varchar(30) NOT NULL,
        `rel_id` int(11) NOT NULL,
        `followup_day` int(11) NOT NULL,
        `task_id` int(11) DEFAULT NULL,
        `completed` tinyint(1) NOT NULL DEFAULT '0',
        `completed_at` datetime DEFAULT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `followup_unique` (`rel_type`, `rel_id`, `followup_day`),
        KEY `rel_type_id` (`rel_type`, `rel_id`),
        KEY `completed` (`completed`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Referral tracking (Growth Flywheel)
if (!$CI->db->table_exists(db_prefix() . 'dw_referrals')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "dw_referrals` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `referrer_client_id` int(11) NOT NULL,
        `referred_type` varchar(20) NOT NULL DEFAULT 'lead',
        `referred_id` int(11) NOT NULL,
        `project_id` int(11) DEFAULT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'pending',
        `revenue` decimal(15,2) NOT NULL DEFAULT '0.00',
        `reward_amount` decimal(15,2) DEFAULT NULL,
        `reward_type` varchar(50) DEFAULT NULL,
        `reward_paid_at` datetime DEFAULT NULL,
        `converted_at` datetime DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `referrer_client_id` (`referrer_client_id`),
        KEY `referred_type_id` (`referred_type`, `referred_id`),
        KEY `status` (`status`),
        KEY `project_id` (`project_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}
