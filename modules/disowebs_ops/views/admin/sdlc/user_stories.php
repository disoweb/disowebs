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
                            <i class="fa fa-book tw-mr-2"></i> <?php echo _l('disowebs_ops_user_stories'); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_user_stories_desc'); ?></p>
                    </div>
                    <div>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc'); ?>" class="btn btn-default tw-mr-2">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newStoryModal">
                            <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_new_story'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kanban View Toggle -->
            <div class="col-md-12 tw-mb-4">
                <div class="btn-group">
                    <button type="button" class="btn btn-default active" onclick="showView('list')">
                        <i class="fa fa-list"></i> <?php echo _l('list'); ?>
                    </button>
                    <button type="button" class="btn btn-default" onclick="showView('kanban')">
                        <i class="fa fa-columns"></i> <?php echo _l('kanban'); ?>
                    </button>
                </div>
                <div class="pull-right">
                    <select id="filterProject" class="selectpicker" data-width="200px" data-live-search="true" onchange="filterStories()">
                        <option value=""><?php echo _l('all_projects'); ?></option>
                        <?php foreach ($projects as $p) { ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- List View -->
            <div class="col-md-12" id="listView">
                <div class="panel_s">
                    <div class="panel-body">
                        <table class="table dt-table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th><?php echo _l('story'); ?></th>
                                    <th><?php echo _l('project'); ?></th>
                                    <th><?php echo _l('priority'); ?></th>
                                    <th><?php echo _l('points'); ?></th>
                                    <th><?php echo _l('status'); ?></th>
                                    <th><?php echo _l('sprint'); ?></th>
                                    <th width="120"><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stories as $story) { ?>
                                <tr class="story-row" data-project="<?php echo $story->project_id; ?>">
                                    <td><strong>US-<?php echo str_pad($story->id, 4, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td>
                                        <a href="#" onclick="editStory(<?php echo $story->id; ?>); return false;">
                                            <strong><?php echo e($story->title); ?></strong>
                                        </a>
                                        <br>
                                        <small class="tw-text-neutral-500">
                                            <?php echo _l('as_a'); ?> <em><?php echo e($story->as_a); ?></em>, 
                                            <?php echo _l('i_want'); ?> <em><?php echo e($story->i_want); ?></em>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($story->project_name) { ?>
                                        <a href="<?php echo admin_url('projects/view/' . $story->project_id); ?>" target="_blank">
                                            <?php echo e($story->project_name); ?>
                                        </a>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php
                                        $priority_class = ['high' => 'danger', 'medium' => 'warning', 'low' => 'info'];
                                        ?>
                                        <span class="label label-<?php echo $priority_class[$story->priority] ?? 'default'; ?>">
                                            <?php echo ucfirst($story->priority); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="tw-font-bold"><?php echo $story->story_points ?? '-'; ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'backlog' => 'default',
                                            'ready' => 'info',
                                            'in_progress' => 'warning',
                                            'testing' => 'primary',
                                            'done' => 'success'
                                        ];
                                        ?>
                                        <span class="label label-<?php echo $status_class[$story->status] ?? 'default'; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $story->status)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $story->sprint ?? '-'; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-xs" onclick="editStory(<?php echo $story->id; ?>)">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <?php if (!$story->linked_task_id) { ?>
                                            <button type="button" class="btn btn-info btn-xs" onclick="convertToTask(<?php echo $story->id; ?>)" data-toggle="tooltip" title="<?php echo _l('convert_to_task'); ?>">
                                                <i class="fa fa-tasks"></i>
                                            </button>
                                            <?php } ?>
                                            <button type="button" class="btn btn-danger btn-xs" onclick="deleteStory(<?php echo $story->id; ?>)">
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

            <!-- Kanban View -->
            <div class="col-md-12 hidden" id="kanbanView">
                <div class="row">
                    <?php 
                    $statuses = ['backlog' => 'Backlog', 'ready' => 'Ready', 'in_progress' => 'In Progress', 'testing' => 'Testing', 'done' => 'Done'];
                    foreach ($statuses as $status_key => $status_label) { ?>
                    <div class="col-md tw-flex-1 tw-min-w-[200px]">
                        <div class="panel_s">
                            <div class="panel-heading tw-bg-neutral-100">
                                <h5 class="tw-font-semibold tw-mb-0"><?php echo $status_label; ?></h5>
                            </div>
                            <div class="panel-body kanban-column tw-min-h-[400px] tw-bg-neutral-50" data-status="<?php echo $status_key; ?>">
                                <?php foreach ($stories as $story) { ?>
                                <?php if ($story->status == $status_key) { ?>
                                <div class="kanban-card tw-bg-white tw-p-3 tw-rounded tw-shadow-sm tw-mb-2 tw-cursor-move" data-id="<?php echo $story->id; ?>">
                                    <div class="tw-flex tw-justify-between tw-items-start tw-mb-2">
                                        <small class="tw-text-neutral-400">US-<?php echo str_pad($story->id, 4, '0', STR_PAD_LEFT); ?></small>
                                        <?php if ($story->story_points) { ?>
                                        <span class="label label-default"><?php echo $story->story_points; ?> pts</span>
                                        <?php } ?>
                                    </div>
                                    <strong class="tw-text-sm"><?php echo e($story->title); ?></strong>
                                    <div class="tw-mt-2">
                                        <span class="label label-<?php echo $priority_class[$story->priority] ?? 'default'; ?> label-xs">
                                            <?php echo ucfirst($story->priority); ?>
                                        </span>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- New/Edit Story Modal -->
<div class="modal fade" id="newStoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php echo form_open(admin_url('disowebs_ops/sdlc/save_story'), ['id' => 'storyForm']); ?>
            <input type="hidden" name="id" id="storyId">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="storyModalTitle"><?php echo _l('disowebs_ops_new_story'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('title'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="storyTitle" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <!-- User Story Format -->
                <div class="panel_s tw-bg-info-50 tw-border-info">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('disowebs_ops_story_format'); ?></h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('as_a'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="as_a" id="storyAsA" class="form-control" required placeholder="<?php echo _l('disowebs_ops_as_a_placeholder'); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('i_want'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="i_want" id="storyIWant" class="form-control" required placeholder="<?php echo _l('disowebs_ops_i_want_placeholder'); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('so_that'); ?></label>
                                    <input type="text" name="so_that" id="storySoThat" class="form-control" placeholder="<?php echo _l('disowebs_ops_so_that_placeholder'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('project'); ?></label>
                            <select name="project_id" id="storyProject" class="selectpicker" data-width="100%" data-live-search="true">
                                <option value=""><?php echo _l('no_project'); ?></option>
                                <?php foreach ($projects as $p) { ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('requirement'); ?></label>
                            <select name="requirement_id" id="storyRequirement" class="selectpicker" data-width="100%" data-live-search="true">
                                <option value=""><?php echo _l('none'); ?></option>
                                <?php foreach ($requirements as $r) { ?>
                                <option value="<?php echo $r->id; ?>"><?php echo e($r->title); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><?php echo _l('priority'); ?></label>
                            <select name="priority" id="storyPriority" class="selectpicker" data-width="100%">
                                <option value="medium"><?php echo _l('medium'); ?></option>
                                <option value="high"><?php echo _l('high'); ?></option>
                                <option value="low"><?php echo _l('low'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><?php echo _l('story_points'); ?></label>
                            <select name="story_points" id="storyPoints" class="selectpicker" data-width="100%">
                                <option value="">-</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="5">5</option>
                                <option value="8">8</option>
                                <option value="13">13</option>
                                <option value="21">21</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><?php echo _l('status'); ?></label>
                            <select name="status" id="storyStatus" class="selectpicker" data-width="100%">
                                <option value="backlog"><?php echo _l('backlog'); ?></option>
                                <option value="ready"><?php echo _l('ready'); ?></option>
                                <option value="in_progress"><?php echo _l('in_progress'); ?></option>
                                <option value="testing"><?php echo _l('testing'); ?></option>
                                <option value="done"><?php echo _l('done'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><?php echo _l('sprint'); ?></label>
                            <input type="text" name="sprint" id="storySprint" class="form-control" placeholder="Sprint 1">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label><?php echo _l('disowebs_ops_acceptance_criteria'); ?></label>
                    <textarea name="acceptance_criteria" id="storyCriteria" class="form-control" rows="4" placeholder="<?php echo _l('disowebs_ops_acceptance_criteria_story_placeholder'); ?>"></textarea>
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
<script src="<?php echo base_url('assets/plugins/sortable/sortable.min.js'); ?>"></script>
<script>
function showView(view) {
    if (view === 'kanban') {
        $('#listView').addClass('hidden');
        $('#kanbanView').removeClass('hidden');
    } else {
        $('#kanbanView').addClass('hidden');
        $('#listView').removeClass('hidden');
    }
}

function filterStories() {
    var project = $('#filterProject').val();
    $('.story-row').each(function() {
        var $row = $(this);
        if (!project || $row.data('project') == project) {
            $row.show();
        } else {
            $row.hide();
        }
    });
}

function editStory(id) {
    $.get(admin_url + 'disowebs_ops/sdlc/get_story/' + id, function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            var s = data.story;
            $('#storyId').val(s.id);
            $('#storyTitle').val(s.title);
            $('#storyAsA').val(s.as_a);
            $('#storyIWant').val(s.i_want);
            $('#storySoThat').val(s.so_that);
            $('#storyCriteria').val(s.acceptance_criteria);
            $('#storySprint').val(s.sprint);
            $('#storyProject').selectpicker('val', s.project_id);
            $('#storyRequirement').selectpicker('val', s.requirement_id);
            $('#storyPriority').selectpicker('val', s.priority);
            $('#storyPoints').selectpicker('val', s.story_points);
            $('#storyStatus').selectpicker('val', s.status);
            $('#storyModalTitle').text('<?php echo _l('edit'); ?> Story');
            $('#newStoryModal').modal('show');
        }
    });
}

function convertToTask(id) {
    if (confirm('<?php echo _l('disowebs_ops_confirm_convert_task'); ?>')) {
        $.post(admin_url + 'disowebs_ops/sdlc/story_to_task/' + id, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                alert_float('success', '<?php echo _l('disowebs_ops_task_created'); ?>');
                setTimeout(function() { location.reload(); }, 1000);
            }
        });
    }
}

function deleteStory(id) {
    if (confirm('<?php echo _l('confirm_delete'); ?>')) {
        $.post(admin_url + 'disowebs_ops/sdlc/delete_story/' + id, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                location.reload();
            }
        });
    }
}

$(function() {
    // Reset form on modal close
    $('#newStoryModal').on('hidden.bs.modal', function() {
        $('#storyForm')[0].reset();
        $('#storyId').val('');
        $('#storyModalTitle').text('<?php echo _l('disowebs_ops_new_story'); ?>');
        $('.selectpicker').selectpicker('refresh');
    });
    
    // Kanban drag & drop
    document.querySelectorAll('.kanban-column').forEach(function(column) {
        new Sortable(column, {
            group: 'stories',
            animation: 150,
            onEnd: function(evt) {
                var storyId = evt.item.dataset.id;
                var newStatus = evt.to.dataset.status;
                $.post(admin_url + 'disowebs_ops/sdlc/update_story_status/' + storyId, {
                    status: newStatus
                });
            }
        });
    });
    
    appValidateForm($('#storyForm'), {
        title: 'required',
        as_a: 'required',
        i_want: 'required'
    });
});
</script>
