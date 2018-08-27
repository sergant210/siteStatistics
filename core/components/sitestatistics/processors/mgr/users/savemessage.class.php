<?php

/**
 * Save a message
 */
class UserStatisticsUpdateProcessor extends modObjectUpdateProcessor {
	public $objectType = 'sitestatistics_item';
	public $classKey = 'UserStatistics';
    public $primaryKeyField = 'user_key';
	public $languageTopics = array('sitestatistics');
	public $permission = 'messages';


	/**
	 * We doing special check of permission
	 * because of our objects is not an instances of modAccessibleObject
	 *
	 * @return bool|string
	 */
	public function beforeSave() {
		if (!$this->checkPermissions()) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}


	/**
	 * @return bool
	 */
	public function beforeSet() {
		$show_message = (int)$this->getProperty('show_message');
        if ($show_message) {
            $this->setProperty('message_showed', null);
        }
		return parent::beforeSet();
	}
}

return 'UserStatisticsUpdateProcessor';
