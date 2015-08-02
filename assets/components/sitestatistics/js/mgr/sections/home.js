siteStatistics.page.Home = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'sitestatistics-panel-home', renderTo: 'sitestatistics-panel-home-div'
		}]
	});
	siteStatistics.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics.page.Home, MODx.Component);
Ext.reg('sitestatistics-page-home', siteStatistics.page.Home);