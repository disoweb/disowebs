<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$CI = &get_instance();
$CI->load->model('disowebs_ops/dw_scope_snapshots_model');
$CI->load->model('estimates_model');
$CI->load->model('proposals_model');

$can_snapshot = has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'approve_change_requests');

$snapshots = $CI->dw_scope_snapshots_model->get_by_project($project->id);
$latest_snapshot = !empty($snapshots) ? $snapshots[0] : null;
$latest_data = $latest_snapshot ? json_decode($latest_snapshot['snapshot_json'], true) : null;

$accepted_estimates = $CI->estimates_model->get('', [
    'project_id' => $project->id,
    'status' => 4,
]);
$accepted_proposals = $CI->proposals_model->get('', [
    'project_id' => $project->id,
    'status' => 3,
]);

$has_sources = !empty($accepted_estimates) || !empty($accepted_proposals);
?>

<div class="panel_s">
    <div class="panel-body">
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
            <div>
                <h4 class="no-margin"><?php echo e(_l('disowebs_ops_tab_scope')); ?></h4>
                <p class="text-muted mtop5"><?php echo e(_l('disowebs_ops_scope_intro')); ?></p>
            </div>
            <?php if (!$can_snapshot) { ?>
            <span class="label label-default mtop10"><?php echo e(_l('disowebs_ops_scope_view_only')); ?></span>
            <?php } ?>
        </div>

        <div class="alert alert-info mtop15">
            <?php echo e(_l('disowebs_ops_scope_cr_policy')); ?>
        </div>

        <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-2 tw-gap-4 mtop15">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between">
                        <h4 class="no-margin"><?php echo e(_l('disowebs_ops_scope_latest')); ?></h4>
                        <?php if ($latest_snapshot) { ?>
                        <span class="label label-info"><?php echo e(_dt($latest_snapshot['created_at'])); ?></span>
                        <?php } ?>
                    </div>

                    <?php if (!$latest_snapshot || !$latest_data) { ?>
                    <p class="text-muted mtop15"><?php echo e(_l('disowebs_ops_scope_no_snapshots')); ?></p>
                    <?php } else { ?>
                    <div class="mtop15">
                        <div class="text-muted"><?php echo e(_l('disowebs_ops_scope_source')); ?></div>
                        <div class="tw-flex tw-items-center tw-flex-wrap tw-gap-2 mtop5">
                            <span class="label label-primary"><?php echo e(ucfirst($latest_data['source_type'])); ?></span>
                            <span class="text-muted"><?php echo e($latest_data['number'] ?? ('#' . $latest_data['source_id'])); ?></span>
                        </div>

                        <?php if (!empty($latest_data['client_name'])) { ?>
                        <div class="mtop10 text-muted"><?php echo e(_l('disowebs_ops_scope_client')); ?>: <?php echo e($latest_data['client_name']); ?></div>
                        <?php } ?>

                        <?php if (isset($latest_data['total'])) { ?>
                        <div class="mtop10">
                            <strong><?php echo e(_l('disowebs_ops_scope_total')); ?>:</strong>
                            <?php echo e(app_format_money($latest_data['total'], $latest_data['currency_name'] ?? '')); ?>
                        </div>
                        <?php } ?>

                        <?php if (!empty($latest_data['items'])) { ?>
                        <div class="mtop15">
                            <div class="text-muted"><?php echo e(_l('disowebs_ops_scope_items_summary', count($latest_data['items']))); ?></div>
                            <ul class="list-unstyled mtop5">
                                <?php foreach (array_slice($latest_data['items'], 0, 3) as $item) { ?>
                                <li>
                                    <i class="fa fa-check text-success"></i>
                                    <?php echo e($item['description']); ?>
                                </li>
                                <?php } ?>
                                <?php if (count($latest_data['items']) > 3) { ?>
                                <li class="text-muted"><?php echo e(_l('disowebs_ops_scope_items_more', count($latest_data['items']) - 3)); ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="panel_s">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between">
                        <h4 class="no-margin"><?php echo e(_l('disowebs_ops_scope_create')); ?></h4>
                        <?php if (!$can_snapshot) { ?>
                        <span class="label label-default"><?php echo e(_l('disowebs_ops_scope_approval_required')); ?></span>
                        <?php } ?>
                    </div>
                    <p class="text-muted mtop10"><?php echo e(_l('disowebs_ops_scope_create_hint')); ?></p>

                    <?php if (!$has_sources) { ?>
                    <p class="text-muted mtop15"><?php echo e(_l('disowebs_ops_scope_no_sources')); ?></p>
                    <?php } else { ?>
                        <?php if ($can_snapshot) { ?>
                        <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/create_scope_snapshot/' . $project->id)); ?>
                        <div class="form-group">
                            <label for="source_ref"><?php echo e(_l('disowebs_ops_scope_source_select')); ?></label>
                            <select class="form-control" name="source_ref" id="source_ref" required>
                                <option value=""><?php echo e(_l('disowebs_ops_scope_source_choose')); ?></option>
                                <?php if (!empty($accepted_estimates)) { ?>
                                <optgroup label="<?php echo e(_l('disowebs_ops_scope_estimates')); ?>">
                                    <?php foreach ($accepted_estimates as $estimate) { ?>
                                    <option value="estimate:<?php echo e($estimate['id']); ?>">
                                        <?php echo e(format_estimate_number($estimate['id'])); ?>
                                        - <?php echo e(app_format_money($estimate['total'], $estimate['currency_name'] ?? '')); ?>
                                    </option>
                                    <?php } ?>
                                </optgroup>
                                <?php } ?>
                                <?php if (!empty($accepted_proposals)) { ?>
                                <optgroup label="<?php echo e(_l('disowebs_ops_scope_proposals')); ?>">
                                    <?php foreach ($accepted_proposals as $proposal) { ?>
                                    <option value="proposal:<?php echo e($proposal['id']); ?>">
                                        <?php echo e(format_proposal_number($proposal['id'])); ?>
                                        - <?php echo e(app_format_money($proposal['total'], $proposal['currency_name'] ?? '')); ?>
                                    </option>
                                    <?php } ?>
                                </optgroup>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_scope_create_button')); ?></button>
                        <?php echo form_close(); ?>
                        <?php } else { ?>
                        <p class="text-muted mtop15"><?php echo e(_l('disowebs_ops_scope_access_note')); ?></p>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="panel_s mtop15">
            <div class="panel-body">
                <h4 class="no-margin"><?php echo e(_l('disowebs_ops_scope_history')); ?></h4>
                <?php if (empty($snapshots)) { ?>
                <p class="text-muted mtop10"><?php echo e(_l('disowebs_ops_scope_history_empty')); ?></p>
                <?php } else { ?>
                <div class="table-responsive mtop15">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?php echo e(_l('disowebs_ops_scope_snapshot_date')); ?></th>
                                <th><?php echo e(_l('disowebs_ops_scope_source')); ?></th>
                                <th><?php echo e(_l('disowebs_ops_scope_total')); ?></th>
                                <th><?php echo e(_l('disowebs_ops_scope_items')); ?></th>
                                <th><?php echo e(_l('disowebs_ops_scope_actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($snapshots as $snapshot) { ?>
                            <?php $data = json_decode($snapshot['snapshot_json'], true); ?>
                            <?php $items_count = isset($data['items']) && is_array($data['items']) ? count($data['items']) : 0; ?>
                            <tr>
                                <td><?php echo e(_dt($snapshot['created_at'])); ?></td>
                                <td>
                                    <span class="label label-primary"><?php echo e(ucfirst($snapshot['source_type'])); ?></span>
                                    <div class="text-muted mtop5"><?php echo e($data['number'] ?? ('#' . $snapshot['source_id'])); ?></div>
                                </td>
                                <td>
                                    <?php if (isset($data['total'])) { ?>
                                    <?php echo e(app_format_money($data['total'], $data['currency_name'] ?? '')); ?>
                                    <?php } else { ?>
                                    <span class="text-muted">--</span>
                                    <?php } ?>
                                </td>
                                <td><?php echo e($items_count); ?></td>
                                <td>
                                    <a data-toggle="collapse" href="#dw-scope-<?php echo e($snapshot['id']); ?>">
                                        <?php echo e(_l('disowebs_ops_scope_view_details')); ?>
                                    </a>
                                </td>
                            </tr>
                            <tr id="dw-scope-<?php echo e($snapshot['id']); ?>" class="collapse">
                                <td colspan="5">
                                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                                        <div>
                                            <h5 class="bold"><?php echo e(_l('disowebs_ops_scope_items')); ?></h5>
                                            <?php if ($items_count === 0) { ?>
                                            <p class="text-muted"><?php echo e(_l('disowebs_ops_scope_items_empty')); ?></p>
                                            <?php } else { ?>
                                            <ul class="list-unstyled">
                                                <?php foreach (array_slice($data['items'], 0, 6) as $item) { ?>
                                                <li>
                                                    <strong><?php echo e($item['description']); ?></strong>
                                                    <?php if (!empty($item['long_description'])) { ?>
                                                    <div class="text-muted"><?php echo e($item['long_description']); ?></div>
                                                    <?php } ?>
                                                </li>
                                                <?php } ?>
                                                <?php if ($items_count > 6) { ?>
                                                <li class="text-muted"><?php echo e(_l('disowebs_ops_scope_items_more', $items_count - 6)); ?></li>
                                                <?php } ?>
                                            </ul>
                                            <?php } ?>
                                        </div>
                                        <div>
                                            <h5 class="bold"><?php echo e(_l('disowebs_ops_scope_notes')); ?></h5>
                                            <?php if (!empty($data['terms'])) { ?>
                                            <div class="text-muted mbot10"><?php echo e(_l('disowebs_ops_scope_terms')); ?></div>
                                            <div class="text-muted"><?php echo e(strip_tags($data['terms'])); ?></div>
                                            <?php } else { ?>
                                            <p class="text-muted"><?php echo e(_l('disowebs_ops_scope_notes_empty')); ?></p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
