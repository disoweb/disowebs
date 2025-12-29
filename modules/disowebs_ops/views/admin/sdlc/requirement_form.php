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
                        <a href="<?php echo admin_url('disowebs_ops/sdlc/requirements'); ?>" class="tw-text-neutral-500 hover:tw-text-primary">
                            <i class="fa fa-arrow-left"></i> <?php echo _l('back'); ?>
                        </a>
                        <h4 class="tw-font-bold tw-text-xl tw-mb-0 tw-mt-2">
                            <?php echo isset($requirement->id) ? _l('edit') . ' ' . _l('disowebs_ops_requirement') : _l('disowebs_ops_new_requirement'); ?>
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php 
                        $form_url = isset($requirement->id) 
                            ? admin_url('disowebs_ops/sdlc/requirement/' . $requirement->id) 
                            : admin_url('disowebs_ops/sdlc/requirement'); 
                        ?>
                        <?php echo form_open($form_url, ['id' => 'requirementForm']); ?>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><?php echo _l('title'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required 
                                           value="<?php echo isset($requirement->title) ? e($requirement->title) : ''; ?>"
                                           placeholder="<?php echo _l('disowebs_ops_requirement_title_placeholder'); ?>">
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
                                        <option value="<?php echo $p['id']; ?>" <?php echo (isset($requirement->project_id) && $requirement->project_id == $p['id']) ? 'selected' : ''; ?>>
                                            <?php echo e($p['name']); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('client'); ?></label>
                                    <select name="client_id" class="selectpicker" data-width="100%" data-live-search="true">
                                        <option value=""><?php echo _l('no_client'); ?></option>
                                        <?php foreach ($clients as $c) { ?>
                                        <option value="<?php echo $c['userid']; ?>" <?php echo (isset($requirement->client_id) && $requirement->client_id == $c['userid']) ? 'selected' : ''; ?>>
                                            <?php echo e($c['company']); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('type'); ?></label>
                                    <select name="requirement_type" class="selectpicker" data-width="100%">
                                        <option value="functional" <?php echo (isset($requirement->requirement_type) && $requirement->requirement_type == 'functional') ? 'selected' : ''; ?>><?php echo _l('functional'); ?></option>
                                        <option value="non_functional" <?php echo (isset($requirement->requirement_type) && $requirement->requirement_type == 'non_functional') ? 'selected' : ''; ?>><?php echo _l('non_functional'); ?></option>
                                        <option value="ui_ux" <?php echo (isset($requirement->requirement_type) && $requirement->requirement_type == 'ui_ux') ? 'selected' : ''; ?>><?php echo _l('ui_ux'); ?></option>
                                        <option value="integration" <?php echo (isset($requirement->requirement_type) && $requirement->requirement_type == 'integration') ? 'selected' : ''; ?>><?php echo _l('integration'); ?></option>
                                        <option value="security" <?php echo (isset($requirement->requirement_type) && $requirement->requirement_type == 'security') ? 'selected' : ''; ?>><?php echo _l('security'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('priority'); ?></label>
                                    <select name="priority" class="selectpicker" data-width="100%">
                                        <option value="medium" <?php echo (!isset($requirement->priority) || $requirement->priority == 'medium') ? 'selected' : ''; ?>><?php echo _l('medium'); ?></option>
                                        <option value="critical" <?php echo (isset($requirement->priority) && $requirement->priority == 'critical') ? 'selected' : ''; ?>><?php echo _l('critical'); ?></option>
                                        <option value="high" <?php echo (isset($requirement->priority) && $requirement->priority == 'high') ? 'selected' : ''; ?>><?php echo _l('high'); ?></option>
                                        <option value="low" <?php echo (isset($requirement->priority) && $requirement->priority == 'low') ? 'selected' : ''; ?>><?php echo _l('low'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('status'); ?></label>
                                    <select name="status" class="selectpicker" data-width="100%">
                                        <option value="draft" <?php echo (!isset($requirement->status) || $requirement->status == 'draft') ? 'selected' : ''; ?>><?php echo _l('draft'); ?></option>
                                        <option value="approved" <?php echo (isset($requirement->status) && $requirement->status == 'approved') ? 'selected' : ''; ?>><?php echo _l('approved'); ?></option>
                                        <option value="in_development" <?php echo (isset($requirement->status) && $requirement->status == 'in_development') ? 'selected' : ''; ?>><?php echo _l('in_development'); ?></option>
                                        <option value="completed" <?php echo (isset($requirement->status) && $requirement->status == 'completed') ? 'selected' : ''; ?>><?php echo _l('completed'); ?></option>
                                        <option value="rejected" <?php echo (isset($requirement->status) && $requirement->status == 'rejected') ? 'selected' : ''; ?>><?php echo _l('rejected'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo _l('description'); ?> <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="6" required
                                      placeholder="<?php echo _l('disowebs_ops_requirement_desc_placeholder'); ?>"><?php echo isset($requirement->description) ? e($requirement->description) : ''; ?></textarea>
                            <small class="tw-text-neutral-500"><?php echo _l('disowebs_ops_requirement_desc_help'); ?></small>
                        </div>

                        <div class="form-group">
                            <label><?php echo _l('disowebs_ops_acceptance_criteria'); ?></label>
                            <textarea name="acceptance_criteria" class="form-control" rows="4"
                                      placeholder="<?php echo _l('disowebs_ops_acceptance_criteria_placeholder'); ?>"><?php echo isset($requirement->acceptance_criteria) ? e($requirement->acceptance_criteria) : ''; ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('disowebs_ops_estimated_hours'); ?></label>
                                    <div class="input-group">
                                        <input type="number" name="estimated_hours" class="form-control" step="0.5" min="0"
                                               value="<?php echo isset($requirement->estimated_hours) ? $requirement->estimated_hours : ''; ?>">
                                        <span class="input-group-addon"><?php echo _l('hours'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('disowebs_ops_source'); ?></label>
                                    <select name="source" class="selectpicker" data-width="100%">
                                        <option value="client_request" <?php echo (isset($requirement->source) && $requirement->source == 'client_request') ? 'selected' : ''; ?>><?php echo _l('client_request'); ?></option>
                                        <option value="internal" <?php echo (isset($requirement->source) && $requirement->source == 'internal') ? 'selected' : ''; ?>><?php echo _l('internal'); ?></option>
                                        <option value="discovery" <?php echo (isset($requirement->source) && $requirement->source == 'discovery') ? 'selected' : ''; ?>><?php echo _l('discovery'); ?></option>
                                        <option value="bug_fix" <?php echo (isset($requirement->source) && $requirement->source == 'bug_fix') ? 'selected' : ''; ?>><?php echo _l('bug_fix'); ?></option>
                                        <option value="enhancement" <?php echo (isset($requirement->source) && $requirement->source == 'enhancement') ? 'selected' : ''; ?>><?php echo _l('enhancement'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo _l('notes'); ?></label>
                            <textarea name="notes" class="form-control" rows="3"><?php echo isset($requirement->notes) ? e($requirement->notes) : ''; ?></textarea>
                        </div>

                        <hr>

                        <div class="tw-flex tw-justify-end tw-gap-2">
                            <a href="<?php echo admin_url('disowebs_ops/sdlc/requirements'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> <?php echo _l('save'); ?>
                            </button>
                        </div>
                        
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <?php if (isset($requirement->id)) { ?>
                <!-- Actions -->
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="tw-font-semibold tw-mb-0"><?php echo _l('actions'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <?php if ($requirement->status == 'approved' && !$requirement->linked_task_id) { ?>
                        <button type="button" class="btn btn-success btn-block tw-mb-2" onclick="convertToTask()">
                            <i class="fa fa-tasks"></i> <?php echo _l('disowebs_ops_convert_to_task'); ?>
                        </button>
                        <?php } ?>
                        <?php if ($requirement->linked_task_id) { ?>
                        <a href="<?php echo admin_url('tasks/view/' . $requirement->linked_task_id); ?>" class="btn btn-info btn-block tw-mb-2" target="_blank">
                            <i class="fa fa-external-link"></i> <?php echo _l('view_task'); ?>
                        </a>
                        <?php } ?>
                        <button type="button" class="btn btn-default btn-block" onclick="duplicateRequirement()">
                            <i class="fa fa-copy"></i> <?php echo _l('duplicate'); ?>
                        </button>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="tw-font-semibold tw-mb-0"><?php echo _l('timeline'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled tw-mb-0">
                            <li class="tw-py-2 tw-border-b">
                                <small class="tw-text-neutral-500"><?php echo _l('created_at'); ?></small><br>
                                <?php echo _dt($requirement->created_at); ?>
                            </li>
                            <?php if ($requirement->updated_at) { ?>
                            <li class="tw-py-2 tw-border-b">
                                <small class="tw-text-neutral-500"><?php echo _l('updated_at'); ?></small><br>
                                <?php echo _dt($requirement->updated_at); ?>
                            </li>
                            <?php } ?>
                            <?php if ($requirement->approved_by) { ?>
                            <li class="tw-py-2">
                                <small class="tw-text-neutral-500"><?php echo _l('approved_by'); ?></small><br>
                                <?php echo get_staff_full_name($requirement->approved_by); ?><br>
                                <small><?php echo _dt($requirement->approved_date); ?></small>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>

                <!-- Quick Tips -->
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="tw-font-semibold tw-mb-0"><i class="fa fa-lightbulb-o"></i> <?php echo _l('tips'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <ul class="tw-text-sm tw-text-neutral-600 tw-mb-0 tw-pl-4">
                            <li class="tw-mb-2"><?php echo _l('disowebs_ops_req_tip_1'); ?></li>
                            <li class="tw-mb-2"><?php echo _l('disowebs_ops_req_tip_2'); ?></li>
                            <li class="tw-mb-2"><?php echo _l('disowebs_ops_req_tip_3'); ?></li>
                            <li><?php echo _l('disowebs_ops_req_tip_4'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
<?php if (isset($requirement->id)) { ?>
function convertToTask() {
    if (confirm('<?php echo _l('disowebs_ops_confirm_convert_task'); ?>')) {
        $.post(admin_url + 'disowebs_ops/sdlc/requirement_to_task/<?php echo $requirement->id; ?>', function(response) {
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

function duplicateRequirement() {
    if (confirm('<?php echo _l('confirm_duplicate'); ?>')) {
        $.post(admin_url + 'disowebs_ops/sdlc/duplicate_requirement/<?php echo $requirement->id; ?>', function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                window.location.href = admin_url + 'disowebs_ops/sdlc/requirement/' + data.id;
            }
        });
    }
}
<?php } ?>

$(function() {
    appValidateForm($('#requirementForm'), {
        title: 'required',
        description: 'required'
    });
});
</script>
