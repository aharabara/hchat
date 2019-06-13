<?php

namespace App\Api;

use App\Api\Entities\DirectMessageChannel;
use App\Api\Entities\Message;
use App\Api\Entities\User;
use Base\Core\Curse;
use Error;
use Httpful\Request;

class Api
{
    public const TYPE_GROUPS = 'groups';
    public const TYPE_DIRECT_MESSAGE = 'dm';
    public const TYPE_CHANNELS = 'channels';
    public const CHANNEL_TYPES = [self::TYPE_DIRECT_MESSAGE, self::TYPE_CHANNELS, self::TYPE_GROUPS];

    protected static $userList = [];


    public $api;
    protected $password;
    protected $id = ROCKET_PERSONAL_USER_ID;
    protected $personalToken = ROCKET_PERSONAL_TOKEN;


    public function __construct()
    {
        $this->api = ROCKET_CHAT_INSTANCE . REST_API_ROOT;

        // set template request to send and expect JSON
        $tmp = Request::init()
            ->sendsJson()
            ->expectsJson();
        Request::ini($tmp);
    }


    /**
     * Get version information. This simple method requires no authentication.
     */
    public function version()
    {
        $response = \Httpful\Request::get($this->api . 'info')->send();
        return $response->body->info->version;
    }

    /**
     * Quick information about the authenticated user.
     */
    public function me()
    {
        $response = Request::get($this->api . 'me')->send();

        if ($response->body->status !== 'error') {
            if (isset($response->body->success) && $response->body->success == true) {
                return $response->body;
            }
        } else {
            echo($response->body->message . "\n");
            return false;
        }
    }


    /**
     * Authenticate with the REST API.
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function login(): bool
    {
        $tmp = Request::init()
            ->addHeader('X-Auth-Token', $this->personalToken)
            ->addHeader('X-User-Id', $this->id);

        Request::ini($tmp);

        // preload users
        self::$userList = $this->users();
        return true;
    }

    /**
     * Deletes an existing user.
     * @return DirectMessageChannel[]
     */
    public function directMessages(): array
    {
        $channels = [];
        try {
            $response = Request::init()
                ->get($this->api . self::TYPE_DIRECT_MESSAGE . '.list')
                ->addHeader('X-Auth-Token', $this->personalToken)
                ->addHeader('X-User-Id', $this->id)
                ->send();
            foreach ($response->body->ims ?? [] as $channel) {
                /* key by second user */
                $channels[end($channel->usernames)] = new DirectMessageChannel($this, $channel);
            }
        } catch (\Throwable $e) {
            \Analog::debug($e->getMessage() . "\n" . $e->getTraceAsString());
        }
        return $channels;
    }

    /**
     * Deletes an existing user.
     * @param string $type
     * @param string $id
     * @return Message
     */
    public function messages(string $type, string $id): ?array
    {
        if (!in_array($type, self::CHANNEL_TYPES, true)) {
            throw new Error("There is no such channel type named '$type'.");
        }
        $messages = [];
        try {
            $response = Request::init()
                ->get($this->api . "$type.messages?roomId=$id")
                ->addHeader('X-Auth-Token', $this->personalToken)
                ->addHeader('X-User-Id', $this->id)
                ->body(['roomId' => $id])
                ->send();

            if ($response->body->success ?? false) {
                foreach ($response->body->messages as $msg) {
                    $sender = self::$userList[$msg->u->_id];
                    $timestamp = new \DateTimeImmutable($msg->ts);
                    $messages[] = new Message($msg->_id, $msg->msg, $sender, $timestamp);
                }
                return $messages;
            }
        } catch (\Throwable $e) {
            \Analog::error($e->getMessage() . "\n" . $e->getTraceAsString());
        }

        return $messages;
    }

    /**
     * @return array
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    protected function users(): array
    {
        $response = Request::get($this->api . 'users.list')->send();

        $users = [];
        if ($response->body->success ?? false) {
            foreach ($response->body->users as $user) {
                $users[$user->_id] = new User($user->_id, $user->username, $user->name, $user->status);
            }
        }
        return $users;
    }

    public function send(string $roomId, string $text)
    {
        $messages = [];
        try {
            $response = Request::init()
                ->get($this->api . 'chat.postMessage')
                ->addHeader('X-Auth-Token', $this->personalToken)
                ->addHeader('X-User-Id', $this->id)
                ->body(['roomId' => $roomId, 'text' => $text])
                ->send();

            if ($response->body->success ?? false) {
                foreach ($response->body->messages as $msg) {
                    $sender = self::$userList[$msg->u->_id];
                    $timestamp = new \DateTimeImmutable($msg->ts);
                    $messages[] = new Message($msg->_id, $msg->msg, $sender, $timestamp);
                }
                return $messages;
            }
        } catch (\Throwable $e) {
            \Analog::error($e->getMessage() . "\n" . $e->getTraceAsString());
        }

        return $messages;
    }
}