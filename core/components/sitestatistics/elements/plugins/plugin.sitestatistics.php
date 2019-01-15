<?php
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
    case 'OnDocFormPrerender':
        if ($modx->getOption('stat.show_statistics_in_doc_form', null, true)) {
            // <i class="icon icon-eye-slash" style="width: 20px;display: inline-block;margin-left: 10px;color: black;"></i>
            $output = '
            Ext.onReady(function() {
                var cb = Ext.create({
                    xtype: "xcheckbox",
                    boxLabel: _("admintools_create_resource_cache"),
                    description: _("admintools_create_resource_cache_help"),
                    hideLabel: true,
                    name: "createCache",
                    id: "createCache"
                });
                if (Ext.getCmp("modx-page-settings-right-box-right")) {
                    Ext.getCmp("modx-page-settings-right-box-right").insert(2,cb);
                    Ext.getCmp("modx-page-settings-right-box-left").add(Ext.getCmp("modx-resource-uri-override"));
                    Ext.getCmp("modx-panel-resource").on("success", function(o){
                        if (o.result.object.createCache != 0) {
                            cb.setValue(true);
                        }
                    });
                };
            }, 200);';
            $modx->controller->addHtml('<script type="text/javascript">' . $output . '</script>');
        }
        break;
}