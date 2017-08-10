<?php

/**
 * @copyright 2009-2016 Vanilla Forums Inc.
 * @license MIT
 */

namespace HipChat\v2\API;

/**
 * Represents Room API methods
 *
 * Implemented according to https://www.hipchat.com/docs/apiv2
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 */
class RoomAPI extends API {

    /**
     * Get all rooms
     *
     * GET /v2/room
     *
     * @param array $options
     * @return array
     */
    public function getAll($options = []) {
        $default = [
            'start-index' => 0,
            'max-results' => 100,
            'include-guests' => false,
            'include-deleted' => false
        ];

        $options = array_merge($default, $options);
        $options = array_intersect_key($options, $default);

        $response = $this->client->get('room', $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Get Room
     *
     * GET /v2/room/<roomid>
     *
     * @param mixed $roomid id or name
     * @return array
     */
    public function get($roomid) {
        $response = $this->client->get("room/{$roomid}");
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Create room
     *
     * PUT /v2/room
     *
     * @param string $name
     * @param array $room
     * @return array
     */
    public function create($name, $room) {
        $fields = [
            'privacy',
            'delegate_admin_visibility',
            'topic',
            'owner_user_id',
            'guest_access'
        ];

        $room = array_intersect_key($room, array_fill_keys($fields, true));
        $room['name'] = $name;

        $response = $this->client->put('room', $room);
        if (!$response->isSuccessful()) {
            return false;
        }

        $data = $response->getBody();
        $room['id'] = $data['id'];
        return $room;
    }

    /**
     * Update room
     *
     * POST /v2/room/<roomid>
     *
     * @param mixed $roomid id or name
     * @param array $changes
     * @return array
     */
    public function update($roomid, $changes) {
        // Fields we can update
        $fields = [
            'name',
            'topic',
            'privacy',
            'is_archived',
            'is_guest_accessible',
            'delegate_admin_visibility',
            'owner',
            'guest_access'
        ];

        // Get room
        $room = $this->get($roomid);

        // Merge changes
        $room = array_merge($room, $changes);
        $room = array_intersect_key($room, array_fill_keys($fields, true));

        // Apply update
        $response = $this->client->post("room/{$roomid}", $room);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $room;
    }

    /**
     * Delete room
     *
     * DELETE /v2/room/<roomid>
     *
     * @param mixed $roomid id or name
     * @return boolean
     */
    public function delete($roomid) {
        $response = $this->client->delete("room/{$roomid}");
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Get room avatar
     *
     * GET /v2/room/<roomid>/avatar
     *
     * @param mixed $roomid
     * @return array
     */
    public function getAvatar($roomid) {
        $response = $this->client->get("room/{$roomid}/avatar");
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Set room avatar
     *
     * POST /v2/room/<roomid>/avatar
     *
     * @param mixed $roomid id or name
     * @param string $file
     * @return boolean
     */
    public function updateAvatar($roomid, $file) {
        if (!file_exists($file)) {
            return false;
        }

        $type = pathinfo($file, PATHINFO_EXTENSION);
        $fileData = file_get_contents($file);
        $fileData = "data:image/{$type};base64,".base64_encode($fileData);

        $options = [
            'avatar' => $fileData
        ];

        $response = $this->client->put("room/{$roomid}/avatar", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Delete room avatar
     *
     * DELETE /v2/room/<roomid>/avatar
     *
     * @param mixed $roomid id or name
     * @return boolean
     */
    public function deleteAvatar($roomid) {
        $response = $this->client->delete("room/{$roomid}/avatar");
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     *
     * @param mixed $roomid id or name
     */
    public function getGlance($roomid) {

    }

    /**
     *
     * @param type $roomid
     */
    public function createGlance($roomid) {

    }

    /**
     *
     * @param mixed $roomid id or name
     */
    public function deleteGlance($roomid) {

    }

    /**
     * Get room message
     *
     * GET /v2/room/<roomid>/history/<messageid>
     *
     * @param mixed $roomid id or name
     * @param mixed $messageid
     * @param array $options optional.
     * @return array
     */
    public function getMessage($roomid, $messageid, $options = []) {
        if (!strlen($messageid)) {
            return false;
        }

        $default = [
            'timezone' => 'UTC',
            'include_deleted' => true
        ];

        $options = array_merge($default, $options);

        $response = $this->client->get("room/{$roomid}/history/{$messageid}", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Get chat history
     *
     * GET /v2/room/<roomid>/history
     *
     * @param mixed $roomid id or name
     */
    public function getHistory($roomid, $options = []) {
        $default = [
            'reverse' => true,
            'include_deleted' => true,
            'date' => 'recent',
            'timezone' => 'UTC',
            'end-date' => null
        ];

        $nonrecentOptions = [
            'max-results' => 100,
            'start-index' => 0
        ];

        $options = array_merge($default, $options);

        if ($options['date'] != 'recent') {
            $options = array_merge($nonrecentOptions, $options);
        }

        $response = $this->client->get("room/{$roomid}/history", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Get recent chat history
     *
     * GET /v2/room/<roomid>/history/latest
     *
     * @param mixed $roomid id or name
     */
    public function getRecentHistory($roomid, $options = []) {
        $default = [
            'max-results' => 75,
            'timezone' => 'UTC',
            'include_deleted' => true,
            'not-before' => null
        ];

        $options = array_merge($default, $options);

        $response = $this->client->get("room/{$roomid}/history/latest", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Invite user to room
     *
     * POST /v2/room/<roomid>/invite/<userid>
     *
     * @param mixed $roomid id or name
     * @param mixed $userid
     * @param string $reason optional.
     * @return boolean
     */
    public function inviteUser($roomid, $userid, $reason = null) {
        if (!strlen($userid)) {
            return false;
        }

        $options = [];
        if ($reason) {
            $options['reason'] = $reason;
        }

        $response = $this->client->post("room/{$roomid}/invite/{$userid}", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Add member to room
     *
     * PUT /v2/room/<roomid>/member/<memberid>
     *
     * @param mixed $roomid id or name
     * @param mixed $userid
     * @param array $options optional.
     * @return boolean
     */
    public function addMember($roomid, $userid, $options = []) {
        if (!strlen($userid)) {
            return false;
        }

        $response = $this->client->put("room/{$roomid}/member/{$userid}", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Remove member from room
     *
     * DELETE /v2/room/<roomid>/member/<memberid>
     *
     * @param mixed $roomid id or name
     * @param mixed $userid
     * @return boolean
     */
    public function removeMember($roomid, $userid) {
        if (!strlen($userid)) {
            return false;
        }

        $response = $this->client->delete("room/{$roomid}/member/{$userid}");
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Get room members
     *
     * GET /v2/room/<roomid>/member
     *
     * @param mixed $roomid id or name
     * @param array $options optional.
     * @return array
     */
    public function getMembers($roomid, $options) {
        $default = [
            'start-index' => 0,
            'max-results' => 100
        ];

        $options = array_merge($default, $options);

        $response = $this->client->get("room/{$roomid}/member", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Get room participants
     *
     * GET /v2/room/<roomid>/participant
     *
     * @param mixed $roomid id or name
     * @param array $options optional.
     */
    public function getParticipants($roomid, $options = []) {
        $default = [
            'start-index' => 0,
            'include-offline' => false,
            'max-results' => 100
        ];

        $options = array_merge($default, $options);

        $response = $this->client->get("room/{$roomid}/member", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Send message
     *
     * POST /v2/room/<roomid>/message
     *
     * @param mixed $roomid id or name
     * @param string $message
     * @return array
     */
    public function sendMessage($roomid, $message) {
        $options = [
            'message' => $message
        ];

        $response = $this->client->post("room/{$roomid}/message", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Send notification
     *
     * POST /v2/room/<roomid>/notification
     *
     * @param mixed $roomid id or name
     * @param string $message
     * @param string $color optional. set the notification color. default 'yellow'.
     * @param boolean $notify optional. notify the room. true or false. default false.
     * @param string $format optional. message format. 'html' or 'text'. default 'text'.
     * @return boolean
     */
    public function sendNotification($roomid, $message, $color = 'yellow', $notify = false, $format = 'text', $options = []) {
        $default = [
            'card' => null
        ];

        $options = array_merge($options, [
            'message' => $message,
            'color' => $color,
            'notify' => $notify,
            'message_format' => $format
        ]);

        $options = array_merge($default, $options);

        $response = $this->client->post("room/{$roomid}/notification", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Reply to room message
     *
     * POST /v2/room/<roomid>/reply
     *
     * @param mixed $roomid id or name
     * @param mixed $messageid id
     * @param string $message
     * @return boolean
     */
    public function sendReply($roomid, $messageid, $message) {
        if (!strlen($messageid)) {
            return false;
        }

        $options = [
            'parentMessageId' => $messageid,
            'message' => $message
        ];

        $response = $this->client->post("room/{$roomid}/reply", $options);
        if ($response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Share file to room
     *
     * POST /v2/room/<roomid>/share/file
     *
     * @param mixed $roomid id or name
     * @param string $file
     * @param string $message optional.
     */
    public function shareFile($roomid, $file, $message = null) {
        return false;
    }

    /**
     * Share link to room
     *
     * POST /v2/room/<roomid>/share/link
     *
     * @param mixed $roomid id or name
     * @param string $link
     * @param string $message optional.
     * @return boolean
     */
    public function shareLink($roomid, $link, $message = null) {
        return false;
    }

    /**
     * Get room statistics
     *
     * GET /v2/room/<roomid>/statistics
     *
     * @param mixed $roomid id or name
     */
    public function getStatistics($roomid) {
        $response = $this->client->get("room/{$roomid}/statistics");
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Set room topic
     *
     * POST /v2/room/<roomid>/topic
     *
     * @param mixed $roomid id or name
     * @param string $topic
     * @return boolean
     */
    public function setTopic($roomid, $topic) {
        $options = [
            'topic' => $topic
        ];

        $response = $this->client->post("room/{$roomid}/topic", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Get all room webhooks
     *
     * GET /v2/room/<roomid>/webhook
     *
     * @param mixed $roomid id or name
     * @param array $options optional.
     * @return array
     */
    public function getWebhooks($roomid, $options = []) {
        $default = [
            'start-index' => 0,
            'max-results' => 100
        ];

        $options = array_merge($default, $options);

        $response = $this->client->get("room/{$roomid}/webook", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Create room webhook
     *
     * POST /v2/room/<roomid>/webhook
     *
     * @param mixed $roomid id or name
     * @param string $name
     * @param string $event
     * @param string $url
     * @param array $options optional.
     * @return array
     */
    public function createWebhook($roomid, $name, $event, $url, $options = []) {
        $default = [
            'authentication' => 'none'
        ];

        $options = array_merge($options, [
            'url' => $url,
            'name' => $name,
            'event' => $event
        ]);

        $webhook = array_merge($default, $options);

        $response = $this->client->post("room/{$roomid}/webhook", $webhook);
        if (!$response->isSuccessful()) {
            return false;
        }

        $data = $response->getBody();
        $webhook['id'] = $data['id'];
        return $webhook;
    }

    /**
     * Get room webhookd
     *
     * GET /v2/room/<roomid>/webhook/<webhookid>
     *
     * @param mixed $roomid id or name
     * @param mixed $webhookid
     * @return array
     */
    public function getWebhook($roomid, $webhookid) {
        if (!strlen($webhookid)) {
            return false;
        }

        $response = $this->client->get("room/{$roomid}/webhook/{$webhookid}");
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Delete room webhook
     *
     * DELETE /v2/room/<roomid>/webhook/<webhookid>
     *
     * @param mixed $roomid id or name
     * @param mixed $webhookid
     * @return boolean
     */
    public function deleteWebhook($roomid, $webhookid) {
        if (!strlen($webhookid)) {
            return false;
        }

        $response = $this->client->delete("room/{$roomid}/webhook/{$webhookid}");
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }


}