<?php
if (preg_match('/(bot|spider|slurp)/i',$_SERVER['HTTP_USER_AGENT'])) return;
switch ($modx->event->name) {
    case 'OnLoadWebDocument': {
        if ($modx->getOption('stat.enable_statistics', false) || $modx->getOption('stat.count_online_users', false)) {
            $path = $modx->getOption('sitestatistics_core_path', null, $modx->getOption('core_path') . 'components/sitestatistics/').'model/sitestatistics/';
            /** @var siteStatistics $siteStat */
            $siteStat = $modx->getService('sitestatistics', 'siteStatistics', $path);
            $siteStat->defineUserKey();
            // Статистика просмотров
            if ($modx->getOption('stat.enable_statistics', false)) {
                $siteStat->setStatistics();
            }
            //  Online Users
            if ($modx->getOption('stat.count_online_users', false)) {
                $siteStat->setUserStatistics();
            }
            $siteStat->need2ClearCache = $siteStat->getMessage();
        }
        break;
    }
    case 'OnWebPageComplete': {
        $modx->sitestatistics->clearCache();
        unset($modx->sitestatistics);
        break;
    }
}