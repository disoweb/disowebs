<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$CI = &get_instance();
$CI->load->model('disowebs_ops/dw_project_profit_model');
$CI->load->model('disowebs_ops/dw_margin_alerts_model');

$profit_summary = disowebs_ops_get_project_profit_summary($project->id);
$margin_alerts = disowebs_ops_get_project_margin_alerts($project->id);

$can_manage = has_permission(DISOWEBS_OPS_MODULE_NAME, '', 'manage');
$is_ceo = disowebs_ops_is_ceo();

$currency = function_exists('get_base_currency') ? get_base_currency() : null;
$currency_name = $currency && isset($currency->name) ? $currency->name : '';

// Calculate margin class
$margin_percent = $profit_summary['margin_percent'] ?? 0;
if ($margin_percent >= 40) {
    $margin_class = 'success';
} elseif ($margin_percent >= 25) {
    $margin_class = 'info';
} elseif ($margin_percent >= 15) {
    $margin_class = 'warning';
} else {
    $margin_class = 'danger';
}
?>

<div class="panel_s">
    <div class="panel-body">
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
            <div>
                <h4 class="no-margin"><?php echo e(_l('disowebs_ops_project_profit')); ?></h4>
                <p class="text-muted mtop5"><?php echo e(_l('disowebs_ops_project_profit_desc')); ?></p>
            </div>
            <?php if ($is_ceo) { ?>
            <a class="btn btn-default btn-sm mtop10 md:mtop0" data-toggle="collapse" href="#profit-settings-form">
                <i class="fa fa-cog"></i> <?php echo e(_l('disowebs_ops_set_expected_values')); ?>
            </a>
            <?php } ?>
        </div>

        <?php if ($is_ceo) { ?>
        <div id="profit-settings-form" class="collapse mtop15">
            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/update_profit_settings/' . $project->id)); ?>
            <div class="row">
                <div class="col-md-4">
                    <?php echo render_input('expected_revenue', 'disowebs_ops_expected_revenue', $profit_summary['expected_revenue'] ?? '', 'number', ['step' => '0.01']); ?>
                </div>
                <div class="col-md-4">
                    <?php echo render_input('expected_cost', 'disowebs_ops_expected_cost', $profit_summary['expected_cost'] ?? '', 'number', ['step' => '0.01']); ?>
                </div>
                <div class="col-md-4">
                    <?php echo render_input('effort_hours', 'disowebs_ops_effort_hours', $profit_summary['effort_hours'] ?? '', 'number', ['step' => '0.5']); ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo e(_l('submit')); ?></button>
            <?php echo form_close(); ?>
        </div>
        <?php } ?>

        <!-- Profit Overview Cards -->
        <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-4 mtop20">
            <!-- Revenue -->
            <div class="panel_s panel-default">
                <div class="panel-body text-center">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_actual_revenue')); ?></div>
                    <div class="tw-text-2xl tw-font-bold text-success">
                        <?php echo app_format_money($profit_summary['actual_revenue'] ?? 0, $currency_name); ?>
                    </div>
                    <?php if (!empty($profit_summary['expected_revenue'])) { ?>
                    <small class="text-muted">
                        / <?php echo app_format_money($profit_summary['expected_revenue'], $currency_name); ?>
                    </small>
                    <?php } ?>
                </div>
            </div>

            <!-- Cost -->
            <div class="panel_s panel-default">
                <div class="panel-body text-center">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_actual_cost')); ?></div>
                    <div class="tw-text-2xl tw-font-bold text-danger">
                        <?php echo app_format_money($profit_summary['actual_cost'] ?? 0, $currency_name); ?>
                    </div>
                    <?php if (!empty($profit_summary['expected_cost'])) { ?>
                    <small class="text-muted">
                        / <?php echo app_format_money($profit_summary['expected_cost'], $currency_name); ?>
                    </small>
                    <?php } ?>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="panel_s panel-<?php echo $margin_class; ?>">
                <div class="panel-body text-center">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_net_profit')); ?></div>
                    <div class="tw-text-2xl tw-font-bold">
                        <?php echo app_format_money($profit_summary['net_profit'] ?? 0, $currency_name); ?>
                    </div>
                </div>
            </div>

            <!-- Margin -->
            <div class="panel_s panel-<?php echo $margin_class; ?>">
                <div class="panel-body text-center">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_margin_percent')); ?></div>
                    <div class="tw-text-2xl tw-font-bold">
                        <?php echo e(number_format($margin_percent, 1)); ?>%
                    </div>
                </div>
            </div>
        </div>

        <!-- CR Impact Summary -->
        <div class="mtop20">
            <h5><?php echo e(_l('disowebs_ops_cr_impact')); ?></h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="panel_s panel-default">
                        <div class="panel-body text-center">
                            <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_revenue_impact')); ?></div>
                            <div class="tw-text-lg tw-font-semibold text-success">
                                +<?php echo app_format_money($profit_summary['cr_revenue_impact'] ?? 0, $currency_name); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel_s panel-default">
                        <div class="panel-body text-center">
                            <div class="text-muted"><?php echo e(_l('disowebs_ops_cr_cost_impact')); ?></div>
                            <div class="tw-text-lg tw-font-semibold text-danger">
                                +<?php echo app_format_money($profit_summary['cr_cost_impact'] ?? 0, $currency_name); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel_s panel-default">
                        <div class="panel-body text-center">
                            <div class="text-muted"><?php echo e(_l('disowebs_ops_effort_hours')); ?></div>
                            <div class="tw-text-lg tw-font-semibold">
                                <?php echo e(number_format($profit_summary['effort_hours'] ?? 0, 1)); ?> <?php echo e(_l('disowebs_ops_hours')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Margin Alerts -->
<?php if (!empty($margin_alerts)) { ?>
<div class="panel_s mtop15">
    <div class="panel-body">
        <h4 class="no-margin text-danger"><i class="fa fa-exclamation-triangle"></i> <?php echo e(_l('disowebs_ops_margin_alerts')); ?></h4>
        <div class="table-responsive mtop15">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo e(_l('disowebs_ops_margin_alert_type')); ?></th>
                        <th><?php echo e(_l('disowebs_ops_margin_alert_message')); ?></th>
                        <th><?php echo e(_l('date')); ?></th>
                        <th><?php echo e(_l('options')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($margin_alerts as $alert) { ?>
                    <tr class="<?php echo $alert['acknowledged'] ? '' : 'warning'; ?>">
                        <td>
                            <?php
                            $type_class = 'default';
                            switch ($alert['alert_type']) {
                                case 'cr_no_impact':
                                    $type_class = 'warning';
                                    break;
                                case 'low_margin':
                                    $type_class = 'danger';
                                    break;
                                case 'margin_erosion':
                                    $type_class = 'danger';
                                    break;
                            }
                            ?>
                            <span class="label label-<?php echo $type_class; ?>">
                                <?php echo e(_l('disowebs_ops_margin_alert_' . $alert['alert_type'])); ?>
                            </span>
                        </td>
                        <td><?php echo e($alert['message']); ?></td>
                        <td><?php echo e(_dt($alert['created_at'])); ?></td>
                        <td>
                            <?php if (!$alert['acknowledged'] && $is_ceo) { ?>
                            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/acknowledge_alert/' . $project->id . '/' . $alert['id'])); ?>
                            <button type="submit" class="btn btn-xs btn-success"><?php echo e(_l('disowebs_ops_acknowledge_alert')); ?></button>
                            <?php echo form_close(); ?>
                            <?php } elseif ($alert['acknowledged']) { ?>
                            <span class="text-success"><i class="fa fa-check"></i> <?php echo e(_l('disowebs_ops_alert_acknowledged')); ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- Profit Breakdown Details -->
<div class="panel_s mtop15">
    <div class="panel-body">
        <h4 class="no-margin"><?php echo e(_l('disowebs_ops_profit_breakdown')); ?></h4>
        <div class="row mtop15">
            <div class="col-md-6">
                <h5><?php echo e(_l('disowebs_ops_revenue_sources')); ?></h5>
                <table class="table">
                    <tbody>
                        <tr>
                            <td><?php echo e(_l('disowebs_ops_invoiced_amount')); ?></td>
                            <td class="text-right"><?php echo app_format_money($profit_summary['invoiced_amount'] ?? 0, $currency_name); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo e(_l('disowebs_ops_paid_amount')); ?></td>
                            <td class="text-right text-success"><?php echo app_format_money($profit_summary['actual_revenue'] ?? 0, $currency_name); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo e(_l('disowebs_ops_cr_additional_revenue')); ?></td>
                            <td class="text-right text-success">+<?php echo app_format_money($profit_summary['cr_revenue_impact'] ?? 0, $currency_name); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h5><?php echo e(_l('disowebs_ops_cost_breakdown')); ?></h5>
                <table class="table">
                    <tbody>
                        <tr>
                            <td><?php echo e(_l('disowebs_ops_base_cost')); ?></td>
                            <td class="text-right"><?php echo app_format_money(($profit_summary['actual_cost'] ?? 0) - ($profit_summary['cr_cost_impact'] ?? 0), $currency_name); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo e(_l('disowebs_ops_cr_cost_addition')); ?></td>
                            <td class="text-right text-danger">+<?php echo app_format_money($profit_summary['cr_cost_impact'] ?? 0, $currency_name); ?></td>
                        </tr>
                        <tr class="active">
                            <td><strong><?php echo e(_l('disowebs_ops_total_cost')); ?></strong></td>
                            <td class="text-right text-danger"><strong><?php echo app_format_money($profit_summary['actual_cost'] ?? 0, $currency_name); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($can_manage) { ?>
<!-- Manual Cost Entry -->
<div class="panel_s mtop15">
    <div class="panel-body">
        <div class="tw-flex tw-items-center tw-justify-between">
            <h4 class="no-margin"><?php echo e(_l('disowebs_ops_add_cost_entry')); ?></h4>
        </div>
        <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/add_cost_entry/' . $project->id)); ?>
        <div class="row mtop15">
            <div class="col-md-4">
                <?php echo render_input('cost_description', 'description', '', 'text', ['required' => true]); ?>
            </div>
            <div class="col-md-3">
                <?php echo render_input('cost_amount', 'amount', '', 'number', ['step' => '0.01', 'required' => true]); ?>
            </div>
            <div class="col-md-3">
                <?php echo render_select('cost_type', [
                    ['id' => 'expense', 'name' => _l('disowebs_ops_expense')],
                    ['id' => 'contractor', 'name' => _l('disowebs_ops_contractor_cost')],
                    ['id' => 'material', 'name' => _l('disowebs_ops_material_cost')],
                    ['id' => 'other', 'name' => _l('disowebs_ops_other_cost')]
                ], ['id', 'name'], 'type'); ?>
            </div>
            <div class="col-md-2">
                <div class="form-group" style="margin-top:26px;">
                    <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_add')); ?></button>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php } ?>
