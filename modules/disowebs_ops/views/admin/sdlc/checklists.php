<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php $this->load->view('admin/includes/alerts'); ?>

            <!-- Header -->
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                    <h4 class="tw-font-bold tw-text-xl tw-mb-0">
                        <i class="fa fa-tasks tw-mr-2"></i>
                        <?php echo _l('disowebs_ops_checklists'); ?>
                        <?php if ($project) { ?>
                        <small class="tw-text-neutral-500">- <?php echo e($project->name); ?></small>
                        <?php } ?>
                    </h4>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createChecklistModal">
                        <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_start_checklist'); ?>
                    </button>
                </div>
            </div>

            <!-- Project Filter -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <form method="get" class="form-inline">
                            <div class="form-group tw-mr-3">
                                <select name="project" class="form-control selectpicker" data-live-search="true" data-width="300px" onchange="this.form.submit()">
                                    <option value=""><?php echo _l('disowebs_ops_all_projects'); ?></option>
                                    <?php foreach ($projects as $p) { ?>
                                    <option value="<?php echo $p->id; ?>" <?php echo $project_id == $p->id ? 'selected' : ''; ?>>
                                        <?php echo e($p->name); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Template Cards -->
            <div class="col-md-12">
                <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('disowebs_ops_checklist_templates'); ?></h5>
                <div class="row">
                    <?php foreach ($templates as $template) { 
                        $type_colors = [
                            'kickoff' => 'primary',
                            'requirements' => 'info',
                            'design' => 'purple',
                            'development' => 'success',
                            'code_review' => 'warning',
                            'qa' => 'danger',
                            'deployment' => 'success',
                            'handover' => 'info',
                            'maintenance' => 'default',
                        ];
                        $color = $type_colors[$template->type] ?? 'default';
                    ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel_s tw-border-l-4 tw-border-<?php echo $color; ?>">
                            <div class="panel-body">
                                <h6 class="tw-font-semibold tw-mb-1"><?php echo e($template->name); ?></h6>
                                <p class="tw-text-sm tw-text-neutral-500 tw-mb-3"><?php echo e($template->description); ?></p>
                                <button type="button" class="btn btn-default btn-xs" onclick="useTemplate(<?php echo $template->id; ?>, '<?php echo e(addslashes($template->name)); ?>')">
                                    <i class="fa fa-play"></i> <?php echo _l('disowebs_ops_use_template'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Active Checklists -->
            <div class="col-md-12">
                <h5 class="tw-font-semibold tw-mb-3 tw-mt-4"><?php echo _l('disowebs_ops_active_checklists'); ?></h5>
                
                <?php if (!empty($checklists)) { ?>
                <div class="row">
                    <?php foreach ($checklists as $checklist) { 
                        $status_class = $checklist->status == 'completed' ? 'success' : ($checklist->status == 'in_progress' ? 'warning' : 'default');
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="panel_s">
                            <div class="panel-heading">
                                <div class="tw-flex tw-justify-between tw-items-start">
                                    <div>
                                        <span class="label label-<?php echo $status_class; ?> tw-mb-2"><?php echo ucfirst(str_replace('_', ' ', $checklist->status)); ?></span>
                                        <h5 class="panel-title tw-mb-1"><?php echo e($checklist->name); ?></h5>
                                        <?php if (isset($checklist->project_name)) { ?>
                                        <small class="tw-text-neutral-400"><?php echo e($checklist->project_name); ?></small>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="tw-mb-3">
                                    <div class="tw-flex tw-justify-between tw-mb-1">
                                        <span class="tw-text-sm"><?php echo _l('progress'); ?></span>
                                        <span class="tw-text-sm tw-font-semibold"><?php echo $checklist->progress; ?>%</span>
                                    </div>
                                    <div class="progress tw-mb-0" style="height: 8px;">
                                        <div class="progress-bar progress-bar-<?php echo $status_class; ?>" style="width: <?php echo $checklist->progress; ?>%;"></div>
                                    </div>
                                </div>
                                <?php if ($checklist->due_date) { ?>
                                <p class="tw-text-sm tw-text-neutral-500 tw-mb-2">
                                    <i class="fa fa-calendar"></i> Due: <?php echo _d($checklist->due_date); ?>
                                </p>
                                <?php } ?>
                                <a href="<?php echo admin_url('disowebs_ops/sdlc/checklist/' . $checklist->id); ?>" class="btn btn-default btn-sm btn-block">
                                    <?php echo _l('disowebs_ops_continue'); ?> <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } else { ?>
                <div class="panel_s">
                    <div class="panel-body tw-text-center tw-py-8">
                        <i class="fa fa-tasks fa-3x tw-text-neutral-300 tw-mb-3"></i>
                        <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_active_checklists'); ?></p>
                        <p class="tw-text-sm tw-text-neutral-400"><?php echo _l('disowebs_ops_use_template_hint'); ?></p>
                    </div>
                </div>
                <?php } ?>
            </div>

        </div>
    </div>
</div>

<!-- Create Checklist Modal -->
<div class="modal fade" id="createChecklistModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo admin_url('disowebs_ops/sdlc/create_checklist_from_template'); ?>" method="post">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo _l('disowebs_ops_start_checklist'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo _l('disowebs_ops_template'); ?> <span class="text-danger">*</span></label>
                        <select name="template_id" class="form-control selectpicker" data-live-search="true" required>
                            <option value=""><?php echo _l('select'); ?>...</option>
                            <?php foreach ($templates as $template) { ?>
                            <option value="<?php echo $template->id; ?>"><?php echo e($template->name); ?> (<?php echo ucfirst($template->type); ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('project'); ?> <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-control selectpicker" data-live-search="true" required>
                            <option value=""><?php echo _l('select'); ?>...</option>
                            <?php foreach ($projects as $p) { ?>
                            <option value="<?php echo $p->id; ?>" <?php echo $project_id == $p->id ? 'selected' : ''; ?>>
                                <?php echo e($p->name); ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('disowebs_ops_create_and_start'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
function useTemplate(templateId, templateName) {
    $('select[name="template_id"]').val(templateId).selectpicker('refresh');
    $('#createChecklistModal').modal('show');
}
</script>
