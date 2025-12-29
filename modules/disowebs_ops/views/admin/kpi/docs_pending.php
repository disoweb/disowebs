<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Header -->
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-6">
                    <div>
                        <h4 class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mb-1">
                            <i class="fa fa-book tw-mr-2 tw-text-info"></i>
                            <?php echo e($title); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_docs_pending_desc'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('disowebs_ops/tsa_dashboard'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left tw-mr-1"></i> <?php echo _l('back'); ?>
                    </a>
                </div>

                <!-- Stats Card -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4">
                            <div class="tw-w-16 tw-h-16 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-bg-info/10">
                                <span class="tw-text-2xl tw-font-bold tw-text-info"><?php echo count($projects); ?></span>
                            </div>
                            <div>
                                <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-1"><?php echo _l('disowebs_ops_projects_need_docs'); ?></h5>
                                <p class="tw-text-neutral-500 tw-text-sm tw-mb-0"><?php echo _l('disowebs_ops_docs_pending_help'); ?></p>
                            </div>
                        </div>
                        
                        <div class="tw-grid tw-grid-cols-3 tw-gap-4 tw-mt-4 tw-pt-4 tw-border-t tw-border-neutral-200">
                            <div class="tw-text-center">
                                <div class="tw-text-2xl tw-font-bold tw-text-info"><?php echo e($stats['total'] ?? 0); ?></div>
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_docs_this_month'); ?></div>
                            </div>
                            <div class="tw-text-center">
                                <div class="tw-text-2xl tw-font-bold tw-text-success"><?php echo e($stats['written_this_week'] ?? 0); ?></div>
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_docs_this_week'); ?></div>
                            </div>
                            <div class="tw-text-center">
                                <div class="tw-text-2xl tw-font-bold tw-text-primary"><?php echo e($stats['coverage_pct'] ?? 0); ?>%</div>
                                <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_docs_coverage'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projects List -->
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (empty($projects)) { ?>
                        <div class="tw-text-center tw-py-12">
                            <i class="fa fa-check-circle tw-text-5xl tw-mb-4 tw-text-success"></i>
                            <h5 class="tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_all_docs_complete'); ?></h5>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_all_docs_complete_desc'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="2" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('project'); ?></th>
                                        <th><?php echo _l('client'); ?></th>
                                        <th><?php echo _l('disowebs_ops_start_date'); ?></th>
                                        <th><?php echo _l('disowebs_ops_deadline'); ?></th>
                                        <th class="tw-text-center"><?php echo _l('disowebs_ops_action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projects as $project) { ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo admin_url('projects/view/' . $project['project_id'] . '?group=project_files'); ?>" class="tw-font-medium">
                                                <?php echo e($project['project_name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo e($project['client_name'] ?? '-'); ?></td>
                                        <td><?php echo !empty($project['start_date']) ? _d($project['start_date']) : '-'; ?></td>
                                        <td>
                                            <?php if (!empty($project['deadline'])) { ?>
                                                <?php 
                                                $deadline = strtotime($project['deadline']);
                                                $now = time();
                                                $is_overdue = $deadline < $now;
                                                ?>
                                                <span class="<?php echo $is_overdue ? 'tw-text-danger tw-font-semibold' : ''; ?>">
                                                    <?php echo _d($project['deadline']); ?>
                                                </span>
                                            <?php } else { ?>
                                                -
                                            <?php } ?>
                                        </td>
                                        <td class="tw-text-center">
                                            <a href="<?php echo admin_url('projects/view/' . $project['project_id'] . '?group=project_files'); ?>" 
                                               class="btn btn-info btn-sm" style="color: #fff !important;">
                                                <i class="fa fa-file-upload tw-mr-1"></i> <?php echo _l('disowebs_ops_add_docs'); ?>
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
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>
