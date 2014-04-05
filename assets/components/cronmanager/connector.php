<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

/**
 * @var modX $modx
 */

$corePath = $modx->getOption('cronmanager.core_path', null, $modx->getOption('core_path') . 'components/cronmanager/');
/** @var CronManager $cm */
$cm = $modx->getService('cronmanager', 'model.cronmanager.CronManager', $corePath);

$modx->lexicon->load('cronmanager:default');

// Handle request
$path = $modx->getOption('processorsPath', $cm->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
