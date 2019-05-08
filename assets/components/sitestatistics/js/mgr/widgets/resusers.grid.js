siteStatistics.grid.ResUsers = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'sitestatistics-grid-res-users';
	}
	Ext.applyIf(config, {
		url: siteStatistics.config.connector_url,
		fields: this.getFields(config),
		columns: this.getColumns(config),
		tbar: this.getTopBar(config),

		baseParams: {
			action: 'mgr/resource/getlist',
			rid: config.resource
		},
		viewConfig: {
			forceFit: true,
			enableRowBody: true,
			autoFill: true,
			showPreview: true,
			scrollOffset: 0
		},
		pageSize: 10,
		paging: true,
		remoteSort: true,
		autoHeight: true
	});
	siteStatistics.grid.ResUsers.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics.grid.ResUsers, MODx.grid.Grid, {
	windows: {},

	getFields: function (config) {
		return ['user_key', 'fullname','date', 'rid', 'ip', 'user_agent', 'referer', 'actions'];
	},

	getColumns: function (config) {
		return [{
			header: 'User key',
			dataIndex: 'user_key',
			sortable: false,
			width: 40,
			hidden: true
		}, {
			header: _('sitestatistics_user'),
			dataIndex: 'fullname',
			sortable: true,
			width: 100
		}, {
			header: _('sitestatistics_date'),
			dataIndex: 'date',
			sortable: true,
			fixed: false,
			width: 80
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
			width: 200
		}, {
			header: _('sitestatistics_referer'),
			dataIndex: 'referer',
			sortable: false,
			fixed: false,
			width: 200
		}, {
			header: '<i class="icon icon-cog" style="margin-left: 2px;"></i>',
			dataIndex: 'actions',
			renderer: siteStatistics.utils.renderActions,
			sortable: false,
			fixed: true,
			width: 50,
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
Ext.reg('sitestatistics-grid-res-users', siteStatistics.grid.ResUsers);