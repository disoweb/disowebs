<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Header -->
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-6">
                    <div>
                        <h4 class="tw-text-lg tw-font-semibold tw-text-neutral-800 tw-mb-1">
                            <i class="fa fa-flag-checkered tw-mr-2" style="color: #16a34a;"></i>
                            <?php echo e($title); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_milestones_delivered_desc'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('disowebs_ops'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left tw-mr-1"></i> <?php echo _l('back'); ?>
                    </a>
                </div>

                <!-- Period Filter -->
                <div class="tw-mb-4">
                    <div class="btn-group">
                        <a href="<?php echo admin_url('disowebs_ops/milestones_delivered?period=day'); ?>" class="btn btn-<?php echo $period === 'day' ? 'primary' : 'default'; ?>"><?php echo _l('disowebs_ops_range_day'); ?></a>
                        <a href="<?php echo admin_url('disowebs_ops/milestones_delivered?period=week'); ?>" class="btn btn-<?php echo $period === 'week' ? 'primary' : 'default'; ?>"><?php echo _l('disowebs_ops_range_week'); ?></a>
                        <a href="<?php echo admin_url('disowebs_ops/milestones_delivered?period=month'); ?>" class="btn btn-<?php echo $period === 'month' ? 'primary' : 'default'; ?>"><?php echo _l('disowebs_ops_range_month'); ?></a>
                        <a href="<?php echo admin_url('disowebs_ops/milestones_delivered?period=quarter'); ?>" class="btn btn-<?php echo $period === 'quarter' ? 'primary' : 'default'; ?>"><?php echo _l('disowebs_ops_range_quarter'); ?></a>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4">
                            <div class="tw-w-16 tw-h-16 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: #dcfce7;">
                                <span class="tw-text-2xl tw-font-bold" style="color: #16a34a;"><?php echo count($milestones); ?></span>
                            </div>
                            <div>
                                <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-1"><?php echo _l('disowebs_ops_milestones_completed'); ?></h5>
                                <p class="tw-text-neutral-500 tw-text-sm tw-mb-0">
                                    <?php echo _d($range['start']); ?> - <?php echo _d($range['end']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Milestones List -->
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (empty($milestones)) { ?>
                        <div class="tw-text-center tw-py-12">
                            <i class="fa fa-inbox tw-text-5xl tw-mb-4 tw-text-neutral-300"></i>
                            <h5 class="tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_no_milestones_period'); ?></h5>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_milestones_period_desc'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="3" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('project'); ?></th>
                                        <th><?php echo _l('client'); ?></th>
                                        <th><?php echo _l('disowebs_ops_milestone'); ?></th>
                                        <th><?php echo _l('disowebs_ops_completed_date'); ?></th>
                                        <th class="tw-text-center"><?php echo _l('disowebs_ops_action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($milestones as $milestone) { ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo admin_url('projects/view/' . $milestone['project_id']); ?>" class="tw-font-medium">
                                                <?php echo e($milestone['project_name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo e($milestone['client_name'] ?? '-'); ?></td>
                                        <td>
                                            <span class="tw-font-medium"><?php echo e($milestone['title']); ?></span>
                                            <?php if (!empty($milestone['description'])) { ?>
                                            <p class="tw-text-neutral-500 tw-text-sm tw-mb-0 tw-mt-1"><?php echo e(mb_strlen($milestone['description']) > 80 ? mb_substr($milestone['description'], 0, 80) . '...' : $milestone['description']); ?></p>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <span class="tw-text-sm"><?php echo _dt($milestone['done_at']); ?></span>
                                        </td>
                                        <td class="tw-text-center">
                                            <a href="<?php echo admin_url('projects/view/' . $milestone['project_id']); ?>" class="btn btn-default btn-xs">
                                                <i class="fa fa-eye tw-mr-1"></i> <?php echo _l('view'); ?>
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
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
