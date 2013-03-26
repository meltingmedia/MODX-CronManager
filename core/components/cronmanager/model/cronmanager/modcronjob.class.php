<?php

class modCronjob extends xPDOSimpleObject
{

    /**
     * Counts the number of logs for this job
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