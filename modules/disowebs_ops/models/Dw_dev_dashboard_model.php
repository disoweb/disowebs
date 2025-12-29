<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Developer Dashboard Model
 * 
 * Comprehensive data layer for Lead Developer Operations Center.
 * Handles all metrics for build, deploy, maintain workflows.
 * 
 * @package DisowebsOps
 * @author  Disowebs
 * @version 2.0.0
 */
class Dw_dev_dashboard_model extends App_Model
{
    private $projects_table;
    private $tasks_table;
    private $phases_table;
    private $milestones_table;
    private $tickets_table;

    public function __construct()
    {
        parent::__construct();
        $this->projects_table = db_prefix() . 'projects';
        $this->tasks_table = db_prefix() . 'tasks';
        $this->phases_table = db_prefix() . 'dw_project_phases';
        $this->milestones_table = db_prefix() . 'dw_project_milestones';
        $this->tickets_table = db_prefix() . 'tickets';
    }

    // =====================================================
    // SYSTEM HEALTH METRICS
    // =====================================================

    /**
     * Get overall system health metrics
     */
    public function get_system_health()
    {
        $health = [
            'status' => 'healthy',
            'score' => 100,
            'issues' => [],
            'last_check' => date('Y-m-d H:i:s'),
        ];

        // Check database connection
        try {
            $this->db->query('SELECT 1');
            $health['database'] = ['status' => 'ok', 'latency_ms' => 0];
        } catch (Exception $e) {
            $health['database'] = ['status' => 'error', 'message' => $e->getMessage()];
            $health['score'] -= 30;
            $health['issues'][] = 'Database connection issue';
        }

        // Check disk space (uploads folder)
        $uploads_path = FCPATH . 'uploads';
        if (is_dir($uploads_path)) {
            $free_space = @disk_free_space($uploads_path);
            $total_space = @disk_total_space($uploads_path);
            if ($free_space !== false && $total_space !== false) {
                $used_pct = round((1 - ($free_space / $total_space)) * 100);
                $health['disk'] = [
                    'status' => $used_pct > 90 ? 'warning' : 'ok',
                    'used_percent' => $used_pct,
                    'free_gb' => round($free_space / 1073741824, 2),
                    'total_gb' => round($total_space / 1073741824, 2),
                ];
                if ($used_pct > 90) {
                    $health['score'] -= 15;
                    $health['issues'][] = 'Disk space running low (' . $used_pct . '% used)';
                }
            }
        }

        // Check PHP version
        $php_version = phpversion();
        $php_status = version_compare($php_version, '8.0.0', '>=') ? 'ok' : 'warning';
        $health['php'] = [
            'status' => $php_status,
            'version' => $php_version,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ];

        // Check critical tables exist
        $required_tables = [
            'dw_project_phases',
            'dw_project_milestones',
            'dw_change_requests',
            'dw_proof_entries',
            'dw_project_gates',
        ];
        $missing_tables = [];
        foreach ($required_tables as $table) {
            if (!$this->db->table_exists(db_prefix() . $table)) {
                $missing_tables[] = $table;
            }
        }
        if (!empty($missing_tables)) {
            $health['score'] -= 20;
            $health['issues'][] = 'Missing tables: ' . implode(', ', $missing_tables);
        }

        // Set overall status
        if ($health['score'] < 50) {
            $health['status'] = 'critical';
        } elseif ($health['score'] < 80) {
            $health['status'] = 'warning';
        }

        return $health;
    }

    /**
     * Get performance metrics
     */
    public function get_performance_metrics()
    {
        $metrics = [];

        // Database stats
        $this->db->select('COUNT(*) as total', FALSE);
        $this->db->from($this->projects_table);
        $metrics['total_projects'] = (int) $this->db->get()->row()->total;

        $this->db->select('COUNT(*) as total', FALSE);
        $this->db->from($this->tasks_table);
        $metrics['total_tasks'] = (int) $this->db->get()->row()->total;

        $this->db->select('COUNT(*) as total', FALSE);
        $this->db->from($this->tickets_table);
        $metrics['total_tickets'] = (int) $this->db->get()->row()->total;

        // Active sessions count - check if timestamp column exists
        $sessions_table = db_prefix() . 'sessions';
        if ($this->db->field_exists('timestamp', $sessions_table)) {
            $this->db->where('timestamp >=', strtotime('-30 minutes'));
            $metrics['active_sessions'] = (int) $this->db->count_all_results($sessions_table);
        } else {
            // Fallback: just count all sessions
            $metrics['active_sessions'] = (int) $this->db->count_all_results($sessions_table);
        }

        // Memory usage
        $metrics['memory_usage'] = [
            'current' => round(memory_get_usage() / 1048576, 2) . ' MB',
            'peak' => round(memory_get_peak_usage() / 1048576, 2) . ' MB',
        ];

        return $metrics;
    }

    // =====================================================
    // PROJECT PIPELINE METRICS
    // =====================================================

    /**
     * Get complete project pipeline overview
     */
    public function get_project_pipeline()
    {
        $pipeline = [];

        // Projects by status
        $statuses = [
            1 => ['name' => 'Not Started', 'color' => 'default', 'key' => 'not_started'],
            2 => ['name' => 'In Progress', 'color' => 'info', 'key' => 'in_progress'],
            3 => ['name' => 'On Hold', 'color' => 'warning', 'key' => 'on_hold'],
            4 => ['name' => 'Finished', 'color' => 'success', 'key' => 'finished'],
            5 => ['name' => 'Cancelled', 'color' => 'danger', 'key' => 'cancelled'],
        ];

        foreach ($statuses as $status_id => $status_info) {
            $this->db->where('status', $status_id);
            $count = (int) $this->db->count_all_results($this->projects_table);
            $pipeline[$status_info['key']] = [
                'count' => $count,
                'name' => $status_info['name'],
                'color' => $status_info['color'],
            ];
        }

        // Projects starting this week
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $week_end = date('Y-m-d', strtotime('sunday this week'));
        $this->db->where('start_date >=', $week_start);
        $this->db->where('start_date <=', $week_end);
        $pipeline['starting_this_week'] = (int) $this->db->count_all_results($this->projects_table);

        // Projects due this week
        $this->db->where('deadline >=', $week_start);
        $this->db->where('deadline <=', $week_end);
        $pipeline['due_this_week'] = (int) $this->db->count_all_results($this->projects_table);

        // Overdue projects
        $this->db->where('deadline <', date('Y-m-d'));
        $this->db->where('status', 2);
        $pipeline['overdue'] = (int) $this->db->count_all_results($this->projects_table);

        return $pipeline;
    }

    /**
     * Get active projects with phase details
     */
    public function get_active_projects_detailed($limit = 10)
    {
        $this->db->select([
            'p.id',
            'p.name',
            'p.start_date',
            'p.deadline',
            'p.status',
            'p.progress',
            'c.company as client_name',
        ]);
        $this->db->from($this->projects_table . ' as p');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = p.clientid', 'left');
        $this->db->where('p.status', 2);
        $this->db->order_by('p.deadline', 'ASC');
        $this->db->limit($limit);
        $projects = $this->db->get()->result();

        foreach ($projects as &$project) {
            // Get current phase
            $this->db->select('name, status');
            $this->db->from($this->phases_table);
            $this->db->where('project_id', $project->id);
            $this->db->where('status', 'in_progress');
            $this->db->limit(1);
            $phase = $this->db->get()->row();
            $project->current_phase = $phase ? $phase->name : 'N/A';

            // Get next milestone (use week_end as due date, status != 'done' for pending)
            $this->db->select('title, week_end, status');
            $this->db->from($this->milestones_table);
            $this->db->where('project_id', $project->id);
            $this->db->where('status !=', 'done');
            $this->db->order_by('week_end', 'ASC');
            $this->db->limit(1);
            $milestone = $this->db->get()->row();
            $project->next_milestone = $milestone ? $milestone->title : 'N/A';
            $project->milestone_due = $milestone ? $milestone->week_end : null;

            // Calculate days until deadline
            if ($project->deadline) {
                $deadline = new DateTime($project->deadline);
                $today = new DateTime();
                $diff = $today->diff($deadline);
                $project->days_remaining = $diff->invert ? -$diff->days : $diff->days;
            } else {
                $project->days_remaining = null;
            }

            // Get blockers count
            $this->db->where('project_id', $project->id);
            $this->db->where('resolved_at IS NULL');
            $project->blockers_count = (int) $this->db->count_all_results(db_prefix() . 'dw_milestone_blockers');
        }

        return $projects;
    }

    // =====================================================
    // TASK & DEVELOPMENT METRICS
    // =====================================================

    /**
     * Get development task metrics
     */
    public function get_dev_task_metrics()
    {
        $metrics = [];

        // Tasks by status
        $task_statuses = [
            1 => 'Not Started',
            2 => 'Awaiting Feedback',
            3 => 'Testing',
            4 => 'In Progress',
            5 => 'Complete',
        ];

        foreach ($task_statuses as $status_id => $status_name) {
            $this->db->where('status', $status_id);
            $metrics['by_status'][$status_id] = [
                'name' => $status_name,
                'count' => (int) $this->db->count_all_results($this->tasks_table),
            ];
        }

        // My assigned tasks (lead dev)
        $staff_id = get_staff_user_id();
        $this->db->select('COUNT(DISTINCT t.id) as total', FALSE);
        $this->db->from($this->tasks_table . ' as t');
        $this->db->join(db_prefix() . 'task_assigned as ta', 'ta.taskid = t.id', 'left');
        $this->db->where('ta.staffid', $staff_id);
        $this->db->where('t.status !=', 5);
        $metrics['my_open_tasks'] = (int) $this->db->get()->row()->total;

        // Tasks created this week
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $this->db->where('dateadded >=', $week_start);
        $metrics['created_this_week'] = (int) $this->db->count_all_results($this->tasks_table);

        // Tasks completed this week
        $this->db->where('datefinished >=', $week_start);
        $this->db->where('status', 5);
        $metrics['completed_this_week'] = (int) $this->db->count_all_results($this->tasks_table);

        // Overdue tasks
        $this->db->where('duedate <', date('Y-m-d'));
        $this->db->where('status !=', 5);
        $metrics['overdue'] = (int) $this->db->count_all_results($this->tasks_table);

        // High priority tasks
        $this->db->where('priority', 1); // Urgent
        $this->db->where('status !=', 5);
        $metrics['high_priority'] = (int) $this->db->count_all_results($this->tasks_table);

        return $metrics;
    }

    /**
     * Get my urgent tasks (lead dev assigned)
     */
    public function get_my_urgent_tasks($limit = 10)
    {
        $staff_id = get_staff_user_id();

        $this->db->select([
            't.id',
            't.name',
            't.duedate',
            't.priority',
            't.status',
            't.rel_id as project_id',
            'p.name as project_name',
        ]);
        $this->db->from($this->tasks_table . ' as t');
        $this->db->join(db_prefix() . 'task_assigned as ta', 'ta.taskid = t.id', 'left');
        $this->db->join($this->projects_table . ' as p', 'p.id = t.rel_id AND t.rel_type = "project"', 'left');
        $this->db->where('ta.staffid', $staff_id);
        $this->db->where('t.status !=', 5);
        $this->db->group_start();
        $this->db->where('t.priority', 1);
        $this->db->or_where('t.duedate <', date('Y-m-d'));
        $this->db->group_end();
        $this->db->order_by('t.priority', 'ASC');
        $this->db->order_by('t.duedate', 'ASC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    // =====================================================
    // DEPLOYMENT & RELEASE METRICS
    // =====================================================

    /**
     * Get deployment pipeline status
     */
    public function get_deployment_stats()
    {
        $stats = [];

        // Projects in QA phase
        $this->db->select('COUNT(*) as total', FALSE);
        $this->db->from($this->phases_table);
        $this->db->where('status', 'in_progress');
        $this->db->group_start();
        $this->db->like('LOWER(name)', 'qa', 'both');
        $this->db->or_like('LOWER(name)', 'test', 'both');
        $this->db->or_like('LOWER(name)', 'quality', 'both');
        $this->db->group_end();
        $stats['in_qa'] = (int) $this->db->get()->row()->total;

        // Projects in Deploy phase
        $this->db->select('COUNT(*) as total', FALSE);
        $this->db->from($this->phases_table);
        $this->db->where('status', 'in_progress');
        $this->db->group_start();
        $this->db->like('LOWER(name)', 'deploy', 'both');
        $this->db->or_like('LOWER(name)', 'launch', 'both');
        $this->db->or_like('LOWER(name)', 'release', 'both');
        $this->db->group_end();
        $stats['in_deploy'] = (int) $this->db->get()->row()->total;

        // Projects pending handover
        $this->db->select('COUNT(*) as total', FALSE);
        $this->db->from($this->phases_table);
        $this->db->where('status', 'in_progress');
        $this->db->group_start();
        $this->db->like('LOWER(name)', 'handover', 'both');
        $this->db->or_like('LOWER(name)', 'training', 'both');
        $this->db->or_like('LOWER(name)', 'handoff', 'both');
        $this->db->group_end();
        $stats['pending_handover'] = (int) $this->db->get()->row()->total;

        // Recently launched (last 30 days)
        $this->db->where('status', 4); // Finished
        $this->db->where('date_finished >=', date('Y-m-d', strtotime('-30 days')));
        $stats['launched_30_days'] = (int) $this->db->count_all_results($this->projects_table);

        return $stats;
    }

    /**
     * Get projects ready for deployment
     */
    public function get_projects_ready_for_deploy($limit = 5)
    {
        $this->db->select([
            'p.id',
            'p.name',
            'p.deadline',
            'c.company as client_name',
            'ph.name as current_phase',
        ]);
        $this->db->from($this->projects_table . ' as p');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = p.clientid', 'left');
        $this->db->join($this->phases_table . ' as ph', 'ph.project_id = p.id AND ph.status = "in_progress"', 'left');
        $this->db->where('p.status', 2);
        $this->db->group_start();
        $this->db->like('LOWER(ph.name)', 'qa', 'both');
        $this->db->or_like('LOWER(ph.name)', 'deploy', 'both');
        $this->db->or_like('LOWER(ph.name)', 'test', 'both');
        $this->db->group_end();
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    // =====================================================
    // SUPPORT & MAINTENANCE METRICS
    // =====================================================

    /**
     * Get support ticket metrics for dev attention
     */
    public function get_support_metrics()
    {
        $metrics = [];

        // Open tickets
        $this->db->where_in('status', [1, 2, 3, 4]); // Open statuses
        $metrics['open_total'] = (int) $this->db->count_all_results($this->tickets_table);

        // High priority/urgent
        $this->db->where_in('status', [1, 2, 3, 4]);
        $this->db->where_in('priority', [1, 2]); // Urgent, High
        $metrics['high_priority'] = (int) $this->db->count_all_results($this->tickets_table);

        // Bug reports (by subject/department)
        $this->db->where_in('status', [1, 2, 3, 4]);
        $this->db->group_start();
        $this->db->like('subject', 'bug', 'both');
        $this->db->or_like('subject', 'error', 'both');
        $this->db->or_like('subject', 'issue', 'both');
        $this->db->or_like('subject', 'broken', 'both');
        $this->db->group_end();
        $metrics['bug_reports'] = (int) $this->db->count_all_results($this->tickets_table);

        // Tickets older than 48 hours
        $this->db->where_in('status', [1, 2, 3, 4]);
        $this->db->where('date <', date('Y-m-d H:i:s', strtotime('-48 hours')));
        $metrics['older_48h'] = (int) $this->db->count_all_results($this->tickets_table);

        // Tickets needing dev attention (escalated or technical)
        $this->db->where_in('status', [1, 2, 3, 4]);
        $this->db->where_in('priority', [1, 2]);
        $this->db->group_start();
        $this->db->like('subject', 'technical', 'both');
        $this->db->or_like('subject', 'code', 'both');
        $this->db->or_like('subject', 'server', 'both');
        $this->db->or_like('subject', 'database', 'both');
        $this->db->or_like('message', 'error', 'both');
        $this->db->group_end();
        $metrics['needs_dev_attention'] = (int) $this->db->count_all_results($this->tickets_table);

        return $metrics;
    }

    /**
     * Get tickets that need developer attention
     */
    public function get_tickets_needing_dev($limit = 10)
    {
        $this->db->select([
            't.ticketid',
            't.subject',
            't.date',
            't.priority',
            't.status',
            'c.company as client_name',
            'd.name as department',
        ]);
        $this->db->from($this->tickets_table . ' as t');
        $this->db->join(db_prefix() . 'clients as c', 'c.userid = t.userid', 'left');
        $this->db->join(db_prefix() . 'departments as d', 'd.departmentid = t.department', 'left');
        $this->db->where_in('t.status', [1, 2, 3, 4]);
        $this->db->group_start();
        $this->db->where_in('t.priority', [1, 2]);
        $this->db->or_like('t.subject', 'bug', 'both');
        $this->db->or_like('t.subject', 'error', 'both');
        $this->db->or_like('t.subject', 'technical', 'both');
        $this->db->group_end();
        $this->db->order_by('t.priority', 'ASC');
        $this->db->order_by('t.date', 'ASC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    // =====================================================
    // CODE QUALITY & TECHNICAL DEBT
    // =====================================================

    /**
     * Get change request metrics (technical debt indicator)
     */
    public function get_change_request_metrics()
    {
        $metrics = [];
        $cr_table = db_prefix() . 'dw_change_requests';

        if (!$this->db->table_exists($cr_table)) {
            return $metrics;
        }

        // Total pending CRs
        $this->db->where_in('status', ['draft', 'submitted', 'approved']);
        $metrics['pending_total'] = (int) $this->db->count_all_results($cr_table);

        // Approved but not implemented
        $this->db->where('status', 'approved');
        $metrics['approved_pending'] = (int) $this->db->count_all_results($cr_table);

        // Total impact days (technical debt hours)
        $this->db->select('SUM(impact_days) as total_days', FALSE);
        $this->db->from($cr_table);
        $this->db->where_in('status', ['draft', 'submitted', 'approved']);
        $result = $this->db->get()->row();
        $metrics['pending_impact_days'] = $result ? (float) $result->total_days : 0;

        // CRs this month
        $this->db->where('created_at >=', date('Y-m-01'));
        $metrics['created_this_month'] = (int) $this->db->count_all_results($cr_table);

        // Average implementation time (days from approved to implemented)
        $this->db->select('AVG(DATEDIFF(updated_at, created_at)) as avg_days', FALSE);
        $this->db->from($cr_table);
        $this->db->where('status', 'implemented');
        $this->db->where('created_at >=', date('Y-m-d', strtotime('-90 days')));
        $result = $this->db->get()->row();
        $metrics['avg_implementation_days'] = $result && $result->avg_days ? round($result->avg_days, 1) : 0;

        return $metrics;
    }

    // =====================================================
    // BACKUP & RECOVERY STATUS
    // =====================================================

    /**
     * Get backup status
     */
    public function get_backup_status()
    {
        $status = [
            'last_backup' => null,
            'backup_size' => 0,
            'backup_count' => 0,
            'status' => 'unknown',
        ];

        $backup_path = FCPATH . 'backups';
        if (!is_dir($backup_path)) {
            $status['status'] = 'no_backup_dir';
            return $status;
        }

        $files = glob($backup_path . '/*.{zip,sql,gz}', GLOB_BRACE);
        $status['backup_count'] = count($files);

        if (!empty($files)) {
            // Sort by modification time
            usort($files, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });

            $latest = $files[0];
            $status['last_backup'] = date('Y-m-d H:i:s', filemtime($latest));
            $status['backup_size'] = round(filesize($latest) / 1048576, 2); // MB
            $status['latest_file'] = basename($latest);

            // Check if backup is recent (within 7 days)
            $days_old = floor((time() - filemtime($latest)) / 86400);
            $status['days_since_backup'] = $days_old;
            $status['status'] = $days_old <= 7 ? 'ok' : ($days_old <= 14 ? 'warning' : 'critical');
        } else {
            $status['status'] = 'no_backups';
        }

        return $status;
    }

    // =====================================================
    // ACTIVITY & AUDIT LOG
    // =====================================================

    /**
     * Get recent activity log
     */
    public function get_recent_activity($limit = 20)
    {
        $this->db->select([
            'al.id',
            'al.description',
            'al.date',
            'al.staffid',
            'CONCAT(s.firstname, " ", s.lastname) as staff_name',
        ]);
        $this->db->from(db_prefix() . 'activity_log as al');
        $this->db->join(db_prefix() . 'staff as s', 's.staffid = al.staffid', 'left');
        $this->db->order_by('al.date', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get my recent activity
     */
    public function get_my_activity($staff_id = null, $limit = 10)
    {
        $staff_id = $staff_id ?: get_staff_user_id();

        $this->db->select(['description', 'date']);
        $this->db->from(db_prefix() . 'activity_log');
        $this->db->where('staffid', $staff_id);
        $this->db->order_by('date', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    // =====================================================
    // QUICK ACTIONS DATA
    // =====================================================

    /**
     * Get quick stats for action buttons
     */
    public function get_quick_action_stats()
    {
        return [
            'pending_cr_approvals' => $this->count_pending_cr_approvals(),
            'unassigned_tasks' => $this->count_unassigned_tasks(),
            'stuck_projects' => $this->count_stuck_projects(),
            'gates_blocked' => $this->count_blocked_gates(),
        ];
    }

    private function count_pending_cr_approvals()
    {
        $table = db_prefix() . 'dw_change_requests';
        if (!$this->db->table_exists($table)) {
            return 0;
        }
        $this->db->where('status', 'submitted');
        return (int) $this->db->count_all_results($table);
    }

    private function count_unassigned_tasks()
    {
        $this->db->select('t.id');
        $this->db->from($this->tasks_table . ' as t');
        $this->db->join(db_prefix() . 'task_assigned as ta', 'ta.taskid = t.id', 'left');
        $this->db->where('ta.taskid IS NULL');
        $this->db->where('t.status !=', 5);
        return (int) $this->db->count_all_results();
    }

    private function count_stuck_projects()
    {
        // Simplified: Count active projects with no tasks created in last 7 days
        $seven_days_ago = date('Y-m-d', strtotime('-7 days'));
        
        $sql = "SELECT COUNT(*) as total FROM " . $this->projects_table . " p 
                WHERE p.status = 2 
                AND (
                    NOT EXISTS (SELECT 1 FROM " . db_prefix() . "tasks t WHERE t.rel_type = 'project' AND t.rel_id = p.id)
                    OR NOT EXISTS (SELECT 1 FROM " . db_prefix() . "tasks t WHERE t.rel_type = 'project' AND t.rel_id = p.id AND t.dateadded >= '" . $seven_days_ago . "')
                )";
        
        $result = $this->db->query($sql)->row();
        return $result ? (int) $result->total : 0;
    }

    private function count_blocked_gates()
    {
        $table = db_prefix() . 'dw_project_gates';
        if (!$this->db->table_exists($table)) {
            return 0;
        }
        $this->db->where('deposit_cleared', 0);
        $this->db->or_where('final_payment_cleared', 0);
        return (int) $this->db->count_all_results($table);
    }

    // =====================================================
    // WEEKLY SUMMARY
    // =====================================================

    /**
     * Get weekly development summary
     */
    public function get_weekly_summary()
    {
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $week_end = date('Y-m-d', strtotime('sunday this week'));

        return [
            'tasks_completed' => $this->count_tasks_completed_in_range($week_start, $week_end),
            'tasks_created' => $this->count_tasks_created_in_range($week_start, $week_end),
            'projects_started' => $this->count_projects_started_in_range($week_start, $week_end),
            'projects_finished' => $this->count_projects_finished_in_range($week_start, $week_end),
            'milestones_done' => $this->count_milestones_done_in_range($week_start, $week_end),
            'tickets_resolved' => $this->count_tickets_resolved_in_range($week_start, $week_end),
        ];
    }

    private function count_tasks_completed_in_range($start, $end)
    {
        $this->db->where('datefinished >=', $start);
        $this->db->where('datefinished <=', $end . ' 23:59:59');
        $this->db->where('status', 5);
        return (int) $this->db->count_all_results($this->tasks_table);
    }

    private function count_tasks_created_in_range($start, $end)
    {
        $this->db->where('dateadded >=', $start);
        $this->db->where('dateadded <=', $end . ' 23:59:59');
        return (int) $this->db->count_all_results($this->tasks_table);
    }

    private function count_projects_started_in_range($start, $end)
    {
        $this->db->where('start_date >=', $start);
        $this->db->where('start_date <=', $end);
        return (int) $this->db->count_all_results($this->projects_table);
    }

    private function count_projects_finished_in_range($start, $end)
    {
        $this->db->where('date_finished >=', $start);
        $this->db->where('date_finished <=', $end);
        $this->db->where('status', 4);
        return (int) $this->db->count_all_results($this->projects_table);
    }

    private function count_milestones_done_in_range($start, $end)
    {
        if (!$this->db->table_exists($this->milestones_table)) {
            return 0;
        }
        $this->db->where('done_at >=', $start);
        $this->db->where('done_at <=', $end . ' 23:59:59');
        $this->db->where('status', 'done');
        return (int) $this->db->count_all_results($this->milestones_table);
    }

    private function count_tickets_resolved_in_range($start, $end)
    {
        $this->db->where('date >=', $start);
        $this->db->where('date <=', $end . ' 23:59:59');
        $this->db->where('status', 5); // Closed
        return (int) $this->db->count_all_results($this->tickets_table);
    }
}
