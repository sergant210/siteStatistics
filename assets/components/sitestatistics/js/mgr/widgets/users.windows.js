siteStatistics.window.UserVisits = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'sitestatistics-visits-window';
	}
	Ext.applyIf(config, {
		autoHeight: true,
		stateful: true,
		//modal: true,
		items: [{
			xtype: 'sitestatistics-user-stats-grid',
			//id: 'sitestatistics-user-stats-grid',
			baseParams: {
				action: 'mgr/users/userstatistics',
				user_key: config.user_key
			}
		}],
		buttons: [{
			text: _('sitestatistics_close'),
			id: 'sitestatistics-close-btn',
			handler: function(){this.hide();},
			scope: this
		}]
	});
	siteStatistics.window.UserVisits.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics.window.UserVisits, MODx.Window);
Ext.reg('sitestatistics-users-visits-window', siteStatistics.window.UserVisits);

/**************************************************************/

siteStatistics.grid.UserStatGrid = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		columns: [{
			header: _('month'),
			dataIndex: 'month',
			sortable: true,
			hidden: true,
			width: 50
		}, {
			header: _('sitestatistics_date'),
			dataIndex: 'date',
			sortable: true,
			width: 100
		}, {
			header: _('sitestatistics_resource'),
			dataIndex: 'rid',
			sortable: false,
			width: 100
		}, {
			header: _('sitestatistics_views'),
			dataIndex: 'views',
			sortable: false,
			width: 100
		}],
		fields: ['user_key','date','views','rid','month'],
		url: siteStatistics.config.connector_url,
		paging: true,
		pageSize: 10,
		remoteSort: true,
		autoHeight: true,
		//
		singleText: _('sitestatistics_record'),
		pluralText: _('sitestatistics_records'),
		grouping: true,
		groupBy: 'month',
		sortBy: 'date',
		sortDir: 'DESC',
		tools: [{
			id: 'plus'
			,qtip: _('sitestatistics_expand_all')
			,handler: this.expandAll
			,scope: this
		},{
			id: 'minus'
			,hidden: true
			,qtip: _('sitestatistics_collapse_all')
			,handler: this.collapseAll
			,scope: this
		}]

	});

	this.view = new Ext.grid.GroupingView({
		emptyText: 'Empty'
		,forceFit: true
		,autoFill: true
		,showPreview: true
		,enableRowBody: true
		,scrollOffset: 0
	});
	siteStatistics.grid.UserStatGrid.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics.grid.UserStatGrid, MODx.grid.Grid);
Ext.reg('sitestatistics-user-stats-grid', siteStatistics.grid.UserStatGrid);

/******************************************************************/

siteStatistics.window.Message = function (config) {
	config = config || {};

	Ext.applyIf(config, {
		width: 800,
		autoHeight: true,
		maxHeight: 500,
		layout: 'anchor',
		stateful: false,
		//modal: true,
		fields: [{
			xtype: "hidden",
			name: "user_key",
			hideLabel: true
		}, {
			xtype: "textarea",
			name: "message",
			hideLabel: true,
			anchor: "100%",
			id: "sitestatistics-message-content"
			,height: 100
		},{
			xtype: 'xcheckbox',
			boxLabel: _('sitestatistics_show_message'),
			name: 'show_message',
			id: 'sitestatistics-show-message',
			style: {paddingTop:'0px'},
			checked: true
		}],
		url: siteStatistics.config.connector_url,
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	siteStatistics.window.Message.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics.window.Message, MODx.Window);
Ext.reg('sitestatistics-users-message-window', siteStatistics.window.Message);