<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
$pipeline = isset($pipeline_stats) ? $pipeline_stats : [];
$hygiene = isset($pipeline_hygiene) ? $pipeline_hygiene : [];
$crm = isset($crm_stats) ? $crm_stats : [];
$active = isset($active_projects) ? (int) $active_projects : 0;
$retention = isset($retention_stats) ? $retention_stats : [];
$retainer = isset($retainer_stats) ? $retainer_stats : [];
$growth = isset($growth_stats) ? $growth_stats : [];
$referrals = isset($referral_stats) ? $referral_stats : [];
?>
<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="tw-mb-6">
            <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                <div>
                    <h4 class="tw-text-lg tw-font-semibold tw-text-neutral-800 tw-mb-1">
                        <i class="fa fa-users tw-mr-2 tw-text-primary"></i>
                        <?php echo e($title); ?>
                    </h4>
                    <p class="tw-text-neutral-500 tw-text-sm tw-mb-0"><?php echo _l('disowebs_ops_crm_dashboard_desc'); ?></p>
                </div>
                <div class="tw-mt-3 md:tw-mt-0 tw-flex tw-gap-2">
                    <a href="<?php echo admin_url('disowebs_ops/ceo_dashboard'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-tachometer-alt tw-mr-1"></i> <?php echo _l('disowebs_ops_ceo_dashboard'); ?>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/tsa_dashboard'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-headset tw-mr-1"></i> <?php echo _l('disowebs_ops_tsa_dashboard'); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Row 1: Priority Actions -->
        <div class="panel_s">
            <div class="panel-body">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-0">
                        <i class="fa fa-fire tw-mr-2 tw-text-danger"></i>
                        <?php echo _l('disowebs_ops_dashboard_crm_priorities_title'); ?>
                    </h5>
                    <?php 
                    $total_actions = ($crm['hot_leads'] ?? 0) + ($crm['followups_today'] ?? 0) + ($crm['proposals_to_send'] ?? 0);
                    ?>
                    <span class="label label-<?php echo $total_actions > 0 ? 'warning' : 'success'; ?> tw-text-sm">
                        <?php echo sprintf(_l('disowebs_ops_actions_pending'), $total_actions); ?>
                    </span>
                </div>
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4">
                    <a href="<?php echo admin_url('leads?status=1'); ?>" class="tw-rounded-lg tw-border-2 tw-border-danger tw-bg-danger/5 tw-p-4 hover:tw-bg-danger/10 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <i class="fa fa-fire-alt tw-text-danger"></i>
                            <span class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-danger"><?php echo _l('disowebs_ops_dashboard_hot_leads'); ?></span>
                        </div>
                        <div class="tw-text-3xl tw-font-bold tw-text-danger tw-mt-2"><?php echo e($crm['hot_leads'] ?? 0); ?></div>
                        <div class="tw-text-xs tw-text-neutral-500 tw-mt-1"><?php echo _l('disowebs_ops_dashboard_require_action'); ?></div>
                    </a>
                    <a href="<?php echo admin_url('utilities/calendar'); ?>" class="tw-rounded-lg tw-border-2 tw-border-warning tw-bg-warning/5 tw-p-4 hover:tw-bg-warning/10 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <i class="fa fa-clock tw-text-warning"></i>
                            <span class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-warning"><?php echo _l('disowebs_ops_dashboard_followups_today'); ?></span>
                        </div>
                        <div class="tw-text-3xl tw-font-bold tw-text-warning tw-mt-2"><?php echo e($crm['followups_today'] ?? 0); ?></div>
                        <div class="tw-text-xs tw-text-neutral-500 tw-mt-1"><?php echo _l('disowebs_ops_dashboard_scheduled_today'); ?></div>
                    </a>
                    <a href="<?php echo admin_url('proposals'); ?>" class="tw-rounded-lg tw-border-2 tw-border-info tw-bg-info/5 tw-p-4 hover:tw-bg-info/10 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <i class="fa fa-file-alt tw-text-info"></i>
                            <span class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-info"><?php echo _l('disowebs_ops_dashboard_proposals_to_send'); ?></span>
                        </div>
                        <div class="tw-text-3xl tw-font-bold tw-text-info tw-mt-2"><?php echo e($crm['proposals_to_send'] ?? 0); ?></div>
                        <div class="tw-text-xs tw-text-neutral-500 tw-mt-1"><?php echo _l('disowebs_ops_dashboard_awaiting_creation'); ?></div>
                    </a>
                    <a href="<?php echo admin_url('projects'); ?>" class="tw-rounded-lg tw-border-2 tw-border-primary tw-bg-primary/5 tw-p-4 hover:tw-bg-primary/10 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <i class="fa fa-project-diagram tw-text-primary"></i>
                            <span class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-primary"><?php echo _l('disowebs_ops_active_projects'); ?></span>
                        </div>
                        <div class="tw-text-3xl tw-font-bold tw-text-primary tw-mt-2"><?php echo e($active); ?></div>
                        <div class="tw-text-xs tw-text-neutral-500 tw-mt-1"><?php echo _l('disowebs_ops_dashboard_currently_running'); ?></div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Row 2: Pipeline Forecast -->
        <div class="panel_s">
            <div class="panel-body">
                <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-4">
                    <i class="fa fa-chart-line tw-mr-2 tw-text-info"></i>
                    <?php echo _l('disowebs_ops_dashboard_pipeline_title'); ?>
                </h5>
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4">
                    <a href="<?php echo admin_url('disowebs_ops/qualified_leads'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_qualified_leads'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($pipeline['qualified_leads'] ?? 0); ?></div>
                        <div class="tw-text-xs tw-text-neutral-400 tw-mt-1"><?php echo _l('disowebs_ops_dashboard_ready_to_convert'); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/proposals_sent'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_proposals_sent'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($pipeline['proposals_sent'] ?? 0); ?></div>
                        <div class="tw-text-xs tw-text-neutral-400 tw-mt-1"><?php echo _l('disowebs_ops_dashboard_pending_response'); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/pipeline_forecast/30'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_forecast_30'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-success tw-mt-1"><?php echo e(app_format_money($pipeline['forecast_30'] ?? 0, $currency_name)); ?></div>
                        <div class="tw-text-xs tw-text-neutral-400 tw-mt-1"><?php echo _l('disowebs_ops_dashboard_expected_close'); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/pipeline_forecast/60'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_forecast_60'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-info tw-mt-1"><?php echo e(app_format_money($pipeline['forecast_60'] ?? 0, $currency_name)); ?></div>
                        <div class="tw-text-xs tw-text-neutral-400 tw-mt-1"><?php echo _l('disowebs_ops_dashboard_expected_close'); ?></div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Row 3: Pipeline Hygiene & Retention -->
        <div class="row">
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                            <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-0">
                                <i class="fa fa-broom tw-mr-2 tw-text-warning"></i>
                                <?php echo _l('disowebs_ops_dashboard_pipeline_hygiene_title'); ?>
                            </h5>
                            <?php 
                            $hygiene_total = ($hygiene['followups_overdue'] ?? 0) + ($hygiene['stale_leads'] ?? 0) + ($hygiene['proposals_pending'] ?? 0);
                            ?>
                            <span class="label label-<?php echo $hygiene_total > 3 ? 'danger' : ($hygiene_total > 0 ? 'warning' : 'success'); ?>">
                                <?php echo $hygiene_total > 0 ? sprintf(_l('disowebs_ops_items_need_attention'), $hygiene_total) : _l('disowebs_ops_all_clear'); ?>
                            </span>
                        </div>
                        <div class="tw-space-y-3">
                            <a href="<?php echo admin_url('disowebs_ops/followups_overdue'); ?>" class="tw-flex tw-items-center tw-justify-between tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-danger/10 tw-flex tw-items-center tw-justify-center">
                                        <i class="fa fa-bell tw-text-danger"></i>
                                    </div>
                                    <div>
                                        <div class="tw-text-sm tw-font-medium tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_followups_overdue'); ?></div>
                                        <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_requires_immediate_action'); ?></div>
                                    </div>
                                </div>
                                <div class="tw-text-2xl tw-font-bold <?php echo ($hygiene['followups_overdue'] ?? 0) > 0 ? 'tw-text-danger' : 'tw-text-success'; ?>">
                                    <?php echo e($hygiene['followups_overdue'] ?? 0); ?>
                                </div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/stale_leads'); ?>" class="tw-flex tw-items-center tw-justify-between tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-warning/10 tw-flex tw-items-center tw-justify-center">
                                        <i class="fa fa-user-clock tw-text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="tw-text-sm tw-font-medium tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_stale_leads'); ?></div>
                                        <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_no_contact_14_days'); ?></div>
                                    </div>
                                </div>
                                <div class="tw-text-2xl tw-font-bold <?php echo ($hygiene['stale_leads'] ?? 0) > 0 ? 'tw-text-warning' : 'tw-text-success'; ?>">
                                    <?php echo e($hygiene['stale_leads'] ?? 0); ?>
                                </div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/stale_proposals'); ?>" class="tw-flex tw-items-center tw-justify-between tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-info/10 tw-flex tw-items-center tw-justify-center">
                                        <i class="fa fa-file-invoice tw-text-info"></i>
                                    </div>
                                    <div>
                                        <div class="tw-text-sm tw-font-medium tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_proposals_stale'); ?></div>
                                        <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_pending_7_days'); ?></div>
                                    </div>
                                </div>
                                <div class="tw-text-2xl tw-font-bold <?php echo ($hygiene['proposals_pending'] ?? 0) > 0 ? 'tw-text-info' : 'tw-text-success'; ?>">
                                    <?php echo e($hygiene['proposals_pending'] ?? 0); ?>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-4">
                            <i class="fa fa-handshake tw-mr-2 tw-text-success"></i>
                            <?php echo _l('disowebs_ops_dashboard_retention_title'); ?>
                        </h5>
                        <div class="tw-grid tw-grid-cols-2 tw-gap-3 tw-mb-3">
                            <a href="<?php echo admin_url('clients'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_repeat_clients'); ?></div>
                                <div class="tw-text-2xl tw-font-bold tw-text-neutral-800"><?php echo e($retention['repeat_clients'] ?? 0); ?></div>
                            </a>
                            <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_retention_rate'); ?></div>
                                <div class="tw-text-2xl tw-font-bold tw-text-success"><?php echo e($retention['retention_rate'] ?? 0); ?>%</div>
                            </div>
                        </div>
                        <div class="tw-rounded-lg tw-bg-neutral-100 tw-p-4">
                            <h6 class="tw-text-sm tw-font-semibold tw-text-neutral-700 tw-mb-3">
                                <i class="fa fa-sync tw-mr-1"></i> <?php echo _l('disowebs_ops_retainer_clients'); ?>
                            </h6>
                            <div class="tw-grid tw-grid-cols-2 tw-gap-3">
                                <a href="<?php echo admin_url('clients?groups=retainer'); ?>" class="tw-rounded-lg tw-bg-white tw-p-3 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                    <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_active_retainers'); ?></div>
                                    <div class="tw-text-xl tw-font-bold tw-text-primary"><?php echo e($retainer['active'] ?? 0); ?></div>
                                </a>
                                <div class="tw-rounded-lg tw-bg-white tw-p-3">
                                    <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_mrr'); ?></div>
                                    <div class="tw-text-xl tw-font-bold tw-text-success"><?php echo e(app_format_money($retainer['mrr'] ?? 0, $currency_name)); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 4: Growth Metrics -->
        <div class="panel_s">
            <div class="panel-body">
                <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-4">
                    <i class="fa fa-seedling tw-mr-2 tw-text-success"></i>
                    <?php echo _l('disowebs_ops_dashboard_growth_title'); ?>
                </h5>
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4">
                    <a href="<?php echo admin_url('leads'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_new_leads_month'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($growth['new_leads'] ?? 0); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/testimonials/all'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_testimonials'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($growth['testimonials_received'] ?? 0); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/referrals'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_referrals'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($referrals['total'] ?? 0); ?></div>
                    </a>
                    <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_conversion_rate'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-success tw-mt-1"><?php echo e($pipeline['conversion_rate'] ?? 0); ?>%</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php init_tail(); ?>
