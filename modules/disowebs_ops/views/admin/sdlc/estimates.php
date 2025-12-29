<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php $this->load->view('admin/includes/alerts'); ?>

            <!-- Header -->
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                    <div>
                        <h4 class="tw-font-bold tw-text-xl tw-mb-0">
                            <i class="fa fa-clock-o tw-mr-2"></i> <?php echo _l('disowebs_ops_time_estimates'); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_time_estimates_desc'); ?></p>
                    </div>
                    <div>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc'); ?>" class="btn btn-default tw-mr-2">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newEstimateModal">
                            <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_new_estimate'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- PERT Calculator -->
            <div class="col-md-12">
                <div class="panel_s tw-bg-gradient-to-r tw-from-blue-50 tw-to-indigo-50">
                    <div class="panel-heading">
                        <h4 class="tw-font-semibold tw-mb-0"><i class="fa fa-calculator"></i> <?php echo _l('disowebs_ops_pert_calculator'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo _l('optimistic'); ?> (O)</label>
                                    <div class="input-group">
                                        <input type="number" id="pertOptimistic" class="form-control" step="0.5" min="0" onchange="calculatePERT()">
                                        <span class="input-group-addon"><?php echo _l('hours'); ?></span>
                                    </div>
                                    <small class="tw-text-neutral-500"><?php echo _l('disowebs_ops_optimistic_help'); ?></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo _l('most_likely'); ?> (M)</label>
                                    <div class="input-group">
                                        <input type="number" id="pertMostLikely" class="form-control" step="0.5" min="0" onchange="calculatePERT()">
                                        <span class="input-group-addon"><?php echo _l('hours'); ?></span>
                                    </div>
                                    <small class="tw-text-neutral-500"><?php echo _l('disowebs_ops_most_likely_help'); ?></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo _l('pessimistic'); ?> (P)</label>
                                    <div class="input-group">
                                        <input type="number" id="pertPessimistic" class="form-control" step="0.5" min="0" onchange="calculatePERT()">
                                        <span class="input-group-addon"><?php echo _l('hours'); ?></span>
                                    </div>
                                    <small class="tw-text-neutral-500"><?php echo _l('disowebs_ops_pessimistic_help'); ?></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo _l('disowebs_ops_pert_estimate'); ?></label>
                                    <div class="tw-bg-white tw-p-3 tw-rounded tw-border tw-text-center">
                                        <span class="tw-text-3xl tw-font-bold tw-text-primary" id="pertResult">-</span>
                                        <span class="tw-text-neutral-500"><?php echo _l('hours'); ?></span>
                                    </div>
                                    <small class="tw-text-neutral-500"><?php echo _l('disowebs_ops_formula'); ?>: (O + 4M + P) / 6</small>
                                </div>
                            </div>
                        </div>
                        <div class="row tw-mt-2">
                            <div class="col-md-12">
                                <div class="tw-flex tw-justify-between tw-items-center tw-text-sm">
                                    <span><?php echo _l('standard_deviation'); ?>: <strong id="pertStdDev">-</strong></span>
                                    <span><?php echo _l('disowebs_ops_confidence_range'); ?>: <strong id="pertRange">-</strong></span>
                                    <button type="button" class="btn btn-info btn-sm" onclick="savePERTEstimate()" id="savePERTBtn" disabled>
                                        <i class="fa fa-save"></i> <?php echo _l('save_estimate'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estimates by Project -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <h4 class="tw-font-semibold tw-mb-0"><?php echo _l('disowebs_ops_saved_estimates'); ?></h4>
                            <select id="filterProject" class="selectpicker" data-width="200px" data-live-search="true" onchange="filterEstimates()">
                                <option value=""><?php echo _l('all_projects'); ?></option>
                                <?php foreach ($projects as $p) { ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo _l('task'); ?></th>
                                    <th><?php echo _l('project'); ?></th>
                                    <th width="100" class="text-center"><?php echo _l('optimistic'); ?></th>
                                    <th width="100" class="text-center"><?php echo _l('most_likely'); ?></th>
                                    <th width="100" class="text-center"><?php echo _l('pessimistic'); ?></th>
                                    <th width="100" class="text-center"><?php echo _l('estimate'); ?></th>
                                    <th width="100" class="text-center"><?php echo _l('actual'); ?></th>
                                    <th width="100" class="text-center"><?php echo _l('variance'); ?></th>
                                    <th width="80"><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($estimates)) { ?>
                                <?php foreach ($estimates as $est) { ?>
                                <tr class="estimate-row" data-project="<?php echo $est->project_id; ?>">
                                    <td>
                                        <strong><?php echo e($est->task_name); ?></strong>
                                        <?php if ($est->task_id) { ?>
                                        <a href="<?php echo admin_url('tasks/view/' . $est->task_id); ?>" target="_blank" class="tw-ml-1">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($est->project_name) { ?>
                                        <?php echo e($est->project_name); ?>
                                        <?php } else { ?>
                                        <span class="tw-text-neutral-400">-</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center"><?php echo $est->optimistic_hours; ?>h</td>
                                    <td class="text-center"><?php echo $est->most_likely_hours; ?>h</td>
                                    <td class="text-center"><?php echo $est->pessimistic_hours; ?>h</td>
                                    <td class="text-center">
                                        <strong class="tw-text-primary"><?php echo number_format($est->pert_estimate, 1); ?>h</strong>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($est->actual_hours > 0) { ?>
                                        <?php echo $est->actual_hours; ?>h
                                        <?php } else { ?>
                                        <input type="number" class="form-control input-sm tw-w-16 tw-inline-block" 
                                               step="0.5" min="0" onblur="updateActual(<?php echo $est->id; ?>, this.value)">
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($est->actual_hours > 0) { 
                                            $variance = (($est->actual_hours - $est->pert_estimate) / $est->pert_estimate) * 100;
                                            $variance_class = abs($variance) < 10 ? 'success' : (abs($variance) < 25 ? 'warning' : 'danger');
                                        ?>
                                        <span class="label label-<?php echo $variance_class; ?>">
                                            <?php echo ($variance > 0 ? '+' : '') . number_format($variance, 0); ?>%
                                        </span>
                                        <?php } else { ?>
                                        <span class="tw-text-neutral-400">-</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-xs" onclick="deleteEstimate(<?php echo $est->id; ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } else { ?>
                                <tr>
                                    <td colspan="9" class="text-center tw-py-6 tw-text-neutral-400">
                                        <?php echo _l('disowebs_ops_no_estimates'); ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                            <?php if (!empty($estimates)) { ?>
                            <tfoot>
                                <tr class="tw-bg-neutral-100 tw-font-bold">
                                    <td colspan="5" class="text-right"><?php echo _l('totals'); ?>:</td>
                                    <td class="text-center"><?php echo number_format(array_sum(array_column($estimates, 'pert_estimate')), 1); ?>h</td>
                                    <td class="text-center"><?php echo array_sum(array_column($estimates, 'actual_hours')); ?>h</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Estimation Accuracy Stats -->
            <?php if (!empty($accuracy_stats)) { ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="tw-font-semibold tw-mb-0"><?php echo _l('disowebs_ops_estimation_accuracy'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 tw-text-center">
                                <h4 class="tw-mb-1 tw-font-bold"><?php echo number_format($accuracy_stats['avg_variance'], 1); ?>%</h4>
                                <small class="tw-text-neutral-500"><?php echo _l('average_variance'); ?></small>
                            </div>
                            <div class="col-md-3 tw-text-center">
                                <h4 class="tw-mb-1 tw-font-bold tw-text-success"><?php echo $accuracy_stats['accurate_count']; ?></h4>
                                <small class="tw-text-neutral-500"><?php echo _l('accurate'); ?> (±10%)</small>
                            </div>
                            <div class="col-md-3 tw-text-center">
                                <h4 class="tw-mb-1 tw-font-bold tw-text-warning"><?php echo $accuracy_stats['underestimated']; ?></h4>
                                <small class="tw-text-neutral-500"><?php echo _l('underestimated'); ?></small>
                            </div>
                            <div class="col-md-3 tw-text-center">
                                <h4 class="tw-mb-1 tw-font-bold tw-text-info"><?php echo $accuracy_stats['overestimated']; ?></h4>
                                <small class="tw-text-neutral-500"><?php echo _l('overestimated'); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>
</div>

<!-- New Estimate Modal -->
<div class="modal fade" id="newEstimateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php echo form_open(admin_url('disowebs_ops/sdlc/save_estimate'), ['id' => 'estimateForm']); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('disowebs_ops_new_estimate'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><?php echo _l('task_name'); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="task_name" id="estTaskName" class="form-control" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('project'); ?></label>
                            <select name="project_id" id="estProject" class="selectpicker" data-width="100%" data-live-search="true">
                                <option value=""><?php echo _l('no_project'); ?></option>
                                <?php foreach ($projects as $p) { ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('link_to_task'); ?></label>
                            <select name="task_id" id="estTask" class="selectpicker" data-width="100%" data-live-search="true">
                                <option value=""><?php echo _l('none'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <hr>
                <h5><?php echo _l('disowebs_ops_pert_values'); ?></h5>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?php echo _l('optimistic'); ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="optimistic_hours" id="estOptimistic" class="form-control" step="0.5" min="0" required onchange="calculateModalPERT()">
                                <span class="input-group-addon">h</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?php echo _l('most_likely'); ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="most_likely_hours" id="estMostLikely" class="form-control" step="0.5" min="0" required onchange="calculateModalPERT()">
                                <span class="input-group-addon">h</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?php echo _l('pessimistic'); ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="pessimistic_hours" id="estPessimistic" class="form-control" step="0.5" min="0" required onchange="calculateModalPERT()">
                                <span class="input-group-addon">h</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tw-bg-neutral-100 tw-p-3 tw-rounded tw-text-center">
                    <?php echo _l('disowebs_ops_pert_estimate'); ?>: <strong class="tw-text-xl" id="modalPertResult">-</strong> <?php echo _l('hours'); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
function calculatePERT() {
    var o = parseFloat($('#pertOptimistic').val()) || 0;
    var m = parseFloat($('#pertMostLikely').val()) || 0;
    var p = parseFloat($('#pertPessimistic').val()) || 0;
    
    if (o > 0 && m > 0 && p > 0) {
        var pert = (o + 4*m + p) / 6;
        var stdDev = (p - o) / 6;
        
        $('#pertResult').text(pert.toFixed(1));
        $('#pertStdDev').text(stdDev.toFixed(2) + 'h');
        $('#pertRange').text((pert - stdDev).toFixed(1) + ' - ' + (pert + stdDev).toFixed(1) + 'h');
        $('#savePERTBtn').prop('disabled', false);
    } else {
        $('#pertResult').text('-');
        $('#pertStdDev').text('-');
        $('#pertRange').text('-');
        $('#savePERTBtn').prop('disabled', true);
    }
}

function calculateModalPERT() {
    var o = parseFloat($('#estOptimistic').val()) || 0;
    var m = parseFloat($('#estMostLikely').val()) || 0;
    var p = parseFloat($('#estPessimistic').val()) || 0;
    
    if (o > 0 && m > 0 && p > 0) {
        var pert = (o + 4*m + p) / 6;
        $('#modalPertResult').text(pert.toFixed(1));
    } else {
        $('#modalPertResult').text('-');
    }
}

function savePERTEstimate() {
    var taskName = prompt('<?php echo _l('task_name'); ?>:');
    if (!taskName) return;
    
    $.post(admin_url + 'disowebs_ops/sdlc/save_estimate', {
        task_name: taskName,
        optimistic_hours: $('#pertOptimistic').val(),
        most_likely_hours: $('#pertMostLikely').val(),
        pessimistic_hours: $('#pertPessimistic').val()
    }, function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            alert_float('success', '<?php echo _l('saved'); ?>');
            setTimeout(function() { location.reload(); }, 500);
        }
    });
}

function filterEstimates() {
    var project = $('#filterProject').val();
    $('.estimate-row').each(function() {
        var $row = $(this);
        if (!project || $row.data('project') == project) {
            $row.show();
        } else {
            $row.hide();
        }
    });
}

function updateActual(id, value) {
    if (!value || value <= 0) return;
    $.post(admin_url + 'disowebs_ops/sdlc/update_estimate_actual/' + id, {
        actual_hours: value
    }, function(response) {
        location.reload();
    });
}

function deleteEstimate(id) {
    if (confirm('<?php echo _l('confirm_delete'); ?>')) {
        $.post(admin_url + 'disowebs_ops/sdlc/delete_estimate/' + id, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                location.reload();
            }
        });
    }
}

$(function() {
    appValidateForm($('#estimateForm'), {
        task_name: 'required',
        optimistic_hours: 'required',
        most_likely_hours: 'required',
        pessimistic_hours: 'required'
    });
});
</script>
