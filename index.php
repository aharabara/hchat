<?php

use Base\{Application, Core\Workspace, Services\ViewRender};

chdir(__DIR__);
require './vendor/autoload.php';
//require './habarnam/vendor/autoload.php';

define('REST_API_ROOT', '/api/v1/');
define('ROCKET_CHAT_INSTANCE', '<your rocket server>');


$render = new ViewRender(__DIR__.'/views/');
$workspace = new Workspace('habarnam-chat');

(new Application($workspace, $render->prepare(), 'main'))
    ->debug(true)
    ->handle();
