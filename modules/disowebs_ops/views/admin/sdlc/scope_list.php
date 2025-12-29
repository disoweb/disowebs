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
                            <i class="fa fa-file-text-o tw-mr-2"></i> <?php echo _l('disowebs_ops_scope_documents'); ?>
                        </h4>
                        <p class="tw-text-neutral-500 tw-mb-0"><?php echo _l('disowebs_ops_scope_desc'); ?></p>
                    </div>
                    <div>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc'); ?>" class="btn btn-default tw-mr-2">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                        <a href="<?php echo admin_url('disowebs_ops/sdlc/scope_document'); ?>" class="btn btn-primary">
                            <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_new_scope'); ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body tw-py-2">
                        <div class="row">
                            <div class="col-md-4">
                                <select id="filterProject" class="selectpicker" data-width="100%" data-live-search="true" onchange="filterScope()">
                                    <option value=""><?php echo _l('all_projects'); ?></option>
                                    <?php foreach ($projects as $p) { ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="filterStatus" class="selectpicker" data-width="100%" onchange="filterScope()">
                                    <option value=""><?php echo _l('all'); ?></option>
                                    <option value="draft"><?php echo _l('draft'); ?></option>
                                    <option value="pending_approval"><?php echo _l('pending_approval'); ?></option>
                                    <option value="approved"><?php echo _l('approved'); ?></option>
                                    <option value="signed"><?php echo _l('signed'); ?></option>
                                    <option value="rejected"><?php echo _l('rejected'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input type="text" id="searchScope" class="form-control" placeholder="<?php echo _l('search'); ?>...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" onclick="filterScope()"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scope Documents Grid -->
            <div class="col-md-12">
                <div class="row" id="scopeGrid">
                    <?php if (!empty($scopes)) { ?>
                    <?php foreach ($scopes as $scope) { ?>
                    <div class="col-md-4 scope-card" data-project="<?php echo $scope->project_id; ?>" data-status="<?php echo $scope->status; ?>">
                        <div class="panel_s tw-h-full">
                            <div class="panel-body">
                                <div class="tw-flex tw-justify-between tw-items-start tw-mb-3">
                                    <div>
                                        <?php
                                        $status_class = [
                                            'draft' => 'default',
                                            'pending_approval' => 'warning',
                                            'approved' => 'success',
                                            'signed' => 'primary',
                                            'rejected' => 'danger'
                                        ];
                                        ?>
                                        <span class="label label-<?php echo $status_class[$scope->status] ?? 'default'; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $scope->status)); ?>
                                        </span>
                                    </div>
                                    <small class="tw-text-neutral-400">v<?php echo $scope->version; ?></small>
                                </div>
                                
                                <h5 class="tw-font-bold tw-mb-2">
                                    <a href="<?php echo admin_url('disowebs_ops/sdlc/scope_document/' . $scope->id); ?>">
                                        <?php echo e($scope->title); ?>
                                    </a>
                                </h5>
                                
                                <?php if ($scope->project_name) { ?>
                                <p class="tw-text-sm tw-mb-2">
                                    <i class="fa fa-folder-o tw-text-neutral-400"></i>
                                    <a href="<?php echo admin_url('projects/view/' . $scope->project_id); ?>" target="_blank">
                                        <?php echo e($scope->project_name); ?>
                                    </a>
                                </p>
                                <?php } ?>
                                
                                <?php if ($scope->client_name) { ?>
                                <p class="tw-text-sm tw-mb-3">
                                    <i class="fa fa-building-o tw-text-neutral-400"></i>
                                    <?php echo e($scope->client_name); ?>
                                </p>
                                <?php } ?>

                                <!-- Stats Row -->
                                <div class="tw-flex tw-justify-between tw-text-sm tw-text-neutral-500 tw-mb-3">
                                    <span><i class="fa fa-list"></i> <?php echo $scope->item_count ?? 0; ?> <?php echo _l('items'); ?></span>
                                    <span>
                                        <?php if ($scope->total_budget > 0) { ?>
                                        <?php echo app_format_money($scope->total_budget, get_base_currency()); ?>
                                        <?php } else { ?>
                                        <?php echo _l('no_budget'); ?>
                                        <?php } ?>
                                    </span>
                                </div>

                                <?php if ($scope->status == 'signed' && $scope->client_signed_date) { ?>
                                <div class="tw-bg-success-50 tw-p-2 tw-rounded tw-text-sm tw-mb-3">
                                    <i class="fa fa-check-circle tw-text-success"></i>
                                    <?php echo _l('signed_on'); ?>: <?php echo _d($scope->client_signed_date); ?>
                                </div>
                                <?php } ?>

                                <div class="tw-border-t tw-pt-3 tw-mt-auto">
                                    <div class="btn-group btn-group-justified">
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/scope_document/' . $scope->id); ?>" class="btn btn-default btn-sm">
                                            <i class="fa fa-pencil"></i> <?php echo _l('edit'); ?>
                                        </a>
                                        <?php if ($scope->status == 'draft' || $scope->status == 'pending_approval') { ?>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/preview_scope/' . $scope->id); ?>" class="btn btn-default btn-sm" target="_blank">
                                            <i class="fa fa-eye"></i> <?php echo _l('preview'); ?>
                                        </a>
                                        <?php } ?>
                                        <?php if ($scope->status == 'approved' || $scope->status == 'signed') { ?>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/scope_pdf/' . $scope->id); ?>" class="btn btn-default btn-sm" target="_blank">
                                            <i class="fa fa-file-pdf-o"></i> PDF
                                        </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } else { ?>
                    <div class="col-md-12">
                        <div class="panel_s">
                            <div class="panel-body tw-text-center tw-py-8">
                                <i class="fa fa-file-text-o fa-3x tw-text-neutral-300 tw-mb-3"></i>
                                <h4 class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_scope_docs'); ?></h4>
                                <p class="tw-text-neutral-400"><?php echo _l('disowebs_ops_no_scope_docs_desc'); ?></p>
                                <a href="<?php echo admin_url('disowebs_ops/sdlc/scope_document'); ?>" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_create_first_scope'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
function filterScope() {
    var project = $('#filterProject').val();
    var status = $('#filterStatus').val();
    var search = $('#searchScope').val().toLowerCase();
    
    $('.scope-card').each(function() {
        var $card = $(this);
        var show = true;
        
        if (project && $card.data('project') != project) show = false;
        if (status && $card.data('status') != status) show = false;
        if (search && $card.text().toLowerCase().indexOf(search) === -1) show = false;
        
        $card.toggle(show);
    });
}

$(function() {
    $('#searchScope').on('keyup', function(e) {
        if (e.keyCode === 13) filterScope();
    });
});
</script>
