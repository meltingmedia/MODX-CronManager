<?php
class modCronjobUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'modCronjob';
    public $languageTopics = array('cronmanager:default');
    public $objectType = 'cronmanager.modcronjob';
    /** @var modCronJob */
    public $object;

    public function beforeSet() {
        $minutes = $this->getProperty('minutes');
        if (!isset($minutes) || empty($minutes)) $this->setProperty('minutes', 1);

        return parent::beforeSet();
    }

    public function cleanup() {
        return $this->success('', $this->object->display());
    }
}

return 'modCronjobUpdateProcessor';
