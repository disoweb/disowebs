<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('disowebs_ops/dw_dashboard_model');
$stats = $this->dw_dashboard_model->get_profit_engine_stats();

$currency = function_exists('get_base_currency') ? get_base_currency() : null;
$currency_name = $currency && isset($currency->name) ? $currency->name : '';

$avg_margin = $stats['avg_margin'] ?? 0;
$margin_class = 'success';
if ($avg_margin < 15) {
    $margin_class = 'danger';
} elseif ($avg_margin < 25) {
    $margin_class = 'warning';
} elseif ($avg_margin < 40) {
    $margin_class = 'info';
}
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_ceo_profit_engine'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_profit_engine_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_profit_engine_title'); ?></h4>
                <span class="label label-<?php echo $margin_class; ?>">
                    <?php echo _l('disowebs_ops_dashboard_avg_margin') . ': ' . number_format($avg_margin, 1) . '%'; ?>
                </span>
            </div>
            
            <!-- Profit Overview Cards - Row 1 -->
            <dl class="tw-grid tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-2 tw-mt-4 tw-mb-0">
                <div class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 tw-transition">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_total_revenue_ytd'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-success-600"><?php echo app_format_money($stats['total_revenue'] ?? 0, $currency_name); ?></dd>
                    </div>
                </div>
                <div class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 tw-transition">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_total_cost_ytd'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-danger-600"><?php echo app_format_money($stats['total_cost'] ?? 0, $currency_name); ?></dd>
                    </div>
                </div>
                <div class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 tw-transition">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_net_profit_ytd'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold <?php echo ($stats['total_profit'] ?? 0) >= 0 ? 'tw-text-success-600' : 'tw-text-danger-600'; ?>"><?php echo app_format_money($stats['total_profit'] ?? 0, $currency_name); ?></dd>
                    </div>
                </div>
                <div class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 tw-transition">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_cr_impact_total'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-info-600"><?php echo app_format_money($stats['cr_impact_total'] ?? 0, $currency_name); ?></dd>
                    </div>
                </div>
            </dl>

            <!-- Alert Cards - Row 2 -->
            <dl class="tw-grid tw-grid-cols-2 tw-gap-2 tw-mt-2 tw-mb-0">
                <div class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 tw-transition">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_alerts_total'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold <?php echo ($stats['alerts_total'] ?? 0) > 0 ? 'tw-text-warning-600' : 'tw-text-success-600'; ?>"><?php echo e($stats['alerts_total'] ?? 0); ?></dd>
                    </div>
                </div>
                <div class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 tw-transition">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_alerts_critical'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold <?php echo ($stats['alerts_critical'] ?? 0) > 0 ? 'tw-text-danger-600' : 'tw-text-success-600'; ?>"><?php echo e($stats['alerts_critical'] ?? 0); ?></dd>
                    </div>
                </div>
            </dl>

            <!-- Top Performing Projects -->
            <?php if (!empty($stats['top_margin_projects'])) { ?>
            <div class="tw-mt-4 tw-border-t tw-border-neutral-200 tw-pt-4">
                <span class="tw-text-sm tw-font-medium tw-text-neutral-700 tw-mb-2 tw-block"><?php echo _l('disowebs_ops_dashboard_top_margin_projects'); ?></span>
                <div class="tw-space-y-2">
                    <?php foreach (array_slice($stats['top_margin_projects'], 0, 3) as $project) { ?>
                    <div class="tw-flex tw-items-center tw-justify-between tw-p-2 tw-rounded tw-bg-success-50">
                        <a href="<?php echo admin_url('projects/view/' . $project['project_id'] . '?group=disowebs_ops_profit'); ?>" class="tw-font-medium tw-text-neutral-800">
                            <?php echo e($project['project_name'] ?? 'Project #' . $project['project_id']); ?>
                        </a>
                        <div class="tw-text-right">
                            <span class="tw-font-semibold tw-text-success-600">
                                <?php echo e(number_format($project['margin_percent'], 1)); ?>%
                            </span>
                            <br>
                            <span class="tw-text-xs tw-text-neutral-500">
                                <?php echo app_format_money($project['net_profit'], $currency_name); ?>
                            </span>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>

            <!-- Lowest Performing Projects -->
            <?php if (!empty($stats['low_margin_projects'])) { ?>
            <div class="tw-mt-4 tw-border-t tw-border-neutral-200 tw-pt-4">
                <span class="tw-text-sm tw-font-medium tw-text-neutral-700 tw-mb-2 tw-block"><?php echo _l('disowebs_ops_dashboard_low_margin_projects'); ?></span>
                <div class="tw-space-y-2">
                    <?php foreach (array_slice($stats['low_margin_projects'], 0, 3) as $project) { ?>
                    <div class="tw-flex tw-items-center tw-justify-between tw-p-2 tw-rounded tw-bg-danger-50">
                        <a href="<?php echo admin_url('projects/view/' . $project['project_id'] . '?group=disowebs_ops_profit'); ?>" class="tw-font-medium tw-text-neutral-800">
                            <?php echo e($project['project_name'] ?? 'Project #' . $project['project_id']); ?>
                        </a>
                        <div class="tw-text-right">
                            <span class="tw-font-semibold tw-text-danger-600">
                                <?php echo e(number_format($project['margin_percent'], 1)); ?>%
                            </span>
                            <br>
                            <span class="tw-text-xs tw-text-neutral-500">
                                <?php echo app_format_money($project['net_profit'], $currency_name); ?>
                            </span>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
