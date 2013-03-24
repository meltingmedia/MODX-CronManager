<?php
class modCronjobUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'modCronjob';
    public $languageTopics = array('cronmanager:default');
    public $objectType = 'cronmanager.modcronjob';
    /** @var modCronJob */
    public $object;

    public function beforeSet() {
        //$this->modx->log(modX::LOG_LEVEL_ERROR, print_r($this->getProperties(), true));
        $active = $this->getProperty('active');
        if ($active == $this->modx->lexicon('yes')) {
            $this->setProperty('active', true);
        } else {
            $this->setProperty('active', false);
        }
        $minutes = $this->getProperty('minutes');
        if (!isset($minutes) || empty($minutes)) $this->setProperty('minutes', 1);

        return parent::beforeSet();
    }

    public function cleanup() {
        return $this->success('', $this->object->display());
    }
}

return 'modCronjobUpdateProcessor';
