<?php

namespace App;

use App\Api\Api;
use App\Api\Entities\DirectMessageChannel;
use Base\Application;
use Base\Components\Input;
use Base\Components\OrderedList\OrderedList;
use Base\Components\Password;
use Base\Components\TextArea;
use Base\Core\BaseController;
use Base\Core\Workspace;

class UserController extends BaseController
{
    /** @var Input */
    protected $userName;
    /** @var Password */
    protected $userPassword;
    /** @var Api */
    protected $api;
    /** @var TextArea */
    protected $infoField;
    /** @var OrderedList */
    protected $userList;
    /** @var DirectMessageChannel */
    protected $currentRoom;
    /** @var Input */
    protected $messageInput;

    /**
     * UserController constructor.
     * @param Application $app
     * @param Workspace $workspace
     */
    public function __construct(Application $app, Workspace $workspace)
    {
        parent::__construct($app, $workspace);
        $this->userList = $app->findFirst('#users', 'main');
        $this->userName = $app->findFirst('#login-username', 'login');
        $this->userPassword = $app->findFirst('#login-password', 'login');
        $this->infoField = $app->findFirst('#info', 'main');
        $this->messageInput = $app->findFirst('#message', 'main');
    }

    public function login(): void
    {
        // login as user
        $this->api = new Api($this->userName->getText(), $this->userPassword->getText());
        if ($this->api->login()) {
            foreach ($this->api->directMessages() as $directMessageChannel) {
                $this->userList->addItems($directMessageChannel->asListItem());
            }
            $this->switchTo('main');

        } else {
            throw new \UnexpectedValueException(var_export([
                $this->api,
            ], true));
        }
    }

    public function openPrivateChat(): void
    {
        /** @var DirectMessageChannel $dm */
        $dm = $this->userList->getSelectedItem()->getValue();
        $content = '';
        foreach($dm->messages() as $message) {
            $content .= "\n{$message->sender()->name()} :\n>>{$message->content()}\n";
        }
        $this->infoField->setText($content);
        $this->currentRoom = $dm;
    }
    
    
    public function sendMessage(): void
    {
        if($this->currentRoom){
            $this->currentRoom->send($this->messageInput->getText());
        }
    }
}