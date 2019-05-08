<?php
$path = $modx->getOption('sitestatistics_core_path', null, $modx->getOption('core_path') . 'components/sitestatistics/') . 'services/';

$userAgents = $modx->getOption('stat.not_allowed_user_agents');
if (!empty($userAgents)) {
    $userAgents = explode(',', $userAgents);
    //$userAgents = array_map('trim', $userAgents);
    foreach ($userAgents as &$userAgent) {
        $userAgent = trim($userAgent);
        $userAgent = preg_quote($userAgent);
    }
    $userAgents = implode('|', $userAgents);
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?: 'empty';
    if (preg_match("/($userAgents)/i", $userAgent)) return;
}

switch ($modx->event->name) {
    case 'OnLoadWebDocument': {
        if ( ($modx->getOption('stat.enable_statistics', null, false) || $modx->getOption('stat.count_online_users', null, false)) && $modx->getOption('site_status')) {
            /** @var siteStatistics $siteStat */
            $siteStat = $modx->getService('sitestatistics', 'siteStatistics', $path);
            // Disable statistics for specified IPs.
            if (!$siteStat->checkIp()) {
                return;
            }
            $siteStat->defineUserKey();
            // Consider statistics
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
    case 'OnDocFormPrerender':
        if ($mode == modSystemEvent::MODE_UPD && $modx->getOption('stat.show_tab_in_resource_form', null, true)) {
            /** @var siteStatistics $siteStat */
            $siteStat = $modx->getService('sitestatistics', 'siteStatistics', $path);
            $siteStat->initializeMgr(true);

        }
        break;
}