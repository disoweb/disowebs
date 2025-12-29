<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple hardcoded connection (update if needed)
$host = 'localhost';
$user = 'u230557394_dwperfex';
$pass = 'Disowebs@8989';
$db = 'u230557394_dwperfex';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$prefix = "tbl";
$tables_created = [];
$errors = [];

// dw_code_snippets
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_code_snippets (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    category VARCHAR(100) NOT NULL DEFAULT 'general',
    language VARCHAR(50) NOT NULL DEFAULT 'php',
    title VARCHAR(255) NOT NULL,
    description TEXT,
    code LONGTEXT NOT NULL,
    tags VARCHAR(500) DEFAULT NULL,
    usage_count INT(11) DEFAULT 0,
    is_public TINYINT(1) DEFAULT 1,
    created_by INT(11) UNSIGNED DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_code_snippets";
else $errors[] = "dw_code_snippets: " . $conn->error;

// dw_dev_templates
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_dev_templates (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    category VARCHAR(100) NOT NULL DEFAULT 'general',
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content LONGTEXT NOT NULL,
    variables TEXT,
    usage_count INT(11) DEFAULT 0,
    created_by INT(11) UNSIGNED DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_dev_templates";
else $errors[] = "dw_dev_templates: " . $conn->error;

// dw_sdlc_checklists
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_sdlc_checklists (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT(11) UNSIGNED DEFAULT NULL,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100) DEFAULT 'general',
    description TEXT,
    is_template TINYINT(1) DEFAULT 0,
    status VARCHAR(50) DEFAULT 'active',
    created_by INT(11) UNSIGNED DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_sdlc_checklists";
else $errors[] = "dw_sdlc_checklists: " . $conn->error;

// dw_checklist_items
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_checklist_items (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    checklist_id INT(11) UNSIGNED NOT NULL,
    parent_id INT(11) UNSIGNED DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_checked TINYINT(1) DEFAULT 0,
    checked_by INT(11) UNSIGNED DEFAULT NULL,
    checked_at DATETIME DEFAULT NULL,
    sort_order INT(11) DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_checklist_items";
else $errors[] = "dw_checklist_items: " . $conn->error;

// dw_project_requirements
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_project_requirements (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT(11) UNSIGNED DEFAULT NULL,
    category VARCHAR(100) DEFAULT 'functional',
    priority VARCHAR(50) DEFAULT 'medium',
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    requirement_type VARCHAR(100) DEFAULT 'functional',
    assigned_to INT(11) UNSIGNED DEFAULT NULL,
    created_by INT(11) UNSIGNED DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_project_requirements";
else $errors[] = "dw_project_requirements: " . $conn->error;

// dw_scope_documents
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_scope_documents (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT(11) UNSIGNED DEFAULT NULL,
    version VARCHAR(20) DEFAULT '1.0',
    title VARCHAR(255) NOT NULL,
    overview TEXT,
    status VARCHAR(50) DEFAULT 'draft',
    internal_approved_by INT(11) UNSIGNED DEFAULT NULL,
    created_by INT(11) UNSIGNED DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_scope_documents";
else $errors[] = "dw_scope_documents: " . $conn->error;

// dw_scope_items
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_scope_items (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    scope_id INT(11) UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    sort_order INT(11) DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_scope_items";
else $errors[] = "dw_scope_items: " . $conn->error;

// dw_change_requests
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_change_requests (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT(11) UNSIGNED DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(50) DEFAULT 'draft',
    priority VARCHAR(50) DEFAULT 'medium',
    created_by INT(11) UNSIGNED DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_change_requests";
else $errors[] = "dw_change_requests: " . $conn->error;

// dw_dev_notes
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_dev_notes (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT(11) UNSIGNED DEFAULT NULL,
    title VARCHAR(255) DEFAULT NULL,
    content TEXT NOT NULL,
    category VARCHAR(100) DEFAULT 'general',
    tags VARCHAR(500) DEFAULT NULL,
    is_pinned TINYINT(1) DEFAULT 0,
    created_by INT(11) UNSIGNED DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_dev_notes";
else $errors[] = "dw_dev_notes: " . $conn->error;

// dw_time_estimates
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_time_estimates (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT(11) UNSIGNED DEFAULT NULL,
    task_name VARCHAR(255) NOT NULL,
    optimistic_hours DECIMAL(10,2) DEFAULT 0,
    likely_hours DECIMAL(10,2) DEFAULT 0,
    pessimistic_hours DECIMAL(10,2) DEFAULT 0,
    estimated_hours DECIMAL(10,2) DEFAULT 0,
    actual_hours DECIMAL(10,2) DEFAULT NULL,
    created_by INT(11) UNSIGNED DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_time_estimates";
else $errors[] = "dw_time_estimates: " . $conn->error;

// dw_user_stories
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_user_stories (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT(11) UNSIGNED DEFAULT NULL,
    requirement_id INT(11) UNSIGNED DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    as_a VARCHAR(255) DEFAULT NULL,
    i_want VARCHAR(500) DEFAULT NULL,
    so_that VARCHAR(500) DEFAULT NULL,
    acceptance_criteria TEXT,
    story_points INT(11) DEFAULT NULL,
    priority VARCHAR(50) DEFAULT 'medium',
    status VARCHAR(50) DEFAULT 'backlog',
    created_by INT(11) UNSIGNED DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_user_stories";
else $errors[] = "dw_user_stories: " . $conn->error;

// dw_technical_specs
$sql = "CREATE TABLE IF NOT EXISTS {$prefix}dw_technical_specs (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT(11) UNSIGNED DEFAULT NULL,
    requirement_id INT(11) UNSIGNED DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    technical_approach TEXT,
    dependencies TEXT,
    risks TEXT,
    status VARCHAR(50) DEFAULT 'draft',
    created_by INT(11) UNSIGNED DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($sql)) $tables_created[] = "dw_technical_specs";
else $errors[] = "dw_technical_specs: " . $conn->error;

$conn->close();

echo "<h2>SDLC Migration Results</h2>";
echo "<p><strong>Tables Created:</strong> " . count($tables_created) . "</p>";
echo "<ul>";
foreach ($tables_created as $t) {
    echo "<li style='color:green'>✓ {$prefix}{$t}</li>";
}
echo "</ul>";

if (!empty($errors)) {
    echo "<p><strong>Errors:</strong></p>";
    echo "<ul>";
    foreach ($errors as $e) {
        echo "<li style='color:red'>✗ {$e}</li>";
    }
    echo "</ul>";
}

echo "<p><strong>Done!</strong> You can delete this file now.</p>";
