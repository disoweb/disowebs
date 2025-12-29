<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('disowebs_ops/dw_dashboard_model');
$stats = $this->dw_dashboard_model->get_capacity_stats();
$active_projects = (int) ($stats['active_projects'] ?? 0);
$queued_projects = (int) ($stats['queued_projects'] ?? 0);
$active_limit = (int) ($stats['active_project_limit'] ?? 0);
$blocked_list = $stats['blocked_milestones'] ?? ($stats['overdue_milestones'] ?? []);
$blocked_total = (int) ($stats['blocked_milestones_total'] ?? count($blocked_list));
$over_limit = $active_limit > 0 && $active_projects > $active_limit;
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_ceo_capacity_risk'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_capacity_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_capacity_title'); ?></h4>
                <span class="label <?php echo $over_limit ? 'label-danger' : 'label-success'; ?>">
                    <?php echo $over_limit ? _l('disowebs_ops_dashboard_over_limit') : _l('disowebs_ops_dashboard_within_limit'); ?>
                </span>
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4 tw-mt-4">
                <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_active_projects'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800">
                        <?php echo e($active_projects); ?>
                        <?php if ($active_limit > 0) { ?>
                        <span class="tw-text-sm tw-text-neutral-500">/ <?php echo e($active_limit); ?></span>
                        <?php } ?>
                    </div>
                    <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_active_limit'); ?></div>
                </div>
                <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_queued_projects'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e($queued_projects); ?></div>
                    <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_queued_hint'); ?></div>
                </div>
                <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_blocked_milestones'); ?></div>
                    <?php if (count($blocked_list) === 0) { ?>
                        <div class="tw-text-sm tw-text-neutral-500 tw-mt-2"><?php echo _l('disowebs_ops_dashboard_overdue_none'); ?></div>
                    <?php } else { ?>
                        <ul class="tw-mt-2 tw-text-sm tw-text-neutral-700 tw-space-y-1">
                            <?php foreach ($blocked_list as $milestone) { ?>
                                <li>
                                    <a href="<?php echo admin_url('projects/view/' . $milestone['project_id']); ?>" class="tw-text-neutral-800">
                                        <?php echo e($milestone['project_name']); ?>
                                    </a>
                                    <span class="tw-text-neutral-500">· <?php echo e($milestone['title']); ?> (<?php echo e(_d($milestone['week_end'])); ?>)</span>
                                </li>
                            <?php } ?>
                        </ul>
                        <?php if ($blocked_total > count($blocked_list)) { ?>
                            <div class="tw-text-xs tw-text-neutral-500 tw-mt-2">
                                <?php echo e(sprintf(_l('disowebs_ops_dashboard_overdue_more'), $blocked_total - count($blocked_list))); ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
