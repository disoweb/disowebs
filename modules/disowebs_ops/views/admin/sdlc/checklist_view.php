<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.checklist-item { transition: all 0.2s; }
.checklist-item:hover { background: #f8f9fa; }
.checklist-item.checked { opacity: 0.6; }
.checklist-item.checked .item-title { text-decoration: line-through; }
.custom-checkbox { width: 22px; height: 22px; cursor: pointer; }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php $this->load->view('admin/includes/alerts'); ?>

            <!-- Header -->
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                    <div>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc/checklists'); ?>" class="tw-text-neutral-500 hover:tw-text-primary">
                            <i class="fa fa-arrow-left"></i> <?php echo _l('back'); ?>
                        </a>
                        <h4 class="tw-font-bold tw-text-xl tw-mb-0 tw-mt-2">
                            <?php echo e($checklist->name); ?>
                        </h4>
                        <span class="label label-<?php echo $checklist->status == 'completed' ? 'success' : ($checklist->status == 'in_progress' ? 'warning' : 'default'); ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $checklist->status)); ?>
                        </span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-default" onclick="addItem()">
                            <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_add_item'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-2">
                            <span class="tw-font-semibold"><?php echo _l('progress'); ?></span>
                            <span class="tw-font-bold tw-text-lg" id="progressPercent"><?php echo $checklist->progress; ?>%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div id="progressBar" class="progress-bar progress-bar-success progress-bar-striped" 
                                 style="width: <?php echo $checklist->progress; ?>%; transition: width 0.3s;">
                            </div>
                        </div>
                        <?php if ($checklist->progress == 100) { ?>
                        <div class="tw-text-center tw-mt-4">
                            <i class="fa fa-check-circle fa-3x tw-text-success tw-mb-2"></i>
                            <h4 class="tw-text-success"><?php echo _l('disowebs_ops_checklist_complete'); ?></h4>
                            <?php if ($checklist->completed_date) { ?>
                            <p class="tw-text-neutral-500"><?php echo _l('completed'); ?>: <?php echo _dt($checklist->completed_date); ?></p>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Checklist Items -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body tw-p-0">
                        <div id="checklistItems">
                            <?php if (!empty($checklist->items)) { ?>
                            <?php foreach ($checklist->items as $item) { ?>
                            <div class="checklist-item tw-p-4 tw-border-b <?php echo $item->is_checked ? 'checked' : ''; ?>" data-id="<?php echo $item->id; ?>">
                                <div class="tw-flex tw-items-start">
                                    <input type="checkbox" class="custom-checkbox tw-mr-4 tw-mt-1" 
                                           <?php echo $item->is_checked ? 'checked' : ''; ?>
                                           onchange="toggleItem(<?php echo $item->id; ?>, this)">
                                    <div class="tw-flex-1">
                                        <div class="item-title tw-font-medium"><?php echo e($item->title); ?></div>
                                        <?php if ($item->description) { ?>
                                        <p class="tw-text-sm tw-text-neutral-500 tw-mb-0 tw-mt-1"><?php echo e($item->description); ?></p>
                                        <?php } ?>
                                        <?php if ($item->is_checked && $item->checked_at) { ?>
                                        <small class="tw-text-neutral-400">
                                            <i class="fa fa-check"></i> <?php echo _l('completed'); ?> <?php echo time_ago($item->checked_at); ?>
                                        </small>
                                        <?php } ?>
                                        
                                        <?php // Render children ?>
                                        <?php if (!empty($item->children)) { ?>
                                        <div class="tw-ml-6 tw-mt-3 tw-border-l-2 tw-border-neutral-200 tw-pl-4">
                                            <?php foreach ($item->children as $child) { ?>
                                            <div class="checklist-item tw-py-2 <?php echo $child->is_checked ? 'checked' : ''; ?>" data-id="<?php echo $child->id; ?>">
                                                <div class="tw-flex tw-items-start">
                                                    <input type="checkbox" class="custom-checkbox tw-mr-3" 
                                                           <?php echo $child->is_checked ? 'checked' : ''; ?>
                                                           onchange="toggleItem(<?php echo $child->id; ?>, this)">
                                                    <div class="tw-flex-1">
                                                        <span class="item-title"><?php echo e($child->title); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="tw-ml-4">
                                        <?php if ($item->is_required) { ?>
                                        <span class="label label-danger"><?php echo _l('required'); ?></span>
                                        <?php } ?>
                                        <button type="button" class="btn btn-default btn-xs tw-ml-2" onclick="deleteItem(<?php echo $item->id; ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php } else { ?>
                            <div class="tw-text-center tw-py-8 tw-text-neutral-400">
                                <i class="fa fa-list fa-2x tw-mb-2"></i>
                                <p><?php echo _l('disowebs_ops_no_checklist_items'); ?></p>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('disowebs_ops_add_item'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><?php echo _l('title'); ?> <span class="text-danger">*</span></label>
                    <input type="text" id="newItemTitle" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?php echo _l('description'); ?></label>
                    <textarea id="newItemDescription" class="form-control" rows="2"></textarea>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="newItemRequired" checked> <?php echo _l('required'); ?>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                <button type="button" class="btn btn-primary" onclick="saveNewItem()"><?php echo _l('add'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
var checklistId = <?php echo $checklist->id; ?>;

function toggleItem(itemId, checkbox) {
    var $item = $(checkbox).closest('.checklist-item');
    
    $.post(admin_url + 'disowebs_ops/sdlc/toggle_checklist_item/' + itemId, function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            if (data.checked) {
                $item.addClass('checked');
            } else {
                $item.removeClass('checked');
            }
            updateProgress(data.progress);
        }
    });
}

function updateProgress(progress) {
    $('#progressPercent').text(progress + '%');
    $('#progressBar').css('width', progress + '%');
    
    if (progress == 100) {
        $('#progressBar').removeClass('progress-bar-striped');
        location.reload();
    }
}

function addItem() {
    $('#newItemTitle').val('');
    $('#newItemDescription').val('');
    $('#newItemRequired').prop('checked', true);
    $('#addItemModal').modal('show');
}

function saveNewItem() {
    var title = $('#newItemTitle').val().trim();
    if (!title) {
        alert('<?php echo _l('field_is_required'); ?>');
        return;
    }
    
    $.post(admin_url + 'disowebs_ops/sdlc/add_checklist_item', {
        checklist_id: checklistId,
        title: title,
        description: $('#newItemDescription').val(),
        is_required: $('#newItemRequired').is(':checked') ? 1 : 0
    }, function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            location.reload();
        } else {
            alert_float('danger', '<?php echo _l('error'); ?>');
        }
    });
}

function deleteItem(itemId) {
    if (confirm('<?php echo _l('confirm_delete'); ?>')) {
        $.post(admin_url + 'disowebs_ops/sdlc/delete_checklist_item/' + itemId, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
