<?php

if (!class_exists('CronManagerManagerController')) {
    require_once __DIR__ . '/../../index.class.php';
}

class CronManagerMgrViewLogManagerController extends CronManagerManagerController
{
    public function loadCustomCssJs()
    {
        $this->addJavascript($this->jsURL . 'mgr/widgets/cronjoblog.grid.js');
        $this->addJavascript($this->jsURL . 'mgr/widgets/logs.panel.js');

        $this->addHtml(
<<<HTML
<script type="text/javascript">
    Ext.onReady(function() {
        MODx.add('cronmanager-panel-logs');
    });
</script>
HTML
        );
    }
}
