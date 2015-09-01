<?php

/**
 * Get a list of online users
 */
class siteStatisticsUsersGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'sitestatistics_item';
	public $classKey = 'UserStatistics';
	public $defaultSortField = 'UserStatistics.uid';
	public $defaultSortDirection = 'ASC';
	//public $permission = 'list';


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
        $c->leftJoin('modUserProfile','User');
        $c->select('UserStatistics.user_key, UserStatistics.rid, UserStatistics.context,User.fullname');
        $time = $this->modx->getOption('stat.online_time',null,15);
        $c->where("UserStatistics.date > NOW() -  INTERVAL '$time' MINUTE");

        return $c;
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $user = $object->toArray();
        if (empty($user['fullname'])) $user['fullname'] = $this->modx->lexicon('stat_online_guest');
        $user['rid'] = '<a href="?a=resource/update&id='.$user['rid'].'">'.$user['rid'].'</a>';
        $user['date'] = date('H:i',strtotime($user['date']));

        return $user;
    }

}

return 'siteStatisticsUsersGetListProcessor';