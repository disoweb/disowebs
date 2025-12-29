<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- SDLC Requirements Tab Content -->
<div role="tabpanel" class="tab-pane" id="tab_sdlc_requirements">
    <div class="sdlc-section">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-mb-4">
                    <i class="fa fa-list-check tw-mr-2"></i><?= _l('dw_project_requirements'); ?>
                </h4>
                <p class="text-muted tw-mb-4"><?= _l('dw_requirements_help'); ?></p>
                
                <div id="requirements-container">
                    <div class="requirement-item tw-mb-3 tw-p-4 tw-bg-neutral-50 tw-rounded-lg" data-index="0">
                        <div class="row">
                            <div class="col-md-4">
                                <?= render_input('sdlc_requirements[0][title]', 'dw_requirement_title', '', 'text', ['placeholder' => _l('dw_requirement_title_placeholder')]); ?>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_priority'); ?></label>
                                    <select name="sdlc_requirements[0][priority]" class="form-control selectpicker" data-width="100%">
                                        <option value="low"><?= _l('dw_priority_low'); ?></option>
                                        <option value="medium" selected><?= _l('dw_priority_medium'); ?></option>
                                        <option value="high"><?= _l('dw_priority_high'); ?></option>
                                        <option value="critical"><?= _l('dw_priority_critical'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_category'); ?></label>
                                    <select name="sdlc_requirements[0][category]" class="form-control selectpicker" data-width="100%">
                                        <option value="functional"><?= _l('dw_functional'); ?></option>
                                        <option value="non_functional"><?= _l('dw_non_functional'); ?></option>
                                        <option value="technical"><?= _l('dw_technical'); ?></option>
                                        <option value="business"><?= _l('dw_business'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-requirement tw-mt-6" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_description'); ?></label>
                                    <textarea name="sdlc_requirements[0][description]" class="form-control" rows="2" placeholder="<?= _l('dw_requirement_description_placeholder'); ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-success btn-sm" id="add-requirement">
                    <i class="fa fa-plus tw-mr-1"></i> <?= _l('dw_add_requirement'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SDLC Scope Tab Content -->
<div role="tabpanel" class="tab-pane" id="tab_sdlc_scope">
    <div class="sdlc-section">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-mb-4">
                    <i class="fa fa-clipboard tw-mr-2"></i><?= _l('dw_scope_documents'); ?>
                </h4>
                <p class="text-muted tw-mb-4"><?= _l('dw_scope_help'); ?></p>
                
                <div id="scope-container">
                    <div class="scope-item tw-mb-3 tw-p-4 tw-bg-neutral-50 tw-rounded-lg" data-index="0">
                        <div class="row">
                            <div class="col-md-8">
                                <?= render_input('sdlc_scope[0][title]', 'dw_scope_title', '', 'text', ['placeholder' => _l('dw_scope_title_placeholder')]); ?>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_scope_type'); ?></label>
                                    <select name="sdlc_scope[0][type]" class="form-control selectpicker" data-width="100%">
                                        <option value="in_scope"><?= _l('dw_in_scope'); ?></option>
                                        <option value="out_scope"><?= _l('dw_out_scope'); ?></option>
                                        <option value="assumption"><?= _l('dw_assumption'); ?></option>
                                        <option value="constraint"><?= _l('dw_constraint'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-scope tw-mt-6" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_description'); ?></label>
                                    <textarea name="sdlc_scope[0][content]" class="form-control" rows="3" placeholder="<?= _l('dw_scope_content_placeholder'); ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-success btn-sm" id="add-scope">
                    <i class="fa fa-plus tw-mr-1"></i> <?= _l('dw_add_scope_item'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Terminal Commands Tab Content -->
<div role="tabpanel" class="tab-pane" id="tab_sdlc_commands">
    <div class="sdlc-section">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-mb-4">
                    <i class="fa fa-terminal tw-mr-2"></i><?= _l('dw_terminal_commands'); ?>
                </h4>
                <p class="text-muted tw-mb-4"><?= _l('dw_terminal_commands_help'); ?></p>
                
                <div id="commands-container">
                    <div class="command-item tw-mb-3 tw-p-4 tw-bg-neutral-50 tw-rounded-lg" data-index="0">
                        <div class="row">
                            <div class="col-md-4">
                                <?= render_input('sdlc_commands[0][title]', 'dw_command_title', '', 'text', ['placeholder' => _l('dw_command_title_placeholder')]); ?>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_platform'); ?></label>
                                    <select name="sdlc_commands[0][platform]" class="form-control selectpicker" data-width="100%">
                                        <option value="powershell">PowerShell</option>
                                        <option value="ubuntu">Ubuntu/Linux</option>
                                        <option value="macos">macOS</option>
                                        <option value="docker">Docker</option>
                                        <option value="git">Git</option>
                                        <option value="npm">NPM</option>
                                        <option value="composer">Composer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_category'); ?></label>
                                    <select name="sdlc_commands[0][category]" class="form-control selectpicker" data-width="100%">
                                        <option value="setup"><?= _l('dw_setup'); ?></option>
                                        <option value="build"><?= _l('dw_build'); ?></option>
                                        <option value="deploy"><?= _l('dw_deploy'); ?></option>
                                        <option value="test"><?= _l('dw_test'); ?></option>
                                        <option value="utility"><?= _l('dw_utility'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-command tw-mt-6" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_command'); ?></label>
                                    <textarea name="sdlc_commands[0][command]" class="form-control tw-font-mono tw-text-sm" rows="3" placeholder="<?= _l('dw_command_placeholder'); ?>"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_notes'); ?></label>
                                    <input type="text" name="sdlc_commands[0][notes]" class="form-control" placeholder="<?= _l('dw_command_notes_placeholder'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-success btn-sm" id="add-command">
                    <i class="fa fa-plus tw-mr-1"></i> <?= _l('dw_add_command'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- AI Prompts Tab Content -->
<div role="tabpanel" class="tab-pane" id="tab_sdlc_prompts">
    <div class="sdlc-section">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-mb-4">
                    <i class="fa fa-robot tw-mr-2"></i><?= _l('dw_ai_prompts'); ?>
                </h4>
                <p class="text-muted tw-mb-4"><?= _l('dw_ai_prompts_help'); ?></p>
                
                <div id="prompts-container">
                    <div class="prompt-item tw-mb-3 tw-p-4 tw-bg-neutral-50 tw-rounded-lg" data-index="0">
                        <div class="row">
                            <div class="col-md-6">
                                <?= render_input('sdlc_prompts[0][title]', 'dw_prompt_title', '', 'text', ['placeholder' => _l('dw_prompt_title_placeholder')]); ?>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_category'); ?></label>
                                    <select name="sdlc_prompts[0][category]" class="form-control selectpicker" data-width="100%">
                                        <option value="code_generation"><?= _l('dw_code_generation'); ?></option>
                                        <option value="debugging"><?= _l('dw_debugging'); ?></option>
                                        <option value="documentation"><?= _l('dw_documentation'); ?></option>
                                        <option value="testing"><?= _l('dw_testing'); ?></option>
                                        <option value="review"><?= _l('dw_review'); ?></option>
                                        <option value="planning"><?= _l('dw_planning'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_ai_model'); ?></label>
                                    <select name="sdlc_prompts[0][model]" class="form-control selectpicker" data-width="100%">
                                        <option value="claude">Claude</option>
                                        <option value="gpt4">GPT-4</option>
                                        <option value="copilot">Copilot</option>
                                        <option value="gemini">Gemini</option>
                                        <option value="any">Any</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-prompt tw-mt-6" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_prompt_content'); ?></label>
                                    <textarea name="sdlc_prompts[0][content]" class="form-control tw-font-mono" rows="5" placeholder="<?= _l('dw_prompt_content_placeholder'); ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-success btn-sm" id="add-prompt">
                    <i class="fa fa-plus tw-mr-1"></i> <?= _l('dw_add_prompt'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Checklists Tab Content -->
<div role="tabpanel" class="tab-pane" id="tab_sdlc_checklists">
    <div class="sdlc-section">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-mb-4">
                    <i class="fa fa-tasks tw-mr-2"></i><?= _l('dw_checklists'); ?>
                </h4>
                <p class="text-muted tw-mb-4"><?= _l('dw_checklists_help'); ?></p>
                
                <div id="checklists-container">
                    <div class="checklist-item tw-mb-3 tw-p-4 tw-bg-neutral-50 tw-rounded-lg" data-index="0">
                        <div class="row">
                            <div class="col-md-6">
                                <?= render_input('sdlc_checklists[0][title]', 'dw_checklist_title', '', 'text', ['placeholder' => _l('dw_checklist_title_placeholder')]); ?>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_phase'); ?></label>
                                    <select name="sdlc_checklists[0][phase]" class="form-control selectpicker" data-width="100%">
                                        <option value="planning"><?= _l('dw_phase_planning'); ?></option>
                                        <option value="design"><?= _l('dw_phase_design'); ?></option>
                                        <option value="development"><?= _l('dw_phase_development'); ?></option>
                                        <option value="testing"><?= _l('dw_phase_testing'); ?></option>
                                        <option value="deployment"><?= _l('dw_phase_deployment'); ?></option>
                                        <option value="review"><?= _l('dw_phase_review'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_is_template'); ?></label>
                                    <div class="checkbox tw-mt-2">
                                        <input type="checkbox" name="sdlc_checklists[0][is_template]" value="1" id="checklist_template_0">
                                        <label for="checklist_template_0"><?= _l('dw_yes'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-checklist tw-mt-6" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_checklist_items'); ?> <small class="text-muted">(<?= _l('dw_one_per_line'); ?>)</small></label>
                                    <textarea name="sdlc_checklists[0][items]" class="form-control" rows="4" placeholder="<?= _l('dw_checklist_items_placeholder'); ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-success btn-sm" id="add-checklist">
                    <i class="fa fa-plus tw-mr-1"></i> <?= _l('dw_add_checklist'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Dev Notes Tab Content -->
<div role="tabpanel" class="tab-pane" id="tab_sdlc_notes">
    <div class="sdlc-section">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-mb-4">
                    <i class="fa fa-sticky-note tw-mr-2"></i><?= _l('dw_dev_notes'); ?>
                </h4>
                <p class="text-muted tw-mb-4"><?= _l('dw_dev_notes_help'); ?></p>
                
                <div id="notes-container">
                    <div class="note-item tw-mb-3 tw-p-4 tw-bg-neutral-50 tw-rounded-lg" data-index="0">
                        <div class="row">
                            <div class="col-md-6">
                                <?= render_input('sdlc_notes[0][title]', 'dw_note_title', '', 'text', ['placeholder' => _l('dw_note_title_placeholder')]); ?>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_category'); ?></label>
                                    <select name="sdlc_notes[0][category]" class="form-control selectpicker" data-width="100%">
                                        <option value="general"><?= _l('dw_general'); ?></option>
                                        <option value="bug"><?= _l('dw_bug'); ?></option>
                                        <option value="todo"><?= _l('dw_todo'); ?></option>
                                        <option value="idea"><?= _l('dw_idea'); ?></option>
                                        <option value="warning"><?= _l('dw_warning'); ?></option>
                                        <option value="important"><?= _l('dw_important'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_pinned'); ?></label>
                                    <div class="checkbox tw-mt-2">
                                        <input type="checkbox" name="sdlc_notes[0][is_pinned]" value="1" id="note_pinned_0">
                                        <label for="note_pinned_0"><?= _l('dw_yes'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-note tw-mt-6" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_content'); ?></label>
                                    <textarea name="sdlc_notes[0][content]" class="form-control tinymce sdlc-note-editor" rows="4" placeholder="<?= _l('dw_note_content_placeholder'); ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-success btn-sm" id="add-note">
                    <i class="fa fa-plus tw-mr-1"></i> <?= _l('dw_add_note'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Documentation Tab Content -->
<div role="tabpanel" class="tab-pane" id="tab_sdlc_docs">
    <div class="sdlc-section">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-mb-4">
                    <i class="fa fa-book tw-mr-2"></i><?= _l('dw_documentation'); ?>
                </h4>
                <p class="text-muted tw-mb-4"><?= _l('dw_documentation_help'); ?></p>
                
                <div id="docs-container">
                    <div class="doc-item tw-mb-3 tw-p-4 tw-bg-neutral-50 tw-rounded-lg" data-index="0">
                        <div class="row">
                            <div class="col-md-5">
                                <?= render_input('sdlc_docs[0][title]', 'dw_doc_title', '', 'text', ['placeholder' => _l('dw_doc_title_placeholder')]); ?>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_doc_type'); ?></label>
                                    <select name="sdlc_docs[0][type]" class="form-control selectpicker" data-width="100%">
                                        <option value="readme"><?= _l('dw_readme'); ?></option>
                                        <option value="api"><?= _l('dw_api_doc'); ?></option>
                                        <option value="user_guide"><?= _l('dw_user_guide'); ?></option>
                                        <option value="installation"><?= _l('dw_installation'); ?></option>
                                        <option value="changelog"><?= _l('dw_changelog'); ?></option>
                                        <option value="other"><?= _l('dw_other'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_version'); ?></label>
                                    <input type="text" name="sdlc_docs[0][version]" class="form-control" placeholder="1.0.0">
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-doc tw-mt-6" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_content'); ?> <small class="text-muted">(Markdown <?= _l('dw_supported'); ?>)</small></label>
                                    <textarea name="sdlc_docs[0][content]" class="form-control tw-font-mono" rows="6" placeholder="<?= _l('dw_doc_content_placeholder'); ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-success btn-sm" id="add-doc">
                    <i class="fa fa-plus tw-mr-1"></i> <?= _l('dw_add_documentation'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Technical Specs Tab Content -->
<div role="tabpanel" class="tab-pane" id="tab_sdlc_specs">
    <div class="sdlc-section">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-mb-4">
                    <i class="fa fa-cogs tw-mr-2"></i><?= _l('dw_technical_specs'); ?>
                </h4>
                <p class="text-muted tw-mb-4"><?= _l('dw_technical_specs_help'); ?></p>
                
                <div id="specs-container">
                    <div class="spec-item tw-mb-3 tw-p-4 tw-bg-neutral-50 tw-rounded-lg" data-index="0">
                        <div class="row">
                            <div class="col-md-5">
                                <?= render_input('sdlc_specs[0][title]', 'dw_spec_title', '', 'text', ['placeholder' => _l('dw_spec_title_placeholder')]); ?>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_spec_category'); ?></label>
                                    <select name="sdlc_specs[0][category]" class="form-control selectpicker" data-width="100%">
                                        <option value="architecture"><?= _l('dw_architecture'); ?></option>
                                        <option value="database"><?= _l('dw_database'); ?></option>
                                        <option value="api"><?= _l('dw_api'); ?></option>
                                        <option value="security"><?= _l('dw_security'); ?></option>
                                        <option value="performance"><?= _l('dw_performance'); ?></option>
                                        <option value="infrastructure"><?= _l('dw_infrastructure'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_status'); ?></label>
                                    <select name="sdlc_specs[0][status]" class="form-control selectpicker" data-width="100%">
                                        <option value="draft"><?= _l('dw_draft'); ?></option>
                                        <option value="review"><?= _l('dw_in_review'); ?></option>
                                        <option value="approved"><?= _l('dw_approved'); ?></option>
                                        <option value="implemented"><?= _l('dw_implemented'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-spec tw-mt-6" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><?= _l('dw_specification'); ?></label>
                                    <textarea name="sdlc_specs[0][content]" class="form-control tw-font-mono" rows="6" placeholder="<?= _l('dw_spec_content_placeholder'); ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-success btn-sm" id="add-spec">
                    <i class="fa fa-plus tw-mr-1"></i> <?= _l('dw_add_tech_spec'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Planning Tab Content -->
<div role="tabpanel" class="tab-pane" id="tab_sdlc_planning">
    <div class="sdlc-section">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-mb-4">
                    <i class="fa fa-project-diagram tw-mr-2"></i><?= _l('dw_planning'); ?>
                </h4>
                <p class="text-muted tw-mb-4"><?= _l('dw_planning_help'); ?></p>
                
                <!-- Time Estimates Section -->
                <div class="tw-mb-6">
                    <h5 class="tw-font-medium tw-mb-3"><?= _l('dw_time_estimates'); ?></h5>
                    <div id="estimates-container">
                        <div class="estimate-item tw-mb-3 tw-p-4 tw-bg-neutral-50 tw-rounded-lg" data-index="0">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= render_input('sdlc_estimates[0][phase]', 'dw_phase_name', '', 'text', ['placeholder' => _l('dw_phase_name_placeholder')]); ?>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><?= _l('dw_estimated_hours'); ?></label>
                                        <input type="number" name="sdlc_estimates[0][estimated_hours]" class="form-control" min="0" step="0.5" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label"><?= _l('dw_start_date'); ?></label>
                                        <input type="text" name="sdlc_estimates[0][start_date]" class="form-control datepicker" placeholder="<?= _l('dw_select_date'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><?= _l('dw_end_date'); ?></label>
                                        <input type="text" name="sdlc_estimates[0][end_date]" class="form-control datepicker" placeholder="<?= _l('dw_select_date'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-1 text-right">
                                    <button type="button" class="btn btn-danger btn-sm remove-estimate tw-mt-6" style="display:none;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label"><?= _l('dw_notes'); ?></label>
                                        <input type="text" name="sdlc_estimates[0][notes]" class="form-control" placeholder="<?= _l('dw_estimate_notes_placeholder'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-success btn-sm" id="add-estimate">
                        <i class="fa fa-plus tw-mr-1"></i> <?= _l('dw_add_phase'); ?>
                    </button>
                </div>
                
                <hr class="tw-my-4">
                
                <!-- User Stories Section -->
                <div class="tw-mb-6">
                    <h5 class="tw-font-medium tw-mb-3"><?= _l('dw_user_stories'); ?></h5>
                    <div id="stories-container">
                        <div class="story-item tw-mb-3 tw-p-4 tw-bg-neutral-50 tw-rounded-lg" data-index="0">
                            <div class="row">
                                <div class="col-md-8">
                                    <?= render_input('sdlc_stories[0][title]', 'dw_story_title', '', 'text', ['placeholder' => _l('dw_story_title_placeholder')]); ?>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label"><?= _l('dw_priority'); ?></label>
                                        <select name="sdlc_stories[0][priority]" class="form-control selectpicker" data-width="100%">
                                            <option value="low"><?= _l('dw_priority_low'); ?></option>
                                            <option value="medium" selected><?= _l('dw_priority_medium'); ?></option>
                                            <option value="high"><?= _l('dw_priority_high'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1 text-right">
                                    <button type="button" class="btn btn-danger btn-sm remove-story tw-mt-6" style="display:none;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label"><?= _l('dw_user_story_format'); ?></label>
                                        <textarea name="sdlc_stories[0][content]" class="form-control" rows="3" placeholder="<?= _l('dw_story_placeholder'); ?>"></textarea>
                                        <small class="text-muted"><?= _l('dw_story_format_help'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label"><?= _l('dw_acceptance_criteria'); ?> <small class="text-muted">(<?= _l('dw_one_per_line'); ?>)</small></label>
                                        <textarea name="sdlc_stories[0][acceptance_criteria]" class="form-control" rows="3" placeholder="<?= _l('dw_acceptance_criteria_placeholder'); ?>"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-success btn-sm" id="add-story">
                        <i class="fa fa-plus tw-mr-1"></i> <?= _l('dw_add_user_story'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SDLC Form JavaScript -->
<?php ob_start(); ?>
<script>
(function($) {
    'use strict';
    $(document).ready(function() {
        // Initialize datepickers
        if (typeof $.fn.datepicker !== 'undefined') {
            $('.datepicker').datepicker({
                format: '<?= get_option('dateformat'); ?>',
                autoclose: true
            });
        }
        
        // Initialize TinyMCE editors in notes section
        if (typeof init_editor === 'function') {
            init_editor('.sdlc-note-editor', {height: 200});
        }
        
        // Helper function to generate unique ID
        function generateUniqueId() {
            return 'sdlc_editor_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }
        
        // Dynamic item management
        var sdlcSections = [
        { container: '#requirements-container', add: '#add-requirement', remove: '.remove-requirement', class: 'requirement-item', prefix: 'sdlc_requirements', hasTinymce: false },
        { container: '#scope-container', add: '#add-scope', remove: '.remove-scope', class: 'scope-item', prefix: 'sdlc_scope', hasTinymce: false },
        { container: '#commands-container', add: '#add-command', remove: '.remove-command', class: 'command-item', prefix: 'sdlc_commands', hasTinymce: false },
        { container: '#prompts-container', add: '#add-prompt', remove: '.remove-prompt', class: 'prompt-item', prefix: 'sdlc_prompts', hasTinymce: false },
        { container: '#checklists-container', add: '#add-checklist', remove: '.remove-checklist', class: 'checklist-item', prefix: 'sdlc_checklists', hasTinymce: false },
        { container: '#notes-container', add: '#add-note', remove: '.remove-note', class: 'note-item', prefix: 'sdlc_notes', hasTinymce: true },
        { container: '#docs-container', add: '#add-doc', remove: '.remove-doc', class: 'doc-item', prefix: 'sdlc_docs', hasTinymce: false },
        { container: '#specs-container', add: '#add-spec', remove: '.remove-spec', class: 'spec-item', prefix: 'sdlc_specs', hasTinymce: false },
        { container: '#estimates-container', add: '#add-estimate', remove: '.remove-estimate', class: 'estimate-item', prefix: 'sdlc_estimates', hasTinymce: false },
        { container: '#stories-container', add: '#add-story', remove: '.remove-story', class: 'story-item', prefix: 'sdlc_stories', hasTinymce: false }
    ];
    
    sdlcSections.forEach(function(section) {
        // Add item
        $(section.add).on('click', function() {
            var $container = $(section.container);
            var $items = $container.find('.' + section.class);
            var newIndex = $items.length;
            
            // For TinyMCE sections, we need to handle the editor specially
            var $firstItem = $items.first();
            var $template;
            
            if (section.hasTinymce) {
                // Remove TinyMCE instance before cloning
                var $editor = $firstItem.find('.sdlc-note-editor');
                var editorId = $editor.attr('id');
                if (editorId && typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                    tinymce.get(editorId).save(); // Save content to textarea
                }
                
                // Clone and process
                $template = $firstItem.clone();
                
                // Remove TinyMCE wrapper elements from clone
                $template.find('.tox-tinymce').remove();
                $template.find('.mce-tinymce').remove();
                
                // Get the textarea and reset it
                var $newEditor = $template.find('.sdlc-note-editor');
                var newEditorId = generateUniqueId();
                $newEditor.attr('id', newEditorId).val('').show().css('display', '');
            } else {
                $template = $firstItem.clone();
            }
            
            // Update indices in names
            $template.attr('data-index', newIndex);
            $template.find('[name]').each(function() {
                var name = $(this).attr('name');
                $(this).attr('name', name.replace(/\[\d+\]/, '[' + newIndex + ']'));
            });
            
            // Update IDs (except TinyMCE editors which we already handled)
            $template.find('[id]').not('.sdlc-note-editor').each(function() {
                var id = $(this).attr('id');
                $(this).attr('id', id.replace(/_\d+$/, '_' + newIndex));
            });
            $template.find('[for]').each(function() {
                var forAttr = $(this).attr('for');
                $(this).attr('for', forAttr.replace(/_\d+$/, '_' + newIndex));
            });
            
            // Clear values
            $template.find('input[type="text"], input[type="number"], textarea').not('.sdlc-note-editor').val('');
            $template.find('input[type="checkbox"]').prop('checked', false);
            $template.find('select').val($template.find('select option:first').val());
            
            // Show remove button
            $template.find(section.remove).show();
            
            // Append and reinitialize
            $container.append($template);
            if (typeof $.fn.selectpicker !== 'undefined') {
                $template.find('.selectpicker').selectpicker();
            }
            if (typeof $.fn.datepicker !== 'undefined') {
                $template.find('.datepicker').datepicker({
                    format: '<?= get_option('dateformat'); ?>',
                    autoclose: true
                });
            }
            
            // Initialize TinyMCE for new editor
            if (section.hasTinymce && typeof init_editor === 'function') {
                var $newTinyMce = $template.find('.sdlc-note-editor');
                if ($newTinyMce.length) {
                    init_editor('#' + $newTinyMce.attr('id'), {height: 200});
                }
            }
            
            updateRemoveButtons(section);
        });
        
        // Remove item
        $(document).on('click', section.remove, function() {
            var $container = $(section.container);
            if ($container.find('.' + section.class).length > 1) {
                var $item = $(this).closest('.' + section.class);
                
                // Remove TinyMCE instance if exists
                if (section.hasTinymce) {
                    var $editor = $item.find('.sdlc-note-editor');
                    var editorId = $editor.attr('id');
                    if (editorId && typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                        tinymce.get(editorId).remove();
                    }
                }
                
                $item.remove();
                updateRemoveButtons(section);
            }
        });
        
        function updateRemoveButtons(section) {
            var $container = $(section.container);
            var $items = $container.find('.' + section.class);
            if ($items.length > 1) {
                $items.find(section.remove).show();
            } else {
                $items.find(section.remove).hide();
            }
        }
    });
    });
})(jQuery);
</script>
<?php $sdlc_js = ob_get_clean(); ?>
<?php hooks()->add_action('app_admin_footer', function() use ($sdlc_js) { echo $sdlc_js; }); ?>
