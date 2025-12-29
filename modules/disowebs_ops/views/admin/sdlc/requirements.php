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
                            <i class="fa fa-list-alt tw-mr-2"></i> <?php echo _l('disowebs_ops_requirements'); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_requirements_desc'); ?></p>
                    </div>
                    <div>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc'); ?>" class="btn btn-default tw-mr-2">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc/requirement'); ?>" class="btn btn-primary">
                            <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_new_requirement'); ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body tw-py-2">
                        <div class="row">
                            <div class="col-md-3">
                                <select id="filterProject" class="selectpicker" data-width="100%" data-live-search="true" onchange="filterRequirements()">
                                    <option value=""><?php echo _l('all_projects'); ?></option>
                                    <?php foreach ($projects as $p) { ?>
                                    <option value="<?php echo $p['id']; ?>" <?php echo isset($project_id) && $project_id == $p['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($p['name']); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filterStatus" class="selectpicker" data-width="100%" onchange="filterRequirements()">
                                    <option value=""><?php echo _l('all'); ?></option>
                                    <option value="draft"><?php echo _l('draft'); ?></option>
                                    <option value="approved"><?php echo _l('approved'); ?></option>
                                    <option value="in_development"><?php echo _l('in_development'); ?></option>
                                    <option value="completed"><?php echo _l('completed'); ?></option>
                                    <option value="rejected"><?php echo _l('rejected'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filterPriority" class="selectpicker" data-width="100%" onchange="filterRequirements()">
                                    <option value=""><?php echo _l('all_priorities'); ?></option>
                                    <option value="critical"><?php echo _l('critical'); ?></option>
                                    <option value="high"><?php echo _l('high'); ?></option>
                                    <option value="medium"><?php echo _l('medium'); ?></option>
                                    <option value="low"><?php echo _l('low'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filterType" class="selectpicker" data-width="100%" onchange="filterRequirements()">
                                    <option value=""><?php echo _l('all_types'); ?></option>
                                    <option value="functional"><?php echo _l('functional'); ?></option>
                                    <option value="non_functional"><?php echo _l('non_functional'); ?></option>
                                    <option value="ui_ux"><?php echo _l('ui_ux'); ?></option>
                                    <option value="integration"><?php echo _l('integration'); ?></option>
                                    <option value="security"><?php echo _l('security'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="text" id="searchReq" class="form-control" placeholder="<?php echo _l('search'); ?>...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" onclick="filterRequirements()"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <div class="panel_s tw-text-center">
                            <div class="panel-body tw-py-3">
                                <h3 class="tw-mb-0 tw-font-bold"><?php echo $stats['total'] ?? 0; ?></h3>
                                <small class="tw-text-neutral-500"><?php echo _l('total'); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel_s tw-text-center tw-border-l-4 tw-border-l-info">
                            <div class="panel-body tw-py-3">
                                <h3 class="tw-mb-0 tw-font-bold tw-text-info"><?php echo $stats['draft'] ?? 0; ?></h3>
                                <small class="tw-text-neutral-500"><?php echo _l('draft'); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel_s tw-text-center tw-border-l-4 tw-border-l-success">
                            <div class="panel-body tw-py-3">
                                <h3 class="tw-mb-0 tw-font-bold tw-text-success"><?php echo $stats['approved'] ?? 0; ?></h3>
                                <small class="tw-text-neutral-500"><?php echo _l('approved'); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel_s tw-text-center tw-border-l-4 tw-border-l-warning">
                            <div class="panel-body tw-py-3">
                                <h3 class="tw-mb-0 tw-font-bold tw-text-warning"><?php echo $stats['in_development'] ?? 0; ?></h3>
                                <small class="tw-text-neutral-500"><?php echo _l('in_development'); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel_s tw-text-center tw-border-l-4 tw-border-l-primary">
                            <div class="panel-body tw-py-3">
                                <h3 class="tw-mb-0 tw-font-bold tw-text-primary"><?php echo $stats['completed'] ?? 0; ?></h3>
                                <small class="tw-text-neutral-500"><?php echo _l('completed'); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel_s tw-text-center tw-border-l-4 tw-border-l-danger">
                            <div class="panel-body tw-py-3">
                                <h3 class="tw-mb-0 tw-font-bold tw-text-danger"><?php echo $stats['critical'] ?? 0; ?></h3>
                                <small class="tw-text-neutral-500"><?php echo _l('critical'); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requirements Table -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <table class="table dt-table table-hover" id="requirementsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo _l('title'); ?></th>
                                    <th><?php echo _l('project'); ?></th>
                                    <th><?php echo _l('type'); ?></th>
                                    <th><?php echo _l('priority'); ?></th>
                                    <th><?php echo _l('status'); ?></th>
                                    <th><?php echo _l('created_at'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requirements as $req) { ?>
                                <tr class="requirement-row" data-status="<?php echo $req->status; ?>" data-priority="<?php echo $req->priority; ?>" data-type="<?php echo $req->requirement_type; ?>" data-project="<?php echo $req->project_id; ?>">
                                    <td><?php echo $req->id; ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/requirement/' . $req->id); ?>" class="tw-font-medium">
                                            <?php echo e($req->title); ?>
                                        </a>
                                        <?php if ($req->linked_task_id) { ?>
                                        <span class="label label-info tw-ml-2"><?php echo _l('has_task'); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($req->project_name) { ?>
                                        <a href="<?php echo admin_url('projects/view/' . $req->project_id); ?>" target="_blank">
                                            <?php echo e($req->project_name); ?>
                                        </a>
                                        <?php } else { ?>
                                        <span class="tw-text-neutral-400"><?php echo _l('no_project'); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <span class="label label-default"><?php echo ucfirst(str_replace('_', ' ', $req->requirement_type)); ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $priority_class = [
                                            'critical' => 'danger',
                                            'high' => 'warning',
                                            'medium' => 'info',
                                            'low' => 'default'
                                        ];
                                        ?>
                                        <span class="label label-<?php echo $priority_class[$req->priority] ?? 'default'; ?>">
                                            <?php echo ucfirst($req->priority); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'draft' => 'default',
                                            'approved' => 'success',
                                            'in_development' => 'warning',
                                            'completed' => 'primary',
                                            'rejected' => 'danger'
                                        ];
                                        ?>
                                        <span class="label label-<?php echo $status_class[$req->status] ?? 'default'; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $req->status)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo _dt($req->created_at); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo admin_url('disowebs_ops/sdlc/requirement/' . $req->id); ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo _l('edit'); ?>">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <?php if (!$req->linked_task_id && $req->status == 'approved') { ?>
                                            <button type="button" class="btn btn-info btn-xs" onclick="convertToTask(<?php echo $req->id; ?>)" data-toggle="tooltip" title="<?php echo _l('disowebs_ops_convert_to_task'); ?>">
                                                <i class="fa fa-tasks"></i>
                                            </button>
                                            <?php } ?>
                                            <button type="button" class="btn btn-danger btn-xs" onclick="deleteRequirement(<?php echo $req->id; ?>)" data-toggle="tooltip" title="<?php echo _l('delete'); ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
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

<?php init_tail(); ?>
<script>
function filterRequirements() {
    var project = $('#filterProject').val();
    var status = $('#filterStatus').val();
    var priority = $('#filterPriority').val();
    var type = $('#filterType').val();
    var search = $('#searchReq').val().toLowerCase();
    
    $('.requirement-row').each(function() {
        var $row = $(this);
        var show = true;
        
        if (project && $row.data('project') != project) show = false;
        if (status && $row.data('status') != status) show = false;
        if (priority && $row.data('priority') != priority) show = false;
        if (type && $row.data('type') != type) show = false;
        if (search && $row.text().toLowerCase().indexOf(search) === -1) show = false;
        
        $row.toggle(show);
    });
}

function convertToTask(id) {
    if (confirm('<?php echo _l('disowebs_ops_confirm_convert_task'); ?>')) {
        $.post(admin_url + 'disowebs_ops/sdlc/requirement_to_task/' + id, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                alert_float('success', '<?php echo _l('disowebs_ops_task_created'); ?>');
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                alert_float('danger', data.message || '<?php echo _l('error'); ?>');
            }
        });
    }
}

function deleteRequirement(id) {
    if (confirm('<?php echo _l('confirm_delete'); ?>')) {
        $.post(admin_url + 'disowebs_ops/sdlc/delete_requirement/' + id, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                alert_float('success', '<?php echo _l('deleted'); ?>');
                setTimeout(function() { location.reload(); }, 1000);
            }
        });
    }
}

$(function() {
    $('#searchReq').on('keyup', function(e) {
        if (e.keyCode === 13) {
            filterRequirements();
        }
    });
});
</script>
