<?php
/**
 * Migration for v1.2.0-beta
 *
 * @var modX $modx
 * @var Migration $migration
 */

// Update tables
$manager = $modx->getManager();
$manager->addField('modCronjobLog', 'error');

// Clean files
$basePath = $modx->getOption('cronmanager.core_path', null, $modx->getOption('core_path') . 'components/cronmanager/');
$baseAssets = $modx->getOption('cronmanager.assets_path', null, $modx->getOption('assets_path') . 'components/cronmanager/');
$assets = array(
    'js/mgr/sections/index.js',
    'js/mgr/sections/viewlog.js',
    'js/mgr/sections/',
);
$core = array(
    'controllers/index.php',
    'controllers/mgr/index.php',
    'controllers/mgr/header.php',
    'controllers/mgr/viewlog.php',
);

foreach ($assets as $file) {
    $file = $baseAssets . $file;
    if (file_exists($file)) {
        if (is_dir($file)) {
            @rmdir($file);
        } else {
            @unlink($file);
        }
    }
}

foreach ($core as $file) {
    $file = $basePath . $file;
    if (file_exists($file)) {
        @unlink($file);
    }
}