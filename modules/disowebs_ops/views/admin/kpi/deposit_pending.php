<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $currency = get_base_currency(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Header -->
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-6">
                    <div>
                        <h4 class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mb-1">
                            <i class="fa fa-credit-card tw-mr-2" style="color: #d97706;"></i>
                            <?php echo e($title); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_deposit_pending_desc'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('disowebs_ops'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left tw-mr-1"></i> <?php echo _l('back'); ?>
                    </a>
                </div>

                <!-- Stats Card -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4">
                            <div class="tw-w-16 tw-h-16 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: #fef3c7;">
                                <span class="tw-text-2xl tw-font-bold" style="color: #d97706;"><?php echo count($projects); ?></span>
                            </div>
                            <div>
                                <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-1"><?php echo _l('disowebs_ops_projects_awaiting_deposit'); ?></h5>
                                <p class="tw-text-neutral-500 tw-text-sm tw-mb-0"><?php echo _l('disowebs_ops_deposit_pending_help'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projects List -->
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (empty($projects)) { ?>
                        <div class="tw-text-center tw-py-12">
                            <i class="fa fa-check-circle tw-text-5xl tw-mb-4" style="color: #16a34a;"></i>
                            <h5 class="tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_all_deposits_cleared'); ?></h5>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_all_deposits_cleared_desc'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="0" data-order-type="asc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('project'); ?></th>
                                        <th><?php echo _l('client'); ?></th>
                                        <th><?php echo _l('disowebs_ops_required'); ?></th>
                                        <th><?php echo _l('disowebs_ops_paid'); ?></th>
                                        <th><?php echo _l('disowebs_ops_outstanding'); ?></th>
                                        <th class="tw-text-center"><?php echo _l('disowebs_ops_action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projects as $project) { ?>
                                    <?php
                                    $required = (float)($project['deposit_required'] ?? 0);
                                    $paid = (float)($project['deposit_paid'] ?? 0);
                                    $outstanding = max(0, $required - $paid);
                                    $percent = $required > 0 ? round(($paid / $required) * 100) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo admin_url('projects/view/' . $project['project_id'] . '?group=disowebs_ops_gates'); ?>" class="tw-font-medium">
                                                <?php echo e($project['project_name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo e($project['client_name'] ?? '-'); ?></td>
                                        <td><?php echo app_format_money($required, $currency); ?></td>
                                        <td>
                                            <span style="color: #16a34a;"><?php echo app_format_money($paid, $currency); ?></span>
                                            <span class="tw-text-neutral-400 tw-text-sm tw-ml-1">(<?php echo $percent; ?>%)</span>
                                        </td>
                                        <td>
                                            <span style="color: #dc2626; font-weight: 600;"><?php echo app_format_money($outstanding, $currency); ?></span>
                                        </td>
                                        <td class="tw-text-center">
                                            <a href="<?php echo admin_url('projects/view/' . $project['project_id'] . '?group=disowebs_ops_gates'); ?>" class="btn btn-primary btn-xs">
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
