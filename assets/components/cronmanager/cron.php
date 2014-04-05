<?php
/**
 * @var modX $modx
 */

set_time_limit(0);

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('cronmanager.core_path', null, $modx->getOption('core_path') . 'components/cronmanager/');

/** @var CronManager $cm */
$cm = $modx->getService('cronmanager', 'model.cronmanager.CronManager', $corePath);
$modx->lexicon->load('cronmanager:default');

$rundatetime = date('Y-m-d H:i:s');

$c = $modx->newQuery('modCronjob');
$c->where(array(
    array(
        array('nextrun' => null,),
        array('OR:nextrun:<=' => $rundatetime,),
    ),
    array(
        'active' => true,
    ),
));

$total = $modx->getCount('modCronjob', $c);
if ($total < 1) {
    // No need to process
    return;
}

// Get all cronjobs which need to run
$cronjobs = $modx->getCollection('modCronjob', $c);
$modx->log(modX::LOG_LEVEL_INFO, "Found {$total} job(s) to be processed");

/** @type modCronjob $cronjob */
foreach ($cronjobs as $cronjob) {
    $cronjob->incrementNextRun();
}

$modx->log(modX::LOG_LEVEL_INFO, "Job(s) next runs incremented, now processing...");

$idx = 1;
foreach ($cronjobs as $cronjob) {
    $rank = $idx .' of '. $total;
    $modx->log(modX::LOG_LEVEL_INFO, "... job {$rank}...");

    $cronjob->execute($rundatetime);

    $idx += 1;
}

$modx->log(modX::LOG_LEVEL_INFO, "... processing complete");
