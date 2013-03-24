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

//    var bar = new Ext.ProgressBar({
//        width: 300
//    });
//    bar.wait({
//        interval: 200
//        ,duration: 5000
//        ,increment: (5000 / 200)
//        ,animate: true
//        ,fn: function() {
//            //console.log(this);
//            this.updateProgress(1, '', true);
//            console.log('done');
//        }
//    });

    Ext.apply(config, {
        border: false
        ,unstyled: true
        ,cls: 'container'
        ,layout: 'anchor'
        ,anchor: '100%'
        ,items: [{
            html: '<h2>' + _('cronmanager') + '</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            defaults: {
                autoHeight: true
                ,anchor: '100%'
            }
            ,layout: 'anchor'
            ,anchor: '100%'
            ,items: [{
                html: _('cronmanager.cronjobs_desc')
                ,bodyCssClass: 'panel-desc'
                ,border: false
            }, /*bar,*/ {
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