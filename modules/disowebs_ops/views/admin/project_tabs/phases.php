<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$CI = &get_instance();
$CI->load->model('disowebs_ops/dw_project_phases_model');
$CI->load->model('disowebs_ops/dw_project_milestones_model');

$phases = $CI->dw_project_phases_model->get_by_project($project->id);
$milestones = $CI->dw_project_milestones_model->get_by_project($project->id);

$can_manage_phases = disowebs_ops_can_manage_phases();
$can_manage_milestones = disowebs_ops_can_manage_milestones();
$can_update_milestone_status = disowebs_ops_can_update_milestone_status();
$can_delete_milestones = disowebs_ops_can_delete_milestones();
$has_edit_access = $can_manage_phases || $can_manage_milestones || $can_update_milestone_status || $can_delete_milestones;
$today = date('Y-m-d');
$week_range = disowebs_ops_get_current_week_range();

$phase_statuses = [
    'not_started' => ['label' => _l('disowebs_ops_status_not_started'), 'class' => 'default'],
    'in_progress' => ['label' => _l('disowebs_ops_status_in_progress'), 'class' => 'info'],
    'done' => ['label' => _l('disowebs_ops_status_done'), 'class' => 'success'],
];

$milestone_statuses = [
    'planned' => ['label' => _l('disowebs_ops_status_planned'), 'class' => 'default'],
    'in_progress' => ['label' => _l('disowebs_ops_status_in_progress'), 'class' => 'info'],
    'done' => ['label' => _l('disowebs_ops_status_done'), 'class' => 'success'],
];

$total_phases = count($phases);
$done_phases = 0;
$build_phase_active = false;
foreach ($phases as $phase) {
    if ($phase['status'] === 'done') {
        $done_phases++;
    }
    if (!$build_phase_active) {
        $phase_name = strtolower((string) $phase['name']);
        if (strpos($phase_name, 'build') !== false && in_array($phase['status'], ['in_progress', 'done'], true)) {
            $build_phase_active = true;
        }
    }
}
$phase_completion = $total_phases > 0 ? (int) round(($done_phases / $total_phases) * 100) : 0;

$total_milestones = count($milestones);
$done_milestones = 0;
$overdue_milestones = [];
$has_current_week_milestone = false;
foreach ($milestones as $milestone) {
    if ($milestone['status'] === 'done') {
        $done_milestones++;
    }
    if ($milestone['status'] !== 'done' && $milestone['week_end'] < $today) {
        $overdue_milestones[] = $milestone;
    }
    if (!$has_current_week_milestone && $milestone['week_start'] <= $week_range['week_end'] && $milestone['week_end'] >= $week_range['week_start']) {
        $has_current_week_milestone = true;
    }
}
$milestone_completion = $total_milestones > 0 ? (int) round(($done_milestones / $total_milestones) * 100) : 0;
$invoice_summary = disowebs_ops_get_project_invoice_summary($project->id);
$deposit_warning = $build_phase_active && ($invoice_summary['total'] ?? 0) > 0 && ($invoice_summary['paid_ratio'] ?? 0) < 0.6;
$final_payment_warning = ((int) $project->status === 4 && ($invoice_summary['outstanding'] ?? 0) > 0);

$milestones_by_phase = [];
$milestones_without_phase = [];
$phase_lookup = [];
foreach ($phases as $phase) {
    $phase_lookup[(int) $phase['id']] = true;
}
foreach ($milestones as $milestone) {
    $phase_id = (int) $milestone['phase_id'];
    if ($phase_id > 0 && isset($phase_lookup[$phase_id])) {
        $milestones_by_phase[$phase_id][] = $milestone;
    } else {
        $milestones_without_phase[] = $milestone;
    }
}
?>

<div class="panel_s">
    <div class="panel-body">
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
            <div>
                <h4 class="no-margin"><?php echo e(_l('disowebs_ops_tab_phases')); ?></h4>
                <p class="text-muted mtop5"><?php echo e(_l('disowebs_ops_phases_intro')); ?></p>
            </div>
            <?php if (!$has_edit_access) { ?>
            <span class="label label-default mtop10"><?php echo e(_l('disowebs_ops_view_only')); ?></span>
            <?php } ?>
        </div>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4 mtop15">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between">
                        <span class="text-muted"><?php echo e(_l('disowebs_ops_phase_completion')); ?></span>
                        <span class="label label-info"><?php echo e($phase_completion); ?>%</span>
                    </div>
                    <div class="progress mtop10">
                        <div class="progress-bar progress-bar-info" role="progressbar" data-percent="<?php echo e($phase_completion); ?>" aria-valuenow="<?php echo e($phase_completion); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo e($phase_completion); ?>%;"></div>
                    </div>
                    <p class="text-muted mtop5"><?php echo e(_l('disowebs_ops_phases_done_of_total', $done_phases . '/' . $total_phases)); ?></p>
                </div>
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between">
                        <span class="text-muted"><?php echo e(_l('disowebs_ops_milestone_completion')); ?></span>
                        <span class="label label-success"><?php echo e($milestone_completion); ?>%</span>
                    </div>
                    <div class="progress mtop10">
                        <div class="progress-bar progress-bar-success" role="progressbar" data-percent="<?php echo e($milestone_completion); ?>" aria-valuenow="<?php echo e($milestone_completion); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo e($milestone_completion); ?>%;"></div>
                    </div>
                    <p class="text-muted mtop5"><?php echo e(_l('disowebs_ops_milestones_done_of_total', $done_milestones . '/' . $total_milestones)); ?></p>
                </div>
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between">
                        <span class="text-muted"><?php echo e(_l('disowebs_ops_overdue_milestones')); ?></span>
                        <span class="label label-danger"><?php echo e(count($overdue_milestones)); ?></span>
                    </div>
                    <?php if (count($overdue_milestones) > 0) { ?>
                    <ul class="list-unstyled mtop10">
                        <?php foreach (array_slice($overdue_milestones, 0, 3) as $overdue) { ?>
                        <li class="text-danger">
                            <i class="fa fa-exclamation-circle"></i>
                            <?php echo e($overdue['title']); ?>
                            <span class="text-muted">(<?php echo e(_d($overdue['week_end'])); ?>)</span>
                        </li>
                        <?php } ?>
                        <?php if (count($overdue_milestones) > 3) { ?>
                        <li class="text-muted"><?php echo e(_l('disowebs_ops_overdue_more', count($overdue_milestones) - 3)); ?></li>
                        <?php } ?>
                    </ul>
                    <?php } else { ?>
                    <p class="text-muted mtop10"><?php echo e(_l('disowebs_ops_overdue_none')); ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php if ((int) $project->status === 2 && !$has_current_week_milestone) { ?>
        <div class="alert alert-warning mtop15">
            <?php echo e(_l('disowebs_ops_current_week_milestone_missing')); ?>
        </div>
        <?php } ?>
        <?php if ($deposit_warning) { ?>
        <div class="alert alert-warning mtop15">
            <?php echo e(_l('disowebs_ops_phase_deposit_warning')); ?>
        </div>
        <?php } ?>
        <?php if ($final_payment_warning) { ?>
        <div class="alert alert-danger mtop15">
            <?php echo e(_l('disowebs_ops_phase_final_payment_warning')); ?>
        </div>
        <?php } ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel_s">
            <div class="panel-body">
                <div class="tw-flex tw-items-center tw-justify-between">
                    <h4 class="no-margin"><?php echo e(_l('disowebs_ops_phases_heading')); ?></h4>
                    <?php if ($can_manage_phases) { ?>
                    <a class="btn btn-primary btn-sm" data-toggle="collapse" href="#dw-add-phase" aria-expanded="false">
                        <i class="fa fa-plus"></i> <?php echo e(_l('disowebs_ops_add_phase')); ?>
                    </a>
                    <?php } ?>
                </div>

                <?php if ($can_manage_phases) { ?>
                <div id="dw-add-phase" class="collapse mtop15">
                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/add_phase/' . $project->id)); ?>
                    <?php echo render_input('name', 'disowebs_ops_phase_name'); ?>
                    <div class="form-group">
                        <label for="phase_status"><?php echo e(_l('disowebs_ops_status')); ?></label>
                        <select class="form-control" id="phase_status" name="status">
                            <?php foreach ($phase_statuses as $key => $meta) { ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($meta['label']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_save_phase')); ?></button>
                    <?php echo form_close(); ?>
                </div>
                <?php } ?>

                <?php if (empty($phases)) { ?>
                <div class="text-muted mtop20"><?php echo e(_l('disowebs_ops_no_phases')); ?></div>
                <?php } ?>

                <?php foreach ($phases as $phase) { ?>
                <?php $status_meta = $phase_statuses[$phase['status']] ?? $phase_statuses['not_started']; ?>
                <div class="panel panel-default mtop15">
                    <div class="panel-body">
                        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                            <div>
                                <strong><?php echo e($phase['name']); ?></strong>
                                <span class="label label-<?php echo e($status_meta['class']); ?> mleft5"><?php echo e($status_meta['label']); ?></span>
                                <div class="text-muted mtop5">
                                    <?php if (!empty($phase['started_at'])) { ?>
                                    <span><?php echo e(_l('disowebs_ops_started_at', _dt($phase['started_at']))); ?></span>
                                    <?php } ?>
                                    <?php if (!empty($phase['completed_at'])) { ?>
                                    <span class="mleft10"><?php echo e(_l('disowebs_ops_completed_at', _dt($phase['completed_at']))); ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if ($can_manage_phases) { ?>
                            <div class="tw-flex tw-flex-wrap tw-gap-2 mtop10 md:tw-justify-end">
                                <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/move_phase/' . $project->id . '/' . $phase['id']), ['class' => 'tw-inline-block']); ?>
                                <?php echo form_hidden('direction', 'up'); ?>
                                <button type="submit" class="btn btn-default btn-sm" title="<?php echo e(_l('disowebs_ops_move_up')); ?>">
                                    <i class="fa fa-arrow-up"></i>
                                </button>
                                <?php echo form_close(); ?>

                                <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/move_phase/' . $project->id . '/' . $phase['id']), ['class' => 'tw-inline-block']); ?>
                                <?php echo form_hidden('direction', 'down'); ?>
                                <button type="submit" class="btn btn-default btn-sm" title="<?php echo e(_l('disowebs_ops_move_down')); ?>">
                                    <i class="fa fa-arrow-down"></i>
                                </button>
                                <?php echo form_close(); ?>

                                <?php if ($phase['status'] !== 'in_progress') { ?>
                                <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/set_phase_status/' . $project->id . '/' . $phase['id']), ['class' => 'tw-inline-block']); ?>
                                <?php echo form_hidden('status', 'in_progress'); ?>
                                <button type="submit" class="btn btn-info btn-sm"><?php echo e(_l('disowebs_ops_mark_in_progress')); ?></button>
                                <?php echo form_close(); ?>
                                <?php } ?>

                                <?php if ($phase['status'] !== 'done') { ?>
                                <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/set_phase_status/' . $project->id . '/' . $phase['id']), ['class' => 'tw-inline-block']); ?>
                                <?php echo form_hidden('status', 'done'); ?>
                                <button type="submit" class="btn btn-success btn-sm"><?php echo e(_l('disowebs_ops_mark_done')); ?></button>
                                <?php echo form_close(); ?>
                                <?php } ?>

                                <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/set_phase_status/' . $project->id . '/' . $phase['id']), ['class' => 'tw-inline-block']); ?>
                                <?php echo form_hidden('status', 'not_started'); ?>
                                <button type="submit" class="btn btn-default btn-sm"><?php echo e(_l('disowebs_ops_reset')); ?></button>
                                <?php echo form_close(); ?>

                                <a class="btn btn-default btn-sm" data-toggle="collapse" href="#phase-edit-<?php echo e($phase['id']); ?>">
                                    <?php echo e(_l('disowebs_ops_edit')); ?>
                                </a>

                                <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/delete_phase/' . $project->id . '/' . $phase['id']), ['class' => 'tw-inline-block']); ?>
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo e(_l('disowebs_ops_confirm_delete')); ?>');">
                                    <?php echo e(_l('disowebs_ops_delete')); ?>
                                </button>
                                <?php echo form_close(); ?>
                            </div>
                            <?php } ?>
                        </div>

                            <?php if ($can_manage_phases) { ?>
                            <div id="phase-edit-<?php echo e($phase['id']); ?>" class="collapse mtop15">
                            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/update_phase/' . $project->id . '/' . $phase['id'])); ?>
                            <?php echo render_input('name', 'disowebs_ops_phase_name', $phase['name']); ?>
                            <div class="form-group">
                                <label for="phase_status_<?php echo e($phase['id']); ?>"><?php echo e(_l('disowebs_ops_status')); ?></label>
                                <select class="form-control" id="phase_status_<?php echo e($phase['id']); ?>" name="status">
                                    <?php foreach ($phase_statuses as $key => $meta) { ?>
                                    <option value="<?php echo e($key); ?>" <?php echo $phase['status'] === $key ? 'selected' : ''; ?>><?php echo e($meta['label']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_save_phase')); ?></button>
                            <?php echo form_close(); ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel_s">
            <div class="panel-body">
                <div class="tw-flex tw-items-center tw-justify-between">
                    <h4 class="no-margin"><?php echo e(_l('disowebs_ops_milestones_heading')); ?></h4>
                    <?php if ($can_manage_milestones) { ?>
                    <a class="btn btn-primary btn-sm" data-toggle="collapse" href="#dw-add-milestone" aria-expanded="false">
                        <i class="fa fa-plus"></i> <?php echo e(_l('disowebs_ops_add_milestone')); ?>
                    </a>
                    <?php } ?>
                </div>

                <?php if ($can_manage_milestones) { ?>
                <div id="dw-add-milestone" class="collapse mtop15">
                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/add_milestone/' . $project->id)); ?>
                    <?php echo render_input('title', 'disowebs_ops_milestone_title'); ?>
                    <?php echo render_textarea('description', 'disowebs_ops_milestone_description', '', ['rows' => 3]); ?>
                    <div class="form-group">
                        <label for="milestone_phase"><?php echo e(_l('disowebs_ops_phase_optional')); ?></label>
                        <select class="form-control" id="milestone_phase" name="phase_id">
                            <option value=""><?php echo e(_l('disowebs_ops_no_phase')); ?></option>
                            <?php foreach ($phases as $phase) { ?>
                            <option value="<?php echo e($phase['id']); ?>"><?php echo e($phase['name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php echo render_date_input('week_start', 'disowebs_ops_week_start', _d($week_range['week_start'])); ?>
                    <?php echo render_date_input('week_end', 'disowebs_ops_week_end', _d($week_range['week_end'])); ?>
                    <div class="form-group">
                        <label for="milestone_status"><?php echo e(_l('disowebs_ops_status')); ?></label>
                        <select class="form-control" id="milestone_status" name="status">
                            <?php foreach ($milestone_statuses as $key => $meta) { ?>
                            <option value="<?php echo e($key); ?>" <?php echo $key === 'planned' ? 'selected' : ''; ?>><?php echo e($meta['label']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_save_milestone')); ?></button>
                    <?php echo form_close(); ?>
                </div>
                <?php } ?>

                <?php if (empty($milestones)) { ?>
                <div class="text-muted mtop20"><?php echo e(_l('disowebs_ops_no_milestones')); ?></div>
                <?php } ?>

                <?php foreach ($phases as $phase) { ?>
                <?php $phase_milestones = $milestones_by_phase[$phase['id']] ?? []; ?>
                <div class="mtop20">
                    <h5 class="bold"><?php echo e($phase['name']); ?></h5>
                    <?php if (empty($phase_milestones)) { ?>
                    <p class="text-muted"><?php echo e(_l('disowebs_ops_no_milestones_for_phase')); ?></p>
                    <?php } ?>
                    <?php foreach ($phase_milestones as $milestone) { ?>
                    <?php $meta = $milestone_statuses[$milestone['status']] ?? $milestone_statuses['planned']; ?>
                    <div class="panel panel-default mtop10">
                        <div class="panel-body">
                            <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                                <div>
                                    <strong><?php echo e($milestone['title']); ?></strong>
                                    <span class="label label-<?php echo e($meta['class']); ?> mleft5"><?php echo e($meta['label']); ?></span>
                                    <div class="text-muted mtop5">
                                        <?php echo e(_l('disowebs_ops_week_range', _d($milestone['week_start']) . ' - ' . _d($milestone['week_end']))); ?>
                                    </div>
                                    <?php if (!empty($milestone['description'])) { ?>
                                    <div class="text-muted mtop5"><?php echo e($milestone['description']); ?></div>
                                    <?php } ?>
                                </div>
                                <?php if ($can_manage_milestones || $can_update_milestone_status || $can_delete_milestones) { ?>
                                <div class="tw-flex tw-flex-wrap tw-gap-2 mtop10 md:tw-justify-end">
                                    <?php if ($can_update_milestone_status && $milestone['status'] !== 'in_progress') { ?>
                                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/set_milestone_status/' . $project->id . '/' . $milestone['id']), ['class' => 'tw-inline-block']); ?>
                                    <?php echo form_hidden('status', 'in_progress'); ?>
                                    <button type="submit" class="btn btn-info btn-sm"><?php echo e(_l('disowebs_ops_mark_in_progress')); ?></button>
                                    <?php echo form_close(); ?>
                                    <?php } ?>

                                    <?php if ($can_update_milestone_status && $milestone['status'] !== 'done') { ?>
                                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/set_milestone_status/' . $project->id . '/' . $milestone['id']), ['class' => 'tw-inline-block']); ?>
                                    <?php echo form_hidden('status', 'done'); ?>
                                    <button type="submit" class="btn btn-success btn-sm"><?php echo e(_l('disowebs_ops_mark_done')); ?></button>
                                    <?php echo form_close(); ?>
                                    <?php } ?>

                                    <?php if ($can_update_milestone_status) { ?>
                                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/set_milestone_status/' . $project->id . '/' . $milestone['id']), ['class' => 'tw-inline-block']); ?>
                                    <?php echo form_hidden('status', 'planned'); ?>
                                    <button type="submit" class="btn btn-default btn-sm"><?php echo e(_l('disowebs_ops_reset')); ?></button>
                                    <?php echo form_close(); ?>
                                    <?php } ?>

                                    <?php if ($can_manage_milestones) { ?>
                                    <a class="btn btn-default btn-sm" data-toggle="collapse" href="#milestone-edit-<?php echo e($milestone['id']); ?>">
                                        <?php echo e(_l('disowebs_ops_edit')); ?>
                                    </a>
                                    <?php } ?>

                                    <?php if ($can_delete_milestones) { ?>
                                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/delete_milestone/' . $project->id . '/' . $milestone['id']), ['class' => 'tw-inline-block']); ?>
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo e(_l('disowebs_ops_confirm_delete')); ?>');">
                                        <?php echo e(_l('disowebs_ops_delete')); ?>
                                    </button>
                                    <?php echo form_close(); ?>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                            </div>

                            <?php if ($can_manage_milestones) { ?>
                            <div id="milestone-edit-<?php echo e($milestone['id']); ?>" class="collapse mtop15">
                                <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/update_milestone/' . $project->id . '/' . $milestone['id'])); ?>
                                <?php echo render_input('title', 'disowebs_ops_milestone_title', $milestone['title']); ?>
                                <?php echo render_textarea('description', 'disowebs_ops_milestone_description', $milestone['description'], ['rows' => 3]); ?>
                                <div class="form-group">
                                    <label for="milestone_phase_<?php echo e($milestone['id']); ?>"><?php echo e(_l('disowebs_ops_phase_optional')); ?></label>
                                    <select class="form-control" id="milestone_phase_<?php echo e($milestone['id']); ?>" name="phase_id">
                                        <option value=""><?php echo e(_l('disowebs_ops_no_phase')); ?></option>
                                        <?php foreach ($phases as $phase) { ?>
                                        <option value="<?php echo e($phase['id']); ?>" <?php echo (int) $milestone['phase_id'] === (int) $phase['id'] ? 'selected' : ''; ?>><?php echo e($phase['name']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group" app-field-wrapper="week_start">
                                    <label for="week_start_<?php echo e($milestone['id']); ?>" class="control-label"><?php echo e(_l('disowebs_ops_week_start')); ?></label>
                                    <div class="input-group date">
                                        <input type="text" id="week_start_<?php echo e($milestone['id']); ?>" name="week_start" class="form-control datepicker" value="<?php echo e(_d($milestone['week_start'])); ?>" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="fa-regular fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" app-field-wrapper="week_end">
                                    <label for="week_end_<?php echo e($milestone['id']); ?>" class="control-label"><?php echo e(_l('disowebs_ops_week_end')); ?></label>
                                    <div class="input-group date">
                                        <input type="text" id="week_end_<?php echo e($milestone['id']); ?>" name="week_end" class="form-control datepicker" value="<?php echo e(_d($milestone['week_end'])); ?>" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="fa-regular fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="milestone_status_<?php echo e($milestone['id']); ?>"><?php echo e(_l('disowebs_ops_status')); ?></label>
                                    <select class="form-control" id="milestone_status_<?php echo e($milestone['id']); ?>" name="status">
                                        <?php foreach ($milestone_statuses as $key => $meta) { ?>
                                        <option value="<?php echo e($key); ?>" <?php echo $milestone['status'] === $key ? 'selected' : ''; ?>><?php echo e($meta['label']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_save_milestone')); ?></button>
                                <?php echo form_close(); ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>

                <?php if (!empty($milestones_without_phase)) { ?>
                <div class="mtop20">
                    <h5 class="bold"><?php echo e(_l('disowebs_ops_unassigned_milestones')); ?></h5>
                    <?php foreach ($milestones_without_phase as $milestone) { ?>
                    <?php $meta = $milestone_statuses[$milestone['status']] ?? $milestone_statuses['planned']; ?>
                    <div class="panel panel-default mtop10">
                        <div class="panel-body">
                            <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                                <div>
                                    <strong><?php echo e($milestone['title']); ?></strong>
                                    <span class="label label-<?php echo e($meta['class']); ?> mleft5"><?php echo e($meta['label']); ?></span>
                                    <div class="text-muted mtop5">
                                        <?php echo e(_l('disowebs_ops_week_range', _d($milestone['week_start']) . ' - ' . _d($milestone['week_end']))); ?>
                                    </div>
                                    <?php if (!empty($milestone['description'])) { ?>
                                    <div class="text-muted mtop5"><?php echo e($milestone['description']); ?></div>
                                    <?php } ?>
                                </div>
                                <?php if ($can_manage_milestones || $can_update_milestone_status || $can_delete_milestones) { ?>
                                <div class="tw-flex tw-flex-wrap tw-gap-2 mtop10 md:tw-justify-end">
                                    <?php if ($can_update_milestone_status && $milestone['status'] !== 'in_progress') { ?>
                                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/set_milestone_status/' . $project->id . '/' . $milestone['id']), ['class' => 'tw-inline-block']); ?>
                                    <?php echo form_hidden('status', 'in_progress'); ?>
                                    <button type="submit" class="btn btn-info btn-sm"><?php echo e(_l('disowebs_ops_mark_in_progress')); ?></button>
                                    <?php echo form_close(); ?>
                                    <?php } ?>

                                    <?php if ($can_update_milestone_status && $milestone['status'] !== 'done') { ?>
                                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/set_milestone_status/' . $project->id . '/' . $milestone['id']), ['class' => 'tw-inline-block']); ?>
                                    <?php echo form_hidden('status', 'done'); ?>
                                    <button type="submit" class="btn btn-success btn-sm"><?php echo e(_l('disowebs_ops_mark_done')); ?></button>
                                    <?php echo form_close(); ?>
                                    <?php } ?>

                                    <?php if ($can_update_milestone_status) { ?>
                                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/set_milestone_status/' . $project->id . '/' . $milestone['id']), ['class' => 'tw-inline-block']); ?>
                                    <?php echo form_hidden('status', 'planned'); ?>
                                    <button type="submit" class="btn btn-default btn-sm"><?php echo e(_l('disowebs_ops_reset')); ?></button>
                                    <?php echo form_close(); ?>
                                    <?php } ?>

                                    <?php if ($can_manage_milestones) { ?>
                                    <a class="btn btn-default btn-sm" data-toggle="collapse" href="#milestone-edit-<?php echo e($milestone['id']); ?>">
                                        <?php echo e(_l('disowebs_ops_edit')); ?>
                                    </a>
                                    <?php } ?>

                                    <?php if ($can_delete_milestones) { ?>
                                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/delete_milestone/' . $project->id . '/' . $milestone['id']), ['class' => 'tw-inline-block']); ?>
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo e(_l('disowebs_ops_confirm_delete')); ?>');">
                                        <?php echo e(_l('disowebs_ops_delete')); ?>
                                    </button>
                                    <?php echo form_close(); ?>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                            </div>

                            <?php if ($can_manage_milestones) { ?>
                            <div id="milestone-edit-<?php echo e($milestone['id']); ?>" class="collapse mtop15">
                                <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/update_milestone/' . $project->id . '/' . $milestone['id'])); ?>
                                <?php echo render_input('title', 'disowebs_ops_milestone_title', $milestone['title']); ?>
                                <?php echo render_textarea('description', 'disowebs_ops_milestone_description', $milestone['description'], ['rows' => 3]); ?>
                                <div class="form-group">
                                    <label for="milestone_phase_<?php echo e($milestone['id']); ?>"><?php echo e(_l('disowebs_ops_phase_optional')); ?></label>
                                    <select class="form-control" id="milestone_phase_<?php echo e($milestone['id']); ?>" name="phase_id">
                                        <option value=""><?php echo e(_l('disowebs_ops_no_phase')); ?></option>
                                        <?php foreach ($phases as $phase) { ?>
                                        <option value="<?php echo e($phase['id']); ?>" <?php echo (int) $milestone['phase_id'] === (int) $phase['id'] ? 'selected' : ''; ?>><?php echo e($phase['name']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group" app-field-wrapper="week_start">
                                    <label for="week_start_<?php echo e($milestone['id']); ?>_unassigned" class="control-label"><?php echo e(_l('disowebs_ops_week_start')); ?></label>
                                    <div class="input-group date">
                                        <input type="text" id="week_start_<?php echo e($milestone['id']); ?>_unassigned" name="week_start" class="form-control datepicker" value="<?php echo e(_d($milestone['week_start'])); ?>" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="fa-regular fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" app-field-wrapper="week_end">
                                    <label for="week_end_<?php echo e($milestone['id']); ?>_unassigned" class="control-label"><?php echo e(_l('disowebs_ops_week_end')); ?></label>
                                    <div class="input-group date">
                                        <input type="text" id="week_end_<?php echo e($milestone['id']); ?>_unassigned" name="week_end" class="form-control datepicker" value="<?php echo e(_d($milestone['week_end'])); ?>" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="fa-regular fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="milestone_status_<?php echo e($milestone['id']); ?>"><?php echo e(_l('disowebs_ops_status')); ?></label>
                                    <select class="form-control" id="milestone_status_<?php echo e($milestone['id']); ?>" name="status">
                                        <?php foreach ($milestone_statuses as $key => $meta) { ?>
                                        <option value="<?php echo e($key); ?>" <?php echo $milestone['status'] === $key ? 'selected' : ''; ?>><?php echo e($meta['label']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_save_milestone')); ?></button>
                                <?php echo form_close(); ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
