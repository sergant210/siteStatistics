<?php

$properties = array();

$tmp = array(
	'ctx' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'toPlaceholder' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'tpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.siteOnlineUsers',
	),

);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(
		array(
			'name' => $k,
			'desc' => 'siteonlineusers_prop_' . $k,
			'lexicon' => PKG_NAME_LOWER . ':properties',
		), $v
	);
}

return $properties;