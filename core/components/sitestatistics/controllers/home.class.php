<?php

/**
 * The home manager controller for siteStatistics.
 *
 */
class siteStatisticsHomeManagerController extends siteStatisticsMainController {
	/* @var siteStatistics $siteStatistics */
	public $siteStatistics;


	/**
	 * @param array $scriptProperties
	 */
	public function process(array $scriptProperties = array()) {
	}


	/**
	 * @return null|string
	 */
	public function getPageTitle() {
		return $this->modx->lexicon('sitestatistics');
	}


	/**
	 * @return void
	 */
	public function loadCustomCssJs() {
		$this->addCss($this->siteStatistics->config['cssUrl'] . 'mgr/main.css');
		$this->addCss($this->siteStatistics->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
		$this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/users.windows.js');
        $this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/stats.windows.js');
        $this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/stats.grid.js');
        $this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/users.grid.js');
        $this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/onlineusers.grid.js');
		$this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/home.panel.js');
		$this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/sections/home.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "sitestatistics-page-home"});
		});
		</script>');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->siteStatistics->config['templatesPath'] . 'home.tpl';
	}
}