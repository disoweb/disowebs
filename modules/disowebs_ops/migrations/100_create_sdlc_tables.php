<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_sdlc_tables extends App_module_migration
{
    public function up()
    {
        $prefix = db_prefix();

        // =====================================================
        // PROJECT REQUIREMENTS
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_project_requirements')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_project_requirements` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `project_id` INT(11) UNSIGNED NOT NULL,
                `category` VARCHAR(100) NOT NULL DEFAULT 'functional',
                `priority` ENUM('critical','high','medium','low') NOT NULL DEFAULT 'medium',
                `title` VARCHAR(255) NOT NULL,
                `description` TEXT,
                `acceptance_criteria` TEXT,
                `source` VARCHAR(100) DEFAULT 'client',
                `status` ENUM('draft','approved','in_progress','implemented','deferred','rejected') DEFAULT 'draft',
                `estimated_hours` DECIMAL(10,2) DEFAULT NULL,
                `actual_hours` DECIMAL(10,2) DEFAULT NULL,
                `assigned_to` INT(11) UNSIGNED DEFAULT NULL,
                `client_approved` TINYINT(1) DEFAULT 0,
                `client_approved_date` DATETIME DEFAULT NULL,
                `notes` TEXT,
                `sort_order` INT(11) DEFAULT 0,
                `created_by` INT(11) UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `project_id` (`project_id`),
                KEY `status` (`status`),
                KEY `priority` (`priority`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // SCOPE DOCUMENTS
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_scope_documents')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_scope_documents` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `project_id` INT(11) UNSIGNED NOT NULL,
                `version` VARCHAR(20) NOT NULL DEFAULT '1.0',
                `title` VARCHAR(255) NOT NULL,
                `overview` TEXT,
                `objectives` TEXT,
                `deliverables` TEXT,
                `out_of_scope` TEXT,
                `assumptions` TEXT,
                `constraints` TEXT,
                `acceptance_criteria` TEXT,
                `timeline_summary` TEXT,
                `budget_summary` TEXT,
                `risks` TEXT,
                `status` ENUM('draft','pending_approval','approved','superseded') DEFAULT 'draft',
                `client_signed` TINYINT(1) DEFAULT 0,
                `client_signed_date` DATETIME DEFAULT NULL,
                `client_signed_by` VARCHAR(255) DEFAULT NULL,
                `internal_approved_by` INT(11) UNSIGNED DEFAULT NULL,
                `internal_approved_date` DATETIME DEFAULT NULL,
                `hash` VARCHAR(64) DEFAULT NULL,
                `created_by` INT(11) UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `project_id` (`project_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // SCOPE LINE ITEMS (Deliverables breakdown)
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_scope_items')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_scope_items` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `scope_id` INT(11) UNSIGNED NOT NULL,
                `parent_id` INT(11) UNSIGNED DEFAULT NULL,
                `type` ENUM('phase','deliverable','feature','task') DEFAULT 'deliverable',
                `title` VARCHAR(255) NOT NULL,
                `description` TEXT,
                `estimated_hours` DECIMAL(10,2) DEFAULT NULL,
                `estimated_cost` DECIMAL(15,2) DEFAULT NULL,
                `included` TINYINT(1) DEFAULT 1,
                `sort_order` INT(11) DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `scope_id` (`scope_id`),
                KEY `parent_id` (`parent_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // CHANGE REQUESTS
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_change_requests')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_change_requests` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `project_id` INT(11) UNSIGNED NOT NULL,
                `scope_id` INT(11) UNSIGNED DEFAULT NULL,
                `cr_number` VARCHAR(50) NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `justification` TEXT,
                `impact_scope` TEXT,
                `impact_timeline` TEXT,
                `impact_budget` TEXT,
                `estimated_hours` DECIMAL(10,2) DEFAULT NULL,
                `estimated_cost` DECIMAL(15,2) DEFAULT NULL,
                `priority` ENUM('critical','high','medium','low') DEFAULT 'medium',
                `status` ENUM('draft','submitted','under_review','approved','rejected','implemented','cancelled') DEFAULT 'draft',
                `requested_by` VARCHAR(255) DEFAULT NULL,
                `requested_date` DATETIME DEFAULT NULL,
                `reviewed_by` INT(11) UNSIGNED DEFAULT NULL,
                `reviewed_date` DATETIME DEFAULT NULL,
                `approved_by` INT(11) UNSIGNED DEFAULT NULL,
                `approved_date` DATETIME DEFAULT NULL,
                `client_approved` TINYINT(1) DEFAULT 0,
                `client_approved_date` DATETIME DEFAULT NULL,
                `notes` TEXT,
                `created_by` INT(11) UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `cr_number` (`cr_number`),
                KEY `project_id` (`project_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // PROJECT DOCUMENTATION
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_project_docs')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_project_docs` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `project_id` INT(11) UNSIGNED DEFAULT NULL,
                `title` VARCHAR(255) NOT NULL,
                `doc_type` ENUM('readme','api','setup','changelog','technical','user','other') DEFAULT 'other',
                `content` LONGTEXT NOT NULL,
                `status` ENUM('draft','published','archived') DEFAULT 'draft',
                `version` VARCHAR(20) DEFAULT '1.0',
                `tags` VARCHAR(500) DEFAULT NULL,
                `views` INT(11) DEFAULT 0,
                `created_by` INT(11) UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `project_id` (`project_id`),
                KEY `doc_type` (`doc_type`),
                KEY `status` (`status`),
                FULLTEXT KEY `search` (`title`, `content`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // CODE SNIPPETS LIBRARY
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_code_snippets')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_code_snippets` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `category` VARCHAR(100) NOT NULL,
                `language` VARCHAR(50) NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `description` TEXT,
                `code` LONGTEXT NOT NULL,
                `tags` VARCHAR(500) DEFAULT NULL,
                `usage_count` INT(11) DEFAULT 0,
                `is_public` TINYINT(1) DEFAULT 1,
                `created_by` INT(11) UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `category` (`category`),
                KEY `language` (`language`),
                FULLTEXT KEY `search` (`title`, `description`, `tags`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // DEV TEMPLATES (boilerplates, docs, etc)
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_dev_templates')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_dev_templates` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `type` ENUM('document','code','email','checklist','requirement','scope') NOT NULL,
                `category` VARCHAR(100) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT,
                `content` LONGTEXT NOT NULL,
                `variables` TEXT,
                `is_default` TINYINT(1) DEFAULT 0,
                `usage_count` INT(11) DEFAULT 0,
                `created_by` INT(11) UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `type` (`type`),
                KEY `category` (`category`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // SDLC CHECKLISTS
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_sdlc_checklists')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_sdlc_checklists` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `type` VARCHAR(50) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT,
                `is_template` TINYINT(1) DEFAULT 0,
                `project_id` INT(11) UNSIGNED DEFAULT NULL,
                `milestone_id` INT(11) UNSIGNED DEFAULT NULL,
                `status` ENUM('not_started','in_progress','completed') DEFAULT 'not_started',
                `progress` INT(3) DEFAULT 0,
                `due_date` DATE DEFAULT NULL,
                `completed_date` DATETIME DEFAULT NULL,
                `created_by` INT(11) UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `type` (`type`),
                KEY `project_id` (`project_id`),
                KEY `is_template` (`is_template`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // CHECKLIST ITEMS
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_checklist_items')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_checklist_items` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `checklist_id` INT(11) UNSIGNED NOT NULL,
                `parent_id` INT(11) UNSIGNED DEFAULT NULL,
                `title` VARCHAR(255) NOT NULL,
                `description` TEXT,
                `is_required` TINYINT(1) DEFAULT 1,
                `is_checked` TINYINT(1) DEFAULT 0,
                `checked_by` INT(11) UNSIGNED DEFAULT NULL,
                `checked_at` DATETIME DEFAULT NULL,
                `notes` TEXT,
                `sort_order` INT(11) DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `checklist_id` (`checklist_id`),
                KEY `parent_id` (`parent_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // USER STORIES
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_user_stories')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_user_stories` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `project_id` INT(11) UNSIGNED NOT NULL,
                `requirement_id` INT(11) UNSIGNED DEFAULT NULL,
                `epic` VARCHAR(255) DEFAULT NULL,
                `as_a` VARCHAR(255) NOT NULL,
                `i_want` TEXT NOT NULL,
                `so_that` TEXT NOT NULL,
                `acceptance_criteria` TEXT,
                `priority` ENUM('critical','high','medium','low') DEFAULT 'medium',
                `story_points` INT(3) DEFAULT NULL,
                `status` ENUM('backlog','ready','in_progress','testing','done') DEFAULT 'backlog',
                `sprint` VARCHAR(100) DEFAULT NULL,
                `assigned_to` INT(11) UNSIGNED DEFAULT NULL,
                `task_id` INT(11) UNSIGNED DEFAULT NULL,
                `created_by` INT(11) UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `project_id` (`project_id`),
                KEY `requirement_id` (`requirement_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // TECHNICAL SPECS
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_technical_specs')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_technical_specs` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `project_id` INT(11) UNSIGNED NOT NULL,
                `type` ENUM('architecture','api','database','integration','security','performance') NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `content` LONGTEXT NOT NULL,
                `version` VARCHAR(20) DEFAULT '1.0',
                `status` ENUM('draft','review','approved','deprecated') DEFAULT 'draft',
                `reviewed_by` INT(11) UNSIGNED DEFAULT NULL,
                `reviewed_at` DATETIME DEFAULT NULL,
                `created_by` INT(11) UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `project_id` (`project_id`),
                KEY `type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // DEV NOTES / KNOWLEDGE BASE
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_dev_notes')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_dev_notes` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `project_id` INT(11) UNSIGNED DEFAULT NULL,
                `category` VARCHAR(100) DEFAULT 'general',
                `title` VARCHAR(255) NOT NULL,
                `content` LONGTEXT NOT NULL,
                `tags` VARCHAR(500) DEFAULT NULL,
                `is_pinned` TINYINT(1) DEFAULT 0,
                `views` INT(11) DEFAULT 0,
                `created_by` INT(11) UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `project_id` (`project_id`),
                KEY `category` (`category`),
                FULLTEXT KEY `search` (`title`, `content`, `tags`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // =====================================================
        // TIME ESTIMATES
        // =====================================================
        if (!$this->db->table_exists($prefix . 'dw_time_estimates')) {
            $this->db->query("CREATE TABLE `{$prefix}dw_time_estimates` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `project_id` INT(11) UNSIGNED DEFAULT NULL,
                `requirement_id` INT(11) UNSIGNED DEFAULT NULL,
                `scope_item_id` INT(11) UNSIGNED DEFAULT NULL,
                `task_id` INT(11) UNSIGNED DEFAULT NULL,
                `description` VARCHAR(255) NOT NULL,
                `optimistic_hours` DECIMAL(10,2) NOT NULL,
                `likely_hours` DECIMAL(10,2) NOT NULL,
                `pessimistic_hours` DECIMAL(10,2) NOT NULL,
                `calculated_hours` DECIMAL(10,2) GENERATED ALWAYS AS ((`optimistic_hours` + 4 * `likely_hours` + `pessimistic_hours`) / 6) STORED,
                `buffer_percent` INT(3) DEFAULT 20,
                `final_estimate` DECIMAL(10,2) DEFAULT NULL,
                `actual_hours` DECIMAL(10,2) DEFAULT NULL,
                `variance` DECIMAL(10,2) GENERATED ALWAYS AS (`actual_hours` - `final_estimate`) STORED,
                `notes` TEXT,
                `created_by` INT(11) UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                KEY `project_id` (`project_id`),
                KEY `task_id` (`task_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // Insert default checklist templates
        $this->seed_default_checklists();
        $this->seed_default_templates();
        $this->seed_default_snippets();
    }

    public function down()
    {
        $prefix = db_prefix();
        $tables = [
            'dw_project_requirements',
            'dw_scope_documents',
            'dw_scope_items',
            'dw_change_requests',
            'dw_project_docs',
            'dw_code_snippets',
            'dw_dev_templates',
            'dw_sdlc_checklists',
            'dw_checklist_items',
            'dw_user_stories',
            'dw_technical_specs',
            'dw_dev_notes',
            'dw_time_estimates',
        ];

        foreach ($tables as $table) {
            if ($this->db->table_exists($prefix . $table)) {
                $this->db->query("DROP TABLE `{$prefix}{$table}`");
            }
        }
    }

    private function seed_default_checklists()
    {
        $prefix = db_prefix();
        $now = date('Y-m-d H:i:s');

        // Project Kickoff Checklist
        $this->db->insert($prefix . 'dw_sdlc_checklists', [
            'type' => 'kickoff',
            'name' => 'Project Kickoff Checklist',
            'description' => 'Essential items to complete before starting development',
            'is_template' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);
        $kickoff_id = $this->db->insert_id();

        $kickoff_items = [
            'Client contract signed',
            'Deposit invoice paid',
            'Project created in CRM',
            'Client added to project',
            'Requirements gathered',
            'Scope document approved',
            'Timeline agreed',
            'Communication channels set up',
            'Git repository created',
            'Development environment ready',
            'Staging environment ready',
            'Access credentials documented',
        ];

        foreach ($kickoff_items as $i => $item) {
            $this->db->insert($prefix . 'dw_checklist_items', [
                'checklist_id' => $kickoff_id,
                'title' => $item,
                'is_required' => 1,
                'sort_order' => $i,
            ]);
        }

        // Pre-Deployment Checklist
        $this->db->insert($prefix . 'dw_sdlc_checklists', [
            'type' => 'deployment',
            'name' => 'Pre-Deployment Checklist',
            'description' => 'Items to verify before deploying to production',
            'is_template' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);
        $deploy_id = $this->db->insert_id();

        $deploy_items = [
            'All tasks marked complete',
            'Code reviewed',
            'Unit tests passing',
            'Integration tests passing',
            'UAT completed',
            'Client sign-off received',
            'Database migrations tested',
            'Backup created',
            'Rollback plan documented',
            'SSL certificate verified',
            'DNS configured correctly',
            'Performance tested',
            'Security scan completed',
            'Documentation updated',
        ];

        foreach ($deploy_items as $i => $item) {
            $this->db->insert($prefix . 'dw_checklist_items', [
                'checklist_id' => $deploy_id,
                'title' => $item,
                'is_required' => 1,
                'sort_order' => $i,
            ]);
        }

        // QA Testing Checklist
        $this->db->insert($prefix . 'dw_sdlc_checklists', [
            'type' => 'qa',
            'name' => 'QA Testing Checklist',
            'description' => 'Standard QA testing items',
            'is_template' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);
        $qa_id = $this->db->insert_id();

        $qa_items = [
            'Functional testing complete',
            'Cross-browser testing (Chrome, Firefox, Safari, Edge)',
            'Mobile responsive testing',
            'Form validation testing',
            'Error handling verified',
            'Loading states tested',
            'Empty states tested',
            'Permissions/access control tested',
            'Email notifications tested',
            'Performance acceptable',
            'Accessibility basics checked',
            'Content/copy reviewed',
        ];

        foreach ($qa_items as $i => $item) {
            $this->db->insert($prefix . 'dw_checklist_items', [
                'checklist_id' => $qa_id,
                'title' => $item,
                'is_required' => 1,
                'sort_order' => $i,
            ]);
        }

        // Code Review Checklist
        $this->db->insert($prefix . 'dw_sdlc_checklists', [
            'type' => 'code_review',
            'name' => 'Code Review Checklist',
            'description' => 'Standards for code review',
            'is_template' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);
        $cr_id = $this->db->insert_id();

        $cr_items = [
            'Code follows project conventions',
            'No hardcoded values',
            'Proper error handling',
            'SQL injection prevention',
            'XSS prevention',
            'CSRF protection',
            'Input validation',
            'Proper logging',
            'No debug code left',
            'Comments for complex logic',
            'DRY principle followed',
            'Functions are single-purpose',
        ];

        foreach ($cr_items as $i => $item) {
            $this->db->insert($prefix . 'dw_checklist_items', [
                'checklist_id' => $cr_id,
                'title' => $item,
                'is_required' => 1,
                'sort_order' => $i,
            ]);
        }

        // Client Handover Checklist
        $this->db->insert($prefix . 'dw_sdlc_checklists', [
            'type' => 'handover',
            'name' => 'Client Handover Checklist',
            'description' => 'Items for project completion and handover',
            'is_template' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);
        $ho_id = $this->db->insert_id();

        $ho_items = [
            'Final invoice sent',
            'Final payment received',
            'Admin credentials delivered',
            'User documentation provided',
            'Training completed',
            'Source code delivered (if applicable)',
            'Hosting details documented',
            'Support agreement signed',
            'Post-launch support period defined',
            'Feedback/testimonial requested',
        ];

        foreach ($ho_items as $i => $item) {
            $this->db->insert($prefix . 'dw_checklist_items', [
                'checklist_id' => $ho_id,
                'title' => $item,
                'is_required' => 1,
                'sort_order' => $i,
            ]);
        }
    }

    private function seed_default_templates()
    {
        $prefix = db_prefix();
        $now = date('Y-m-d H:i:s');

        // Scope Document Template
        $scope_template = '# Project Scope Document

## Project: {project_name}
## Client: {client_name}
## Date: {date}
## Version: {version}

---

## 1. Project Overview
{overview}

## 2. Objectives
- Objective 1
- Objective 2
- Objective 3

## 3. Deliverables

### In Scope
| # | Deliverable | Description | Est. Hours |
|---|-------------|-------------|------------|
| 1 | | | |
| 2 | | | |

### Out of Scope
- Item 1
- Item 2

## 4. Assumptions
- Assumption 1
- Assumption 2

## 5. Constraints
- Constraint 1
- Constraint 2

## 6. Timeline
| Phase | Start | End | Duration |
|-------|-------|-----|----------|
| Discovery | | | |
| Development | | | |
| Testing | | | |
| Deployment | | | |

## 7. Budget Summary
| Item | Hours | Rate | Total |
|------|-------|------|-------|
| Development | | | |
| Testing | | | |
| Project Management | | | |
| **Total** | | | |

## 8. Acceptance Criteria
{acceptance_criteria}

## 9. Sign-off

**Client Approval:**
Name: ____________________
Signature: ____________________
Date: ____________________

**Developer Approval:**
Name: ____________________
Date: ____________________';

        $this->db->insert($prefix . 'dw_dev_templates', [
            'type' => 'scope',
            'category' => 'project',
            'name' => 'Standard Scope Document',
            'description' => 'Professional scope document template with all essential sections',
            'content' => $scope_template,
            'variables' => json_encode(['project_name', 'client_name', 'date', 'version', 'overview', 'acceptance_criteria']),
            'is_default' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);

        // Requirements Intake Template
        $req_template = '# Requirements Intake Form

## Project Information
- **Project Name:** {project_name}
- **Client:** {client_name}
- **Date:** {date}
- **Gathered By:** {gathered_by}

---

## Business Requirements

### 1. What problem are you trying to solve?


### 2. Who are the primary users?


### 3. What are the main goals of this project?


### 4. What does success look like?


---

## Functional Requirements

### Features Needed (Priority: H/M/L)
| Feature | Description | Priority | Notes |
|---------|-------------|----------|-------|
| | | | |

### User Roles
| Role | Permissions | Notes |
|------|-------------|-------|
| | | |

### Integrations Required
- [ ] Payment Gateway: ____________
- [ ] Email Service: ____________
- [ ] CRM: ____________
- [ ] Other: ____________

---

## Technical Requirements

### Platform
- [ ] Web Application
- [ ] Mobile App (iOS/Android)
- [ ] Desktop Application
- [ ] API Only

### Hosting Preference
- [ ] Client provides hosting
- [ ] We provide hosting
- [ ] Cloud (AWS/GCP/Azure)

### Browser Support
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] IE11

---

## Design Requirements

### Branding
- Logo available: [ ] Yes [ ] No
- Brand colors: ____________
- Style guide: [ ] Yes [ ] No

### Design Approach
- [ ] Custom design needed
- [ ] Use existing template
- [ ] Minimal design (functional focus)

---

## Timeline & Budget

### Timeline Expectations
- Ideal launch date: ____________
- Hard deadline: ____________
- Flexible: [ ] Yes [ ] No

### Budget Range
- [ ] Under $5,000
- [ ] $5,000 - $15,000
- [ ] $15,000 - $50,000
- [ ] $50,000+
- [ ] Not defined

---

## Additional Notes
{notes}';

        $this->db->insert($prefix . 'dw_dev_templates', [
            'type' => 'requirement',
            'category' => 'intake',
            'name' => 'Requirements Intake Form',
            'description' => 'Comprehensive requirements gathering template',
            'content' => $req_template,
            'variables' => json_encode(['project_name', 'client_name', 'date', 'gathered_by', 'notes']),
            'is_default' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);

        // User Story Template
        $story_template = '## User Story

**As a** {user_role}
**I want** {feature}
**So that** {benefit}

### Acceptance Criteria
- [ ] Criteria 1
- [ ] Criteria 2
- [ ] Criteria 3

### Technical Notes
{technical_notes}

### UI/UX Notes
{ux_notes}

### Dependencies
- Dependency 1
- Dependency 2

### Estimation
- Story Points: {points}
- Hours: {hours}';

        $this->db->insert($prefix . 'dw_dev_templates', [
            'type' => 'requirement',
            'category' => 'user_story',
            'name' => 'User Story Template',
            'description' => 'Standard user story format with acceptance criteria',
            'content' => $story_template,
            'variables' => json_encode(['user_role', 'feature', 'benefit', 'technical_notes', 'ux_notes', 'points', 'hours']),
            'is_default' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);

        // Change Request Template
        $cr_template = '# Change Request

## CR Number: {cr_number}
## Project: {project_name}
## Date: {date}

---

## Change Description
{description}

## Justification
{justification}

---

## Impact Analysis

### Scope Impact
{scope_impact}

### Timeline Impact
- Original deadline: {original_deadline}
- New deadline: {new_deadline}
- Delay: {delay_days} days

### Budget Impact
- Additional hours: {additional_hours}
- Additional cost: ${additional_cost}

---

## Approval

### Client Approval
- [ ] Approved
- [ ] Rejected
- [ ] Pending

Signature: ____________________
Date: ____________________

### Developer Approval
- [ ] Approved
- [ ] Rejected

Notes: ____________________';

        $this->db->insert($prefix . 'dw_dev_templates', [
            'type' => 'document',
            'category' => 'change_request',
            'name' => 'Change Request Template',
            'description' => 'Formal change request document template',
            'content' => $cr_template,
            'variables' => json_encode(['cr_number', 'project_name', 'date', 'description', 'justification', 'scope_impact', 'original_deadline', 'new_deadline', 'delay_days', 'additional_hours', 'additional_cost']),
            'is_default' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);

        // Technical Spec Template
        $tech_template = '# Technical Specification

## Project: {project_name}
## Feature: {feature_name}
## Version: {version}
## Author: {author}
## Date: {date}

---

## 1. Overview
{overview}

## 2. Architecture

### System Components
```
[Diagram or description]
```

### Data Flow
```
[Diagram or description]
```

## 3. Database Design

### Tables
| Table | Purpose |
|-------|---------|
| | |

### Schema Changes
```sql
-- Add schema SQL here
```

## 4. API Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | /api/... | | Yes/No |
| POST | /api/... | | Yes/No |

## 5. Security Considerations
- Authentication: 
- Authorization:
- Data validation:
- Encryption:

## 6. Performance Considerations
- Expected load:
- Caching strategy:
- Database indexing:

## 7. Testing Strategy
- Unit tests:
- Integration tests:
- E2E tests:

## 8. Deployment Notes
{deployment_notes}

## 9. Rollback Plan
{rollback_plan}';

        $this->db->insert($prefix . 'dw_dev_templates', [
            'type' => 'document',
            'category' => 'technical',
            'name' => 'Technical Specification Template',
            'description' => 'Detailed technical specification document',
            'content' => $tech_template,
            'variables' => json_encode(['project_name', 'feature_name', 'version', 'author', 'date', 'overview', 'deployment_notes', 'rollback_plan']),
            'is_default' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);
    }

    private function seed_default_snippets()
    {
        $prefix = db_prefix();
        $now = date('Y-m-d H:i:s');

        // PHP - CodeIgniter AJAX Handler
        $this->db->insert($prefix . 'dw_code_snippets', [
            'category' => 'perfex',
            'language' => 'php',
            'title' => 'AJAX Request Handler',
            'description' => 'Standard AJAX handler for Perfex CRM with JSON response',
            'code' => '<?php
public function ajax_handler()
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }

    $data = $this->input->post();
    
    // Validate input
    if (empty($data[\'id\'])) {
        echo json_encode([\'success\' => false, \'message\' => \'Invalid request\']);
        return;
    }

    try {
        // Process the request
        $result = $this->model->process($data);
        
        echo json_encode([
            \'success\' => true,
            \'message\' => \'Operation successful\',
            \'data\' => $result
        ]);
    } catch (Exception $e) {
        log_activity(\'Error: \' . $e->getMessage());
        echo json_encode([\'success\' => false, \'message\' => $e->getMessage()]);
    }
}',
            'tags' => 'ajax,json,controller,perfex',
            'is_public' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);

        // PHP - Permission Check
        $this->db->insert($prefix . 'dw_code_snippets', [
            'category' => 'perfex',
            'language' => 'php',
            'title' => 'Permission Check Pattern',
            'description' => 'Standard permission checking for Perfex CRM',
            'code' => '<?php
// Check basic permission
if (!has_permission(\'module_name\', \'\', \'view\')) {
    access_denied(\'module_name\');
}

// Check with ownership
$item = $this->model->get($id);
if (!has_permission(\'module_name\', \'\', \'view\') && $item->created_by != get_staff_user_id()) {
    access_denied(\'module_name\');
}

// Check admin or specific permission
if (!is_admin() && !has_permission(\'module_name\', \'\', \'edit\')) {
    access_denied(\'module_name\');
}

// In view file - show/hide based on permission
<?php if (has_permission(\'module_name\', \'\', \'create\')) { ?>
    <a href="#" class="btn btn-primary">Add New</a>
<?php } ?>',
            'tags' => 'permission,security,access,perfex',
            'is_public' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);

        // JavaScript - DataTable Init
        $this->db->insert($prefix . 'dw_code_snippets', [
            'category' => 'perfex',
            'language' => 'javascript',
            'title' => 'DataTable Initialization',
            'description' => 'Standard DataTable setup for Perfex CRM',
            'code' => '// Initialize DataTable with server-side processing
var table = $(\'#myTable\').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: admin_url + \'module/ajax_table\',
        type: \'POST\'
    },
    columns: [
        { data: \'id\', name: \'id\' },
        { data: \'name\', name: \'name\' },
        { data: \'status\', name: \'status\' },
        { 
            data: \'actions\', 
            name: \'actions\',
            orderable: false,
            searchable: false
        }
    ],
    order: [[0, \'desc\']],
    language: {
        url: app.langURL
    }
});

// Reload table
function reloadTable() {
    table.ajax.reload(null, false);
}

// Row click handler
$(\'#myTable tbody\').on(\'click\', \'tr\', function() {
    var data = table.row(this).data();
    console.log(data);
});',
            'tags' => 'datatable,table,ajax,javascript',
            'is_public' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);

        // PHP - Model CRUD Pattern
        $this->db->insert($prefix . 'dw_code_snippets', [
            'category' => 'perfex',
            'language' => 'php',
            'title' => 'Model CRUD Operations',
            'description' => 'Complete CRUD pattern for Perfex CRM model',
            'code' => '<?php
defined(\'BASEPATH\') or exit(\'No direct script access allowed\');

class My_model extends App_Model
{
    private $table = \'mytable\';

    public function get($id = null)
    {
        if ($id) {
            $this->db->where(\'id\', $id);
            return $this->db->get(db_prefix() . $this->table)->row();
        }
        return $this->db->get(db_prefix() . $this->table)->result();
    }

    public function add($data)
    {
        $data[\'created_at\'] = date(\'Y-m-d H:i:s\');
        $data[\'created_by\'] = get_staff_user_id();
        
        $this->db->insert(db_prefix() . $this->table, $data);
        $insert_id = $this->db->insert_id();
        
        if ($insert_id) {
            log_activity(\'New Item Added [ID: \' . $insert_id . \']\');
            return $insert_id;
        }
        return false;
    }

    public function update($id, $data)
    {
        $data[\'updated_at\'] = date(\'Y-m-d H:i:s\');
        
        $this->db->where(\'id\', $id);
        $this->db->update(db_prefix() . $this->table, $data);
        
        if ($this->db->affected_rows() > 0) {
            log_activity(\'Item Updated [ID: \' . $id . \']\');
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $this->db->where(\'id\', $id);
        $this->db->delete(db_prefix() . $this->table);
        
        if ($this->db->affected_rows() > 0) {
            log_activity(\'Item Deleted [ID: \' . $id . \']\');
            return true;
        }
        return false;
    }
}',
            'tags' => 'model,crud,database,perfex',
            'is_public' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);

        // SQL - Common Queries
        $this->db->insert($prefix . 'dw_code_snippets', [
            'category' => 'database',
            'language' => 'sql',
            'title' => 'Perfex Common Queries',
            'description' => 'Frequently used SQL queries for Perfex CRM',
            'code' => '-- Get all projects with client info
SELECT p.*, c.company as client_name
FROM tblprojects p
LEFT JOIN tblclients c ON c.userid = p.clientid
WHERE p.status != 4
ORDER BY p.id DESC;

-- Get tasks by project with assignees
SELECT t.*, 
       GROUP_CONCAT(CONCAT(s.firstname, \' \', s.lastname)) as assignees
FROM tbltasks t
LEFT JOIN tbltask_assigned ta ON ta.taskid = t.id
LEFT JOIN tblstaff s ON s.staffid = ta.staffid
WHERE t.rel_type = \'project\' AND t.rel_id = ?
GROUP BY t.id;

-- Project progress calculation
SELECT 
    p.id,
    p.name,
    COUNT(CASE WHEN t.status = 5 THEN 1 END) as completed,
    COUNT(t.id) as total,
    ROUND(COUNT(CASE WHEN t.status = 5 THEN 1 END) / COUNT(t.id) * 100, 2) as progress
FROM tblprojects p
LEFT JOIN tbltasks t ON t.rel_type = \'project\' AND t.rel_id = p.id
GROUP BY p.id;

-- Staff performance this month
SELECT 
    s.staffid,
    CONCAT(s.firstname, \' \', s.lastname) as name,
    COUNT(t.id) as tasks_completed,
    SUM(TIME_TO_SEC(TIMEDIFF(t.datefinished, t.startdate))) / 3600 as total_hours
FROM tblstaff s
LEFT JOIN tbltasks t ON t.finished_by = s.staffid 
    AND MONTH(t.datefinished) = MONTH(CURRENT_DATE())
GROUP BY s.staffid;',
            'tags' => 'sql,query,perfex,reporting',
            'is_public' => 1,
            'created_by' => 1,
            'created_at' => $now,
        ]);
    }
}
