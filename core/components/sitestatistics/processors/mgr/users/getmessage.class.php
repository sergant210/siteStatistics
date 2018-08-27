<?php

/**
 * Get a message
 */
class UserStatisticsMessageGetProcessor extends modObjectGetProcessor {
    public $objectType = 'sitestatistics_item';
    public $classKey = 'UserStatistics';
    /** @var string $primaryKeyField The primary key field to grab the object by */
    public $primaryKeyField = 'user_key';
    public $languageTopics = array('sitestatistics');
	public $permission = 'messages';


	/**
	 * We doing special check of permission
	 * because of our objects is not an instances of modAccessibleObject
	 *
	 * @return mixed
	 */
	public function process() {
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}

		return parent::process();
	}

}

return 'UserStatisticsMessageGetProcessor';