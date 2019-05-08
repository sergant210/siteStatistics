<?php

/**
 * Get a list of users who visits the selected resource
 */
class siteStatisticsGetResourceUsersProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sitestatistics_item';
    public $classKey = 'PageStatistics';
    public $languageTopics = ['sitestatistics'];
    public $defaultSortField = 'User.fullname';
    public $defaultSortDirection = 'ASC';
    public $permission = 'list_statistics';


    public function getData()
    {
        $data = [];
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);

        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '', [$this->getProperty('sort')]);
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }
        $c->prepare();
        $data['results'] = [];
        if ($c->stmt->execute()) {
            $data['results'] = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    public function iterate(array $data)
    {
        $list = [];
        $this->currentIndex = 0;
        foreach ($data['results'] as $row) {
            $list[] = $this->prepareUserRow($row);
//            $list[] = $row;
            $this->currentIndex++;
        }
        return $list;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $data = $this->getProperty('data');
        if (empty($data)) {
            return $this->failure($this->modx->lexicon('sitestatistics_item_err_ns'));
        }
        $show_total = $this->getProperty('show_total', 0);
        //$c->setClassAlias('Stat');
        $c->join('UserStatistics', 'StatUser');
        $c->leftJoin('modUserProfile', 'User', 'User.internalKey = StatUser.uid');
        if (empty($show_total)) {
            $c->select(['PageStatistics.rid, PageStatistics.user_key, User.fullname, SUM(PageStatistics.views) as views']);
            $c->groupby('PageStatistics.rid,PageStatistics.user_key');
        } else {
            $c->select("'' as rid, PageStatistics.user_key, User.fullname, SUM(PageStatistics.views) as views");
            $c->groupby('PageStatistics.user_key');
        }
        list($rid, $date, $month, $year, $period) = explode('&', $data);
        if (!empty($rid)) {
            $c->where(['rid' => $rid]);
        }
        $period = isset($period) ? $period : '';

        switch ($period) {
            case 'day':
                $c->groupby('PageStatistics.date');
                if (empty($date)) {
                    $c->where([
                        'date' => date('Y-m-d'),
                    ]);
                } else {
                    $c->where([
                        'date' => date('Y-m-d', strtotime($date)),
                    ]);
                }
                break;
            case 'month':
                $c->groupby('PageStatistics.month');
                if (empty($month)) {
                    $c->where([
                        'month' => date('Y-m'),
                    ]);
                } else {
                    $c->where([
                        'month' => date('Y-m', strtotime($month)),
                    ]);
                }
                break;
            case 'year':
                $c->groupby('PageStatistics.year');
                if (empty($year)) {
                    $c->where([
                        'year' => date('Y'),
                    ]);
                } else {
                    $c->where([
                        'year' => (int)$year,
                    ]);
                }
                break;
        }

        return $c;
    }

    /**
     */
    public function prepareUserRow($user)
    {
        if (empty($user['fullname'])) {
            $user['fullname'] = $this->modx->lexicon('stat_online_guest');
        }
        return $user;
    }

}

return 'siteStatisticsGetResourceUsersProcessor';