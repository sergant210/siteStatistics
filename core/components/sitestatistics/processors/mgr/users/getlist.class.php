<?php

/**
 * Get a list of online users
 */
class siteStatisticsUsersGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'sitestatistics_item';
	public $classKey = 'UserStatistics';
    public $languageTopics = array('sitestatistics');
	public $defaultSortField = 'UserStatistics.date';
	public $defaultSortDirection = 'DESC';
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
        $month = 'month'. date('n',strtotime($user['date']));
        $user['date'] = $this->modx->lexicon($month).' '.date('j, Y, H:i',strtotime($user['date']));
        $month = 'month'. date('n',$user['message_showed']);
        if ($user['message_showed']) $user['message_showed'] = $this->modx->lexicon($month).' '.date('j, Y, H:i',$user['message_showed']);
        $user['actions'] = array();
        // Show statistics
        $user['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-table',
            'title' => $this->modx->lexicon('sitestatistics_open_stat'),
            //'multiple' => $this->modx->lexicon('sitestatistics_send_message'),
            'action' => 'getStatistics',
            'button' => true,
            'menu' => true,
        );
        // Send Message
        $user['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-envelope-o',
            'title' => $this->modx->lexicon('sitestatistics_send_message'),
            'multiple' => $this->modx->lexicon('sitestatistics_send_message'),
            'action' => 'sendMessage',
            'button' => true,
            'menu' => true,
        );

        return $user;
    }

}

return 'siteStatisticsUsersGetListProcessor';