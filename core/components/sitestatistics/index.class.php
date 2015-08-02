<?php

/**
 * Class siteStatisticsMainController
 */
abstract class siteStatisticsMainController extends modExtraManagerController {
	/** @var siteStatistics $siteStatistics */
	public $siteStatistics;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('sitestatistics_core_path', null, $this->modx->getOption('core_path') . 'components/sitestatistics/');
		require_once $corePath . 'model/sitestatistics/sitestatistics.class.php';

		$this->siteStatistics = new siteStatistics($this->modx);
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
		$this->addJavascript($this->siteStatistics->config['jsUrl'] . 'mgr/sitestatistics.js');
		$this->addHtml('
		<script type="text/javascript">
			siteStatistics.config = ' . $this->modx->toJSON($this->siteStatistics->config) . ';
			siteStatistics.config.connector_url = "' . $this->siteStatistics->config['connectorUrl'] . '";
			siteStatistics.config.periods = ' . "[['day','".$this->modx->lexicon('day')."'],['month','".$this->modx->lexicon('month')."'],['year','".$this->modx->lexicon('year')."']]" . ';
			siteStatistics.config.contexts = '.$ctx.';
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('sitestatistics:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends siteStatisticsMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'home';
	}
}