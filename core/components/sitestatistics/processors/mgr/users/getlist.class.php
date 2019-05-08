<?php

/**
 * Get a list of online users
 */

class siteStatisticsUsersGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sitestatistics_item';
    public $classKey = 'UserStatistics';
    public $languageTopics = ['sitestatistics'];
    public $defaultSortField = 'UserStatistics.date';
    public $defaultSortDirection = 'DESC';
    public $permission = 'list_statistics';
    protected $fields = [
        'user' => 'Profile.fullname',
        'page' => 'Resource.pagetitle',
        'context' => 'UserStatistics.context',
        'ip' => 'UserStatistics.ip',
        'user_agent' => 'UserStatistics.user_agent',
        'referer' => 'UserStatistics.referer',
    ];


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
        $sort = $this->getProperty('sort');
        if ($sort == 'fullname') {
            $this->setProperty('sort', 'User.fullname');
        } elseif ($sort != $this->defaultSortField) {
            $this->setProperty('sort', 'UserStatistics.' . $sort);
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
        $c->leftJoin('modUserProfile', 'Profile');
        $c->leftJoin('modResource', 'Resource');
        $c->select('UserStatistics.user_key, UserStatistics.rid, UserStatistics.context, Profile.fullname, Resource.pagetitle');

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
                    'OR:Resource.pagetitle:LIKE' => "%{$query}%",
                    'OR:UserStatistics.context:LIKE' => "%{$query}%",
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

    protected function parseQuery($query)
    {
        $where = null;
        $parts = explode(':', $query);
        if (count($parts) >= 2) {
            $field = array_shift($parts);
            $query = trim(implode(':', $parts));
            if ($query == $this->modx->lexicon('stat_online_guest')) {
                $query = '';
            }
            if ($query) {
                $where = isset($this->fields[$field]) ? [$this->fields[$field] . ':LIKE ' => "%{$query}%"] : null;
            } else {
                $where = isset($this->fields[$field]) 
                    ? [$this->fields[$field] => '', "OR:{$this->fields[$field]}:IS" => null]
                    : null;
            }
        }

        return $where;
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
        $user['pagetitle'] = !empty($user['rid']) ? '<a href="?a=resource/update&id=' . $user['rid'] . '">' . $user['pagetitle'] . '</a>' : '';
        $month = 'month' . date('n', strtotime($user['date']));
        $user['date'] = $this->modx->lexicon($month) . ' ' . date('j, Y, H:i:s', strtotime($user['date']));
        $month = 'month' . date('n', $user['message_showed']);
        if ($user['message_showed']) {
            $user['message_showed'] = $this->modx->lexicon($month) . ' ' . date('j, Y, H:i', $user['message_showed']);
        }
        $user['actions'] = [];
        // Show statistics
        $user['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-table',
            'title' => $this->modx->lexicon('sitestatistics_open_stat'),
            //'multiple' => $this->modx->lexicon('sitestatistics_send_message'),
            'action' => 'getStatistics',
            'button' => true,
            'menu' => true,
        ];
        // Send Message
        $user['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-envelope-o',
            'title' => $this->modx->lexicon('sitestatistics_send_message'),
            'multiple' => $this->modx->lexicon('sitestatistics_send_message'),
            'action' => 'sendMessage',
            'button' => true,
            'menu' => true,
        ];
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

return 'siteStatisticsUsersGetListProcessor';