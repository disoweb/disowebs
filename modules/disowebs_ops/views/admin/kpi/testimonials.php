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
                            <i class="fa fa-star tw-mr-2" style="color: #f59e0b;"></i>
                            <?php echo e($title); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_testimonials_desc'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('disowebs_ops'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left tw-mr-1"></i> <?php echo _l('back'); ?>
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
                    <a href="<?php echo admin_url('disowebs_ops/testimonials/all'); ?>" class="panel_s tw-mb-0 tw-no-underline hover:tw-shadow-lg tw-transition <?php echo $filter == 'all' ? 'tw-ring-2 tw-ring-blue-500' : ''; ?>">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: #6366f1;"><?php echo $stats['total']; ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_total'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/testimonials/pending'); ?>" class="panel_s tw-mb-0 tw-no-underline hover:tw-shadow-lg tw-transition <?php echo $filter == 'pending' ? 'tw-ring-2 tw-ring-amber-500' : ''; ?>">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: #f59e0b;"><?php echo $stats['pending']; ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_pending_approval'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/testimonials/approved'); ?>" class="panel_s tw-mb-0 tw-no-underline hover:tw-shadow-lg tw-transition <?php echo $filter == 'approved' ? 'tw-ring-2 tw-ring-green-500' : ''; ?>">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: #16a34a;"><?php echo $stats['approved']; ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_approved'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('disowebs_ops/testimonials/requested'); ?>" class="panel_s tw-mb-0 tw-no-underline hover:tw-shadow-lg tw-transition <?php echo $filter == 'requested' ? 'tw-ring-2 tw-ring-purple-500' : ''; ?>">
                        <div class="panel-body tw-text-center">
                            <div class="tw-text-3xl tw-font-bold" style="color: #8b5cf6;"><?php echo $stats['requested']; ?></div>
                            <div class="tw-text-sm tw-text-neutral-500"><?php echo _l('disowebs_ops_requested'); ?></div>
                        </div>
                    </a>
                </div>

                <!-- Testimonials List -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-4">
                            <?php 
                            $filter_labels = [
                                'all' => _l('disowebs_ops_all_testimonials'),
                                'pending' => _l('disowebs_ops_pending_approval'),
                                'approved' => _l('disowebs_ops_approved_testimonials'),
                                'requested' => _l('disowebs_ops_requested_testimonials')
                            ];
                            echo $filter_labels[$filter] ?? _l('disowebs_ops_testimonials');
                            ?>
                        </h5>
                        
                        <?php if (empty($testimonials)) { ?>
                        <div class="tw-text-center tw-py-8">
                            <i class="fa fa-star tw-text-4xl tw-mb-3 tw-text-neutral-300"></i>
                            <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_testimonials_found'); ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="tw-space-y-4">
                            <?php foreach ($testimonials as $testimonial) { ?>
                            <div class="tw-border tw-rounded-lg tw-p-4 tw-bg-white hover:tw-shadow-md tw-transition">
                                <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-start tw-gap-4">
                                    <!-- Avatar & Info -->
                                    <div class="tw-flex-shrink-0">
                                        <div class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-gradient-to-br tw-from-amber-400 tw-to-orange-500 tw-flex tw-items-center tw-justify-center tw-text-white tw-font-bold tw-text-lg">
                                            <?php echo strtoupper(substr($testimonial['client_name'] ?? 'C', 0, 1)); ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="tw-flex-1 tw-min-w-0">
                                        <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-2 tw-mb-2">
                                            <span class="tw-font-semibold tw-text-neutral-800">
                                                <?php echo e($testimonial['client_name'] ?? _l('disowebs_ops_unknown_client')); ?>
                                            </span>
                                            <span class="tw-text-neutral-400">•</span>
                                            <a href="<?php echo admin_url('projects/view/' . $testimonial['project_id']); ?>" class="tw-text-blue-600 hover:tw-underline tw-text-sm">
                                                <?php echo e($testimonial['project_name']); ?>
                                            </a>
                                            
                                            <!-- Status Badge -->
                                            <?php if (!empty($testimonial['approved_at'])) { ?>
                                            <span class="label" style="background: #16a34a;"><?php echo _l('disowebs_ops_approved'); ?></span>
                                            <?php } elseif (!empty($testimonial['testimonial'])) { ?>
                                            <span class="label" style="background: #f59e0b;"><?php echo _l('disowebs_ops_pending'); ?></span>
                                            <?php } else { ?>
                                            <span class="label" style="background: #8b5cf6;"><?php echo _l('disowebs_ops_requested'); ?></span>
                                            <?php } ?>
                                        </div>
                                        
                                        <!-- Testimonial Text -->
                                        <?php if (!empty($testimonial['testimonial'])) { ?>
                                        <div class="tw-bg-neutral-50 tw-rounded tw-p-3 tw-mb-3">
                                            <i class="fa fa-quote-left tw-text-neutral-300 tw-mr-2"></i>
                                            <span class="tw-text-neutral-700"><?php echo e($testimonial['testimonial']); ?></span>
                                        </div>
                                        
                                        <!-- Rating -->
                                        <?php if (!empty($testimonial['rating'])) { ?>
                                        <div class="tw-mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++) { ?>
                                            <i class="fa fa-star <?php echo $i <= $testimonial['rating'] ? 'tw-text-amber-400' : 'tw-text-neutral-200'; ?>"></i>
                                            <?php } ?>
                                            <span class="tw-text-sm tw-text-neutral-500 tw-ml-1">(<?php echo $testimonial['rating']; ?>/5)</span>
                                        </div>
                                        <?php } ?>
                                        <?php } else { ?>
                                        <p class="tw-text-neutral-400 tw-italic tw-mb-3"><?php echo _l('disowebs_ops_awaiting_testimonial'); ?></p>
                                        <?php } ?>
                                        
                                        <!-- Meta -->
                                        <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-4 tw-text-xs tw-text-neutral-500">
                                            <?php if (!empty($testimonial['created_at'])) { ?>
                                            <span><i class="fa fa-clock tw-mr-1"></i> <?php echo _l('disowebs_ops_submitted'); ?>: <?php echo _dt($testimonial['created_at']); ?></span>
                                            <?php } ?>
                                            <?php if (!empty($testimonial['approved_at'])) { ?>
                                            <span><i class="fa fa-check tw-mr-1"></i> <?php echo _l('disowebs_ops_approved'); ?>: <?php echo _dt($testimonial['approved_at']); ?></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="tw-flex-shrink-0 tw-flex tw-gap-2">
                                        <?php if (!empty($testimonial['testimonial']) && empty($testimonial['approved_at'])) { ?>
                                        <form action="<?php echo admin_url('disowebs_ops_projects/approve_testimonial/' . $testimonial['project_id'] . '/' . $testimonial['id']); ?>" method="post" class="tw-inline">
                                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                            <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('<?php echo _l('disowebs_ops_confirm_approve'); ?>');">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="<?php echo admin_url('disowebs_ops_projects/reject_testimonial/' . $testimonial['project_id'] . '/' . $testimonial['id']); ?>" method="post" class="tw-inline">
                                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _l('disowebs_ops_confirm_reject'); ?>');">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                        <?php } ?>
                                        <a href="<?php echo admin_url('projects/view/' . $testimonial['project_id'] . '?group=disowebs_ops_gates'); ?>" class="btn btn-default btn-xs">
                                            <i class="fa fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
