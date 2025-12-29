<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
$support = isset($support_stats) ? $support_stats : [];
$qa = isset($qa_stats) ? $qa_stats : [];
$proof = isset($proof_stats) ? $proof_stats : [];
$docs = isset($docs_stats) ? $docs_stats : [];
$my_tasks = isset($my_tasks_count) ? (int) $my_tasks_count : 0;
$proof_pending = isset($projects_needing_proof) ? (int) $projects_needing_proof : 0;
$docs_pending = isset($projects_needing_docs) ? (int) $projects_needing_docs : 0;
?>
<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="tw-mb-6">
            <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                <div>
                    <h4 class="tw-text-lg tw-font-semibold tw-text-neutral-800 tw-mb-1">
                        <i class="fa fa-headset tw-mr-2 tw-text-primary"></i>
                        <?php echo e($title); ?>
                    </h4>
                    <p class="tw-text-neutral-500 tw-text-sm tw-mb-0"><?php echo _l('disowebs_ops_tsa_dashboard_desc'); ?></p>
                </div>
                <div class="tw-mt-3 md:tw-mt-0 tw-flex tw-gap-2">
                    <a href="<?php echo admin_url('disowebs_ops/ceo_dashboard'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-tachometer-alt tw-mr-1"></i> <?php echo _l('disowebs_ops_ceo_dashboard'); ?>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/crm_dashboard'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-users tw-mr-1"></i> <?php echo _l('disowebs_ops_crm_dashboard'); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Row 1: My Priority Tasks -->
        <div class="panel_s">
            <div class="panel-body">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-0">
                        <i class="fa fa-tasks tw-mr-2 tw-text-primary"></i>
                        <?php echo _l('disowebs_ops_tsa_my_priorities'); ?>
                    </h5>
                    <a href="<?php echo admin_url('tasks'); ?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-eye tw-mr-1"></i> <?php echo _l('disowebs_ops_view_all_tasks'); ?>
                    </a>
                </div>
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4">
                    <a href="<?php echo admin_url('tasks?status=1'); ?>" class="tw-rounded-lg tw-border-2 tw-border-danger tw-bg-danger/5 tw-p-4 hover:tw-bg-danger/10 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <i class="fa fa-list-check tw-text-danger"></i>
                            <span class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-danger"><?php echo _l('disowebs_ops_tsa_my_tasks'); ?></span>
                        </div>
                        <div class="tw-text-3xl tw-font-bold tw-text-danger tw-mt-2"><?php echo e($my_tasks); ?></div>
                        <div class="tw-text-xs tw-text-neutral-500 tw-mt-1"><?php echo _l('disowebs_ops_tsa_assigned_to_me'); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/demos'); ?>" class="tw-rounded-lg tw-border-2 tw-border-warning tw-bg-warning/5 tw-p-4 hover:tw-bg-warning/10 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <i class="fa fa-photo-video tw-text-warning"></i>
                            <span class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-warning"><?php echo _l('disowebs_ops_tsa_proof_pending'); ?></span>
                        </div>
                        <div class="tw-text-3xl tw-font-bold tw-text-warning tw-mt-2"><?php echo e($proof_pending); ?></div>
                        <div class="tw-text-xs tw-text-neutral-500 tw-mt-1"><?php echo _l('disowebs_ops_tsa_needs_recording'); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/docs/pending'); ?>" class="tw-rounded-lg tw-border-2 tw-border-info tw-bg-info/5 tw-p-4 hover:tw-bg-info/10 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <i class="fa fa-book tw-text-info"></i>
                            <span class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-info"><?php echo _l('disowebs_ops_tsa_docs_pending'); ?></span>
                        </div>
                        <div class="tw-text-3xl tw-font-bold tw-text-info tw-mt-2"><?php echo e($docs_pending); ?></div>
                        <div class="tw-text-xs tw-text-neutral-500 tw-mt-1"><?php echo _l('disowebs_ops_tsa_needs_documentation'); ?></div>
                    </a>
                    <a href="<?php echo admin_url('tickets?status=1'); ?>" class="tw-rounded-lg tw-border-2 tw-border-success tw-bg-success/5 tw-p-4 hover:tw-bg-success/10 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <i class="fa fa-ticket-alt tw-text-success"></i>
                            <span class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-success"><?php echo _l('disowebs_ops_tsa_open_tickets'); ?></span>
                        </div>
                        <div class="tw-text-3xl tw-font-bold tw-text-success tw-mt-2"><?php echo e($support['open_tickets'] ?? 0); ?></div>
                        <div class="tw-text-xs tw-text-neutral-500 tw-mt-1"><?php echo _l('disowebs_ops_tsa_awaiting_response'); ?></div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Row 2: Support Queue -->
        <div class="panel_s">
            <div class="panel-body">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-0">
                        <i class="fa fa-life-ring tw-mr-2 tw-text-info"></i>
                        <?php echo _l('disowebs_ops_tsa_support_queue'); ?>
                    </h5>
                    <?php 
                    $urgent_count = ($support['high_priority'] ?? 0) + ($support['overdue'] ?? 0);
                    ?>
                    <span class="label label-<?php echo $urgent_count > 0 ? 'danger' : 'success'; ?>">
                        <?php echo $urgent_count > 0 ? sprintf(_l('disowebs_ops_urgent_items'), $urgent_count) : _l('disowebs_ops_queue_clear'); ?>
                    </span>
                </div>
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4">
                    <a href="<?php echo admin_url('tickets?status=1'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_open_tickets'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($support['open_tickets'] ?? 0); ?></div>
                    </a>
                    <a href="<?php echo admin_url('tickets?priority=1'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_high_priority'); ?></div>
                        <div class="tw-text-2xl tw-font-bold <?php echo ($support['high_priority'] ?? 0) > 0 ? 'tw-text-danger' : 'tw-text-success'; ?> tw-mt-1"><?php echo e($support['high_priority'] ?? 0); ?></div>
                    </a>
                    <a href="<?php echo admin_url('tickets?filter=overdue'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_overdue'); ?></div>
                        <div class="tw-text-2xl tw-font-bold <?php echo ($support['overdue'] ?? 0) > 0 ? 'tw-text-danger' : 'tw-text-success'; ?> tw-mt-1"><?php echo e($support['overdue'] ?? 0); ?></div>
                    </a>
                    <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_avg_response'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($support['avg_response_time'] ?? '0h'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: QA & Proof -->
        <div class="row">
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-4">
                            <i class="fa fa-check-double tw-mr-2 tw-text-success"></i>
                            <?php echo _l('disowebs_ops_tsa_qa_readiness'); ?>
                        </h5>
                        <div class="tw-space-y-3">
                            <a href="<?php echo admin_url('disowebs_ops/qa/pending'); ?>" class="tw-flex tw-items-center tw-justify-between tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-warning/10 tw-flex tw-items-center tw-justify-center">
                                        <i class="fa fa-hourglass-half tw-text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="tw-text-sm tw-font-medium tw-text-neutral-800"><?php echo _l('disowebs_ops_tsa_qa_pending'); ?></div>
                                        <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_awaiting_review'); ?></div>
                                    </div>
                                </div>
                                <div class="tw-text-2xl tw-font-bold tw-text-warning">
                                    <?php echo e($qa['pending'] ?? 0); ?>
                                </div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/qa/passed'); ?>" class="tw-flex tw-items-center tw-justify-between tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-success/10 tw-flex tw-items-center tw-justify-center">
                                        <i class="fa fa-check-circle tw-text-success"></i>
                                    </div>
                                    <div>
                                        <div class="tw-text-sm tw-font-medium tw-text-neutral-800"><?php echo _l('disowebs_ops_tsa_qa_passed'); ?></div>
                                        <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_this_week'); ?></div>
                                    </div>
                                </div>
                                <div class="tw-text-2xl tw-font-bold tw-text-success">
                                    <?php echo e($qa['passed_this_week'] ?? 0); ?>
                                </div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/qa/failed'); ?>" class="tw-flex tw-items-center tw-justify-between tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-danger/10 tw-flex tw-items-center tw-justify-center">
                                        <i class="fa fa-times-circle tw-text-danger"></i>
                                    </div>
                                    <div>
                                        <div class="tw-text-sm tw-font-medium tw-text-neutral-800"><?php echo _l('disowebs_ops_tsa_qa_failed'); ?></div>
                                        <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_needs_revision'); ?></div>
                                    </div>
                                </div>
                                <div class="tw-text-2xl tw-font-bold <?php echo ($qa['failed'] ?? 0) > 0 ? 'tw-text-danger' : 'tw-text-success'; ?>">
                                    <?php echo e($qa['failed'] ?? 0); ?>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-4">
                            <i class="fa fa-video tw-mr-2 tw-text-info"></i>
                            <?php echo _l('disowebs_ops_tsa_proof_backlog'); ?>
                        </h5>
                        <div class="tw-space-y-3">
                            <a href="<?php echo admin_url('disowebs_ops/demos'); ?>" class="tw-flex tw-items-center tw-justify-between tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-warning/10 tw-flex tw-items-center tw-justify-center">
                                        <i class="fa fa-camera tw-text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="tw-text-sm tw-font-medium tw-text-neutral-800"><?php echo _l('disowebs_ops_tsa_projects_need_proof'); ?></div>
                                        <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_missing_demo_video'); ?></div>
                                    </div>
                                </div>
                                <div class="tw-text-2xl tw-font-bold <?php echo $proof_pending > 0 ? 'tw-text-warning' : 'tw-text-success'; ?>">
                                    <?php echo e($proof_pending); ?>
                                </div>
                            </a>
                            <a href="<?php echo admin_url('disowebs_ops/proof/recorded'); ?>" class="tw-flex tw-items-center tw-justify-between tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-success/10 tw-flex tw-items-center tw-justify-center">
                                        <i class="fa fa-play-circle tw-text-success"></i>
                                    </div>
                                    <div>
                                        <div class="tw-text-sm tw-font-medium tw-text-neutral-800"><?php echo _l('disowebs_ops_tsa_recorded_this_week'); ?></div>
                                        <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_demos_completed'); ?></div>
                                    </div>
                                </div>
                                <div class="tw-text-2xl tw-font-bold tw-text-success">
                                    <?php echo e($proof['recorded_this_week'] ?? 0); ?>
                                </div>
                            </a>
                            <div class="tw-flex tw-items-center tw-justify-between tw-rounded-lg tw-border tw-border-neutral-200 tw-p-3">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-primary/10 tw-flex tw-items-center tw-justify-center">
                                        <i class="fa fa-database tw-text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="tw-text-sm tw-font-medium tw-text-neutral-800"><?php echo _l('disowebs_ops_tsa_total_proof_library'); ?></div>
                                        <div class="tw-text-xs tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_all_time'); ?></div>
                                    </div>
                                </div>
                                <div class="tw-text-2xl tw-font-bold tw-text-primary">
                                    <?php echo e($proof['total'] ?? 0); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 4: Documentation -->
        <div class="panel_s">
            <div class="panel-body">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-0">
                        <i class="fa fa-book-open tw-mr-2 tw-text-primary"></i>
                        <?php echo _l('disowebs_ops_tsa_documentation'); ?>
                    </h5>
                    <a href="<?php echo admin_url('disowebs_ops/docs'); ?>" class="btn btn-info btn-sm">
                        <i class="fa fa-external-link-alt tw-mr-1"></i> <?php echo _l('disowebs_ops_view_all_docs'); ?>
                    </a>
                </div>
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4">
                    <a href="<?php echo admin_url('disowebs_ops/docs/pending'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_docs_pending'); ?></div>
                        <div class="tw-text-2xl tw-font-bold <?php echo $docs_pending > 0 ? 'tw-text-warning' : 'tw-text-success'; ?> tw-mt-1"><?php echo e($docs_pending); ?></div>
                        <div class="tw-text-xs tw-text-neutral-400 tw-mt-1"><?php echo _l('disowebs_ops_tsa_projects_need_docs'); ?></div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/docs/written'); ?>" class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4 hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-block">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_written_this_week'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-success tw-mt-1"><?php echo e($docs['written_this_week'] ?? 0); ?></div>
                        <div class="tw-text-xs tw-text-neutral-400 tw-mt-1"><?php echo _l('disowebs_ops_tsa_docs_completed'); ?></div>
                    </a>
                    <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_total_docs'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-neutral-800 tw-mt-1"><?php echo e($docs['total'] ?? 0); ?></div>
                        <div class="tw-text-xs tw-text-neutral-400 tw-mt-1"><?php echo _l('disowebs_ops_tsa_in_library'); ?></div>
                    </a>
                    <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-p-4">
                        <div class="tw-text-xs tw-uppercase tw-tracking-wide tw-text-neutral-500"><?php echo _l('disowebs_ops_tsa_coverage'); ?></div>
                        <div class="tw-text-2xl tw-font-bold tw-text-primary tw-mt-1"><?php echo e($docs['coverage_pct'] ?? 0); ?>%</div>
                        <div class="tw-text-xs tw-text-neutral-400 tw-mt-1"><?php echo _l('disowebs_ops_tsa_projects_documented'); ?></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php init_tail(); ?>
