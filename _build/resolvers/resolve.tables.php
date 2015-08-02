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
				'StatOnlineUsers',
				'StatPageStatistics',
			);
			foreach ($objects as $tmp) {
				$manager->createObjectContainer($tmp);
			}
			break;

		case xPDOTransport::ACTION_UPGRADE:
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;
