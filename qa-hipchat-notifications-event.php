<?php

/*
  HipChat Notifications

  File: qa-plugin/hipchat-notifications/qa-hipchat-notifications-event.php
  Version: 0.1
  Date: 2014-02-25
  Description: Event module class for HipChat notifications plugin
*/

require_once QA_INCLUDE_DIR.'qa-app-posts.php';

class qa_hipchat_notifications_event {

  private $plugindir;

  function load_module($directory, $urltoroot)
  {
    $this->plugindir = $directory;
  }

  public function process_event($event, $userid, $handle, $cookieid, $params)
  {
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
        $parentpost=qa_post_get_full($params['parentid']);

        $this->send_hipchat_notification(
          $this->build_new_answer_message(
            isset($handle) ? $handle : qa_lang('main/anonymous'),
            $parentpost['title'],
            qa_path(qa_q_request($params['parentid'], $parentpost['title']), null, qa_opt('site_url'), null, qa_anchor('A', $params['postid']))
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
      try{
        $result = $hc->message_room($room, $sender, $message, $notify, $color);
      }
      catch (HipChat\HipChat_Exception $e) {
        error_log($e->getMessage());
      }
    }
  }
}
