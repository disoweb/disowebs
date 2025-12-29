<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('disowebs_ops/dw_dashboard_model');
$stats = $this->dw_dashboard_model->get_revenue_cashflow_stats();
$revenue = $stats['revenue'] ?? ['total' => 0.0, 'count' => 0];
$outstanding = $stats['outstanding'] ?? ['count' => 0, 'total' => 0.0];
$retainers = $stats['retainers'] ?? ['count' => 0, 'mrr' => 0.0];
$currency = get_base_currency();
$currency_name = $currency ? $currency->name : '';
$month_label = date('F Y');
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_ceo_revenue_cashflow'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_revenue_cashflow_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_revenue_cashflow_title'); ?></h4>
                <span class="label label-default"><?php echo e($month_label); ?></span>
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4 tw-mt-4">
                <a href="<?php echo admin_url('invoices/list_invoices?status=5'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_revenue_month'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e(app_format_money($revenue['total'], $currency_name)); ?></div>
                    <div class="tw-text-xs tw-text-neutral-500"><?php echo e(sprintf(_l('disowebs_ops_dashboard_payments_count'), (int) $revenue['count'])); ?></div>
                </a>
                <a href="<?php echo admin_url('invoices/list_invoices?status=1'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_outstanding_invoices'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e(app_format_money($outstanding['total'], $currency_name)); ?></div>
                    <div class="tw-text-xs tw-text-neutral-500"><?php echo e(sprintf(_l('disowebs_ops_dashboard_outstanding_count'), (int) $outstanding['count'])); ?></div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/retainers'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_active_retainers'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e((int) $retainers['count']); ?></div>
                    <div class="tw-text-xs tw-text-neutral-500"><?php echo e(sprintf(_l('disowebs_ops_dashboard_retainer_mrr'), app_format_money($retainers['mrr'], $currency_name))); ?></div>
                </a>
            </div>
        </div>
    </div>
</div>
