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
                            <?php echo _l('disowebs_ops_qualified_leads_title'); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_qualified_leads_desc'); ?></p>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold tw-text-primary"><?php echo e($stats['qualified_leads']); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_total_qualified'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold tw-text-info"><?php echo e($stats['proposals_sent']); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_proposals_sent'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <?php 
                            $conversion_rate = $stats['qualified_leads'] > 0 
                                ? round(($stats['proposals_sent'] / $stats['qualified_leads']) * 100, 1) 
                                : 0;
                            ?>
                            <div class="tw-text-3xl tw-font-bold tw-text-success"><?php echo e($conversion_rate); ?>%</div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_conversion_rate'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold tw-text-warning"><?php echo count($leads); ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_leads_shown'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Leads Table -->
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (empty($leads)) { ?>
                        <div class="tw-text-center tw-py-10">
                            <div class="tw-text-5xl tw-text-neutral-300 tw-mb-4"><i class="fa fa-users"></i></div>
                            <h5 class="tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_no_qualified_leads'); ?></h5>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_qualified_leads_desc'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-striped dt-table" data-order-col="5" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('lead_name'); ?></th>
                                        <th><?php echo _l('lead_company'); ?></th>
                                        <th><?php echo _l('leads_source'); ?></th>
                                        <th><?php echo _l('lead_status'); ?></th>
                                        <th><?php echo _l('lead_assigned'); ?></th>
                                        <th><?php echo _l('lead_add_date'); ?></th>
                                        <th><?php echo _l('lead_last_contact'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leads as $lead) { ?>
                                    <tr>
                                        <td>
                                            <a href="#" onclick="init_lead(<?php echo e($lead['id']); ?>); return false;" class="tw-font-medium">
                                                <?php echo e($lead['name']); ?>
                                            </a>
                                            <?php if (!empty($lead['email'])) { ?>
                                            <br><small class="tw-text-neutral-500"><?php echo e($lead['email']); ?></small>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo e($lead['company'] ?: '-'); ?></td>
                                        <td><?php echo e($lead['source_name'] ?: '-'); ?></td>
                                        <td>
                                            <?php if (!empty($lead['status_name'])) { ?>
                                            <span class="label" style="background-color: <?php echo e($lead['status_color']); ?>; color: #fff;">
                                                <?php echo e($lead['status_name']); ?>
                                            </span>
                                            <?php } else { ?>
                                            -
                                            <?php } ?>
                                        </td>
                                        <td><?php echo e($lead['assigned_name'] ?: '-'); ?></td>
                                        <td data-order="<?php echo e($lead['dateadded']); ?>">
                                            <?php echo _dt($lead['dateadded']); ?>
                                        </td>
                                        <td data-order="<?php echo e($lead['lastcontact'] ?: ''); ?>">
                                            <?php echo $lead['lastcontact'] ? _dt($lead['lastcontact']) : '-'; ?>
                                        </td>
                                        <td>
                                            <a href="#" onclick="init_lead(<?php echo e($lead['id']); ?>); return false;" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo _l('view'); ?>">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="<?php echo admin_url('proposals/proposal?rel_type=lead&rel_id=' . $lead['id']); ?>" class="btn btn-primary btn-xs" data-toggle="tooltip" title="<?php echo _l('new_proposal'); ?>">
                                                <i class="fa fa-file-alt"></i>
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
