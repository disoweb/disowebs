<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.note-card { transition: transform 0.2s, box-shadow 0.2s; }
.note-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.note-type-idea { border-left: 4px solid #f39c12; }
.note-type-bug { border-left: 4px solid #e74c3c; }
.note-type-todo { border-left: 4px solid #3498db; }
.note-type-question { border-left: 4px solid #9b59b6; }
.note-type-reference { border-left: 4px solid #1abc9c; }
.note-type-general { border-left: 4px solid #95a5a6; }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php $this->load->view('admin/includes/alerts'); ?>

            <!-- Header -->
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                    <div>
                        <h4 class="tw-font-bold tw-text-xl tw-mb-0">
                            <i class="fa fa-sticky-note tw-mr-2"></i> <?php echo _l('disowebs_ops_dev_notes'); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_dev_notes_desc'); ?></p>
                    </div>
                    <div>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc'); ?>" class="btn btn-default tw-mr-2">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                        <button type="button" class="btn btn-primary" onclick="newNote()">
                            <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_new_note'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Note -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-9">
                                <input type="text" id="quickNoteTitle" class="form-control form-control-lg" 
                                       placeholder="<?php echo _l('disowebs_ops_quick_note_placeholder'); ?>"
                                       style="font-size: 16px;">
                            </div>
                            <div class="col-md-2">
                                <select id="quickNoteType" class="selectpicker" data-width="100%">
                                    <option value="general"><?php echo _l('general'); ?></option>
                                    <option value="idea"><?php echo _l('idea'); ?></option>
                                    <option value="bug"><?php echo _l('bug'); ?></option>
                                    <option value="todo"><?php echo _l('todo'); ?></option>
                                    <option value="question"><?php echo _l('question'); ?></option>
                                    <option value="reference"><?php echo _l('reference'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-primary btn-block" onclick="saveQuickNote()">
                                    <i class="fa fa-save"></i>
                                </button>
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
                                <select id="filterProject" class="selectpicker" data-width="100%" data-live-search="true" onchange="filterNotes()">
                                    <option value=""><?php echo _l('all_projects'); ?></option>
                                    <?php foreach ($projects as $p) { ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filterType" class="selectpicker" data-width="100%" onchange="filterNotes()">
                                    <option value=""><?php echo _l('all_types'); ?></option>
                                    <option value="general"><?php echo _l('general'); ?></option>
                                    <option value="idea"><?php echo _l('idea'); ?></option>
                                    <option value="bug"><?php echo _l('bug'); ?></option>
                                    <option value="todo"><?php echo _l('todo'); ?></option>
                                    <option value="question"><?php echo _l('question'); ?></option>
                                    <option value="reference"><?php echo _l('reference'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="searchNotes" class="form-control" placeholder="<?php echo _l('search'); ?>..." onkeyup="filterNotes()">
                            </div>
                            <div class="col-md-3 tw-text-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default active" onclick="setView('grid')">
                                        <i class="fa fa-th"></i>
                                    </button>
                                    <button type="button" class="btn btn-default" onclick="setView('list')">
                                        <i class="fa fa-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Grid -->
            <div class="col-md-12" id="notesGrid">
                <div class="row">
                    <?php if (!empty($notes)) { ?>
                    <?php foreach ($notes as $note) { ?>
                    <div class="col-md-4 note-item" data-project="<?php echo $note->project_id; ?>" data-type="<?php echo $note->note_type; ?>">
                        <div class="panel_s note-card note-type-<?php echo $note->note_type; ?> tw-h-full">
                            <div class="panel-body">
                                <div class="tw-flex tw-justify-between tw-items-start tw-mb-2">
                                    <span class="label label-<?php echo $note->note_type == 'bug' ? 'danger' : ($note->note_type == 'idea' ? 'warning' : ($note->note_type == 'todo' ? 'info' : 'default')); ?>">
                                        <?php echo ucfirst($note->note_type); ?>
                                    </span>
                                    <div class="dropdown">
                                        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="#" onclick="editNote(<?php echo $note->id; ?>); return false;"><i class="fa fa-pencil"></i> <?php echo _l('edit'); ?></a></li>
                                            <?php if ($note->note_type == 'todo' && !$note->is_resolved) { ?>
                                            <li><a href="#" onclick="resolveNote(<?php echo $note->id; ?>); return false;"><i class="fa fa-check"></i> <?php echo _l('mark_resolved'); ?></a></li>
                                            <?php } ?>
                                            <li><a href="#" onclick="deleteNote(<?php echo $note->id; ?>); return false;"><i class="fa fa-trash"></i> <?php echo _l('delete'); ?></a></li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <h5 class="tw-font-bold tw-mb-2 <?php echo $note->is_resolved ? 'tw-line-through tw-text-neutral-400' : ''; ?>">
                                    <?php echo e($note->title); ?>
                                </h5>
                                
                                <?php if ($note->content) { ?>
                                <p class="tw-text-sm tw-text-neutral-600 tw-mb-3" style="max-height: 80px; overflow: hidden;">
                                    <?php echo nl2br(e(substr($note->content, 0, 200))); ?>
                                    <?php echo strlen($note->content) > 200 ? '...' : ''; ?>
                                </p>
                                <?php } ?>
                                
                                <?php if ($note->tags) { ?>
                                <div class="tw-mb-3">
                                    <?php foreach (explode(',', $note->tags) as $tag) { ?>
                                    <span class="label label-default tw-mr-1"><?php echo trim($tag); ?></span>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                                
                                <div class="tw-flex tw-justify-between tw-items-center tw-text-xs tw-text-neutral-400 tw-border-t tw-pt-2">
                                    <span>
                                        <?php if ($note->project_name) { ?>
                                        <i class="fa fa-folder-o"></i> <?php echo e($note->project_name); ?>
                                        <?php } ?>
                                    </span>
                                    <span><?php echo time_ago($note->created_at); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } else { ?>
                    <div class="col-md-12">
                        <div class="panel_s">
                            <div class="panel-body tw-text-center tw-py-8">
                                <i class="fa fa-sticky-note fa-3x tw-text-neutral-300 tw-mb-3"></i>
                                <h4 class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_notes'); ?></h4>
                                <p class="tw-text-neutral-400"><?php echo _l('disowebs_ops_no_notes_desc'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Note Modal -->
<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php echo form_open(admin_url('disowebs_ops/sdlc/save_note'), ['id' => 'noteForm']); ?>
            <input type="hidden" name="id" id="noteId">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="noteModalTitle"><?php echo _l('disowebs_ops_new_note'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label><?php echo _l('title'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="noteTitle" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?php echo _l('type'); ?></label>
                            <select name="note_type" id="noteType" class="selectpicker" data-width="100%">
                                <option value="general"><?php echo _l('general'); ?></option>
                                <option value="idea"><?php echo _l('idea'); ?></option>
                                <option value="bug"><?php echo _l('bug'); ?></option>
                                <option value="todo"><?php echo _l('todo'); ?></option>
                                <option value="question"><?php echo _l('question'); ?></option>
                                <option value="reference"><?php echo _l('reference'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><?php echo _l('content'); ?></label>
                    <textarea name="content" id="noteContent" class="form-control" rows="8"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('project'); ?></label>
                            <select name="project_id" id="noteProject" class="selectpicker" data-width="100%" data-live-search="true">
                                <option value=""><?php echo _l('no_project'); ?></option>
                                <?php foreach ($projects as $p) { ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('tags'); ?></label>
                            <input type="text" name="tags" id="noteTags" class="form-control" placeholder="<?php echo _l('disowebs_ops_tags_placeholder'); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('related_file'); ?></label>
                            <input type="text" name="related_file" id="noteFile" class="form-control" placeholder="path/to/file.php">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('link'); ?></label>
                            <input type="url" name="link" id="noteLink" class="form-control" placeholder="https://...">
                        </div>
                    </div>
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
function filterNotes() {
    var project = $('#filterProject').val();
    var type = $('#filterType').val();
    var search = $('#searchNotes').val().toLowerCase();
    
    $('.note-item').each(function() {
        var $item = $(this);
        var show = true;
        
        if (project && $item.data('project') != project) show = false;
        if (type && $item.data('type') != type) show = false;
        if (search && $item.text().toLowerCase().indexOf(search) === -1) show = false;
        
        $item.toggle(show);
    });
}

function setView(view) {
    // Toggle button active state
    $('.btn-group .btn').removeClass('active');
    if (view === 'grid') {
        $('.note-item').removeClass('col-md-12').addClass('col-md-4');
    } else {
        $('.note-item').removeClass('col-md-4').addClass('col-md-12');
    }
}

function saveQuickNote() {
    var title = $('#quickNoteTitle').val().trim();
    if (!title) {
        alert_float('warning', '<?php echo _l('field_is_required'); ?>');
        return;
    }
    
    $.post(admin_url + 'disowebs_ops/sdlc/save_note', {
        title: title,
        note_type: $('#quickNoteType').val()
    }, function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            alert_float('success', '<?php echo _l('saved'); ?>');
            setTimeout(function() { location.reload(); }, 500);
        }
    });
}

function newNote() {
    $('#noteForm')[0].reset();
    $('#noteId').val('');
    $('#noteModalTitle').text('<?php echo _l('disowebs_ops_new_note'); ?>');
    $('.selectpicker').selectpicker('refresh');
    $('#noteModal').modal('show');
}

function editNote(id) {
    $.get(admin_url + 'disowebs_ops/sdlc/get_note/' + id, function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            var n = data.note;
            $('#noteId').val(n.id);
            $('#noteTitle').val(n.title);
            $('#noteContent').val(n.content);
            $('#noteTags').val(n.tags);
            $('#noteFile').val(n.related_file);
            $('#noteLink').val(n.link);
            $('#noteType').selectpicker('val', n.note_type);
            $('#noteProject').selectpicker('val', n.project_id);
            $('#noteModalTitle').text('<?php echo _l('edit'); ?> Note');
            $('#noteModal').modal('show');
        }
    });
}

function resolveNote(id) {
    $.post(admin_url + 'disowebs_ops/sdlc/resolve_note/' + id, function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            location.reload();
        }
    });
}

function deleteNote(id) {
    if (confirm('<?php echo _l('confirm_delete'); ?>')) {
        $.post(admin_url + 'disowebs_ops/sdlc/delete_note/' + id, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                location.reload();
            }
        });
    }
}

$(function() {
    // Quick note on enter
    $('#quickNoteTitle').on('keypress', function(e) {
        if (e.which === 13) {
            saveQuickNote();
        }
    });
    
    appValidateForm($('#noteForm'), {
        title: 'required'
    });
});
</script>
