<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$status_labels = [
    'draft' => ['label' => _l('disowebs_ops_cr_status_draft'), 'class' => 'default'],
    'submitted' => ['label' => _l('disowebs_ops_cr_status_submitted'), 'class' => 'warning'],
    'approved' => ['label' => _l('disowebs_ops_cr_status_approved'), 'class' => 'success'],
    'rejected' => ['label' => _l('disowebs_ops_cr_status_rejected'), 'class' => 'danger'],
    'implemented' => ['label' => _l('disowebs_ops_cr_status_implemented'), 'class' => 'info'],
];
$currency = get_base_currency();

// Calculate stats
$cr_count = count($change_requests);
$pending_count = 0;
$approved_count = 0;
$total_impact_days = 0;
$total_impact_cost = 0;

foreach ($change_requests as $cr) {
    if ($cr['status'] === 'submitted') $pending_count++;
    if ($cr['status'] === 'approved') $approved_count++;
    $total_impact_days += (int)($cr['impact_days'] ?? 0);
    $total_impact_cost += (float)($cr['impact_cost'] ?? 0);
}
$snapshot_count = count($snapshots);
?>
<?php init_head(); ?>
<style>
/* Mobile-friendly DataTable controls - simple fix */
@media (max-width: 768px) {
    .dataTables_wrapper .row:first-child {
        display: block !important;
    }
    .dataTables_wrapper .row:first-child > div {
        width: 100% !important;
        float: none !important;
        display: block !important;
        padding: 5px 0 !important;
    }
    .dataTables_wrapper .dataTables_length {
        display: inline-block !important;
        float: left !important;
        margin-right: 10px !important;
    }
    .dataTables_wrapper .dt-buttons {
        display: inline-block !important;
        float: right !important;
    }
    .dataTables_wrapper .dataTables_filter {
        clear: both !important;
        float: none !important;
        text-align: left !important;
        padding-top: 10px !important;
    }
    .dataTables_wrapper .dataTables_filter label {
        display: block !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        width: 100% !important;
        margin-left: 0 !important;
        margin-top: 5px !important;
    }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        float: none !important;
        text-align: center !important;
        padding-top: 10px;
    }
}
</style>
<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="tw-mb-6">
            <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                <div>
                    <h4 class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mb-1"><?php echo e($title); ?></h4>
                    <p class="tw-text-neutral-500 tw-mb-0"><?php echo e(_l('disowebs_ops_scope_overview_intro')); ?></p>
                </div>
            </div>
        </div>

        <!-- KPI Stats -->
        <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 tw-gap-4 tw-mb-6">
            <!-- Total CRs -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_total_crs')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: #dbeafe;">
                            <i class="fa fa-exchange" style="color: #2563eb;"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold tw-text-neutral-800"><?php echo $cr_count; ?></div>
                </div>
            </div>

            <!-- Pending Approval -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_pending_approval')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: #fef3c7;">
                            <i class="fa fa-clock-o" style="color: #d97706;"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold" style="color: #d97706;"><?php echo $pending_count; ?></div>
                </div>
            </div>

            <!-- Approved -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_approved')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: #dcfce7;">
                            <i class="fa fa-check" style="color: #16a34a;"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold" style="color: #16a34a;"><?php echo $approved_count; ?></div>
                </div>
            </div>

            <!-- Total Impact Days -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_impact_days')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: #dbeafe;">
                            <i class="fa fa-calendar" style="color: #0284c7;"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold tw-text-neutral-800"><?php echo $total_impact_days; ?><span class="tw-text-lg tw-text-neutral-400">d</span></div>
                </div>
            </div>

            <!-- Impact Value -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_impact_value')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: #fee2e2;">
                            <i class="fa fa-dollar" style="color: #dc2626;"></i>
                        </span>
                    </div>
                    <div class="tw-text-2xl tw-font-bold tw-text-neutral-800"><?php echo disowebs_ops_format_compact_money($total_impact_cost, $currency); ?></div>
                </div>
            </div>

            <!-- Scope Snapshots Count -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_scope_recent_snapshots')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: #e0e7ff;">
                            <i class="fa fa-camera" style="color: #4f46e5;"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold tw-text-neutral-800"><?php echo $snapshot_count; ?></div>
                </div>
            </div>
        </div>

        <!-- Change Requests Table -->
        <div class="panel_s" style="overflow-x: auto;">
            <div class="panel-body" style="min-width: 100%; overflow: visible;">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-0">
                        <i class="fa fa-exchange text-warning tw-mr-2"></i>
                        <?php echo e(_l('disowebs_ops_scope_recent_crs')); ?>
                    </h5>
                </div>
                <?php if (empty($change_requests)) { ?>
                    <div class="tw-text-center tw-py-12">
                        <i class="fa fa-exchange tw-text-5xl tw-text-neutral-200 tw-mb-4"></i>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo e(_l('disowebs_ops_scope_recent_crs_empty')); ?></p>
                    </div>
                <?php } else { ?>
                    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table class="table dt-table" style="min-width: 700px; width: 100%;">
                            <thead>
                                <tr>
                                    <th><?php echo e(_l('disowebs_ops_project')); ?></th>
                                    <th><?php echo e(_l('disowebs_ops_cr_title')); ?></th>
                                    <th><?php echo e(_l('disowebs_ops_status')); ?></th>
                                    <th><?php echo e(_l('disowebs_ops_cr_impact_summary')); ?></th>
                                    <th><?php echo e(_l('disowebs_ops_created')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($change_requests as $change_request) { ?>
                                    <?php
                                    $status = $status_labels[$change_request['status']] ?? ['label' => ucfirst($change_request['status']), 'class' => 'default'];
                                    $creator = trim(($change_request['firstname'] ?? '') . ' ' . ($change_request['lastname'] ?? ''));
                                    $created = _dt($change_request['created_at']);
                                    if ($creator !== '') {
                                        $created .= ' • ' . $creator;
                                    }
                                    $project_url = admin_url('projects/view/' . $change_request['project_id'] . '?group=disowebs_ops_change_requests');
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e($project_url); ?>" class="tw-font-medium">
                                                <?php echo e($change_request['project_name'] ?: _l('disowebs_ops_unknown')); ?>
                                            </a>
                                        </td>
                                        <td><?php echo e($change_request['title']); ?></td>
                                        <td><span class="label label-<?php echo e($status['class']); ?>"><?php echo e($status['label']); ?></span></td>
                                        <td>
                                            <span style="color: #0284c7;" class="tw-font-medium"><?php echo e((int) $change_request['impact_days']); ?>d</span>
                                            <span class="tw-mx-1 tw-text-neutral-300">|</span>
                                            <span style="color: #16a34a;" class="tw-font-medium"><?php echo e(app_format_money($change_request['impact_cost'], $currency)); ?></span>
                                        </td>
                                        <td class="tw-text-neutral-500 tw-text-sm"><?php echo e($created); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Scope Snapshots -->
        <div class="panel_s">
            <div class="panel-body">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-0">
                        <i class="fa fa-camera text-info tw-mr-2"></i>
                        <?php echo e(_l('disowebs_ops_scope_recent_snapshots')); ?>
                    </h5>
                </div>
                <?php if (empty($snapshots)) { ?>
                    <div class="tw-text-center tw-py-12">
                        <i class="fa fa-camera tw-text-5xl tw-text-neutral-200 tw-mb-4"></i>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo e(_l('disowebs_ops_scope_recent_snapshots_empty')); ?></p>
                    </div>
                <?php } else { ?>
                    <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table class="table dt-table" style="min-width: 500px;">
                            <thead>
                                <tr>
                                    <th><?php echo e(_l('disowebs_ops_project')); ?></th>
                                    <th><?php echo e(_l('disowebs_ops_scope_source')); ?></th>
                                    <th><?php echo e(_l('disowebs_ops_scope_snapshot_date')); ?></th>
                                    <th><?php echo e(_l('disowebs_ops_scope_actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($snapshots as $snapshot) { ?>
                                    <?php
                                    $source_label = ucfirst($snapshot['source_type']) . ' #' . $snapshot['source_id'];
                                    $source_url = $snapshot['source_type'] === 'estimate'
                                        ? admin_url('estimates/list_estimates/' . $snapshot['source_id'])
                                        : admin_url('proposals/list_proposals/' . $snapshot['source_id']);
                                    $project_url = admin_url('projects/view/' . $snapshot['project_id'] . '?group=disowebs_ops_scope');
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e($project_url); ?>" class="tw-font-medium">
                                                <?php echo e($snapshot['project_name'] ?: _l('disowebs_ops_unknown')); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo e($source_url); ?>" target="_blank" class="tw-inline-flex tw-items-center tw-gap-1">
                                                <i class="fa fa-<?php echo $snapshot['source_type'] === 'estimate' ? 'file-text-o' : 'file-o'; ?> tw-text-neutral-400"></i>
                                                <?php echo e($source_label); ?>
                                            </a>
                                        </td>
                                        <td class="tw-text-neutral-500"><?php echo e(_dt($snapshot['created_at'])); ?></td>
                                        <td>
                                            <a href="<?php echo e($project_url); ?>" class="btn btn-default btn-xs">
                                                <i class="fa fa-eye"></i> <?php echo e(_l('disowebs_ops_view_project')); ?>
                                            </a>
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
<?php init_tail(); ?>
