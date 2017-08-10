<?php

/**
 * @copyright 2009-2016 Vanilla Forums Inc.
 * @license MIT
 */

namespace HipChat\v2\API;

/**
 * HipChat API section base class
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 */
class API {

    /**
     * HipChat Client
     * @var \HipChat\HipChatClient
     */
    protected $client;

    /**
     * API constructor
     *
     * @param \HipChat\HipChatClient $client
     */
    public function __construct($client) {
        $this->client = $client;
    }

}