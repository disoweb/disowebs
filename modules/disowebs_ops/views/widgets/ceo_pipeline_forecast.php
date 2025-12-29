<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('disowebs_ops/dw_dashboard_model');
$stats = $this->dw_dashboard_model->get_pipeline_forecast_stats();
$currency = get_base_currency();
$currency_name = $currency ? $currency->name : '';
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_ceo_pipeline_forecast'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_pipeline_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_pipeline_title'); ?></h4>
            </div>
            <dl class="tw-grid tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-2 tw-mt-4 tw-mb-0">
                <a href="<?php echo admin_url('disowebs_ops/qualified_leads'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_qualified_leads'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-neutral-600"><?php echo e($stats['qualified_leads']); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/proposals_sent'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_proposals_sent'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-neutral-600"><?php echo e($stats['proposals_sent']); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/pipeline_forecast/30'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_forecast_30'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-neutral-600"><?php echo e(app_format_money($stats['forecast_30'], $currency_name)); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/pipeline_forecast/60'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_forecast_60'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-neutral-600"><?php echo e(app_format_money($stats['forecast_60'], $currency_name)); ?></dd>
                    </div>
                </a>
            </dl>
        </div>
    </div>
</div>
