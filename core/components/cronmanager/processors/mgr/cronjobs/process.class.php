<?php

class RunNow extends modProcessor
{
    /** @var modCronjob */
    protected $job;

    public function process()
    {
        $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($this->getProperties(), true));

        $id = $this->getProperty('id');
        if (!$id) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'id');
            return $this->failure('No job ID given');
        }
        $this->job = $this->modx->getObject('modCronjob', $id);
        if (!$this->job || !($this->job instanceof modCronjob)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Job not found, sorry');
            return $this->failure('Job not found, sorry');
        }
        $this->modx->log(modX::LOG_LEVEL_ERROR, 'before job');
        $this->executeJob();
        $this->modx->log(modX::LOG_LEVEL_ERROR, 'after exec');

        return $this->success();
    }

    public function executeJob()
    {
        $rundatetime = date('Y-m-d H:i:s');

        $properties = $this->job->get('properties');
        if (!empty($properties)) {
            // try to get a propertyset
            /** @var modPropertySet $propset */
            $propset = $this->modx->getObject('modPropertySet', array('name' => $properties));
            if (!empty($propset) && is_object($propset) && $propset instanceof modPropertySet) {
                $properties = $propset->getProperties();
            } else if(substr($properties, 0, 1) == '{' && substr($properties, (strlen($properties)-1), 1) == '}') {
                $props = $this->modx->fromJSON($properties);
                if (!empty($props) && is_array($props)) {
                    $properties = $props;
                }
            } else {
                $lines = explode("\n", $properties);
                $properties = array();
                foreach ($lines as $line) {
                    list($key, $value) = explode(':', $line);
                    $properties[trim($key)] = trim($value);
                }
            }
        } else {
            $properties = array();
        }
        /** @var modSnippet $snippet */
        $snippet = $this->job->getOne('Snippet');
        $this->modx->log(modX::LOG_LEVEL_ERROR, $snippet->get('name'));
        /**
         * The snippet should return a json array :
         * array('error' => boolean, 'message' => string)
         * If not, the default output will be transformed
         *
         * This will allow to define if an error occurred and ease the process of filtering logs
         */
        $response = $snippet->process($properties);
        $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($response, true));
        if (substr($response, 0, 1) == '{' && substr($response, (strlen($response)-1), 1) == '}') {
            $response = json_decode($response, true);
        } else {
            $msg = $response;
            $response = array();
            $response['message'] = $msg;
        }
        $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($response, true));

        // add log run
        $logs = array();
        /** @var modCronjobLog $log */
        $log = $this->modx->newObject('modCronjobLog');
        $log->fromArray($response);
        $log->set('logdate', $rundatetime);
        $logs[] = $log;

        $this->job->set('lastrun', $rundatetime);
        $this->job->set('nextrun', date('Y-m-d H:i:s', (strtotime($rundatetime) + ($this->job->get('minutes') * 60 ))));
        $this->job->addMany($logs);
        $this->job->save();
    }
}

return 'RunNow';