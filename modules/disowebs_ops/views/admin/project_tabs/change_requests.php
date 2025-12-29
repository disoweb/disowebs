<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$CI = &get_instance();
$CI->load->model('disowebs_ops/dw_change_requests_model');

$change_requests = $CI->dw_change_requests_model->get_by_project($project->id);

$can_manage_cr = disowebs_ops_can_manage_change_requests();
$can_approve = disowebs_ops_can_approve_change_requests();
$can_delete_cr = disowebs_ops_can_delete_change_requests();
$can_create_cr_task = disowebs_ops_can_create_change_request_task();
$can_mark_implemented = disowebs_ops_can_mark_change_request_implemented();

$statuses = [
    'draft' => ['label' => _l('disowebs_ops_cr_status_draft'), 'class' => 'default'],
    'submitted' => ['label' => _l('disowebs_ops_cr_status_submitted'), 'class' => 'warning'],
    'approved' => ['label' => _l('disowebs_ops_cr_status_approved'), 'class' => 'success'],
    'rejected' => ['label' => _l('disowebs_ops_cr_status_rejected'), 'class' => 'danger'],
    'implemented' => ['label' => _l('disowebs_ops_cr_status_implemented'), 'class' => 'info'],
];

$counts = array_fill_keys(array_keys($statuses), 0);
$impact_summary = [
    'approved_days' => 0,
    'approved_cost' => 0.0,
    'pending_days' => 0,
    'pending_cost' => 0.0,
];
foreach ($change_requests as $change_request) {
    $status = $change_request['status'];
    if (isset($counts[$status])) {
        $counts[$status]++;
    }
    if (in_array($status, ['approved', 'implemented'], true)) {
        $impact_summary['approved_days'] += (int) $change_request['impact_days'];
        $impact_summary['approved_cost'] += (float) $change_request['impact_cost'];
    } elseif ($status === 'submitted') {
        $impact_summary['pending_days'] += (int) $change_request['impact_days'];
        $impact_summary['pending_cost'] += (float) $change_request['impact_cost'];
    }
}

$existing_cr_tasks = [];
foreach ($change_requests as $change_request) {
    $CI->db->where('rel_type', 'project');
    $CI->db->where('rel_id', $project->id);
    $CI->db->like('name', '[DW' . (int) $change_request['id'] . ']', 'after');
    $CI->db->limit(1);
    $existing_cr_tasks[$change_request['id']] = (bool) $CI->db->get(db_prefix() . 'tasks')->row_array();
}

$currency = function_exists('get_base_currency') ? get_base_currency() : null;
$currency_name = $currency && isset($currency->name) ? $currency->name : '';
?>

<div class="panel_s">
    <div class="panel-body">
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
            <div>
                <h4 class="no-margin"><?php echo e(_l('disowebs_ops_tab_change_requests')); ?></h4>
                <p class="text-muted mtop5"><?php echo e(_l('disowebs_ops_cr_intro')); ?></p>
            </div>
            <?php if (!$can_approve) { ?>
            <span class="label label-default mtop10"><?php echo e(_l('disowebs_ops_cr_approval_note')); ?></span>
            <?php } ?>
        </div>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-4 mtop15">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_total')); ?></div>
                    <h4 class="no-margin"><?php echo e(count($change_requests)); ?></h4>
                </div>
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_submitted_count')); ?></div>
                    <h4 class="no-margin"><?php echo e($counts['submitted']); ?></h4>
                </div>
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_approved_count')); ?></div>
                    <h4 class="no-margin"><?php echo e($counts['approved']); ?></h4>
                </div>
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_pending')); ?></div>
                    <h4 class="no-margin"><?php echo e($counts['draft'] + $counts['rejected']); ?></h4>
                </div>
            </div>
        </div>

        <div class="panel_s mtop15">
            <div class="panel-body">
                <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                    <h4 class="no-margin"><?php echo e(_l('disowebs_ops_cr_impact_summary')); ?></h4>
                    <span class="text-muted"><?php echo e(_l('disowebs_ops_cr_impact_summary_hint')); ?></span>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-4 mtop15">
                    <div>
                        <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_impact_approved_days')); ?></div>
                        <h4 class="no-margin"><?php echo e($impact_summary['approved_days']); ?></h4>
                    </div>
                    <div>
                        <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_impact_approved_cost')); ?></div>
                        <h4 class="no-margin"><?php echo e(app_format_money($impact_summary['approved_cost'], $currency_name)); ?></h4>
                    </div>
                    <div>
                        <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_impact_pending_days')); ?></div>
                        <h4 class="no-margin"><?php echo e($impact_summary['pending_days']); ?></h4>
                    </div>
                    <div>
                        <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_impact_pending_cost')); ?></div>
                        <h4 class="no-margin"><?php echo e(app_format_money($impact_summary['pending_cost'], $currency_name)); ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($can_manage_cr) { ?>
        <div class="mtop20">
            <a class="btn btn-primary btn-sm" data-toggle="collapse" href="#dw-add-cr" aria-expanded="false">
                <i class="fa fa-plus"></i> <?php echo e(_l('disowebs_ops_cr_add')); ?>
            </a>
        </div>

        <div id="dw-add-cr" class="collapse mtop15">
            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/add_change_request/' . $project->id)); ?>
            <?php echo render_input('title', 'disowebs_ops_cr_title'); ?>
            <?php echo render_textarea('description', 'disowebs_ops_cr_description', '', ['rows' => 4]); ?>
            <?php echo render_input('impact_days', 'disowebs_ops_cr_impact_days', '', 'number', ['min' => 0]); ?>
            <?php echo render_input('impact_cost', 'disowebs_ops_cr_impact_cost', '', 'number', ['min' => 0, 'step' => '0.01']); ?>
            <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_cr_save')); ?></button>
            <?php echo form_close(); ?>
        </div>
        <?php } ?>

        <?php if (empty($change_requests)) { ?>
        <p class="text-muted mtop20"><?php echo e(_l('disowebs_ops_cr_none')); ?></p>
        <?php } ?>

        <?php foreach ($change_requests as $change_request) { ?>
        <?php $status_meta = $statuses[$change_request['status']] ?? $statuses['draft']; ?>
        <div class="panel panel-default mtop20">
            <div class="panel-body">
                <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                    <div>
                        <div class="tw-flex tw-items-center tw-flex-wrap tw-gap-2">
                            <h4 class="no-margin"><?php echo e($change_request['title']); ?></h4>
                            <span class="label label-<?php echo e($status_meta['class']); ?>"><?php echo e($status_meta['label']); ?></span>
                        </div>
                        <div class="text-muted mtop5">
                            <?php echo e(_l('disowebs_ops_cr_created', _dt($change_request['created_at']))); ?>
                            <?php if (!empty($change_request['created_by'])) { ?>
                            <span class="text-muted">• <?php echo e(get_staff_full_name($change_request['created_by'])); ?></span>
                            <?php } ?>
                        </div>
                        <?php if (!empty($change_request['approved_by']) && !empty($change_request['approved_at'])) { ?>
                        <div class="text-muted mtop5">
                            <?php echo e(_l('disowebs_ops_cr_approved_by', _dt($change_request['approved_at']))); ?>
                            <span class="text-muted">• <?php echo e(get_staff_full_name($change_request['approved_by'])); ?></span>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="tw-flex tw-flex-col tw-items-end mtop10 md:mtop0">
                        <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_impact_days')); ?>: <strong><?php echo e((int) $change_request['impact_days']); ?></strong></div>
                        <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_impact_cost')); ?>: <strong><?php echo e(app_format_money($change_request['impact_cost'], $currency_name)); ?></strong></div>
                    </div>
                </div>

                <?php if (!empty($change_request['description'])) { ?>
                <div class="mtop10">
                    <?php echo nl2br(e($change_request['description'])); ?>
                </div>
                <?php } ?>

                <div class="tw-flex tw-flex-wrap tw-gap-2 mtop15">
        <?php if ($can_manage_cr && in_array($change_request['status'], ['draft', 'rejected'], true)) { ?>
        <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/submit_change_request/' . $project->id . '/' . $change_request['id']), ['class' => 'tw-inline-block']); ?>
        <button type="submit" class="btn btn-info btn-sm"><?php echo e(_l('disowebs_ops_cr_submit')); ?></button>
        <?php echo form_close(); ?>
        <?php } ?>

                    <?php if ($can_approve && $change_request['status'] === 'submitted') { ?>
                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/approve_change_request/' . $project->id . '/' . $change_request['id']), ['class' => 'tw-inline-block']); ?>
                    <button type="submit" class="btn btn-success btn-sm"><?php echo e(_l('disowebs_ops_cr_approve')); ?></button>
                    <?php echo form_close(); ?>

                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/reject_change_request/' . $project->id . '/' . $change_request['id']), ['class' => 'tw-inline-block']); ?>
                    <button type="submit" class="btn btn-danger btn-sm"><?php echo e(_l('disowebs_ops_cr_reject')); ?></button>
                    <?php echo form_close(); ?>
                    <?php } ?>

        <?php if ($can_create_cr_task && $change_request['status'] === 'approved') { ?>
        <?php if (!empty($existing_cr_tasks[$change_request['id']])) { ?>
        <span class="label label-success"><?php echo e(_l('disowebs_ops_cr_task_created_label')); ?></span>
        <?php } else { ?>
        <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/create_change_request_task/' . $project->id . '/' . $change_request['id']), ['class' => 'tw-inline-block']); ?>
        <button type="submit" class="btn btn-primary btn-sm"><?php echo e(_l('disowebs_ops_cr_task_create')); ?></button>
        <?php echo form_close(); ?>
        <?php } ?>
        <?php } ?>

        <?php if ($can_mark_implemented && $change_request['status'] === 'approved') { ?>
        <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/mark_change_request_implemented/' . $project->id . '/' . $change_request['id']), ['class' => 'tw-inline-block']); ?>
        <button type="submit" class="btn btn-default btn-sm"><?php echo e(_l('disowebs_ops_cr_mark_implemented')); ?></button>
        <?php echo form_close(); ?>
        <?php } ?>

        <?php if ($can_manage_cr && in_array($change_request['status'], ['draft', 'submitted', 'rejected'], true)) { ?>
        <a class="btn btn-default btn-sm" data-toggle="collapse" href="#cr-edit-<?php echo e($change_request['id']); ?>">
            <?php echo e(_l('disowebs_ops_edit')); ?>
        </a>
        <?php } ?>

        <?php if ($can_delete_cr && in_array($change_request['status'], ['draft', 'rejected'], true)) { ?>
        <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/delete_change_request/' . $project->id . '/' . $change_request['id']), ['class' => 'tw-inline-block']); ?>
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo e(_l('disowebs_ops_confirm_delete')); ?>');">
            <?php echo e(_l('disowebs_ops_delete')); ?>
        </button>
        <?php echo form_close(); ?>
        <?php } ?>
                </div>

        <?php if ($can_manage_cr && in_array($change_request['status'], ['draft', 'submitted', 'rejected'], true)) { ?>
        <div id="cr-edit-<?php echo e($change_request['id']); ?>" class="collapse mtop15">
                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/update_change_request/' . $project->id . '/' . $change_request['id'])); ?>
                    <?php echo render_input('title', 'disowebs_ops_cr_title', $change_request['title']); ?>
                    <?php echo render_textarea('description', 'disowebs_ops_cr_description', $change_request['description'], ['rows' => 4]); ?>
                    <?php echo render_input('impact_days', 'disowebs_ops_cr_impact_days', $change_request['impact_days'], 'number', ['min' => 0]); ?>
                    <?php echo render_input('impact_cost', 'disowebs_ops_cr_impact_cost', $change_request['impact_cost'], 'number', ['min' => 0, 'step' => '0.01']); ?>
                    <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_cr_update')); ?></button>
                    <?php echo form_close(); ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
