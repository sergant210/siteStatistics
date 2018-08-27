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
    'tplItem' => array(
		'type' => 'textfield',
		'value' => '@INLINE <p>[[+stat.fullname]]</p>',
	),
    'fullMode' => array(
        'type' => 'combo-boolean',
        'value' => false,
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