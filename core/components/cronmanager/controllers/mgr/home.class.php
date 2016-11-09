<?php

if (!class_exists('CronManagerManagerController')) {
    require_once __DIR__ . '/../../index.class.php';
}

class CronManagerMgrHomeManagerController extends CronManagerManagerController
{
    public function loadCustomCssJs()
    {
        $this->addJavascript($this->modx->config['manager_url'] . 'assets/modext/util/datetime.js');

        $this->addJavascript($this->jsURL . 'mgr/widgets/cronjoblog.grid.js');
        $this->addJavascript($this->jsURL . 'mgr/widgets/cronjobs.grid.js');
        $this->addJavascript($this->jsURL . 'mgr/widgets/home.panel.js');

        $this->addHtml(
<<<HTML
<script type="text/javascript">
    Ext.onReady(function() {
        MODx.add('cronmanager-panel-home');
    });
</script>
HTML
        );
    }
}
