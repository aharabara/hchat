<?php
namespace App\Api;

use Httpful\Request;
use RocketChat\Channel;
use RocketChat\Group;

class AbstractClient{

    public $api;

    function __construct(){
        $this->api = getenv('ROCKET_CHAT_INSTANCE') . getenv('REST_API_ROOT');
        // set template request to send and expect JSON
        $tmp = Request::init()
            ->sendsJson()
            ->expectsJson();
        Request::ini( $tmp );
    }

    /**
     * Get version information. This simple method requires no authentication.
     */
    public function version() {
        $response = Request::get("$this->api/info" )->send();
        return $response->body->info->version;
    }

    /**
     * Quick information about the authenticated user.
     */
    public function me() {
        $response = Request::get( "$this->api/me" )->send();

        if( $response->body->status != 'error' ) {
            if( isset($response->body->success) && $response->body->success == true ) {
                return $response->body;
            }
        } else {
            echo( $response->body->message . "\n" );
            return false;
        }
    }

    /**
     * List all of the users and their information.
     *
     * Gets all of the users in the system and their information, the result is
     * only limited to what the callee has access to view.
     */
    public function list_users(){
        $response = Request::get( "$this->api/users.list" )->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            return $response->body->users;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }

    /**
     * List the private groups the caller is part of.
     */
    public function list_groups() {
        $response = Request::get( "$this->api/groups.list" )->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            $groups = array();
            foreach($response->body->groups as $group){
                $groups[] = new Group($group);
            }
            return $groups;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }

    /**
     * List the channels the caller has access to.
     */
    public function list_channels() {
        $response = Request::get( $this->api . '/channels.list' )->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            $groups = array();
            foreach($response->body->channels as $group){
                $groups[] = new Channel($group);
            }
            return $groups;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }


    /**
     * Gets a user’s information, limited to the caller’s permissions.
     */
    public function info() {
        $response = Request::get( $this->api . '/users.info?userId=' . $this->id )->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            $this->id = $response->body->user->_id;
            $this->nickname = $response->body->user->name;
            $this->email = $response->body->user->emails[0]->address;
            return $response->body;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }

}
