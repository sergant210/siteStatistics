siteStatistics.grid.Users = function (config) {
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
			action: 'mgr/users/getlist'
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
	siteStatistics.grid.Users.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics.grid.Users, MODx.grid.Grid, {
	windows: {},

	getFields: function (config) {
		return ['user_key', 'fullname', 'context'];
	},

	getColumns: function (config) {
		return [{
			header: _('sitestatistics_item_id'),
			dataIndex: 'user_key',
			sortable: true,
			width: 70,
			hidden: true
		}, {
			header: _('stat_online_users'),
			dataIndex: 'fullname',
			sortable: true,
			width: 400
		}, {
			header: _('sitestatistics_context'),
			dataIndex: 'context',
			sortable: false,
			width: 100
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
Ext.reg('sitestatistics-grid-online-users', siteStatistics.grid.Users);