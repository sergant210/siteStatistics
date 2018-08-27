<?php

/**
 * Get a list of users
 */
class siteStatisticsUsersGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'sitestatistics_item';
	public $classKey = 'PageStatistics';
	public $defaultSortField = 'date';
	public $defaultSortDirection = 'DESC';
	public $permission = 'list_statistics';


	/**
	 * * We doing special check of permission
	 * because of our objects is not an instances of modAccessibleObject
	 *
	 * @return boolean|string
	 */
	public function beforeQuery() {
		if (!$this->checkPermissions()) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->select('user_key,date,rid,views, pagetitle');
        $c->innerJoin('modResource', 'Resource');
        $c->where(array('user_key'=>$this->getProperty('user_key')));
        return $c;
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $user = $object->toArray();
        $month = 'month'. date('n',strtotime($user['date']));
        $user['month'] = $this->modx->lexicon($month).' '. date('Y',strtotime($user['date']));
        $user['date'] = $this->modx->lexicon($month).' '.date('j, Y',strtotime($user['date']));

        return $user;
    }

}

return 'siteStatisticsUsersGetListProcessor';