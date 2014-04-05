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

// Get all cronjobs which needs to run
$cronjobs = $modx->getCollection('modCronjob', $c);
$modx->log(modX::LOG_LEVEL_INFO, "Found {$total} job(s) to be processed");

/** @type modCronjob $cronjob */
foreach ($cronjobs as $cronjob) {
    $cronjob->incrementNextRun();
}

$modx->log(modX::LOG_LEVEL_INFO, "Job(s) next runs incremented, now processing...");

foreach ($cronjobs as $idx => $cronjob) {
    $rank = $idx + 1 .' of '. $total;
    $modx->log(modX::LOG_LEVEL_INFO, "... job {$rank}...");

    $properties = $cronjob->get('properties');

    if (!empty($properties)) {
        // Try to get a property set
        /** @var modPropertySet $propset */
        $propset = $modx->getObject('modPropertySet', array('name' => $properties));

        if (!empty($propset) && is_object($propset) && $propset instanceof modPropertySet) {
            $properties = $propset->getProperties();
        } elseif (substr($properties, 0, 1) == '{' && substr($properties, (strlen($properties)-1), 1) == '}') {
            // Check if it is a json object
            $props = $modx->fromJSON($properties);
            if (!empty($props) && is_array($props)) {
                $properties = $props;
            }
        } else {
            // Then must be it a key value pair group
            $lines = explode("\n", $properties);
            $properties = array();
            foreach ($lines as $line) {
                list($key, $value) = explode(':', $line);
                $properties[trim($key)] = trim($value);
            }
        }
    } else {
        // When empty, make it an array
        $properties = array();
    }

    /** @var modSnippet $snippet */
    $snippet = $cronjob->getOne('Snippet');
    /**
     * The snippet should return a json array :
     * array('error' => boolean, 'message' => string)
     * If not, the default output will be transformed
     *
     * This will allow to define if an error occurred and ease the process of filtering logs
     */
    $response = $snippet->process($properties);
    if (substr($response, 0, 1) == '{' && substr($response, (strlen($response)-1), 1) == '}') {
        $response = json_decode($response, true);
    } else {
        $msg = $response;
        $response = array();
        $response['message'] = $msg;
    }

    // Add log run
    $logs = array();
    /** @var modCronjobLog $log */
    $log = $modx->newObject('modCronjobLog');
    $log->fromArray($response);
    $log->set('logdate', $rundatetime);
    $logs[] = $log;

    $cronjob->set('lastrun', date('Y-m-d H:i:s'));
    $cronjob->addMany($logs);
    $cronjob->save();
}

$modx->log(modX::LOG_LEVEL_INFO, "... processing complete");
