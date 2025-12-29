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
                            <?php echo _l('disowebs_ops_stale_proposals_title'); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_stale_proposals_desc'); ?></p>
                    </div>
                </div>

                <?php 
                $currency = get_base_currency();
                $currency_name = $currency ? $currency->name : '';
                $total_value = array_sum(array_column($proposals, 'total'));
                ?>

                <!-- Urgency Alert - only show if there are stale proposals -->
                <?php if (!empty($proposals)) { ?>
                <div class="alert alert-warning">
                    <i class="fa fa-hourglass-half tw-mr-2"></i>
                    <strong><?php echo _l('disowebs_ops_stale_proposals_alert'); ?></strong>
                    <p class="tw-mt-2 tw-mb-0">
                        <?php echo _l('disowebs_ops_stale_proposals_alert_desc'); ?>
                    </p>
                </div>
                <?php } ?>

                <!-- Stats Summary -->
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold tw-text-warning"><?php echo e(count($proposals)); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_stale_proposals'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold tw-text-danger"><?php echo e(app_format_money($total_value, $currency_name)); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_value_at_risk'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <?php 
                            $max_days = 0;
                            foreach ($proposals as $p) {
                                if ($p['days_pending'] > $max_days) {
                                    $max_days = $p['days_pending'];
                                }
                            }
                            ?>
                            <div class="tw-text-3xl tw-font-bold tw-text-info"><?php echo e($max_days); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_max_days_pending'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <?php 
                            $avg_days = count($proposals) > 0 ? round(array_sum(array_column($proposals, 'days_pending')) / count($proposals), 1) : 0;
                            ?>
                            <div class="tw-text-3xl tw-font-bold tw-text-primary"><?php echo e($avg_days); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_avg_days_pending'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Proposals Table -->
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (empty($proposals)) { ?>
                        <div class="tw-text-center tw-py-10">
                            <div class="tw-text-5xl tw-text-success tw-mb-4"><i class="fa fa-check-circle"></i></div>
                            <h5 class="tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_no_stale_proposals'); ?></h5>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_stale_proposals_desc'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-striped dt-table" data-order-col="5" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo _l('proposal_subject'); ?></th>
                                        <th><?php echo _l('proposal_related'); ?></th>
                                        <th><?php echo _l('proposal_status'); ?></th>
                                        <th><?php echo _l('proposal_date'); ?></th>
                                        <th><?php echo _l('disowebs_ops_days_pending'); ?></th>
                                        <th><?php echo _l('proposal_total'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($proposals as $proposal) { ?>
                                    <?php 
                                    $urgency_class = '';
                                    if ($proposal['days_pending'] >= 30) {
                                        $urgency_class = 'tw-bg-red-50';
                                    } elseif ($proposal['days_pending'] >= 14) {
                                        $urgency_class = 'tw-bg-orange-50';
                                    }
                                    
                                    $status_class = 'default';
                                    $status_name = _l('proposal_status_' . $proposal['status']);
                                    if ($proposal['status'] == 4) { $status_class = 'info'; }
                                    if ($proposal['status'] == 5) { $status_class = 'warning'; }
                                    ?>
                                    <tr class="<?php echo $urgency_class; ?>">
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
                                        <td data-order="<?php echo e($proposal['date']); ?>">
                                            <?php echo _d($proposal['date']); ?>
                                        </td>
                                        <td data-order="<?php echo e($proposal['days_pending']); ?>">
                                            <span class="tw-font-bold <?php echo $proposal['days_pending'] >= 14 ? 'tw-text-danger' : 'tw-text-warning'; ?>">
                                                <?php echo e($proposal['days_pending']); ?> days
                                            </span>
                                        </td>
                                        <td class="tw-font-semibold">
                                            <?php echo e(app_format_money($proposal['total'], $currency_name)); ?>
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
