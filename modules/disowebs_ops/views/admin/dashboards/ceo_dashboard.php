<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
$pipeline = isset($pipeline_stats) ? $pipeline_stats : [];
$hygiene = isset($pipeline_hygiene) ? $pipeline_hygiene : [];
$delivery = isset($delivery_stats) ? $delivery_stats : [];
$revenue = isset($revenue_stats) ? $revenue_stats : [];
$risk = isset($risk_stats) ? $risk_stats : [];
$growth = isset($growth_stats) ? $growth_stats : [];
$referrals = isset($referral_stats) ? $referral_stats : [];
$execution = isset($execution_stats) ? $execution_stats : [];
$alerts = isset($margin_alerts) ? $margin_alerts : [];
$active = isset($active_projects) ? (int) $active_projects : 0;
$limit = isset($project_limit) ? (int) $project_limit : 4;
$capacity_pct = $limit > 0 ? min(100, round(($active / $limit) * 100)) : 0;
?>
<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="tw-mb-6">
            <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                <div>
                    <h4 class="tw-text-lg tw-font-semibold tw-text-neutral-800 tw-mb-1">
                        <i class="fa fa-tachometer-alt tw-mr-2 tw-text-primary"></i>
                        <?php echo e($title); ?>
                    </h4>
                    <p class="tw-text-neutral-500 tw-text-sm tw-mb-0"><?php echo _l('disowebs_ops_ceo_dashboard_desc'); ?></p>
                </div>
                <div class="tw-mt-3 md:tw-mt-0 tw-flex tw-gap-2">
                    <a href="<?php echo admin_url('disowebs_ops/crm_dashboard'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-users tw-mr-1"></i> <?php echo _l('disowebs_ops_crm_dashboard'); ?>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/tsa_dashboard'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-headset tw-mr-1"></i> <?php echo _l('disowebs_ops_tsa_dashboard'); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Banner if any margin alerts -->
        <?php if (!empty($alerts)) { ?>
        <div class="alert alert-danger tw-mb-6">
            <div class="tw-flex tw-items-center tw-gap-3">
                <i class="fa fa-exclamation-circle tw-text-xl"></i>
                <div>
                    <strong><?php echo sprintf(_l('disowebs_ops_dashboard_alerts_count'), count($alerts)); ?></strong>
                    <span class="tw-text-sm tw-ml-2"><?php echo _l('disowebs_ops_margin_alerts_need_attention'); ?></span>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- Row 1: Pipeline Forecast -->
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
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/proposals_sent'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_proposals_sent'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($pipeline['proposals_sent'] ?? 0); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/pipeline_forecast/30'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_forecast_30'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-success tw-mt-1"><?php echo e(app_format_money($pipeline['forecast_30'] ?? 0, $currency_name)); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/pipeline_forecast/60'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_forecast_60'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-info tw-mt-1"><?php echo e(app_format_money($pipeline['forecast_60'] ?? 0, $currency_name)); ?></div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Row 2: Delivery Engine & Revenue -->
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-4">
                            <i class="fa fa-cogs tw-mr-2 tw-text-warning"></i>
                            <?php echo _l('disowebs_ops_dashboard_delivery_v2_title'); ?>
                        </h5>
                        <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-3 tw-mb-4">
                            <a href="<?php echo admin_url('disowebs_ops/deposit_pending'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_deposit_pending'); ?></div>
                                <div class="tw-text-xl tw-font-bold <?php echo ($delivery['deposit_pending'] ?? 0) > 0 ? 'tw-text-warning' : 'tw-text-success'; ?>"><?php echo e($delivery['deposit_pending'] ?? 0); ?></div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/final_pending'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_final_payment_pending'); ?></div>
                                <div class="tw-text-xl tw-font-bold <?php echo ($delivery['final_pending'] ?? 0) > 0 ? 'tw-text-warning' : 'tw-text-success'; ?>"><?php echo e($delivery['final_pending'] ?? 0); ?></div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/training_pending'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_training_pending'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-neutral-800"><?php echo e($delivery['training_pending'] ?? 0); ?></div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/demo_missing'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_demo_missing_this_week'); ?></div>
                                <div class="tw-text-xl tw-font-bold <?php echo ($delivery['demo_missing'] ?? 0) > 0 ? 'tw-text-danger' : 'tw-text-success'; ?>"><?php echo e($delivery['demo_missing'] ?? 0); ?></div>
                            </a>
                        </div>
                        <div class="tw-grid tw-grid-cols-2 tw-gap-3">
                            <a href="<?php echo admin_url('disowebs_ops/blockers/active'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_active_blockers'); ?></div>
                                <div class="tw-text-xl tw-font-bold <?php echo ($delivery['active_blockers'] ?? 0) > 0 ? 'tw-text-danger' : 'tw-text-success'; ?>"><?php echo e($delivery['active_blockers'] ?? 0); ?></div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/blockers/overdue'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_overdue_blockers'); ?></div>
                                <div class="tw-text-xl tw-font-bold <?php echo ($delivery['overdue_blockers'] ?? 0) > 0 ? 'tw-text-danger' : 'tw-text-success'; ?>"><?php echo e($delivery['overdue_blockers'] ?? 0); ?></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-4">
                            <i class="fa fa-dollar-sign tw-mr-2 tw-text-success"></i>
                            <?php echo _l('disowebs_ops_dashboard_revenue_cashflow_title'); ?>
                        </h5>
                        <?php 
                        $rev = $revenue['revenue'] ?? ['total' => 0, 'count' => 0];
                        $out = $revenue['outstanding'] ?? ['total' => 0, 'count' => 0];
                        $ret = $revenue['retainers'] ?? ['count' => 0, 'mrr' => 0];
                        ?>
                        <div class="tw-space-y-3">
                            <a href="<?php echo admin_url('invoices/list_invoices?status=5'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_revenue_month'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-success"><?php echo e(app_format_money($rev['total'], $currency_name)); ?></div>
                                <div class="tw-text-xs tw-text-neutral-400"><?php echo sprintf(_l('disowebs_ops_dashboard_payments_count'), (int) $rev['count']); ?></div>
                            </a>
                            <a href="<?php echo admin_url('invoices/list_invoices?status=1'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_outstanding_invoices'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-warning"><?php echo e(app_format_money($out['total'], $currency_name)); ?></div>
                                <div class="tw-text-xs tw-text-neutral-400"><?php echo sprintf(_l('disowebs_ops_dashboard_outstanding_count'), (int) $out['count']); ?></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Risk & Capacity -->
        <div class="row">
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                            <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-0">
                                <i class="fa fa-exclamation-triangle tw-mr-2 tw-text-danger"></i>
                                <?php echo _l('disowebs_ops_dashboard_risk_alerts_title'); ?>
                            </h5>
                            <?php 
                            $has_risk = ($risk['overdue_milestones'] ?? 0) > 0 || ($risk['overdue_invoices'] ?? 0) > 0;
                            ?>
                            <span class="label label-<?php echo $has_risk ? 'danger' : 'success'; ?>">
                                <?php echo $has_risk ? _l('disowebs_ops_dashboard_risk_active') : _l('disowebs_ops_dashboard_risk_clear'); ?>
                            </span>
                        </div>
                        <div class="tw-grid tw-grid-cols-3 tw-gap-3">
                            <a href="<?php echo admin_url('disowebs_ops/milestones_delivered?filter=overdue'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_overdue_milestones'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-neutral-800"><?php echo e($risk['overdue_milestones'] ?? 0); ?></div>
                            </a>
                            <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_overdue_invoices'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-neutral-800"><?php echo e($risk['overdue_invoices'] ?? 0); ?></div>
                            </a>
                            <a href="<?php echo admin_url('projects?status=2'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_projects_without_scope'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-neutral-800"><?php echo e($risk['projects_without_scope'] ?? 0); ?></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-4">
                            <i class="fa fa-battery-half tw-mr-2 tw-text-primary"></i>
                            <?php echo _l('disowebs_ops_dashboard_capacity_risk_title'); ?>
                        </h5>
                        <?php 
                        $cap_class = $capacity_pct >= 100 ? 'danger' : ($capacity_pct >= 75 ? 'warning' : 'success');
                        $cap_color = $cap_class === 'danger' ? '#dc2626' : ($cap_class === 'warning' ? '#d97706' : '#16a34a');
                        ?>
                        <div class="tw-flex tw-items-center tw-gap-4">
                            <div class="tw-flex-shrink-0">
                                <div class="tw-text-4xl tw-font-bold" style="color: <?php echo $cap_color; ?>;">
                                    <?php echo $active; ?><span class="tw-text-xl tw-text-neutral-400">/<?php echo $limit; ?></span>
                                </div>
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_active_projects'); ?></div>
                            </div>
                            <div class="tw-flex-1">
                                <div class="tw-w-full tw-rounded-full tw-mb-2" style="height: 12px; background-color: #e5e7eb;">
                                    <div class="tw-rounded-full" style="height: 12px; width: <?php echo $capacity_pct; ?>%; background-color: <?php echo $cap_color; ?>;"></div>
                                </div>
                                <div class="tw-text-sm tw-text-neutral-500"><?php echo $capacity_pct; ?>% <?php echo _l('disowebs_ops_capacity_used'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 4: Pipeline Hygiene & Growth -->
        <div class="row">
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-4">
                            <i class="fa fa-broom tw-mr-2 tw-text-info"></i>
                            <?php echo _l('disowebs_ops_dashboard_pipeline_hygiene_title'); ?>
                        </h5>
                        <div class="tw-grid tw-grid-cols-3 tw-gap-3">
                            <a href="<?php echo admin_url('disowebs_ops/followups_overdue'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_followups_overdue'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-neutral-800"><?php echo e($hygiene['followups_overdue'] ?? 0); ?></div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/stale_leads'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_stale_leads'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-neutral-800"><?php echo e($hygiene['stale_leads'] ?? 0); ?></div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/stale_proposals'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_proposals_stale'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-neutral-800"><?php echo e($hygiene['proposals_pending'] ?? 0); ?></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-4">
                            <i class="fa fa-seedling tw-mr-2 tw-text-success"></i>
                            <?php echo _l('disowebs_ops_dashboard_growth_title'); ?>
                        </h5>
                        <div class="tw-grid tw-grid-cols-3 tw-gap-3">
                            <a href="<?php echo admin_url('disowebs_ops/testimonials/all'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_testimonials'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-neutral-800"><?php echo e($growth['testimonials_received'] ?? 0); ?></div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/referrals'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_referrals'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-neutral-800"><?php echo e($referrals['total'] ?? 0); ?></div>
                            </a>
                            <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_conversion_rate'); ?></div>
                                <div class="tw-text-xl tw-font-bold tw-text-success"><?php echo e($referrals['conversion_rate'] ?? 0); ?>%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 5: Weekly Execution -->
        <div class="panel_s">
            <div class="panel-body">
                <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-4">
                    <i class="fa fa-tasks tw-mr-2 tw-text-primary"></i>
                    <?php echo _l('disowebs_ops_dashboard_ceo_title'); ?> - <?php echo _l('disowebs_ops_range_week'); ?>
                </h5>
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4">
                    <a href="<?php echo admin_url('disowebs_ops/milestones_delivered?period=week'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_milestones_delivered'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($execution['milestones_done'] ?? 0); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/demos'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_proof'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($execution['proof_entries'] ?? 0); ?></div>
                    </a>
                    <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_tasks_completed'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($execution['tasks_done'] ?? 0); ?></div>
                    </div>
                    <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_invoices_paid'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-success tw-mt-1"><?php echo e($execution['invoices_paid'] ?? 0); ?></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php init_tail(); ?>
