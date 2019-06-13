<?php

use App\Api\Api;
use Base\{Application, Core\Workspace, Services\ViewRender};

chdir(__DIR__);
require './vendor/autoload.php';
require '/home/aharabara/Projects/Experimets/habarnam/vendor/autoload.php';

define('REST_API_ROOT', '/api/v1/');
define('ROCKET_CHAT_INSTANCE', 'https://rocket.pentalog.com');
define('ROCKET_PERSONAL_TOKEN', 'tCRbPjMOogbjpMm6uuUOohlwBz5-G1mrOfpt9ZRYcaQ');
define('ROCKET_PERSONAL_USER_ID', '7uwYXpB7khsHfax8v');


//$api = new Api("", '');
//$api->login();
//$users = $api->list_users();
//$rooms = $api->directMessages();

//foreach ($users as $friend) {
//    if (strpos($friend->username, 'itr') !== 0) {
//        continue;
//    }
//    PRINT "$friend->username \n";
////        $item = new ListItem([
////            'text' => $friend->username,
////            'value' => $friend->_id,
////        ]);
//    var_export(array_keys($rooms));die;
//    var_export($rooms[$friend->username]->users());die;
//    var_export($room->messages());
//}

//die();
$render = new ViewRender(__DIR__ . '/views/');
$workspace = new Workspace('habarnam-chat');

(new Application($workspace, $render->prepare(), 'main'))
    ->debug(true)
    ->handle();
