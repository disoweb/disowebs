<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Header -->
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-6">
                    <div>
                        <h4 class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mb-1">
                            <i class="fa fa-video tw-mr-2" style="color: #2563eb;"></i>
                            <?php echo e($title); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_demos_desc'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('disowebs_ops'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left tw-mr-1"></i> <?php echo _l('back'); ?>
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: #2563eb;"><?php echo $stats['active_projects']; ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_active_projects'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: #16a34a;"><?php echo $stats['demos_scheduled']; ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_scheduled_this_week'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: #8b5cf6;"><?php echo $stats['demos_completed']; ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_completed_this_week'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: <?php echo $stats['missing_demos'] > 0 ? '#dc2626' : '#16a34a'; ?>;"><?php echo $stats['missing_demos']; ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_missing_demos'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Demos -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-4">
                            <i class="fa fa-calendar-alt tw-mr-2" style="color: #2563eb;"></i>
                            <?php echo _l('disowebs_ops_upcoming_demos'); ?>
                        </h5>
                        <?php if (empty($upcoming_demos)) { ?>
                        <div class="tw-text-center tw-py-8">
                            <i class="fa fa-calendar tw-text-4xl tw-mb-3 tw-text-neutral-300"></i>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_upcoming_demos'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('project'); ?></th>
                                        <th><?php echo _l('disowebs_ops_demo_date'); ?></th>
                                        <th><?php echo _l('disowebs_ops_week'); ?></th>
                                        <th class="tw-text-center"><?php echo _l('disowebs_ops_action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcoming_demos as $demo) { ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo admin_url('projects/view/' . $demo['project_id']); ?>" class="tw-font-medium">
                                                <?php echo e($demo['project_name']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if (!empty($demo['demo_date'])) { ?>
                                            <span class="tw-font-medium"><?php echo _d($demo['demo_date']); ?></span>
                                            <?php if (!empty($demo['demo_time'])) { ?>
                                            <span class="tw-text-neutral-500"> @ <?php echo e($demo['demo_time']); ?></span>
                                            <?php } ?>
                                            <?php } else { ?>
                                            <span class="tw-text-neutral-400"><?php echo _l('disowebs_ops_tbd'); ?></span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <span class="tw-text-sm tw-text-neutral-500"><?php echo _d($demo['week_start']); ?> - <?php echo _d($demo['week_end']); ?></span>
                                        </td>
                                        <td class="tw-text-center">
                                            <a href="<?php echo admin_url('projects/view/' . $demo['project_id'] . '?group=disowebs_ops_gates'); ?>" class="btn btn-default btn-xs">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Recent Completed Demos -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-4">
                            <i class="fa fa-check-circle tw-mr-2" style="color: #16a34a;"></i>
                            <?php echo _l('disowebs_ops_recent_demos'); ?>
                        </h5>
                        <?php if (empty($recent_demos)) { ?>
                        <div class="tw-text-center tw-py-8">
                            <i class="fa fa-video-slash tw-text-4xl tw-mb-3 tw-text-neutral-300"></i>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_recent_demos'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="2" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('project'); ?></th>
                                        <th><?php echo _l('client'); ?></th>
                                        <th><?php echo _l('disowebs_ops_demo_date'); ?></th>
                                        <th><?php echo _l('disowebs_ops_notes'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_demos as $demo) { ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo admin_url('projects/view/' . $demo['project_id']); ?>" class="tw-font-medium">
                                                <?php echo e($demo['project_name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo e($demo['client_name'] ?? '-'); ?></td>
                                        <td><?php echo _d($demo['demo_date']); ?></td>
                                        <td class="tw-text-neutral-500 tw-text-sm"><?php echo e(character_limiter($demo['notes'] ?? '', 50)); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
