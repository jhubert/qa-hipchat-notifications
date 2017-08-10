<?php

/**
 * @copyright 2009-2016 Vanilla Forums Inc.
 * @license MIT
 */

namespace HipChat\v2;

use Garden\Http\HttpClient;

/**
 * Represents a HipChat client connection.
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 */
class HipChatClient extends HttpClient {

    /**
     * HipChat API Capabilities
     * @var array
     */
    protected $capabilities;

    /**
     * Auth Container
     * @var \HipChat\API\Auth\AuthContainer
     */
    protected $auth;

    /**
     * Construct client
     *
     * @param string $baseUrl optional. default is HipChat's API.
     */
    public function __construct($baseUrl = 'https://api.hipchat.com/v2/') {
        parent::__construct($baseUrl);
        $this
            ->setDefaultHeader('Content-Type', 'application/json')
            ->setThrowExceptions(true);
    }

    /**
     * Get instance of user API
     *
     * @staticvar \HipChat\API\UserAPI $users
     * @return \HipChat\API\UserAPI
     */
    public function usersAPI() {
        static $users = null;
        if (!($users instanceof API)) {
            $users = new API\UserAPI($this);
        }
        return $users;
    }

    /**
     * Get instance of room API
     *
     * @staticvar \HipChat\API\RoomAPI $rooms
     * @return \HipChat\API\RoomAPI
     */
    public function roomsAPI() {
        static $rooms = null;
        if (!($rooms instanceof API)) {
            $rooms = new API\RoomAPI($this);
        }
        return $rooms;
    }

    /**
     * Get a HipChat AuthContainer
     *
     * @param string $token
     * @return \HipChat\HipChatClient
     */
    public function setAuth($token, $type = 'Bearer') {
        $this->auth = new Auth\AuthContainer($token, $type);
        return $this;
    }

    /**
     * Get API capabilities
     *
     * @return array
     */
    public function getCapabilities() {
        if (!is_array($this->capabilities)) {
            $response = $this->get('capabilities');
            if (!$response->isSuccessful()) {
                return false;
            }

            $this->capabilities = $response->getBody();
        }
        return $this->capabilities;
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri, array $query = [], array $headers = [], $options = []) {
        if ($this->auth instanceof Auth\AuthContainer) {
            $headers['Authorization'] = $this->auth->getAuth();
        }
        return parent::get($uri, $query, $headers, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function head($uri, array $query = [], array $headers = [], $options = []) {
        if ($this->auth instanceof Auth\AuthContainer) {
            $headers['Authorization'] = $this->auth->getAuth();
        }
        return parent::head($uri, $query, $headers, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function options($uri, array $query = [], array $headers = [], $options = []) {
        if ($this->auth instanceof Auth\AuthContainer) {
            $headers['Authorization'] = $this->auth->getAuth();
        }
        return parent::options($uri, $query, $headers, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function post($uri, $body = [], array $headers = [], $options = []) {
        if ($this->auth instanceof Auth\AuthContainer) {
            $headers['Authorization'] = $this->auth->getAuth();
        }
        return parent::post($uri, $body, $headers, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function put($uri, $body = [], array $headers = [], $options = []) {
        if ($this->auth instanceof Auth\AuthContainer) {
            $headers['Authorization'] = $this->auth->getAuth();
        }
        return parent::put($uri, $body, $headers, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($uri, $body = [], array $headers = [], $options = []) {
        if ($this->auth instanceof Auth\AuthContainer) {
            $headers['Authorization'] = $this->auth->getAuth();
        }
        return parent::patch($uri, $body, $headers, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($uri, array $query = [], array $headers = [], $options = []) {
        if ($this->auth instanceof Auth\AuthContainer) {
            $headers['Authorization'] = $this->auth->getAuth();
        }
        return parent::delete($uri, $query, $headers, $options);
    }


}