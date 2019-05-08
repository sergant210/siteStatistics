var SiteStatistics = function (config) {
	config = config || {};
	SiteStatistics.superclass.constructor.call(this, config);
};
Ext.extend(SiteStatistics, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('sitestatistics', SiteStatistics);

siteStatistics = new SiteStatistics();