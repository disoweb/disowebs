<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('disowebs_ops/dw_dashboard_model');
$stats = $this->dw_dashboard_model->get_risk_alerts_stats();
$has_risk = ($stats['overdue_milestones'] ?? 0) > 0 || ($stats['overdue_invoices'] ?? 0) > 0 || ($stats['projects_without_scope'] ?? 0) > 0;
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_ceo_risk_alerts'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_risk_alerts_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_risk_alerts_title'); ?></h4>
                <?php if ($has_risk) { ?>
                <span class="label label-danger"><?php echo _l('disowebs_ops_dashboard_risk_active'); ?></span>
                <?php } else { ?>
                <span class="label label-success"><?php echo _l('disowebs_ops_dashboard_risk_clear'); ?></span>
                <?php } ?>
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4 tw-mt-4">
                <a href="<?php echo admin_url('disowebs_ops/milestones_delivered?filter=overdue'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_overdue_milestones'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e($stats['overdue_milestones']); ?></div>
                </a>
                <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_overdue_invoices'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e($stats['overdue_invoices']); ?></div>
                </a>
                <a href="<?php echo admin_url('projects?status=2'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_projects_without_scope'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e($stats['projects_without_scope']); ?></div>
                </a>
            </div>
        </div>
    </div>
</div>
