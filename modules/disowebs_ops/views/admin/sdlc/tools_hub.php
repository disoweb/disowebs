<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php $this->load->view('admin/includes/alerts'); ?>

            <!-- Header -->
            <div class="col-md-12">
                <h4 class="tw-font-bold tw-text-xl tw-mb-6">
                    <i class="fa fa-tools tw-mr-2"></i>
                    <?php echo _l('disowebs_ops_sdlc_tools'); ?>
                </h4>
            </div>

            <!-- Quick Stats -->
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body tw-flex tw-items-center tw-justify-between">
                                <div>
                                    <h3 class="tw-font-bold tw-text-2xl tw-mb-0 tw-text-primary"><?php echo $stats['total_snippets']; ?></h3>
                                    <span class="tw-text-neutral-500"><?php echo _l('disowebs_ops_code_snippets'); ?></span>
                                </div>
                                <i class="fa fa-code fa-2x tw-text-neutral-300"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body tw-flex tw-items-center tw-justify-between">
                                <div>
                                    <h3 class="tw-font-bold tw-text-2xl tw-mb-0 tw-text-success"><?php echo $stats['total_templates']; ?></h3>
                                    <span class="tw-text-neutral-500"><?php echo _l('disowebs_ops_templates'); ?></span>
                                </div>
                                <i class="fa fa-file-alt fa-2x tw-text-neutral-300"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body tw-flex tw-items-center tw-justify-between">
                                <div>
                                    <h3 class="tw-font-bold tw-text-2xl tw-mb-0 tw-text-warning"><?php echo $stats['active_checklists']; ?></h3>
                                    <span class="tw-text-neutral-500"><?php echo _l('disowebs_ops_active_checklists'); ?></span>
                                </div>
                                <i class="fa fa-tasks fa-2x tw-text-neutral-300"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body tw-flex tw-items-center tw-justify-between">
                                <div>
                                    <h3 class="tw-font-bold tw-text-2xl tw-mb-0 tw-text-danger"><?php echo $stats['pending_change_requests']; ?></h3>
                                    <span class="tw-text-neutral-500"><?php echo _l('disowebs_ops_pending_crs'); ?></span>
                                </div>
                                <i class="fa fa-exchange-alt fa-2x tw-text-neutral-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?php echo _l('disowebs_ops_quick_actions'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <?php foreach ($quick_actions as $action) { ?>
                            <div class="col-md-2 col-sm-4 col-xs-6 tw-mb-4">
                                <a href="<?php echo admin_url($action['url']); ?>" 
                                   class="tw-block tw-text-center tw-p-4 tw-rounded-lg tw-border tw-border-neutral-200 hover:tw-border-primary hover:tw-bg-primary-50 tw-transition">
                                    <i class="<?php echo $action['icon']; ?> fa-2x tw-text-<?php echo $action['color']; ?> tw-mb-2"></i>
                                    <div class="tw-text-sm tw-font-medium"><?php echo $action['label']; ?></div>
                                </a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tools Grid -->
            <div class="col-md-12">
                <h5 class="tw-font-semibold tw-mb-4"><?php echo _l('disowebs_ops_sdlc_modules'); ?></h5>
                <div class="row">
                    <!-- Requirements -->
                    <div class="col-md-4 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-items-start">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-lg tw-bg-blue-100 tw-flex tw-items-center tw-justify-center tw-mr-4">
                                        <i class="fa fa-list-alt fa-lg tw-text-blue-600"></i>
                                    </div>
                                    <div class="tw-flex-1">
                                        <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('disowebs_ops_requirements'); ?></h5>
                                        <p class="tw-text-sm tw-text-neutral-500 tw-mb-3">Gather and manage project requirements, acceptance criteria, and priorities.</p>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/requirements'); ?>" class="btn btn-default btn-xs">
                                            <?php echo _l('disowebs_ops_manage'); ?> <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scope Documents -->
                    <div class="col-md-4 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-items-start">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-lg tw-bg-green-100 tw-flex tw-items-center tw-justify-center tw-mr-4">
                                        <i class="fa fa-file-contract fa-lg tw-text-green-600"></i>
                                    </div>
                                    <div class="tw-flex-1">
                                        <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('disowebs_ops_scope_documents'); ?></h5>
                                        <p class="tw-text-sm tw-text-neutral-500 tw-mb-3">Create professional scope documents with deliverables, timelines, and budgets.</p>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/scope'); ?>" class="btn btn-default btn-xs">
                                            <?php echo _l('disowebs_ops_manage'); ?> <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Requests -->
                    <div class="col-md-4 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-items-start">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-lg tw-bg-amber-100 tw-flex tw-items-center tw-justify-center tw-mr-4">
                                        <i class="fa fa-exchange-alt fa-lg tw-text-amber-600"></i>
                                    </div>
                                    <div class="tw-flex-1">
                                        <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('disowebs_ops_change_requests'); ?></h5>
                                        <p class="tw-text-sm tw-text-neutral-500 tw-mb-3">Track scope changes, impact analysis, and client approvals.</p>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/change_requests'); ?>" class="btn btn-default btn-xs">
                                            <?php echo _l('disowebs_ops_manage'); ?> <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Stories -->
                    <div class="col-md-4 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-items-start">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-lg tw-bg-purple-100 tw-flex tw-items-center tw-justify-center tw-mr-4">
                                        <i class="fa fa-book fa-lg tw-text-purple-600"></i>
                                    </div>
                                    <div class="tw-flex-1">
                                        <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('disowebs_ops_user_stories'); ?></h5>
                                        <p class="tw-text-sm tw-text-neutral-500 tw-mb-3">Write user stories with acceptance criteria, epics, and story points.</p>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/stories'); ?>" class="btn btn-default btn-xs">
                                            <?php echo _l('disowebs_ops_manage'); ?> <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Checklists -->
                    <div class="col-md-4 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-items-start">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-lg tw-bg-teal-100 tw-flex tw-items-center tw-justify-center tw-mr-4">
                                        <i class="fa fa-tasks fa-lg tw-text-teal-600"></i>
                                    </div>
                                    <div class="tw-flex-1">
                                        <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('disowebs_ops_checklists'); ?></h5>
                                        <p class="tw-text-sm tw-text-neutral-500 tw-mb-3">SDLC checklists for kickoff, QA, deployment, and handover.</p>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/checklists'); ?>" class="btn btn-default btn-xs">
                                            <?php echo _l('disowebs_ops_manage'); ?> <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Estimates -->
                    <div class="col-md-4 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-items-start">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-lg tw-bg-rose-100 tw-flex tw-items-center tw-justify-center tw-mr-4">
                                        <i class="fa fa-clock fa-lg tw-text-rose-600"></i>
                                    </div>
                                    <div class="tw-flex-1">
                                        <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('disowebs_ops_time_estimates'); ?></h5>
                                        <p class="tw-text-sm tw-text-neutral-500 tw-mb-3">PERT estimation with optimistic, likely, pessimistic hours.</p>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/estimates'); ?>" class="btn btn-default btn-xs">
                                            <?php echo _l('disowebs_ops_manage'); ?> <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Developer Tools -->
            <div class="col-md-12">
                <h5 class="tw-font-semibold tw-mb-4"><?php echo _l('disowebs_ops_developer_tools'); ?></h5>
                <div class="row">
                    <!-- Code Snippets -->
                    <div class="col-md-4 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-items-start">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-lg tw-bg-slate-800 tw-flex tw-items-center tw-justify-center tw-mr-4">
                                        <i class="fa fa-code fa-lg tw-text-green-400"></i>
                                    </div>
                                    <div class="tw-flex-1">
                                        <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('disowebs_ops_code_snippets'); ?></h5>
                                        <p class="tw-text-sm tw-text-neutral-500 tw-mb-3">Reusable code snippets library with search and categories.</p>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/snippets'); ?>" class="btn btn-default btn-xs">
                                            <?php echo _l('disowebs_ops_browse'); ?> <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Templates -->
                    <div class="col-md-4 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-items-start">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-lg tw-bg-indigo-100 tw-flex tw-items-center tw-justify-center tw-mr-4">
                                        <i class="fa fa-file-alt fa-lg tw-text-indigo-600"></i>
                                    </div>
                                    <div class="tw-flex-1">
                                        <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('disowebs_ops_templates'); ?></h5>
                                        <p class="tw-text-sm tw-text-neutral-500 tw-mb-3">Document and code templates with variable placeholders.</p>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/templates'); ?>" class="btn btn-default btn-xs">
                                            <?php echo _l('disowebs_ops_browse'); ?> <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dev Notes -->
                    <div class="col-md-4 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-flex tw-items-start">
                                    <div class="tw-w-12 tw-h-12 tw-rounded-lg tw-bg-yellow-100 tw-flex tw-items-center tw-justify-center tw-mr-4">
                                        <i class="fa fa-sticky-note fa-lg tw-text-yellow-600"></i>
                                    </div>
                                    <div class="tw-flex-1">
                                        <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('disowebs_ops_dev_notes'); ?></h5>
                                        <p class="tw-text-sm tw-text-neutral-500 tw-mb-3">Knowledge base for troubleshooting, how-tos, and decisions.</p>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/notes'); ?>" class="btn btn-default btn-xs">
                                            <?php echo _l('disowebs_ops_browse'); ?> <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Snippets -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-code tw-mr-2"></i>
                            <?php echo _l('disowebs_ops_recent_snippets'); ?>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <?php if (!empty($recent_snippets)) { ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('title'); ?></th>
                                        <th><?php echo _l('language'); ?></th>
                                        <th><?php echo _l('disowebs_ops_uses'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_snippets as $snippet) { ?>
                                    <tr class="tw-cursor-pointer" onclick="window.location='<?php echo admin_url('disowebs_ops/sdlc/snippet/' . $snippet->id); ?>'">
                                        <td>
                                            <strong><?php echo e($snippet->title); ?></strong>
                                            <br><small class="tw-text-neutral-400"><?php echo e($snippet->category); ?></small>
                                        </td>
                                        <td><span class="label label-default"><?php echo e($snippet->language); ?></span></td>
                                        <td><?php echo $snippet->usage_count; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } else { ?>
                        <p class="tw-text-center tw-text-neutral-400 tw-py-4"><?php echo _l('disowebs_ops_no_snippets'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Pinned Notes -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-thumbtack tw-mr-2"></i>
                            <?php echo _l('disowebs_ops_pinned_notes'); ?>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <?php if (!empty($recent_notes)) { ?>
                        <div class="list-group">
                            <?php foreach ($recent_notes as $note) { ?>
                            <a href="<?php echo admin_url('disowebs_ops/sdlc/note/' . $note->id); ?>" class="list-group-item">
                                <div class="tw-flex tw-items-center">
                                    <?php if ($note->is_pinned) { ?>
                                    <i class="fa fa-thumbtack tw-text-warning tw-mr-2"></i>
                                    <?php } ?>
                                    <div class="tw-flex-1">
                                        <strong><?php echo e($note->title); ?></strong>
                                        <br>
                                        <small class="tw-text-neutral-400">
                                            <?php echo e($note->category); ?>
                                            <?php if ($note->project_name) { ?>
                                            • <?php echo e($note->project_name); ?>
                                            <?php } ?>
                                        </small>
                                    </div>
                                    <span class="badge"><?php echo $note->views; ?> views</span>
                                </div>
                            </a>
                            <?php } ?>
                        </div>
                        <?php } else { ?>
                        <p class="tw-text-center tw-text-neutral-400 tw-py-4"><?php echo _l('disowebs_ops_no_notes'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php init_tail(); ?>
