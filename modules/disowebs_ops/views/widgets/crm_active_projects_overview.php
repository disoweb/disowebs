<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('disowebs_ops/dw_dashboard_model');
$projects = $this->dw_dashboard_model->get_crm_active_projects_overview();
$currency = get_base_currency();
$currency_name = $currency ? $currency->name : '';
?>
<div class="widget" id="widget-<?php echo create_widget_id('disowebs_ops_crm_active_projects'); ?>" data-name="<?php echo _l('disowebs_ops_dashboard_active_projects_title'); ?>">
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
                <h4 class="tw-mb-0 tw-font-semibold tw-text-neutral-800"><?php echo _l('disowebs_ops_dashboard_active_projects_title'); ?></h4>
            </div>
            <?php if (empty($projects)) { ?>
                <p class="text-muted mtop15"><?php echo e(_l('disowebs_ops_dashboard_active_projects_empty')); ?></p>
            <?php } else { ?>
                <div class="table-responsive mtop15">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?php echo e(_l('disowebs_ops_project')); ?></th>
                                <th><?php echo e(_l('disowebs_ops_dashboard_active_project_milestone')); ?></th>
                                <th><?php echo e(_l('disowebs_ops_dashboard_active_project_due')); ?></th>
                                <th><?php echo e(_l('disowebs_ops_dashboard_active_project_payment')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project) { ?>
                            <?php
                            $milestone_title = $project['milestone_title'] !== '' ? $project['milestone_title'] : _l('disowebs_ops_dashboard_active_project_none');
                            $milestone_due = $project['milestone_due'] ? _d($project['milestone_due']) : '--';
                            $payment_outstanding = (float) ($project['payment_outstanding'] ?? 0.0);
                            $payment_label = $payment_outstanding > 0 ? app_format_money($payment_outstanding, $currency_name) : _l('disowebs_ops_dashboard_payment_ok');
                            ?>
                            <tr>
                                <td>
                                    <a href="<?php echo admin_url('projects/view/' . $project['project_id']); ?>">
                                        <?php echo e($project['project_name']); ?>
                                    </a>
                                </td>
                                <td><?php echo e($milestone_title); ?></td>
                                <td><?php echo e($milestone_due); ?></td>
                                <td><?php echo e($payment_label); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
