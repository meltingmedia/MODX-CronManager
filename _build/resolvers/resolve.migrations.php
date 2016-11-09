<?php
/**
 * @see xPDOVehicle::resolve
 *
 * @var xPDOVehicle $this
 * @var xPDOTransport $transport
 * @var xPDOObject|mixed $object
 * @var array $options
 *
 * @var array $fileMeta
 * @var string $fileName
 * @var string $fileSource
 *
 * @var array $r
 * @var string $type (file/php), obviously php :)
 * @var string $body (json)
 * @var integer $preExistingMode
 */
if ($object->xpdo and (
    $options[xPDOTransport::PACKAGE_ACTION] == xPDOTransport::ACTION_INSTALL
        or $options[xPDOTransport::PACKAGE_ACTION] == xPDOTransport::ACTION_UPGRADE)
) {
    /** @var $modx modX */
    $modx =& $object->xpdo;
    $rootPath = $modx->getOption('cronmanager.core_path', null, $modx->getOption('core_path') . 'components/cronmanager/');
    $modelPath = $rootPath . 'model/';

    $loaded = $modx->loadClass('Migration', $modelPath . 'migration/', true, true);
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
            $for = '1.1.5-beta';
            if (version_compare($previousVersion, $for) <= 0) {
                include_once $migration->getMigration($for);
            }

            break;
    }

    // Update the version setting
    $migration->setVersion($thisVersion);
}
