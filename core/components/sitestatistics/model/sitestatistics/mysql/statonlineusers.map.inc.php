<?php
$xpdo_meta_map['StatOnlineUsers']= array (
  'package' => 'sitestatistics',
  'version' => '1.1',
  'table' => 'stat_online_users',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'user_key' => NULL,
    'date' => NULL,
    'user' => NULL,
    'context' => NULL,
  ),
  'fieldMeta' => 
  array (
    'user_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
      'index' => 'pk',
    ),
    'date' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'user' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'context' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'user_key' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'User' => 
    array (
      'class' => 'modUserProfile',
      'local' => 'user',
      'foreign' => 'internalKey',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
