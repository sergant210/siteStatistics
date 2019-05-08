<?php

/**
 * Get a list of online users
 */
class siteStatisticsOnlineUsersGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sitestatistics_item';
    public $classKey = 'UserStatistics';
    public $defaultSortField = 'UserStatistics.uid';
    public $defaultSortDirection = 'ASC';
    public $permission = 'list_statistics';


    /**
     * * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return boolean|string
     */
    public function beforeQuery()
    {
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
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin('modUserProfile', 'User');
        $c->select('UserStatistics.user_key, UserStatistics.rid, UserStatistics.context, User.fullname');
        $time = $this->modx->getOption('stat.online_time', null, 15);
        $where = "UserStatistics.date > '" . date('Y-m-d H:i:s') . "' -  INTERVAL '" . $time . "' MINUTE";
        $c->where($where);
        $c->sortby('UserStatistics.date', 'DESC');

        return $c;
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        $user = $object->toArray();
        if (empty($user['fullname'])) {
            $user['fullname'] = $this->modx->lexicon('stat_online_guest');
        }
        $query = $this->modx->newQuery('modResource', [
            'id' => $user['rid'],
        ]);
        $query->select('pagetitle');
        if (!$pagetitle = $this->modx->getValue($query->prepare())) {
            $pagetitle = $user['rid'];
        }
        $user['rid'] = !empty($user['rid']) ? '<a href="?a=resource/update&id=' . $user['rid'] . '">' . $pagetitle . '</a>' : '';
        $user['date'] = date('H:i', strtotime($user['date']));

        return $user;
    }

}

return 'siteStatisticsOnlineUsersGetListProcessor';