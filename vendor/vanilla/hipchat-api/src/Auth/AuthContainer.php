<?php

/**
 * @copyright 2009-2016 Vanilla Forums Inc.
 * @license MIT
 */

namespace HipChat\v2\Auth;

/**
 * Represents a HipChat auth token.
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 */
class AuthContainer {

    /**
     * Token
     * @var string
     */
    protected $token;

    /**
     * Token type (Basic or Bearer)
     * @var string
     */
    protected $type;

    /**
     * Auth Container
     *
     * @param string $token oauth2 token for hipchat
     */
    public function __construct($token, $type) {
        $this->token = $token;
        $this->type = $type;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Get Authorization Header
     *
     * @return string
     */
    public function getAuth() {
        return "{$this->type} {$this->token}";
    }
}