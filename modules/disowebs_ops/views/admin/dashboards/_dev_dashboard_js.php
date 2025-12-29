<script>
"use strict";

// Wait for jQuery to be available
(function waitForJQuery() {
    if (typeof jQuery === 'undefined') {
        setTimeout(waitForJQuery, 50);
        return;
    }
    
    var $ = jQuery;
    
    $(document).ready(function() {
        // Initialize tooltips
        if ($.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }
        
        // Initialize selectpicker
        if ($.fn.selectpicker) {
            $('.selectpicker').selectpicker();
        }
        
        // Initialize horizontal tabs scrolling (Perfex built-in)
        if (typeof init_tabs_scrollable === 'function') {
            init_tabs_scrollable();
        }
        
        // PERT Calculator
        $('#pertForm').on('submit', function(e) {
            e.preventDefault();
            var o = parseFloat($('input[name="optimistic"]').val());
            var m = parseFloat($('input[name="most_likely"]').val());
            var p = parseFloat($('input[name="pessimistic"]').val());
            
            var estimate = (o + (4 * m) + p) / 6;
            var stdDev = (p - o) / 6;
            
            $('#pertEstimate').text(estimate.toFixed(1));
            $('#pertRange').text((estimate - stdDev).toFixed(1) + ' - ' + (estimate + stdDev).toFixed(1) + ' hrs');
            $('#pertResult').removeClass('tw-hidden');
        });
        
        // Quick Note Form
        $('#quickNoteForm').on('submit', function(e) {
            e.preventDefault();
            $.post(admin_url + 'disowebs_ops/save_dev_note', $(this).serialize(), function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    location.reload();
                }
            });
        });
        
        // Requirement Form
        $('#requirementForm').on('submit', function(e) {
            e.preventDefault();
            $.post(admin_url + 'disowebs_ops/save_requirement', $(this).serialize(), function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    $('#requirementModal').modal('hide');
                    location.reload();
                }
            });
        });
        
        // Snippet Form
        $('#snippetForm').on('submit', function(e) {
            e.preventDefault();
            $.post(admin_url + 'disowebs_ops/save_snippet', $(this).serialize(), function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    $('#snippetModal').modal('hide');
                    location.reload();
                }
            });
        });
        
        // Scope Form
        $('#scopeForm').on('submit', function(e) {
            e.preventDefault();
            $.post(admin_url + 'disowebs_ops/save_scope', $(this).serialize(), function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    $('#scopeModal').modal('hide');
                    location.reload();
                } else {
                    alert_float('danger', res.message || 'Error saving scope document');
                }
            });
        });
        
        // Document Form
        $('#documentForm').on('submit', function(e) {
            e.preventDefault();
            $.post(admin_url + 'disowebs_ops/save_document', $(this).serialize(), function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    $('#documentModal').modal('hide');
                    location.reload();
                } else {
                    alert_float('danger', res.message || 'Error saving document');
                }
            });
        });
        
        // Tech Spec Form
        $('#techSpecForm').on('submit', function(e) {
            e.preventDefault();
            $.post(admin_url + 'disowebs_ops/save_tech_spec', $(this).serialize(), function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    $('#techSpecModal').modal('hide');
                    location.reload();
                } else {
                    alert_float('danger', res.message || 'Error saving spec');
                }
            });
        });
        
        // Milestone Form
        $('#milestoneForm').on('submit', function(e) {
            e.preventDefault();
            $.post(admin_url + 'disowebs_ops/save_milestone', $(this).serialize(), function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    $('#milestoneModal').modal('hide');
                    location.reload();
                } else {
                    alert_float('danger', res.message || 'Error saving milestone');
                }
            });
        });
        
        // Phase Template Form
        $('#phaseTemplateForm').on('submit', function(e) {
            e.preventDefault();
            $.post(admin_url + 'disowebs_ops/create_phases_from_template', $(this).serialize(), function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    $('#phaseTemplateModal').modal('hide');
                    alert_float('success', '<?php echo _l('disowebs_ops_phases_created'); ?>');
                    location.reload();
                } else {
                    alert_float('danger', res.message || 'Error creating phases');
                }
            });
        });
        
        // Checklist Form
        $('#checklistForm').on('submit', function(e) {
            e.preventDefault();
            $.post(admin_url + 'disowebs_ops/save_checklist', $(this).serialize(), function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    $('#checklistModal').modal('hide');
                    alert_float('success', '<?php echo _l('disowebs_ops_checklist_saved'); ?>');
                    location.reload();
                } else {
                    alert_float('danger', res.message || 'Error saving checklist');
                }
            });
        });
        
        // Document project filter
        $('#docProjectFilter').on('change', function() {
            var projectId = $(this).val();
            $('#documentsList > div').each(function() {
                if (projectId === '' || $(this).data('project') == projectId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // Tech Spec project filter
        $('#techSpecProjectFilter').on('change', function() {
            var projectId = $(this).val();
            $('#techSpecsList > div').each(function() {
                if (projectId === '' || $(this).data('project') == projectId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // =====================================================
        // SDLC TOOL FUNCTIONS - Make them global
        // =====================================================
        window.editRequirement = function(id) {
            $.get(admin_url + 'disowebs_ops/get_requirement/' + id, function(response) {
                var req = JSON.parse(response);
                if (req) {
                    $('#requirement_id').val(req.id);
                    $('#requirementForm input[name="title"]').val(req.title);
                    $('#requirementForm select[name="project_id"]').val(req.project_id).selectpicker('refresh');
                    $('#requirementForm select[name="priority"]').val(req.priority);
                    $('#requirementForm textarea[name="description"]').val(req.description);
                    $('#requirementModal .modal-title').text('<?php echo _l('edit'); ?>');
                    $('#requirementModal').modal('show');
                }
            });
        };

        window.deleteRequirement = function(id) {
            if (confirm('<?php echo _l('confirm_delete'); ?>')) {
                $.post(admin_url + 'disowebs_ops/delete_requirement/' + id, function(response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        location.reload();
                    }
                });
            }
        };

        window.editSnippet = function(id) {
            $.get(admin_url + 'disowebs_ops/get_snippet/' + id, function(response) {
                var snippet = JSON.parse(response);
                if (snippet) {
                    $('#snippet_id').val(snippet.id);
                    $('#snippetForm input[name="title"]').val(snippet.title);
                    $('#snippetForm select[name="language"]').val(snippet.language);
                    $('#snippetForm textarea[name="code"]').val(snippet.code);
                    $('#snippetForm textarea[name="description"]').val(snippet.description);
                    $('#snippetModal .modal-title').text('<?php echo _l('edit'); ?>');
                    $('#snippetModal').modal('show');
                }
            });
        };

        window.deleteSnippet = function(id) {
            if (confirm('<?php echo _l('confirm_delete'); ?>')) {
                $.post(admin_url + 'disowebs_ops/delete_snippet/' + id, function(response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        location.reload();
                    }
                });
            }
        };

        window.copySnippet = function(id) {
            $.get(admin_url + 'disowebs_ops/get_snippet/' + id, function(response) {
                var snippet = JSON.parse(response);
                if (snippet && snippet.code) {
                    navigator.clipboard.writeText(snippet.code).then(function() {
                        alert_float('success', '<?php echo _l('copied_to_clipboard'); ?>');
                    });
                }
            });
        };

        window.toggleNotePin = function(id) {
            $.post(admin_url + 'disowebs_ops/toggle_note_pin/' + id, function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    location.reload();
                }
            });
        };

        window.deleteNote = function(id) {
            if (confirm('<?php echo _l('confirm_delete'); ?>')) {
                $.post(admin_url + 'disowebs_ops/delete_dev_note/' + id, function(response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        location.reload();
                    }
                });
            }
        };

        window.useTemplate = function(templateId, templateName) {
            // Pre-select template and open modal
            $('#checklistForm input[name="name"]').val('');
            $('#checklistForm select[name="template_id"]').val(templateId);
            $('#checklistModal .modal-title').text('<?php echo _l('disowebs_ops_new_checklist'); ?> - ' + templateName);
            $('#checklistModal').modal('show');
        };

        window.viewChecklist = function(id) {
            // Open checklist view - could be a modal or redirect
            window.location.href = admin_url + 'disowebs_ops/checklist/' + id;
        };

        // =====================================================
        // DOCUMENTATION FUNCTIONS
        // =====================================================
        window.createDocFromTemplate = function(template) {
            var templates = {
                'readme': {
                    title: 'README.md',
                    content: '# Project Name\n\n## Description\nA brief description of what this project does.\n\n## Installation\n```bash\n# Installation steps\n```\n\n## Usage\n```php\n// Usage example\n```\n\n## Features\n- Feature 1\n- Feature 2\n\n## Contributing\nContribution guidelines.\n\n## License\nMIT License'
                },
                'api': {
                    title: 'API Documentation',
                    content: '# API Documentation\n\n## Base URL\n```\nhttps://api.example.com/v1\n```\n\n## Authentication\nAll requests require an API key.\n\n## Endpoints\n\n### GET /resource\nRetrieve all resources.\n\n**Response:**\n```json\n{\n  "data": [],\n  "status": "success"\n}\n```\n\n### POST /resource\nCreate a new resource.\n\n**Request Body:**\n```json\n{\n  "name": "string",\n  "value": "string"\n}\n```'
                },
                'setup': {
                    title: 'Setup Guide',
                    content: '# Setup Guide\n\n## Prerequisites\n- PHP 7.4+\n- MySQL 5.7+\n- Composer\n\n## Installation Steps\n\n### 1. Clone Repository\n```bash\ngit clone <repository-url>\ncd project-name\n```\n\n### 2. Install Dependencies\n```bash\ncomposer install\nnpm install\n```\n\n### 3. Environment Setup\n```bash\ncp .env.example .env\n# Edit .env with your settings\n```\n\n### 4. Database Setup\n```bash\nphp artisan migrate\n```\n\n## Configuration\nDescribe configuration options.'
                },
                'changelog': {
                    title: 'CHANGELOG',
                    content: '# Changelog\n\nAll notable changes to this project will be documented in this file.\n\n## [Unreleased]\n\n### Added\n- New feature\n\n### Changed\n- Updated feature\n\n### Fixed\n- Bug fix\n\n## [1.0.0] - ' + new Date().toISOString().split('T')[0] + '\n\n### Added\n- Initial release'
                }
            };
            
            if (templates[template]) {
                $('#document_template').val(template);
                $('#documentForm input[name="title"]').val(templates[template].title);
                $('#documentContent').val(templates[template].content);
                $('#documentForm select[name="doc_type"]').val(template == 'readme' ? 'readme' : template);
                $('#documentModal').modal('show');
            }
        };

        window.editDocument = function(id) {
            $.get(admin_url + 'disowebs_ops/get_document/' + id, function(response) {
                var doc = JSON.parse(response);
                if (doc) {
                    $('#document_id').val(doc.id);
                    $('#documentForm input[name="title"]').val(doc.title);
                    $('#documentForm select[name="project_id"]').val(doc.project_id).selectpicker('refresh');
                    $('#documentForm select[name="doc_type"]').val(doc.doc_type);
                    $('#documentForm select[name="status"]').val(doc.status);
                    $('#documentContent').val(doc.content);
                    $('#documentModal .modal-title').text('<?php echo _l('edit'); ?>');
                    $('#documentModal').modal('show');
                }
            });
        };

        window.viewDocument = function(id) {
            window.location.href = admin_url + 'disowebs_ops/view_document/' + id;
        };

        window.deleteDocument = function(id) {
            if (confirm('<?php echo _l('confirm_delete'); ?>')) {
                $.post(admin_url + 'disowebs_ops/delete_document/' + id, function(response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        location.reload();
                    }
                });
            }
        };

        window.duplicateDocument = function(id) {
            $.post(admin_url + 'disowebs_ops/duplicate_document/' + id, function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    location.reload();
                }
            });
        };

        window.exportDocument = function(id) {
            window.open(admin_url + 'disowebs_ops/export_document/' + id, '_blank');
        };

        // =====================================================
        // TECHNICAL SPECS FUNCTIONS
        // =====================================================
        window.editTechSpec = function(id) {
            $.get(admin_url + 'disowebs_ops/get_tech_spec/' + id, function(response) {
                var spec = JSON.parse(response);
                if (spec) {
                    $('#techspec_id').val(spec.id);
                    $('#techSpecForm input[name="title"]').val(spec.title);
                    $('#techSpecForm input[name="version"]').val(spec.version);
                    $('#techSpecForm select[name="project_id"]').val(spec.project_id).selectpicker('refresh');
                    $('#techSpecForm select[name="type"]').val(spec.type);
                    $('#techSpecForm select[name="status"]').val(spec.status);
                    $('#techSpecContent').val(spec.content);
                    $('#techSpecModal .modal-title').text('<?php echo _l('edit'); ?>');
                    $('#techSpecModal').modal('show');
                }
            });
        };

        window.viewTechSpec = function(id) {
            window.location.href = admin_url + 'disowebs_ops/view_tech_spec/' + id;
        };

        window.deleteTechSpec = function(id) {
            if (confirm('<?php echo _l('confirm_delete'); ?>')) {
                $.post(admin_url + 'disowebs_ops/delete_tech_spec/' + id, function(response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        location.reload();
                    }
                });
            }
        };

        window.exportTechSpec = function(id) {
            window.open(admin_url + 'disowebs_ops/export_tech_spec/' + id, '_blank');
        };

        window.filterSpecs = function(type) {
            $('.spec-category').removeClass('tw-bg-primary tw-bg-opacity-10');
            $('.spec-category[data-type="' + type + '"]').addClass('tw-bg-primary tw-bg-opacity-10');
            
            if (type === 'all') {
                $('.tech-spec-item').show();
            } else {
                $('.tech-spec-item').each(function() {
                    if ($(this).data('type') === type) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        };

        // =====================================================
        // PROJECT PLANNING FUNCTIONS
        // =====================================================
        window.loadProjectPhases = function(projectId) {
            if (!projectId) {
                $('#milestonePhaseSelect').html('<option value=""><?php echo _l('disowebs_ops_no_phase'); ?></option>');
                return;
            }
            
            $.get(admin_url + 'disowebs_ops/get_project_phases/' + projectId, function(response) {
                var phases = JSON.parse(response);
                var html = '<option value=""><?php echo _l('disowebs_ops_no_phase'); ?></option>';
                if (phases && phases.length > 0) {
                    phases.forEach(function(phase) {
                        html += '<option value="' + phase.id + '">' + phase.name + '</option>';
                    });
                }
                $('#milestonePhaseSelect').html(html);
            });
        };

    }); // End document.ready
})(); // End waitForJQuery
</script>
