<?php

class CronManagerMgrViewLogManagerController extends CronManagerManagerController
{

    public function getPageTitle()
    {
        return $this->modx->lexicon('cronmanager');
    }

    public function loadCustomCssJs()
    {
        $this->addJavascript($this->jsURL . 'mgr/widgets/cronjoblog.grid.js');
        $this->addJavascript($this->jsURL . 'mgr/widgets/logs.panel.js');
        $this->addJavascript($this->jsURL . 'mgr/sections/viewlog.js');

        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                MODx.add("cronmanager-page-logs");
            });
        </script>');
    }
}