siteStatistics.grid.Statistics = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'sitestatistics-grid-statistics';
	}
	Ext.applyIf(config, {
		url: siteStatistics.config.connector_url,
		fields: this.getFields(config),
		columns: this.getColumns(config),
		tbar: this.getTopBar(config),
		sm: new Ext.grid.CheckboxSelectionModel(),
		baseParams: {
			action: 'mgr/statistics/getlist'
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
	siteStatistics.grid.Statistics.superclass.constructor.call(this, config);

	// Clear selection on grid refresh
	this.store.on('load', function () {
		if (this._getSelectedIds().length) {
			this.getSelectionModel().clearSelections();
		}
	}, this);
};
Ext.extend(siteStatistics.grid.Statistics, MODx.grid.Grid, {
	windows: {},

	getMenu: function (grid, rowIndex) {
		var ids = this._getSelectedIds();
		var row = grid.getStore().getAt(rowIndex);
		var menu = siteStatistics.utils.getMenu(row.data['actions'], this, ids);

		this.addContextMenuItem(menu);
	},

	removeStatistics: function (grid, rowIndex) {
		var ids = this._getSelectedIds();
		if (!ids.length) {
			return false;
		}
		MODx.msg.confirm({
			title: _('sitestatistics_item_remove'),
			text:  _('sitestatistics_item_remove_confirm'),
			url: this.config.url,
			params: {
				action: 'mgr/statistics/remove',
				ids: Ext.util.JSON.encode(ids)
			},
			listeners: {
				success: {
					fn: function (r) {
						this.refresh();
					}, scope: this
				}
			}
		});
		return true;
	},

	getFields: function (config) {
		return ['idx','rid', 'pagetitle', 'period', 'context', 'period_name', 'display_date', 'date', 'month', 'year', 'users', 'views', 'actions'];
	},

	getColumns: function (config) {
		return [{
			header: 'N',
			dataIndex: 'idx',
			sortable: true,
			width: 20
		}, {
			header: 'ID',
			dataIndex: 'rid',
			sortable: true,
			width: 30
		}, {
			header: _('sitestatistics_pagetitle'),
			dataIndex: 'pagetitle',
			sortable: false,
			width: 350
		}, {
			header: _('sitestatistics_context'),
			dataIndex: 'context',
			sortable: false,
			width: 100
		}, {
			header: _('sitestatistics_period'),
			dataIndex: 'period_name',
			sortable: false,
			width: 100
		}, {
			header: _('sitestatistics_date'),
			dataIndex: 'display_date',
			sortable: true,
			width: 100
		}, {
			header: _('sitestatistics_date'),
			dataIndex: 'date',
			sortable: false,
			hidden: true,
			width: 100
		}, {
			header: _('month'),
			dataIndex: 'month',
			sortable: false,
			hidden: true,
			width: 50
		}, {
			header: _('year'),
			dataIndex: 'year',
			sortable: false,
			hidden: true,
			width: 70
		}, {
			header: _('sitestatistics_users'),
			dataIndex: 'users',
			sortable: true,
			width: 100
		}, {
			header: _('sitestatistics_views'),
			dataIndex: 'views',
			sortable: true,
			width: 100
		}, {
			header: 'Period',
			dataIndex: 'period',
			sortable: false,
			hidden: true,
			width: 70
		}, {
			header: _('sitestatistics_grid_actions'),
			dataIndex: 'actions',
			renderer: siteStatistics.utils.renderActions,
			sortable: false,
			width: 40,
			id: 'actions'
		}];
	},

	getTopBar: function (config) {
		return ['->', {
			xtype: 'xcheckbox',
			boxLabel: _('sitestatistics_show_total'),
			id: config.id + '-show-total',
			name: 'show_total',
			cls: 'sitestatistics-show-total',
			fieldLabel: 'total',
			value: 1,
			checked: false
		}, {
			xtype: 'sitestatistics-combo-context',
			name: 'context',
			width: 110,
			emptyText: _('sitestatistics_context'),
			style: {marginLeft: '20px'},
			id: config.id + '-context-field'
		}, {
			xtype: 'sitestatistics-combo-period',
			name: 'period',
			width: 110,
			emptyText: _('sitestatistics_period'),
			style: {marginLeft: '20px'},
			id:  config.id + '-period-field',
			listeners: {
				select: {
					fn: function (combo, rec, index) {
						this._change_format(combo,index)
					}, scope: this
				}
			}
		}, {
			xtype: 'datefield',
			name: 'date',
			width: 110,
			id:  config.id + '-date-field',
			hidden: true
		}, {
			xtype: 'datefield',
			name: 'display_date',
			format: 'd.m.Y',
			style: {fontSize: '13px',paddingRight:'0'},
			emptyText: _('sitestatistics_date'),
			width: 110,
			startDay:1,
			submitValue: false,
			id:  config.id + '-display-date-field',
			listeners: {
				select: {
					fn: function (el, date) {
						Ext.getCmp(this.config.id + '-date-field').setValue(date);
					}, scope: this
				}
			}
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
							this._doSearch();
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

	onClick: function (e) {
		var elem = e.getTarget();
		if (elem.nodeName == 'BUTTON') {
			var row = this.getSelectionModel().getSelected();
			if (typeof(row) != 'undefined') {
				var action = elem.getAttribute('action');
				if (action == 'showMenu') {
					var ri = this.getStore().find('id', row.id);
					return this._showMenu(this, ri, e);
				}
				else if (typeof this[action] === 'function') {
					this.menu.record = row.data;
					return this[action](this, e);
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
			ids.push(selected[i]['data']['rid']+'&'+selected[i]['data']['date']+'&'+selected[i]['data']['month']+'&'+selected[i]['data']['year']+'&'+selected[i]['data']['period']);
		}

		return ids;
	},
	_change_format: function(combo,i){
		var _date = Ext.getCmp(this.config.id + '-display-date-field'),
			date_val = Ext.getCmp(this.config.id + '-date-field').getValue();
		switch (i){
			case 0:
				_date.format = 'd.m.Y';
				break;
			case 1:
				_date.format = 'M, Y';
				break;
			case 2:
				_date.format = 'Y';
				break;
		}
		_date.setValue(date_val);
	},

	_search: function () {
		var s = this.getStore();
		s.baseParams.date = Ext.getCmp(this.config.id +'-date-field').getValue();
		s.baseParams.query = Ext.getCmp(this.config.id+'-search-field').getValue();
		s.baseParams.period = Ext.getCmp(this.config.id +'-period-field').getValue();
		s.baseParams.context = Ext.getCmp(this.config.id +'-context-field').getValue();
		s.baseParams.show_total = Ext.getCmp(this.config.id +'-show-total').getValue() == true ? 1 : 0;
		this.getBottomToolbar().changePage(1);
		//this.refresh();
	},

	_clearSearch: function (btn, e) {
		this.getStore().baseParams.date = '';
		this.getStore().baseParams.query = '';
		this.getStore().baseParams.period = '';
		this.getStore().baseParams.context = '';
		this.getStore().baseParams.show_total = 0;
		Ext.getCmp(this.config.id + '-date-field').setValue('');
		Ext.getCmp(this.config.id + '-display-date-field').setValue('');
		Ext.getCmp(this.config.id + '-period-field').setValue('');
		Ext.getCmp(this.config.id + '-context-field').setValue('');
		Ext.getCmp(this.config.id + '-search-field').setValue('');
		Ext.getCmp(this.config.id + '-show-total').setValue(0);
		this.getBottomToolbar().changePage(1);
	}
});
Ext.reg('sitestatistics-grid-statistics', siteStatistics.grid.Statistics);