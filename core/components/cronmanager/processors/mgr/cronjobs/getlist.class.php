<?php
class modCronjobGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'modCronjob';
    public $languageTopics = array('cronmanager:default');
    public $objectType = 'cronmanager.modcronjob';
    public $defaultSortField = 'snippet';
    public $defaultSortDirection = 'ASC';

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->leftJoin('modSnippet', 'Snippet');
        $c->select(array(
            $this->modx->getSelectColumns($this->classKey, $this->classKey),
            $this->modx->getSelectColumns('modSnippet', 'Snippet', 'snippet_', array('id', 'name', 'description')),
        ));

        return parent::prepareQueryBeforeCount($c);
    }

    public function prepareRow(xPDOObject $object) {
        /** @var modCronJob $object */
        $objectArray = $object->toArray();

        $objectArray['logs'] = $object->countLogs();

        if (empty($objectArray['nextrun'])) {
            $objectArray['nextrun'] = '<i>'. $this->modx->lexicon('cronmanager.runempty') .'</i>';
        }

        if (empty($objectArray['lastrun'])) {
            $objectArray['lastrun'] = '<i>'. $this->modx->lexicon('cronmanager.runempty') .'</i>';
        }

        return $objectArray;
    }
}

return 'modCronjobGetListProcessor';
