<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.scope-item { transition: all 0.2s; background: #f8f9fa; }
.scope-item:hover { background: #e9ecef; }
.scope-item-handle { cursor: move; }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php $this->load->view('admin/includes/alerts'); ?>

            <!-- Header -->
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                    <div>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc/scope'); ?>" class="tw-text-neutral-500 hover:tw-text-primary">
                            <i class="fa fa-arrow-left"></i> <?php echo _l('back'); ?>
                        </a>
                        <h4 class="tw-font-bold tw-text-xl tw-mb-0 tw-mt-2">
                            <?php echo isset($scope->id) ? _l('edit') . ': ' . e($scope->title) : _l('disowebs_ops_new_scope'); ?>
                        </h4>
                    </div>
                    <div>
                        <?php if (isset($scope->id)) { ?>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc/preview_scope/' . $scope->id); ?>" class="btn btn-default" target="_blank">
                            <i class="fa fa-eye"></i> <?php echo _l('preview'); ?>
                        </a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <div class="col-md-8">
                <?php echo form_open(admin_url('disowebs_ops/sdlc/save_scope'), ['id' => 'scopeForm']); ?>
                <input type="hidden" name="id" value="<?php echo isset($scope->id) ? $scope->id : ''; ?>">
                
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="tw-font-semibold tw-mb-0"><?php echo _l('disowebs_ops_scope_details'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><?php echo _l('title'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required 
                                           value="<?php echo isset($scope->title) ? e($scope->title) : ''; ?>"
                                           placeholder="<?php echo _l('disowebs_ops_scope_title_placeholder'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('project'); ?> <span class="text-danger">*</span></label>
                                    <select name="project_id" id="projectSelect" class="selectpicker" data-width="100%" data-live-search="true" required>
                                        <option value=""><?php echo _l('select_project'); ?></option>
                                        <?php foreach ($projects as $p) { ?>
                                        <option value="<?php echo $p['id']; ?>" 
                                                data-client="<?php echo $p['clientid']; ?>"
                                                <?php echo (isset($scope->project_id) && $scope->project_id == $p['id']) ? 'selected' : ''; ?>>
                                            <?php echo e($p['name']); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('client'); ?></label>
                                    <select name="client_id" id="clientSelect" class="selectpicker" data-width="100%" data-live-search="true">
                                        <option value=""><?php echo _l('no_client'); ?></option>
                                        <?php foreach ($clients as $c) { ?>
                                        <option value="<?php echo $c['userid']; ?>" <?php echo (isset($scope->client_id) && $scope->client_id == $c['userid']) ? 'selected' : ''; ?>>
                                            <?php echo e($c['company']); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo _l('disowebs_ops_project_overview'); ?></label>
                            <?php echo render_textarea('overview', '', isset($scope->overview) ? $scope->overview : '', ['rows' => 4], [], '', 'tinymce'); ?>
                        </div>

                        <div class="form-group">
                            <label><?php echo _l('disowebs_ops_objectives'); ?></label>
                            <?php echo render_textarea('objectives', '', isset($scope->objectives) ? $scope->objectives : '', ['rows' => 4], [], '', 'tinymce'); ?>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('start_date'); ?></label>
                                    <input type="text" name="start_date" class="form-control datepicker" 
                                           value="<?php echo isset($scope->start_date) ? _d($scope->start_date) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('end_date'); ?></label>
                                    <input type="text" name="end_date" class="form-control datepicker" 
                                           value="<?php echo isset($scope->end_date) ? _d($scope->end_date) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('disowebs_ops_total_budget'); ?></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo get_base_currency()->symbol; ?></span>
                                        <input type="number" name="total_budget" class="form-control" step="0.01" min="0"
                                               value="<?php echo isset($scope->total_budget) ? $scope->total_budget : ''; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('status'); ?></label>
                                    <select name="status" class="selectpicker" data-width="100%">
                                        <option value="draft" <?php echo (!isset($scope->status) || $scope->status == 'draft') ? 'selected' : ''; ?>><?php echo _l('draft'); ?></option>
                                        <option value="pending_approval" <?php echo (isset($scope->status) && $scope->status == 'pending_approval') ? 'selected' : ''; ?>><?php echo _l('pending_approval'); ?></option>
                                        <option value="approved" <?php echo (isset($scope->status) && $scope->status == 'approved') ? 'selected' : ''; ?>><?php echo _l('approved'); ?></option>
                                        <option value="signed" <?php echo (isset($scope->status) && $scope->status == 'signed') ? 'selected' : ''; ?>><?php echo _l('signed'); ?></option>
                                        <option value="rejected" <?php echo (isset($scope->status) && $scope->status == 'rejected') ? 'selected' : ''; ?>><?php echo _l('rejected'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('version'); ?></label>
                                    <input type="text" name="version" class="form-control" 
                                           value="<?php echo isset($scope->version) ? $scope->version : '1.0'; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scope Items -->
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <h4 class="tw-font-semibold tw-mb-0"><?php echo _l('disowebs_ops_deliverables'); ?></h4>
                            <button type="button" class="btn btn-primary btn-xs" onclick="addScopeItem()">
                                <i class="fa fa-plus"></i> <?php echo _l('add'); ?>
                            </button>
                        </div>
                    </div>
                    <div class="panel-body tw-p-0">
                        <div id="scopeItems">
                            <?php if (isset($scope->items) && !empty($scope->items)) { ?>
                            <?php foreach ($scope->items as $index => $item) { ?>
                            <div class="scope-item tw-p-4 tw-border-b" data-index="<?php echo $index; ?>">
                                <div class="tw-flex tw-items-start">
                                    <span class="scope-item-handle tw-mr-3 tw-text-neutral-400 tw-mt-2">
                                        <i class="fa fa-bars"></i>
                                    </span>
                                    <div class="tw-flex-1">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" name="items[<?php echo $index; ?>][title]" class="form-control" 
                                                       placeholder="<?php echo _l('title'); ?>" value="<?php echo e($item->title); ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <select name="items[<?php echo $index; ?>][category]" class="form-control">
                                                    <option value="development" <?php echo $item->category == 'development' ? 'selected' : ''; ?>><?php echo _l('development'); ?></option>
                                                    <option value="design" <?php echo $item->category == 'design' ? 'selected' : ''; ?>><?php echo _l('design'); ?></option>
                                                    <option value="testing" <?php echo $item->category == 'testing' ? 'selected' : ''; ?>><?php echo _l('testing'); ?></option>
                                                    <option value="deployment" <?php echo $item->category == 'deployment' ? 'selected' : ''; ?>><?php echo _l('deployment'); ?></option>
                                                    <option value="documentation" <?php echo $item->category == 'documentation' ? 'selected' : ''; ?>><?php echo _l('documentation'); ?></option>
                                                    <option value="training" <?php echo $item->category == 'training' ? 'selected' : ''; ?>><?php echo _l('training'); ?></option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="items[<?php echo $index; ?>][hours]" class="form-control" 
                                                       placeholder="<?php echo _l('hours'); ?>" value="<?php echo $item->estimated_hours; ?>">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-xs tw-mt-1" onclick="removeItem(this)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row tw-mt-2">
                                            <div class="col-md-12">
                                                <textarea name="items[<?php echo $index; ?>][description]" class="form-control" rows="2" 
                                                          placeholder="<?php echo _l('description'); ?>"><?php echo e($item->description); ?></textarea>
                                            </div>
                                        </div>
                                        <input type="hidden" name="items[<?php echo $index; ?>][id]" value="<?php echo $item->id; ?>">
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php } ?>
                        </div>
                        <div id="noItemsMessage" class="tw-text-center tw-py-6 tw-text-neutral-400 <?php echo (isset($scope->items) && !empty($scope->items)) ? 'hidden' : ''; ?>">
                            <i class="fa fa-list fa-2x tw-mb-2"></i>
                            <p><?php echo _l('disowebs_ops_add_deliverables'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Exclusions & Assumptions -->
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="tw-font-semibold tw-mb-0"><?php echo _l('disowebs_ops_exclusions_assumptions'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label><?php echo _l('disowebs_ops_exclusions'); ?></label>
                            <textarea name="exclusions" class="form-control" rows="4" 
                                      placeholder="<?php echo _l('disowebs_ops_exclusions_placeholder'); ?>"><?php echo isset($scope->exclusions) ? e($scope->exclusions) : ''; ?></textarea>
                            <small class="tw-text-neutral-500"><?php echo _l('disowebs_ops_exclusions_help'); ?></small>
                        </div>
                        <div class="form-group">
                            <label><?php echo _l('disowebs_ops_assumptions'); ?></label>
                            <textarea name="assumptions" class="form-control" rows="4" 
                                      placeholder="<?php echo _l('disowebs_ops_assumptions_placeholder'); ?>"><?php echo isset($scope->assumptions) ? e($scope->assumptions) : ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="tw-font-semibold tw-mb-0"><?php echo _l('disowebs_ops_terms_conditions'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <?php echo render_textarea('terms', '', isset($scope->terms) ? $scope->terms : '', ['rows' => 6], [], '', 'tinymce'); ?>
                    </div>
                </div>

                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-end tw-gap-2">
                            <a href="<?php echo admin_url('disowebs_ops/sdlc/scope'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
                            <button type="submit" name="save_draft" class="btn btn-default">
                                <i class="fa fa-save"></i> <?php echo _l('save_as_draft'); ?>
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> <?php echo _l('save'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <?php echo form_close(); ?>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <?php if (isset($scope->id)) { ?>
                <!-- Actions -->
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="tw-font-semibold tw-mb-0"><?php echo _l('actions'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <?php if ($scope->status == 'draft') { ?>
                        <button type="button" class="btn btn-warning btn-block tw-mb-2" onclick="submitForApproval()">
                            <i class="fa fa-paper-plane"></i> <?php echo _l('submit_for_approval'); ?>
                        </button>
                        <?php } ?>
                        <?php if ($scope->status == 'pending_approval') { ?>
                        <button type="button" class="btn btn-success btn-block tw-mb-2" onclick="approveScope()">
                            <i class="fa fa-check"></i> <?php echo _l('approve'); ?>
                        </button>
                        <?php } ?>
                        <?php if ($scope->status == 'approved') { ?>
                        <button type="button" class="btn btn-info btn-block tw-mb-2" onclick="sendForSignature()">
                            <i class="fa fa-send"></i> <?php echo _l('send_for_signature'); ?>
                        </button>
                        <?php } ?>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc/scope_pdf/' . $scope->id); ?>" class="btn btn-default btn-block tw-mb-2" target="_blank">
                            <i class="fa fa-file-pdf-o"></i> <?php echo _l('generate_pdf'); ?>
                        </a>
                        <button type="button" class="btn btn-default btn-block" onclick="generateFromRequirements()">
                            <i class="fa fa-magic"></i> <?php echo _l('disowebs_ops_import_requirements'); ?>
                        </button>
                    </div>
                </div>

                <!-- Info -->
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="tw-font-semibold tw-mb-0"><?php echo _l('info'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled tw-mb-0">
                            <li class="tw-py-2 tw-border-b tw-flex tw-justify-between">
                                <span class="tw-text-neutral-500"><?php echo _l('created_at'); ?></span>
                                <span><?php echo _dt($scope->created_at); ?></span>
                            </li>
                            <li class="tw-py-2 tw-border-b tw-flex tw-justify-between">
                                <span class="tw-text-neutral-500"><?php echo _l('version'); ?></span>
                                <span>v<?php echo $scope->version; ?></span>
                            </li>
                            <?php if ($scope->approved_date) { ?>
                            <li class="tw-py-2 tw-border-b tw-flex tw-justify-between">
                                <span class="tw-text-neutral-500"><?php echo _l('approved_date'); ?></span>
                                <span><?php echo _d($scope->approved_date); ?></span>
                            </li>
                            <?php } ?>
                            <?php if ($scope->client_signed_date) { ?>
                            <li class="tw-py-2 tw-flex tw-justify-between">
                                <span class="tw-text-neutral-500"><?php echo _l('signed_date'); ?></span>
                                <span><?php echo _d($scope->client_signed_date); ?></span>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>

                <!-- Templates -->
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="tw-font-semibold tw-mb-0"><?php echo _l('templates'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <select id="templateSelect" class="selectpicker" data-width="100%" onchange="loadTemplate(this.value)">
                            <option value=""><?php echo _l('select_template'); ?></option>
                            <?php foreach ($templates as $tpl) { ?>
                            <option value="<?php echo $tpl->id; ?>"><?php echo e($tpl->name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Item Template -->
<template id="scopeItemTemplate">
    <div class="scope-item tw-p-4 tw-border-b" data-index="__INDEX__">
        <div class="tw-flex tw-items-start">
            <span class="scope-item-handle tw-mr-3 tw-text-neutral-400 tw-mt-2">
                <i class="fa fa-bars"></i>
            </span>
            <div class="tw-flex-1">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="items[__INDEX__][title]" class="form-control" placeholder="<?php echo _l('title'); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="items[__INDEX__][category]" class="form-control">
                            <option value="development"><?php echo _l('development'); ?></option>
                            <option value="design"><?php echo _l('design'); ?></option>
                            <option value="testing"><?php echo _l('testing'); ?></option>
                            <option value="deployment"><?php echo _l('deployment'); ?></option>
                            <option value="documentation"><?php echo _l('documentation'); ?></option>
                            <option value="training"><?php echo _l('training'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[__INDEX__][hours]" class="form-control" placeholder="<?php echo _l('hours'); ?>">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-xs tw-mt-1" onclick="removeItem(this)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="row tw-mt-2">
                    <div class="col-md-12">
                        <textarea name="items[__INDEX__][description]" class="form-control" rows="2" placeholder="<?php echo _l('description'); ?>"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<?php init_tail(); ?>
<script src="<?php echo base_url('assets/plugins/sortable/sortable.min.js'); ?>"></script>
<script>
var itemIndex = <?php echo isset($scope->items) ? count($scope->items) : 0; ?>;

function addScopeItem() {
    var template = $('#scopeItemTemplate').html().replace(/__INDEX__/g, itemIndex);
    $('#scopeItems').append(template);
    $('#noItemsMessage').addClass('hidden');
    itemIndex++;
}

function removeItem(btn) {
    $(btn).closest('.scope-item').remove();
    if ($('#scopeItems .scope-item').length === 0) {
        $('#noItemsMessage').removeClass('hidden');
    }
}

<?php if (isset($scope->id)) { ?>
function submitForApproval() {
    $.post(admin_url + 'disowebs_ops/sdlc/scope_action/<?php echo $scope->id; ?>/pending_approval', function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            alert_float('success', '<?php echo _l('submitted_for_approval'); ?>');
            setTimeout(function() { location.reload(); }, 1000);
        }
    });
}

function approveScope() {
    $.post(admin_url + 'disowebs_ops/sdlc/approve_scope/<?php echo $scope->id; ?>', function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            alert_float('success', '<?php echo _l('scope_approved'); ?>');
            setTimeout(function() { location.reload(); }, 1000);
        }
    });
}

function sendForSignature() {
    $.post(admin_url + 'disowebs_ops/sdlc/send_scope_signature/<?php echo $scope->id; ?>', function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            alert_float('success', '<?php echo _l('signature_request_sent'); ?>');
        }
    });
}

function generateFromRequirements() {
    $.get(admin_url + 'disowebs_ops/sdlc/generate_scope_from_requirements/<?php echo $scope->project_id; ?>', function(response) {
        var data = JSON.parse(response);
        if (data.success && data.items.length > 0) {
            data.items.forEach(function(item) {
                var template = $('#scopeItemTemplate').html().replace(/__INDEX__/g, itemIndex);
                var $item = $(template);
                $item.find('input[name*="[title]"]').val(item.title);
                $item.find('textarea[name*="[description]"]').val(item.description);
                $item.find('input[name*="[hours]"]').val(item.hours || '');
                $('#scopeItems').append($item);
                $('#noItemsMessage').addClass('hidden');
                itemIndex++;
            });
            alert_float('success', data.items.length + ' items imported');
        } else {
            alert_float('warning', '<?php echo _l('no_requirements_found'); ?>');
        }
    });
}
<?php } ?>

function loadTemplate(templateId) {
    if (!templateId) return;
    $.get(admin_url + 'disowebs_ops/sdlc/get_template/' + templateId, function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            if (data.template.content) {
                tinymce.get('overview').setContent(data.template.content);
            }
            $('#templateSelect').selectpicker('val', '');
        }
    });
}

$(function() {
    // Auto-select client when project changes
    $('#projectSelect').on('change', function() {
        var clientId = $(this).find(':selected').data('client');
        if (clientId) {
            $('#clientSelect').selectpicker('val', clientId);
        }
    });
    
    // Make items sortable
    if ($('#scopeItems').length) {
        new Sortable(document.getElementById('scopeItems'), {
            handle: '.scope-item-handle',
            animation: 150
        });
    }
    
    appValidateForm($('#scopeForm'), {
        title: 'required',
        project_id: 'required'
    });
});
</script>
