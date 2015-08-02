<?php
/** @var array $scriptProperties */
/** @var siteStatistics $siteStat */
if (!$siteStat = $modx->getService('sitestatistics', 'siteStatistics', $modx->getOption('sitestatistics_core_path', null, $modx->getOption('core_path') . 'components/sitestatistics/') . 'model/sitestatistics/')) {
	return 'Could not load siteStatistics class!';
}
// mode: page/site
if (empty($mode)) $mode = $scriptProperties['mode'] = 'page';
if (empty($tpl)) $scriptProperties['tpl'] = 'tpl.siteStatistics';
if (empty($resource)) $resource = $modx->resource->id;

$siteStat->initialize($scriptProperties);

if ($mode == 'page') {
	/** @var array|int $output */
	$output = $siteStat->getPageStatistics($resource);
	if (!empty($toPlaceholders))
		$modx->toPlaceholders($output,'stat');
	else
		return $output;
} else {
	return $siteStat->getSiteStatistics();
}