<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
}
else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var siteStatistics $siteStatistics */
$siteStatistics = $modx->getService('sitestatistics', 'siteStatistics', $modx->getOption('sitestatistics_core_path', null, $modx->getOption('core_path') . 'components/sitestatistics/') . 'model/sitestatistics/');

// handle request
$corePath = $modx->getOption('sitestatistics_core_path', null, $modx->getOption('core_path') . 'components/sitestatistics/');
$path = $modx->getOption('processorsPath', $siteStatistics->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));