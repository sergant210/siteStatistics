siteStatistics.grid.Users = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'sitestatistics-grid-users';
	}
	this.sm = new Ext.grid.CheckboxSelectionModel();
	Ext.applyIf(config, {
		url: siteStatistics.config.connector_url,
		fields: this.getFields(config),
		columns: this.getColumns(config),
		tbar: this.getTopBar(config),

		sm: this.sm,
		baseParams: {
			action: 'mgr/users/getlist'
		},
		listeners: {
			rowDblClick: function (grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.getStatistics(grid, e, row);
			}
		},
		viewConfig: {
			forceFit: true,
			enableRowBody: true,
			autoFill: true,
			showPreview: true,
			scrollOffset: 0
		},
		primaryKey: 'user_key',
		paging: true,
		remoteSort: true,
		autoHeight: true
	});
	siteStatistics.grid.Users.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics.grid.Users, MODx.grid.Grid, {
	windows: {},

	getMenu: function (grid, rowIndex) {
		var ids = this._getSelectedIds();
		var row = grid.getStore().getAt(rowIndex);
		var menu = siteStatistics.utils.getMenu(row.data['actions'], this, ids);

		this.addContextMenuItem(menu);
	},
	getFields: function (config) {
		return ['user_key', 'fullname','date', 'rid', 'context','actions','message_showed'];
	},
	getStatistics: function (btn, e, row) {
		var record = typeof(row) != 'undefined'
			? row.data
			: this.menu.record;
		if (this.UserStatTable) this.UserStatTable.close();
		this.UserStatTable = MODx.load({
			xtype: 'sitestatistics-users-visits-window',
			title: _('sitestatistics_visits_title', {name: record.fullname}),
			user_key: record.user_key
		});

		this.UserStatTable.show(Ext.EventObject.target);
	},
	sendMessage: function (btn, e, row) {
		var users = this.getSelectedAsList();
		if (users === false) return false;
		if (users.split(',').length > 1) {
			if (this.UserMessageTable) this.UserMessageTable.close();
			this.UserMessageTable = MODx.load({
				xtype: 'sitestatistics-users-message-window',
				id: 'sitestatistics-message-window',
				title: _('sitestatistics_message'),
				action: 'mgr/users/savemessages',
				listeners: {
					success: {
						fn: function () {
							this.refresh();
						}, scope: this
					},
					failure: {
						fn: function () {}, scope: this
					}
				}
			});
			this.UserMessageTable.reset();
			this.UserMessageTable.setValues({user_key:users});
			this.UserMessageTable.show(e.target);
		} else {
			MODx.Ajax.request({
				url: this.config.url,
				params: {
					action: 'mgr/users/getmessage',
					user_key: users
				},
				listeners: {
					success: {
						fn: function (r) {
							if (this.UserMessageTable) this.UserMessageTable.close();
							this.UserMessageTable = MODx.load({
								xtype: 'sitestatistics-users-message-window',
								id: 'sitestatistics-message-window',
								title: _('sitestatistics_message_title', {name: row.data.fullname}),
								action: 'mgr/users/savemessage',
								listeners: {
									success: {
										fn: function () {
											this.refresh();
										}, scope: this
									},
									failure: {
										fn: function () {
										}, scope: this
									}
								}
							});
							this.UserMessageTable.reset();
							this.UserMessageTable.setValues(r.object);
							this.UserMessageTable.show(e.target);
						}, scope: this
					}
				}
			});
		}
	},
	getColumns: function (config) {
		return [this.sm,{
			header: 'User key',
			dataIndex: 'user_key',
			sortable: false,
			width: 40,
			hidden: true
		}, {
			header: _('sitestatistics_users'),
			dataIndex: 'fullname',
			sortable: true,
			width: 500
		}, {
			header: _('sitestatistics_resource'),
			dataIndex: 'rid',
			sortable: true,
			width: 100
		}, {
			header: _('sitestatistics_date'),
			dataIndex: 'date',
			sortable: true,
			fixed: true,
			width: 150
		}, {
			header: _('sitestatistics_context'),
			dataIndex: 'context',
			sortable: false,
			fixed: true,
			width: 80
		}, {
			header: _('sitestatistics_msg_showed'),
			dataIndex: 'message_showed',
			sortable: false,
			fixed: true,
			width: 150
		}, {
			header: '<i class="icon icon-cog" style="margin-left: 2px;"></i>',
			dataIndex: 'actions',
			renderer: siteStatistics.utils.renderActions,
			sortable: false,
			fixed: true,
			width: 70,
			id: 'actions'
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
	},
	onClick: function (e) {
		var elem = e.getTarget();
		if (elem.nodeName == 'BUTTON') {
			var row = this.getSelectionModel().getSelected();
			if (typeof(row) != 'undefined') {
				var action = elem.getAttribute('action');
				if (action == 'showMenu') {
					var ri = this.getStore().find('user_key', row.id);
					return this._showMenu(this, ri, e);
				}
				else if (typeof this[action] === 'function') {
					this.menu.record = row.data;
					return this[action](this, e, row);
				}
			}
		}
		return this.processEvent('click', e);
	},
	_getSelectedIds: function () {
		var ids = [];
		var selected = this.getSelectionModel().getSelections();

		for (var i in selected) {
			if (!selected.hasOwnProperty(i)) {
				continue;
			}
			ids.push(selected[i]['user_key']);
		}

		return ids;
	}
});
Ext.reg('sitestatistics-grid-users', siteStatistics.grid.Users);