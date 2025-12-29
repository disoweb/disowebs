<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('disowebs_ops/dw_dashboard_model');
$stats = $this->dw_dashboard_model->get_retention_stats();

$currency = function_exists('get_base_currency') ? get_base_currency() : null;
$currency_name = $currency && isset($currency->name) ? $currency->name : '';
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_crm_retention_v2'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_retention_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_retention_title'); ?></h4>
            </div>
            
            <!-- Retention Overview -->
            <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-3 tw-mt-4">
                <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_retainer_eligible'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800">
                        <?php echo e($stats['eligible_for_retainer'] ?? 0); ?>
                    </div>
                </div>
                <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_retainer_offered'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-info-600">
                        <?php echo e($stats['retainers_offered'] ?? 0); ?>
                    </div>
                </div>
                <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_retainer_accepted'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-success-600">
                        <?php echo e($stats['retainers_accepted'] ?? 0); ?>
                    </div>
                </div>
                <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_retainer_conversion'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800">
                        <?php echo e(number_format($stats['conversion_rate'] ?? 0, 1)); ?>%
                    </div>
                </div>
            </div>

            <!-- Monthly Retainer Value -->
            <div class="tw-mt-4 tw-border-t tw-border-neutral-200 tw-pt-4">
                <div class="tw-flex tw-items-center tw-justify-between">
                    <span class="tw-text-sm tw-font-medium tw-text-neutral-700"><?php echo _l('disowebs_ops_dashboard_monthly_retainer_value'); ?></span>
                    <span class="tw-text-xl tw-font-bold tw-text-success-600">
                        <?php echo app_format_money($stats['monthly_retainer_value'] ?? 0, $currency_name); ?>
                    </span>
                </div>
            </div>

            <!-- Eligible Projects List -->
            <?php if (!empty($stats['eligible_projects'])) { ?>
            <div class="tw-mt-4 tw-border-t tw-border-neutral-200 tw-pt-4">
                <span class="tw-text-sm tw-font-medium tw-text-neutral-700 tw-mb-2 tw-block"><?php echo _l('disowebs_ops_dashboard_projects_needing_retainer'); ?></span>
                <div class="tw-space-y-2">
                    <?php foreach (array_slice($stats['eligible_projects'], 0, 5) as $project) { ?>
                    <div class="tw-flex tw-items-center tw-justify-between tw-p-2 tw-rounded tw-bg-neutral-50">
                        <div>
                            <a href="<?php echo admin_url('projects/view/' . $project['id'] . '?group=disowebs_ops_gates'); ?>" class="tw-font-medium tw-text-neutral-800">
                                <?php echo e($project['name']); ?>
                            </a>
                            <p class="tw-text-xs tw-text-neutral-500 tw-mb-0">
                                <?php echo sprintf(_l('disowebs_ops_days_since_launch'), $project['days_since_launch']); ?>
                            </p>
                        </div>
                        <a href="<?php echo admin_url('disowebs_ops/disowebs_ops_projects/create_retainer_offer/' . $project['id']); ?>" 
                           class="btn btn-xs btn-primary"
                           onclick="return confirm('<?php echo _l('disowebs_ops_confirm_create_retainer'); ?>');">
                            <?php echo _l('disowebs_ops_offer_retainer'); ?>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
