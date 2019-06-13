<?php

namespace App\Api\Entities;

class User
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $id;
    /** @var string */
    protected $userName;
    /** @var string */
    protected $status;

    /**
     * User constructor.
     * @param string $id
     * @param string $userName
     * @param string $name
     * @param string $status
     */
    public function __construct(string $id, string $userName, string $name, string $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->userName = $userName;
    }

    /** @return string */
    public function name(): string
    {
        return $this->name;
    }

    /** @return string */
    public function username(): string
    {
        return $this->userName;
    }

    /** @return string */
    public function status(): string
    {
        return $this->status;
    }
}