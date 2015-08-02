<?php
$xpdo_meta_map['StatPageStatistics']= array (
  'package' => 'sitestatistics',
  'version' => '1.1',
  'table' => 'stat_page_statistics',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'rid' => NULL,
    'uid' => NULL,
    'date' => NULL,
    'month' => NULL,
    'year' => NULL,
    'views' => 0,
  ),
  'fieldMeta' => 
  array (
    'rid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
    'uid' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
      'index' => 'pk',
    ),
    'date' => 
    array (
      'dbtype' => 'date',
      'phptype' => 'date',
      'null' => false,
      'index' => 'pk',
    ),
    'month' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '7',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'year' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '4',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'views' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
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
        'rid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'uid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'date' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'month' => 
    array (
      'alias' => 'month',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'month' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'year' => 
    array (
      'alias' => 'year',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'year' => 
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
    'Resource' => 
    array (
      'class' => 'modResource',
      'local' => 'rid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
