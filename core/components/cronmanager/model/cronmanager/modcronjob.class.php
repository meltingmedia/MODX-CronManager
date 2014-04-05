<?php

class modCronjob extends xPDOSimpleObject
{

    /**
     * Counts the number of logs for this job
     *
     * @param bool $errors - Whether or not to only display logs with errors
     *
     * @return int
     */
    public function countLogs($errors = false)
    {
        $c = $this->xpdo->newQuery('modCronjobLog');
        $c->where(array(
            'cronjob' => $this->get('id'),
        ));
        if ($errors) {
            $c->where(array(
                'error' => true,
            ));
        }

        return $this->xpdo->getCount('modCronjobLog', $c);
    }

    /**
     * Calculate the next run date
     *
     * @return void
     */
    public function incrementNextRun()
    {
        $next = $this->get('nextrun');
        if (empty($next)) {
            $this->xpdo->log(
                modX::LOG_LEVEL_INFO,
                '[modCronjob::incrementNextRun] No next run found, considering it to be right now'
            );
            $next = date('Y-m-d H:i:s');
        }
        $this->xpdo->log(
            modX::LOG_LEVEL_INFO,
            '[modCronjob::incrementNextRun] Current run should have been executed on '. $next . ' currently '.
            date('Y-m-d H:i:s')
        );
        $next = strtotime($next);
        $delay = $this->get('minutes') * 60;

        $newRun = $next + $delay;
        if ($newRun < time()) {
            $this->xpdo->log(
                modX::LOG_LEVEL_INFO,
                '[modCronjob::incrementNextRun] Next run is in the past, fixing it...'
            );
            $newRun = time() + $delay;
        }

        $newRun = date('Y-m-d H:i:s', $newRun);
        $this->xpdo->log(
            modX::LOG_LEVEL_INFO,
            '[modCronjob::incrementNextRun] Next run is scheduled for '. $newRun
        );

        $this->set('nextrun', $newRun);
        $this->save();
    }

    /**
     * Display all information about this job
     *
     * @return array
     */
    public function display()
    {
        $data = $this->toArray();
        $data['logs'] = $this->countLogs();
        $data['logs_error'] = $this->countLogs(true);

        $data['snippet_description'] = '';
        /** @var $snippet modSnippet */
        $snippet = $this->Snippet;
        if ($snippet) {
            $data['snippet_name'] = $snippet->get('name');
            $data['snippet_description'] = $snippet->get('description');
        }

        if (empty($data['nextrun'])) {
            $data['nextrun'] = '<i>'. $this->xpdo->lexicon('cronmanager.runempty') .'</i>';
        } else {
            $data['next'] = $next = strtotime($data['nextrun']);
//        $data['last'] = $last = strtotime($data['lastrun']);
            $data['now'] = $now = time();
//        $data['next_full'] = $nextFull = $data['minutes'] * 60;
            $data['nextrun_seconds'] = $next - $now;
//        $data['elapsed'] = $nextFull - $data['nextrun_seconds'];
//        $data['elapsed_ratio'] = $data['elapsed'] / $nextFull;
//        $this->xpdo->log(modX::LOG_LEVEL_ERROR, print_r($data, true));
        }

        if (empty($data['lastrun'])) {
            $data['lastrun'] = '<i>'. $this->xpdo->lexicon('cronmanager.runempty') .'</i>';
        }

        return $data;
    }

    /**
     * Process the job
     *
     * @param string $runDate
     *
     * @return void
     */
    public function execute($runDate = '')
    {
        if (empty($runDate)) {
            $runDate = date('Y-m-d H:i:s');
        }

        $response = $this->processSnippet();
        $this->addLog($response, $runDate);

        $this->set('lastrun', date('Y-m-d H:i:s'));
        $this->save();
    }

    /**
     * Wrapper method to execute the snippet
     *
     * @return array
     */
    protected function processSnippet()
    {
        $properties = $this->getProperties();

        /** @var modSnippet $snippet */
        $snippet = $this->getOne('Snippet');
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

        return $response;
    }

    /**
     * Add the result as log
     *
     * @param array $response - The snippet response
     * @param string $runDate
     *
     * @return void
     */
    public function addLog(array $response, $runDate = '')
    {
        if (empty($runDate)) {
            $runDate = date('Y-m-d H:i:s');
        }
        $logs = array();
        /** @var modCronjobLog $log */
        $log = $this->xpdo->newObject('modCronjobLog');
        $log->fromArray($response);
        $log->set('logdate', $runDate);
        $logs[] = $log;

        $this->addMany($logs);
    }

    /**
     * Get the snippet properties, if any
     *
     * @return array
     */
    public function getProperties()
    {
        $properties = $this->get('properties');

        if (!empty($properties)) {
            // Try to get a property set
            /** @var modPropertySet $propset */
            $propset = $this->xpdo->getObject('modPropertySet', array(
                'name' => $properties,
            ));

            if (!empty($propset) && is_object($propset) && $propset instanceof modPropertySet) {
                $properties = $propset->getProperties();
            } elseif (substr($properties, 0, 1) == '{' && substr($properties, (strlen($properties)-1), 1) == '}') {
                // Check if it is a json object
                $props = $this->xpdo->fromJSON($properties);
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

        return $properties;
    }
}
