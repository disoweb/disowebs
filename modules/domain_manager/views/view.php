<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <?= form_hidden('domain_id', $domain->id) ?>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-text-lg tw-font-bold tw-text-neutral-800 tw-mt-0">
                    <div class="tw-space-x-3 tw-flex tw-items-center">
                        <span class="tw-truncate">
                            # <?= e($domain->id); ?> <?= e($domain->domain_name); ?>
                        </span>
                    </div>
                </h4>
            </div>
            <div class="visible-xs">
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 project-overview-left">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="tw-font-semibold tw-text-base tw-mb-4"><?=_l('domain_manager_overview')?></h4>
                                <dl class="tw-grid tw-grid-cols-1 tw-gap-x-4 tw-gap-y-3 sm:tw-grid-cols-2">
                                    <div class="sm:tw-col-span-1 project-overview-id">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600"><?=_l('domain_manager_id')?> # </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500"><?=$domain->id?></dd>
                                    </div>

                                    <div class="sm:tw-col-span-1 project-overview-customer">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600"><?= _l('domain_manager_domain_name')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                                <?=$domain->domain_name?>
                                        </dd>
                                    </div>

                                    <div class="sm:tw-col-span-1 project-overview-billing">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                        <?= _l('domain_manager_client')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                            <a href="<?= admin_url('clients/client/'.$domain->client_id)?>">
                                                <?=$domain->client_name?>
                                            </a>
                                        </dd>
                                    </div>
                                    <div class="sm:tw-col-span-1 project-overview-customer">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600"><?=_l('domain_manager_project')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                            <a href="<?= admin_url('projects/view/'.$domain->project_id)?>">
                                                <?=$domain->project_name?>
                                            </a>
                                        </dd>
                                    </div>

                                    <div class="sm:tw-col-span-1 project-overview-status">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?=_l('domain_manager_registrar')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <?= $domain->registrar?> </dd>
                                    </div>
                                    <div class="sm:tw-col-span-1 project-overview-deadline">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?= _l('domain_manager_dns_hosting')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <?= _l($domain->dns_hosting)?> </dd> </dd>
                                    </div>

                                    <div class="sm:tw-col-span-1 project-overview-date-created">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?= _l('domain_manager_purchase_date')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <?= _d($domain->purchase_date)?> </dd>
                                    </div>
                                    <div class="sm:tw-col-span-1 project-overview-start-date">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?=_l('domain_manager_expiry_date')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <?= _d($domain->expiry_date)?> </dd>
                                    </div>
                                    <div class="sm:tw-col-span-1 project-overview-billing">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                        <?= _l('domain_manager_provider')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <?= $domain->provider_name?> </dd>
                                    </div>
                                    
                                    <div class="sm:tw-col-span-1 project-overview-customer">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600"><?=_l('domain_manager_provider_url')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                            <a href="<?= $domain->provider_url?>">
                                            <?= $domain->provider_url?> 
                                            </a>
                                        </dd>
                                    </div>
                                  
                                    <div class="sm:tw-col-span-1 project-overview-status">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?=_l('domain_manager_username')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <?= $domain->username?>  </dd>
                                    </div>

                                    <div class="sm:tw-col-span-1 project-overview-date-created">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?= _l('domain_manager_password')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <?= $domain->password?>  </dd>
                                    </div>
                                   
                                    <div class="sm:tw-col-span-1 project-overview-deadline">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?=  _l('Registration Status')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <?= _l($domain->registration_status)?> </dd>
                                    </div>


                                    <div class="sm:tw-col-span-1 project-overview-estimated-hours">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?=_l('domain_manager_status')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm text-neutral-900">
                                        <?= _l($domain->status)?></dd>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="sm:tw-col-span-2 project-overview-description tc-content">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                        <?=_l('description')?> </dt>
                                        <dd class="tw-mt-1 tw-space-y-5 tw-text-sm tw-text-neutral-500">
                                            <p class="text-muted tw-mb-0">
                                            <?= $domain->description?></p>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(isset($hosting) && !empty($hosting)){ ?>
                <div class="col-md-6 project-overview-right">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="tw-font-semibold tw-text-base tw-mb-4"><?=_l('domain_manager_hosting_overview')?></h4>
                                <dl class="tw-grid tw-grid-cols-1 tw-gap-x-4 tw-gap-y-3 sm:tw-grid-cols-2">
                                    <div class="sm:tw-col-span-1 project-overview-id">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600"><?=_l('domain_manager_hosting_id')?> # </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500"><?=$hosting->id?></dd>
                                    </div>

                                    <div class="sm:tw-col-span-1 project-overview-customer">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600"><?= _l('domain_manager_domain_name')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                                <?=$domain->domain_name?>
                                        </dd>
                                    </div>

                                    <div class="sm:tw-col-span-1 project-overview-billing">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600"><?=_l('domain_manager_provider')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500"><?=$hosting->provider?></dd>
                                    </div>
                                    
                                    <div class="sm:tw-col-span-1 project-overview-customer">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600"><?=_l('domain_manager_provider_url')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <a href="<?= (isset($hosting->access_url) && !empty($hosting->access_url) && strpos($hosting->access_url, 'https://') === 0 ? $hosting->access_url : 'https://default-url.com') ?>">
    <?=$hosting->access_url ?: ''?>
</a>
                                        </dd>
                                    </div>
                                  
                                    <div class="sm:tw-col-span-1 project-overview-status">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?=_l('domain_manager_username')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <?=$hosting->username?> </dd>
                                    </div>

                                    <div class="sm:tw-col-span-1 project-overview-date-created">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?= _l('domain_manager_password')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500"><?=$hosting->password?>  </dd>
                                    </div>
                                   
                                    <div class="sm:tw-col-span-1 project-overview-billing">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                        <?= _l('domain_manager_client')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <a href="<?= admin_url('clients/client/'.$hosting->client_id)?>">
                                                <?=$hosting->client_name?>
                                            </a></dd>
                                    </div>
                                    <div class="sm:tw-col-span-1 project-overview-customer">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600"><?=_l('domain_manager_project')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                            <a href="<?= admin_url('projects/view/'.$hosting->project_id)?>">
                                                <?=$hosting->project_name?>
                                            </a>
                                        </dd>
                                    </div>
                                    <div class="sm:tw-col-span-1 project-overview-start-date">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?=_l('domain_manager_start_date')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <?= _d($hosting->start_date)?> </dd>
                                    </div>
                                    <div class="sm:tw-col-span-1 project-overview-deadline">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?= _l('domain_manager_expiry_date')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-500">
                                        <?= _d($hosting->expiration_date)?> </dd>
                                    </div>
                                   


                                    <div class="sm:tw-col-span-1 project-overview-estimated-hours">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                            <?=_l('domain_manager_status')?> </dt>
                                        <dd class="tw-mt-1 tw-text-sm text-neutral-900">
                                        <?= _l($hosting->status)?></dd>
                                    </div>

                                   


                                    <div class="clearfix"></div>
                                    <div class="sm:tw-col-span-2 project-overview-description tc-content">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-600">
                                        <?=_l('description')?> </dt>
                                        <dd class="tw-mt-1 tw-space-y-5 tw-text-sm tw-text-neutral-500">
                                            <p class="text-muted tw-mb-0">
                                            <?= $hosting->description?> </p>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
         
        </div>
    </div>
</div>
</div>
</div>



<?php init_tail(); ?>
<!-- For invoices table -->
<script>
    $(".menu-item-domain_manager").addClass('active');
    $(".sub-menu-item-domain_manager").addClass('active');
    
</script>

</body>

</html>