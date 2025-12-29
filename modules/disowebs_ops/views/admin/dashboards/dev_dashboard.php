<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
/* Activity Feed Color Hierarchy */
.activity-feed .feed-item[data-color="success"] { border-left-color: #84c529 !important; }
.activity-feed .feed-item[data-color="success"]:after { border-color: #84c529 !important; background: #84c529 !important; }
.activity-feed .feed-item[data-color="warning"] { border-left-color: #f39c12 !important; }
.activity-feed .feed-item[data-color="warning"]:after { border-color: #f39c12 !important; background: #f39c12 !important; }
.activity-feed .feed-item[data-color="danger"] { border-left-color: #fc2d42 !important; }
.activity-feed .feed-item[data-color="danger"]:after { border-color: #fc2d42 !important; background: #fc2d42 !important; }
.activity-feed .feed-item[data-color="info"] { border-left-color: #03a9f4 !important; }
.activity-feed .feed-item[data-color="info"]:after { border-color: #03a9f4 !important; background: #03a9f4 !important; }
.activity-feed .feed-item[data-color="purple"] { border-left-color: #8e44ad !important; }
.activity-feed .feed-item[data-color="purple"]:after { border-color: #8e44ad !important; background: #8e44ad !important; }
.activity-feed .feed-item .activity-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    margin-right: 5px;
}
.activity-badge.badge-success { background: #e8f5e9; color: #2e7d32; }
.activity-badge.badge-warning { background: #fff8e1; color: #f57c00; }
.activity-badge.badge-danger { background: #ffebee; color: #c62828; }
.activity-badge.badge-info { background: #e3f2fd; color: #1565c0; }
.activity-badge.badge-purple { background: #f3e5f5; color: #7b1fa2; }
</style>
<?php
// Extract data with defaults
$system_health = isset($system_health) ? $system_health : [];
$performance = isset($performance) ? $performance : [];
$pipeline = isset($pipeline) ? $pipeline : [];
$active_projects = isset($active_projects) ? $active_projects : [];
$task_metrics = isset($task_metrics) ? $task_metrics : [];
$urgent_tasks = isset($urgent_tasks) ? $urgent_tasks : [];
$deployment = isset($deployment) ? $deployment : [];
$deploy_ready = isset($deploy_ready) ? $deploy_ready : [];
$support_metrics = isset($support_metrics) ? $support_metrics : [];
$dev_tickets = isset($dev_tickets) ? $dev_tickets : [];
$cr_metrics = isset($cr_metrics) ? $cr_metrics : [];
$backup_status = isset($backup_status) ? $backup_status : [];
$recent_activity = isset($recent_activity) ? $recent_activity : [];
$quick_actions = isset($quick_actions) ? $quick_actions : [];
$weekly_summary = isset($weekly_summary) ? $weekly_summary : [];

// SDLC data defaults
$active_tab = isset($active_tab) ? $active_tab : 'overview';
$sdlc_stats = isset($sdlc_stats) ? $sdlc_stats : [];
$requirements = isset($requirements) ? $requirements : [];
$requirements_stats = isset($requirements_stats) ? $requirements_stats : [];
$scopes = isset($scopes) ? $scopes : [];
$snippets = isset($snippets) ? $snippets : [];
$snippet_categories = isset($snippet_categories) ? $snippet_categories : [];
$snippet_languages = isset($snippet_languages) ? $snippet_languages : [];
$checklist_templates = isset($checklist_templates) ? $checklist_templates : [];
$active_checklists = isset($active_checklists) ? $active_checklists : [];
$dev_notes = isset($dev_notes) ? $dev_notes : [];
$estimates = isset($estimates) ? $estimates : [];
$projects = isset($projects) ? $projects : [];

// Calculate health score
$health_score = $system_health['overall_score'] ?? 0;
$health_color = $health_score >= 80 ? 'success' : ($health_score >= 60 ? 'warning' : 'danger');
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php $this->load->view('admin/includes/alerts'); ?>
            
            <!-- Dashboard Tabs -->
            <div class="col-md-12 tw-mb-4">
                <div class="horizontal-scrollable-tabs tw-min-h-0 tw-px-3">
                    <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                    <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                    <div class="horizontal-tabs -tw-mx-[calc(theme(spacing.3)-1px)]">
                        <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                            <li role="presentation" class="<?php echo $active_tab == 'overview' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('disowebs_ops/dev_dashboard'); ?>">
                                    <i class="fa fa-tachometer-alt tw-mr-1"></i> <?php echo _l('disowebs_ops_overview'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="<?php echo $active_tab == 'requirements' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('disowebs_ops/dev_dashboard?tab=requirements'); ?>">
                                    <i class="fa fa-clipboard-list tw-mr-1"></i> <?php echo _l('disowebs_ops_requirements'); ?>
                                    <?php if (($sdlc_stats['pending_requirements'] ?? 0) > 0) { ?>
                                    <span class="badge bg-warning"><?php echo $sdlc_stats['pending_requirements']; ?></span>
                                    <?php } ?>
                                </a>
                            </li>
                            <li role="presentation" class="<?php echo $active_tab == 'scope' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('disowebs_ops/dev_dashboard?tab=scope'); ?>">
                                    <i class="fa fa-file-contract tw-mr-1"></i> <?php echo _l('disowebs_ops_scope'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="<?php echo $active_tab == 'snippets' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('disowebs_ops/dev_dashboard?tab=snippets'); ?>">
                                    <i class="fa fa-code tw-mr-1"></i> <?php echo _l('disowebs_ops_code_snippets'); ?>
                                    <span class="badge"><?php echo $sdlc_stats['total_snippets'] ?? 0; ?></span>
                                </a>
                            </li>
                            <li role="presentation" class="<?php echo $active_tab == 'checklists' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('disowebs_ops/dev_dashboard?tab=checklists'); ?>">
                                    <i class="fa fa-tasks tw-mr-1"></i> <?php echo _l('disowebs_ops_checklists'); ?>
                                    <?php if (($sdlc_stats['active_checklists'] ?? 0) > 0) { ?>
                                    <span class="badge bg-info"><?php echo $sdlc_stats['active_checklists']; ?></span>
                                    <?php } ?>
                                </a>
                            </li>
                            <li role="presentation" class="<?php echo $active_tab == 'notes' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('disowebs_ops/dev_dashboard?tab=notes'); ?>">
                                    <i class="fa fa-sticky-note tw-mr-1"></i> <?php echo _l('disowebs_ops_dev_notes'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="<?php echo $active_tab == 'estimates' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('disowebs_ops/dev_dashboard?tab=estimates'); ?>">
                                    <i class="fa fa-clock tw-mr-1"></i> <?php echo _l('disowebs_ops_time_estimates'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="<?php echo $active_tab == 'documentation' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('disowebs_ops/dev_dashboard?tab=documentation'); ?>">
                                    <i class="fa fa-book tw-mr-1"></i> <?php echo _l('disowebs_ops_documentation'); ?>
                                    <?php if (($sdlc_stats['docs_pending'] ?? 0) > 0) { ?>
                                    <span class="badge bg-warning"><?php echo $sdlc_stats['docs_pending']; ?></span>
                                    <?php } ?>
                                </a>
                            </li>
                            <li role="presentation" class="<?php echo $active_tab == 'planning' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('disowebs_ops/dev_dashboard?tab=planning'); ?>">
                                    <i class="fa fa-project-diagram tw-mr-1"></i> <?php echo _l('disowebs_ops_project_planning'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="<?php echo $active_tab == 'technical' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('disowebs_ops/dev_dashboard?tab=technical'); ?>">
                                    <i class="fa fa-cogs tw-mr-1"></i> <?php echo _l('disowebs_ops_technical_specs'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <?php if ($active_tab == 'overview') { ?>
            <!-- ========== OVERVIEW TAB ========== -->

            <!-- Top Stats Row - Quick Stats Style -->
            <div class="col-md-12">
                <div class="widget relative">
                    <div class="tw-grid tw-grid-cols-2 sm:tw-flex sm:tw-flex-row tw-gap-x-4 tw-gap-y-2">
                        <!-- System Health -->
                        <div class="sm:tw-flex-1">
                            <div class="top_stats_wrapper">
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600 tw-hidden sm:tw-block">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="tw-truncate tw-text-sm sm:tw-text-base">
                                            <?php echo _l('disowebs_ops_system_health'); ?>
                                        </span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-<?php echo $health_color; ?> tw-shrink-0 tw-hidden sm:tw-inline">
                                        <?php echo e($health_score); ?>%
                                    </span>
                                </div>
                                <div class="tw-font-semibold tw-text-<?php echo $health_color; ?> tw-text-lg tw-mt-1 sm:tw-hidden">
                                    <?php echo e($health_score); ?>%
                                </div>
                                <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">
                                    <div class="progress-bar progress-bar-<?php echo $health_color; ?> no-percent-text not-dynamic" role="progressbar"
                                        aria-valuenow="<?php echo e($health_score); ?>" aria-valuemin="0"
                                        aria-valuemax="100" style="width: 0%"
                                        data-percent="<?php echo e($health_score); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Projects -->
                        <div class="sm:tw-flex-1">
                            <div class="top_stats_wrapper">
                                <?php 
                                $in_progress = is_array($pipeline['in_progress'] ?? 0) ? ($pipeline['in_progress']['count'] ?? 0) : ($pipeline['in_progress'] ?? 0);
                                $not_started = is_array($pipeline['not_started'] ?? 0) ? ($pipeline['not_started']['count'] ?? 0) : ($pipeline['not_started'] ?? 0);
                                $on_hold = is_array($pipeline['on_hold'] ?? 0) ? ($pipeline['on_hold']['count'] ?? 0) : ($pipeline['on_hold'] ?? 0);
                                $finished = is_array($pipeline['finished'] ?? 0) ? ($pipeline['finished']['count'] ?? 0) : ($pipeline['finished'] ?? 0);
                                $cancelled = is_array($pipeline['cancelled'] ?? 0) ? ($pipeline['cancelled']['count'] ?? 0) : ($pipeline['cancelled'] ?? 0);
                                $total_projects = $not_started + $in_progress + $on_hold + $finished + $cancelled;
                                $percent_active = $total_projects > 0 ? round(($in_progress / $total_projects) * 100) : 0;
                                ?>
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600 tw-hidden sm:tw-block">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                        </svg>
                                        <span class="tw-truncate tw-text-sm sm:tw-text-base">
                                            <?php echo _l('disowebs_ops_projects_active'); ?>
                                        </span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 tw-hidden sm:tw-inline">
                                        <?php echo e($in_progress); ?> / <?php echo e($total_projects); ?>
                                    </span>
                                </div>
                                <div class="tw-font-semibold tw-text-neutral-600 tw-text-lg tw-mt-1 sm:tw-hidden">
                                    <?php echo e($in_progress); ?> / <?php echo e($total_projects); ?>
                                </div>
                                <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">
                                    <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar"
                                        aria-valuenow="<?php echo e($percent_active); ?>" aria-valuemin="0"
                                        aria-valuemax="100" style="width: 0%"
                                        data-percent="<?php echo e($percent_active); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tasks Open -->
                        <div class="sm:tw-flex-1">
                            <div class="top_stats_wrapper">
                                <?php 
                                $open_tasks = $task_metrics['total_open'] ?? 0;
                                $completed_tasks = $task_metrics['completed_this_month'] ?? 0;
                                $total_tasks = $open_tasks + $completed_tasks;
                                $percent_open = $total_tasks > 0 ? round(($open_tasks / $total_tasks) * 100) : 0;
                                ?>
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600 tw-hidden sm:tw-block">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                        </svg>
                                        <span class="tw-truncate tw-text-sm sm:tw-text-base">
                                            <?php echo _l('disowebs_ops_tasks_open'); ?>
                                        </span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 tw-hidden sm:tw-inline">
                                        <?php echo e($open_tasks); ?>
                                    </span>
                                </div>
                                <div class="tw-font-semibold tw-text-neutral-600 tw-text-lg tw-mt-1 sm:tw-hidden">
                                    <?php echo e($open_tasks); ?>
                                </div>
                                <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">
                                    <div class="progress-bar progress-bar-warning no-percent-text not-dynamic" role="progressbar"
                                        aria-valuenow="<?php echo e($percent_open); ?>" aria-valuemin="0"
                                        aria-valuemax="100" style="width: 0%"
                                        data-percent="<?php echo e($percent_open); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Overdue Tasks -->
                        <div class="sm:tw-flex-1">
                            <div class="top_stats_wrapper">
                                <?php 
                                $overdue_tasks = $task_metrics['overdue'] ?? 0;
                                ?>
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-danger tw-hidden sm:tw-block">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                        </svg>
                                        <span class="tw-truncate tw-text-sm sm:tw-text-base text-danger">
                                            <?php echo _l('disowebs_ops_overdue'); ?>
                                        </span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-danger tw-shrink-0 tw-hidden sm:tw-inline">
                                        <?php echo e($overdue_tasks); ?>
                                    </span>
                                </div>
                                <div class="tw-font-semibold tw-text-danger tw-text-lg tw-mt-1 sm:tw-hidden">
                                    <?php echo e($overdue_tasks); ?>
                                </div>
                                <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">
                                    <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar"
                                        aria-valuenow="<?php echo $overdue_tasks > 0 ? 100 : 0; ?>" aria-valuemin="0"
                                        aria-valuemax="100" style="width: 0%"
                                        data-percent="<?php echo $overdue_tasks > 0 ? 100 : 0; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <!-- Middle Row: 6/6 Split -->
            <div class="col-md-6">
                <!-- Project Pipeline Overview -->
                <div class="widget">
                    <div class="panel_s">
                        <div class="panel-body padding-10">
                            <p class="tw-font-semibold tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse tw-p-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                                <span class="tw-text-neutral-700">
                                    <?php echo _l('disowebs_ops_project_pipeline'); ?>
                                </span>
                            </p>
                            <hr class="-tw-mx-3 tw-mt-3 tw-mb-6">
                            
                            <dl class="tw-grid tw-grid-cols-2 tw-gap-2 tw-mb-0">
                                <a href="<?php echo admin_url('projects?status=1'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 tw-transition">
                                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                                        <dt class="tw-font-normal text-muted"><?php echo _l('project_status_1'); ?></dt>
                                        <dd class="tw-mt-1 tw-font-semibold tw-text-neutral-600"><?php echo e($not_started); ?></dd>
                                    </div>
                                </a>
                                <a href="<?php echo admin_url('projects?status=2'); ?>" class="tw-border tw-border-solid tw-border-info/50 tw-rounded-md tw-bg-info/5 tw-block hover:tw-bg-info/10 tw-transition">
                                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                                        <dt class="tw-font-normal text-info"><?php echo _l('project_status_2'); ?></dt>
                                        <dd class="tw-mt-1 tw-font-semibold tw-text-info"><?php echo e($in_progress); ?></dd>
                                    </div>
                                </a>
                                <a href="<?php echo admin_url('projects?status=3'); ?>" class="tw-border tw-border-solid tw-border-warning/50 tw-rounded-md tw-bg-warning/5 tw-block hover:tw-bg-warning/10 tw-transition">
                                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                                        <dt class="tw-font-normal text-warning"><?php echo _l('project_status_3'); ?></dt>
                                        <dd class="tw-mt-1 tw-font-semibold tw-text-warning"><?php echo e($on_hold); ?></dd>
                                    </div>
                                </a>
                                <a href="<?php echo admin_url('projects?status=4'); ?>" class="tw-border tw-border-solid tw-border-success/50 tw-rounded-md tw-bg-success/5 tw-block hover:tw-bg-success/10 tw-transition">
                                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                                        <dt class="tw-font-normal text-success"><?php echo _l('project_status_4'); ?></dt>
                                        <dd class="tw-mt-1 tw-font-semibold tw-text-success"><?php echo e($finished); ?></dd>
                                    </div>
                                </a>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Weekly Summary -->
                <div class="widget">
                    <div class="panel_s">
                        <div class="panel-body padding-10">
                            <p class="tw-font-semibold tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse tw-p-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                <span class="tw-text-neutral-700">
                                    <?php echo _l('disowebs_ops_weekly_summary'); ?>
                                </span>
                            </p>
                            <hr class="-tw-mx-3 tw-mt-3 tw-mb-6">

                            <div class="tw-grid tw-grid-cols-3 tw-gap-3">
                                <div class="tw-text-center tw-p-3 tw-bg-success/5 tw-rounded-lg tw-border tw-border-success/20">
                                    <div class="tw-text-2xl tw-font-bold tw-text-success"><?php echo e($weekly_summary['tasks_completed'] ?? 0); ?></div>
                                    <div class="tw-text-xs tw-text-neutral-500 tw-mt-1"><?php echo _l('disowebs_ops_tasks_done'); ?></div>
                                </div>
                                <div class="tw-text-center tw-p-3 tw-bg-info/5 tw-rounded-lg tw-border tw-border-info/20">
                                    <div class="tw-text-2xl tw-font-bold tw-text-info"><?php echo e($weekly_summary['tasks_created'] ?? 0); ?></div>
                                    <div class="tw-text-xs tw-text-neutral-500 tw-mt-1"><?php echo _l('disowebs_ops_tasks_created'); ?></div>
                                </div>
                                <div class="tw-text-center tw-p-3 tw-bg-primary/5 tw-rounded-lg tw-border tw-border-primary/20">
                                    <div class="tw-text-2xl tw-font-bold tw-text-primary"><?php echo e($weekly_summary['milestones_done'] ?? 0); ?></div>
                                    <div class="tw-text-xs tw-text-neutral-500 tw-mt-1"><?php echo _l('disowebs_ops_milestones'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Quick Actions - Todo Style -->
                <div class="widget">
                    <div class="panel_s todo-panel">
                        <div class="panel-body padding-10">
                            <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">
                                <p class="tw-font-semibold tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                    </svg>
                                    <span class="tw-text-neutral-700">
                                        <?php echo _l('disowebs_ops_quick_actions'); ?>
                                    </span>
                                </p>
                            </div>
                            <hr class="-tw-mx-3 tw-mt-2 tw-mb-4">

                            <div class="tw-space-y-2">
                                <?php if (($quick_actions['pending_crs'] ?? 0) > 0) { ?>
                                <a href="<?php echo admin_url('disowebs_ops/change_requests?status=pending'); ?>" class="tw-flex tw-items-center tw-justify-between tw-p-3 tw-bg-warning/10 tw-rounded-lg tw-border tw-border-warning/30 hover:tw-bg-warning/20 tw-transition">
                                    <div class="tw-flex tw-items-center tw-gap-3">
                                        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-warning/20 tw-flex tw-items-center tw-justify-center">
                                            <i class="fa fa-code-branch tw-text-warning"></i>
                                        </div>
                                        <span class="tw-font-medium tw-text-neutral-700"><?php echo _l('disowebs_ops_pending_crs'); ?></span>
                                    </div>
                                    <span class="tw-px-2 tw-py-1 tw-bg-warning tw-text-white tw-rounded tw-text-sm tw-font-semibold"><?php echo e($quick_actions['pending_crs']); ?></span>
                                </a>
                                <?php } ?>

                                <?php if (($quick_actions['unassigned_tasks'] ?? 0) > 0) { ?>
                                <a href="<?php echo admin_url('tasks/list_tasks?status=1'); ?>" class="tw-flex tw-items-center tw-justify-between tw-p-3 tw-bg-info/10 tw-rounded-lg tw-border tw-border-info/30 hover:tw-bg-info/20 tw-transition">
                                    <div class="tw-flex tw-items-center tw-gap-3">
                                        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-info/20 tw-flex tw-items-center tw-justify-center">
                                            <i class="fa fa-tasks tw-text-info"></i>
                                        </div>
                                        <span class="tw-font-medium tw-text-neutral-700"><?php echo _l('disowebs_ops_unassigned_tasks'); ?></span>
                                    </div>
                                    <span class="tw-px-2 tw-py-1 tw-bg-info tw-text-white tw-rounded tw-text-sm tw-font-semibold"><?php echo e($quick_actions['unassigned_tasks']); ?></span>
                                </a>
                                <?php } ?>

                                <?php if (($quick_actions['stuck_projects'] ?? 0) > 0) { ?>
                                <a href="<?php echo admin_url('projects'); ?>" class="tw-flex tw-items-center tw-justify-between tw-p-3 tw-bg-danger/10 tw-rounded-lg tw-border tw-border-danger/30 hover:tw-bg-danger/20 tw-transition">
                                    <div class="tw-flex tw-items-center tw-gap-3">
                                        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-danger/20 tw-flex tw-items-center tw-justify-center">
                                            <i class="fa fa-exclamation-circle tw-text-danger"></i>
                                        </div>
                                        <span class="tw-font-medium tw-text-neutral-700"><?php echo _l('disowebs_ops_stuck_projects'); ?></span>
                                    </div>
                                    <span class="tw-px-2 tw-py-1 tw-bg-danger tw-text-white tw-rounded tw-text-sm tw-font-semibold"><?php echo e($quick_actions['stuck_projects']); ?></span>
                                </a>
                                <?php } ?>

                                <?php if (($quick_actions['blocked_gates'] ?? 0) > 0) { ?>
                                <a href="<?php echo admin_url('disowebs_ops/gates'); ?>" class="tw-flex tw-items-center tw-justify-between tw-p-3 tw-bg-purple-500/10 tw-rounded-lg tw-border tw-border-purple-500/30 hover:tw-bg-purple-500/20 tw-transition">
                                    <div class="tw-flex tw-items-center tw-gap-3">
                                        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-purple-500/20 tw-flex tw-items-center tw-justify-center">
                                            <i class="fa fa-lock tw-text-purple-600"></i>
                                        </div>
                                        <span class="tw-font-medium tw-text-neutral-700"><?php echo _l('disowebs_ops_blocked_gates'); ?></span>
                                    </div>
                                    <span class="tw-px-2 tw-py-1 tw-bg-purple-600 tw-text-white tw-rounded tw-text-sm tw-font-semibold"><?php echo e($quick_actions['blocked_gates']); ?></span>
                                </a>
                                <?php } ?>

                                <?php if (empty($quick_actions['pending_crs']) && empty($quick_actions['unassigned_tasks']) && empty($quick_actions['stuck_projects']) && empty($quick_actions['blocked_gates'])) { ?>
                                <div class="tw-text-center tw-py-6 tw-text-neutral-400">
                                    <i class="fa fa-check-circle tw-text-3xl tw-text-success tw-mb-2"></i>
                                    <p class="tw-mb-0"><?php echo _l('disowebs_ops_all_clear'); ?></p>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deployment Pipeline -->
                <div class="widget">
                    <div class="panel_s">
                        <div class="panel-body padding-10">
                            <p class="tw-font-semibold tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse tw-p-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                </svg>
                                <span class="tw-text-neutral-700">
                                    <?php echo _l('disowebs_ops_deployment_pipeline'); ?>
                                </span>
                            </p>
                            <hr class="-tw-mx-3 tw-mt-3 tw-mb-4">

                            <div class="tw-flex tw-items-center tw-justify-between tw-gap-2">
                                <div class="tw-flex-1 tw-text-center tw-p-2 tw-bg-neutral-100 tw-rounded">
                                    <div class="tw-text-lg tw-font-bold tw-text-neutral-700"><?php echo e($deployment['qa_ready'] ?? 0); ?></div>
                                    <div class="tw-text-xs tw-text-neutral-500">QA</div>
                                </div>
                                <i class="fa fa-arrow-right tw-text-neutral-300"></i>
                                <div class="tw-flex-1 tw-text-center tw-p-2 tw-bg-info/10 tw-rounded">
                                    <div class="tw-text-lg tw-font-bold tw-text-info"><?php echo e($deployment['deploy_ready'] ?? 0); ?></div>
                                    <div class="tw-text-xs tw-text-neutral-500">Deploy</div>
                                </div>
                                <i class="fa fa-arrow-right tw-text-neutral-300"></i>
                                <div class="tw-flex-1 tw-text-center tw-p-2 tw-bg-warning/10 tw-rounded">
                                    <div class="tw-text-lg tw-font-bold tw-text-warning"><?php echo e($deployment['handover_pending'] ?? 0); ?></div>
                                    <div class="tw-text-xs tw-text-neutral-500">Handover</div>
                                </div>
                                <i class="fa fa-arrow-right tw-text-neutral-300"></i>
                                <div class="tw-flex-1 tw-text-center tw-p-2 tw-bg-success/10 tw-rounded">
                                    <div class="tw-text-lg tw-font-bold tw-text-success"><?php echo e($deployment['launched'] ?? 0); ?></div>
                                    <div class="tw-text-xs tw-text-neutral-500">Live</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <!-- 8/4 Split - Projects & Activity -->
            <div class="col-md-8">
                <!-- Active Projects Table -->
                <div class="widget">
                    <div class="panel_s projects-activity">
                        <div class="panel-body padding-10">
                            <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">
                                <p class="tw-font-semibold tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                    </svg>
                                    <span class="tw-text-neutral-700">
                                        <?php echo _l('disowebs_ops_active_projects'); ?>
                                    </span>
                                </p>
                                <a href="<?php echo admin_url('projects'); ?>" class="tw-text-sm">
                                    <?php echo _l('home_widget_view_all'); ?>
                                </a>
                            </div>
                            <hr class="-tw-mx-3 tw-mt-2 tw-mb-4">

                            <?php if (!empty($active_projects)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover tw-mb-0">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('project_name'); ?></th>
                                            <th class="tw-text-center"><?php echo _l('project_status'); ?></th>
                                            <th class="tw-text-center"><?php echo _l('disowebs_ops_progress'); ?></th>
                                            <th class="tw-text-right"><?php echo _l('project_deadline'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($active_projects as $project) { 
                                            $deadline = $project->deadline ?? null;
                                            $is_overdue = $deadline && strtotime($deadline) < time();
                                            $progress = $project->progress ?? 0;
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo admin_url('projects/view/' . $project->id); ?>" class="tw-font-medium">
                                                    <?php echo e($project->name); ?>
                                                </a>
                                                <?php if (!empty($project->client_name)) { ?>
                                                <div class="tw-text-xs tw-text-neutral-400"><?php echo e($project->client_name); ?></div>
                                                <?php } ?>
                                            </td>
                                            <td class="tw-text-center">
                                                <?php 
                                                $status_colors = [1 => 'default', 2 => 'info', 3 => 'warning', 4 => 'success', 5 => 'danger'];
                                                $status_color = $status_colors[$project->status] ?? 'default';
                                                ?>
                                                <span class="label label-<?php echo $status_color; ?>"><?php echo e($project->status_name ?? ''); ?></span>
                                            </td>
                                            <td class="tw-text-center" style="min-width:100px">
                                                <div class="progress progress-bar-mini tw-mb-0">
                                                    <div class="progress-bar progress-bar-<?php echo $progress >= 100 ? 'success' : ($progress >= 50 ? 'info' : 'warning'); ?>" 
                                                         style="width: <?php echo e($progress); ?>%"></div>
                                                </div>
                                                <small class="tw-text-neutral-500"><?php echo e($progress); ?>%</small>
                                            </td>
                                            <td class="tw-text-right <?php echo $is_overdue ? 'text-danger' : ''; ?>">
                                                <?php echo $deadline ? _d($deadline) : '-'; ?>
                                                <?php if ($is_overdue) { ?>
                                                <i class="fa fa-exclamation-triangle tw-ml-1"></i>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } else { ?>
                            <div class="tw-text-center tw-py-8 tw-text-neutral-400">
                                <i class="fa fa-folder-open tw-text-4xl tw-mb-3"></i>
                                <p><?php echo _l('disowebs_ops_no_active_projects'); ?></p>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Urgent Tasks -->
                <div class="widget">
                    <div class="panel_s">
                        <div class="panel-body padding-10">
                            <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">
                                <p class="tw-font-semibold tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-danger">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    <span class="tw-text-neutral-700">
                                        <?php echo _l('disowebs_ops_urgent_tasks'); ?>
                                    </span>
                                </p>
                                <a href="<?php echo admin_url('tasks/list_tasks'); ?>" class="tw-text-sm">
                                    <?php echo _l('home_widget_view_all'); ?>
                                </a>
                            </div>
                            <hr class="-tw-mx-3 tw-mt-2 tw-mb-4">

                            <?php if (!empty($urgent_tasks)) { ?>
                            <ul class="list-unstyled tw-space-y-2 tw-mb-0">
                                <?php foreach ($urgent_tasks as $task) { 
                                    $is_overdue = isset($task->duedate) && strtotime($task->duedate) < time();
                                ?>
                                <li class="tw-flex tw-items-center tw-justify-between tw-p-2 tw-rounded <?php echo $is_overdue ? 'tw-bg-danger/10' : 'tw-bg-neutral-50'; ?>">
                                    <div class="tw-flex tw-items-center tw-gap-3">
                                        <span class="tw-w-2 tw-h-2 tw-rounded-full <?php echo $is_overdue ? 'tw-bg-danger' : 'tw-bg-warning'; ?>"></span>
                                        <a href="<?php echo admin_url('tasks/view/' . $task->id); ?>" class="tw-text-neutral-700 hover:tw-text-primary">
                                            <?php echo e($task->name); ?>
                                        </a>
                                    </div>
                                    <span class="tw-text-xs <?php echo $is_overdue ? 'tw-text-danger tw-font-semibold' : 'tw-text-neutral-500'; ?>">
                                        <?php echo isset($task->duedate) ? _d($task->duedate) : ''; ?>
                                    </span>
                                </li>
                                <?php } ?>
                            </ul>
                            <?php } else { ?>
                            <div class="tw-text-center tw-py-6 tw-text-neutral-400">
                                <i class="fa fa-check-circle tw-text-3xl tw-text-success tw-mb-2"></i>
                                <p class="tw-mb-0"><?php echo _l('disowebs_ops_no_urgent_tasks'); ?></p>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- System Status -->
                <div class="widget">
                    <div class="panel_s">
                        <div class="panel-body padding-10">
                            <p class="tw-font-semibold tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse tw-p-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" />
                                </svg>
                                <span class="tw-text-neutral-700">
                                    <?php echo _l('disowebs_ops_system_status'); ?>
                                </span>
                            </p>
                            <hr class="-tw-mx-3 tw-mt-3 tw-mb-4">

                            <ul class="list-unstyled tw-space-y-3 tw-mb-0">
                                <li class="tw-flex tw-items-center tw-justify-between">
                                    <span class="tw-text-neutral-600">Database</span>
                                    <span class="tw-flex tw-items-center tw-gap-1">
                                        <span class="tw-w-2 tw-h-2 tw-rounded-full <?php echo ($system_health['database'] ?? false) ? 'tw-bg-success' : 'tw-bg-danger'; ?>"></span>
                                        <span class="tw-text-sm <?php echo ($system_health['database'] ?? false) ? 'tw-text-success' : 'tw-text-danger'; ?>">
                                            <?php echo ($system_health['database'] ?? false) ? 'Online' : 'Error'; ?>
                                        </span>
                                    </span>
                                </li>
                                <li class="tw-flex tw-items-center tw-justify-between">
                                    <span class="tw-text-neutral-600">Disk Space</span>
                                    <span class="tw-text-sm tw-text-neutral-500"><?php echo e($system_health['disk_free'] ?? 'N/A'); ?></span>
                                </li>
                                <li class="tw-flex tw-items-center tw-justify-between">
                                    <span class="tw-text-neutral-600">PHP Version</span>
                                    <span class="tw-text-sm tw-text-neutral-500"><?php echo e($system_health['php_version'] ?? PHP_VERSION); ?></span>
                                </li>
                                <li class="tw-flex tw-items-center tw-justify-between">
                                    <span class="tw-text-neutral-600">Memory</span>
                                    <span class="tw-text-sm tw-text-neutral-500"><?php echo e($system_health['memory_usage'] ?? 'N/A'); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Backup Status -->
                <div class="widget">
                    <div class="panel_s">
                        <div class="panel-body padding-10">
                            <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">
                                <p class="tw-font-semibold tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                    </svg>
                                    <span class="tw-text-neutral-700">
                                        <?php echo _l('disowebs_ops_backup_status'); ?>
                                    </span>
                                </p>
                                <a href="<?php echo admin_url('backup'); ?>" class="tw-text-sm">
                                    <?php echo _l('disowebs_ops_manage_backups'); ?>
                                </a>
                            </div>
                            <hr class="-tw-mx-3 tw-mt-2 tw-mb-4">

                            <?php 
                            $backup_ok = ($backup_status['status'] ?? '') === 'ok';
                            ?>
                            <div class="tw-text-center tw-py-4">
                                <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-16 tw-h-16 tw-rounded-full <?php echo $backup_ok ? 'tw-bg-success/10' : 'tw-bg-danger/10'; ?> tw-mb-3">
                                    <i class="fa fa-<?php echo $backup_ok ? 'check' : 'times'; ?> tw-text-2xl <?php echo $backup_ok ? 'tw-text-success' : 'tw-text-danger'; ?>"></i>
                                </div>
                                <?php if (!empty($backup_status['last_backup'])) { ?>
                                <div class="tw-text-sm tw-text-neutral-500">
                                    <?php echo _l('disowebs_ops_last_backup'); ?>
                                </div>
                                <div class="tw-text-neutral-700 tw-font-medium">
                                    <?php echo _dt($backup_status['last_backup']); ?>
                                </div>
                                <?php } else { ?>
                                <div class="tw-text-sm tw-text-neutral-500">
                                    <?php echo _l('disowebs_ops_no_backups_found'); ?>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="widget">
                    <div class="panel_s">
                        <div class="panel-body padding-10">
                            <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">
                                <p class="tw-font-semibold tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="tw-text-neutral-700">
                                        <?php echo _l('disowebs_ops_recent_activity'); ?>
                                    </span>
                                </p>
                                <a href="<?php echo admin_url('utilities/activity_log'); ?>" class="tw-text-sm">
                                    <?php echo _l('home_widget_view_all'); ?>
                                </a>
                            </div>
                            <hr class="-tw-mx-3 tw-mt-2 tw-mb-4">

                            <?php if (!empty($recent_activity)) { ?>
                            <div class="activity-feed">
                                <?php foreach ($recent_activity as $log) { 
                                    // Determine activity color based on description keywords
                                    $desc_lower = strtolower($log->description ?? '');
                                    
                                    // Success actions (green) - completions, approvals, successful operations
                                    if (preg_match('/(completed|approved|success|deployed|finished|closed|resolved|verified|activated|enabled)/', $desc_lower)) {
                                        $activity_type = 'success';
                                        $activity_label = 'Done';
                                    }
                                    // Warning actions (amber) - updates, modifications, in-progress
                                    elseif (preg_match('/(updated|modified|changed|edited|in progress|pending|assigned|moved|transferred)/', $desc_lower)) {
                                        $activity_type = 'warning';
                                        $activity_label = 'Updated';
                                    }
                                    // Danger actions (red) - deletions, failures, errors
                                    elseif (preg_match('/(deleted|removed|failed|error|rejected|cancelled|blocked|stopped|disabled|expired)/', $desc_lower)) {
                                        $activity_type = 'danger';
                                        $activity_label = 'Alert';
                                    }
                                    // Info actions (blue) - logins, views, exports, system events
                                    elseif (preg_match('/(login|logged|viewed|exported|imported|downloaded|sent|email|backup|sync|scheduled)/', $desc_lower)) {
                                        $activity_type = 'info';
                                        $activity_label = 'System';
                                    }
                                    // New/Create actions (purple) - new items
                                    elseif (preg_match('/(created|added|new|registered|inserted|uploaded)/', $desc_lower)) {
                                        $activity_type = 'purple';
                                        $activity_label = 'New';
                                    }
                                    // Default
                                    else {
                                        $activity_type = 'success';
                                        $activity_label = '';
                                    }
                                ?>
                                <div class="feed-item" data-color="<?php echo $activity_type; ?>">
                                    <div class="date">
                                        <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo e(_dt($log->date)); ?>">
                                            <?php echo e(time_ago($log->date)); ?>
                                        </span>
                                        <?php if ($activity_label) { ?>
                                        <span class="activity-badge badge-<?php echo $activity_type; ?>"><?php echo $activity_label; ?></span>
                                        <?php } ?>
                                    </div>
                                    <div class="text">
                                        <strong><?php echo e($log->staff_name ?: 'System'); ?></strong><br />
                                        <?php echo e($log->description); ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } else { ?>
                            <div class="tw-text-center tw-py-6 tw-text-neutral-400">
                                <p class="tw-mb-0"><?php echo _l('disowebs_ops_no_recent_activity'); ?></p>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php } elseif ($active_tab == 'requirements') { ?>
            <!-- ========== REQUIREMENTS TAB ========== -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                            <h4 class="tw-font-bold tw-text-lg tw-mb-0">
                                <i class="fa fa-clipboard-list tw-mr-2"></i><?php echo _l('disowebs_ops_requirements'); ?>
                            </h4>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#requirementModal">
                                <i class="fa fa-plus"></i> <?php echo _l('add_new'); ?>
                            </button>
                        </div>
                        
                        <!-- Stats Row -->
                        <div class="tw-grid tw-grid-cols-4 tw-gap-4 tw-mb-4">
                            <div class="tw-bg-neutral-50 tw-rounded-lg tw-p-4 tw-text-center">
                                <div class="tw-text-2xl tw-font-bold tw-text-neutral-600"><?php echo $requirements_stats['total'] ?? 0; ?></div>
                                <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('total'); ?></div>
                            </div>
                            <div class="tw-bg-warning/10 tw-rounded-lg tw-p-4 tw-text-center">
                                <div class="tw-text-2xl tw-font-bold tw-text-warning"><?php echo $requirements_stats['pending'] ?? 0; ?></div>
                                <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_pending'); ?></div>
                            </div>
                            <div class="tw-bg-info/10 tw-rounded-lg tw-p-4 tw-text-center">
                                <div class="tw-text-2xl tw-font-bold tw-text-info"><?php echo $requirements_stats['in_progress'] ?? 0; ?></div>
                                <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_in_progress'); ?></div>
                            </div>
                            <div class="tw-bg-success/10 tw-rounded-lg tw-p-4 tw-text-center">
                                <div class="tw-text-2xl tw-font-bold tw-text-success"><?php echo $requirements_stats['completed'] ?? 0; ?></div>
                                <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_completed'); ?></div>
                            </div>
                        </div>
                        
                        <!-- Requirements Table -->
                        <div class="table-responsive">
                            <table class="table table-striped dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('disowebs_ops_requirement'); ?></th>
                                        <th><?php echo _l('project'); ?></th>
                                        <th><?php echo _l('disowebs_ops_priority'); ?></th>
                                        <th><?php echo _l('status'); ?></th>
                                        <th><?php echo _l('disowebs_ops_category'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requirements as $req) { ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($req->title); ?></strong>
                                            <?php if ($req->description) { ?>
                                            <br><small class="text-muted"><?php echo character_limiter(strip_tags($req->description), 80); ?></small>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo e($req->project_name ?? '-'); ?></td>
                                        <td>
                                            <?php
                                            $priority_class = ['critical' => 'danger', 'high' => 'warning', 'medium' => 'info', 'low' => 'default'];
                                            ?>
                                            <span class="label label-<?php echo $priority_class[$req->priority] ?? 'default'; ?>">
                                                <?php echo ucfirst($req->priority); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = ['pending' => 'warning', 'approved' => 'info', 'in_development' => 'primary', 'completed' => 'success', 'rejected' => 'danger'];
                                            ?>
                                            <span class="label label-<?php echo $status_class[$req->status] ?? 'default'; ?>">
                                                <?php echo str_replace('_', ' ', ucfirst($req->status)); ?>
                                            </span>
                                        </td>
                                        <td><?php echo ucfirst($req->category ?? 'functional'); ?></td>
                                        <td>
                                            <button class="btn btn-default btn-xs" onclick="editRequirement(<?php echo $req->id; ?>)">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-xs" onclick="deleteRequirement(<?php echo $req->id; ?>)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php } elseif ($active_tab == 'scope') { ?>
            <!-- ========== SCOPE TAB ========== -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                            <h4 class="tw-font-bold tw-text-lg tw-mb-0">
                                <i class="fa fa-file-contract tw-mr-2"></i><?php echo _l('disowebs_ops_scope_documents'); ?>
                            </h4>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#scopeModal">
                                <i class="fa fa-plus"></i> <?php echo _l('add_new'); ?>
                            </button>
                        </div>
                        
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 tw-gap-4">
                            <?php foreach ($scopes as $scope) { ?>
                            <div class="tw-border tw-rounded-lg tw-p-4 tw-bg-white hover:tw-shadow-md tw-transition">
                                <div class="tw-flex tw-justify-between tw-items-start tw-mb-2">
                                    <h5 class="tw-font-semibold tw-mb-0"><?php echo e($scope->title); ?></h5>
                                    <span class="label label-<?php echo $scope->status == 'approved' ? 'success' : ($scope->status == 'draft' ? 'default' : 'warning'); ?>">
                                        <?php echo ucfirst($scope->status); ?>
                                    </span>
                                </div>
                                <p class="tw-text-sm tw-text-neutral-500 tw-mb-2"><?php echo e($scope->project_name ?? 'No Project'); ?></p>
                                <div class="tw-text-xs tw-text-neutral-400">
                                    v<?php echo $scope->version; ?> • <?php echo _d($scope->created_at); ?>
                                </div>
                                <div class="tw-mt-3 tw-pt-3 tw-border-t">
                                    <a href="#" class="btn btn-default btn-xs">
                                        <i class="fa fa-eye"></i> <?php echo _l('view'); ?>
                                    </a>
                                    <a href="#" class="btn btn-default btn-xs">
                                        <i class="fa fa-edit"></i> <?php echo _l('edit'); ?>
                                    </a>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if (empty($scopes)) { ?>
                            <div class="col-md-12 tw-text-center tw-py-8 tw-text-neutral-400">
                                <i class="fa fa-file-contract fa-3x tw-mb-3"></i>
                                <p><?php echo _l('disowebs_ops_no_scope_documents'); ?></p>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php } elseif ($active_tab == 'snippets') { ?>
            <!-- ========== SNIPPETS TAB ========== -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                            <h4 class="tw-font-bold tw-text-lg tw-mb-0">
                                <i class="fa fa-code tw-mr-2"></i><?php echo _l('disowebs_ops_code_snippets'); ?>
                            </h4>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#snippetModal">
                                <i class="fa fa-plus"></i> <?php echo _l('add_new'); ?>
                            </button>
                        </div>
                        
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <?php foreach ($snippets as $snippet) { ?>
                            <div class="tw-border tw-rounded-lg tw-bg-white hover:tw-shadow-md tw-transition">
                                <div class="tw-p-4 tw-border-b tw-flex tw-justify-between tw-items-center">
                                    <div>
                                        <h5 class="tw-font-semibold tw-mb-1"><?php echo e($snippet->title); ?></h5>
                                        <span class="label label-default"><?php echo e($snippet->language); ?></span>
                                        <?php if ($snippet->category) { ?>
                                        <span class="label label-info"><?php echo e($snippet->category); ?></span>
                                        <?php } ?>
                                    </div>
                                    <div>
                                        <button class="btn btn-default btn-xs" onclick="copySnippet(<?php echo $snippet->id; ?>)" title="<?php echo _l('copy'); ?>">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                        <button class="btn btn-default btn-xs" onclick="editSnippet(<?php echo $snippet->id; ?>)">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-xs" onclick="deleteSnippet(<?php echo $snippet->id; ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <pre class="tw-m-0 tw-p-3 tw-bg-neutral-900 tw-text-neutral-100 tw-text-sm tw-rounded-b-lg tw-max-h-40 tw-overflow-auto"><code class="language-<?php echo strtolower($snippet->language); ?>"><?php echo htmlspecialchars($snippet->code); ?></code></pre>
                            </div>
                            <?php } ?>
                            <?php if (empty($snippets)) { ?>
                            <div class="col-md-12 tw-text-center tw-py-8 tw-text-neutral-400">
                                <i class="fa fa-code fa-3x tw-mb-3"></i>
                                <p><?php echo _l('disowebs_ops_no_snippets'); ?></p>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php } elseif ($active_tab == 'checklists') { ?>
            <!-- ========== CHECKLISTS TAB ========== -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                            <h4 class="tw-font-bold tw-text-lg tw-mb-0">
                                <i class="fa fa-tasks tw-mr-2"></i><?php echo _l('disowebs_ops_checklists'); ?>
                            </h4>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#checklistModal">
                                <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_new_checklist'); ?>
                            </button>
                        </div>
                        
                        <!-- Templates -->
                        <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('disowebs_ops_templates'); ?></h5>
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-3 tw-mb-6">
                            <?php foreach ($checklist_templates as $template) { ?>
                            <div class="tw-border tw-rounded-lg tw-p-4 tw-bg-neutral-50 hover:tw-bg-white tw-transition tw-cursor-pointer" onclick="useTemplate(<?php echo $template->id; ?>, '<?php echo e($template->name); ?>')">
                                <div class="tw-flex tw-items-center">
                                    <i class="fa fa-clipboard-check tw-text-xl tw-text-primary tw-mr-3"></i>
                                    <div>
                                        <strong><?php echo e($template->name); ?></strong>
                                        <div class="tw-text-xs tw-text-neutral-500"><?php echo $template->item_count ?? 0; ?> items</div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        
                        <!-- Active Checklists -->
                        <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('disowebs_ops_active_checklists'); ?></h5>
                        <?php if (!empty($active_checklists)) { ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('name'); ?></th>
                                        <th><?php echo _l('project'); ?></th>
                                        <th><?php echo _l('progress'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($active_checklists as $checklist) { 
                                        $progress = $checklist->total_items > 0 ? round(($checklist->completed_items / $checklist->total_items) * 100) : 0;
                                    ?>
                                    <tr>
                                        <td><?php echo e($checklist->name); ?></td>
                                        <td><?php echo e($checklist->project_name ?? '-'); ?></td>
                                        <td>
                                            <div class="progress tw-mb-0" style="height: 8px;">
                                                <div class="progress-bar progress-bar-<?php echo $progress == 100 ? 'success' : 'info'; ?>" style="width: <?php echo $progress; ?>%"></div>
                                            </div>
                                            <small><?php echo $checklist->completed_items ?? 0; ?>/<?php echo $checklist->total_items ?? 0; ?></small>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-default btn-xs" onclick="viewChecklist(<?php echo $checklist->id; ?>)">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } else { ?>
                        <div class="tw-text-center tw-py-6 tw-text-neutral-400">
                            <p><?php echo _l('disowebs_ops_no_active_checklists'); ?></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <?php } elseif ($active_tab == 'notes') { ?>
            <!-- ========== DEV NOTES TAB ========== -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                            <h4 class="tw-font-bold tw-text-lg tw-mb-0">
                                <i class="fa fa-sticky-note tw-mr-2"></i><?php echo _l('disowebs_ops_dev_notes'); ?>
                            </h4>
                        </div>
                        
                        <!-- Quick Note Input -->
                        <div class="tw-mb-4 tw-p-4 tw-bg-neutral-50 tw-rounded-lg">
                            <form id="quickNoteForm">
                                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                <div class="form-group tw-mb-2">
                                    <textarea name="content" class="form-control" rows="2" placeholder="<?php echo _l('disowebs_ops_quick_note_placeholder'); ?>"></textarea>
                                </div>
                                <div class="tw-flex tw-justify-between tw-items-center">
                                    <select name="project_id" class="form-control" style="width: auto;">
                                        <option value=""><?php echo _l('disowebs_ops_no_project'); ?></option>
                                        <?php foreach ($projects as $proj) { ?>
                                        <option value="<?php echo $proj['id']; ?>"><?php echo e($proj['name']); ?></option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> <?php echo _l('add'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Notes List -->
                        <div class="tw-space-y-3">
                            <?php foreach ($dev_notes as $note) { ?>
                            <div class="tw-border tw-rounded-lg tw-p-4 <?php echo $note->is_pinned ? 'tw-border-warning tw-bg-warning/5' : ''; ?>">
                                <div class="tw-flex tw-justify-between tw-items-start">
                                    <div class="tw-flex-1">
                                        <?php if ($note->is_pinned) { ?>
                                        <i class="fa fa-thumbtack tw-text-warning tw-mr-2"></i>
                                        <?php } ?>
                                        <p class="tw-mb-2"><?php echo nl2br(e($note->content)); ?></p>
                                        <small class="tw-text-neutral-400">
                                            <?php if ($note->project_name) { ?>
                                            <span class="label label-default"><?php echo e($note->project_name); ?></span>
                                            <?php } ?>
                                            <?php echo time_ago($note->created_at); ?>
                                        </small>
                                    </div>
                                    <div class="tw-flex tw-gap-1">
                                        <button class="btn btn-default btn-xs" onclick="toggleNotePin(<?php echo $note->id; ?>)" title="<?php echo $note->is_pinned ? _l('unpin') : _l('pin'); ?>">
                                            <i class="fa fa-thumbtack"></i>
                                        </button>
                                        <button class="btn btn-danger btn-xs" onclick="deleteNote(<?php echo $note->id; ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if (empty($dev_notes)) { ?>
                            <div class="tw-text-center tw-py-6 tw-text-neutral-400">
                                <i class="fa fa-sticky-note fa-3x tw-mb-3"></i>
                                <p><?php echo _l('disowebs_ops_no_notes'); ?></p>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php } elseif ($active_tab == 'estimates') { ?>
            <!-- ========== TIME ESTIMATES TAB ========== -->
            <div class="col-md-12">
                <div class="row">
                    <!-- PERT Calculator -->
                    <div class="col-md-4">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold tw-mb-4">
                                    <i class="fa fa-calculator tw-mr-2"></i><?php echo _l('disowebs_ops_pert_calculator'); ?>
                                </h5>
                                <form id="pertForm">
                                    <div class="form-group">
                                        <label><?php echo _l('disowebs_ops_optimistic'); ?> (O)</label>
                                        <input type="number" name="optimistic" class="form-control" step="0.5" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo _l('disowebs_ops_most_likely'); ?> (M)</label>
                                        <input type="number" name="most_likely" class="form-control" step="0.5" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo _l('disowebs_ops_pessimistic'); ?> (P)</label>
                                        <input type="number" name="pessimistic" class="form-control" step="0.5" min="0" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-calculator"></i> <?php echo _l('disowebs_ops_calculate'); ?>
                                    </button>
                                </form>
                                
                                <div id="pertResult" class="tw-mt-4 tw-p-4 tw-bg-neutral-50 tw-rounded-lg tw-hidden">
                                    <div class="tw-text-center">
                                        <div class="tw-text-3xl tw-font-bold tw-text-primary" id="pertEstimate">-</div>
                                        <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_hours'); ?></div>
                                    </div>
                                    <div class="tw-mt-3 tw-text-sm">
                                        <div class="tw-flex tw-justify-between">
                                            <span><?php echo _l('disowebs_ops_confidence_range'); ?>:</span>
                                            <span id="pertRange">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Estimates List -->
                    <div class="col-md-8">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                                    <h5 class="tw-font-semibold tw-mb-0">
                                        <i class="fa fa-clock tw-mr-2"></i><?php echo _l('disowebs_ops_saved_estimates'); ?>
                                    </h5>
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#estimateModal">
                                        <i class="fa fa-plus"></i> <?php echo _l('add_new'); ?>
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th><?php echo _l('disowebs_ops_task_feature'); ?></th>
                                                <th><?php echo _l('project'); ?></th>
                                                <th><?php echo _l('disowebs_ops_estimate'); ?></th>
                                                <th><?php echo _l('disowebs_ops_actual'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($estimates as $est) { ?>
                                            <tr>
                                                <td><?php echo e($est->task_name); ?></td>
                                                <td><?php echo e($est->project_name ?? '-'); ?></td>
                                                <td><?php echo $est->estimated_hours; ?>h</td>
                                                <td>
                                                    <?php if ($est->actual_hours) { ?>
                                                    <span class="<?php echo $est->actual_hours > $est->estimated_hours ? 'text-danger' : 'text-success'; ?>">
                                                        <?php echo $est->actual_hours; ?>h
                                                    </span>
                                                    <?php } else { ?>
                                                    -
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php } elseif ($active_tab == 'documentation') { ?>
            <!-- ========== DOCUMENTATION TAB ========== -->
            <div class="col-md-12">
                <div class="row">
                    <!-- Documentation Overview -->
                    <div class="col-md-4">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold tw-mb-4">
                                    <i class="fa fa-book tw-mr-2"></i><?php echo _l('disowebs_ops_docs_overview'); ?>
                                </h5>
                                <div class="tw-space-y-3">
                                    <div class="tw-flex tw-justify-between tw-items-center tw-p-3 tw-bg-neutral-50 tw-rounded-lg">
                                        <span class="tw-text-neutral-600"><?php echo _l('disowebs_ops_total_docs'); ?></span>
                                        <span class="tw-font-bold tw-text-lg"><?php echo $docs_stats['total'] ?? 0; ?></span>
                                    </div>
                                    <div class="tw-flex tw-justify-between tw-items-center tw-p-3 tw-bg-warning-50 tw-rounded-lg">
                                        <span class="tw-text-neutral-600"><?php echo _l('disowebs_ops_docs_drafts'); ?></span>
                                        <span class="tw-font-bold tw-text-lg tw-text-warning"><?php echo $docs_stats['draft'] ?? 0; ?></span>
                                    </div>
                                    <div class="tw-flex tw-justify-between tw-items-center tw-p-3 tw-bg-success-50 tw-rounded-lg">
                                        <span class="tw-text-neutral-600"><?php echo _l('disowebs_ops_docs_published'); ?></span>
                                        <span class="tw-font-bold tw-text-lg tw-text-success"><?php echo $docs_stats['published'] ?? 0; ?></span>
                                    </div>
                                </div>
                                <hr class="tw-my-4">
                                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#documentModal">
                                    <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_new_document'); ?>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Quick Doc Templates -->
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold tw-mb-4">
                                    <i class="fa fa-file-alt tw-mr-2"></i><?php echo _l('disowebs_ops_doc_templates'); ?>
                                </h5>
                                <div class="tw-space-y-2">
                                    <a href="#" onclick="createDocFromTemplate('readme')" class="tw-block tw-p-3 tw-border tw-rounded-lg hover:tw-bg-neutral-50 tw-transition">
                                        <div class="tw-flex tw-items-center">
                                            <i class="fa fa-file-code tw-mr-3 tw-text-primary"></i>
                                            <div>
                                                <div class="tw-font-medium">README.md</div>
                                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_readme_desc'); ?></div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" onclick="createDocFromTemplate('api')" class="tw-block tw-p-3 tw-border tw-rounded-lg hover:tw-bg-neutral-50 tw-transition">
                                        <div class="tw-flex tw-items-center">
                                            <i class="fa fa-plug tw-mr-3 tw-text-info"></i>
                                            <div>
                                                <div class="tw-font-medium"><?php echo _l('disowebs_ops_api_documentation'); ?></div>
                                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_api_doc_desc'); ?></div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" onclick="createDocFromTemplate('setup')" class="tw-block tw-p-3 tw-border tw-rounded-lg hover:tw-bg-neutral-50 tw-transition">
                                        <div class="tw-flex tw-items-center">
                                            <i class="fa fa-cog tw-mr-3 tw-text-success"></i>
                                            <div>
                                                <div class="tw-font-medium"><?php echo _l('disowebs_ops_setup_guide'); ?></div>
                                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_setup_guide_desc'); ?></div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" onclick="createDocFromTemplate('changelog')" class="tw-block tw-p-3 tw-border tw-rounded-lg hover:tw-bg-neutral-50 tw-transition">
                                        <div class="tw-flex tw-items-center">
                                            <i class="fa fa-history tw-mr-3 tw-text-warning"></i>
                                            <div>
                                                <div class="tw-font-medium">CHANGELOG</div>
                                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_changelog_desc'); ?></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documentation List -->
                    <div class="col-md-8">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                                    <h5 class="tw-font-semibold tw-mb-0">
                                        <i class="fa fa-folder-open tw-mr-2"></i><?php echo _l('disowebs_ops_project_documentation'); ?>
                                    </h5>
                                    <div class="tw-flex tw-gap-2">
                                        <select id="docProjectFilter" class="form-control input-sm" style="width: 200px;">
                                            <option value=""><?php echo _l('disowebs_ops_all_projects'); ?></option>
                                            <?php foreach ($projects as $proj) { ?>
                                            <option value="<?php echo $proj['id']; ?>"><?php echo e($proj['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <?php if (!empty($project_docs)) { ?>
                                <div class="tw-space-y-3" id="documentsList">
                                    <?php foreach ($project_docs as $doc) { ?>
                                    <div class="tw-border tw-rounded-lg tw-p-4 hover:tw-shadow-md tw-transition" data-project="<?php echo $doc->project_id; ?>">
                                        <div class="tw-flex tw-justify-between tw-items-start">
                                            <div class="tw-flex tw-items-start tw-gap-3">
                                                <div class="tw-p-2 tw-bg-primary tw-bg-opacity-10 tw-rounded-lg">
                                                    <i class="fa fa-file-alt tw-text-primary"></i>
                                                </div>
                                                <div>
                                                    <a href="#" onclick="viewDocument(<?php echo $doc->id; ?>)" class="tw-font-semibold tw-text-neutral-800 hover:tw-text-primary">
                                                        <?php echo e($doc->title); ?>
                                                    </a>
                                                    <div class="tw-text-sm tw-text-neutral-500">
                                                        <?php echo e($doc->project_name ?? _l('disowebs_ops_general')); ?>
                                                        <span class="tw-mx-2">•</span>
                                                        <span class="label label-<?php echo $doc->status == 'published' ? 'success' : 'warning'; ?>">
                                                            <?php echo _l('disowebs_ops_doc_status_' . $doc->status); ?>
                                                        </span>
                                                    </div>
                                                    <div class="tw-text-xs tw-text-neutral-400 tw-mt-1">
                                                        <?php echo _l('disowebs_ops_last_updated'); ?>: <?php echo _dt($doc->updated_at); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li><a href="#" onclick="editDocument(<?php echo $doc->id; ?>)"><i class="fa fa-edit tw-mr-2"></i><?php echo _l('edit'); ?></a></li>
                                                    <li><a href="#" onclick="duplicateDocument(<?php echo $doc->id; ?>)"><i class="fa fa-copy tw-mr-2"></i><?php echo _l('copy'); ?></a></li>
                                                    <li><a href="#" onclick="exportDocument(<?php echo $doc->id; ?>)"><i class="fa fa-download tw-mr-2"></i><?php echo _l('export'); ?></a></li>
                                                    <li class="divider"></li>
                                                    <li><a href="#" onclick="deleteDocument(<?php echo $doc->id; ?>)" class="text-danger"><i class="fa fa-trash tw-mr-2"></i><?php echo _l('delete'); ?></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php } else { ?>
                                <div class="tw-text-center tw-py-8">
                                    <i class="fa fa-book tw-text-4xl tw-text-neutral-300 tw-mb-3"></i>
                                    <h5 class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_docs'); ?></h5>
                                    <p class="tw-text-neutral-400"><?php echo _l('disowebs_ops_no_docs_desc'); ?></p>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#documentModal">
                                        <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_create_first_doc'); ?>
                                    </button>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php } elseif ($active_tab == 'planning') { ?>
            <!-- ========== PROJECT PLANNING TAB ========== -->
            <div class="col-md-12">
                <div class="row">
                    <!-- Planning Stats -->
                    <div class="col-md-3">
                        <div class="panel_s">
                            <div class="panel-body tw-text-center">
                                <i class="fa fa-project-diagram tw-text-3xl tw-text-primary tw-mb-2"></i>
                                <div class="tw-text-2xl tw-font-bold"><?php echo $planning_stats['active_projects'] ?? 0; ?></div>
                                <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_active_projects'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel_s">
                            <div class="panel-body tw-text-center">
                                <i class="fa fa-layer-group tw-text-3xl tw-text-info tw-mb-2"></i>
                                <div class="tw-text-2xl tw-font-bold"><?php echo $planning_stats['total_phases'] ?? 0; ?></div>
                                <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_total_phases'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel_s">
                            <div class="panel-body tw-text-center">
                                <i class="fa fa-flag tw-text-3xl tw-text-success tw-mb-2"></i>
                                <div class="tw-text-2xl tw-font-bold"><?php echo $planning_stats['total_milestones'] ?? 0; ?></div>
                                <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_total_milestones'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel_s">
                            <div class="panel-body tw-text-center">
                                <i class="fa fa-exclamation-triangle tw-text-3xl tw-text-warning tw-mb-2"></i>
                                <div class="tw-text-2xl tw-font-bold"><?php echo $planning_stats['overdue_milestones'] ?? 0; ?></div>
                                <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_overdue'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Active Projects with Phases -->
                    <div class="col-md-8">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                                    <h5 class="tw-font-semibold tw-mb-0">
                                        <i class="fa fa-stream tw-mr-2"></i><?php echo _l('disowebs_ops_project_phases'); ?>
                                    </h5>
                                    <a href="<?php echo admin_url('projects'); ?>" class="btn btn-default btn-sm">
                                        <i class="fa fa-external-link-alt"></i> <?php echo _l('disowebs_ops_view_all_projects'); ?>
                                    </a>
                                </div>
                                
                                <?php if (!empty($projects_with_phases)) { ?>
                                <div class="tw-space-y-4">
                                    <?php foreach ($projects_with_phases as $project) { ?>
                                    <div class="tw-border tw-rounded-lg tw-p-4">
                                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-3">
                                            <div>
                                                <a href="<?php echo admin_url('projects/view/' . $project['id']); ?>" class="tw-font-semibold tw-text-primary">
                                                    <?php echo e($project['name']); ?>
                                                </a>
                                                <span class="label label-<?php echo $project['status_color']; ?> tw-ml-2">
                                                    <?php echo $project['status_name']; ?>
                                                </span>
                                            </div>
                                            <span class="tw-text-sm tw-text-neutral-500">
                                                <?php echo $project['phases_completed']; ?>/<?php echo $project['phases_total']; ?> <?php echo _l('disowebs_ops_phases_complete'); ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Phase Progress -->
                                        <div class="tw-flex tw-gap-1 tw-mb-3">
                                            <?php foreach ($project['phases'] as $phase) { ?>
                                            <div class="tw-flex-1 tw-h-2 tw-rounded-full <?php echo $phase['status'] == 'done' ? 'tw-bg-success' : ($phase['status'] == 'in_progress' ? 'tw-bg-info' : 'tw-bg-neutral-200'); ?>"
                                                 title="<?php echo e($phase['name'] . ' - ' . ucfirst($phase['status'])); ?>"
                                                 data-toggle="tooltip"></div>
                                            <?php } ?>
                                        </div>
                                        
                                        <!-- Upcoming Milestones -->
                                        <?php if (!empty($project['upcoming_milestones'])) { ?>
                                        <div class="tw-text-sm">
                                            <span class="tw-text-neutral-500"><?php echo _l('disowebs_ops_upcoming'); ?>:</span>
                                            <?php foreach (array_slice($project['upcoming_milestones'], 0, 2) as $ms) { ?>
                                            <span class="tw-inline-flex tw-items-center tw-bg-neutral-100 tw-px-2 tw-py-1 tw-rounded tw-mr-1">
                                                <i class="fa fa-flag tw-mr-1 tw-text-xs"></i>
                                                <?php echo e($ms['title']); ?>
                                            </span>
                                            <?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php } else { ?>
                                <div class="tw-text-center tw-py-8">
                                    <i class="fa fa-project-diagram tw-text-4xl tw-text-neutral-300 tw-mb-3"></i>
                                    <h5 class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_active_projects'); ?></h5>
                                    <p class="tw-text-neutral-400"><?php echo _l('disowebs_ops_no_projects_desc'); ?></p>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Milestone Timeline -->
                    <div class="col-md-4">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold tw-mb-4">
                                    <i class="fa fa-calendar-alt tw-mr-2"></i><?php echo _l('disowebs_ops_milestone_timeline'); ?>
                                </h5>
                                
                                <?php if (!empty($upcoming_milestones)) { ?>
                                <div class="tw-space-y-3">
                                    <?php foreach ($upcoming_milestones as $milestone) { ?>
                                    <div class="tw-flex tw-items-start tw-gap-3">
                                        <div class="tw-flex-shrink-0 tw-w-10 tw-h-10 tw-rounded-full tw-flex tw-items-center tw-justify-center
                                            <?php echo $milestone['is_overdue'] ? 'tw-bg-danger' : ($milestone['is_this_week'] ? 'tw-bg-warning' : 'tw-bg-neutral-100'); ?>">
                                            <i class="fa fa-flag tw-text-<?php echo $milestone['is_overdue'] ? 'white' : ($milestone['is_this_week'] ? 'white' : 'neutral-500'); ?>"></i>
                                        </div>
                                        <div class="tw-flex-1">
                                            <div class="tw-font-medium tw-text-sm"><?php echo e($milestone['title']); ?></div>
                                            <div class="tw-text-xs tw-text-neutral-500"><?php echo e($milestone['project_name']); ?></div>
                                            <div class="tw-text-xs <?php echo $milestone['is_overdue'] ? 'tw-text-danger tw-font-semibold' : 'tw-text-neutral-400'; ?>">
                                                <?php echo _d($milestone['week_end']); ?>
                                                <?php if ($milestone['is_overdue']) { ?>
                                                <span class="tw-ml-1">(<?php echo _l('disowebs_ops_overdue'); ?>)</span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php } else { ?>
                                <div class="tw-text-center tw-py-4">
                                    <i class="fa fa-calendar-check tw-text-2xl tw-text-neutral-300 tw-mb-2"></i>
                                    <p class="tw-text-sm tw-text-neutral-400"><?php echo _l('disowebs_ops_no_upcoming_milestones'); ?></p>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold tw-mb-4">
                                    <i class="fa fa-bolt tw-mr-2"></i><?php echo _l('disowebs_ops_quick_actions'); ?>
                                </h5>
                                <div class="tw-space-y-2">
                                    <a href="<?php echo admin_url('projects/create'); ?>" class="btn btn-primary btn-block">
                                        <i class="fa fa-plus tw-mr-1"></i> <?php echo _l('new_project'); ?>
                                    </a>
                                    <button type="button" class="btn btn-default btn-block" data-toggle="modal" data-target="#phaseTemplateModal">
                                        <i class="fa fa-layer-group tw-mr-1"></i> <?php echo _l('disowebs_ops_add_phases_to_project'); ?>
                                    </button>
                                    <button type="button" class="btn btn-default btn-block" data-toggle="modal" data-target="#milestoneModal">
                                        <i class="fa fa-flag tw-mr-1"></i> <?php echo _l('disowebs_ops_add_milestone'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php } elseif ($active_tab == 'technical') { ?>
            <!-- ========== TECHNICAL SPECS TAB ========== -->
            <div class="col-md-12">
                <div class="row">
                    <!-- Tech Specs Categories -->
                    <div class="col-md-3">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold tw-mb-4">
                                    <i class="fa fa-folder tw-mr-2"></i><?php echo _l('disowebs_ops_spec_categories'); ?>
                                </h5>
                                <div class="tw-space-y-2">
                                    <a href="#" onclick="filterSpecs('architecture')" class="tw-block tw-p-3 tw-border tw-rounded-lg hover:tw-bg-neutral-50 tw-transition spec-category" data-type="architecture">
                                        <i class="fa fa-sitemap tw-mr-2 tw-text-primary"></i> <?php echo _l('disowebs_ops_spec_architecture'); ?>
                                        <span class="tw-float-right tw-text-neutral-400"><?php echo $tech_specs_counts['architecture'] ?? 0; ?></span>
                                    </a>
                                    <a href="#" onclick="filterSpecs('api')" class="tw-block tw-p-3 tw-border tw-rounded-lg hover:tw-bg-neutral-50 tw-transition spec-category" data-type="api">
                                        <i class="fa fa-plug tw-mr-2 tw-text-info"></i> <?php echo _l('disowebs_ops_spec_api'); ?>
                                        <span class="tw-float-right tw-text-neutral-400"><?php echo $tech_specs_counts['api'] ?? 0; ?></span>
                                    </a>
                                    <a href="#" onclick="filterSpecs('database')" class="tw-block tw-p-3 tw-border tw-rounded-lg hover:tw-bg-neutral-50 tw-transition spec-category" data-type="database">
                                        <i class="fa fa-database tw-mr-2 tw-text-success"></i> <?php echo _l('disowebs_ops_spec_database'); ?>
                                        <span class="tw-float-right tw-text-neutral-400"><?php echo $tech_specs_counts['database'] ?? 0; ?></span>
                                    </a>
                                    <a href="#" onclick="filterSpecs('integration')" class="tw-block tw-p-3 tw-border tw-rounded-lg hover:tw-bg-neutral-50 tw-transition spec-category" data-type="integration">
                                        <i class="fa fa-link tw-mr-2 tw-text-warning"></i> <?php echo _l('disowebs_ops_spec_integration'); ?>
                                        <span class="tw-float-right tw-text-neutral-400"><?php echo $tech_specs_counts['integration'] ?? 0; ?></span>
                                    </a>
                                    <a href="#" onclick="filterSpecs('security')" class="tw-block tw-p-3 tw-border tw-rounded-lg hover:tw-bg-neutral-50 tw-transition spec-category" data-type="security">
                                        <i class="fa fa-shield-alt tw-mr-2 tw-text-danger"></i> <?php echo _l('disowebs_ops_spec_security'); ?>
                                        <span class="tw-float-right tw-text-neutral-400"><?php echo $tech_specs_counts['security'] ?? 0; ?></span>
                                    </a>
                                    <a href="#" onclick="filterSpecs('performance')" class="tw-block tw-p-3 tw-border tw-rounded-lg hover:tw-bg-neutral-50 tw-transition spec-category" data-type="performance">
                                        <i class="fa fa-tachometer-alt tw-mr-2 tw-text-purple"></i> <?php echo _l('disowebs_ops_spec_performance'); ?>
                                        <span class="tw-float-right tw-text-neutral-400"><?php echo $tech_specs_counts['performance'] ?? 0; ?></span>
                                    </a>
                                </div>
                                <hr class="tw-my-4">
                                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#techSpecModal">
                                    <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_new_spec'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Technical Specs List -->
                    <div class="col-md-9">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                                    <h5 class="tw-font-semibold tw-mb-0">
                                        <i class="fa fa-cogs tw-mr-2"></i><?php echo _l('disowebs_ops_technical_specifications'); ?>
                                    </h5>
                                    <select id="techSpecProjectFilter" class="form-control input-sm" style="width: 200px;">
                                        <option value=""><?php echo _l('disowebs_ops_all_projects'); ?></option>
                                        <?php foreach ($projects as $proj) { ?>
                                        <option value="<?php echo $proj['id']; ?>"><?php echo e($proj['name']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                
                                <?php if (!empty($technical_specs)) { ?>
                                <div class="tw-space-y-3" id="techSpecsList">
                                    <?php foreach ($technical_specs as $spec) { ?>
                                    <div class="tw-border tw-rounded-lg tw-p-4 hover:tw-shadow-md tw-transition tech-spec-item" 
                                         data-type="<?php echo $spec->type; ?>" data-project="<?php echo $spec->project_id; ?>">
                                        <div class="tw-flex tw-justify-between tw-items-start">
                                            <div class="tw-flex tw-items-start tw-gap-3">
                                                <div class="tw-p-2 tw-rounded-lg
                                                    <?php 
                                                    $type_colors = ['architecture' => 'primary', 'api' => 'info', 'database' => 'success', 
                                                                   'integration' => 'warning', 'security' => 'danger', 'performance' => 'purple'];
                                                    echo 'tw-bg-' . ($type_colors[$spec->type] ?? 'neutral') . ' tw-bg-opacity-10';
                                                    ?>">
                                                    <i class="fa fa-<?php 
                                                    $type_icons = ['architecture' => 'sitemap', 'api' => 'plug', 'database' => 'database', 
                                                                  'integration' => 'link', 'security' => 'shield-alt', 'performance' => 'tachometer-alt'];
                                                    echo $type_icons[$spec->type] ?? 'file';
                                                    ?> tw-text-<?php echo $type_colors[$spec->type] ?? 'neutral'; ?>"></i>
                                                </div>
                                                <div>
                                                    <a href="#" onclick="viewTechSpec(<?php echo $spec->id; ?>)" class="tw-font-semibold tw-text-neutral-800 hover:tw-text-primary">
                                                        <?php echo e($spec->title); ?>
                                                    </a>
                                                    <div class="tw-text-sm tw-text-neutral-500">
                                                        <?php echo e($spec->project_name ?? _l('disowebs_ops_general')); ?>
                                                        <span class="tw-mx-2">•</span>
                                                        v<?php echo $spec->version; ?>
                                                        <span class="tw-mx-2">•</span>
                                                        <span class="label label-<?php echo $spec->status == 'approved' ? 'success' : ($spec->status == 'review' ? 'warning' : 'default'); ?>">
                                                            <?php echo _l('disowebs_ops_spec_status_' . $spec->status); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li><a href="#" onclick="editTechSpec(<?php echo $spec->id; ?>)"><i class="fa fa-edit tw-mr-2"></i><?php echo _l('edit'); ?></a></li>
                                                    <li><a href="#" onclick="exportTechSpec(<?php echo $spec->id; ?>)"><i class="fa fa-download tw-mr-2"></i><?php echo _l('export'); ?></a></li>
                                                    <li class="divider"></li>
                                                    <li><a href="#" onclick="deleteTechSpec(<?php echo $spec->id; ?>)" class="text-danger"><i class="fa fa-trash tw-mr-2"></i><?php echo _l('delete'); ?></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php } else { ?>
                                <div class="tw-text-center tw-py-8">
                                    <i class="fa fa-cogs tw-text-4xl tw-text-neutral-300 tw-mb-3"></i>
                                    <h5 class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_tech_specs'); ?></h5>
                                    <p class="tw-text-neutral-400"><?php echo _l('disowebs_ops_no_tech_specs_desc'); ?></p>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#techSpecModal">
                                        <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_create_first_spec'); ?>
                                    </button>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
<div class="modal fade" id="requirementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><?php echo _l('disowebs_ops_add_requirement'); ?></h4>
            </div>
            <form id="requirementForm">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="requirement_id">
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_title'); ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo _l('project'); ?></label>
                                <select name="project_id" class="form-control selectpicker" data-live-search="true">
                                    <option value=""><?php echo _l('disowebs_ops_no_project'); ?></option>
                                    <?php foreach ($projects as $proj) { ?>
                                    <option value="<?php echo $proj['id']; ?>"><?php echo e($proj['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_priority'); ?></label>
                                <select name="priority" class="form-control">
                                    <option value="low"><?php echo _l('disowebs_ops_low'); ?></option>
                                    <option value="medium" selected><?php echo _l('disowebs_ops_medium'); ?></option>
                                    <option value="high"><?php echo _l('disowebs_ops_high'); ?></option>
                                    <option value="critical"><?php echo _l('disowebs_ops_critical'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('description'); ?></label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Snippet Modal -->
<div class="modal fade" id="snippetModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><?php echo _l('disowebs_ops_add_snippet'); ?></h4>
            </div>
            <form id="snippetForm">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="snippet_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_title'); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_language'); ?></label>
                                <select name="language" class="form-control">
                                    <option value="php">PHP</option>
                                    <option value="javascript">JavaScript</option>
                                    <option value="html">HTML</option>
                                    <option value="css">CSS</option>
                                    <option value="sql">SQL</option>
                                    <option value="bash">Bash</option>
                                    <option value="python">Python</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_code'); ?> <span class="text-danger">*</span></label>
                        <textarea name="code" class="form-control" rows="10" style="font-family: monospace;" required></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('description'); ?></label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><?php echo _l('disowebs_ops_new_document'); ?></h4>
            </div>
            <form id="documentForm">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="document_id">
                    <input type="hidden" name="template" id="document_template">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_title'); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo _l('project'); ?></label>
                                <select name="project_id" class="form-control selectpicker" data-live-search="true">
                                    <option value=""><?php echo _l('disowebs_ops_general'); ?></option>
                                    <?php foreach ($projects as $proj) { ?>
                                    <option value="<?php echo $proj['id']; ?>"><?php echo e($proj['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_doc_type'); ?></label>
                                <select name="doc_type" class="form-control">
                                    <option value="readme">README</option>
                                    <option value="api">API Documentation</option>
                                    <option value="setup">Setup Guide</option>
                                    <option value="changelog">Changelog</option>
                                    <option value="technical">Technical Guide</option>
                                    <option value="user">User Manual</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo _l('status'); ?></label>
                                <select name="status" class="form-control">
                                    <option value="draft"><?php echo _l('disowebs_ops_draft'); ?></option>
                                    <option value="published"><?php echo _l('disowebs_ops_published'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_content'); ?> <span class="text-danger">*</span></label>
                        <textarea name="content" id="documentContent" class="form-control" rows="15" required></textarea>
                        <small class="text-muted"><?php echo _l('disowebs_ops_markdown_supported'); ?></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Technical Spec Modal -->
<div class="modal fade" id="techSpecModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><?php echo _l('disowebs_ops_new_spec'); ?></h4>
            </div>
            <form id="techSpecForm">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="techspec_id">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_title'); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_version'); ?></label>
                                <input type="text" name="version" class="form-control" value="1.0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo _l('project'); ?></label>
                                <select name="project_id" class="form-control selectpicker" data-live-search="true">
                                    <option value=""><?php echo _l('disowebs_ops_general'); ?></option>
                                    <?php foreach ($projects as $proj) { ?>
                                    <option value="<?php echo $proj['id']; ?>"><?php echo e($proj['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_spec_type'); ?></label>
                                <select name="type" class="form-control">
                                    <option value="architecture"><?php echo _l('disowebs_ops_spec_architecture'); ?></option>
                                    <option value="api"><?php echo _l('disowebs_ops_spec_api'); ?></option>
                                    <option value="database"><?php echo _l('disowebs_ops_spec_database'); ?></option>
                                    <option value="integration"><?php echo _l('disowebs_ops_spec_integration'); ?></option>
                                    <option value="security"><?php echo _l('disowebs_ops_spec_security'); ?></option>
                                    <option value="performance"><?php echo _l('disowebs_ops_spec_performance'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo _l('status'); ?></label>
                                <select name="status" class="form-control">
                                    <option value="draft"><?php echo _l('disowebs_ops_draft'); ?></option>
                                    <option value="review"><?php echo _l('disowebs_ops_in_review'); ?></option>
                                    <option value="approved"><?php echo _l('disowebs_ops_approved'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_content'); ?> <span class="text-danger">*</span></label>
                        <textarea name="content" id="techSpecContent" class="form-control" rows="15" required></textarea>
                        <small class="text-muted"><?php echo _l('disowebs_ops_markdown_supported'); ?></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Milestone Modal -->
<div class="modal fade" id="milestoneModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><?php echo _l('disowebs_ops_add_milestone'); ?></h4>
            </div>
            <form id="milestoneForm">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="milestone_id">
                    <div class="form-group">
                        <label><?php echo _l('project'); ?> <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-control selectpicker" data-live-search="true" required onchange="loadProjectPhases(this.value)">
                            <option value=""><?php echo _l('disowebs_ops_select_project'); ?></option>
                            <?php foreach ($projects as $proj) { ?>
                            <option value="<?php echo $proj['id']; ?>"><?php echo e($proj['name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_phase'); ?></label>
                        <select name="phase_id" id="milestonePhaseSelect" class="form-control">
                            <option value=""><?php echo _l('disowebs_ops_no_phase'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_title'); ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_week_start'); ?> <span class="text-danger">*</span></label>
                                <input type="date" name="week_start" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_week_end'); ?> <span class="text-danger">*</span></label>
                                <input type="date" name="week_end" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('description'); ?></label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Checklist Modal -->
<div class="modal fade" id="checklistModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><?php echo _l('disowebs_ops_new_checklist'); ?></h4>
            </div>
            <form id="checklistForm">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <input type="hidden" name="id" id="checklist_id" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo _l('name'); ?> <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="<?php echo _l('disowebs_ops_checklist_name_placeholder'); ?>">
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('project'); ?></label>
                        <select name="project_id" class="form-control selectpicker" data-live-search="true">
                            <option value=""><?php echo _l('disowebs_ops_select_project'); ?></option>
                            <?php foreach ($projects as $proj) { ?>
                            <option value="<?php echo $proj['id']; ?>"><?php echo e($proj['name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_template'); ?></label>
                        <select name="template_id" class="form-control">
                            <option value=""><?php echo _l('disowebs_ops_blank_checklist'); ?></option>
                            <?php foreach ($checklist_templates as $tpl) { ?>
                            <option value="<?php echo $tpl->id; ?>"><?php echo e($tpl->name); ?></option>
                            <?php } ?>
                        </select>
                        <small class="text-muted"><?php echo _l('disowebs_ops_template_help'); ?></small>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('description'); ?></label>
                        <textarea name="description" class="form-control" rows="2" placeholder="<?php echo _l('disowebs_ops_checklist_description_placeholder'); ?>"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Phase Template Modal -->
<div class="modal fade" id="phaseTemplateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><?php echo _l('disowebs_ops_add_phases_to_project'); ?></h4>
            </div>
            <form id="phaseTemplateForm">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo _l('project'); ?> <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-control selectpicker" data-live-search="true" required>
                            <option value=""><?php echo _l('disowebs_ops_select_project'); ?></option>
                            <?php foreach ($projects as $proj) { ?>
                            <option value="<?php echo $proj['id']; ?>"><?php echo e($proj['name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_phase_template'); ?></label>
                        <select name="template" class="form-control">
                            <option value="default"><?php echo _l('disowebs_ops_default_phases'); ?></option>
                            <option value="agile"><?php echo _l('disowebs_ops_agile_phases'); ?></option>
                            <option value="waterfall"><?php echo _l('disowebs_ops_waterfall_phases'); ?></option>
                        </select>
                        <small class="text-muted"><?php echo _l('disowebs_ops_phase_template_help'); ?></small>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="create_milestones" value="1" id="create_milestones" checked>
                        <label for="create_milestones"><?php echo _l('disowebs_ops_auto_create_milestones'); ?></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('disowebs_ops_create_phases'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scope Modal -->
<div class="modal fade" id="scopeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><?php echo _l('disowebs_ops_new_scope_document'); ?></h4>
            </div>
            <form id="scopeForm">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <input type="hidden" name="id" id="scope_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_title'); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required placeholder="Project Scope Document">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo _l('disowebs_ops_version'); ?></label>
                                <input type="text" name="version" class="form-control" value="1.0" placeholder="1.0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo _l('project'); ?></label>
                                <select name="project_id" class="form-control selectpicker" data-live-search="true">
                                    <option value=""><?php echo _l('disowebs_ops_select_project'); ?></option>
                                    <?php foreach ($projects as $proj) { ?>
                                    <option value="<?php echo $proj['id']; ?>"><?php echo e($proj['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo _l('status'); ?></label>
                                <select name="status" class="form-control">
                                    <option value="draft"><?php echo _l('disowebs_ops_draft'); ?></option>
                                    <option value="review"><?php echo _l('disowebs_ops_in_review'); ?></option>
                                    <option value="approved"><?php echo _l('disowebs_ops_approved'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_project_overview'); ?></label>
                        <textarea name="overview" class="form-control" rows="3" placeholder="Brief overview of the project..."></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_objectives'); ?></label>
                        <textarea name="objectives" class="form-control" rows="3" placeholder="Project objectives and goals..."></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_deliverables'); ?></label>
                        <textarea name="deliverables" class="form-control" rows="3" placeholder="Key deliverables..."></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_exclusions'); ?></label>
                        <textarea name="exclusions" class="form-control" rows="2" placeholder="What's NOT included in scope..."></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_assumptions'); ?></label>
                        <textarea name="assumptions" class="form-control" rows="2" placeholder="Project assumptions..."></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_constraints'); ?></label>
                        <textarea name="constraints" class="form-control" rows="2" placeholder="Budget, timeline, resource constraints..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<?php echo $this->load->view('admin/dashboards/_dev_dashboard_js', [], true); ?>
