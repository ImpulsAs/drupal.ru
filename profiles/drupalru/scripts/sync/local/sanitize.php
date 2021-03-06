<?php

/**
 * @file
 * Sanitize dump of drupal.ru database.
 */

$rules = [
  'actions',
  'block',
  'block_custom',
  'bueditor_editors',
  'bueditor_buttons',
  'captcha_points',
  'contact',
  'date_formats',
  'date_format_type',
  'field_config',
  'field_config_instance',
  'filter',
  'filter_format',
  'image_effects',
  'image_styles',
  'languages',
  'locales_source',
  'locales_target',
  'menu_custom',
  'menu_links',
  'menu_router',
  'metatag_config',
  'node_type',
  'ocupload_templates',
  'profile_field',
  'registry',
  'registry_file',
  'role',
  'role_permission',
  'system',
  'taxonomy_term_data',
  'taxonomy_term_hierarchy',
  'taxonomy_vocabulary',
  'trigger_assignments',
  'variable',
  'views_display',
  'views_view',
  'users' => [['uid', 0, '!=']],
  'ymb',
];

$hash = hash('sha256', microtime() . '' . rand());
$clean_variables = [
  'values' => [
    'abuse_warn_bcc' => 's:15:"mail@drupal.loc";',
    'site_mail' => 's:15:"mail@drupal.loc";',
    'pm_email_notify_from' => 's:15:"mail@drupal.loc";',
    'captcha_token' => sprintf('s:32:"%s";', substr($hash, 0, 32)),
    'cron_key' => sprintf('s:64:"%s";', $hash),
    'drupal_private_key' => sprintf('s:64:"%s";', $hash),
    'spambot_sfs_api_key' => sprintf('s:14:"%s";', substr($hash, 0, 14)),
    'token' => sprintf('s:32:"%s";', substr($hash, 0, 32)),
    'comment_notify_node_notify_default_mailalert' => 'i:0;',
  ],
  'drop' => [
    'abuse_',
    'birthdays_',
    'color_garland_',
    'druid_',
    'googleajaxsearch_',
    'googleanalytics_',
    'googlemap_',
    'listhandler_',
    'mibbit_',
    'mysite_',
    'pearwiki_',
    'postcard_',
    'recaptcha_',
    'relativity_',
    'reptag_',
    'signature_',
    'site_user_',
    'user_relationship_',
    'user_relationships_',
    'xtemplate_',
  ],
];

$rules_keys = array_keys($rules);
foreach (db_find_tables('%') as $table) {
  if (!in_array($table, $rules_keys, TRUE) && !in_array($table, $rules, TRUE)) {
    print "TRUNCATE TABLE $table" . PHP_EOL;
    db_truncate($table)->execute();
  }
  elseif (isset($rules[$table]) && is_array($rules[$table])) {
    $query = db_delete($table);
    foreach ($rules[$table] as $condition) {
      switch (count($condition)) {
        case 2:
          $query->condition(reset($condition), next($condition));
          break;
        case 3:
          $query->condition(reset($condition), next($condition), next($condition));
          break;
      }
    }
    $query->execute();
    print str_replace(PHP_EOL, '', $query->__toString()) . PHP_EOL;
    unset($query);
  }
  else {
    print "Skip table $table" . PHP_EOL;
  }
}

print PHP_EOL;
print '####### Clean Variables #######' . PHP_EOL;
foreach ($clean_variables['drop'] as $variable) {
  $query = db_delete('variable')->condition('name', db_like($variable) . '%', 'LIKE');
  print str_replace(PHP_EOL, '', $query->__toString()) . PHP_EOL;
  $query->execute();
}

print PHP_EOL;
print '####### Change sensitive data #######' . PHP_EOL;
foreach ($clean_variables['values'] as $variable => $value) {
  $query = db_update('variable')
    ->fields(['value' => $value])
    ->condition('name', $variable);
  print str_replace(PHP_EOL, '', $query->__toString()) . PHP_EOL;
  $query->execute();
}

print PHP_EOL;
print '####### Cleaning of blocks #######' . PHP_EOL;
$query = db_update('block_custom')
  ->fields(['body' => ''])
  ->condition('bid', 46); // Block with counters
print str_replace(PHP_EOL, '', $query->__toString()) . PHP_EOL;
$query->execute();