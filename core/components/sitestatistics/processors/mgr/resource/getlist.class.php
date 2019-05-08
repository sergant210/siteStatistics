<?php
include_once dirname(__DIR__) . '/users/getlist.class.php';
/**
 * Get a list of users for the specified resource.
 */
class siteStatisticsResourceGetUserListProcessor extends siteStatisticsUsersGetListProcessor
{
    protected $fields = [
        'user' => 'Profile.fullname',
        'ip' => 'UserStatistics.ip',
        'user_agent' => 'UserStatistics.user_agent',
        'referer' => 'UserStatistics.referer',
    ];


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $rid = (int)$this->getProperty('rid', 0);
        $c->leftJoin('modUserProfile', 'Profile');
        $c->select('UserStatistics.user_key, UserStatistics.rid, Profile.fullname');
        $c->where(['UserStatistics.rid' => $rid]);
        if ($date = trim($this->getProperty('date', null))) {
            $c->where(
                'DATE_FORMAT(`date`, "%Y-%m-%d") = ' . $this->modx->quote(date('Y-m-d', strtotime($date)))
            );
        }

        if ($query = trim($this->getProperty('query'))) {
            $where = $this->parseQuery($query);
            if ($where) {
                $c->where($where);
            } else {
                $where = [
                    'Profile.fullname:LIKE' => "%{$query}%",
                    'OR:UserStatistics.ip:LIKE' => "%{$query}%",
                    'OR:UserStatistics.user_agent:LIKE' => "%{$query}%",
                    'OR:UserStatistics.referer:LIKE' => "%{$query}%",
                ];
                if ($query == $this->modx->lexicon('stat_online_guest')) {
                    $where = array_merge($where, ['OR:Profile.fullname:IS' => null]);
                }
                $c->where($where);
            }
        }
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
        $month = 'month' . date('n', strtotime($user['date']));
        $user['date'] = $this->modx->lexicon($month) . ' ' . date('j, Y, H:i:s', strtotime($user['date']));

        $user['actions'] = [];
        // Remove
        $user['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('sitestatistics_user_remove'),
            'multiple' => $this->modx->lexicon('sitestatistics_users_remove'),
            'action' => 'removeUser',
            'button' => true,
            'menu' => true,
        ];
        return $user;
    }

}

return 'siteStatisticsResourceGetUserListProcessor';