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
                            <i class="fa fa-exclamation-triangle tw-mr-2" style="color: #dc2626;"></i>
                            <?php echo e($title); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_blockers_desc'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('disowebs_ops'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left tw-mr-1"></i> <?php echo _l('back'); ?>
                    </a>
                </div>

                <!-- Filter Tabs -->
                <ul class="nav nav-tabs tw-mb-4">
                    <li class="<?php echo $filter === 'active' ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('disowebs_ops/blockers/active'); ?>"><?php echo _l('disowebs_ops_active_blockers'); ?></a>
                    </li>
                    <li class="<?php echo $filter === 'overdue' ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('disowebs_ops/blockers/overdue'); ?>"><?php echo _l('disowebs_ops_overdue_blockers'); ?></a>
                    </li>
                </ul>

                <!-- Stats Card -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4">
                            <div class="tw-w-16 tw-h-16 tw-rounded-full tw-flex tw-items-center tw-justify-center" style="background-color: <?php echo $filter === 'overdue' ? '#fee2e2' : '#fef3c7'; ?>;">
                                <span class="tw-text-2xl tw-font-bold" style="color: <?php echo $filter === 'overdue' ? '#dc2626' : '#d97706'; ?>;"><?php echo count($blockers); ?></span>
                            </div>
                            <div>
                                <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-1">
                                    <?php echo $filter === 'overdue' ? _l('disowebs_ops_overdue_blockers') : _l('disowebs_ops_active_blockers'); ?>
                                </h5>
                                <p class="tw-text-neutral-500 tw-text-sm tw-mb-0"><?php echo _l('disowebs_ops_blockers_need_attention'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Blockers List -->
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (empty($blockers)) { ?>
                        <div class="tw-text-center tw-py-12">
                            <i class="fa fa-check-circle tw-text-5xl tw-mb-4" style="color: #16a34a;"></i>
                            <h5 class="tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_no_blockers'); ?></h5>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_blockers_desc'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="4" data-order-type="asc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('project'); ?></th>
                                        <th><?php echo _l('disowebs_ops_blocker_title'); ?></th>
                                        <th><?php echo _l('disowebs_ops_blocker_type'); ?></th>
                                        <th><?php echo _l('disowebs_ops_owner'); ?></th>
                                        <th><?php echo _l('disowebs_ops_due_date'); ?></th>
                                        <th class="tw-text-center"><?php echo _l('disowebs_ops_action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($blockers as $blocker) { ?>
                                    <?php
                                    $is_overdue = !empty($blocker['due_date']) && strtotime($blocker['due_date']) < time();
                                    $owner_name = '';
                                    if (!empty($blocker['owner_id'])) {
                                        foreach ($staff_members as $staff) {
                                            if ($staff['staffid'] == $blocker['owner_id']) {
                                                $owner_name = $staff['firstname'] . ' ' . $staff['lastname'];
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <tr class="<?php echo $is_overdue ? 'tw-bg-red-50' : ''; ?>">
                                        <td>
                                            <a href="<?php echo admin_url('projects/view/' . $blocker['project_id'] . '?group=disowebs_ops_gates'); ?>" class="tw-font-medium">
                                                <?php echo e($blocker['project_name'] ?? 'Project #' . $blocker['project_id']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="tw-font-medium"><?php echo e($blocker['title']); ?></span>
                                            <?php if (!empty($blocker['description'])) { ?>
                                            <p class="tw-text-neutral-500 tw-text-sm tw-mb-0 tw-mt-1"><?php echo e(character_limiter($blocker['description'], 100)); ?></p>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php
                                            $type_colors = [
                                                'client' => '#3b82f6',
                                                'technical' => '#8b5cf6',
                                                'resource' => '#f59e0b',
                                                'scope' => '#10b981',
                                                'other' => '#6b7280',
                                            ];
                                            $type_color = $type_colors[$blocker['blocker_type'] ?? 'other'] ?? '#6b7280';
                                            ?>
                                            <span class="label" style="background-color: <?php echo $type_color; ?>;">
                                                <?php echo _l('disowebs_ops_blocker_type_' . ($blocker['blocker_type'] ?? 'other')); ?>
                                            </span>
                                        </td>
                                        <td><?php echo e($owner_name ?: '-'); ?></td>
                                        <td>
                                            <?php if (!empty($blocker['due_date'])) { ?>
                                            <span class="<?php echo $is_overdue ? 'tw-text-red-600 tw-font-semibold' : ''; ?>">
                                                <?php echo _d($blocker['due_date']); ?>
                                            </span>
                                            <?php if ($is_overdue) { ?>
                                            <span class="label label-danger tw-ml-1"><?php echo _l('disowebs_ops_overdue'); ?></span>
                                            <?php } ?>
                                            <?php } else { ?>
                                            <span class="tw-text-neutral-400">-</span>
                                            <?php } ?>
                                        </td>
                                        <td class="tw-text-center">
                                            <a href="<?php echo admin_url('projects/view/' . $blocker['project_id'] . '?group=disowebs_ops_gates'); ?>" class="btn btn-primary btn-xs">
                                                <i class="fa fa-eye tw-mr-1"></i> <?php echo _l('view'); ?>
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
