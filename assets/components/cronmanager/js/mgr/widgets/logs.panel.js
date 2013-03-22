CronManager.panel.Logs = function(config) {
    config = config || {};

    Ext.apply(config, {
        border: false
        ,unstyled: true
        ,cls: 'container'
        ,items: [{
            html: '<h2>' + _('cronmanager.log') + '</h2>'
            ,border: false
            ,id: 'cronmanager-logs-page-header'
            ,cls: 'modx-page-header'
        },{
            layout: 'anchor'
            ,items: [{
                html: _('cronmanager.logs_desc')
                ,bodyCssClass: 'panel-desc'
                ,border: false
            },{
                xtype: 'cronmanager-grid-cronjoblog'
                ,cls: 'main-wrapper'
            }]
        }]
    });
    CronManager.panel.Logs.superclass.constructor.call(this, config);
    this._init();
};
Ext.extend(CronManager.panel.Logs, MODx.Panel, {
    _init: function() {
        // Render help button
        var modAB = new Ext.Toolbar({
            renderTo: 'modAB'
            ,id: 'modx-action-buttons'
            ,items: [{
                text: _('cronmanager.log.btnback')
                ,id: 'answers-btn-back'
                ,handler: function() {
                    location.href = '?a=' + MODx.request.a;
                }
                ,scope: this
            },'-',{
                text: _('help_ex'),
                handler: MODx.loadHelpPane
            }]
        });
        modAB.doLayout();
        this.setup();
    }

    ,setup: function() {
        if (!MODx.request.id) return;

        MODx.Ajax.request({
            url: CronManager.config.connectorUrl
            ,params: {
                action: 'mgr/cronjobs/get'
                ,id: MODx.request.id
            }
            ,listeners: {
                success: {
                    fn: function(r) {
                        Ext.getCmp('cronmanager-logs-page-header').getEl().update('<h2>' + _('cronmanager.log') + ': ' + r.object.snippet_name + ' (' + r.object.id + ')</h2>');
                        this.fireEvent('ready', r.object);
                    }
                    ,scope: this
                }
            }
        });
    }
});
Ext.reg('cronmanager-panel-logs',CronManager.panel.Logs);