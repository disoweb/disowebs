<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
<style>
.snippet-card { transition: all 0.2s; cursor: pointer; }
.snippet-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.snippet-code { max-height: 200px; overflow: hidden; position: relative; }
.snippet-code::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 50px;
    background: linear-gradient(transparent, #282c34);
}
pre code { font-size: 12px !important; line-height: 1.4 !important; }
.copy-btn { position: absolute; top: 10px; right: 10px; z-index: 10; }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php $this->load->view('admin/includes/alerts'); ?>

            <!-- Header -->
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                    <h4 class="tw-font-bold tw-text-xl tw-mb-0">
                        <i class="fa fa-code tw-mr-2"></i>
                        <?php echo _l('disowebs_ops_code_snippets'); ?>
                    </h4>
                    <a href="<?php echo admin_url('disowebs_ops/sdlc/snippet'); ?>" class="btn btn-primary">
                        <i class="fa fa-plus"></i> <?php echo _l('add_new'); ?>
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <form method="get" class="form-inline">
                            <div class="form-group tw-mr-3">
                                <input type="text" name="search" class="form-control" placeholder="<?php echo _l('search'); ?>..." 
                                       value="<?php echo e($filters['search'] ?? ''); ?>">
                            </div>
                            <div class="form-group tw-mr-3">
                                <select name="category" class="form-control selectpicker" data-live-search="true" data-width="200px">
                                    <option value=""><?php echo _l('all_categories'); ?></option>
                                    <?php foreach ($categories as $cat) { ?>
                                    <option value="<?php echo e($cat->category); ?>" <?php echo ($filters['category'] ?? '') == $cat->category ? 'selected' : ''; ?>>
                                        <?php echo e($cat->category); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group tw-mr-3">
                                <select name="language" class="form-control selectpicker" data-live-search="true" data-width="150px">
                                    <option value=""><?php echo _l('all_languages'); ?></option>
                                    <?php foreach ($languages as $lang) { ?>
                                    <option value="<?php echo e($lang->language); ?>" <?php echo ($filters['language'] ?? '') == $lang->language ? 'selected' : ''; ?>>
                                        <?php echo e($lang->language); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-default">
                                <i class="fa fa-filter"></i> <?php echo _l('filter'); ?>
                            </button>
                            <?php if (!empty($filters['search']) || !empty($filters['category']) || !empty($filters['language'])) { ?>
                            <a href="<?php echo admin_url('disowebs_ops/sdlc/snippets'); ?>" class="btn btn-default tw-ml-2">
                                <i class="fa fa-times"></i> <?php echo _l('clear'); ?>
                            </a>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Snippets Grid -->
            <?php if (!empty($snippets)) { ?>
            <?php foreach ($snippets as $snippet) { ?>
            <div class="col-md-6 col-lg-4">
                <div class="panel_s snippet-card" data-snippet-id="<?php echo $snippet->id; ?>">
                    <div class="panel-heading">
                        <div class="tw-flex tw-justify-between tw-items-start">
                            <div>
                                <h5 class="panel-title tw-mb-1"><?php echo e($snippet->title); ?></h5>
                                <div>
                                    <span class="label label-default"><?php echo e($snippet->language); ?></span>
                                    <span class="label label-info"><?php echo e($snippet->category); ?></span>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="#" onclick="copySnippet(<?php echo $snippet->id; ?>); return false;">
                                            <i class="fa fa-copy"></i> <?php echo _l('copy'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo admin_url('disowebs_ops/sdlc/snippet/' . $snippet->id); ?>">
                                            <i class="fa fa-edit"></i> <?php echo _l('edit'); ?>
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="#" onclick="deleteSnippet(<?php echo $snippet->id; ?>); return false;" class="text-danger">
                                            <i class="fa fa-trash"></i> <?php echo _l('delete'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body tw-p-0">
                        <?php if ($snippet->description) { ?>
                        <p class="tw-text-sm tw-text-neutral-500 tw-px-4 tw-pt-3 tw-mb-0"><?php echo e($snippet->description); ?></p>
                        <?php } ?>
                        <div class="snippet-code tw-relative">
                            <pre class="tw-mb-0 tw-rounded-none"><code class="<?php echo e($snippet->language); ?>"><?php echo e($snippet->code); ?></code></pre>
                        </div>
                    </div>
                    <div class="panel-footer tw-flex tw-justify-between tw-items-center">
                        <div>
                            <?php if ($snippet->tags) { ?>
                            <small class="tw-text-neutral-400">
                                <?php 
                                $tags = explode(',', $snippet->tags);
                                foreach (array_slice($tags, 0, 3) as $tag) {
                                    echo '<span class="tw-mr-1">#' . trim($tag) . '</span>';
                                }
                                if (count($tags) > 3) echo '...';
                                ?>
                            </small>
                            <?php } ?>
                        </div>
                        <div>
                            <span class="tw-text-neutral-400 tw-text-xs">
                                <i class="fa fa-copy"></i> <?php echo $snippet->usage_count; ?> uses
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php } else { ?>
            <div class="col-md-12">
                <div class="tw-text-center tw-py-12">
                    <i class="fa fa-code fa-3x tw-text-neutral-300 tw-mb-4"></i>
                    <p class="tw-text-neutral-500"><?php echo _l('disowebs_ops_no_snippets_found'); ?></p>
                    <a href="<?php echo admin_url('disowebs_ops/sdlc/snippet'); ?>" class="btn btn-primary">
                        <i class="fa fa-plus"></i> <?php echo _l('disowebs_ops_add_first_snippet'); ?>
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Copy Modal -->
<div class="modal fade" id="snippetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="snippetModalTitle">Code Snippet</h4>
            </div>
            <div class="modal-body">
                <div class="tw-relative">
                    <button type="button" class="btn btn-success btn-sm copy-btn" onclick="copyToClipboard()">
                        <i class="fa fa-copy"></i> <?php echo _l('copy'); ?>
                    </button>
                    <pre><code id="snippetModalCode" class=""></code></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
hljs.highlightAll();

var currentCode = '';

function copySnippet(id) {
    $.get(admin_url + 'disowebs_ops/sdlc/get_snippet/' + id, function(response) {
        var snippet = JSON.parse(response);
        if (snippet) {
            currentCode = snippet.code;
            $('#snippetModalTitle').text(snippet.title);
            $('#snippetModalCode').text(snippet.code).attr('class', snippet.language);
            hljs.highlightElement(document.getElementById('snippetModalCode'));
            $('#snippetModal').modal('show');
        }
    });
}

function copyToClipboard() {
    navigator.clipboard.writeText(currentCode).then(function() {
        alert_float('success', '<?php echo _l('copied_to_clipboard'); ?>');
    });
}

function deleteSnippet(id) {
    if (confirm('<?php echo _l('confirm_delete'); ?>')) {
        $.post(admin_url + 'disowebs_ops/sdlc/delete_snippet/' + id, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                location.reload();
            } else {
                alert_float('danger', '<?php echo _l('error'); ?>');
            }
        });
    }
}

// Click card to open snippet
$('.snippet-card').on('click', function(e) {
    if ($(e.target).closest('.dropdown, .dropdown-menu, a, button').length === 0) {
        copySnippet($(this).data('snippet-id'));
    }
});
</script>
