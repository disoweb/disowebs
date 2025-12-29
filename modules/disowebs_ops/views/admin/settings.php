<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin font-bold mbot15">
                            <i class="fa fa-cubes"></i> <?php echo _l('disowebs_ops'); ?> - <?php echo _l('settings'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('disowebs_ops/settings'), ['id' => 'disowebs-ops-settings-form']); ?>

                        <!-- Phase Settings -->
                        <h5 class="bold mbot15 mtop25"><?php echo _l('disowebs_ops_project_phases'); ?></h5>
                        
                        <div class="form-group">
                            <label for="disowebs_ops_auto_create_phases" class="control-label">
                                <?php echo _l('disowebs_ops_auto_create_phases'); ?>
                            </label>
                            <div class="radio radio-primary">
                                <input type="radio" name="settings[disowebs_ops_auto_create_phases]" value="1" id="auto_phases_yes" <?php echo get_option('disowebs_ops_auto_create_phases') == 1 ? 'checked' : ''; ?>>
                                <label for="auto_phases_yes"><?php echo _l('yes'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" name="settings[disowebs_ops_auto_create_phases]" value="0" id="auto_phases_no" <?php echo get_option('disowebs_ops_auto_create_phases') == 0 ? 'checked' : ''; ?>>
                                <label for="auto_phases_no"><?php echo _l('no'); ?></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="disowebs_ops_active_project_limit" class="control-label">
                                <?php echo _l('disowebs_ops_active_project_limit'); ?>
                            </label>
                            <input type="number" min="1" max="20" class="form-control" name="settings[disowebs_ops_active_project_limit]" 
                                   value="<?php echo get_option('disowebs_ops_active_project_limit'); ?>">
                            <p class="text-muted"><?php echo _l('disowebs_ops_active_project_limit_help'); ?></p>
                        </div>

                        <hr />

                        <!-- Proof Enforcement Settings -->
                        <h5 class="bold mbot15 mtop25">
                            <i class="fa fa-shield-alt text-info"></i> <?php echo _l('disowebs_ops_proof_enforcement'); ?>
                        </h5>

                        <div class="form-group">
                            <label class="control-label">
                                <?php echo _l('disowebs_ops_block_project_closure'); ?>
                            </label>
                            <div class="radio radio-primary">
                                <input type="radio" name="settings[disowebs_ops_block_project_closure]" value="1" id="block_closure_yes" <?php echo get_option('disowebs_ops_block_project_closure') == 1 ? 'checked' : ''; ?>>
                                <label for="block_closure_yes"><?php echo _l('yes'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" name="settings[disowebs_ops_block_project_closure]" value="0" id="block_closure_no" <?php echo get_option('disowebs_ops_block_project_closure') == 0 ? 'checked' : ''; ?>>
                                <label for="block_closure_no"><?php echo _l('no'); ?></label>
                            </div>
                            <p class="text-muted">
                                <i class="fa fa-info-circle"></i> 
                                <?php echo _l('disowebs_ops_block_project_closure_help'); ?>
                            </p>
                        </div>

                        <hr />

                        <!-- Lead Qualification Settings -->
                        <h5 class="bold mbot15 mtop25">
                            <i class="fa fa-filter text-success"></i> <?php echo _l('disowebs_ops_qualification_gate'); ?>
                        </h5>

                        <div class="form-group">
                            <label class="control-label">
                                <?php echo _l('disowebs_ops_require_lead_qualification'); ?>
                            </label>
                            <div class="radio radio-primary">
                                <input type="radio" name="settings[disowebs_ops_require_lead_qualification]" value="1" id="require_qual_yes" <?php echo get_option('disowebs_ops_require_lead_qualification') == 1 ? 'checked' : ''; ?>>
                                <label for="require_qual_yes"><?php echo _l('yes'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" name="settings[disowebs_ops_require_lead_qualification]" value="0" id="require_qual_no" <?php echo get_option('disowebs_ops_require_lead_qualification') == 0 ? 'checked' : ''; ?>>
                                <label for="require_qual_no"><?php echo _l('no'); ?></label>
                            </div>
                        </div>

                        <?php 
                        $required_fields = json_decode(get_option('disowebs_ops_required_lead_fields'), true);
                        if (!is_array($required_fields)) {
                            $required_fields = ['name', 'email', 'phonenumber', 'company'];
                        }
                        $available_fields = [
                            'name'        => _l('lead_name'),
                            'email'       => _l('lead_email'),
                            'phonenumber' => _l('lead_phone'),
                            'company'     => _l('lead_company'),
                            'address'     => _l('lead_address'),
                            'city'        => _l('lead_city'),
                            'website'     => _l('lead_website'),
                        ];
                        ?>
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('disowebs_ops_required_lead_fields'); ?></label>
                            <div class="row">
                                <?php foreach ($available_fields as $field_key => $field_label) : ?>
                                <div class="col-md-4">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="settings[disowebs_ops_required_lead_fields][]" 
                                               value="<?php echo $field_key; ?>" 
                                               id="lead_field_<?php echo $field_key; ?>"
                                               <?php echo in_array($field_key, $required_fields) ? 'checked' : ''; ?>>
                                        <label for="lead_field_<?php echo $field_key; ?>"><?php echo $field_label; ?></label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <hr />

                        <!-- Growth Flywheel Settings -->
                        <h5 class="bold mbot15 mtop25">
                            <i class="fa fa-rocket text-warning"></i> <?php echo _l('disowebs_ops_growth_flywheel'); ?>
                        </h5>

                        <div class="form-group">
                            <label class="control-label">
                                <?php echo _l('disowebs_ops_auto_testimonial_request'); ?>
                            </label>
                            <div class="radio radio-primary">
                                <input type="radio" name="settings[disowebs_ops_auto_testimonial_request]" value="1" id="auto_testimonial_yes" <?php echo get_option('disowebs_ops_auto_testimonial_request') == 1 ? 'checked' : ''; ?>>
                                <label for="auto_testimonial_yes"><?php echo _l('yes'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" name="settings[disowebs_ops_auto_testimonial_request]" value="0" id="auto_testimonial_no" <?php echo get_option('disowebs_ops_auto_testimonial_request') == 0 ? 'checked' : ''; ?>>
                                <label for="auto_testimonial_no"><?php echo _l('no'); ?></label>
                            </div>
                            <p class="text-muted">
                                <i class="fa fa-info-circle"></i>
                                <?php echo _l('disowebs_ops_auto_testimonial_help'); ?>
                            </p>
                        </div>

                        <hr />

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> <?php echo _l('save'); ?>
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
