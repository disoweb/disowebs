<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$project_name = $project->name ?? '';
$client_name = '';
if ($client && isset($client->company)) {
    $client_name = $client->company;
}

$image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo e(_l('disowebs_ops_case_study_title')); ?> - <?php echo e($project_name); ?></title>
    <style>
        body {
            font-family: "Georgia", "Times New Roman", serif;
            margin: 40px;
            color: #1f2933;
            line-height: 1.6;
        }
        h1, h2, h3 {
            font-family: "Helvetica Neue", Arial, sans-serif;
            margin: 0 0 12px 0;
        }
        h1 {
            font-size: 32px;
        }
        h2 {
            font-size: 22px;
            margin-top: 32px;
        }
        h3 {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #52606d;
        }
        .muted {
            color: #52606d;
        }
        .meta {
            margin: 10px 0 24px 0;
            padding: 12px 16px;
            background: #f5f7fa;
            border-radius: 8px;
        }
        .proof-entry {
            margin-top: 24px;
            padding: 16px 18px;
            border: 1px solid #e4e7eb;
            border-radius: 8px;
        }
        .proof-entry + .proof-entry {
            margin-top: 16px;
        }
        .section {
            margin-top: 16px;
        }
        .files {
            margin-top: 12px;
            padding-left: 16px;
        }
        .files li {
            margin-bottom: 6px;
        }
        .image-preview {
            margin-top: 12px;
        }
        .image-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 6px;
            border: 1px solid #e4e7eb;
        }
    </style>
</head>
<body>
    <h1><?php echo e(_l('disowebs_ops_case_study_title')); ?></h1>
    <p class="muted"><?php echo e(_l('disowebs_ops_case_study_subtitle')); ?></p>

    <div class="meta">
        <div><strong><?php echo e(_l('disowebs_ops_case_study_project')); ?></strong> <?php echo e($project_name); ?></div>
        <?php if ($client_name !== '') { ?>
        <div><strong><?php echo e(_l('disowebs_ops_case_study_client')); ?></strong> <?php echo e($client_name); ?></div>
        <?php } ?>
        <div><strong><?php echo e(_l('disowebs_ops_case_study_date')); ?></strong> <?php echo e(_d(date('Y-m-d'))); ?></div>
    </div>

    <h2><?php echo e(_l('disowebs_ops_case_study_proof_heading')); ?></h2>

    <?php if (empty($proof_entries)) { ?>
    <p class="muted"><?php echo e(_l('disowebs_ops_case_study_no_proof')); ?></p>
    <?php } ?>

    <?php foreach ($proof_entries as $entry) { ?>
    <?php $entry_files = $files_by_entry[$entry['id']] ?? []; ?>
    <div class="proof-entry">
        <h3><?php echo e($entry['title']); ?></h3>
        <div class="muted"><?php echo e(_l('disowebs_ops_proof_created', _dt($entry['created_at']))); ?></div>

        <div class="section">
            <strong><?php echo e(_l('disowebs_ops_proof_problem')); ?></strong>
            <div><?php echo nl2br(e($entry['problem'])); ?></div>
        </div>
        <div class="section">
            <strong><?php echo e(_l('disowebs_ops_proof_solution')); ?></strong>
            <div><?php echo nl2br(e($entry['solution'])); ?></div>
        </div>
        <div class="section">
            <strong><?php echo e(_l('disowebs_ops_proof_outcome')); ?></strong>
            <div><?php echo nl2br(e($entry['outcome'])); ?></div>
        </div>

        <?php if (!empty($entry_files)) { ?>
        <div class="section">
            <strong><?php echo e(_l('disowebs_ops_proof_files')); ?></strong>
            <ul class="files">
                <?php foreach ($entry_files as $file) { ?>
                <?php
                $file_url = disowebs_ops_proof_upload_url($entry['id'], $file['file_path']);
                $extension = strtolower(pathinfo($file['file_path'], PATHINFO_EXTENSION));
                ?>
                <li>
                    <a href="<?php echo e($file_url); ?>" target="_blank"><?php echo e($file['file_path']); ?></a>
                    <?php if (in_array($extension, $image_extensions, true)) { ?>
                    <div class="image-preview">
                        <img src="<?php echo e($file_url); ?>" alt="<?php echo e($entry['title']); ?>">
                    </div>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</body>
</html>
