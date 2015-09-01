<?php

/**
 * Save messages for selected users
 */
class modExtraItemEnableProcessor extends modObjectProcessor {
    public $objectType = 'sitestatistics_item';
    public $classKey = 'UserStatistics';
    public $languageTopics = array('sitestatistics');
    //public $permission = 'save';


    /**
     * @return array|string
     */
    public function process() {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $users = $this->getProperty('user_key');
        if (empty($users)) {
            return $this->failure($this->modx->lexicon('sitestatistics_item_err_ns'));
        }
        foreach (explode(',',$users) as $user) {
            /** @var UserStatistics $object */
            if (!$object = $this->modx->getObject($this->classKey, $user)) {
                return $this->failure($this->modx->lexicon('sitestatistics_item_err_nf'));
            }

            $message = $this->getProperty('message');
            $object->set('message', trim($message));
            $show_message = $this->getProperty('show_message',0);
            $object->set('show_message', $show_message);
            if ($show_message) $object->set('message_showed', null);

            $object->save();
        }

        return $this->success();
    }

}

return 'modExtraItemEnableProcessor';