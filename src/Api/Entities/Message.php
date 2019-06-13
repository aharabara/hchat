<?php

namespace App\Api\Entities;

use DateTimeImmutable;

class Message
{
    /** @var string */
    protected $id;
    /** @var string */
    protected $content;
    /** @var User */
    protected $sender;
    /** @var DateTimeImmutable */
    protected $createdAt;

    /**
     * Message constructor.
     * @param string $id
     * @param string $content
     * @param User $sender
     * @param DateTimeImmutable $createdAt
     */
    public function __construct(string $id, string $content, User $sender, DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->content = $content;
        $this->sender = $sender;
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * @return User
     */
    public function sender(): User
    {
        return $this->sender;
    }

    /**
     * @return DateTimeImmutable
     */
    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}