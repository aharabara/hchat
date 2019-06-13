<?php

namespace App\Api\Entities;

use App\Api\Api;
use Base\Components\OrderedList\ListItem;

class DirectMessageChannel
{
    /** @var string */
    protected $id;

    /** @var Api */
    protected $api;

    /** @var string[] */
    protected $usernames;

    /**
     * DirectMessageChannel constructor.
     * @param Api $api
     * @param \stdClass $channel
     */
    public function __construct(Api $api, \stdClass $channel)
    {
        $this->id = $channel->_id;
        $this->usernames = $channel->usernames;
        $this->api = $api;
    }

    /**
     * @return Message[]
     */
    public function messages(): array
    {
        return $this->api->messages(Api::TYPE_DIRECT_MESSAGE, $this->id);
    }

    /**
     * @param string $text
     * @return Message[]
     */
    public function send(string $text): array
    {
        return $this->api->send($this->id, $text);
    }

    /**
     * @return string[]
     */
    public function users(): array
    {
        return $this->usernames;
    }

    /**
     * @return ListItem
     */
    public function asListItem(): ListItem
    {
        return new ListItem([
            'text' => end($this->usernames),
            'value' => $this
        ]);
    }
}