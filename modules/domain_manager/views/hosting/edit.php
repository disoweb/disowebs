<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="tw-max-w-4xl tw-mx-auto">
            <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
            <?=_l('domain_manager_host_edit')?></h4>
            <?php echo form_open(admin_url('domain_manager/update_hosting'),['id'=>'save_form']); ?>

                <div class="panel_s">
                <div class="panel-body">
                <div class="container-fluid">
                <input type="hidden" value="<?=$hosting->id?>" name="id">

                            <div class="form-group">
                                <label for="domain_id"><?php echo _l('domain'); ?></label>
                                <select name="domain_id" id="domain_id" class="form-control selectpicker"
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="">-  Domain -</option>
                                    <?php foreach ($domains as $domain) { ?>
                                    <option value="<?= $domain['id']; ?>" <?= $hosting->domain_id == $domain['id']?'selected':''; ?>><?= $domain['domain_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                           
                            <div class="row">   
                                <div class="col-md-6">
                                    <?php echo render_input('domain_manager_provider', _l('domain_manager_provider'), $hosting->provider, 'text', ['required' => 'required','id' => 'domain_manager_provider', 'placeholder' =>  _l('domain_manager_provider'), 'autocomplete' => 'off']); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_input('domain_manager_provider_url', _l('domain_manager_provider_url'), $hosting->access_url, 'text', ['id' => 'domain_manager_provider_url', 'placeholder' =>  _l('domain_manager_provider_url'), 'autocomplete' => 'off']); ?>
                                </div>
                                
                                <div class="col-md-6">
                                    <?php echo render_input('domain_manager_username', _l('domain_manager_username'), $hosting->username, 'text', ['id' => 'domain_manager_username', 'placeholder' =>  _l('domain_manager_username'), 'autocomplete' => 'off']); ?>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password" class="control-label">
                                        <?= _l('domain_manager_password'); ?>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control password" name="password" value="<?=$hosting->password?>"
                                                autocomplete="false">
                                            <span class="input-group-addon tw-border-l-0">
                                            <a href="#password" class="show_password"
                                                onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_date_input('domain_manager_start_date', _l('domain_manager_start_date'), $hosting->start_date, ['id' => 'domain_manager_start_date', 'autocomplete' => 'off','class'=>'col-ms-6']); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_date_input('domain_manager_expiry_date', 'Expiry Date',  $hosting->expiration_date, ['id' => 'domain_manager_expiry_date', 'autocomplete' => 'off']); ?>
                                </div>
                               
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="project_id"><?php echo _l('Project'); ?></label>
                                        <select name="project_id" id="project_id" class="form-control selectpicker"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <option value="">- Project -</option>
                                            <?php foreach ($projects as $project) { ?>
                                            <option value="<?= $project['id']; ?>"  <?= $hosting->project_id == $project['id'] ?'selected':'' ?>><?= $project['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id"><?php echo _l('Client'); ?></label>
                                        <select name="client_id" id="client_id" class="form-control selectpicker"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <option value="">- Client -</option>
                                            <?php foreach ($clients as $client) { ?>
                                            <option value="<?= $client['userid']; ?>" <?= $hosting->client_id == $client['userid'] ?'selected':'' ?>><?= $client['company']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                               
                            </div>
                            <div class="form-group">
                                <label for="status"><?php echo _l('Status'); ?></label>
                                <select name="status" class="selectpicker" data-width="100%" id="status">

                                    <?php $status = [
                                        'active'=>  _l('domain_manager_active'),
                                        'domain_manager_expiring_soon'=>  _l('domain_manager_expiring_soon'),
                                        'expired'=>  _l('domain_manager_expired'),
                                        'pending'=>  _l('domain_manager_pending'),
                                    ]; foreach ($status as $key => $s) { ?>
                                    <option value="<?=$key?>" <?= $hosting->status == $key ?'selected':'' ?>><?=$s?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php echo render_textarea('description', 'Description', $hosting->description, ['placeholder' => 'Description']); ?>
                        </div>


                    </div>
                    <div class="panel-footer text-right">
                        <button class="btn btn-primary" type="submit"><?=_l('Update')?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>
        </div>
    </div>
</div>


<?php init_tail(); ?>
<script>
    $(".menu-item-domain_manager").addClass('active');
    $(".sub-menu-item-hosting_view").addClass('active');
</script>
</body>
</html>