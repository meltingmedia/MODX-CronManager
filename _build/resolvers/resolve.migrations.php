<?php

if ($object->xpdo && ($options[xPDOTransport::PACKAGE_ACTION] || $options[xPDOTransport::ACTION_UPGRADE])) {
    /** @var $modx modX */
    $modx =& $object->xpdo;
    $rootPath = $modx->getOption('cronmanager.core_path', null, $modx->getOption('core_path') . 'components/cronmanager/');
    $modelPath = $rootPath . 'model/';

    $loaded = $modx->loadClass('migration', $modelPath . 'migration/', true, true);
    $migration = new Migration($modx, array(
        'component_name' => PKG_NAME_LOWER,
        'package_name' => PKG_NAME_LOWER,
        'namespace' => $options['namespace'],
        'migrations_path' => $rootPath . 'migrations/',
    ));

    // Get signature
    $tmpVersion = $migration->getVersion($options);
    $name = $options['namespace'];
    $thisVersion = str_replace($name . '-', '', $tmpVersion);

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $modx->log(modX::LOG_LEVEL_INFO, 'Installing version : '. $thisVersion);
            $modx->addPackage('cronmanager', $modelPath);

            $manager = $modx->getManager();

            $manager->createObjectContainer('modCronjob');
            $manager->createObjectContainer('modCronjobLog');

            break;
        case xPDOTransport::ACTION_UPGRADE:
            $modx->addPackage('cronmanager', $modelPath);

            // Get previously installed version
            $previousVersion = $migration->getPreviousVersion();
            $modx->log(modX::LOG_LEVEL_INFO, 'Previous version : '. $previousVersion);

            // Lets trigger appropriate "migrations"
            $for = '1.2.0-beta';
            if (version_compare($previousVersion, $for) <= 0) {
                include_once $migration->getMigration($for);
            }

            break;
    }

    // Update the version setting
    $migration->setCurrentVersion($thisVersion);
}
