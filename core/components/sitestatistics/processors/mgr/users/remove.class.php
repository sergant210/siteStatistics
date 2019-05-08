<?php

/**
 * Remove the user statistics
 */
class siteStatisticsUserRemoveProcessor extends modObjectProcessor
{
    public $objectType = 'sitestatistics_item';
    public $classKey = 'UserStatistics';
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
        $remove_page_stats = $this->modx->fromJSON($this->getProperty('remove_page_stats'));
        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('sitestatistics_item_err_ns'));
        }

        foreach ($ids as $id) {
            /** @var UserStatistics $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('sitestatistics_item_err_nf'));
            }

            if ($object->remove() && $remove_page_stats) {
                $c = $this->modx->newQuery('PageStatistics');
                $c->command('delete');
                $c->where([
                    'user_key' => $id,
                ]);
                $c->prepare();
                $c->stmt->execute();
            }
        }

        return $this->success();
    }
}

return 'siteStatisticsUserRemoveProcessor';