<?php

/**
 * Remove the page statistics
 */
class siteStatisticsPageRemoveProcessor extends modObjectProcessor
{
    public $objectType = 'sitestatistics_item';
    public $classKey = 'PageStatistics';
    public $languageTopics = ['sitestatistics'];
    public $permission = 'remove_statistics';

    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }
        $_ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($_ids)) {
            return $this->failure($this->modx->lexicon('sitestatistics_item_err_ns'));
        }
        $ids = [];
        $delete_table = false;
        foreach ($_ids as $id) {
            list($rid, $date, $month, $year, $period) = explode('&', $id);
            $where = [];
            switch ($period) {
                case '':
                    if (empty($rid)) {
                        $delete_table = true;
                    } else {
                        $where = ['rid' => $rid];
                    }
                    break;
                case 'day':
                    if (empty($rid)) {
                        $where = ['date' => $date];
                    } else {
                        $where = ['rid' => $rid, 'date' => $date];
                    }
                    break;
                case 'month':
                    if (empty($rid)) {
                        $where = ['month' => $month];
                    } else {
                        $where = ['rid' => $rid, 'month' => $month];
                    }
                    break;
                case 'year':
                    if (empty($rid)) {
                        $where = ['year' => $year];
                    } else {
                        $where = ['rid' => $rid, 'year' => $year];
                    }
                    break;
            }
            $ids[] = $where;
        }
        foreach ($ids as $where) {
            if (empty($where) && !$delete_table) {
                continue;
            }
            /** @var xPDOquery $q */
            $q = $this->modx->newQuery('PageStatistics');
            $q->command('delete');
            //$q->select('rid');
            $q->where($where);
            $q->prepare();
            $q->stmt->execute();
        }
        return $this->success();
    }
}

return 'siteStatisticsPageRemoveProcessor';