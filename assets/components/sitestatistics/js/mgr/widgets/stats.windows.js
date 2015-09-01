siteStatistics.window.StatUsers = function (config) {
	config = config || {};

	Ext.applyIf(config, {
		stateful: false,
		width: 700,
		autoHeight: true,
		layout: 'anchor',
		title: _('sitestatistics_users'),
		//modal: true,
		items: [{
			xtype: 'sitestatistics-stats-users-grid',
			baseParams: {
				action: 'mgr/statistics/getusers',
				data: config.data,
				show_total: config.show_total
			}
		}],
		buttons: [{
			text: _('sitestatistics_close'),
			id: 'sitestatistics-close-btn',
			handler: function(){this.hide();},
			scope: this
		}]
	});
	siteStatistics.window.StatUsers.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics.window.StatUsers, MODx.Window);
Ext.reg('sitestatistics-stats-users-window', siteStatistics.window.StatUsers);

/**************************************************************/

siteStatistics.grid.StatUsersGrid = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		columns: [{
			header: 'ID',
			dataIndex: 'user_key',
			sortable: true,
			hidden: true,
			width: 50
		}, {
			header: _('sitestatistics_user'),
			dataIndex: 'fullname',
			sortable: false,
			width: 100
		}, {
			header: _('sitestatistics_views'),
			dataIndex: 'views',
			sortable: false,
			width: 100
		}],
		fields: ['user_key','fullname','views'],
		url: siteStatistics.config.connector_url,
		viewConfig: {
			forceFit: true,
			enableRowBody: true,
			autoFill: true,
			showPreview: true,
			scrollOffset: 0
		},
		paging: true,
		pageSize: 10,
		remoteSort: true,
		autoHeight: true

	});
	siteStatistics.grid.StatUsersGrid.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics.grid.StatUsersGrid, MODx.grid.Grid);
Ext.reg('sitestatistics-stats-users-grid', siteStatistics.grid.StatUsersGrid);