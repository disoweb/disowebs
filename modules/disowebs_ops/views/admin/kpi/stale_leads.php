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
                            <?php echo _l('disowebs_ops_stale_leads_title'); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_stale_leads_desc'); ?></p>
                    </div>
                </div>

                <!-- Urgency Alert - only show if there are stale leads -->
                <?php if (!empty($leads)) { ?>
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle tw-mr-2"></i>
                    <strong><?php echo _l('disowebs_ops_stale_leads_alert'); ?></strong>
                    <p class="tw-mt-2 tw-mb-0">
                        <?php echo _l('disowebs_ops_stale_leads_alert_desc'); ?>
                    </p>
                </div>
                <?php } ?>

                <!-- Stats Summary -->
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 tw-gap-4 tw-mb-6">
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold tw-text-danger"><?php echo e(count($leads)); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_stale_leads'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <?php 
                            $max_days = 0;
                            foreach ($leads as $lead) {
                                if ($lead['days_since_contact'] > $max_days) {
                                    $max_days = $lead['days_since_contact'];
                                }
                            }
                            ?>
                            <div class="tw-text-3xl tw-font-bold tw-text-warning"><?php echo e($max_days); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_max_days_stale'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <?php 
                            $avg_days = count($leads) > 0 ? round(array_sum(array_column($leads, 'days_since_contact')) / count($leads), 1) : 0;
                            ?>
                            <div class="tw-text-3xl tw-font-bold tw-text-info"><?php echo e($avg_days); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_avg_days_stale'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Leads Table -->
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (empty($leads)) { ?>
                        <div class="tw-text-center tw-py-10">
                            <div class="tw-text-5xl tw-text-success tw-mb-4"><i class="fa fa-check-circle"></i></div>
                            <h5 class="tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_no_stale_leads'); ?></h5>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_stale_leads_desc'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-striped dt-table" data-order-col="5" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('lead_name'); ?></th>
                                        <th><?php echo _l('lead_company'); ?></th>
                                        <th><?php echo _l('lead_status'); ?></th>
                                        <th><?php echo _l('lead_source'); ?></th>
                                        <th><?php echo _l('disowebs_ops_last_contact'); ?></th>
                                        <th><?php echo _l('disowebs_ops_days_stale'); ?></th>
                                        <th><?php echo _l('lead_assigned'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leads as $lead) { ?>
                                    <?php 
                                    $urgency_class = '';
                                    if ($lead['days_since_contact'] >= 30) {
                                        $urgency_class = 'tw-bg-red-50';
                                    } elseif ($lead['days_since_contact'] >= 14) {
                                        $urgency_class = 'tw-bg-orange-50';
                                    }
                                    ?>
                                    <tr class="<?php echo $urgency_class; ?>">
                                        <td>
                                            <a href="#" onclick="init_lead(<?php echo e($lead['id']); ?>); return false;" class="tw-font-medium">
                                                <?php echo e($lead['name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo e($lead['company'] ?: '-'); ?></td>
                                        <td>
                                            <span class="label label-default" style="background-color: <?php echo e($lead['status_color'] ?: '#888'); ?>">
                                                <?php echo e($lead['status_name'] ?: '-'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo e($lead['source_name'] ?: '-'); ?></td>
                                        <td data-order="<?php echo e($lead['lastcontact'] ?: '1970-01-01'); ?>">
                                            <?php if (!empty($lead['lastcontact'])) { ?>
                                                <?php echo _d($lead['lastcontact']); ?>
                                            <?php } else { ?>
                                                <span class="tw-text-danger">Never</span>
                                            <?php } ?>
                                        </td>
                                        <td data-order="<?php echo e($lead['days_since_contact']); ?>">
                                            <span class="tw-font-bold <?php echo $lead['days_since_contact'] >= 14 ? 'tw-text-danger' : 'tw-text-warning'; ?>">
                                                <?php echo e($lead['days_since_contact']); ?> days
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($lead['assigned_name'])) { ?>
                                            <a href="<?php echo admin_url('profile/' . $lead['assigned']); ?>">
                                                <?php echo e($lead['assigned_name']); ?>
                                            </a>
                                            <?php } else { ?>
                                            <span class="tw-text-warning">Unassigned</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="#" onclick="init_lead(<?php echo e($lead['id']); ?>); return false;" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo _l('view'); ?>">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if (!empty($lead['phonenumber'])) { ?>
                                                <a href="tel:<?php echo e($lead['phonenumber']); ?>" class="btn btn-success btn-xs" data-toggle="tooltip" title="<?php echo _l('lead_phonenumber'); ?>">
                                                    <i class="fa fa-phone"></i>
                                                </a>
                                                <?php } ?>
                                                <?php if (!empty($lead['email'])) { ?>
                                                <a href="mailto:<?php echo e($lead['email']); ?>" class="btn btn-info btn-xs" data-toggle="tooltip" title="<?php echo _l('lead_email'); ?>">
                                                    <i class="fa fa-envelope"></i>
                                                </a>
                                                <?php } ?>
                                            </div>
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
