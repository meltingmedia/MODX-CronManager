/**
 * Home (index) panel
 *
 * @class CronManager.panel.Home
 * @extends MODx.panel
 * @param config
 * @xtype cronmanager-panel-home
 */
CronManager.panel.Home = function(config) {
    config = config || {};

    Ext.apply(config, {
        border: false
        ,unstyled: true
        ,cls: 'container'
        ,items: [{
            html: '<h2>' + _('cronmanager') + '</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            defaults: { autoHeight: true }
            ,layout: 'anchor'
            ,items: [{
                html: _('cronmanager.cronjobs_desc')
                ,bodyCssClass: 'panel-desc'
                ,border: false
            },{
                xtype: 'cronmanager-grid-cronjobs'
                ,cls: 'main-wrapper'
            }]
        }]
    });
    CronManager.panel.Home.superclass.constructor.call(this, config);
    this._init();
};
Ext.extend(CronManager.panel.Home, MODx.Panel, {
    _init: function() {
        // Render help button
        var modAB = new Ext.Toolbar({
            renderTo: 'modAB'
            ,id: 'modx-action-buttons'
            ,items: [{
                text: _('help_ex'),
                handler: MODx.loadHelpPane
            }]
        });
        modAB.doLayout();
    }
});
Ext.reg('cronmanager-panel-home', CronManager.panel.Home);