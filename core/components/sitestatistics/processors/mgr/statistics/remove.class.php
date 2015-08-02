<?php

/**
 * Remove an Items
 */
class siteStatisticsRemoveProcessor extends modObjectProcessor {
	public $objectType = 'StatPageStatistics';
	public $classKey = 'StatPageStatistics';
	public $languageTopics = array('sitestatistics');
	//public $permission = 'remove';


	/**
	 * @return array|string
	 */
	public function process() {
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}
		//return $this->success();
		$_ids = $this->modx->fromJSON($this->getProperty('ids'));
		if (empty($_ids)) {
			return $this->failure($this->modx->lexicon('sitestatistics_item_err_ns'));
		}
		$ids = array();
		$delete_table = false;
		foreach ($_ids as $id) {
			list($rid,$date,$month,$year,$period) = explode('&', $id);
			$where = array();
			switch ($period) {
				case '':
					if (empty($rid)) {
						$delete_table = true;
					} else {
						$where = array('rid'=>$rid);
					}
					break;
				case 'day':
					if (empty($rid)) {
						$where = array('date'=>$date);
					} else {
						$where = array('rid'=>$rid,'date'=>$date);
					}
					break;
				case 'month':
					if (empty($rid)) {
						$where = array('month'=>$month);
					} else {
						$where = array('rid'=>$rid,'month'=>$month);
					}
					break;
				case 'year':
					if (empty($rid)) {
						$where = array('year'=>$year);
					} else {
						$where = array('rid'=>$rid,'year'=>$year);
					}
					break;
			}
			$ids[] = $where;
		}
		foreach ($ids as $where) {
			if (empty($where) && !$delete_table) continue;
			/** @var xPDOquery $q */
			$q = $this->modx->newQuery('StatPageStatistics');
			$q->command('delete');
			//$q->select('rid');
			$q->where($where);
			$q->prepare();
//$this->modx->log(modX::LOG_LEVEL_ERROR,print_r($q->toSQL(),true) );
			$q->stmt->execute();

		}
//$this->modx->log(modX::LOG_LEVEL_ERROR, print_r($ids,true));
		/*
		foreach ($ids as $id) {
			// @var StatPageStatistics $object
			if (!$object = $this->modx->getObject($this->classKey, $id)) {
				return $this->failure($this->modx->lexicon('sitestatistics_item_err_nf'));
			}

			//$object->remove();
		}
	*/

		return $this->success();
	}

}

return 'siteStatisticsRemoveProcessor';