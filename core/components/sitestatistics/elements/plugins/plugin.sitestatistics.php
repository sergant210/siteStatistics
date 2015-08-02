<?php
switch ($modx->event->name) {
	case 'OnLoadWebDocument': {
		if ($modx->getOption('stat.enable_statistics', false) || $modx->getOption('stat.count_online_users', false)) {
			$key = 'siteStatistics';

			if (empty($_COOKIE[$key])) {
				if (empty($_SESSION[$key])) {
					$_SESSION[$key] = md5(rand() . time() . rand());
				}
				setcookie($key, $_SESSION[$key], 0x7FFFFFFF, '/');
			} else {
				if (empty($_SESSION[$key])) {
					$_SESSION[$key] = $_COOKIE[$key];
				} elseif ($_SESSION[$key] != $_COOKIE[$key]) {
					$_COOKIE[$key] = $_SESSION[$key];
				}
			}
			$siteStat = $modx->getService('sitestatistics', 'siteStatistics', $modx->getOption('core_path') . 'components/sitestatistics/model/sitestatistics/');
			// Статистика просмотров
			if ($modx->getOption('stat.enable_statistics', false)) {
				$siteStat->setStatistics();
			}
			//  Online Users
			if ($modx->getOption('stat.count_online_users', false)) {
				$siteStat->countOnlineUsers($_SESSION[$key]);
			}
		}
		break;
	}
}