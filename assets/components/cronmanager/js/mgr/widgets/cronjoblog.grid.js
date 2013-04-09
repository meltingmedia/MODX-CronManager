CronManager.grid.CronJobLog = function(config) {    config = config || {};    this.sm = new Ext.grid.CheckboxSelectionModel();    Ext.applyIf(config,{        id: 'cronmanager-grid-cronjoblog',		url: CronManager.config.connectorUrl,		baseParams: { action: 'mgr/cronjobs/getlog', cronid: MODx.request.id },		fields: [		  'id','logdate','message','day','error',		  // Added 1.2:		  'start_time',// - Time that job is called           'memory_peak',// - Memory peak of job in MB          'status', // - SET('Started','Completed')          'execution_time'// - Caluclated time in miliseconds		],		paging: true,		remoteSort: true,		anchor: '97%',		autoExpandColumn: 'snippet',        grouping: true,        groupBy: 'day',        sortBy: 'logdate',        sortDir: 'DESC',        singleText: _('cronmanager.log_message'),        pluralText: _('cronmanager.log_messages'),        sm: this.sm,		emptyText: _('cronmanager.log.norecords'),		columns: [this.sm, {            header: _('id'),			dataIndex: 'id',			sortable: true,			width: 25,            hidden: true        },{            header: _('cronmanager.log_error')            ,dataIndex: 'error'            ,hidden: true        },{            header: _('cronmanager.log_day')            ,dataIndex: 'day'            ,hidden: true        },          // Added 1.2:        {            header: _('cronmanager.log.date'),			dataIndex: 'start_time',			width: 40,			sortable: true,			renderer : Ext.util.Format.dateRenderer(MODx.config.manager_date_format +' '+ MODx.config.manager_time_format)        },{            header: _('cronmanager.log.status'),            description: _('cronmanager.log.status_desc'),            dataIndex: 'status',            width: 60,            sortable: true        },{            header: _('cronmanager.log.memory_peak'),            description: _('cronmanager.log.memory_peak_desc'),            dataIndex: 'memory_peak',            width: 40,            sortable: true        },{            header: _('cronmanager.log.execution_time'),            description: _('cronmanager.log.execution_time_desc'),            dataIndex: 'execution_time',            width: 40,            sortable: true        },        {            header: _('cronmanager.log.message'),			dataIndex: 'message',			sortable: true        }],		tbar:[{            xtype: 'tbsplit'            ,text: _('cronmanager.logs_actions')            ,menu: [{                text: _('cronmanager.logs_purge_no_err')                ,handler: this.purgeLogs                ,scope: this            }/*,{                text: _('cronmanager.logs_purge_all')                ,handler: this.purgeLogs('all')                ,scope: this            }*/]            ,handler: function(btn) {                btn.showMenu();            }		},'-',{			xtype: 'textfield',			id: 'cronmanager-search-filter',			emptyText: _('cronmanager.search...'),			listeners: {				'change': { fn:this.search, scope:this },				'render': { fn: function(cmp) {					new Ext.KeyMap(cmp.getEl(), {						key: Ext.EventObject.ENTER,						fn: function() {							this.fireEvent('change',this);							this.blur();							return true;						}, scope: cmp					});				}, scope: this }			}		}        ,'->',{            xtype: 'cronmanager-combo-error'            ,name: 'error'            ,listeners: {                select: {                    fn: this.filter                    ,scope: this                }            }        }]    });    if (config.grouping) {        Ext.applyIf(config,{          view: new Ext.grid.GroupingView({            forceFit: true            ,hideGroupedColumn: true            ,enableGroupingMenu: false            ,enableNoGroups: false            ,scrollOffset: 0            ,groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "'                +(config.pluralText || _('records')) + '" : "'                +(config.singleText || _('record'))+'"]})'          })        });    }    CronManager.grid.CronJobLog.superclass.constructor.call(this, config)};Ext.extend(CronManager.grid.CronJobLog, MODx.grid.Grid);Ext.extend(CronManager.grid.CronJobLog, MODx.grid.Grid, {	search: function(tf,nv,ov) {        var s = this.getStore();        s.baseParams.query = tf.getValue();        this.getBottomToolbar().changePage(1);        this.refresh();    }    // Grid contextual menu    ,getMenu: function(cfg) {        var m = [];        if (this.getSelectionModel().getCount() > 1) {            // Multiple rows selected            var rs = this.getSelectionModel().getSelections();            //m.push('-');            m.push({                text: _('cronmanager.logs_delete_selected')                ,handler: function() {                    var cs = this.getSelectedAsList();                    if (cs === false) return false;                    MODx.msg.confirm({                        title: _('cronmanager.logs_delete_selected')                        ,text: _('cronmanager.logs_delete_selected_confirm')                        ,url: this.config.url                        ,params: {                            action: 'mgr/cronjobs/deleteMulti'                            ,ids: cs                        }                        ,listeners: {                            'success': {                                fn: function(r) {                                    this.refresh();                                }                                ,scope: this                            }                        }                    });                }            });        } else {            // Single row selected            m.push({                text: _('cronmanager.log_view_full')                ,handler: this.viewLog            });        }        this.addContextMenuItem(m);    }    // View full log    ,viewLog: function(btn, e) {        if (!this.fullLog) {            this.fullLog = MODx.load({                xtype: 'cronmanager-window-fulllog'                ,record: this.menu.record                ,listeners: {                    success: {                        fn: this.refresh                        ,scope: this                    }                }            });        }        this.fullLog.setValues(this.menu.record);        this.fullLog.show(e.target);    }    // Filter logs    ,filter: function(cb, rec, ri) {        var s = this.getStore();        switch (rec.data.v) {            case '0':                s.setBaseParam('error', 0);                break;            case '1':                s.setBaseParam('error', 1);                break;            case 'all':                s.setBaseParam('error', 'all');                break;        }        this.getBottomToolbar().changePage(1);        s.removeAll();        this.refresh();    }    ,purgeLogs: function() {        var grid = Ext.getCmp('cronmanager-grid-cronjoblog');        if (!grid) return false;        MODx.msg.confirm({            title: _('cronmanager.logs_purge_title')            ,text: _('cronmanager.logs_purge_confirm')            ,url: grid.url            ,params: {                action: 'mgr/cronjobs/purgeLogs'                ,cronjob: grid.baseParams.cronid                //,error_only: (type == 'all') ? '0' : '1'            }            ,listeners: {                success: {                    fn: function(r) {                        //console.log('response: ' + r.message);                        grid.refresh();                    }                    ,scope: this                }            }        });    }});Ext.reg('cronmanager-grid-cronjoblog', CronManager.grid.CronJobLog);/** * @class CronManager.window.FullLog * @extends MODx.Window * @param config * @xtype cronmanager-window-fulllog */CronManager.window.FullLog = function(config) {    config = config || {};    Ext.applyIf(config, {        title: config.record.logdate        ,fields: [{            xtype: 'textarea'            ,name: 'message'            ,anchor: '100%'            ,readOnly: true            ,height: '150'        }]        ,buttons: [{            text: _('close')            ,scope: this            ,handler: function() { this.hide(); }        }]        ,keys: []    });    CronManager.window.FullLog.superclass.constructor.call(this, config);};Ext.extend(CronManager.window.FullLog, MODx.Window);Ext.reg('cronmanager-window-fulllog', CronManager.window.FullLog);/** * @class CronManager.combo.Error * @extends Ext.form.ComboBox * @param config * @xtype cronmanager-combo-error */CronManager.combo.Error = function(config) {    config = config || {};    Ext.applyIf(config, {        store: new Ext.data.SimpleStore({            fields: ['d', 'v']            ,data: [                [_('cronmanager.logs_filter_all'), 'all']                ,[_('cronmanager.logs_filter_no_error'), '0']                ,[_('cronmanager.logs_filter_error'), '1']            ]        })        ,displayField: 'd'        ,valueField: 'v'        ,value: 'all'        ,mode: 'local'        ,name: 'error'        ,hiddenName: 'error'        ,triggerAction: 'all'        ,editable: false        ,selectOnFocus: false        ,listWidth: 0    });    CronManager.combo.Error.superclass.constructor.call(this, config);};Ext.extend(CronManager.combo.Error, Ext.form.ComboBox);Ext.reg('cronmanager-combo-error', CronManager.combo.Error);