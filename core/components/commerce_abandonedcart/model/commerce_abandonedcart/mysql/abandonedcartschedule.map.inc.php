<?php
/**
 * Abandoned Cart for Commerce.
 *
 * Copyright 2019 by Tony Klapatch <tony@klapatch.net>
 *
 * This file is meant to be used with Commerce by modmore. A valid Commerce license is required.
 *
 * @package commerce_abandonedcart
 * @license See core/components/commerce_abandonedcart/docs/license.txt
 */

$xpdo_meta_map['AbandonedCartSchedule']= array (
  'package' => 'commerce_abandonedcart',
  'version' => '1.1',
  'table' => 'commerce_abandoned_cart_schedule',
  'extends' => 'comSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'from' => '',
    'subject' => '',
    'content' => '',
    'send_time' => '',
    'conditions' => '',
    'removed' => 0,
    'removed_on' => NULL,
    'removed_by' => 0,
  ),
  'fieldMeta' => 
  array (
    'from' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'subject' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'content' => 
    array (
      'dbtype' => 'text',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'send_time' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'conditions' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'array',
      'null' => true,
      'default' => '',
    ),
    'removed' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'removed_on' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'removed_by' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'removed' => 
    array (
      'alias' => 'removed',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'removed' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
