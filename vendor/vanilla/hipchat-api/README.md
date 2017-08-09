HipChat API
==========

[![Packagist Version](https://img.shields.io/packagist/v/vanilla/hipchat-api.svg?style=flat-square)](https://packagist.org/packages/vanilla/hipchat-api)
![MIT License](https://img.shields.io/packagist/l/vanilla/hipchat-api.svg?style=flat-square)

`hipchat-api` is a PHP library that provides an implementation of the HipChat v2 REST API.

Installation
------------

*hipchat-api requires PHP 5.4 or higher*

`hipchat-api` is [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) compliant and can be easily installed using [composer](//getcomposer.org). 

Just add `vanilla/hipchat-api` to your composer.json.

```json
"require": {
    "vanilla/hipchat-api": "~2.0"
}
```

Usage
-----

HipChat uses OAuth2 tokens with specific scopes to control API access. You can request a token for yourself by visiting the [HipChat Account/API page](https://www.hipchat.com/account/api).

```php
<?php

// The library lives in the HipChat namespace.
use HipChat\v2\HipChatClient;

// Require composer's autoloader.
require_once 'vendor/autoload.php';

// Define the cli options.
$client = new HipChatClient();
$client->setAuth('<OAUTH BEARER TOKEN>');

// Start making calls!
$users = $client->usersAPI()->getAll();
```

This example makes a call to the Users API to [get all users](https://www.hipchat.com/docs/apiv2/method/get_all_users).

* You can make the client throw an exception instead of returning `false` on error by calling `setThrowExceptions(true)` on `$client`.

Custom API Endpoint
-------------------

If you are using a private instance of HipChat, you can change the API base URL when you instantiate the client:

```php
<?php

use HipChat\v2\HipChatClient;

$client = new HipChatClient('https://my.hipchatdomain.com');
```

* You can also change the base URL at any time by calling `setBaseUrl()` on an existing `$client`.

Current status
--------------

The following list shows methods available and missing:

###Add ons
- [ ] Get addon installable data
- [ ] Create addon link
- [ ] Invoke addon link
- [ ] Delete addon link

###Capabilities
- [x] Get capabilities

###Emoticons
- [ ] Get emoticon
- [ ] Get all emoticons

###OAuth Sessions
- [ ] Generate token
- [ ] Get session
- [ ] Delete session

###Rooms
- [x] Get all rooms
- [x] Create room
- [x] Get room
- [x] Update room
- [x] Delete room
- [x] Get avatar
- [X] Update avatar
- [X] Delete avatar
- [x] Get room message
- [x] View room history
- [x] View recent room history
- [x] Get glance
- [x] Create glance
- [x] Delete glance
- [x] Invite user
- [x] Add member
- [x] Remove member
- [x] Get all members
- [x] Get all participants
- [x] Send message
- [x] Send room notification
- [x] Reply to message
- [ ] Share file with room
- [ ] Share link with room
- [x] Get room statistics
- [x] Set topic
- [x] Get webhook
- [x] Delete webhook
- [x] Get all webhooks
- [x] Create webhook

###Users
- [x] Get all users
- [x] Create user
- [x] View user
- [x] Update user
- [x] Delete user
- [x] View privatechat history
- [x] View recent privatechat history
- [x] Get private chat message
- [x] Send message to user
- [x] Get photo
- [x] Upload photo
- [x] Delete photo
- [x] Get preference (auto-join)
- [ ] Share file with user
- [ ] Share link with user
