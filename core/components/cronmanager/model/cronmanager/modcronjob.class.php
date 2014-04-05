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

        $this->set('nextrun', date('Y-m-d H:i:s', $newRun));
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
}
