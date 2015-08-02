<?php

/**
 * The base class for siteStatistics.
 */
class siteStatistics {
	/* @var modX $modx */
	public $modx;
	public $config = array();

	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('sitestatistics_core_path', $config, $this->modx->getOption('core_path') . 'components/sitestatistics/');
		$assetsUrl = $this->modx->getOption('sitestatistics_assets_url', $config, $this->modx->getOption('assets_url') . 'components/sitestatistics/');
		$connectorUrl = $assetsUrl . 'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'connectorUrl' => $connectorUrl,

			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/',

			'count' => 'byday'
		), $config);

		$this->modx->addPackage('sitestatistics', $this->config['modelPath']);
		$this->modx->lexicon->load('sitestatistics:default');
	}
	public function initialize($sp) {
		$this->modx->regClientCSS($this->config['cssUrl'].'web/style.css');
		$this->config = array_merge($this->config, $sp);
	}

	public function setStatistics(){
		$user_id = ($this->modx->user->id != 0) ? $this->modx->user->id : $_SESSION['siteStatistics'];
		$data = array(
			'rid' => $this->modx->resource->get('id'),
			'date' => date('Y-m-d'),
			'uid' => $user_id
		);
		if (!$pageStat = $this->modx->getObject('StatPageStatistics',$data)) {
			$pageStat = $this->modx->newObject('StatPageStatistics');
			$pageStat->set('rid',$data['rid']);
			$pageStat->set('date',$data['date']);
			$pageStat->set('uid',$data['uid']);
			$pageStat->set('month',date('Y-m'));
			$pageStat->set('year',date('Y'));
		}
		$count = $pageStat->get('views');
		$pageStat->set('views',$count+1);
		$pageStat->save();
	}

	/**
	 * @param $resource
	 * @return int|void
	 */
	public function getPageStatistics($resource=0)
	{
		$query = $this->modx->newQuery('StatPageStatistics');
		//$query->setClassAlias('');
		if (!empty($resource) && is_numeric($resource)) {
			$query->where(array(
				'rid' => $resource,
			));
		}
		switch ($this->config['count']) {
			case 'byday':
				$query->groupby('date');
				if (empty($this->config['date'])) {
					$query->where(array(
						'date' => date('Y-m-d'),
					));
				} else {
					$query->where(array(
						'date' => date('Y-m-d', strtotime($this->config['date'])),
					));
				}
				break;
			case 'bymonth':
				$query->groupby('month');
				if (empty($this->config['date'])) {
					$query->where(array(
						'month' => date('Y-m'),
					));
				} else {
					$query->where(array(
						'month' => date('Y-m', strtotime($this->config['date'])),
					));
				}
				break;
			case 'byyear':
				$query->groupby('year');
				if (empty($this->config['date'])) {
					$query->where(array(
						'year' => date('Y'),
					));
				} else {
					$query->where(array(
						'year' => (int) $this->config['date'],
					));
				}
				break;
		}
		if (($this->config['toPlaceholders'] && $this->config['toPlaceholders'] !='false') || $this->config['show'] == 'all') {
			$query->select('COUNT(DISTINCT uid) as users, SUM(views) as views');
			$tstart = microtime(true);
			if ($query->prepare() && $query->stmt->execute()) {
				$this->modx->queryTime += microtime(true) - $tstart;
				$this->modx->executedQueries++;
				$res = $query->stmt->fetch(PDO::FETCH_ASSOC);
			}
			if (empty($res)) $res = array('users'=>0,'views'=>0);
		} else {
			if (trim($this->config['show']) == 'users')
				$query->select('COUNT(DISTINCT uid)');
			else
				$query->select('SUM(views)');
			$res = $this->modx->getValue($query->prepare());
			if (empty($res)) $res = 0;
		}
		return $res;
	}

	/**
	 * @return array
	 */
	public function getSiteStatistics(){
		$this->config['show'] = 'all';
		/** @var array $output */
		$output = $this->getPageStatistics();
		$output = $this->modx->getChunk($this->config['tpl'],$output);
		return $output;
	}

	/**
	 * @param string $user_key
	 */
	public function countOnlineUsers($user_key){
		if ($this->modx->getCount('StatOnlineUsers',$user_key)) {
			$query = $this->modx->newQuery('StatOnlineUsers');
			$query->command('update');
			$query->set(array(
				'date' => date('Y-m-d H:i'),
				'user' => $this->modx->user->id
			));
			$query->where(array('user_key' => $user_key));
			$tstart = microtime(true);
			if ($query->prepare() && $query->stmt->execute()) {
				$this->modx->queryTime += microtime(true) - $tstart;
				$this->modx->executedQueries++;
			}
		} else {
			$data = array(
				$this->modx->quote($user_key),
				$this->modx->quote(date('Y-m-d H:i')),
				$this->modx->user->id,
				"'".$this->modx->context->get('key')."'"
			);
			$sql = "INSERT INTO {$this->modx->getTableName('StatOnlineUsers')} (`user_key`,`date`,`user`,`context`) VALUES (" . implode(',',$data).")";
			$query = $this->modx->prepare($sql);
			if (!$query->execute()) {
				$this->modx->log(modX::LOG_LEVEL_ERROR, '[siteStatistics] Could not save online user data. '.print_r($query->errorInfo(),1));
			}
		}
		// Удаляем неактивных пользователей
		$time = $this->modx->getOption('stat.online_time',15);
		$sql = "DELETE FROM {$this->modx->getTableName('StatOnlineUsers')} WHERE date < NOW() -  INTERVAL '$time' MINUTE";
		$query = $this->modx->prepare($sql);
		if (!$query->execute()) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[siteStatistics] Could not delete inactive online user. '.print_r($query->errorInfo(),1));
		}
	}

	/**
	 * @return array
	 */
	public function getOnlineUsers(){
		$query = $this->modx->newQuery('StatOnlineUsers');
		$query->select('user');
		if (!empty($this->config['ctx'])) $query->where(array('context'=>trim($this->config['ctx'])));
		$tstart = microtime(true);
		$res = array();
		if ($query->prepare() && $query->stmt->execute()) {
			$this->modx->queryTime += microtime(true) - $tstart;
			$this->modx->executedQueries++;
			$res = $query->stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		$users = $guests = 0;
		foreach ($res as $user) {
			if ($user['user'])
				$users++;
			else
				$guests++;
		}
		return array('stat.online_users'=>$users,'stat.online_guests'=>$guests);
	}
}