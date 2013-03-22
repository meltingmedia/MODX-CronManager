<?php

if ($object->xpdo) {
    /** @var $modx modX */
    $modx =& $object->xpdo;
    $basePath = $modx->getOption('cronmanager.core_path', null, $modx->getOption('core_path').'components/cronmanager/');
    $baseAssets = $modx->getOption('cronmanager.assets_path', null, $modx->getOption('assets_path').'components/cronmanager/');

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
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

            break;
    }
}