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
                            <i class="fa fa-user-plus tw-mr-2" style="color: #8b5cf6;"></i>
                            <?php echo e($title); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_referrals_desc'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('disowebs_ops'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left tw-mr-1"></i> <?php echo _l('back'); ?>
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: #6366f1;"><?php echo $stats['total']; ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_total_referrals'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: #16a34a;"><?php echo $stats['converted']; ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_converted'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: #f59e0b;"><?php echo $stats['pending']; ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_pending'); ?></div>
                        </div>
                    </div>
                    <div class="panel_s tw-mb-0">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: #8b5cf6;">
                                <?php echo $stats['total'] > 0 ? round(($stats['converted'] / $stats['total']) * 100) : 0; ?>%
                            </div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_conversion_rate'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Referrals List -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-4">
                            <i class="fa fa-list tw-mr-2"></i>
                            <?php echo _l('disowebs_ops_all_referrals'); ?>
                        </h5>
                        
                        <?php if (empty($referrals)) { ?>
                        <div class="tw-text-center tw-py-8">
                            <i class="fa fa-user-plus tw-text-4xl tw-mb-3 tw-text-neutral-300"></i>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_referrals_found'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="4" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('disowebs_ops_referred_by'); ?></th>
                                        <th><?php echo _l('disowebs_ops_referral_name'); ?></th>
                                        <th><?php echo _l('disowebs_ops_referral_contact'); ?></th>
                                        <th><?php echo _l('status'); ?></th>
                                        <th><?php echo _l('disowebs_ops_date'); ?></th>
                                        <th><?php echo _l('project'); ?></th>
                                        <th class="tw-text-center"><?php echo _l('disowebs_ops_action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($referrals as $referral) { ?>
                                    <tr>
                                        <td>
                                            <div class="tw-flex tw-items-center tw-gap-2">
                                                <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-gradient-to-br tw-from-purple-400 tw-to-indigo-500 tw-flex tw-items-center tw-justify-center tw-text-white tw-font-bold tw-text-xs">
                                                    <?php echo strtoupper(substr($referral['client_name'] ?? 'C', 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="tw-font-medium"><?php echo e($referral['client_name'] ?? '-'); ?></div>
                                                    <div class="tw-text-xs tw-text-neutral-500">
                                                        <a href="<?php echo admin_url('projects/view/' . $referral['project_id']); ?>">
                                                            <?php echo e($referral['project_name']); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="tw-font-medium"><?php echo e($referral['referral_name']); ?></td>
                                        <td>
                                            <?php if (!empty($referral['referral_email'])) { ?>
                                            <a href="mailto:<?php echo e($referral['referral_email']); ?>" class="tw-text-blue-600">
                                                <i class="fa fa-envelope tw-mr-1"></i><?php echo e($referral['referral_email']); ?>
                                            </a>
                                            <?php } ?>
                                            <?php if (!empty($referral['referral_phone'])) { ?>
                                            <br><a href="tel:<?php echo e($referral['referral_phone']); ?>" class="tw-text-neutral-600">
                                                <i class="fa fa-phone tw-mr-1"></i><?php echo e($referral['referral_phone']); ?>
                                            </a>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($referral['converted'] == 1) { ?>
                                            <span class="label" style="background: #16a34a;"><?php echo _l('disowebs_ops_converted'); ?></span>
                                            <?php } else { ?>
                                            <span class="label" style="background: #f59e0b;"><?php echo _l('disowebs_ops_pending'); ?></span>
                                            <?php } ?>
                                        </td>
                                        <td data-order="<?php echo strtotime($referral['created_at']); ?>">
                                            <?php echo _dt($referral['created_at']); ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($referral['converted_project_id'])) { ?>
                                            <a href="<?php echo admin_url('projects/view/' . $referral['converted_project_id']); ?>" class="tw-text-green-600">
                                                <i class="fa fa-check-circle tw-mr-1"></i><?php echo _l('disowebs_ops_view_project'); ?>
                                            </a>
                                            <?php } else { ?>
                                            <span class="tw-text-neutral-400">-</span>
                                            <?php } ?>
                                        </td>
                                        <td class="tw-text-center">
                                            <a href="<?php echo admin_url('projects/view/' . $referral['project_id'] . '?group=disowebs_ops_gates'); ?>" class="btn btn-default btn-xs" title="<?php echo _l('disowebs_ops_view_source'); ?>">
                                                <i class="fa fa-external-link-alt"></i>
                                            </a>
                                            <?php if ($referral['converted'] != 1) { ?>
                                            <a href="#" class="btn btn-success btn-xs" title="<?php echo _l('disowebs_ops_mark_converted'); ?>" onclick="alert('Feature coming soon'); return false;">
                                                <i class="fa fa-check"></i>
                                            </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Top Referrers -->
                <?php if (!empty($top_referrers)) { ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-4">
                            <i class="fa fa-trophy tw-mr-2" style="color: #f59e0b;"></i>
                            <?php echo _l('disowebs_ops_top_referrers'); ?>
                        </h5>
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4">
                            <?php 
                            $medals = ['🥇', '🥈', '🥉'];
                            $i = 0;
                            foreach ($top_referrers as $referrer) { 
                            ?>
                            <div class="tw-border tw-rounded-lg tw-p-4 tw-text-center">
                                <div class="tw-text-3xl tw-mb-2"><?php echo $medals[$i] ?? '⭐'; ?></div>
                                <div class="tw-font-semibold tw-text-neutral-800"><?php echo e($referrer['client_name']); ?></div>
                                <div class="tw-text-2xl tw-font-bold" style="color: #8b5cf6;"><?php echo $referrer['referral_count']; ?></div>
                                <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_referrals'); ?></div>
                            </div>
                            <?php $i++; } ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
