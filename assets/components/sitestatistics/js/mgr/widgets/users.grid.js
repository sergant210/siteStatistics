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
			/*rowDblClick: function (grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.getStatistics(grid, e, row);
			}*/
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
		return ['user_key', 'fullname','date', 'rid', 'pagetitle','context', 'actions', 'message_showed', 'ip', 'user_agent', 'referer'];
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
			var self = this;
			MODx.Ajax.request({
				url: self.config.url,
				params: {
					action: 'mgr/users/getmessage',
					user_key: users
				},
				listeners: {
					success: {
						fn: function (r) {
							if (self.UserMessageTable) self.UserMessageTable.close();
							self.UserMessageTable = MODx.load({
								xtype: 'sitestatistics-users-message-window',
								id: 'sitestatistics-message-window',
								title: _('sitestatistics_message_title', {name: row.data.fullname}),
								action: 'mgr/users/savemessage',
								listeners: {
									success: {
										fn: function () {
											self.refresh();
										}, scope: self
									},
									failure: {
										fn: function () {
										}, scope: self
									}
								}
							});
							self.UserMessageTable.reset();
							self.UserMessageTable.setValues(r.object);
							self.UserMessageTable.show(e.target);
						}, scope: self
					}
				}
			});
		}
	},
	removeUser: function (grid, rowIndex) {
		var ids = this._getSelectedIds(),
			self = this;
		if (!ids.length) {
			return false;
		}
		Ext.Msg.show({
			title: ids.length > 1
				? _('sitestatistics_users_remove')
				: _('sitestatistics_user_remove'),
			msg: ids.length > 1
				? _('sitestatistics_users_remove_confirm')
				: _('sitestatistics_user_remove_confirm'),
			buttons: Ext.Msg.YESNOCANCEL,
			closable:false,
			fn: function(btn) {
				if (btn != 'cancel') {
					MODx.Ajax.request({
						url: self.config.url,
						scope: self,
						params: {
							action: 'mgr/users/remove',
							ids: Ext.util.JSON.encode(ids),
							remove_page_stats: btn === 'yes'
						},
						listeners: {
							success: {
								fn: function () {
									self.refresh();
								}, scope: self
							},
							failure: {
								fn: function () {
								}, scope: self
							}
						}
					});
				}
			}
		});
		return true;
	},
	getColumns: function (config) {
		return [this.sm,{
			header: 'User key',
			dataIndex: 'user_key',
			sortable: false,
			width: 40,
			hidden: true
		}, {
			header: _('sitestatistics_user'),
			dataIndex: 'fullname',
			sortable: true,
			width: 130
		}, {
			header: _('sitestatistics_resource'),
			dataIndex: 'pagetitle',
			sortable: true,
			width: 130
		}, {
			header: _('sitestatistics_date'),
			dataIndex: 'date',
			sortable: true,
			fixed: false,
			width: 100
		}, {
			header: _('sitestatistics_context'),
			dataIndex: 'context',
			sortable: false,
			fixed: false,
			width: 50
		}, {
			header: _('sitestatistics_ip'),
			dataIndex: 'ip',
			sortable: false,
			fixed: false,
			width: 70
		}, {
			header: _('sitestatistics_user_agent'),
			dataIndex: 'user_agent',
			sortable: false,
			fixed: false,
			editable: true,
			editor:	{xtype: 'textfield'},
			width: 150
		}, {
			header: _('sitestatistics_referer'),
			dataIndex: 'referer',
			sortable: false,
			editable: true,
			editor:	{xtype: 'textfield'},
			fixed: false,
			width: 120
		}, {
			header: _('sitestatistics_msg_showed'),
			dataIndex: 'message_showed',
			sortable: false,
			hidden: true,
			fixed: false,
			width: 120
		}, {
			header: '<i class="icon icon-cog" style="margin-left: 2px;"></i>',
			dataIndex: 'actions',
			renderer: siteStatistics.utils.renderActions,
			sortable: false,
			fixed: true,
			width: 100,
			id: 'actions'
		}];
	},

	getTopBar: function (config) {
		return [{
			text: '<i class="icon icon-refresh">&nbsp;' + _('stat_online_refresh'),
			handler: this._refresh,
			scope: this
		}, '->', {
			xtype: 'datefield',
			name: 'date',
			format: 'd.m.Y',
			style: {fontSize: '13px',paddingRight:'0'},
			emptyText: _('sitestatistics_date'),
			width: 110,
			startDay:1,
			submitValue: false,
			id:  config.id + '-date-field'
		}, {
			xtype: 'textfield',
			name: 'query',
			width: 200,
			id: config.id + '-search-field',
			emptyText: _('sitestatistics_grid_search'),
			listeners: {
				render: {
					fn: function (tf) {
						tf.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
							this._search();
						}, this);
					}, scope: this
				}
			}
		}, {
			xtype: 'button',
			id:  config.id + '-search-btn',
			text: '<i class="icon icon-search"></i>',
			listeners: {
				click: {fn: this._search, scope: this}
			}
		}, {
			xtype: 'button',
			id: config.id + '-search-clear',
			text: '<i class="icon icon-times"></i>',
			listeners: {
				click: {fn: this._clearSearch, scope: this}
			}
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
			ids.push(selected[i]['data']['user_key']);
		}

		return ids;
	},
	_search: function () {
		var s = this.getStore();
		s.baseParams.date = Ext.getCmp(this.config.id +'-date-field').getValue();
		s.baseParams.query = Ext.getCmp(this.config.id+'-search-field').getValue();
		this.getBottomToolbar().changePage(1);
		//this.refresh();
	},

	_clearSearch: function (btn, e) {
		this.getStore().baseParams.date = '';
		this.getStore().baseParams.query = '';
		Ext.getCmp(this.config.id + '-date-field').setValue('');
		Ext.getCmp(this.config.id + '-search-field').setValue('');
		this.getBottomToolbar().changePage(1);
	}
});
Ext.reg('sitestatistics-grid-users', siteStatistics.grid.Users);