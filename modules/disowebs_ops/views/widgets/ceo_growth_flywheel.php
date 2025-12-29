<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('disowebs_ops/dw_dashboard_model');
$this->load->model('disowebs_ops/dw_referrals_model');
$stats = $this->dw_dashboard_model->get_growth_flywheel_stats();
$referral_stats = $this->dw_referrals_model->get_stats(90);

// Testimonial conversion rate
$testimonial_rate = ($stats['testimonials_requested'] > 0) 
    ? round(($stats['testimonials_received'] / $stats['testimonials_requested']) * 100, 0) 
    : 0;
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_ceo_growth_flywheel'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_growth_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_growth_title'); ?></h4>
                <span class="label label-<?php echo $testimonial_rate >= 50 ? 'success' : ($testimonial_rate >= 25 ? 'warning' : 'default'); ?>">
                    <?php echo $testimonial_rate; ?>% <?php echo _l('disowebs_ops_testimonial_rate'); ?>
                </span>
            </div>
            
            <!-- Social Proof Section -->
            <div class="tw-mt-3 tw-mb-2">
                <span class="tw-text-xs tw-font-medium tw-text-neutral-500 tw-uppercase"><?php echo _l('disowebs_ops_social_proof'); ?></span>
            </div>
            <dl class="tw-grid tw-grid-cols-2 tw-gap-2 tw-mb-0">
                <div class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 tw-transition">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_proof_entries_month'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-neutral-600"><?php echo e($stats['proof_entries_month']); ?></dd>
                    </div>
                </div>
                <div class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 tw-transition">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_dashboard_case_studies_ready'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-neutral-600"><?php echo e($stats['case_studies_ready']); ?></dd>
                    </div>
                </div>
            </dl>

            <!-- Testimonials Section -->
            <div class="tw-mt-3 tw-mb-2">
                <span class="tw-text-xs tw-font-medium tw-text-neutral-500 tw-uppercase"><?php echo _l('disowebs_ops_testimonials'); ?></span>
            </div>
            <dl class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-2 tw-mb-0">
                <a href="<?php echo admin_url('disowebs_ops/testimonials/requested'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_requested'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-neutral-600"><?php echo e($stats['testimonials_requested']); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/testimonials/all'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_received'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold" style="color: #16a34a;"><?php echo e($stats['testimonials_received']); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/testimonials/pending'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_pending'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold" style="color: <?php echo ($stats['testimonials_pending'] ?? 0) > 0 ? '#d97706' : '#525252'; ?>;"><?php echo e($stats['testimonials_pending'] ?? 0); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/testimonials/approved'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_approved'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold" style="color: #2563eb;"><?php echo e($stats['testimonials_approved'] ?? 0); ?></dd>
                    </div>
                </a>
            </dl>

            <!-- Referrals Section -->
            <div class="tw-mt-3 tw-mb-2">
                <span class="tw-text-xs tw-font-medium tw-text-neutral-500 tw-uppercase"><?php echo _l('disowebs_ops_referrals'); ?></span>
            </div>
            <dl class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 tw-gap-2 tw-mb-0">
                <a href="<?php echo admin_url('disowebs_ops/referrals'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_total'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold tw-text-neutral-600"><?php echo e($referral_stats['total']); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/referrals'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_converted'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold" style="color: #16a34a;"><?php echo e($referral_stats['converted']); ?></dd>
                    </div>
                </a>
                <a href="<?php echo admin_url('disowebs_ops/referrals'); ?>" class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white tw-block hover:tw-bg-neutral-50 hover:tw-shadow-md tw-transition tw-no-underline tw-col-span-2 md:tw-col-span-1">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-normal text-muted"><?php echo _l('disowebs_ops_conversion_rate'); ?></dt>
                        <dd class="tw-mt-1 tw-font-semibold" style="color: <?php echo $referral_stats['conversion_rate'] >= 30 ? '#16a34a' : '#525252'; ?>;"><?php echo e($referral_stats['conversion_rate']); ?>%</dd>
                    </div>
                </a>
            </dl>
            <?php if ($referral_stats['revenue'] > 0) { ?>
            <div class="tw-mt-2 tw-text-right">
                <span class="tw-text-sm text-muted"><?php echo _l('disowebs_ops_referral_revenue'); ?>:</span>
                <span class="tw-text-sm tw-font-semibold tw-text-success-600"><?php echo app_format_money($referral_stats['revenue'], get_base_currency()); ?></span>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
