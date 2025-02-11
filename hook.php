<?php
/**
 * @package Hook
 * @version 1.0.0
 */
/*
Plugin Name: Hook
Description: Trigger actions when events occur.
Author: David ROBIN
Version: 1.0.0
Author URI: https://drbn.fr/
*/

function writeFile() {
  $writeFolder = '/out/';
    
  if (is_dir($writeFolder)) {
    file_put_contents($writeFolder.'hello.txt', 'Hello World.');
  }
}

add_action('admin_notices', 'writeFile');