<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$CI = &get_instance();
$this->load->model('disowebs_ops/dw_dashboard_model');
$range_key = $CI->input->get('dw_just_exec_range', true);
$range = disowebs_ops_get_date_range_for_key($range_key);
$stats = $this->dw_dashboard_model->get_weekly_execution_stats($range);
$range_options = [
    'day' => _l('disowebs_ops_range_day'),
    'week' => _l('disowebs_ops_range_week'),
    'month' => _l('disowebs_ops_range_month'),
    'quarter' => _l('disowebs_ops_range_quarter'),
    'year' => _l('disowebs_ops_range_year'),
];
$widget_url = admin_url('disowebs_ops/just_execution_widget');
$show_warning = ((int) $stats['milestones_done'] === 0 || (int) $stats['proof_entries'] === 0);
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_ceo_weekly_execution'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_ceo_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 tw-flex-nowrap" style="display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:nowrap;">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800 tw-flex-1 tw-min-w-0 tw-truncate" style="margin:0; flex:1 1 auto; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo _l('disowebs_ops_dashboard_ceo_title'); ?></h4>
                <select class="form-control input-sm tw-shrink-0 tw-text-xs" data-dw-just-exec-range style="width:110px !important; flex:0 0 110px; display:inline-block;">
                    <?php foreach ($range_options as $key => $label) { ?>
                    <?php $option_url = $widget_url . '?dw_just_exec_range=' . $key; ?>
                    <option value="<?php echo e($option_url); ?>" <?php echo $range['key'] === $key ? 'selected' : ''; ?>>
                        <?php echo e($label); ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <dl class="tw-grid tw-grid-cols-2 tw-gap-2 tw-mt-4 tw-mb-0">
                <a href="<?php echo admin_url('disowebs_ops/milestones_delivered?range=' . $range['key']); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <div class="tw-flex tw-items-center tw-justify-between tw-gap-2">
                            <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_delivery'); ?></dt>
                            <dd class="tw-font-semibold tw-text-neutral-600"><?php echo e($stats['milestones_done']); ?></dd>
                        </div>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/demos'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <div class="tw-flex tw-items-center tw-justify-between tw-gap-2">
                            <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_proof'); ?></dt>
                            <dd class="tw-font-semibold tw-text-neutral-600"><?php echo e($stats['proof_entries']); ?></dd>
                        </div>
                    </div>
                </a>
            </dl>
            <?php if ($show_warning) { ?>
            <div class="alert alert-warning mtop15 mbot0"><?php echo _l('disowebs_ops_dashboard_weekly_execution_warning'); ?></div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
if (typeof window.disowebsOpsJustExecInit === 'undefined') {
    window.disowebsOpsJustExecInit = true;
    $(document).on('change', '[data-dw-just-exec-range]', function() {
        var $select = $(this);
        var url = $select.val();
        if (!url) {
            return;
        }
        var $widget = $select.closest('.widget');
        $.get(url, function(html) {
            $widget.replaceWith(html);
        }).fail(function() {
            window.location = url;
        });
    });
}
</script>
