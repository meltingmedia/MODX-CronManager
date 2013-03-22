<?php

require_once dirname(__FILE__) . '/model/cronmanager/cronmanager.class.php';

abstract class CronManagerManagerController extends modExtraManagerController
{
    /** @var CronManager $cronmanager */
    public $cronmanager;
    public $jsURL;
    public $cssURL;

    public function initialize()
    {
        $this->cronmanager = new CronManager($this->modx);
        $this->jsURL = $this->cronmanager->config['jsUrl'];
        $this->cssURL = $this->cronmanager->config['cssUrl'];
        $this->loadBase();
        parent::initialize();
    }

    public function loadBase()
    {
        $this->addJavascript($this->jsURL . 'mgr/cronmanager.js');
        $this->addJavascript($this->jsURL . 'mgr/combos.js');
        $this->addJavascript($this->jsURL . 'mgr/expander.js');

        $this->addHtml('<script type="text/javascript">
        Ext.ns("CronManager");
        Ext.onReady(function() {
            CronManager.config = '. $this->modx->toJSON($this->cronmanager->config) .';
            CronManager.action = "'. (!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0) .'";
        });
        </script>');
    }

    public function getLanguageTopics()
    {
        return array('cronmanager:default');
    }
}

class IndexManagerController extends CronManagerManagerController
{
    public static function getDefaultController()
    {
        return 'mgr/home';
    }
}