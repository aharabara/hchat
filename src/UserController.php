<?php

namespace App;

use App\Api\User;
use Base\Application;
use Base\Components\Input;
use Base\Components\OrderedList\ListItem;
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
    /** @var User */
    protected $user;
    /**
     * @var TextArea
     */
    protected $infoField;
    /**
     * @var OrderedList
     */
    protected $userList;

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
    }

    public function login(): void
    {
        // login as user
        $this->user = $user = new User($this->userName->getText(), $this->userPassword->getText());
        if ($user->login()) {
            $users = $user->list_users();
            foreach ($users as $friend){
                if(strpos($friend->username, 'itr') !== 0) continue;
                $item  = new ListItem([
                    'text' => $friend->username,
                    'value' => $friend->_id,
                ]);
                $this->userList->addItems($item);
                $user->test($friend->_id);
            }
//            $this->infoField->setText(json_encode($users, JSON_PRETTY_PRINT));;
            $this->switchTo('main');
            
        } else {
            throw new \UnexpectedValueException(var_export([
                $user,
            ], true));
        }
    }
}