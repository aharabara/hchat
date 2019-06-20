<?php

namespace App\Api;

use Httpful\Request;

class User extends AbstractClient
{

    protected $password;
    protected $id;

    /**
     * Authenticate with the REST API.
     * @param bool $save_auth
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function login($save_auth = true)
    {
//        $response = Request::post( $this->api . 'login' )
//            ->body(array( 'user' => $this->username, 'password' => $this->password ))
//            ->send();

//        throw new \UnexpectedValueException(var_export($response->body, true));
//        if( (int)$response->code === 200 && isset($response->body->status) && $response->body->status == 'success' ) {
        $this->id = '';
        if ($save_auth) {
            // save auth token for future requests
            $tmp = Request::init()
                ->addHeader('X-Auth-Token', getenv('ROCKET_CHAT_PERSONAL_TOKEN'))
                ->addHeader('X-User-Id', $this->id);
            Request::ini($tmp);
        }
        return true;
    }


    /**
     * Deletes an existing user.
     */
    public function test($id) {

        $response = Request::post( $this->api . 'channels.history?roomId=$id' )
//            ->body(array('roomId' => $id, ))
            ->send();

        var_export($response->body);die;
        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            return true;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }


}