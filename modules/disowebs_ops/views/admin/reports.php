<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Header with Period Filter -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                            <div>
                                <h4 class="no-margin tw-font-bold"><?php echo e($title); ?></h4>
                                <p class="text-muted mtop5">
                                    <?php echo e(_l('disowebs_ops_reports_subtitle')); ?>
                                    <span class="text-info">
                                        <?php echo e(_d($range['start'])); ?> - <?php echo e(_d($range['end'])); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="mtop10 md:mtop0">
                                <?php echo form_open(admin_url('disowebs_ops/reports'), ['method' => 'get', 'class' => 'form-inline']); ?>
                                <select name="period" class="selectpicker" data-width="150px" onchange="this.form.submit()">
                                    <?php foreach ($period_options as $key => $label) { ?>
                                    <option value="<?php echo e($key); ?>" <?php echo $period === $key ? 'selected' : ''; ?>>
                                        <?php echo e($label); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI Cards Row -->
                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 lg:tw-grid-cols-6 tw-gap-4 mtop15">
                    <div class="panel_s">
                        <div class="panel-body text-center">
                            <div class="tw-text-3xl tw-font-bold text-success"><?php echo e($kpi['projects_delivered']); ?></div>
                            <div class="text-muted"><?php echo e(_l('disowebs_ops_projects_delivered')); ?></div>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body text-center">
                            <div class="tw-text-3xl tw-font-bold text-primary"><?php echo e($kpi['milestones_done']); ?></div>
                            <div class="text-muted"><?php echo e(_l('disowebs_ops_milestones_done')); ?></div>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body text-center">
                            <div class="tw-text-3xl tw-font-bold text-info"><?php echo e($kpi['proof_entries']); ?></div>
                            <div class="text-muted"><?php echo e(_l('disowebs_ops_proof_entries')); ?></div>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body text-center">
                            <?php 
                            $currency = function_exists('get_base_currency') ? get_base_currency() : null;
                            $currency_symbol = $currency && isset($currency->symbol) ? $currency->symbol : '$';
                            $currency_name = $currency && isset($currency->name) ? $currency->name : '';
                            ?>
                            <div class="tw-text-3xl tw-font-bold text-success"><?php echo disowebs_ops_format_compact_money($kpi['revenue'], $currency_symbol); ?></div>
                            <div class="text-muted"><?php echo e(_l('disowebs_ops_revenue_collected')); ?></div>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body text-center">
                            <div class="tw-text-3xl tw-font-bold <?php echo $kpi['active_projects'] > 4 ? 'text-warning' : 'text-primary'; ?>">
                                <?php echo e($kpi['active_projects']); ?>
                            </div>
                            <div class="text-muted"><?php echo e(_l('disowebs_ops_active_projects')); ?></div>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body text-center">
                            <div class="tw-text-3xl tw-font-bold <?php echo $kpi['pending_crs'] > 0 ? 'text-warning' : 'text-success'; ?>">
                                <?php echo e($kpi['pending_crs']); ?>
                            </div>
                            <div class="text-muted"><?php echo e(_l('disowebs_ops_pending_crs')); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Delivery & Revenue Row -->
                <div class="row mtop15">
                    <!-- Delivery Engine Report -->
                    <div class="col-md-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold no-margin"><?php echo e(_l('disowebs_ops_delivery_engine')); ?></h5>
                                <hr class="hr-panel-separator" />
                                
                                <!-- Phases by Status -->
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <div class="tw-text-2xl tw-font-bold text-muted">
                                            <?php echo e($delivery['phases_by_status']['not_started'] ?? 0); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_phase_not_started')); ?></small>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <div class="tw-text-2xl tw-font-bold text-primary">
                                            <?php echo e($delivery['phases_by_status']['in_progress'] ?? 0); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_phase_in_progress')); ?></small>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <div class="tw-text-2xl tw-font-bold text-success">
                                            <?php echo e($delivery['phases_by_status']['done'] ?? 0); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_phase_done')); ?></small>
                                    </div>
                                </div>

                                <hr />

                                <!-- Blockers & Demos -->
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <div class="tw-text-2xl tw-font-bold <?php echo $delivery['active_blockers'] > 0 ? 'text-danger' : 'text-success'; ?>">
                                            <?php echo e($delivery['active_blockers']); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_active_blockers')); ?></small>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <div class="tw-text-2xl tw-font-bold <?php echo $delivery['overdue_blockers'] > 0 ? 'text-danger' : 'text-success'; ?>">
                                            <?php echo e($delivery['overdue_blockers']); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_overdue_blockers')); ?></small>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <div class="tw-text-2xl tw-font-bold text-info">
                                            <?php echo e($delivery['demos_completed']); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_demos_completed')); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Report -->
                    <div class="col-md-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold no-margin"><?php echo e(_l('disowebs_ops_revenue_report')); ?></h5>
                                <hr class="hr-panel-separator" />
                                
                                <div class="row">
                                    <div class="col-xs-6 text-center">
                                        <div class="tw-text-2xl tw-font-bold text-warning">
                                            <?php echo disowebs_ops_format_compact_money($revenue['outstanding'], $currency_symbol); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_outstanding')); ?></small>
                                    </div>
                                    <div class="col-xs-6 text-center">
                                        <div class="tw-text-2xl tw-font-bold <?php echo $revenue['overdue_count'] > 0 ? 'text-danger' : 'text-success'; ?>">
                                            <?php echo e($revenue['overdue_count']); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_overdue_invoices')); ?></small>
                                    </div>
                                </div>

                                <?php if (!empty($revenue['payments_by_date'])) { ?>
                                <hr />
                                <h6 class="text-muted"><?php echo e(_l('disowebs_ops_payments_timeline')); ?></h6>
                                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                                    <table class="table table-condensed no-margin">
                                        <tbody>
                                            <?php foreach ($revenue['payments_by_date'] as $payment) { ?>
                                            <tr>
                                                <td><?php echo e(_d($payment['pay_date'])); ?></td>
                                                <td class="text-right text-success tw-font-semibold">
                                                    <?php echo disowebs_ops_format_compact_money($payment['total'], $currency_symbol); ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php } else { ?>
                                <p class="text-muted mtop15"><?php echo e(_l('disowebs_ops_no_payments_period')); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Growth & Retention Row -->
                <div class="row mtop15">
                    <!-- Growth Flywheel -->
                    <div class="col-md-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold no-margin"><?php echo e(_l('disowebs_ops_growth_flywheel')); ?></h5>
                                <hr class="hr-panel-separator" />
                                
                                <div class="row">
                                    <div class="col-xs-6 col-md-3 text-center">
                                        <div class="tw-text-2xl tw-font-bold text-info">
                                            <?php echo e($growth['proof_entries']); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_proof_entries')); ?></small>
                                    </div>
                                    <div class="col-xs-6 col-md-3 text-center">
                                        <div class="tw-text-2xl tw-font-bold text-warning">
                                            <?php echo e($growth['testimonials_pending']); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_testimonials_pending')); ?></small>
                                    </div>
                                    <div class="col-xs-6 col-md-3 text-center">
                                        <div class="tw-text-2xl tw-font-bold text-success">
                                            <?php echo e($growth['testimonials_received']); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_testimonials_received')); ?></small>
                                    </div>
                                    <div class="col-xs-6 col-md-3 text-center">
                                        <div class="tw-text-2xl tw-font-bold <?php echo $growth['proof_backlog'] > 0 ? 'text-danger' : 'text-success'; ?>">
                                            <?php echo e($growth['proof_backlog']); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_proof_backlog')); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Retention Engine -->
                    <div class="col-md-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold no-margin"><?php echo e(_l('disowebs_ops_retention_engine')); ?></h5>
                                <hr class="hr-panel-separator" />
                                
                                <div class="row">
                                    <div class="col-xs-6 col-md-3 text-center">
                                        <div class="tw-text-2xl tw-font-bold text-primary">
                                            <?php echo e($retention['launched'] ?? 0); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_projects_launched')); ?></small>
                                    </div>
                                    <div class="col-xs-6 col-md-3 text-center">
                                        <div class="tw-text-2xl tw-font-bold text-info">
                                            <?php echo e($retention['offered'] ?? 0); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_retainers_offered')); ?></small>
                                    </div>
                                    <div class="col-xs-6 col-md-3 text-center">
                                        <div class="tw-text-2xl tw-font-bold text-success">
                                            <?php echo e($retention['accepted'] ?? 0); ?>
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_retainers_accepted')); ?></small>
                                    </div>
                                    <div class="col-xs-6 col-md-3 text-center">
                                        <?php 
                                        $conversion_rate = ($retention['offered'] ?? 0) > 0 
                                            ? round((($retention['accepted'] ?? 0) / ($retention['offered'] ?? 1)) * 100) 
                                            : 0;
                                        ?>
                                        <div class="tw-text-2xl tw-font-bold <?php echo $conversion_rate >= 30 ? 'text-success' : 'text-warning'; ?>">
                                            <?php echo e($conversion_rate); ?>%
                                        </div>
                                        <small class="text-muted"><?php echo e(_l('disowebs_ops_conversion_rate')); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profit Engine Summary -->
                <div class="panel_s mtop15">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold no-margin"><?php echo e(_l('disowebs_ops_profit_engine')); ?></h5>
                        <hr class="hr-panel-separator" />
                        
                        <div class="row">
                            <div class="col-md-2 col-xs-6 text-center">
                                <div class="tw-text-2xl tw-font-bold text-success">
                                    <?php echo disowebs_ops_format_compact_money($profit['total_actual_revenue'] ?? 0, $currency_symbol); ?>
                                </div>
                                <small class="text-muted"><?php echo e(_l('disowebs_ops_total_revenue')); ?></small>
                            </div>
                            <div class="col-md-2 col-xs-6 text-center">
                                <div class="tw-text-2xl tw-font-bold text-danger">
                                    <?php echo disowebs_ops_format_compact_money($profit['total_actual_cost'] ?? 0, $currency_symbol); ?>
                                </div>
                                <small class="text-muted"><?php echo e(_l('disowebs_ops_total_cost')); ?></small>
                            </div>
                            <div class="col-md-2 col-xs-6 text-center">
                                <?php $net = ($profit['total_actual_revenue'] ?? 0) - ($profit['total_actual_cost'] ?? 0); ?>
                                <div class="tw-text-2xl tw-font-bold <?php echo $net >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo disowebs_ops_format_compact_money($net, $currency_symbol); ?>
                                </div>
                                <small class="text-muted"><?php echo e(_l('disowebs_ops_net_profit')); ?></small>
                            </div>
                            <div class="col-md-2 col-xs-6 text-center">
                                <?php 
                                $avg_margin = $profit['avg_margin_percent'] ?? 0;
                                $margin_class = $avg_margin >= 40 ? 'success' : ($avg_margin >= 25 ? 'info' : ($avg_margin >= 15 ? 'warning' : 'danger'));
                                ?>
                                <div class="tw-text-2xl tw-font-bold text-<?php echo $margin_class; ?>">
                                    <?php echo e(number_format($avg_margin, 1)); ?>%
                                </div>
                                <small class="text-muted"><?php echo e(_l('disowebs_ops_avg_margin')); ?></small>
                            </div>
                            <div class="col-md-2 col-xs-6 text-center">
                                <div class="tw-text-2xl tw-font-bold text-warning">
                                    <?php echo disowebs_ops_format_compact_money($profit['total_cr_impact'] ?? 0, $currency_symbol); ?>
                                </div>
                                <small class="text-muted"><?php echo e(_l('disowebs_ops_cr_impact')); ?></small>
                            </div>
                            <div class="col-md-2 col-xs-6 text-center">
                                <div class="tw-text-2xl tw-font-bold text-primary">
                                    <?php echo e($profit['projects_tracked'] ?? 0); ?>
                                </div>
                                <small class="text-muted"><?php echo e(_l('disowebs_ops_projects_tracked')); ?></small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
