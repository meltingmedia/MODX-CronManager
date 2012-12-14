CronManager.grid.CronJobs = function(config) {    config = config || {};    this.exp = new Ext.grid.RowExpander({        tpl : new Ext.Template(            '<p class="desc">{snippet_description}</p>'        )    });    Ext.applyIf(config,{        id: 'cronmanager-grid-cronjobs',		url: CronManager.config.connectorUrl,		baseParams: { action: 'mgr/cronjobs/getList' },		save_action: 'mgr/cronjobs/updateFromGrid',		autosave: true,		fields: ['id','snippet','snippet_name','properties','minutes','nextrun','lastrun','active','sortorder','snippet_description', 'logs'],        plugins: this.exp,		paging: true,		remoteSort: true,		anchor: '97%',		autoExpandColumn: 'snippet',		emptyText: _('cronmanager.norecords'),		columns: [this.exp ,{            header: _('id'),			dataIndex: 'id',			sortable: true,			width: 25        },{            header: _('cronmanager.snippet'),			dataIndex: 'snippet_name',			sortable: true        },{            header: _('cronmanager.minutes'),			dataIndex: 'minutes',			sortable: false,			width: 40,			editor: {                xtype: 'numberfield'                ,minValue: 1                ,description: _('cronmanager.minutes_desc')                ,allowNegative: false            }        },{            header: _('cronmanager.lastrun'),			dataIndex: 'lastrun',			sortable: false        },{            header: _('cronmanager.nextrun'),			dataIndex: 'nextrun',			sortable: false,			editor: {                xtype: 'xdatetime',				dateFormat: MODx.config.manager_date_format,				timeFormat: MODx.config.manager_time_format            }        },{            header: _('cronmanager.logs_entries')            ,dataIndex: 'logs'            ,width: 40            ,sortable: false        },{            header: _('cronmanager.active'),			dataIndex: 'active',			sortable: false,			width: 40,			renderer: this.renderYNfield.createDelegate(this,[this],true),			editor: { xtype: 'combo-boolean' }        }],		tbar:[{			text: _('cronmanager.create'),			handler: {				xtype: 'cronmanager-window-create',				blankValues: true			}		}]    });    CronManager.grid.CronJobs.superclass.constructor.call(this, config)};Ext.extend(CronManager.grid.CronJobs, MODx.grid.Grid);Ext.extend(CronManager.grid.CronJobs, MODx.grid.Grid, {	getMenu: function() {		var m = [{			text: _('cronmanager.update'),			handler: this.updateCronjob		},{			text: _('cronmanager.viewlog'),			handler: this.viewLog		},'-',{			text: _('cronmanager.remove'),			handler: this.removeCronjob		}];		this.addContextMenuItem(m);		return true;	},	updateCronjob: function(btn, e) {		if(!this.updateCronjobWindow) {			this.updateCronjobWindow = MODx.load({				xtype: 'cronmanager-window-update',				record: this.menu.record,				listeners: {					'success': { fn: this.refresh, scope: this }				}			});		}		this.updateCronjobWindow.setValues(this.menu.record);		this.updateCronjobWindow.show(e.target);    },	viewLog: function(btn, e) {		if (!this.menu.record || !this.menu.record.id) return false;        location.href = '?a=' + MODx.request.a + '&action=viewlog&id=' + this.menu.record.id;	},	removeCronjob: function() {		MODx.msg.confirm({			title: _('cronmanager.remove'),			text: _('cronmanager.remove_confirm', { snippet: '<b>'+this.menu.record.snippet_name+'</b>' }),			url: this.config.url,			params: {				action: 'mgr/cronjobs/remove',				id: this.menu.record.id			},			listeners: {				'success': { fn:this.refresh, scope:this }			}		});    },	renderYNfield: function(v,md,rec,ri,ci,s,g) {        var r = s.getAt(ri).data;        v = Ext.util.Format.htmlEncode(v);        var f = MODx.grid.Grid.prototype.rendYesNo;        return f(v,md,rec,ri,ci,s,g);    }});Ext.reg('cronmanager-grid-cronjobs', CronManager.grid.CronJobs);// --------------------------// Create windowCronManager.window.Create = function(config) {    config = config || {};    Ext.applyIf(config,{		title: _('cronmanager.create'),		url: CronManager.config.connectorUrl,		baseParams: {			action: 'mgr/cronjobs/create'		},		fields: [{			xtype: 'cronmanager-combo-snippets',			fieldLabel: _('cronmanager.snippet'),			name: 'snippet',            anchor: '100%',			allowBlank: false		},{			xtype: 'numberfield',			fieldLabel: _('cronmanager.minutes'),			description: _('cronmanager.minutes_desc'),			name: 'minutes',			width: 60,			value: 60,			minValue: 1,			allowBlank: false,            allowNegative: false		},{			xtype: 'textarea',			fieldLabel: _('cronmanager.properties'),			description: _('cronmanager.properties_desc'),			name: 'properties',            anchor: '100%',			allowBlank: true,			grow: true,			growMax: 200		}],		keys:[{			key: Ext.EventObject.ENTER,			shift: true,			fn: this.submit,			scope: this		}]    });    CronManager.window.Create.superclass.constructor.call(this, config);};Ext.extend(CronManager.window.Create, MODx.Window);Ext.reg('cronmanager-window-create', CronManager.window.Create);// --------------------------// Update windowCronManager.window.Update = function(config) {    config = config || {};    Ext.applyIf(config,{        title: _('cronmanager.update'),		url: CronManager.config.connectorUrl,		baseParams: {            action: 'mgr/cronjobs/update'        },		fields: [{			xtype: 'hidden',			name: 'id'		},{			xtype: 'cronmanager-combo-snippets',			fieldLabel: _('cronmanager.snippet'),			name: 'snippet',            anchor: '100%',			allowBlank: false		},{			xtype: 'numberfield',			fieldLabel: _('cronmanager.minutes'),			description: _('cronmanager.minutes_desc'),			name: 'minutes',			width: 60,			minValue: 1,			allowBlank: false,            allowNegative: false		},{			xtype: 'textarea',			fieldLabel: _('cronmanager.properties'),			description: _('cronmanager.properties_desc'),			name: 'properties',            anchor: '100%',			allowBlank: true,			grow: true,			growMax: 200		}],		keys:[{			key: Ext.EventObject.ENTER,			shift: true,			fn: this.submit,			scope: this		}]    });    CronManager.window.Update.superclass.constructor.call(this, config);};Ext.extend(CronManager.window.Update, MODx.Window);Ext.reg('cronmanager-window-update', CronManager.window.Update);