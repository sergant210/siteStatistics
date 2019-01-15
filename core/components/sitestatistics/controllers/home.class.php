<?php

/**
 * The home manager controller for siteStatistics.
 *
 */
class siteStatisticsHomeManagerController extends modExtraManagerController {
	/* @var siteStatistics $siteStatistics */
	public $siteStatistics;


	/**
	 * @param array $scriptProperties
	 */
	public function process(array $scriptProperties = array()) {
	}

    /**
     * @return array
     */
    public function getLanguageTopics() {
        return array('sitestatistics:default');
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
    public function initialize() {
        $corePath = $this->modx->getOption('sitestatistics_core_path', null, $this->modx->getOption('core_path') . 'components/sitestatistics/');
        require_once $corePath . 'model/sitestatistics/sitestatistics.class.php';

        $this->siteStatistics = new siteStatistics($this->modx);

        parent::initialize();
    }

	/**
	 * @return void
	 */
	public function loadCustomCssJs() {
	    // CSS
		$this->addCss($this->siteStatistics->config['cssUrl'] . 'mgr/main.css');
		$this->addCss($this->siteStatistics->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
		// JS
        $this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/sitestatistics.js');
		$this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/users.windows.js');
        $this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/stats.windows.js');
        $this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/stats.grid.js');
        $this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/users.grid.js');
        $this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/onlineusers.grid.js');
		$this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/widgets/home.panel.js');
		$this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/sections/home.js');
		$this->addHtml('<script>
		Ext.onReady(function() {
			MODx.load({ xtype: "sitestatistics-page-home"});
		});
		</script>');
        // Make context combo
        $q = $this->modx->newQuery('modContext');
        $q->select('key');
        $q->where(array('key:!='=>'mgr'));
        $q->prepare();
        $q->stmt->execute();
        $ctx = '[';
        while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($ctx != '[') $ctx .= ',';
            $ctx .= "['". $row['key']."','". $row['key']."']";
        }
        $ctx .= ']';

        $this->addHtml('
		<script>
			siteStatistics.config = ' . $this->modx->toJSON($this->siteStatistics->config) . ';
			siteStatistics.config.connector_url = "' . $this->siteStatistics->config['connectorUrl'] . '";
			siteStatistics.config.periods = ' . "[['day','".$this->modx->lexicon('day')."'],['month','".$this->modx->lexicon('month')."'],['year','".$this->modx->lexicon('year')."']]" . ';
			siteStatistics.config.contexts = '.$ctx.';
		</script>
		');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->siteStatistics->config['templatesPath'] . 'home.tpl';
	}
}