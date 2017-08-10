<?php

/**
 * @copyright 2009-2016 Vanilla Forums Inc.
 * @license MIT
 */

namespace HipChat\v2\API;

/**
 * Represents User API methods
 *
 * Implemented according to https://www.hipchat.com/docs/apiv2
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 */
class UserAPI extends API {

    /**
     * Get all users
     *
     * GET /v2/user
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

        $response = $this->client->get('user', $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Get User
     *
     * GET /v2/user/<userid>
     *
     * @param mixed $userid id, email, or @mention name
     * @return array
     */
    public function get($userid) {
        $response = $this->client->get("user/{$userid}");
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Create user
     *
     * PUT /v2/user
     *
     * @param array $user
     * @return array
     */
    public function create($name, $user) {
        $fields = [
            'password',
            'email',
            'roles',
            'title',
            'mention_name',
            'is_group_admin',
            'timezone'
        ];

        $user = array_intersect_key($user, array_fill_keys($fields, true));
        $user['name'] = $name;

        $response = $this->client->put('user', $user);
        if (!$response->isSuccessful()) {
            return false;
        }

        $data = $response->getBody();
        $user['id'] = $data['id'];
        return $user;
    }

    /**
     * Update user
     *
     * POST /v2/user/<userid>
     *
     * @param mixed $userid id, email, or @mention name
     * @param array $changes
     * @return array
     */
    public function update($userid, $changes) {
        // Fields we can update
        $fields = [
            'name',
            'roles',
            'title',
            'presence',
            'mention_name',
            'is_group_admin',
            'timezone',
            'password',
            'email'
        ];

        // Get user
        $user = $this->get($userid);

        // Merge changes
        $user = array_merge($user, $changes);
        $user = array_intersect_key($user, array_fill_keys($fields, true));

        // Apply update
        $response = $this->client->post("user/{$userid}", $user);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $user;
    }

    /**
     * Delete user
     *
     * DELETE /v2/user/<userid>
     *
     * @param mixed $userid id, email, or @mention name
     * @return boolean
     */
    public function delete($userid) {
        $response = $this->client->delete("user/{$userid}");
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Get user message
     *
     * GET /v2/user/<userid>/history/<messageid>
     *
     * @param mixed $userid id, email, or @mention name
     * @param mixed $messageid
     * @return array
     */
    public function getMessage($userid, $messageid, $options = []) {
        if (!strlen($messageid)) {
            return false;
        }

        $default = [
            'timezone' => 'UTC',
            'include_deleted' => true
        ];

        $options = array_merge($default, $options);

        $response = $this->client->get("user/{$userid}/history/{$messageid}", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Get chat history
     *
     * GET /v2/user/<userid>/history
     *
     * @param mixed $userid id, email, or @mention name
     * @param array $options optional.
     * @return array
     */
    public function getHistory($userid, $options = []) {
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

        $response = $this->client->get("user/{$userid}/history", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Get recent chat history
     *
     * GET /v2/user/<userid>/history/latest
     *
     * @param mixed $userid id, email, or @mention name
     * @param array $options optional.
     * @return array
     */
    public function getRecentHistory($userid, $options = []) {
        $default = [
            'max-results' => 75,
            'timezone' => 'UTC',
            'include_deleted' => true,
            'not-before' => null
        ];

        $options = array_merge($default, $options);

        $response = $this->client->get("user/{$userid}/history/latest", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Send message
     *
     * POST /v2/user/<userid>/message
     *
     * @param mixed $userid id, email, or @mention name
     * @param string $message
     * @param boolean $notify optional. notify the user. true or false. default false.
     * @param string $format optional. message format. 'html' or 'text'. default 'text'.
     * @return boolean
     */
    public function sendMessage($userid, $message, $notify = false, $format = 'text') {
        $options = [
            'message' => $message,
            'notify' => $notify,
            'message_format' => $format
        ];

        $response = $this->client->post("user/{$userid}/message", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Get user photo
     *
     * GET /v2/user/<userid>/photo/<size>
     *
     * @param mixed $userid id, email, or @mention name
     * @param string $size optional. 'small' or 'big'. default 'small'.
     * @return array
     */
    public function getPhoto($userid, $size = 'small') {
        if (!strlen($size)) {
            return false;
        }

        $response = $this->client->get("user/{$userid}/photo/{$size}");
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getRawBody();
    }

    /**
     * Update user photo
     *
     * POST /v2/user/<userid>/photo
     *
     * @param mixed $userid id, email, or @mention name
     * @param string $file
     * @return boolean
     */
    public function updatePhoto($userid, $file) {
        if (!file_exists($file)) {
            return false;
        }

        $type = pathinfo($file, PATHINFO_EXTENSION);
        $fileData = file_get_contents($file);
        $fileData = "data:image/{$type};base64,".base64_encode($fileData);

        $options = [
            'photo' => $fileData
        ];

        $response = $this->client->put("user/{$userid}/photo", $options);
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Delete user photo
     *
     * DELETE /v2/user/<userid>/photo
     *
     * @param mixed $userid id, email, or @mention name
     * @return boolean
     */
    public function deletePhoto($userid) {
        $response = $this->client->delete("user/{$userid}/photo");
        if (!$response->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     * Get user preference
     *
     * GET /v2/user/<userid>/preference
     *
     * Preferences:
     *  auto-join -> user's auto join rooms
     *
     * @param mixed $userid id, email, or @mention name
     * @param string $preference optional. default 'auto-join'.
     * @return array
     */
    public function getPreference($userid, $preference = 'auto-join') {
        if (!strlen($preference)) {
            return false;
        }

        $response = $this->client->get("user/{$userid}/preference/{$preference}");
        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }

    /**
     * Share file to user
     *
     * POST /v2/user/<userid>/share/file
     *
     * @param mixed $userid id, email, or @mention name
     * @param string $file
     * @param string $message
     */
    public function shareFile($userid, $file, $message) {
        return false;
    }

    /**
     * Share link to user
     *
     * POST /v2/user/<userid>/share/link
     *
     * @param mixed $userid id, email, or @mention name
     * @param string $link
     * @param string $message
     */
    public function shareLink($userid, $link, $message) {
        return false;
    }

}