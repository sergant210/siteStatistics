<?php
$xpdo_meta_map['UserStatistics']= array (
  'package' => 'sitestatistics',
  'version' => '1.1',
  'table' => 'stat_online_users',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'user_key' => NULL,
    'date' => NULL,
    'uid' => 0,
    'rid' => NULL,
    'context' => NULL,
    'show_message' => 0,
    'message' => NULL,
    'message_showed' => NULL,
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
      'index' => 'index',
    ),
    'uid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'rid' => 
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
    'show_message' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'message' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'message_showed' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
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
    'date' => 
    array (
      'alias' => 'date',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'date' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'uid' => 
    array (
      'alias' => 'uid',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'uid' => 
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
      'local' => 'uid',
      'foreign' => 'internalKey',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'PageUser' => 
    array (
      'class' => 'PageStatistics',
      'local' => 'user_key',
      'foreign' => 'user_key',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
