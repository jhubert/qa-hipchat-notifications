<?php

/*
  Plugin Name: HipChat Notifications
  Plugin URI: https://github.com/jhubert/qa-hipchat-notifications
  Plugin Description: Sends HipChat notifications of various events.
  Plugin Version: 0.1
  Plugin Date: 2014-02-25
  Plugin Author: Jeremy Baker
  Plugin Author URI: https://github.com/jhubert
  Plugin License: MIT
  Plugin Minimum Question2Answer Version: 1.5
  Plugin Update Check URI:
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
  header('Location: ../../');
  exit;
}

qa_register_plugin_module('page', 'qa-hipchat-notifications-page.php', 'qa_hipchat_notifications_page', 'HipChat Notifications Configuration');
qa_register_plugin_module('event', 'qa-hipchat-notifications-event.php', 'qa_hipchat_notifications_event', 'HipChat Notifications');
