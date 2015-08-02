<?php

/**
 * Get a list of online users
 */
class siteStatisticsUsersGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'StatOnlineUsers';
	public $classKey = 'StatOnlineUsers';
	public $defaultSortField = 'StatOnlineUsers.user';
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
        $c->select('StatOnlineUsers.user_key, StatOnlineUsers.context,User.fullname');

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

        return $user;
    }

}

return 'siteStatisticsUsersGetListProcessor';