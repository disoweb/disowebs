<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
// Calculate additional stats
$total_entries = $proof_totals['entries'] ?? 0;
$with_files = $proof_totals['entries_with_files'] ?? 0;
$total_files = $proof_totals['files'] ?? 0;
$coverage_percent = $total_entries > 0 ? round(($with_files / $total_entries) * 100) : 0;
?>
<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="tw-mb-6">
            <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                <div>
                    <h4 class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mb-1"><?php echo e($title); ?></h4>
                    <p class="tw-text-neutral-500 tw-mb-0"><?php echo e(_l('disowebs_ops_proof_overview_intro')); ?></p>
                </div>
            </div>
        </div>

        <!-- KPI Stats -->
        <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
            <!-- Total Proof Entries -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_proof_total')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-primary-100 tw-flex tw-items-center tw-justify-center">
                            <i class="fa fa-shield tw-text-primary-600"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold tw-text-neutral-800"><?php echo $total_entries; ?></div>
                </div>
            </div>

            <!-- With Files -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_proof_with_files')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-success-100 tw-flex tw-items-center tw-justify-center">
                            <i class="fa fa-file-image-o tw-text-success-600"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold tw-text-success-600"><?php echo $with_files; ?></div>
                </div>
            </div>

            <!-- Total Files -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_proof_files_total')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-info-100 tw-flex tw-items-center tw-justify-center">
                            <i class="fa fa-files-o tw-text-info-600"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold tw-text-info-600"><?php echo $total_files; ?></div>
                </div>
            </div>

            <!-- Coverage Rate -->
            <div class="panel_s tw-mb-0">
                <div class="panel-body">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                        <span class="tw-text-neutral-500 tw-text-sm"><?php echo e(_l('disowebs_ops_proof_coverage')); ?></span>
                        <span class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-<?php echo $coverage_percent >= 80 ? 'success' : ($coverage_percent >= 50 ? 'warning' : 'danger'); ?>-100 tw-flex tw-items-center tw-justify-center">
                            <i class="fa fa-pie-chart tw-text-<?php echo $coverage_percent >= 80 ? 'success' : ($coverage_percent >= 50 ? 'warning' : 'danger'); ?>-600"></i>
                        </span>
                    </div>
                    <div class="tw-text-3xl tw-font-bold tw-text-neutral-800"><?php echo $coverage_percent; ?><span class="tw-text-lg tw-text-neutral-400">%</span></div>
                    <div class="progress tw-h-1 tw-mt-2 tw-mb-0">
                        <div class="progress-bar progress-bar-<?php echo $coverage_percent >= 80 ? 'success' : ($coverage_percent >= 50 ? 'warning' : 'danger'); ?>" style="width: <?php echo $coverage_percent; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Proof Entries -->
        <div class="panel_s">
            <div class="panel-body">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-0">
                        <i class="fa fa-camera text-success tw-mr-2"></i>
                        <?php echo e(_l('disowebs_ops_proof_recent_entries')); ?>
                    </h5>
                </div>
                <?php if (empty($proof_entries)) { ?>
                    <div class="tw-text-center tw-py-12">
                        <i class="fa fa-camera tw-text-5xl tw-text-neutral-200 tw-mb-4"></i>
                        <p class="tw-text-neutral-500 tw-mb-2"><?php echo e(_l('disowebs_ops_proof_recent_empty')); ?></p>
                        <p class="tw-text-neutral-400 tw-text-sm tw-mb-0"><?php echo e(_l('disowebs_ops_proof_hint')); ?></p>
                    </div>
                <?php } else { ?>
                    <!-- Grid View -->
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 tw-gap-4 tw-mb-6">
                        <?php foreach ($proof_entries as $entry) { ?>
                            <?php
                            $creator = trim(($entry['firstname'] ?? '') . ' ' . ($entry['lastname'] ?? ''));
                            $created = _dt($entry['created_at']);
                            $project_url = admin_url('projects/view/' . $entry['project_id'] . '?group=disowebs_ops_proof');
                            $files_count = (int)($entry['files_count'] ?? 0);
                            ?>
                            <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-bg-white tw-overflow-hidden hover:tw-shadow-md tw-transition">
                                <!-- Card Header -->
                                <div class="tw-p-4 tw-border-b tw-border-neutral-100">
                                    <div class="tw-flex tw-items-start tw-justify-between">
                                        <div class="tw-flex-1">
                                            <h6 class="tw-font-semibold tw-text-neutral-800 tw-mb-1 tw-truncate"><?php echo e($entry['title']); ?></h6>
                                            <a href="<?php echo e($project_url); ?>" class="tw-text-sm tw-text-primary-600 hover:tw-underline">
                                                <?php echo e($entry['project_name'] ?: _l('disowebs_ops_unknown')); ?>
                                            </a>
                                        </div>
                                        <?php if ($files_count > 0) { ?>
                                        <span class="tw-flex tw-items-center tw-gap-1 tw-text-sm tw-text-success-600 tw-bg-success-50 tw-px-2 tw-py-1 tw-rounded">
                                            <i class="fa fa-paperclip"></i> <?php echo $files_count; ?>
                                        </span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="tw-p-4 tw-bg-neutral-50">
                                    <div class="tw-flex tw-items-center tw-justify-between">
                                        <div class="tw-text-sm tw-text-neutral-500">
                                            <i class="fa fa-clock-o tw-mr-1"></i>
                                            <?php echo $created; ?>
                                            <?php if ($creator) { ?>
                                            <span class="tw-block tw-mt-1">
                                                <i class="fa fa-user tw-mr-1"></i> <?php echo e($creator); ?>
                                            </span>
                                            <?php } ?>
                                        </div>
                                        <a href="<?php echo e($project_url); ?>" class="btn btn-default btn-xs">
                                            <i class="fa fa-eye"></i> <?php echo e(_l('disowebs_ops_view')); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Table View (Alternative) -->
                    <div class="tw-border-t tw-border-neutral-200 tw-pt-4">
                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                            <span class="tw-text-sm tw-text-neutral-500"><?php echo e(_l('disowebs_ops_table_view')); ?></span>
                        </div>
                        <div class="table-responsive">
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo e(_l('disowebs_ops_project')); ?></th>
                                        <th><?php echo e(_l('disowebs_ops_proof_title')); ?></th>
                                        <th class="tw-text-center"><?php echo e(_l('disowebs_ops_proof_files_total')); ?></th>
                                        <th><?php echo e(_l('disowebs_ops_created')); ?></th>
                                        <th class="tw-text-center"><?php echo e(_l('disowebs_ops_scope_actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($proof_entries as $entry) { ?>
                                        <?php
                                        $creator = trim(($entry['firstname'] ?? '') . ' ' . ($entry['lastname'] ?? ''));
                                        $created = _dt($entry['created_at']);
                                        if ($creator !== '') {
                                            $created .= ' • ' . $creator;
                                        }
                                        $project_url = admin_url('projects/view/' . $entry['project_id'] . '?group=disowebs_ops_proof');
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo e($project_url); ?>" class="tw-font-medium">
                                                    <?php echo e($entry['project_name'] ?: _l('disowebs_ops_unknown')); ?>
                                                </a>
                                            </td>
                                            <td><?php echo e($entry['title']); ?></td>
                                            <td class="tw-text-center">
                                                <?php if ((int)$entry['files_count'] > 0) { ?>
                                                <span class="label label-success"><?php echo e($entry['files_count']); ?> <?php echo e(_l('disowebs_ops_files')); ?></span>
                                                <?php } else { ?>
                                                <span class="label label-default">0</span>
                                                <?php } ?>
                                            </td>
                                            <td class="tw-text-neutral-500 tw-text-sm"><?php echo e($created); ?></td>
                                            <td class="tw-text-center">
                                                <a href="<?php echo e($project_url); ?>" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i> <?php echo e(_l('disowebs_ops_view_project')); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
