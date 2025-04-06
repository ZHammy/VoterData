<?php
if (!defined("ABSPATH")) {return;}
/*
 * This is an auto-generated file by Fluent Snippets plugin.
 * Please do not edit manually.
 */
return array (
  'published' => 
  array (
    '1-uploadsnippet.php' => 
    array (
      'name' => 'UploadSnippet',
      'description' => '',
      'type' => 'PHP',
      'status' => 'published',
      'tags' => '',
      'created_at' => '',
      'updated_at' => '2025-04-06 00:03:51',
      'run_at' => 'all',
      'priority' => 10,
      'group' => '',
      'condition' => 
      array (
        'status' => 'yes',
        'run_if' => 'assertive',
        'items' => 
        array (
          0 => 
          array (
          ),
        ),
      ),
      'load_as_file' => '',
      'file_name' => '1-uploadsnippet.php',
    ),
    '2-redtest.php' => 
    array (
      'name' => 'RedTest',
      'description' => '',
      'type' => 'css',
      'status' => 'published',
      'tags' => '',
      'created_at' => '',
      'updated_at' => '2025-04-05 23:45:46',
      'run_at' => 'wp_head',
      'priority' => 10,
      'group' => '',
      'condition' => 
      array (
        'status' => 'yes',
        'run_if' => 'assertive',
        'items' => 
        array (
          0 => 
          array (
          ),
        ),
      ),
      'load_as_file' => '',
      'file_name' => '2-redtest.php',
    ),
  ),
  'draft' => 
  array (
  ),
  'hooks' => 
  array (
    'all' => 
    array (
      0 => '1-uploadsnippet.php',
    ),
    'wp_head' => 
    array (
      0 => '2-redtest.php',
    ),
  ),
  'meta' => 
  array (
    'secret_key' => 'adfe568a1f738fd5b8a774f83b903969',
    'force_disabled' => 'no',
    'cached_at' => '2025-04-06 00:03:51',
    'cached_version' => '10.34',
    'cashed_domain' => 'https://wordpress.ddev.site',
    'legacy_status' => 'new',
    'auto_disable' => 'yes',
    'auto_publish' => 'no',
    'remove_on_uninstall' => 'no',
  ),
  'error_files' => 
  array (
  ),
);