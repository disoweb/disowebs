<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
// Use data from controller
$active_count = isset($active_count) ? (int)$active_count : 0;
$limit = isset($project_limit) ? (int)$project_limit : 4;
$capacity_percent = $limit > 0 ? min(100, round(($active_count / $limit) * 100)) : 0;
$capacity_class = $capacity_percent >= 100 ? 'danger' : ($capacity_percent >= 75 ? 'warning' : 'success');
$delivery_stats = isset($delivery_stats) ? $delivery_stats : [];
$blockers = isset($blockers) ? $blockers : [];
$upcoming_demos = isset($upcoming_demos) ? $upcoming_demos : [];
?>
<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="tw-mb-6">
            <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                <div>
                    <h4 class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mb-1"><?php echo e($title); ?></h4>
                    <p class="tw-text-neutral-500 tw-mb-0"><?php echo e(_l('disowebs_ops_delivery_intro')); ?></p>
                </div>
                <div class="tw-mt-4 md:tw-mt-0">
                    <a href="<?php echo admin_url('disowebs_ops/settings'); ?>" class="btn btn-default">
                        <i class="fa fa-cog"></i> <?php echo e(_l('settings')); ?>
                    </a>
                </div>
            </div>
        </div>

<?php
// Set icon colors based on capacity
$capacity_bg = $capacity_class === 'danger' ? '#fee2e2' : ($capacity_class === 'warning' ? '#fef3c7' : '#dcfce7');
$capacity_color = $capacity_class === 'danger' ? '#dc2626' : ($capacity_class === 'warning' ? '#d97706' : '#16a34a');
$blockers_count = $delivery_stats['active_blockers'] ?? 0;
$blockers_bg = $blockers_count > 0 ? '#fee2e2' : '#dcfce7';
$blockers_color = $blockers_count > 0 ? '#dc2626' : '#16a34a';
$missing_count = $delivery_stats['demo_missing'] ?? 0;
$missing_color = $missing_count > 0 ? '#dc2626' : '#16a34a';
?>
        <!-- KPI Cards Row -->
        <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
            <!-- Active Projects / Capacity -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_active_projects')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: <?php echo $capacity_bg; ?>;">
                            <i class="fa fa-folder-open" style="color: <?php echo $capacity_color; ?>;"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold tw-text-neutral-800"><?php echo $active_count; ?><span class="tw-text-lg tw-text-neutral-400">/<?php echo $limit; ?></span></div>
                    <div class="tw-w-full tw-rounded-full tw-mt-2" style="height: 6px; background-color: #e5e7eb;">
                        <div class="tw-rounded-full" style="height: 6px; width: <?php echo $capacity_percent; ?>%; background-color: <?php echo $capacity_color; ?>;"></div>
                    </div>
                </div>
            </div>

            <!-- Active Blockers -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_active_blockers')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: <?php echo $blockers_bg; ?>;">
                            <i class="fa fa-exclamation-triangle" style="color: <?php echo $blockers_color; ?>;"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold tw-text-neutral-800"><?php echo $blockers_count; ?></div>
                    <span class="tw-text-xs tw-text-neutral-400"><?php echo ($delivery_stats['overdue_blockers'] ?? 0); ?> <?php echo e(_l('disowebs_ops_overdue')); ?></span>
                </div>
            </div>

            <!-- Demos This Week -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_demos_this_week')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: #dbeafe;">
                            <i class="fa fa-video-camera" style="color: #0284c7;"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold tw-text-neutral-800"><?php echo $delivery_stats['demos_completed'] ?? 0; ?></div>
                    <span class="tw-text-xs" style="color: <?php echo $missing_color; ?>;"><?php echo $missing_count; ?> <?php echo e(_l('disowebs_ops_missing')); ?></span>
                </div>
            </div>

            <!-- Payment Gates -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_payment_gates')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: #fef3c7;">
                            <i class="fa fa-money" style="color: #d97706;"></i>
                        </span>
                    </div>
                    <div class="tw-flex tw-items-baseline tw-gap-3">
                        <div>
                            <div class="tw-text-xl tw-font-bold" style="color: #d97706;"><?php echo $delivery_stats['deposit_pending'] ?? 0; ?></div>
                            <span class="tw-text-xs tw-text-neutral-400"><?php echo e(_l('disowebs_ops_deposits')); ?></span>
                        </div>
                        <div>
                            <div class="tw-text-xl tw-font-bold" style="color: #0284c7;"><?php echo $delivery_stats['final_pending'] ?? 0; ?></div>
                            <span class="tw-text-xs tw-text-neutral-400"><?php echo e(_l('disowebs_ops_finals')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Active Blockers List -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                            <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-0">
                                <i class="fa fa-exclamation-circle text-danger tw-mr-2"></i>
                                <?php echo e(_l('disowebs_ops_active_blockers')); ?>
                            </h5>
                        </div>
                        <?php if (empty($blockers)) { ?>
                        <div class="tw-text-center tw-py-8">
                            <i class="fa fa-check-circle tw-text-4xl tw-text-success-500 tw-mb-3"></i>
                            <p class="tw-text-neutral-500 tw-mb-0"><?php echo e(_l('disowebs_ops_no_blockers')); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="tw-space-y-3">
                            <?php foreach ($blockers as $blocker) { 
                                $is_overdue = !empty($blocker['next_action_date']) && $blocker['next_action_date'] < date('Y-m-d');
                                $owner_label = ucfirst($blocker['owner_type'] ?? 'team');
                            ?>
                            <div class="tw-p-3 tw-rounded-lg tw-border <?php echo $is_overdue ? 'tw-border-danger-200 tw-bg-danger-50' : 'tw-border-neutral-200 tw-bg-neutral-50'; ?>">
                                <div class="tw-flex tw-items-start tw-justify-between">
                                    <div class="tw-flex-1">
                                        <div class="tw-font-medium tw-text-neutral-800"><?php echo e($blocker['blocker_description']); ?></div>
                                        <div class="tw-text-sm tw-text-neutral-500 tw-mt-1">
                                            <a href="<?php echo admin_url('projects/view/' . $blocker['project_id']); ?>"><?php echo e($blocker['project_name'] ?? 'Project #' . $blocker['project_id']); ?></a>
                                            <?php if (!empty($blocker['milestone_title'])) { ?>
                                            <span class="tw-mx-1">•</span> <?php echo e($blocker['milestone_title']); ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="tw-text-right">
                                        <span class="label label-<?php echo $owner_label === 'client' ? 'warning' : 'info'; ?>"><?php echo $owner_label; ?></span>
                                        <?php if ($is_overdue) { ?>
                                        <div class="tw-text-xs tw-text-danger-600 tw-mt-1"><?php echo e(_l('disowebs_ops_overdue')); ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Upcoming Demos -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                            <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-0">
                                <i class="fa fa-calendar text-info tw-mr-2"></i>
                                <?php echo e(_l('disowebs_ops_upcoming_demos')); ?>
                            </h5>
                        </div>
                        <?php if (empty($upcoming_demos)) { ?>
                        <div class="tw-text-center tw-py-8">
                            <i class="fa fa-calendar-o tw-text-4xl tw-text-neutral-300 tw-mb-3"></i>
                            <p class="tw-text-neutral-500 tw-mb-0"><?php echo e(_l('disowebs_ops_no_upcoming_demos')); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="tw-space-y-3">
                            <?php foreach ($upcoming_demos as $demo) { 
                                $demo_date = !empty($demo['demo_scheduled_at']) ? _dt($demo['demo_scheduled_at']) : _l('disowebs_ops_not_scheduled');
                                $is_completed = !empty($demo['demo_completed']);
                            ?>
                            <div class="tw-p-3 tw-rounded-lg tw-border tw-border-neutral-200 tw-bg-white">
                                <div class="tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-flex-1">
                                        <div class="tw-font-medium tw-text-neutral-800">
                                            <a href="<?php echo admin_url('projects/view/' . $demo['project_id']); ?>"><?php echo e($demo['project_name'] ?? 'Project #' . $demo['project_id']); ?></a>
                                        </div>
                                        <div class="tw-text-sm tw-text-neutral-500"><?php echo $demo_date; ?></div>
                                    </div>
                                    <?php if ($is_completed) { ?>
                                    <span class="label label-success"><i class="fa fa-check"></i> <?php echo e(_l('disowebs_ops_completed')); ?></span>
                                    <?php } else { ?>
                                    <span class="label label-default"><?php echo e(_l('disowebs_ops_pending')); ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="panel_s">
            <div class="panel-body">
                <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-4"><?php echo e(_l('disowebs_ops_quick_actions')); ?></h5>
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4">
                    <a href="<?php echo admin_url('projects'); ?>" class="tw-p-4 tw-rounded-lg tw-border tw-border-neutral-200 tw-bg-white hover:tw-bg-neutral-50 tw-text-center tw-transition tw-no-underline">
                        <i class="fa fa-folder-open tw-text-2xl tw-text-primary-500 tw-mb-2"></i>
                        <div class="tw-font-medium tw-text-neutral-700"><?php echo e(_l('disowebs_ops_view_projects')); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/scope'); ?>" class="tw-p-4 tw-rounded-lg tw-border tw-border-neutral-200 tw-bg-white hover:tw-bg-neutral-50 tw-text-center tw-transition tw-no-underline">
                        <i class="fa fa-exchange tw-text-2xl tw-text-warning-500 tw-mb-2"></i>
                        <div class="tw-font-medium tw-text-neutral-700"><?php echo e(_l('disowebs_ops_scope_change_requests')); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/proof'); ?>" class="tw-p-4 tw-rounded-lg tw-border tw-border-neutral-200 tw-bg-white hover:tw-bg-neutral-50 tw-text-center tw-transition tw-no-underline">
                        <i class="fa fa-camera tw-text-2xl tw-text-success-500 tw-mb-2"></i>
                        <div class="tw-font-medium tw-text-neutral-700"><?php echo e(_l('disowebs_ops_proof_vault')); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/reports'); ?>" class="tw-p-4 tw-rounded-lg tw-border tw-border-neutral-200 tw-bg-white hover:tw-bg-neutral-50 tw-text-center tw-transition tw-no-underline">
                        <i class="fa fa-bar-chart tw-text-2xl tw-text-info-500 tw-mb-2"></i>
                        <div class="tw-font-medium tw-text-neutral-700"><?php echo e(_l('disowebs_ops_reports')); ?></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
