<?php

$settings = array();

$tmp = array(
	'online_time' => array(
		'xtype' => 'numberfield',
		'value' => 15,
		'area'  => 'sitestatistics_main',
	),
	'enable_statistics' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area'  => 'sitestatistics_main',
	),
	'count_online_users' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area'  => 'sitestatistics_main',
	),
    'frontend_css' => array(
        'xtype' => 'textfield',
        'value' => '{assets_url}components/sitestatistics/css/web/style.css',
        'area'  => 'sitestatistics_main',
    ),
    'not_allowed_ip' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area'  => 'sitestatistics_main',
    ),
    'not_allowed_user_agents' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area'  => 'sitestatistics_main',
    ),
    'show_tab_in_resource_form' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area'  => 'sitestatistics_main',
    ),
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'stat.' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
