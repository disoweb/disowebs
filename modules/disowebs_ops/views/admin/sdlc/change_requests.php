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
                            <i class="fa fa-exchange tw-mr-2"></i> <?php echo _l('disowebs_ops_change_requests'); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_change_requests_desc'); ?></p>
                    </div>
                    <div>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc'); ?>" class="btn btn-default tw-mr-2">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newChangeRequestModal">
                            <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_new_change_request'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel_s tw-text-center tw-border-l-4 tw-border-l-info">
                            <div class="panel-body tw-py-3">
                                <h3 class="tw-mb-0 tw-font-bold tw-text-info"><?php echo $stats['pending'] ?? 0; ?></h3>
                                <small class="tw-text-neutral-500"><?php echo _l('pending'); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel_s tw-text-center tw-border-l-4 tw-border-l-warning">
                            <div class="panel-body tw-py-3">
                                <h3 class="tw-mb-0 tw-font-bold tw-text-warning"><?php echo $stats['under_review'] ?? 0; ?></h3>
                                <small class="tw-text-neutral-500"><?php echo _l('under_review'); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel_s tw-text-center tw-border-l-4 tw-border-l-success">
                            <div class="panel-body tw-py-3">
                                <h3 class="tw-mb-0 tw-font-bold tw-text-success"><?php echo $stats['approved'] ?? 0; ?></h3>
                                <small class="tw-text-neutral-500"><?php echo _l('approved'); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel_s tw-text-center tw-border-l-4 tw-border-l-danger">
                            <div class="panel-body tw-py-3">
                                <h3 class="tw-mb-0 tw-font-bold tw-text-danger"><?php echo $stats['rejected'] ?? 0; ?></h3>
                                <small class="tw-text-neutral-500"><?php echo _l('rejected'); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body tw-py-2">
                        <div class="row">
                            <div class="col-md-3">
                                <select id="filterProject" class="selectpicker" data-width="100%" data-live-search="true" onchange="filterCR()">
                                    <option value=""><?php echo _l('all_projects'); ?></option>
                                    <?php foreach ($projects as $p) { ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filterStatus" class="selectpicker" data-width="100%" onchange="filterCR()">
                                    <option value=""><?php echo _l('all'); ?></option>
                                    <option value="pending"><?php echo _l('pending'); ?></option>
                                    <option value="under_review"><?php echo _l('under_review'); ?></option>
                                    <option value="approved"><?php echo _l('approved'); ?></option>
                                    <option value="rejected"><?php echo _l('rejected'); ?></option>
                                    <option value="implemented"><?php echo _l('implemented'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filterImpact" class="selectpicker" data-width="100%" onchange="filterCR()">
                                    <option value=""><?php echo _l('all_impact'); ?></option>
                                    <option value="high"><?php echo _l('high'); ?></option>
                                    <option value="medium"><?php echo _l('medium'); ?></option>
                                    <option value="low"><?php echo _l('low'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="text" id="searchCR" class="form-control" placeholder="<?php echo _l('search'); ?>..." onkeyup="filterCR()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Requests Table -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <table class="table table-hover" id="crTable">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th><?php echo _l('title'); ?></th>
                                    <th><?php echo _l('project'); ?></th>
                                    <th><?php echo _l('impact'); ?></th>
                                    <th><?php echo _l('status'); ?></th>
                                    <th><?php echo _l('cost_impact'); ?></th>
                                    <th><?php echo _l('time_impact'); ?></th>
                                    <th><?php echo _l('requested_by'); ?></th>
                                    <th><?php echo _l('date'); ?></th>
                                    <th width="100"><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($change_requests as $cr) { ?>
                                <tr class="cr-row" data-project="<?php echo $cr->project_id; ?>" data-status="<?php echo $cr->status; ?>" data-impact="<?php echo $cr->impact_level; ?>">
                                    <td><strong>CR-<?php echo str_pad($cr->id, 4, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td>
                                        <a href="#" onclick="viewCR(<?php echo $cr->id; ?>); return false;" class="tw-font-medium">
                                            <?php echo e($cr->title); ?>
                                        </a>
                                        <?php if ($cr->scope_id) { ?>
                                        <span class="label label-info tw-ml-1" data-toggle="tooltip" title="<?php echo _l('linked_to_scope'); ?>">
                                            <i class="fa fa-link"></i>
                                        </span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($cr->project_name) { ?>
                                        <a href="<?php echo admin_url('projects/view/' . $cr->project_id); ?>" target="_blank">
                                            <?php echo e($cr->project_name); ?>
                                        </a>
                                        <?php } else { ?>
                                        <span class="tw-text-neutral-400">-</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php
                                        $impact_class = ['high' => 'danger', 'medium' => 'warning', 'low' => 'info'];
                                        ?>
                                        <span class="label label-<?php echo $impact_class[$cr->impact_level] ?? 'default'; ?>">
                                            <?php echo ucfirst($cr->impact_level); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'pending' => 'default',
                                            'under_review' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'implemented' => 'primary'
                                        ];
                                        ?>
                                        <span class="label label-<?php echo $status_class[$cr->status] ?? 'default'; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $cr->status)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($cr->cost_impact > 0) { ?>
                                        <span class="tw-text-danger">+<?php echo app_format_money($cr->cost_impact, get_base_currency()); ?></span>
                                        <?php } else { ?>
                                        <span class="tw-text-neutral-400">-</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($cr->time_impact > 0) { ?>
                                        <span class="tw-text-warning">+<?php echo $cr->time_impact; ?>h</span>
                                        <?php } else { ?>
                                        <span class="tw-text-neutral-400">-</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($cr->requested_by) { ?>
                                        <?php echo get_staff_full_name($cr->requested_by); ?>
                                        <?php } elseif ($cr->client_id) { ?>
                                        <span class="tw-text-info"><i class="fa fa-building"></i> <?php echo _l('client'); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo _d($cr->request_date); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-xs" onclick="viewCR(<?php echo $cr->id; ?>)">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <?php if ($cr->status == 'pending' || $cr->status == 'under_review') { ?>
                                            <button type="button" class="btn btn-success btn-xs" onclick="approveCR(<?php echo $cr->id; ?>)" data-toggle="tooltip" title="<?php echo _l('approve'); ?>">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs" onclick="rejectCR(<?php echo $cr->id; ?>)" data-toggle="tooltip" title="<?php echo _l('reject'); ?>">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- New Change Request Modal -->
<div class="modal fade" id="newChangeRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php echo form_open(admin_url('disowebs_ops/sdlc/save_change_request'), ['id' => 'crForm']); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('disowebs_ops_new_change_request'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('title'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('project'); ?></label>
                            <select name="project_id" class="selectpicker" data-width="100%" data-live-search="true">
                                <option value=""><?php echo _l('no_project'); ?></option>
                                <?php foreach ($projects as $p) { ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('scope_document'); ?></label>
                            <select name="scope_id" class="selectpicker" data-width="100%" data-live-search="true">
                                <option value=""><?php echo _l('none'); ?></option>
                                <?php foreach ($scopes as $s) { ?>
                                <option value="<?php echo $s->id; ?>"><?php echo e($s->title); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label><?php echo _l('description'); ?> <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="4" required placeholder="<?php echo _l('disowebs_ops_cr_desc_placeholder'); ?>"></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo _l('reason'); ?></label>
                    <textarea name="reason" class="form-control" rows="2" placeholder="<?php echo _l('disowebs_ops_cr_reason_placeholder'); ?>"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?php echo _l('impact_level'); ?></label>
                            <select name="impact_level" class="selectpicker" data-width="100%">
                                <option value="low"><?php echo _l('low'); ?></option>
                                <option value="medium" selected><?php echo _l('medium'); ?></option>
                                <option value="high"><?php echo _l('high'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?php echo _l('cost_impact'); ?></label>
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo get_base_currency()->symbol; ?></span>
                                <input type="number" name="cost_impact" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?php echo _l('time_impact'); ?></label>
                            <div class="input-group">
                                <input type="number" name="time_impact" class="form-control" step="0.5" min="0">
                                <span class="input-group-addon"><?php echo _l('hours'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- View Change Request Modal -->
<div class="modal fade" id="viewCRModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="viewCRTitle"></h4>
            </div>
            <div class="modal-body" id="viewCRBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
function filterCR() {
    var project = $('#filterProject').val();
    var status = $('#filterStatus').val();
    var impact = $('#filterImpact').val();
    var search = $('#searchCR').val().toLowerCase();
    
    $('.cr-row').each(function() {
        var $row = $(this);
        var show = true;
        
        if (project && $row.data('project') != project) show = false;
        if (status && $row.data('status') != status) show = false;
        if (impact && $row.data('impact') != impact) show = false;
        if (search && $row.text().toLowerCase().indexOf(search) === -1) show = false;
        
        $row.toggle(show);
    });
}

function viewCR(id) {
    $.get(admin_url + 'disowebs_ops/sdlc/get_change_request/' + id, function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            var cr = data.change_request;
            $('#viewCRTitle').html('CR-' + String(cr.id).padStart(4, '0') + ': ' + cr.title);
            var html = '<div class="row">';
            html += '<div class="col-md-8"><h5><?php echo _l('description'); ?></h5><p>' + (cr.description || '-') + '</p>';
            html += '<h5><?php echo _l('reason'); ?></h5><p>' + (cr.reason || '-') + '</p></div>';
            html += '<div class="col-md-4">';
            html += '<p><strong><?php echo _l('status'); ?>:</strong> ' + cr.status + '</p>';
            html += '<p><strong><?php echo _l('impact'); ?>:</strong> ' + cr.impact_level + '</p>';
            html += '<p><strong><?php echo _l('cost_impact'); ?>:</strong> ' + (cr.cost_impact > 0 ? '$' + cr.cost_impact : '-') + '</p>';
            html += '<p><strong><?php echo _l('time_impact'); ?>:</strong> ' + (cr.time_impact > 0 ? cr.time_impact + 'h' : '-') + '</p>';
            html += '</div></div>';
            $('#viewCRBody').html(html);
            $('#viewCRModal').modal('show');
        }
    });
}

function approveCR(id) {
    if (confirm('<?php echo _l('confirm_approve'); ?>')) {
        $.post(admin_url + 'disowebs_ops/sdlc/approve_change_request/' + id, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                alert_float('success', '<?php echo _l('approved'); ?>');
                setTimeout(function() { location.reload(); }, 1000);
            }
        });
    }
}

function rejectCR(id) {
    var reason = prompt('<?php echo _l('rejection_reason'); ?>:');
    if (reason !== null) {
        $.post(admin_url + 'disowebs_ops/sdlc/reject_change_request/' + id, {reason: reason}, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                alert_float('success', '<?php echo _l('rejected'); ?>');
                setTimeout(function() { location.reload(); }, 1000);
            }
        });
    }
}

$(function() {
    appValidateForm($('#crForm'), {
        title: 'required',
        description: 'required'
    });
});
</script>
