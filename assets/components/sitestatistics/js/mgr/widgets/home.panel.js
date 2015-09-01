siteStatistics.panel.Home = function (config) {
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		hideMode: 'offsets',
		items: [{
			html: '<h2>' + _('sitestatistics_title') + '</h2>',
			cls: '',
			style: {margin: '15px 0'},
			id: 'sitestatistics-panel-title'
		}, {
			xtype: 'modx-tabs',
			defaults: {border: false, autoHeight: true},
			border: true,
			hideMode: 'offsets',
			items: [{
				title: _('stat_tab_title'),
				layout: 'anchor',
				items: [{
					xtype: 'sitestatistics-grid-statistics',
					cls: 'main-wrapper'
				}]
			}, {
				title: _('users_tab_title'),
				layout: 'anchor',
				items: [{
					xtype: 'sitestatistics-grid-users',
					cls: 'main-wrapper'
				}]
			}, {
				title: _('onlineusers_tab_title'),
				layout: 'anchor',
				items: [{
					xtype: 'sitestatistics-grid-online-users',
					cls: 'main-wrapper'
				}]
			}]
		}],
		listeners: {
			'render': function (p) {
				MODx.Ajax.request({
					url: siteStatistics.config.connector_url,
					params: {
						action: 'mgr/package/checkupdate'
					},
					listeners: {
						success: {
							fn: function (r) {
								Ext.getCmp('sitestatistics-panel-title').html ='<h2>' + _('sitestatistics_title') + ' ' + _('sitestatistics_package_update') + '</h2>';
							}, scope: this
						}
					}
				});
			}
		}
	});
	siteStatistics.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics.panel.Home, MODx.Panel);
Ext.reg('sitestatistics-panel-home', siteStatistics.panel.Home);
