/** * Cron jobs grid * * @class CronManager.grid.CronJobs * @extends MODx.grid.Grid * @param config * @xtype cronmanager-grid-cronjobs */CronManager.grid.CronJobs = function(config) {    config = config || {};    this.snippetTpl = new Ext.XTemplate(        '<div><ul><li>{snippet_name}</li>'        ,'<tpl if="snippet_description"><li><i><small>{snippet_description}</small></i></li></tpl>'        ,'</ul></div>'    );    this.rowContent = new Ext.ux.grid.RowPanelExpander({        expandOnDblClick: false        //,expandOnRowClick: false        //,enableCaching: false        ,createExpandingRowPanelItems: function(record, rowIndex) {            var grid = this.grid;            return [                new CronManager.panel.JobPanel({                    grid: grid                    ,record: record                    ,rowIndex: rowIndex                })            ];        }    });    Ext.applyIf(config, {        url: CronManager.config.connectorUrl        ,baseParams: {            action: 'mgr/cronjobs/getList'        }        ,save_action: 'mgr/cronjobs/updateFromGrid'        ,autosave: true        ,fields: ['id','snippet','snippet_name','properties','minutes','nextrun','lastrun','active','sortorder','snippet_description', 'logs', 'logs_error', 'nextrun_seconds']        ,plugins: [this.rowContent]        ,paging: true        ,remoteSort: true        ,anchor: '100%'        ,autoExpandColumn: 'snippet'        ,emptyText: _('cronmanager.norecords')        ,columns: [{            header: _('id')            ,dataIndex: 'id'            ,sortable: true            ,hidden: true        }, this.rowContent,{            header: _('cronmanager.snippet')            ,dataIndex: 'snippet_name'            ,sortable: true            ,renderer: function(value, meta, record) {                return this.snippetTpl.apply(record.data);            }            ,scope: this        },{            header: _('cronmanager.logs_entries')            ,dataIndex: 'logs'            ,fixed: true            ,menuDisabled: true            ,renderer: this.renderLogs            ,align: 'right'        },{            header: 'Next run'            ,renderer: this.renderBar            ,fixed: true            ,menuDisabled: true            ,width: 220        }/*,{            header: _('cronmanager.active')            ,dataIndex: 'active'            ,fixed: true            ,renderer: this.rendYesNo            ,menuDisabled: true            ,align: 'center'        }*/],        tbar:[{            text: _('cronmanager.create')            ,handler: {                xtype: 'cronmanager-window-create'                ,blankValues: true            }        }]        ,listeners: {            afterlayout: function() {                // Workaround to resize the content                //console.log('after layout');                this.resizeJobPanel();            }        }    });    CronManager.grid.CronJobs.superclass.constructor.call(this, config);    this.getSelectionModel().on('beforerowselect', function() {        // Prevent row selection because of nested grids        return false;    });    // Animations    this.rowContent.on('expand', function(exp, rec, body, rowIdx) {        this.resizeJobPanel();        exp.expandingRowPanel[rec.id].el.slideIn('t', {            duration: 0.2//            ,callback: function() {//                console.log('expand');//                //console.log(exp);//            }        });    }, this);    this.rowContent.on('beforecollapse', function(exp, rec, body, rowIdx) {        exp.expandingRowPanel[rec.id].el.slideOut('t', {            duration: 0.2            ,callback: function() {                // Set as "collapsed" after the animation                var row = exp.grid.view.getRow(rowIdx);                exp.state[rec.id] = false;                Ext.fly(row).replaceClass('x-grid3-row-expanded', 'x-grid3-row-collapsed');                exp.grid.saveState();                exp.fireEvent('collapse', exp, rec, body, rowIdx);            }        });        // Prevent immediate "collapsing"        return false;    }, this);    this.getStore().on('load', function(store, records, options) {        //console.log('store load');        this.setProgressBars(store, records, options);    }, this);    this.getStore().on('update', function(store, records, options) {        //console.log('store update');        this.setProgressBars(store, records, options);        //this.getView().refresh();    }, this);};Ext.extend(CronManager.grid.CronJobs, MODx.grid.Grid, {    // Mark all columns as not expanded when the store is (re) loaded    collapseTrigger: function() {        var aRows = this.getView().getRows();        for (var i = 0; i < aRows.length; i++) {            Ext.fly(aRows[i]).replaceClass('x-grid3-row-expanded', 'x-grid3-row-collapsed');        }    }    ,setProgressBars: function(store, records, options) {        if (records.length > 0) {            var me = this;            Ext.each(records, function(record, idx, array) {                var bar = new Ext.ProgressBar({                    renderTo: 'bar-' + record.id                    ,text: me.setTextBar(record.data)                    ,disabled: !(record.data.active)                });                if (record.data.nextrun_seconds > 0 && record.data.active) {                    me.runBar(bar, record);                }            });        }    }    ,setTextBar: function(data) {        if (!data.active) {            var text = 'Inactive';        } else if (data.nextrun_seconds > 0) {            text = '';        } else if (data.nextrun_seconds <= 0) {            text = 'Running…';        }        return text;    }    ,runBar: function(bar, record) {        var duration = record.data.nextrun_seconds * 1000            ,interval = 200            ,me = this;        bar.wait({            interval: interval            ,duration: duration            ,increment: (duration / interval)            ,animate: true            ,fn: function() {                console.log('done');                bar.updateProgress(1, 'Running…', true);                bar.updateText('Running…');//                Ext.defer(function() {//                    me.refresh();//                }, 2000);            }        });        bar.on('update', function(cmp, value) {            if (value == 1) {                bar.updateText('Running…');                return '';            }            duration = duration - interval;            var seconds = ~~(duration / 1000)                ,display = 'Next run in ';            if (seconds < 61) {                display +=  seconds +' sec.';            } else {                display += (seconds / 60).toFixed(2) +' min.';            }            bar.updateText(display);        });    }    ,renderBar: function(value, meta, record) {        return '<div><ul><li>'+ _('cronmanager.lastrun') +' : <span style="float: right">'+ record.data.lastrun +'</span></li><li id="bar-'+ record.id +'"></li></ul></div>';    }    ,renderLogs: function(value, meta, record, idx, colIdx, store) {        return '<div><ul><li>total : '+ value +'</li><li>errors : <span'+ (record.data.logs_error > 0 ? ' class="red"' : '') +'>'+ record.data.logs_error +'</span></li></ul></div>';    }    // Workaround to resize the panels    ,resizeJobPanel: function() {        var width = this.getWidth()            ,panels = this.rowContent.expandingRowPanel;        if (panels && panels.length > 0) {            Ext.each(panels, function(panel, idx, list) {                if (undefined === panel || null === panel) return '';                panel.setWidth(width - 32);            })        }    }});Ext.reg('cronmanager-grid-cronjobs', CronManager.grid.CronJobs);/** * Cron job update window * * @class CronManager.window.Update * @extends CronManager.window.Create * @param config * @xtype cronmanager-window-update */CronManager.panel.JobPanel = function(config) {    config = config || {};    //console.log(config.record);    Ext.applyIf(config, {        plain: true        ,border: false        ,activeTab: 0        ,layoutOnTabChange: true        ,forceLayout: true        ,cls: 'trackme'        ,defaults: {            autoHeight: true            ,border: false            ,cls: 'tab_content'//            ,style: {//                background: 'blue'//            }            ,layout: 'anchor'        }        ,items:[{            title: '&#160;'            ,iconCls: 'x-toolbar-more-icon'            ,disabled: true            ,style: {                padding: 0                ,color: '#000'            }        },{            title: _('cronmanager.update')            ,loadAction: 'showEditPanel'            ,iconCls:'icon-list-new'        },{            title: _('cronmanager.viewlog')            ,loadAction: 'showLogPanel'            ,iconCls: 'ext-ux-uploaddialog-uploadstartbtn'        },{            title: _('cronmanager.run_now')            ,loadAction: 'showRunPanel'            ,iconCls: 'ext-ux-uploaddialog-removebtn'        },{            title: _('cronmanager.remove')            ,loadAction: 'showRemovePanel'            ,iconCls: 'ext-ux-uploaddialog-resetbtn'        }]        ,listeners: {            tabchange: function(elem, tab) {                this.activeTabIdx = elem.items.findIndex('id', tab.id);                if (tab.disabled) return '';                tab.removeAll();                var action = tab.loadAction;                if (action && 'function' == typeof this[action]) {                    tab.add(this[action]());                    tab.doLayout();                    tab.el.slideIn('r', {                        duration: 0.2                    });                }            }            ,beforetabchange: function(panel, newTab, currentTab) {                if (newTab.disabled && panel.activeTab !== undefined) {                    currentTab.el.slideOut('t', {                        duration: 0.2                    });                }            }            ,scope: this        }    });    CronManager.panel.JobPanel.superclass.constructor.call(this, config);};Ext.extend(CronManager.panel.JobPanel, Ext.TabPanel, {    // "false" close tab    closePanel: function() {        this.setActiveTab(0);    }    // Edit cron details panel    ,showEditPanel: function() {        return new CronManager.panel.EditCron({            record: this.record            ,grid: this.grid            ,tabPanel: this        });    }    // Logs panel    ,showLogPanel: function() {        if (this.record.data.logs <= 0) {            return {                html: 'No logs'                ,border: false            };        }        return new CronManager.grid.CronJobLog({            baseParams: {                action: 'mgr/cronjobs/getlog'                ,cronid: this.record.id            }            ,id: 'cronlogs-' + this.record.id            ,tabPanel: this        });    }    // Run cron job panel    ,showRunPanel: function() {        return new MODx.Panel({            layout: 'anchor'            ,tabPanel: this            ,items: [{                html: 'Execute this task right now ?'                ,border: false            },{                xtype: 'toolbar'                ,style: {                    background: 'transparent'                    ,border: '0 none transparent'                }                ,defaultType: 'button'                ,items: [{                    text: 'Execute'                    ,cls: 'green'                    ,handler: function() {                        var mask = new Ext.LoadMask(                            this.el                            ,{                                msg: 'Please wait…'                                ,removeMask: true                            }                        );                        mask.show();                        // Execute the selected job                        var exec = MODx.Ajax;                        exec.request({                            url: CronManager.config.connectorUrl                            ,params: {                                action: 'mgr/cronjobs/process'                                ,id: this.record.id                            }                        });                        exec.on('success', function(r) {                            console.log(r);                            this.record.data = r.object;                            this.record.commit();                           mask.hide();                        }, this);                    }                    ,scope: this                },{                    text: 'Cancel'                    ,handler: function() {                        this.closePanel();                    }                    ,scope: this                }]            }]        });    }    ,showRemovePanel: function() {        return [];        // Delete the selected entry//        ,removeCronJob: function() {//            MODx.msg.confirm({//                title: _('cronmanager.remove')//                ,text: _('cronmanager.remove_confirm', { snippet: '<b>'+ this.menu.record.snippet_name +'</b>' })//                ,url: this.config.url//                ,params: {//                    action: 'mgr/cronjobs/remove'//                    ,id: this.menu.record.id//                }//                ,listeners: {//                    success: {//                        fn: this.refresh//                        ,scope: this//                    }//                }//            });//        }    }});CronManager.panel.EditCron = function(config) {    config = config || {};    Ext.applyIf(config, {        url: CronManager.config.connectorUrl        ,baseParams: {            action: 'mgr/cronjobs/update'        }        ,baseCls: 'modx-formpanel'        ,labelAlign: 'top'        ,cls: 'container'        ,anchor: '100%'        ,layout: 'column'        ,items: [{            columnWidth: .67            ,layout: 'form'            ,defaults: {                msgTarget: 'under'                ,allowBlank: false                ,anchor: '100%'            }            ,items: [{                xtype: 'hidden'                ,name: 'id'            },{                xtype: 'cronmanager-combo-snippets'                ,fieldLabel: _('cronmanager.snippet')                ,name: 'snippet'            },{                xtype: 'textarea'                ,fieldLabel: _('cronmanager.properties')                ,description: _('cronmanager.properties_desc')                ,name: 'properties'                ,allowBlank: true                ,grow: true            }]        },{            columnWidth: .33            ,layout: 'form'            ,labelWidth: 0            ,border: false            ,style: 'margin-right: 0'            ,defaults: {                msgTarget: 'under'                ,anchor: '100%'            }            ,items: [{                xtype: 'xdatetime'                ,fieldLabel: 'Next run'                ,name: 'nextrun'            },{                xtype: 'combo-boolean'                ,fieldLabel: _('cronmanager.active')                ,name: 'active'                ,listWidth: false            },{                xtype: 'numberfield'                ,fieldLabel: _('cronmanager.minutes')                ,description: _('cronmanager.minutes_desc')                ,name: 'minutes'                ,width: 60                ,value: 60                ,minValue: 1                ,allowNegative: false                ,allowBlank: false            }]        }]        ,bbar: new Ext.Toolbar({            style: {                background: 'transparent'                ,border: '0 none transparent'                ,marginTop: '40px'            }            ,defaultType: 'button'            ,items: ['->', {                text: _('save')                ,cls: 'green'                ,handler: function() {                    this.submit();                }                ,scope: this            }, '-',{                text: 'Cancel'                ,handler: function() {                    this.tabPanel.setActiveTab(0);                }                ,scope: this            }]        })        ,listeners: {            setup: function() {                this.getForm().setValues(this.record.data);            }            ,success: function(r) {//                var idx = this.tabPanel.activeTabIdx;////                if (idx) {//                    this.grid.getStore().on('update', function() {//                        console.log('store update');//                        console.log(idx);//                        console.log(this.grid.rowContent.expandingRowPanel[this.record.id].items[0]);//                        this.grid.rowContent.on('expand', function() {//                            console.log('expand after update');//                            this.tabPanel.setActiveTab(idx);//                        }, this);//                    }, this);//                }                this.record.data = r.result.object;                this.record.commit();            }            ,scope: this        }    });    CronManager.panel.EditCron.superclass.constructor.call(this, config);};Ext.extend(CronManager.panel.EditCron, MODx.FormPanel, {});/** * Cron job create window * * @class CronManager.window.Create * @extends MODx.Window * @param config * @xtype cronmanager-window-create */CronManager.window.Create = function(config) {    config = config || {};    Ext.applyIf(config, {        title: _('cronmanager.create')        ,url: CronManager.config.connectorUrl        ,baseParams: {            action: 'mgr/cronjobs/create'        }        ,formDefaults: {            anchor: '100%'            ,allowBlank: false        }        ,fields: [{            xtype: 'hidden'            ,name: 'id'        },{            xtype: 'cronmanager-combo-snippets'            ,fieldLabel: _('cronmanager.snippet')            ,name: 'snippet'        },{            xtype: 'numberfield'            ,fieldLabel: _('cronmanager.minutes')            ,description: _('cronmanager.minutes_desc')            ,name: 'minutes'            ,width: 60            ,value: 60            ,minValue: 1            ,allowNegative: false        },{            xtype: 'textarea'            ,fieldLabel: _('cronmanager.properties')            ,description: _('cronmanager.properties_desc')            ,name: 'properties'            ,allowBlank: true            ,grow: true            ,growMax: 200        }]        ,keys:[{            key: Ext.EventObject.ENTER            ,shift: true            ,fn: this.submit            ,scope: this        }]    });    CronManager.window.Create.superclass.constructor.call(this, config);};Ext.extend(CronManager.window.Create, MODx.Window);Ext.reg('cronmanager-window-create', CronManager.window.Create);