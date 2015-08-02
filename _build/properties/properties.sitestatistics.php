<?php

$properties = array();

$tmp = array(
	'count' => array(
		'type' => 'list',
		'options' => array(
			array('text' => '', 'value' => ''),
			array('text' => 'byday', 'value' => 'byday'),
			array('text' => 'bymonth', 'value' => 'bymonth'),
			array('text' => 'byyear', 'value' => 'byyear'),
		),
		'value' => ''
	),
	'date' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'mode' => array(
		'type' => 'list',
		'options' => array(
			array('text' => 'page', 'value' => 'page'),
			array('text' => 'site', 'value' => 'site'),
		),
		'value' => 'page'
	),
	'resource' => array(
		'type' => 'numberfield',
		'value' => '',
	),
	'show' => array(
		'type' => 'list',
		'options' => array(
			array('text' => 'views', 'value' => 'views'),
			array('text' => 'users', 'value' => 'users'),
		),
		'value' => 'views'
	),
	'toPlaceholders' => array(
		'type' => 'combo-boolean',
		'value' => false,
	),
	'tpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.siteStatistics',
	),

);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(
		array(
			'name' => $k,
			'desc' => PKG_NAME_LOWER . '_prop_' . $k,
			'lexicon' => PKG_NAME_LOWER . ':properties',
		), $v
	);
}

return $properties;