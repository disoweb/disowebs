<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$CI = &get_instance();
$CI->load->model('disowebs_ops/dw_proof_entries_model');
$CI->load->model('disowebs_ops/dw_proof_files_model');

$proof_entries = $CI->dw_proof_entries_model->get_by_project($project->id);
$proof_files = $CI->dw_proof_files_model->get_by_project($project->id);

$files_by_entry = [];
foreach ($proof_files as $file) {
    $files_by_entry[$file['proof_entry_id']][] = $file;
}

$can_manage_entries = disowebs_ops_can_manage_proof_entries();
$can_delete_entries = disowebs_ops_can_delete_proof_entries();
$can_delete_files = disowebs_ops_can_delete_proof_files();
$entries_with_files = 0;
foreach ($proof_entries as $entry) {
    if (!empty($files_by_entry[$entry['id']])) {
        $entries_with_files++;
    }
}
$proof_required = ((int) $project->status === 4 && empty($proof_entries));
?>

<div class="panel_s">
    <div class="panel-body">
        <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
            <div>
                <h4 class="no-margin"><?php echo e(_l('disowebs_ops_tab_proof')); ?></h4>
                <p class="text-muted mtop5"><?php echo e(_l('disowebs_ops_proof_intro')); ?></p>
            </div>
            <div class="tw-flex tw-items-center tw-gap-2 mtop10 md:mtop0">
                <a class="btn btn-default btn-sm" target="_blank" href="<?php echo admin_url('disowebs_ops/disowebs_ops_projects/export_case_study/' . $project->id); ?>">
                    <i class="fa fa-file-text-o"></i> <?php echo e(_l('disowebs_ops_proof_export')); ?>
                </a>
                <?php if (!$can_manage_entries) { ?>
                <span class="label label-default"><?php echo e(_l('disowebs_ops_view_only')); ?></span>
                <?php } ?>
            </div>
        </div>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4 mtop15">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_proof_total')); ?></div>
                    <h4 class="no-margin"><?php echo e(count($proof_entries)); ?></h4>
                </div>
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_proof_with_files')); ?></div>
                    <h4 class="no-margin"><?php echo e($entries_with_files); ?></h4>
                </div>
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_proof_files_total')); ?></div>
                    <h4 class="no-margin"><?php echo e(count($proof_files)); ?></h4>
                </div>
            </div>
        </div>

        <?php if ($proof_required) { ?>
        <div class="alert alert-warning mtop15">
            <?php echo e(_l('disowebs_ops_proof_required_before_close')); ?>
        </div>
        <?php } ?>

        <?php if ($can_manage_entries) { ?>
        <div class="mtop20">
            <a class="btn btn-primary btn-sm" data-toggle="collapse" href="#dw-add-proof" aria-expanded="false">
                <i class="fa fa-plus"></i> <?php echo e(_l('disowebs_ops_proof_add')); ?>
            </a>
        </div>

        <div id="dw-add-proof" class="collapse mtop15">
            <?php echo form_open_multipart(admin_url('disowebs_ops/disowebs_ops_projects/add_proof_entry/' . $project->id)); ?>
            <?php echo render_input('title', 'disowebs_ops_proof_title'); ?>
            <?php echo render_textarea('problem', 'disowebs_ops_proof_problem', '', ['rows' => 3]); ?>
            <?php echo render_textarea('solution', 'disowebs_ops_proof_solution', '', ['rows' => 3]); ?>
            <?php echo render_textarea('outcome', 'disowebs_ops_proof_outcome', '', ['rows' => 3]); ?>
            <div class="form-group">
                <label for="proof_files"><?php echo e(_l('disowebs_ops_proof_files')); ?></label>
                <input type="file" class="form-control" name="proof_files[]" id="proof_files" multiple
                    extension="<?php echo str_replace('.', '', get_option('allowed_files')); ?>"
                    filesize="<?php echo file_upload_max_size(); ?>">
                <p class="text-muted mtop5"><?php echo e(_l('disowebs_ops_proof_files_hint')); ?></p>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_proof_save')); ?></button>
            <?php echo form_close(); ?>
        </div>
        <?php } ?>

        <?php if (empty($proof_entries)) { ?>
        <p class="text-muted mtop20"><?php echo e(_l('disowebs_ops_proof_none')); ?></p>
        <?php } ?>

        <?php foreach ($proof_entries as $entry) { ?>
        <?php $entry_files = $files_by_entry[$entry['id']] ?? []; ?>
        <div class="panel panel-default mtop20">
            <div class="panel-body">
                <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between">
                    <div>
                        <h4 class="no-margin"><?php echo e($entry['title']); ?></h4>
                        <div class="text-muted mtop5">
                            <?php echo e(_l('disowebs_ops_proof_created', _dt($entry['created_at']))); ?>
                            <?php if (!empty($entry['created_by'])) { ?>
                            <span class="text-muted">• <?php echo e(get_staff_full_name($entry['created_by'])); ?></span>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($can_manage_entries || $can_delete_entries) { ?>
                    <div class="tw-flex tw-flex-wrap tw-gap-2 mtop10 md:mtop0">
                        <?php if ($can_manage_entries) { ?>
                        <a class="btn btn-default btn-sm" data-toggle="collapse" href="#proof-edit-<?php echo e($entry['id']); ?>">
                            <?php echo e(_l('disowebs_ops_edit')); ?>
                        </a>
                        <?php } ?>
                        <?php if ($can_delete_entries) { ?>
                        <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/delete_proof_entry/' . $project->id . '/' . $entry['id']), ['class' => 'tw-inline-block']); ?>
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo e(_l('disowebs_ops_confirm_delete')); ?>');">
                            <?php echo e(_l('disowebs_ops_delete')); ?>
                        </button>
                        <?php echo form_close(); ?>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>

                <div class="mtop15">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_proof_problem')); ?></div>
                    <div><?php echo nl2br(e($entry['problem'])); ?></div>
                </div>
                <div class="mtop15">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_proof_solution')); ?></div>
                    <div><?php echo nl2br(e($entry['solution'])); ?></div>
                </div>
                <div class="mtop15">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_proof_outcome')); ?></div>
                    <div><?php echo nl2br(e($entry['outcome'])); ?></div>
                </div>

                <div class="mtop15">
                    <div class="text-muted"><?php echo e(_l('disowebs_ops_proof_files')); ?></div>
                    <?php if (empty($entry_files)) { ?>
                    <p class="text-muted mtop5"><?php echo e(_l('disowebs_ops_proof_files_none')); ?></p>
                    <?php } else { ?>
                    <div class="mtop10">
                        <?php foreach ($entry_files as $file) { ?>
                        <?php $file_url = disowebs_ops_proof_upload_url($entry['id'], $file['file_path']); ?>
                        <div class="tw-flex tw-items-center tw-justify-between tw-gap-2 mtop5">
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <i class="<?php echo e(get_mime_class($file['file_type'])); ?>"></i>
                                <a href="<?php echo e($file_url); ?>" target="_blank"><?php echo e($file['file_path']); ?></a>
                            </div>
                            <?php if ($can_delete_files) { ?>
                            <?php echo form_open(admin_url('disowebs_ops/disowebs_ops_projects/delete_proof_file/' . $project->id . '/' . $entry['id'] . '/' . $file['id']), ['class' => 'tw-inline-block']); ?>
                            <button type="submit" class="btn btn-link text-danger btn-sm" onclick="return confirm('<?php echo e(_l('disowebs_ops_confirm_delete')); ?>');">
                                <?php echo e(_l('disowebs_ops_delete')); ?>
                            </button>
                            <?php echo form_close(); ?>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>

                <?php if ($can_manage_entries) { ?>
                <div id="proof-edit-<?php echo e($entry['id']); ?>" class="collapse mtop15">
                    <?php echo form_open_multipart(admin_url('disowebs_ops/disowebs_ops_projects/update_proof_entry/' . $project->id . '/' . $entry['id'])); ?>
                    <?php echo render_input('title', 'disowebs_ops_proof_title', $entry['title']); ?>
                    <?php echo render_textarea('problem', 'disowebs_ops_proof_problem', $entry['problem'], ['rows' => 3]); ?>
                    <?php echo render_textarea('solution', 'disowebs_ops_proof_solution', $entry['solution'], ['rows' => 3]); ?>
                    <?php echo render_textarea('outcome', 'disowebs_ops_proof_outcome', $entry['outcome'], ['rows' => 3]); ?>
                    <div class="form-group">
                        <label for="proof_files_<?php echo e($entry['id']); ?>"><?php echo e(_l('disowebs_ops_proof_files_add')); ?></label>
                        <input type="file" class="form-control" name="proof_files[]" id="proof_files_<?php echo e($entry['id']); ?>" multiple
                            extension="<?php echo str_replace('.', '', get_option('allowed_files')); ?>"
                            filesize="<?php echo file_upload_max_size(); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo e(_l('disowebs_ops_proof_update')); ?></button>
                    <?php echo form_close(); ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
