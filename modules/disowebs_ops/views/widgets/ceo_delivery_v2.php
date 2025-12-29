<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('disowebs_ops/dw_dashboard_model');
$this->load->model('disowebs_ops/dw_margin_alerts_model');

$stats = $this->dw_dashboard_model->get_delivery_engine_stats();
$margin_alerts = $this->dw_margin_alerts_model->get_dashboard_alerts(5);
$has_alerts = !empty($margin_alerts);
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_ceo_delivery_v2'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_delivery_v2_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_delivery_v2_title'); ?></h4>
                <?php if ($has_alerts) { ?>
                <span class="label label-danger"><?php echo sprintf(_l('disowebs_ops_dashboard_alerts_count'), count($margin_alerts)); ?></span>
                <?php } ?>
            </div>
            
            <!-- Gate Status Cards - Row 1 -->
            <dl class="tw-grid tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-2 tw-mt-4 tw-mb-0">
                <a href="<?php echo admin_url('disowebs_ops/deposit_pending'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_deposit_pending'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold <?php echo ($stats['deposit_pending'] ?? 0) > 0 ? 'tw-text-warning-600' : 'tw-text-success-600'; ?>"><?php echo e($stats['deposit_pending'] ?? 0); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/final_pending'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_final_payment_pending'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold <?php echo ($stats['final_pending'] ?? 0) > 0 ? 'tw-text-warning-600' : 'tw-text-success-600'; ?>"><?php echo e($stats['final_pending'] ?? 0); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/training_pending'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_training_pending'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-neutral-600"><?php echo e($stats['training_pending'] ?? 0); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/demo_missing'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_demo_missing_this_week'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold <?php echo ($stats['demo_missing'] ?? 0) > 0 ? 'tw-text-danger-600' : 'tw-text-success-600'; ?>"><?php echo e($stats['demo_missing'] ?? 0); ?></dd>
                    </div>
                </a>
            </dl>

            <!-- Blockers Cards - Row 2 -->
            <dl class="tw-grid tw-grid-cols-2 tw-gap-2 tw-mt-2 tw-mb-0">
                <a href="<?php echo admin_url('disowebs_ops/blockers/active'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_active_blockers'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold <?php echo ($stats['active_blockers'] ?? 0) > 0 ? 'tw-text-danger-600' : 'tw-text-success-600'; ?>"><?php echo e($stats['active_blockers'] ?? 0); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/blockers/overdue'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_overdue_blockers'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold <?php echo ($stats['overdue_blockers'] ?? 0) > 0 ? 'tw-text-danger-600' : 'tw-text-success-600'; ?>"><?php echo e($stats['overdue_blockers'] ?? 0); ?></dd>
                    </div>
                </a>
            </dl>

            <!-- Margin Alerts Section -->
            <?php if ($has_alerts) { ?>
            <div class="tw-mt-4 tw-border-t tw-border-neutral-200 tw-pt-4">
                <span class="tw-text-sm tw-font-medium tw-text-neutral-700 tw-mb-2 tw-block"><?php echo _l('disowebs_ops_margin_alerts'); ?></span>
                <div class="tw-space-y-2">
                    <?php foreach ($margin_alerts as $alert) { ?>
                    <div class="tw-flex tw-items-center tw-justify-between tw-p-2 tw-rounded tw-bg-danger-50 tw-border tw-border-danger-200">
                        <div>
                            <span class="tw-text-xs tw-font-medium tw-text-danger-700">
                                <?php echo e(_l('disowebs_ops_margin_alert_' . $alert['alert_type'])); ?>
                            </span>
                            <p class="tw-text-sm tw-text-neutral-600 tw-mb-0"><?php echo e($alert['message']); ?></p>
                        </div>
                        <?php if (!$alert['acknowledged']) { ?>
                        <a href="<?php echo admin_url('projects/view/' . $alert['project_id'] . '?group=disowebs_ops_profit'); ?>" class="btn btn-xs btn-danger">
                            <?php echo _l('view'); ?>
                        </a>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
