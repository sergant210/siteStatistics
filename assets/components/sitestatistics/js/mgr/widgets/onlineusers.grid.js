siteStatistics.grid.OnlineUsers = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'sitestatistics-grid-online-users';
	}
	Ext.applyIf(config, {
		url: siteStatistics.config.connector_url,
		fields: this.getFields(config),
		columns: this.getColumns(config),
		tbar: this.getTopBar(config),
		baseParams: {
			action: 'mgr/onlineusers/getlist'
		},
		viewConfig: {
			forceFit: true,
			enableRowBody: true,
			autoFill: true,
			showPreview: true,
			scrollOffset: 0
		},
		paging: true,
		remoteSort: true,
		autoHeight: true
	});
	siteStatistics.grid.OnlineUsers.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics.grid.OnlineUsers, MODx.grid.Grid, {
	windows: {},

	getFields: function (config) {
		return ['user_key', 'fullname','date', 'rid', 'context', 'ip', 'user_agent'];
	},

	getColumns: function (config) {
		return [{
			header: 'User key',
			dataIndex: 'user_key',
			sortable: true,
			width: 70,
			hidden: true
		}, {
			header: _('sitestatistics_users'),
			dataIndex: 'fullname',
			sortable: true,
			width: 200
		}, {
			header: _('sitestatistics_resource'),
			dataIndex: 'rid',
			sortable: true,
			width: 200
		}, {
			header: _('stat_online_time'),
			dataIndex: 'date',
			sortable: true,
			width: 70
		}, {
			header: _('sitestatistics_context'),
			dataIndex: 'context',
			sortable: false,
			width: 50
		}, {
			header: _('sitestatistics_ip'),
			dataIndex: 'ip',
			sortable: false,
			fixed: false,
			width: 50
		}, {
			header: _('sitestatistics_user_agent'),
			dataIndex: 'user_agent',
			sortable: false,
			fixed: false,
			width: 180
		}];
	},

	getTopBar: function (config) {
		return [{
			text: '<i class="icon icon-refresh">&nbsp;' + _('stat_online_refresh'),
			handler: this._refresh,
			scope: this
		}];
	},
	_refresh: function () {
		//this.getStore().baseParams.query ='';
		this.refresh();
	}
});
Ext.reg('sitestatistics-grid-online-users', siteStatistics.grid.OnlineUsers);