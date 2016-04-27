<?php
$userAgents = $modx->getOption('stat.not_allowed_user_agents');
if (!empty($userAgents)) {
    $userAgents = explode(',', $userAgents);
    $userAgents = array_map('trim', $userAgents);
    $userAgents = implode('|', $userAgents);
    $userAgent = empty($_SERVER['HTTP_USER_AGENT']) ? 'empty' : $_SERVER['HTTP_USER_AGENT'];
    $pattern = "/($userAgents)/i";
    if (preg_match($pattern, $userAgent)) return;
}
switch ($modx->event->name) {
    case 'OnLoadWebDocument': {

        if ( ($modx->getOption('stat.enable_statistics', null, false) || $modx->getOption('stat.count_online_users', null, false)) && $modx->getOption('site_status')) {
            $path = $modx->getOption('sitestatistics_core_path', null, $modx->getOption('core_path') . 'components/sitestatistics/').'model/sitestatistics/';
            /** @var siteStatistics $siteStat */
            $siteStat = $modx->getService('sitestatistics', 'siteStatistics', $path);
            if (!$siteStat->checkIp()) return;
            // если текущий ip в запрещенных, то не учитывать статистику.
            $siteStat->defineUserKey();
            // Статистика просмотров
            if ($modx->getOption('stat.enable_statistics', null, false)) {
                $siteStat->setStatistics();
            }
            //  Online Users
            if ($modx->getOption('stat.count_online_users', null, false)) {
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