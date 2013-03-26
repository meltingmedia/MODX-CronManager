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
            },{
                xtype: 'panel'
                ,layout: 'card'
                ,activeItem: 0
                ,id: 'cm-cardpanel'
                ,unstyled: true
                ,items: [{
                    xtype: 'cronmanager-grid-cronjobs'
                    ,cls: 'main-wrapper'
                    ,homePanel: this
                },{
                    xtype: 'cronmanager-panel-edit'
                    ,cls: 'main-wrapper'
                    ,unstyled: true
                    ,baseParams: {
                        action: 'mgr/cronjobs/create'
                    }
                    ,bbar: ['->', {
                        text: _('save')
                        ,handler: function() {
                            this.addCron();
                        }
                        ,scope: this
                    },'-',{
                        text: _('cronmanager.cancel')
                        ,handler: function() {
                            this.switchTab(0);
                        }
                        ,scope: this
                    }]
                    ,listeners: {
                        success: function(r) {
                            this.switchTab(0);
                            Ext.getCmp('cronmanager-grid-cronjobs').refresh();
                        }
                        ,scope: this
                    }
                }]
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

        this.cardPanel = Ext.getCmp('cm-cardpanel');
        this.formPanel = this.cardPanel.items.get(1);
    }

    ,switchTab: function(id) {
        this.cardPanel.getLayout().setActiveItem(id);
        if (id === 1) {
            // Set defaults to the form
            this.formPanel.getForm().setValues({
                snippet: ''
                ,minutes: 60
                ,properties: ''
                ,active: false
            });
        }
        this.cardPanel.items.get(id).el.slideIn('r', {
            duration: .2
        })
    }

    ,addCron: function() {
        this.formPanel.submit();
    }
});
Ext.reg('cronmanager-panel-home', CronManager.panel.Home);