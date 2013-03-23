<?php

class CronManagerMgrHomeManagerController extends CronManagerManagerController
{

    public function getPageTitle()
    {
        return $this->modx->lexicon('cronmanager');
    }

    public function loadCustomCssJs()
    {
        $this->addJavascript($this->modx->config['manager_url'] . 'assets/modext/util/datetime.js');

        $this->addJavascript($this->jsURL . 'mgr/widgets/cronjoblog.grid.js');
        $this->addJavascript($this->jsURL . 'mgr/widgets/cronjobs.grid.js');
        $this->addJavascript($this->jsURL . 'mgr/widgets/home.panel.js');

        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                MODx.add("cronmanager-panel-home");
            });
        </script>');
    }
}