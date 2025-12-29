<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$CI = &get_instance();
$CI->load->model('disowebs_ops/dw_project_gates_model');
$CI->load->model('disowebs_ops/dw_milestone_blockers_model');
$CI->load->model('disowebs_ops/dw_weekly_demos_model');
$CI->load->model('disowebs_ops/dw_retainer_offers_model');
$CI->load->model('disowebs_ops/dw_testimonials_model');

// Ensure gate record exists
$CI->dw_project_gates_model->ensure_exists($project->id);
$CI->dw_project_gates_model->calculate_deposit_status($project->id);
$CI->dw_project_gates_model->calculate_final_payment_status($project->id);

$gate_status = disowebs_ops_get_project_gate_status($project->id);
$blockers = $CI->dw_milestone_blockers_model->get_unresolved_by_project($project->id);
$demo_status = disowebs_ops_get_project_demo_status($project->id);
$retainer_status = disowebs_ops_get_project_retainer_status($project->id);
$testimonial_status = disowebs_ops_get_project_testimonial_status($project->id);

$can_manage = has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage');
$is_ceo = disowebs_ops_is_ceo();

$currency = function_exists('get_base_currency') ? get_base_currency() : null;
$currency_name = $currency && isset($currency->name) ? $currency->name : '';

// Get staff for blocker owner dropdown
$CI->load->model('staff_model');
$staff_members = $CI->staff_model->get('', ['active' => 1]);
?>

<div class="panel_s">
    <div class="panel-body">
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
            <div>
                <h4 class="no-margin"><?php echo e(_l('disowebs_ops_project_gates')); ?></h4>
                <p class="text-muted mtop5"><?php echo e(_l('disowebs_ops_deposit_gate_desc')); ?></p>
            </div>
            <?php if (!$can_manage) { ?>
            <span class="label label-default mtop10"><?php echo e(_l('disowebs_ops_view_only')); ?></span>
            <?php } ?>
        </div>

        <!-- Payment Gates -->
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4 mtop15">
            <!-- Deposit Gate -->
            <div class="panel_s <?php echo $gate_status['deposit_cleared'] ? 'panel-success' : 'panel-warning'; ?>">
                <div class="panel-body">
                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <div class="text-muted"><?php echo e(_l('disowebs_ops_deposit_gate')); ?></div>
                        <?php if ($gate_status['deposit_cleared']) { ?>
                        <span class="label label-success tw-self-start"><?php echo e(_l('disowebs_ops_deposit_cleared')); ?></span>
                        <?php } else { ?>
                        <span class="label label-warning tw-self-start"><?php echo e(_l('disowebs_ops_deposit_pending')); ?></span>
                        <?php } ?>
                    </div>
                    <div class="mtop10">
                        <div class="progress" style="height:8px; margin-bottom:5px;">
                            <div class="progress-bar progress-bar-<?php echo $gate_status['deposit_cleared'] ? 'success' : 'warning'; ?>" 
                                 style="width:<?php echo e($gate_status['deposit_percent']); ?>%"></div>
                        </div>
                        <small class="text-muted">
                            <?php echo sprintf(
                                _l('disowebs_ops_deposit_progress'),
                                $gate_status['deposit_percent'],
                                app_format_money($gate_status['deposit_paid'], $currency_name),
                                app_format_money($gate_status['deposit_required'], $currency_name)
                            ); ?>
                        </small>
                    </div>
                    <?php if ($is_ceo && !$gate_status['deposit_cleared']) { ?>
                    <div class="mtop10">
                        <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/clear_deposit_gate/' . $project->id)); ?>
                        <button type="submit" class="btn btn-xs btn-success"><?php echo e(_l('disowebs_ops_clear_deposit_gate')); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Final Payment Gate -->
            <div class="panel_s <?php echo $gate_status['final_payment_cleared'] ? 'panel-success' : 'panel-default'; ?>">
                <div class="panel-body">
                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <div class="text-muted"><?php echo e(_l('disowebs_ops_final_payment_gate')); ?></div>
                        <?php if ($gate_status['final_payment_cleared']) { ?>
                        <span class="label label-success tw-self-start"><?php echo e(_l('disowebs_ops_final_payment_cleared')); ?></span>
                        <?php } else { ?>
                        <span class="label label-default tw-self-start"><?php echo e(_l('disowebs_ops_final_payment_pending')); ?></span>
                        <?php } ?>
                    </div>
                    <div class="mtop10">
                        <div class="progress" style="height:8px; margin-bottom:5px;">
                            <div class="progress-bar progress-bar-<?php echo $gate_status['final_payment_cleared'] ? 'success' : 'info'; ?>" 
                                 style="width:<?php echo e($gate_status['final_payment_percent']); ?>%"></div>
                        </div>
                        <small class="text-muted">
                            <?php echo sprintf(
                                _l('disowebs_ops_deposit_progress'),
                                $gate_status['final_payment_percent'],
                                app_format_money($gate_status['final_payment_paid'], $currency_name),
                                app_format_money($gate_status['final_payment_required'], $currency_name)
                            ); ?>
                        </small>
                    </div>
                    <?php if ($is_ceo && !$gate_status['final_payment_cleared']) { ?>
                    <div class="mtop10">
                        <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/clear_final_gate/' . $project->id)); ?>
                        <button type="submit" class="btn btn-xs btn-success"><?php echo e(_l('disowebs_ops_clear_final_gate')); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Handover Gate -->
            <div class="panel_s <?php echo $gate_status['handover_released'] ? 'panel-success' : 'panel-default'; ?>">
                <div class="panel-body">
                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <div class="text-muted"><?php echo e(_l('disowebs_ops_handover_gate')); ?></div>
                        <?php if ($gate_status['handover_released']) { ?>
                        <span class="label label-success tw-self-start"><?php echo e(_l('disowebs_ops_handover_released')); ?></span>
                        <?php } elseif (!$gate_status['final_payment_cleared']) { ?>
                        <span class="label label-danger tw-self-start"><?php echo e(_l('disowebs_ops_handover_blocked')); ?></span>
                        <?php } else { ?>
                        <span class="label label-default tw-self-start"><?php echo e(_l('disowebs_ops_final_payment_pending')); ?></span>
                        <?php } ?>
                    </div>
                    <?php if ($is_ceo && $gate_status['final_payment_cleared'] && !$gate_status['handover_released']) { ?>
                    <div class="mtop10">
                        <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/release_handover/' . $project->id)); ?>
                        <button type="submit" class="btn btn-xs btn-success"><?php echo e(_l('disowebs_ops_release_handover')); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Training Gate -->
            <div class="panel_s <?php echo $gate_status['training_completed'] ? 'panel-success' : 'panel-default'; ?>">
                <div class="panel-body">
                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <div class="text-muted"><?php echo e(_l('disowebs_ops_training_gate')); ?></div>
                        <?php if ($gate_status['training_completed']) { ?>
                        <span class="label label-success tw-self-start"><?php echo e(_l('disowebs_ops_training_completed')); ?></span>
                        <?php } else { ?>
                        <span class="label label-default tw-self-start"><?php echo e(_l('disowebs_ops_training_pending')); ?></span>
                        <?php } ?>
                    </div>
                    <?php if ($can_manage && !$gate_status['training_completed']) { ?>
                    <div class="mtop10">
                        <a class="btn btn-xs btn-default" data-toggle="collapse" href="#training-form">
                            <?php echo e(_l('disowebs_ops_mark_training_completed')); ?>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php if ($can_manage && !$gate_status['training_completed']) { ?>
        <div id="training-form" class="collapse mtop15">
            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/mark_training_completed/' . $project->id)); ?>
            <?php echo render_textarea('training_notes', 'disowebs_ops_training_notes', '', ['rows' => 3]); ?>
            <button type="submit" class="btn btn-primary btn-sm"><?php echo e(_l('disowebs_ops_mark_training_completed')); ?></button>
            <?php echo form_close(); ?>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Blockers Section -->
<div class="panel_s mtop15">
    <div class="panel-body">
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
            <div>
                <h4 class="no-margin"><?php echo e(_l('disowebs_ops_blockers')); ?></h4>
                <p class="text-muted mtop5"><?php echo sprintf(_l('disowebs_ops_blockers_count'), count($blockers)); ?></p>
            </div>
            <?php if ($can_manage) { ?>
            <a class="btn btn-primary btn-sm mtop10 md:mtop0" data-toggle="collapse" href="#add-blocker-form">
                <i class="fa fa-plus"></i> <?php echo e(_l('disowebs_ops_add_blocker')); ?>
            </a>
            <?php } ?>
        </div>

        <?php if ($can_manage) { ?>
        <div id="add-blocker-form" class="collapse mtop15">
            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/add_blocker/' . $project->id)); ?>
            <input type="hidden" name="milestone_id" value="">
            <?php echo render_textarea('description', 'disowebs_ops_blocker_description', '', ['rows' => 3, 'required' => true]); ?>
            <div class="row">
                <div class="col-md-4">
                    <?php echo render_select('owner_staff_id', $staff_members, ['staffid', ['firstname', 'lastname']], 'disowebs_ops_blocker_owner'); ?>
                </div>
                <div class="col-md-4">
                    <?php echo render_input('next_action', 'disowebs_ops_blocker_next_action'); ?>
                </div>
                <div class="col-md-4">
                    <?php echo render_date_input('next_action_date', 'disowebs_ops_blocker_next_action_date'); ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo e(_l('submit')); ?></button>
            <?php echo form_close(); ?>
        </div>
        <?php } ?>

        <?php if (empty($blockers)) { ?>
        <p class="text-muted mtop15"><?php echo e(_l('disowebs_ops_no_blockers')); ?></p>
        <?php } else { ?>
        <div class="table-responsive mtop15">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo e(_l('disowebs_ops_blocker_description')); ?></th>
                        <th><?php echo e(_l('disowebs_ops_blocker_owner')); ?></th>
                        <th><?php echo e(_l('disowebs_ops_blocker_next_action')); ?></th>
                        <th><?php echo e(_l('disowebs_ops_blocker_next_action_date')); ?></th>
                        <th><?php echo e(_l('options')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blockers as $blocker) { ?>
                    <?php 
                        $is_overdue = !empty($blocker['next_action_date']) && strtotime($blocker['next_action_date']) < strtotime('today');
                    ?>
                    <tr class="<?php echo $is_overdue ? 'danger' : ''; ?>">
                        <td><?php echo nl2br(e($blocker['description'])); ?></td>
                        <td>
                            <?php if (!empty($blocker['owner_staff_id'])) { ?>
                            <?php echo e(get_staff_full_name($blocker['owner_staff_id'])); ?>
                            <?php } else { ?>
                            <span class="text-muted">-</span>
                            <?php } ?>
                        </td>
                        <td><?php echo e($blocker['next_action'] ?: '-'); ?></td>
                        <td>
                            <?php if (!empty($blocker['next_action_date'])) { ?>
                            <?php echo e(_d($blocker['next_action_date'])); ?>
                            <?php if ($is_overdue) { ?>
                            <span class="label label-danger"><?php echo e(_l('overdue')); ?></span>
                            <?php } ?>
                            <?php } else { ?>
                            <span class="text-muted">-</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($can_manage) { ?>
                            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/resolve_blocker/' . $project->id . '/' . $blocker['id']), ['class' => 'tw-inline-block']); ?>
                            <button type="submit" class="btn btn-xs btn-success"><?php echo e(_l('disowebs_ops_resolve_blocker')); ?></button>
                            <?php echo form_close(); ?>
                            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/delete_blocker/' . $project->id . '/' . $blocker['id']), ['class' => 'tw-inline-block']); ?>
                            <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('<?php echo e(_l('disowebs_ops_confirm_delete')); ?>');">
                                <?php echo e(_l('disowebs_ops_delete')); ?>
                            </button>
                            <?php echo form_close(); ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Weekly Demo Section -->
<div class="panel_s mtop15">
    <div class="panel-body">
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
            <div>
                <h4 class="no-margin"><?php echo e(_l('disowebs_ops_weekly_demo')); ?></h4>
                <p class="text-muted mtop5"><?php echo e(disowebs_ops_get_current_week_range()['week_start'] . ' - ' . disowebs_ops_get_current_week_range()['week_end']); ?></p>
            </div>
            <div class="tw-flex tw-items-center tw-gap-2 mtop10 md:mtop0">
                <?php if ($demo_status['completed']) { ?>
                <span class="label label-success"><?php echo e(_l('disowebs_ops_demo_completed')); ?></span>
                <?php } elseif ($demo_status['scheduled']) { ?>
                <span class="label label-info"><?php echo e(_l('disowebs_ops_demo_scheduled')); ?></span>
                <?php } else { ?>
                <span class="label label-warning"><?php echo e(_l('disowebs_ops_demo_not_scheduled')); ?></span>
                <?php } ?>
            </div>
        </div>

        <?php if ($can_manage && !$demo_status['scheduled']) { ?>
        <div class="mtop15">
            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/schedule_demo/' . $project->id)); ?>
            <div class="row">
                <div class="col-md-4">
                    <?php echo render_datetime_input('demo_date', 'disowebs_ops_demo_date', '', ['required' => true]); ?>
                </div>
                <div class="col-md-8">
                    <div class="form-group" style="margin-top:26px;">
                        <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_schedule_demo')); ?></button>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
        <?php } ?>

        <?php if ($demo_status['scheduled'] && !$demo_status['completed'] && $can_manage) { ?>
        <div class="mtop15">
            <a class="btn btn-success btn-sm" data-toggle="collapse" href="#complete-demo-form">
                <?php echo e(_l('disowebs_ops_mark_demo_completed')); ?>
            </a>
        </div>
        <div id="complete-demo-form" class="collapse mtop15">
            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/complete_demo/' . $project->id)); ?>
            <?php echo render_textarea('attendees', 'disowebs_ops_demo_attendees', '', ['rows' => 2]); ?>
            <?php echo render_textarea('notes', 'disowebs_ops_demo_notes', '', ['rows' => 2]); ?>
            <?php echo render_textarea('feedback', 'disowebs_ops_demo_feedback', '', ['rows' => 2]); ?>
            <button type="submit" class="btn btn-success"><?php echo e(_l('disowebs_ops_mark_demo_completed')); ?></button>
            <?php echo form_close(); ?>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Retention Quick Status -->
<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4 mtop15">
    <!-- Retainer Status -->
    <div class="panel_s">
        <div class="panel-body">
            <h4 class="no-margin"><?php echo e(_l('disowebs_ops_retainer_offer')); ?></h4>
            <div class="mtop10">
                <?php if ($retainer_status['accepted']) { ?>
                <span class="label label-success"><?php echo e(_l('disowebs_ops_retainer_status_accepted')); ?></span>
                <?php } elseif ($retainer_status['declined']) { ?>
                <span class="label label-danger"><?php echo e(_l('disowebs_ops_retainer_status_declined')); ?></span>
                <?php } elseif ($retainer_status['offered']) { ?>
                <span class="label label-info"><?php echo e(_l('disowebs_ops_retainer_status_offered')); ?></span>
                <?php } else { ?>
                <span class="label label-default"><?php echo e(_l('disowebs_ops_retainer_status_pending')); ?></span>
                <?php if ($can_manage) { ?>
                <div class="mtop10">
                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/create_retainer_offer/' . $project->id)); ?>
                    <button type="submit" class="btn btn-xs btn-primary"><?php echo e(_l('disowebs_ops_create_retainer_offer')); ?></button>
                    <?php echo form_close(); ?>
                </div>
                <?php } ?>
                <?php } ?>
            </div>
            <?php if ($retainer_status['days_since_launch'] > 0) { ?>
            <div class="text-muted mtop5">
                <?php echo _l('disowebs_ops_days_since_launch'); ?>: <?php echo e($retainer_status['days_since_launch']); ?>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- Testimonial Status -->
    <div class="panel_s">
        <div class="panel-body">
            <h4 class="no-margin"><?php echo e(_l('disowebs_ops_testimonial')); ?></h4>
            <div class="mtop10">
                <?php if ($testimonial_status['approved']) { ?>
                <span class="label label-success"><?php echo e(_l('disowebs_ops_testimonial_status_approved')); ?></span>
                <span class="text-muted">(<?php echo e($testimonial_status['approved_count']); ?>)</span>
                <?php } elseif ($testimonial_status['received']) { ?>
                <span class="label label-warning"><?php echo e(_l('disowebs_ops_testimonial_awaiting_approval')); ?></span>
                <span class="text-muted">(<?php echo e($testimonial_status['received_count']); ?>)</span>
                <?php } elseif ($testimonial_status['requested']) { ?>
                <span class="label label-info"><?php echo e(_l('disowebs_ops_testimonial_pending')); ?></span>
                <?php } else { ?>
                <span class="label label-default"><?php echo e(_l('disowebs_ops_no_testimonials')); ?></span>
                <?php if ($can_manage && !empty($project->clientid)) { ?>
                <div class="mtop10">
                    <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/request_testimonial/' . $project->id)); ?>
                    <button type="submit" class="btn btn-xs btn-primary"><?php echo e(_l('disowebs_ops_request_testimonial')); ?></button>
                    <?php echo form_close(); ?>
                </div>
                <?php } ?>
                <?php } ?>
            </div>

            <?php if (!empty($testimonial_status['testimonials'])) { ?>
            <div class="mtop15">
                <?php foreach ($testimonial_status['testimonials'] as $testimonial) { ?>
                <div class="tw-border tw-border-solid tw-border-neutral-200 tw-rounded-md tw-p-3 tw-mb-3">
                    <div class="tw-flex tw-items-start tw-justify-between tw-flex-wrap tw-gap-2">
                        <div class="tw-flex-1">
                            <?php if ($testimonial['status'] === 'requested') { ?>
                            <span class="label label-info"><?php echo e(_l('disowebs_ops_testimonial_pending')); ?></span>
                            <span class="text-muted tw-text-sm tw-ml-2"><?php echo _l('disowebs_ops_requested'); ?>: <?php echo _dt($testimonial['requested_at']); ?></span>
                            <?php } elseif ($testimonial['status'] === 'received') { ?>
                            <span class="label label-warning"><?php echo e(_l('disowebs_ops_testimonial_awaiting_approval')); ?></span>
                            <?php if (!empty($testimonial['testimonial_text'])) { ?>
                            <div class="mtop10 tw-bg-neutral-50 tw-p-3 tw-rounded tw-italic tw-text-neutral-600">
                                "<?php echo e($testimonial['testimonial_text']); ?>"
                            </div>
                            <?php } ?>
                            <?php if (!empty($testimonial['rating'])) { ?>
                            <div class="mtop5">
                                <?php for ($i = 1; $i <= 5; $i++) { ?>
                                <i class="fa fa-star" style="color: <?php echo $i <= $testimonial['rating'] ? '#f59e0b' : '#d1d5db'; ?>;"></i>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            <?php if ($is_ceo) { ?>
                            <div class="mtop10 tw-flex tw-gap-2 tw-flex-wrap">
                                <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#approve-testimonial-<?php echo $testimonial['id']; ?>">
                                    <i class="fa fa-check"></i> <?php echo e(_l('disowebs_ops_approve_testimonial')); ?>
                                </button>
                                <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#reject-testimonial-<?php echo $testimonial['id']; ?>">
                                    <i class="fa fa-times"></i> <?php echo e(_l('disowebs_ops_reject_testimonial')); ?>
                                </button>
                            </div>
                            <?php } ?>
                            <?php } elseif ($testimonial['status'] === 'approved') { ?>
                            <span class="label label-success"><?php echo e(_l('disowebs_ops_testimonial_status_approved')); ?></span>
                            <?php if (!empty($testimonial['testimonial_text'])) { ?>
                            <div class="mtop10 tw-bg-green-50 tw-p-3 tw-rounded tw-italic tw-text-neutral-600">
                                "<?php echo e($testimonial['testimonial_text']); ?>"
                            </div>
                            <?php } ?>
                            <?php if (!empty($testimonial['rating'])) { ?>
                            <div class="mtop5">
                                <?php for ($i = 1; $i <= 5; $i++) { ?>
                                <i class="fa fa-star" style="color: <?php echo $i <= $testimonial['rating'] ? '#f59e0b' : '#d1d5db'; ?>;"></i>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            <div class="mtop5 text-muted tw-text-sm">
                                <?php echo e(_l('disowebs_ops_approved_by')); ?>: <?php echo get_staff_full_name($testimonial['approved_by']); ?> • <?php echo _dt($testimonial['approved_at']); ?>
                            </div>
                            <?php } elseif ($testimonial['status'] === 'rejected') { ?>
                            <span class="label label-danger"><?php echo e(_l('disowebs_ops_testimonial_status_rejected')); ?></span>
                            <?php if (!empty($testimonial['approval_notes'])) { ?>
                            <div class="mtop10 tw-text-sm text-muted">
                                <strong><?php echo e(_l('disowebs_ops_rejection_notes')); ?>:</strong> <?php echo e($testimonial['approval_notes']); ?>
                            </div>
                            <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <?php if ($testimonial['status'] === 'received' && $is_ceo) { ?>
                <!-- Approve Modal -->
                <div class="modal fade" id="approve-testimonial-<?php echo $testimonial['id']; ?>" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/approve_testimonial/' . $project->id . '/' . $testimonial['id'])); ?>
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h4 class="modal-title"><?php echo e(_l('disowebs_ops_approve_testimonial')); ?></h4>
                            </div>
                            <div class="modal-body">
                                <?php if (!empty($testimonial['testimonial_text'])) { ?>
                                <div class="tw-bg-neutral-50 tw-p-3 tw-rounded tw-italic tw-text-neutral-600 tw-mb-4">
                                    "<?php echo e($testimonial['testimonial_text']); ?>"
                                </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label><?php echo e(_l('disowebs_ops_approval_notes')); ?> <small class="text-muted">(<?php echo e(_l('optional')); ?>)</small></label>
                                    <textarea name="approval_notes" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo e(_l('close')); ?></button>
                                <button type="submit" class="btn btn-success"><?php echo e(_l('disowebs_ops_approve_testimonial')); ?></button>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div class="modal fade" id="reject-testimonial-<?php echo $testimonial['id']; ?>" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/reject_testimonial/' . $project->id . '/' . $testimonial['id'])); ?>
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                <h4 class="modal-title"><?php echo e(_l('disowebs_ops_reject_testimonial')); ?></h4>
                            </div>
                            <div class="modal-body">
                                <?php if (!empty($testimonial['testimonial_text'])) { ?>
                                <div class="tw-bg-neutral-50 tw-p-3 tw-rounded tw-italic tw-text-neutral-600 tw-mb-4">
                                    "<?php echo e($testimonial['testimonial_text']); ?>"
                                </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label><?php echo e(_l('disowebs_ops_rejection_notes')); ?></label>
                                    <textarea name="rejection_notes" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo e(_l('close')); ?></button>
                                <button type="submit" class="btn btn-danger"><?php echo e(_l('disowebs_ops_reject_testimonial')); ?></button>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Adoption Baseline -->
<?php
$CI->load->model('disowebs_ops/dw_adoption_baseline_model');
$adoption = $CI->dw_adoption_baseline_model->get_by_project($project->id);
$adoption_score = $adoption ? (int) $adoption->adoption_score : 0;
$adoption_status = $adoption ? $CI->dw_adoption_baseline_model->get_status_label($adoption_score) : 'pending';
$adoption_class = $adoption ? $CI->dw_adoption_baseline_model->get_status_class($adoption_score) : 'default';
?>
<div class="panel_s mtop15">
    <div class="panel-body">
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
            <div>
                <h4 class="no-margin"><?php echo e(_l('disowebs_ops_adoption_baseline')); ?></h4>
                <p class="text-muted mtop5"><?php echo e(_l('disowebs_ops_adoption_baseline_desc')); ?></p>
            </div>
            <?php if (!$adoption && $can_manage && !empty($project->clientid)) { ?>
            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/capture_adoption_baseline/' . $project->id), ['class' => 'mtop10 md:mtop0']); ?>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa fa-chart-line"></i> <?php echo e(_l('disowebs_ops_capture_baseline')); ?>
            </button>
            <?php echo form_close(); ?>
            <?php } ?>
        </div>

        <?php if ($adoption) { ?>
        <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-5 tw-gap-4 mtop15">
            <div class="text-center">
                <div class="tw-text-2xl tw-font-bold text-<?php echo $adoption_class; ?>"><?php echo e($adoption_score); ?></div>
                <small class="text-muted"><?php echo e(_l('disowebs_ops_adoption_score')); ?></small>
            </div>
            <div class="text-center">
                <div class="tw-text-2xl tw-font-bold"><?php echo e($adoption->logins_count ?? 0); ?></div>
                <small class="text-muted"><?php echo e(_l('disowebs_ops_logins_count')); ?></small>
            </div>
            <div class="text-center">
                <div class="tw-text-2xl tw-font-bold"><?php echo e($adoption->training_sessions ?? 0); ?></div>
                <small class="text-muted"><?php echo e(_l('disowebs_ops_training_sessions')); ?></small>
            </div>
            <div class="text-center">
                <div class="tw-text-2xl tw-font-bold"><?php echo e($adoption->support_tickets ?? 0); ?></div>
                <small class="text-muted"><?php echo e(_l('disowebs_ops_support_tickets_opened')); ?></small>
            </div>
            <div class="text-center">
                <span class="label label-<?php echo $adoption_class; ?> tw-text-sm">
                    <?php echo e(_l('disowebs_ops_adoption_' . $adoption_status)); ?>
                </span>
            </div>
        </div>

        <?php if ($can_manage) { ?>
        <div class="mtop15">
            <a class="btn btn-default btn-sm" data-toggle="collapse" href="#update-adoption-form">
                <i class="fa fa-edit"></i> <?php echo e(_l('disowebs_ops_update_baseline')); ?>
            </a>
        </div>
        <div id="update-adoption-form" class="collapse mtop15">
            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/update_adoption_baseline/' . $project->id)); ?>
            <div class="row">
                <div class="col-md-3">
                    <?php echo render_input('logins_count', 'disowebs_ops_logins_count', $adoption->logins_count ?? 0, 'number', ['min' => 0]); ?>
                </div>
                <div class="col-md-3">
                    <?php echo render_input('training_sessions', 'disowebs_ops_training_sessions', $adoption->training_sessions ?? 0, 'number', ['min' => 0]); ?>
                </div>
                <div class="col-md-3">
                    <?php echo render_input('support_tickets', 'disowebs_ops_support_tickets_opened', $adoption->support_tickets ?? 0, 'number', ['min' => 0]); ?>
                </div>
                <div class="col-md-3">
                    <?php echo render_input('features_used', 'disowebs_ops_features_used', $adoption->features_used ?? '', 'text', ['placeholder' => 'e.g., invoicing, projects, tickets']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php echo render_textarea('notes', 'notes', $adoption->notes ?? '', ['rows' => 2]); ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo e(_l('submit')); ?></button>
            <?php echo form_close(); ?>
        </div>
        <?php } ?>
        <?php } else { ?>
        <div class="alert alert-info mtop15">
            <?php echo e(_l('disowebs_ops_no_adoption_baseline')); ?>
        </div>
        <?php } ?>
    </div>
</div>
