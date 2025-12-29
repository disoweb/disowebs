<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('disowebs_ops/dw_dashboard_model');
$stats = $this->dw_dashboard_model->get_qa_readiness_stats();
$qa_tracked = !empty($stats['qa_checklist_tracked']);
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_tsa_qa_readiness'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_qa_readiness_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_qa_readiness_title'); ?></h4>
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4 tw-mt-4">
                <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_projects_awaiting_qa'); ?></div>
                    <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800"><?php echo e($stats['projects_awaiting_qa']); ?></div>
                </div>
                <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                    <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_dashboard_qa_checklist_status'); ?></div>
                    <?php if ($qa_tracked) { ?>
                        <div class="tw-text-2xl tw-font-semibold tw-text-neutral-800">100%</div>
                    <?php } else { ?>
                        <div class="tw-text-sm tw-text-neutral-500 tw-mt-2"><?php echo _l('disowebs_ops_dashboard_qa_checklist_not_tracked'); ?></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
