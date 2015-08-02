var siteStatistics = function (config) {
	config = config || {};
	siteStatistics.superclass.constructor.call(this, config);
};
Ext.extend(siteStatistics, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('sitestatistics', siteStatistics);

siteStatistics = new siteStatistics();