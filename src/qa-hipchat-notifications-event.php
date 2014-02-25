<?php

/*
  HipChat Notifications

  File: qa-plugin/qa-hipchat-notifications/qa-hipchat-notifications-event.php
  Version: 0.1
  Date: 2014-02-25
  Description: Event module class for HipChat notifications plugin
*/

class qa_hipchat_notifications_event {

  private $plugindir;

  function load_module($directory, $urltoroot)
  {
    $this->plugindir = $directory;
  }

  public function process_event($event, $userid, $handle, $cookieid, $params) {
    require_once QA_INCLUDE_DIR . 'qa-app-emails.php';
    require_once QA_INCLUDE_DIR . 'qa-app-format.php';
    require_once QA_INCLUDE_DIR . 'qa-util-string.php';

    switch ($event) {
      case 'q_post':
        $this->send_hipchat_notification(
          $this->build_new_question_message(
            isset($handle) ? $handle : qa_lang('main/anonymous'),
            $params['title'],
            qa_q_path($params['postid'], $params['title'], true)
          )
        );
        break;
      case 'a_post':
        $this->send_hipchat_notification(
          $this->build_new_answer_message(
            isset($handle) ? $handle : qa_lang('main/anonymous'),
            $params['title'],
            qa_q_path($params['postid'], $params['title'], true)
          )
        );
        break;
    }
  }

  private function build_new_question_message($who, $title, $url) {
    return sprintf("%s asked a new question: <a href=\"%s\">\"%s\"</a>. Do you know the answer?", $who, $url, $title);
  }

  private function build_new_answer_message($who, $title, $url) {
    return sprintf("%s answered the question: <a href=\"%s\">\"%s\"</a>.", $who, $url, $title);
  }

  private function send_hipchat_notification($message) {
    require_once $this->plugindir . 'HipChat' . DIRECTORY_SEPARATOR . 'HipChat.php';

    $token = qa_opt('hipchat_notifications_api_token');
    $room = qa_opt('hipchat_notifications_room_name');
    $sender = qa_opt('hipchat_notifications_sender');
    $color = qa_opt('hipchat_notifications_color');
    $notify = qa_opt('hipchat_notifications_notify') > 0 ? true : false;

    if ($sender == null || $sender == '')
      $sender = 'Question2Answer';

    if ($color == null || $color == '')
      $color = 'yellow';

    if ($token && $room) {
      $hc = new HipChat\HipChat($token);

      $result = $hc->message_room($room, $sender, $message, $notify, $color);
    }
  }
}
