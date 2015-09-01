<?php

if ($object->xpdo) {
	/** @var modX $modx */
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
			$modelPath = $modx->getOption('sitestatistics_core_path', null, $modx->getOption('core_path') . 'components/sitestatistics/') . 'model/';
			$modx->addPackage('sitestatistics', $modelPath);

			$manager = $modx->getManager();
			$objects = array(
				'PageStatistics',
				'UserStatistics',
			);
			foreach ($objects as $tmp) {
				$manager->createObjectContainer($tmp);
			}
			break;

		case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('sitestatistics_core_path', null, $modx->getOption('core_path') . 'components/sitestatistics/') . 'model/';
            $modx->addPackage('sitestatistics', $modelPath);

            $manager = $modx->getManager();
            // Переименовываем 1
            $query = "SHOW COLUMNS FROM {$modx->getTableName('PageStatistics')}";
            $result = $modx->query($query);
            $fields = $result->fetchAll(PDO::FETCH_ASSOC);
            $rename = true;
            foreach ($fields as $field){
                if ($field['Field'] == 'user_key') $rename = false;
            };
            if ($rename) {
                $query = "ALTER TABLE {$modx->getTableName('PageStatistics')} CHANGE uid user_key varchar(32)";
                $result = $modx->exec($query);
            }
            // Переименовываем 2
            $query = "SHOW COLUMNS FROM {$modx->getTableName('UserStatistics')}";
            $result = $modx->query($query);
            $fields = $result->fetchAll(PDO::FETCH_ASSOC);
            $rename = true;
            foreach ($fields as $field){
                if ($field['Field'] == 'uid') $rename = false;
            };
            if ($rename) {
                $query = "ALTER TABLE {$modx->getTableName('UserStatistics')} CHANGE user uid int";
                $result = $modx->exec($query);
            }
            //UserStatistics
            $manager->addField('UserStatistics','rid');
            $manager->addField('UserStatistics','show_message');
            $manager->addField('UserStatistics','message');
            $manager->addField('UserStatistics','message_showed');
            $manager->addIndex('UserStatistics','uid');
            $manager->addIndex('UserStatistics','date');
            //PageStatistics
            $manager->removeIndex('PageStatistics','PRIMARY');
            $manager->addIndex('PageStatistics','PRIMARY');
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;
