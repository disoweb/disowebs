<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                    <div>
                        <h4 class="tw-font-semibold tw-text-lg tw-text-neutral-800 tw-my-0">
                            <a href="<?php echo admin_url('disowebs_ops'); ?>" class="tw-text-neutral-500 hover:tw-text-neutral-700">
                                <i class="fa fa-arrow-left tw-mr-2"></i>
                            </a>
                            <?php echo $days; ?>-Day <?php echo _l('disowebs_ops_pipeline_forecast_title'); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_pipeline_forecast_desc'); ?></p>
                    </div>
                    <div class="tw-flex tw-gap-2">
                        <a href="<?php echo admin_url('disowebs_ops/pipeline_forecast/30'); ?>" class="btn btn-<?php echo $days == 30 ? 'primary' : 'default'; ?>">
                            30 Days
                        </a>
                        <a href="<?php echo admin_url('disowebs_ops/pipeline_forecast/60'); ?>" class="btn btn-<?php echo $days == 60 ? 'primary' : 'default'; ?>">
                            60 Days
                        </a>
                    </div>
                </div>

                <?php 
                $currency = get_base_currency();
                $currency_name = $currency ? $currency->name : '';
                $total_raw = 0;
                $total_weighted = 0;
                foreach ($proposals as $p) {
                    $total_raw += $p['total'];
                    $total_weighted += $p['weighted_value'];
                }
                ?>

                <!-- Stats Summary -->
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold tw-text-primary"><?php echo e(count($proposals)); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_total_proposals'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold tw-text-info"><?php echo e(app_format_money($total_raw, $currency_name)); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_total_pipeline'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold tw-text-success"><?php echo e(app_format_money($total_weighted, $currency_name)); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_weighted_forecast'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <?php $avg_weight = count($proposals) > 0 ? round(($total_weighted / $total_raw) * 100, 1) : 0; ?>
                            <div class="tw-text-3xl tw-font-bold tw-text-warning"><?php echo e($avg_weight); ?>%</div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_avg_weight'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Forecast Explanation -->
                <div class="alert alert-info">
                    <i class="fa fa-info-circle tw-mr-2"></i>
                    <strong><?php echo _l('disowebs_ops_weighted_forecast_info'); ?></strong>
                    <p class="tw-mt-2 tw-mb-0">
                        <?php echo _l('disowebs_ops_weighted_forecast_info_desc'); ?>
                    </p>
                </div>

                <!-- Proposals Table -->
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (empty($proposals)) { ?>
                        <div class="tw-text-center tw-py-10">
                            <div class="tw-text-5xl tw-text-neutral-300 tw-mb-4"><i class="fa fa-chart-line"></i></div>
                            <h5 class="tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_no_forecast_data'); ?></h5>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_forecast_data_desc'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-striped dt-table" data-order-col="6" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo _l('proposal_subject'); ?></th>
                                        <th><?php echo _l('proposal_related'); ?></th>
                                        <th><?php echo _l('proposal_status'); ?></th>
                                        <th><?php echo _l('disowebs_ops_probability'); ?></th>
                                        <th><?php echo _l('proposal_total'); ?></th>
                                        <th><?php echo _l('disowebs_ops_weighted_value'); ?></th>
                                        <th><?php echo _l('proposal_open_till'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($proposals as $proposal) { ?>
                                    <?php 
                                    $status_class = 'default';
                                    $status_name = _l('proposal_status_' . $proposal['status']);
                                    if ($proposal['status'] == 4) { $status_class = 'info'; }
                                    if ($proposal['status'] == 5) { $status_class = 'warning'; }
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo admin_url('proposals/list_proposals/' . $proposal['id']); ?>">
                                                PRO-<?php echo str_pad($proposal['id'], 6, '0', STR_PAD_LEFT); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo admin_url('proposals/list_proposals/' . $proposal['id']); ?>" class="tw-font-medium">
                                                <?php echo e($proposal['subject']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if ($proposal['rel_type'] == 'lead') { ?>
                                            <a href="#" onclick="init_lead(<?php echo e($proposal['rel_id']); ?>); return false;">
                                                <i class="fa fa-user-tie tw-mr-1"></i><?php echo e($proposal['related_name'] ?: '-'); ?>
                                            </a>
                                            <?php } elseif ($proposal['rel_type'] == 'customer') { ?>
                                            <a href="<?php echo admin_url('clients/client/' . $proposal['rel_id']); ?>">
                                                <i class="fa fa-building tw-mr-1"></i><?php echo e($proposal['related_name'] ?: '-'); ?>
                                            </a>
                                            <?php } else { ?>
                                            -
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <span class="label label-<?php echo $status_class; ?>">
                                                <?php echo $status_name; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="tw-font-semibold <?php echo $proposal['probability'] >= 50 ? 'tw-text-success' : 'tw-text-warning'; ?>">
                                                <?php echo e($proposal['probability']); ?>%
                                            </span>
                                        </td>
                                        <td class="tw-font-medium">
                                            <?php echo e(app_format_money($proposal['total'], $currency_name)); ?>
                                        </td>
                                        <td class="tw-font-bold tw-text-success">
                                            <?php echo e(app_format_money($proposal['weighted_value'], $currency_name)); ?>
                                        </td>
                                        <td data-order="<?php echo e($proposal['open_till'] ?: ''); ?>">
                                            <?php if (!empty($proposal['open_till'])) { ?>
                                                <?php 
                                                $days_left = floor((strtotime($proposal['open_till']) - strtotime('today')) / 86400);
                                                $urgency_class = $days_left <= 7 ? 'tw-text-danger' : ($days_left <= 14 ? 'tw-text-warning' : '');
                                                ?>
                                                <span class="<?php echo $urgency_class; ?>">
                                                    <?php echo _d($proposal['open_till']); ?>
                                                    <small class="tw-block"><?php echo $days_left; ?> days left</small>
                                                </span>
                                            <?php } else { ?>
                                            -
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo admin_url('proposals/list_proposals/' . $proposal['id']); ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo _l('view'); ?>">
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
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
