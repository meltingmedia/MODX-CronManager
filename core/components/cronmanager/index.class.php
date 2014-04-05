<?php

abstract class CronManagerManagerController extends modExtraManagerController
{
    /**
     * @var CronManager $cronmanager
     */
    public $cronmanager;
    /**
     * @var string
     */
    public $jsURL;
    /**
     * @var string
     */
    public $cssURL;

    public function initialize()
    {
        $path = $this->modx->getOption(
            'cronmanager.core_path',
            null,
            $this->modx->getOption('core_path') . 'components/cronmanager/'
        );
        $this->cronmanager = $this->modx->getService('cronmanager', 'model.cronmanager.CronManager', $path);
        $this->jsURL = $this->cronmanager->config['jsUrl'];
        $this->cssURL = $this->cronmanager->config['cssUrl'];
        $this->loadBase();
        parent::initialize();
    }

    public function loadBase()
    {
        $this->addCss($this->cssURL . 'expander-editor.css');
        $this->addJavascript($this->jsURL . 'mgr/cronmanager.js');
        $this->addJavascript($this->jsURL . 'mgr/combos.js');
        $this->addJavascript($this->jsURL . 'mgr/expander.js');

        $action = !empty($_REQUEST['a']) ? $_REQUEST['a'] : 0;

        $this->addHtml(
<<<HTML
<script type="text/javascript">
    Ext.ns('CronManager');
    Ext.onReady(function() {
        CronManager.config = {$this->modx->toJSON($this->cronmanager->config)};
        CronManager.action = "{$action}";
    });
</script>
HTML
        );
    }

    public function getLanguageTopics()
    {
        return array('cronmanager:default');
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('cronmanager');
    }
}

class IndexManagerController extends CronManagerManagerController
{
    public static function getDefaultController()
    {
        return 'mgr/home';
    }
}
