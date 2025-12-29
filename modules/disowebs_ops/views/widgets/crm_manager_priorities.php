<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('disowebs_ops/dw_dashboard_model');
$stats = $this->dw_dashboard_model->get_crm_manager_stats();
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_crm_manager_priorities'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_crm_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_crm_title'); ?></h4>
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4 tw-mt-4">
                <a href="<?php echo admin_url('disowebs_ops/followups_overdue'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_followups_due'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e($stats['followups_due']); ?></div>
                </a>
                <a href="<?php echo admin_url('clients'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_clients_weekly_update'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e($stats['clients_weekly_updates']); ?></div>
                </a>
                <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_invoices_followup'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e($stats['invoices_followup']); ?></div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/change_requests'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_pending_crs'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e($stats['pending_change_requests']); ?></div>
                </a>
            </div>
        </div>
    </div>
</div>
