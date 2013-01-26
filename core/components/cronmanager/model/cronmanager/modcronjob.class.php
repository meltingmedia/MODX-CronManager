<?php
class modCronjob extends xPDOSimpleObject {

    /**
     * Counts the number of logs for this job
     *
     * @return int
     */
    public function countLogs() {
        $c = $this->xpdo->newQuery('modCronjobLog');
        $c->where(array(
            'cronjob' => $this->get('id'),
        ));

        return $this->xpdo->getCount('modCronjobLog', $c);
    }

    /**
     * Checks if this job has some error
     *
     * @param bool $returnCount Whether or not to return the number of error
     *
     * @return bool|int Either true/false if the job has some error or not, or the number of errors if $returnCount == true
     */
    public function hasFailedLogs($returnCount = false) {
        $c = $this->xpdo->newQuery('modCronjobLog');
        $c->where(array(
            'cronjob' => $this->get('id'),
            'error' => true,
        ));

        $total = $this->xpdo->getCount('modCronjobLog', $c);

        if ($returnCount) return $total;
        return ($total > 0);
    }
}